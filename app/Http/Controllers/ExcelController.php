<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelController extends Controller
{
    public function index()
    {
        return view('excel');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        // Nettoyage des anciens fichiers
        @unlink(storage_path('app/public/processed.xlsx'));
        @unlink(storage_path('app/public/unmatched.xlsx'));
        @unlink(storage_path('app/public/matched.xlsx'));

        // Chargement du fichier
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Index des colonnes
        $headers = $rows[1];
        $colInsee = array_search('insee', $headers);
        $colInseeListe = array_search('insee_liste', $headers);

        if (!$colInsee || !$colInseeListe) {
            return back()->withErrors(['Le fichier doit contenir les colonnes insee et insee_liste.']);
        }

        $highlightedRows = [];

        foreach ($rows as $i => $row) {
            if ($i === 1) continue;
            $val = $row[$colInseeListe] ?? null;

            foreach ($rows as $j => $rowCheck) {
                if ($j === 1) continue;
                if (($rowCheck[$colInsee] ?? null) == $val) {
                    $sheet->getStyle("A$j:Z$j")->getFill()->setFillType('solid')
                          ->getStartColor()->setARGB('FFFFFF00'); // Jaune
                    $highlightedRows[] = $j;
                }
            }
        }

        // Fichier traité
        $processedFile = storage_path('app/public/processed.xlsx');
        (new Xlsx($spreadsheet))->save($processedFile);

        // Fichier unmatched
        $unmatchedSpreadsheet = new Spreadsheet();
        $unmatchedSheet = $unmatchedSpreadsheet->getActiveSheet();
        $rowIndex = 1;
        foreach ($rows as $i => $row) {
            if ($i === 1) {
                $unmatchedSheet->fromArray(array_values($row), null, "A$rowIndex");
                $rowIndex++;
                continue;
            }

            if (!in_array($i, $highlightedRows)) {
                $unmatchedSheet->fromArray(array_values($row), null, "A$rowIndex");
                $rowIndex++;
            }
        }
        (new Xlsx($unmatchedSpreadsheet))->save(storage_path('app/public/unmatched.xlsx'));

        // ✅ Fichier matched
        $matchedSpreadsheet = new Spreadsheet();
        $matchedSheet = $matchedSpreadsheet->getActiveSheet();
        $rowIndex = 1;
        foreach ($rows as $i => $row) {
            if ($i === 1) {
                $matchedSheet->fromArray(array_values($row), null, "A$rowIndex");
                $rowIndex++;
                continue;
            }

            // Si la ligne est dans les lignes mises en surbrillance, sans coloriage
            // if (in_array($i, $highlightedRows)) {
            //     $matchedSheet->fromArray(array_values($row), null, "A$rowIndex");
            //     $rowIndex++;
            // }

            // Si la ligne est dans les lignes mises en surbrillance, avec coloriage en jaune
            if (in_array($i, $highlightedRows)) {
                $matchedSheet->fromArray(array_values($row), null, "A$rowIndex");
                $matchedSheet->getStyle("A$rowIndex:Z$rowIndex")->getFill()->setFillType('solid')
                    ->getStartColor()->setARGB('FFFFFF00'); // jaune
                $rowIndex++;
            }
            
        }
        (new Xlsx($matchedSpreadsheet))->save(storage_path('app/public/matched.xlsx'));

        // Statistiques
        $total = count($rows) - 1;
        $matchedCount = count($highlightedRows);
        $unmatchedCount = $total - $matchedCount;
        $matchPercent = $total > 0 ? round(($matchedCount / $total) * 100, 2) : 0;
        $unmatchPercent = $total > 0 ? round(($unmatchedCount / $total) * 100, 2) : 0;

        return redirect()->route('excel.index')
            ->with('success', 'Fichier traité avec succès.')
            ->with('download', route('excel.download'))
            ->with('downloadUnmatched', route('excel.downloadUnmatched'))
            ->with('downloadMatched', route('excel.downloadMatched'))
            ->with('stats', [
                'total' => $total,
                'matched' => $matchedCount,
                'unmatched' => $unmatchedCount,
                'match_percent' => $matchPercent,
                'unmatch_percent' => $unmatchPercent,
            ]);
    }

    public function download()
    {
        $path = storage_path('app/public/processed.xlsx');
        return response()->download($path)->deleteFileAfterSend(false);
    }

    public function downloadUnmatched()
    {
        $path = storage_path('app/public/unmatched.xlsx');
        return response()->download($path)->deleteFileAfterSend(false);
    }

    public function downloadMatched()
    {
        $path = storage_path('app/public/matched.xlsx');
        return response()->download($path)->deleteFileAfterSend(false);
    }
}
