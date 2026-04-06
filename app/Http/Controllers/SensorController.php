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
            ]);
            
            // ========================================
            // KALIBRASI SOIL MOISTURE SENSOR
            // ========================================
            // TODO: Update nilai a dan b sesuai hasil kalibrasi Anda!
            // Contoh persamaan linear: kelembaban = ((analog - a) / b) * 100
            
            $a = 3200;  // Nilai analog saat tanah kering (0%)
            $b = -2000; // Slope (negatif karena analog turun = kelembaban naik)
            
            $kelembaban_tanah = (($validated['soil_analog'] - $a) / $b) * 100;
            
            // Clamp nilai antara 0-100%
            $kelembaban_tanah = max(0, min(100, $kelembaban_tanah));
            
            // Insert data sensor
            $sensorData = SensorData::create([
                'suhu_udara' => $validated['suhu_udara'],
                'kelembaban_udara' => $validated['kelembaban_udara'],
                'soil_analog' => $validated['soil_analog'],
                'kelembaban_tanah' => $kelembaban_tanah,
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'data_id' => $sensorData->id,
                'kelembaban_tanah_terhitung' => round($kelembaban_tanah, 1)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
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
