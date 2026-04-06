<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setpoints', function (Blueprint $table) {
            $table->id();
            $table->float('suhu_setpoint')->default(30);
            $table->float('kelembaban_setpoint')->default(60);
            $table->timestamps();
        });
        
        // Insert default data
        DB::table('setpoints')->insert([
            'id' => 1,
            'suhu_setpoint' => 30,
            'kelembaban_setpoint' => 60,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('setpoints');
    }
};