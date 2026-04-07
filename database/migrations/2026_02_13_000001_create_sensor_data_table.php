<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel untuk sistem baru: DHT22 + Capacitive Soil Moisture Sensor
     */
    public function up(): void
    {
        Schema::create('sensor_data', function (Blueprint $table) {
            $table->id();
            
            // Data dari DHT22
            $table->float('suhu_udara')->comment('Suhu udara dari DHT22 (°C)');
            $table->float('kelembaban_udara')->comment('Kelembaban udara dari DHT22 (%)');
            
            // Data dari Capacitive Soil Moisture Sensor
            $table->integer('soil_analog')->comment('Nilai analog sensor (0-4095)');
            $table->float('kelembaban_tanah')->comment('Kelembaban tanah setelah kalibrasi (%)');
            
            
            $table->dateTime('timestamp')->index();
            // Index untuk performa query
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
