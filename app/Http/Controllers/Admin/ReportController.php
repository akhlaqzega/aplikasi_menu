<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    /**
     * Tampilkan detail transaksi (audit) berdasarkan rentang tanggal.
     */
    public function index(Request $request)
    {
        // 1. Tentukan Rentang Tanggal
        // Default: Hari ini
        $today = Carbon::today();
        
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : $today->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : $today->endOfDay();

        // Jika start_date dan end_date sama, atur rentang ke satu hari penuh
        if ($startDate->toDateString() == $endDate->toDateString()) {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        // 2. Query Detail Transaksi (Audit Table)
        // Gunakan Eager Loading (with) untuk relasi 'table' dan 'payment'
        $detailedTransactions = Order::whereBetween('created_at', [$startDate, $endDate])
                            ->with(['table', 'payment']) // Eager Load untuk menghindari N+1
                            ->orderBy('created_at', 'asc')
                            ->get();
        
      

        return view('admin.reports.index', [
            // Variabel summary DIHILANGKAN karena tidak lagi diperlukan di view
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'detailedTransactions' => $detailedTransactions, // Hanya kirim detail transaksi
        ]);
    }

    public function exportPdf(Request $request)
    {
        // Ambil data dan periode seperti di fungsi index()
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        
        if ($startDate->toDateString() == $endDate->toDateString()) {
            $startDate->startOfDay();
            $endDate->endOfDay();
        }

        $detailedTransactions = Order::whereBetween('created_at', [$startDate, $endDate])
                            ->with(['table', 'payment'])
                            ->orderBy('created_at', 'asc')
                            ->get();

        // Siapkan data untuk dikirim ke view PDF
        $data = [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactions' => $detailedTransactions,
            // Anda bisa menambahkan ringkasan total pendapatan di sini jika perlu
            'totalRevenue' => $detailedTransactions->where('status', 'completed')->sum('total_price')
        ];

        // 1. Load view PDF
        $pdf = FacadePdf::loadView('admin.reports.pdf_template', $data);

        // 2. Tentukan nama file
        $fileName = 'Laporan_Transaksi_' . $startDate->format('Ymd') . '_to_' . $endDate->format('Ymd') . '.pdf';

        // 3. Download file
        return $pdf->download($fileName);
    }
}
