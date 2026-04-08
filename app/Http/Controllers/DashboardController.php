<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil data paling baru untuk status real-time
        $data = SensorData::latest('timestamp')->first();
        
        // 2. Cek apakah sistem sedang offline
        // Interval ESP32: 5 menit (300 detik). Toleransi: +1 menit (total 360 detik)
        $is_offline = false;
        if ($data) {
            $last_update = Carbon::parse($data->timestamp);
            if ($last_update->diffInSeconds(now()) > 360) {
                $is_offline = true;
            }
        }
        
        // 3. Data untuk grafik (50 data terakhir untuk Dual-Axis & Scatter Plot)
        // Kita ambil 50 data terbaru, lalu diurutkan dari yang terlama ke terbaru
        $chartData = SensorData::latest('timestamp')
            ->limit(50)
            ->get()
            ->reverse()
            ->values(); 
        
        // 4. Statistik 24 jam terakhir
        $stats24h = $this->getStatistics24Hours();
        
        return view('dashboard', compact(
            'data',
            'is_offline',
            'chartData',
            'stats24h'
        ));
    }
    
    /**
     * Halaman history dengan pagination
     */

    public function history(Request $request)
    {
        $query = SensorData::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('timestamp', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        }

        $data = $query->latest('timestamp')->paginate(100);
        
        return view('history', compact('data'));
    }
    /**
     * Download data sebagai CSV untuk diolah di Excel (sesuai kebutuhan Bab 3 & 4)
     */
    public function downloadCsv()
    {
        $filename = 'Greenhouse_Data_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM agar Excel tidak berantakan saat baca karakter spesial/UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header CSV (sesuai format skripsi)
            fputcsv($file, [
                'ID',
                'Waktu (WIB)',
                'Suhu Udara (°C)',
                'Kelembaban Udara (%)',
                'Soil Analog (RAW)',
                'Kelembaban Tanah (%)'
            ]);
            
            // Gunakan chunk agar server tidak crash saat data sudah mencapai ribuan
            SensorData::orderBy('timestamp', 'desc')->chunk(1000, function($records) use ($file) {
                foreach ($records as $row) {
                    fputcsv($file, [
                        $row->id,
                        $row->timestamp,
                        $row->suhu_udara,
                        $row->kelembaban_udara,
                        $row->soil_analog,
                        $row->kelembaban_tanah
                    ]);
                }
            });
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Menghitung statistik 24 jam terakhir dari database
     */
    private function getStatistics24Hours()
    {
        // Ambil data dari 24 jam yang lalu berdasarkan kolom timestamp
        $data24h = SensorData::where('timestamp', '>=', now()->subHours(24))->get();
        
        if ($data24h->isEmpty()) {
            return null;
        }
        
        return [
            'count' => $data24h->count(),
            'suhu_avg' => round($data24h->avg('suhu_udara'), 1),
            'suhu_min' => round($data24h->min('suhu_udara'), 1),
            'suhu_max' => round($data24h->max('suhu_udara'), 1),
            'kelembaban_tanah_avg' => round($data24h->avg('kelembaban_tanah'), 1),
            'kelembaban_tanah_min' => round($data24h->min('kelembaban_tanah'), 1),
            'kelembaban_tanah_max' => round($data24h->max('kelembaban_tanah'), 1),
        ];
    }
}