<?php

use Illuminate\Support\Facades\Route;
// routes/web.php

use App\Http\Controllers\ExcelController;

Route::get('/', [ExcelController::class, 'index'])->name('excel.index');
Route::post('/upload', [ExcelController::class, 'upload'])->name('excel.upload');
Route::get('/download', [ExcelController::class, 'download'])->name('excel.download');
Route::get('/download-unmatched', [ExcelController::class, 'downloadUnmatched'])->name('excel.downloadUnmatched');



// Route::get('/', function () {
//     return view('welcome');
// });
