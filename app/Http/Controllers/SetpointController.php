<?php

namespace App\Http\Controllers;

use App\Models\Setpoint;
use Illuminate\Http\Request;

class SetpointController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'suhu_setpoint' => 'required|numeric',
            'kelembaban_setpoint' => 'required|numeric',
        ]);
        
        Setpoint::updateOrCreate(
            ['id' => 1],
            [
                'suhu_setpoint' => $validated['suhu_setpoint'],
                'kelembaban_setpoint' => $validated['kelembaban_setpoint'],
            ]
        );
        
        return redirect()->back()->with('success', 'Setpoint berhasil diupdate!');
    }
}