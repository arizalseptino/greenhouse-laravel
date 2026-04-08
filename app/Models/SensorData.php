<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_data';
    
    protected $fillable = [
        'suhu_udara',
        'kelembaban_udara',
        'soil_analog',
        'kelembaban_tanah',
        'timestamp',
    ];
    
    protected $casts = [
        'suhu_udara' => 'float',
        'kelembaban_udara' => 'float',
        'soil_analog' => 'integer',
        'kelembaban_tanah' => 'float',
    ];
    
    // Nonaktifkan created_at dan updated_at karena kita pakai timestamp custom
    const CREATED_AT = null;
    const UPDATED_AT = null;
    
    /**
     * Accessor untuk format tanggal Indonesia
     */
    public function getFormattedTimestampAttribute()
    {
        return \Carbon\Carbon::parse($this->timestamp)->format('d/m/Y H:i:s');
    }
    
    /**
     * Accessor untuk format tanggal pendek (untuk chart)
     */
    public function getShortTimestampAttribute()
    {
        return \Carbon\Carbon::parse($this->timestamp)->format('d/m H:i');
    }
}
