<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data terakhir
        $data = SensorData::latest('timestamp')->first();
        
        // Cek sistem offline (> 6 menit = 360 detik)
        // Interval ESP32: 5 menit (300 detik)
        // Toleransi: +1 menit untuk delay network
        $is_offline = false;
        if ($data) {
            $last_update = strtotime($data->timestamp);
            if ((time() - $last_update) > 360) {  // 6 menit
                $is_offline = true;
            }
        }
        
        // Data untuk grafik (50 data terakhir, dibalik urutannya agar kronologis)
        $chartData = SensorData::latest('timestamp')
            ->limit(50)
            ->get()
            ->reverse()
            ->values(); // Reset array keys
        
        // Statistik 24 jam terakhir
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
    public function history()
    {
        $data = SensorData::latest('timestamp')->paginate(100);
        
        return view('history', compact('data'));
    }
    
    /**
     * Download data sebagai CSV
     */
    public function downloadCsv()
    {
        $filename = 'Sensor_Data_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header CSV
            fputcsv($file, [
                'ID',
                'Timestamp',
                'Suhu Udara (°C)',
                'Kelembaban Udara (%)',
                'Soil Analog (RAW)',
                'Kelembaban Tanah (%)'
            ]);
            
            // Data (chunk untuk efisiensi memory)
            SensorData::orderBy('id', 'desc')->chunk(1000, function($data) use ($file) {
                foreach ($data as $row) {
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
     * Get statistik 24 jam terakhir
     */
    private function getStatistics24Hours()
    {
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
