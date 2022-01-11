<?php

use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     // return view('sheet');
//     return "Excel Work";
// });


Route::get('/', [ExcelController::class, 'select_file']);
Route::post('upload_file', [ExcelController::class, 'upload_file'])->name('upload_file');
Route::get('sheet', [ExcelController::class, 'sheet'])->name('sheet');
Route::get('xlsx', [ExcelController::class, 'xlsx'])->name('xlsx');
Route::get('xlsxupd', [ExcelController::class, 'xlsxupd'])->name('xlsxupd');
