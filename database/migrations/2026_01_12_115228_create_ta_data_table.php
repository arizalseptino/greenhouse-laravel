<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ta_data', function (Blueprint $table) {
            $table->id();
            $table->float('temp_avg')->default(0)->comment('Rata-rata 3 DHT22');
            $table->float('hum_avg')->default(0)->comment('Rata-rata kelembaban');
            $table->float('temp1')->default(0)->comment('DHT22 #1');
            $table->float('temp2')->default(0)->comment('DHT22 #2');
            $table->float('temp3')->default(0)->comment('DHT22 #3');
            $table->float('hum1')->default(0)->comment('Humidity DHT22 #1');
            $table->float('hum2')->default(0)->comment('Humidity DHT22 #2');
            $table->float('hum3')->default(0)->comment('Humidity DHT22 #3');
            $table->float('temp_air')->default(0)->comment('DS18B20 Suhu Air');
            $table->float('voltage')->default(0)->comment('PZEM Voltage');
            $table->float('current')->default(0)->comment('PZEM Current');
            $table->float('power')->default(0)->comment('PZEM Power (Watt)');
            $table->float('energy')->default(0)->comment('PZEM Energy (kWh)');
            $table->float('pln_voltage')->default(0)->comment('Tegangan PLN');
            $table->tinyInteger('fan_status')->default(0)->comment('0=OFF, 1=ON');
            $table->tinyInteger('warning_fan')->default(0)->comment('1=Fan Failure');
            $table->tinyInteger('warning_pln')->default(0)->comment('1=PLN Outage');
            $table->timestamps();
            
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ta_data');
    }
};