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
        // 1. Validation du fichier
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        // 2. Chargement du fichier
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // 3. Trouver les indexes des colonnes
        $headers = $rows[1];
        $colInsee = array_search('insee', $headers);
        $colInseeListe = array_search('insee_liste', $headers);

        if (!$colInsee || !$colInseeListe) {
            return back()->withErrors(['Le fichier doit contenir les colonnes insee et insee_liste.']);
        }

        // 4. Marquage des lignes matchées
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

        // 5. Génération du fichier traité
        $processedFile = storage_path('app/public/processed.xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save($processedFile);

        // 6. Génération du fichier des lignes non matchées
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

        $unmatchedFile = storage_path('app/public/unmatched.xlsx');
        $writer2 = new Xlsx($unmatchedSpreadsheet);
        $writer2->save($unmatchedFile);

        // 7. Statistiques
        $total = count($rows) - 1;
        $matchedCount = count($highlightedRows);
        $unmatchedCount = $total - $matchedCount;
        $matchPercent = $total > 0 ? round(($matchedCount / $total) * 100, 2) : 0;
        $unmatchPercent = $total > 0 ? round(($unmatchedCount / $total) * 100, 2) : 0;

        // 8. Redirection avec données
        return redirect()->route('excel.index')
            ->with('success', 'Fichier traité avec succès.')
            ->with('download', route('excel.download'))
            ->with('downloadUnmatched', route('excel.downloadUnmatched'))
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
        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function downloadUnmatched()
    {
        $path = storage_path('app/public/unmatched.xlsx');
        return response()->download($path)->deleteFileAfterSend(true);
    }
}
