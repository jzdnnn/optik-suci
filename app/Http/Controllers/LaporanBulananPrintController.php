<?php

namespace App\Http\Controllers;

use App\Models\LaporanBulanan;
use Illuminate\Http\Request;

class LaporanBulananPrintController extends Controller
{
    public function printMonthlyReport(LaporanBulanan $laporanBulanan)
    {
        if (!auth()->check() || !auth()->user()->can('viewAny_laporan_bulanan')) {
            abort(403, 'Unauthorized.');
        }

        return view('reports.print-bulanan', [
            'report' => $laporanBulanan,
        ]);
    }
}
