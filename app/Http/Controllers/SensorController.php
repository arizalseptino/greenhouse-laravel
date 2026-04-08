<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /**
     * Store sensor data from ESP32
     * Endpoint: POST /api/sensor-data
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'suhu_udara' => 'required|numeric',
                'kelembaban_udara' => 'required|numeric',
                'soil_analog' => 'required|integer',
                'timestamp' => 'required', // WAJIB: Minta waktu dari ESP32
                'kelembaban_tanah' => 'nullable|numeric', // Opsional, karena kita bisa hitung ulang
            ]);

            // ========================================
            // KALIBRASI SESUAI HASIL SKRIPSI KAMU
            // ========================================
            $a = 3161.74; // Intercept
            $b = -24.96;  // Slope
            
            // Hitung persentase berdasarkan analog yang masuk
            $kelembaban_tanah = ($validated['soil_analog'] - $a) / $b;
            
            // Clamp nilai agar tetap 0-100%
            $kelembaban_tanah = max(0, min(100, $kelembaban_tanah));

            // Simpan data
            $sensorData = SensorData::create([
                'suhu_udara' => $validated['suhu_udara'],
                'kelembaban_udara' => $validated['kelembaban_udara'],
                'soil_analog' => $validated['soil_analog'],
                'kelembaban_tanah' => $kelembaban_tanah, 
                'timestamp' => $validated['timestamp'], // PAKAI WAKTU DARI ESP32/RTC
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'kelembaban_terhitung' => round($kelembaban_tanah, 1)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get latest sensor data
     * Endpoint: GET /api/sensor/latest
     */
    public function latest()
    {
        $data = SensorData::latest('timestamp')->first();
        
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum ada data sensor'
            ], 404);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $data->id,
                'suhu_udara' => round($data->suhu_udara, 1),
                'kelembaban_udara' => round($data->kelembaban_udara, 1),
                'soil_analog' => $data->soil_analog,
                'kelembaban_tanah' => round($data->kelembaban_tanah, 1),
                'timestamp' => $data->timestamp,
                'formatted_timestamp' => $data->formatted_timestamp
            ]
        ]);
    }
}
