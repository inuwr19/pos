<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // Mengambil waktu sekarang dan memulai hari
        $today = Carbon::now()->startOfDay();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // Menghitung total penjualan per hari dan per bulan
        $totalSalesToday = Order::whereDate('created_at', $today)->count();
        $totalSalesMonth = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $revenueToday = Order::whereDate('created_at', $today)->sum('total_price');
        $revenueMonth = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('total_price');

        // Data penjualan harian untuk grafik
        $dailySales = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->orderBy('date') // Mengurutkan data berdasarkan tanggal
                        ->get()
                        ->map(function($item) {
                            return [
                                'date' => $item->date,
                                'count' => $item->count
                            ];
                        });

        // Tambahkan log untuk debugging
        Log::info('Daily Sales Data:', $dailySales->toArray());

        return view('owner.dashboard', [
            'totalSalesToday' => $totalSalesToday,
            'totalSalesMonth' => $totalSalesMonth,
            'revenueToday' => $revenueToday,
            'revenueMonth' => $revenueMonth,
            'dailySales' => $dailySales
        ]);
    }
}
