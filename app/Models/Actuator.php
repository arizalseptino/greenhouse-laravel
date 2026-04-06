<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actuator extends Model
{
    protected $table = 'actuators';
    
    protected $fillable = [
        'fan_status'
    ];
    
    protected $casts = [
        'fan_status' => 'integer',
    ];
    
    // Override nama kolom timestamp
    const CREATED_AT = null;
    const UPDATED_AT = 'updated_at';
}