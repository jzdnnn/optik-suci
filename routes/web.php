<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/admin/laporan-keuangan/print', [App\Http\Controllers\ReportController::class, 'printDailyReport'])
    ->name('laporan-keuangan.print')
    ->middleware(['web', 'auth']);

Route::get('/admin/pembukuan-harian/print', [App\Http\Controllers\ReportController::class, 'printPembukuanHarian'])
    ->name('pembukuan-harian.print')
    ->middleware(['web', 'auth']);

Route::get('/admin/laporan-bulanan/{laporanBulanan}/print', [App\Http\Controllers\LaporanBulananPrintController::class, 'printMonthlyReport'])
    ->name('laporan-bulanan.print')
    ->middleware(['web', 'auth']);

Route::get('/admin/frame-keluar/print-bulanan', [App\Http\Controllers\FrameKeluarPrintController::class, 'print'])
    ->name('frame-keluar.print-bulanan')
    ->middleware(['web', 'auth']);

Route::get('/admin/frame-keluar/print-10-hari', [App\Http\Controllers\FrameKeluarPrintController::class, 'printTenDays'])
    ->name('frame-keluar.print-10-hari')
    ->middleware(['web', 'auth']);

Route::get('/admin/lensa-keluar/print', [App\Http\Controllers\LensKeluarPrintController::class, 'print'])
    ->name('lensa-keluar.print')
    ->middleware(['web', 'auth']);
