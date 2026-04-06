<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setpoint extends Model
{
    protected $table = 'setpoints';
    
    protected $fillable = [
        'suhu_setpoint',
        'kelembaban_setpoint'
    ];
    
    protected $casts = [
        'suhu_setpoint' => 'float',
        'kelembaban_setpoint' => 'float',
    ];
    
    // Override nama kolom timestamp
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
}