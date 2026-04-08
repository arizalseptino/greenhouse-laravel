<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Monitoring Greenhouse Sekolah Vokasi IPB') }}
            </h2>
            <a href="{{ route('download.csv') }}" 
               class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-lg transition duration-150">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download Data
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- STATUS SISTEM -->
            @if($is_offline)
            <div class="bg-white border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium text-red-800">Sistem Offline</span>
                    <span class="ml-2 text-sm text-red-600">Data terakhir: {{ $data ? $data->formatted_timestamp : '-' }}</span>
                </div>
            </div>
            @else
            <div class="bg-white border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                    <span class="font-medium text-gray-800">Sistem Online</span>
                    <span class="ml-2 text-sm text-gray-600">Update: {{ $data ? $data->formatted_timestamp : '-' }}</span>
                </div>
            </div>
            @endif

            <!-- KARTU DATA REAL-TIME -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Suhu Udara -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Suhu Udara</p>
                                <p class="text-3xl font-bold text-gray-900" id="suhu-realtime">
                                    {{ $data ? number_format($data->suhu_udara, 1) : '--' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">°Celsius</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kelembaban Udara -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Kelembaban Udara</p>
                                <p class="text-3xl font-bold text-gray-900" id="rh-realtime">
                                    {{ $data ? number_format($data->kelembaban_udara, 1) : '--' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Persen (%)</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kelembaban Tanah -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-1">Kelembaban Tanah</p>
                                <p class="text-3xl font-bold text-gray-900" id="soil-realtime">
                                    {{ $data ? number_format($data->kelembaban_tanah, 1) : '--' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">Persen (%)
                                    <span class="text-xs ml-1">• Analog: <span id="soil-analog-realtime">{{ $data ? $data->soil_analog : '--' }}</span></span>
                                </p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTIK 24 JAM -->
            @if($stats24h)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="text-lg font-semibold text-gray-800">Statistik 24 Jam Terakhir</h5>
                    <p class="text-sm text-gray-500 mt-1">{{ $stats24h['count'] }} data tercatat</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Suhu -->
                        <div>
                            <h6 class="font-semibold text-gray-700 mb-4 pb-2 border-b">Suhu Udara</h6>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Rata-rata</span>
                                    <span class="font-semibold text-gray-900">{{ $stats24h['suhu_avg'] }}°C</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Minimum</span>
                                    <span class="text-gray-700">{{ $stats24h['suhu_min'] }}°C</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Maximum</span>
                                    <span class="text-gray-700">{{ $stats24h['suhu_max'] }}°C</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kelembaban Tanah -->
                        <div>
                            <h6 class="font-semibold text-gray-700 mb-4 pb-2 border-b">Kelembaban Tanah</h6>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Rata-rata</span>
                                    <span class="font-semibold text-gray-900">{{ $stats24h['kelembaban_tanah_avg'] }}%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Minimum</span>
                                    <span class="text-gray-700">{{ $stats24h['kelembaban_tanah_min'] }}%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Maximum</span>
                                    <span class="text-gray-700">{{ $stats24h['kelembaban_tanah_max'] }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- GRAFIK TIME SERIES -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="text-lg font-semibold text-gray-800">Grafik Time Series</h5>
                    <p class="text-sm text-gray-500 mt-1">50 data terakhir</p>
                </div>
                <div class="p-6">
                    <canvas id="timeSeriesChart" height="80"></canvas>
                </div>
            </div>

            <!-- GRAFIK SCATTER (KORELASI) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="text-lg font-semibold text-gray-800">Scatter Plot: Suhu Udara vs Kelembaban Tanah</h5>
                    <p class="text-sm text-gray-500 mt-1">Analisis korelasi untuk penelitian</p>
                </div>
                <div class="p-6">
                    <canvas id="scatterChart" height="80"></canvas>
                </div>
            </div>

        </div>
    </div>


    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // 1. Ambil data dari Controller
        const rawData = @json($chartData);

        // 2. Fungsi Hitung Statistik Regresi & Korelasi (Matematika Skripsi)
        function calculateRegressionStats(data) {
            const n = data.length;
            if (n < 2) return null;

            let sumX = 0, sumY = 0, sumXY = 0, sumX2 = 0, sumY2 = 0;

            data.forEach(d => {
                const x = parseFloat(d.suhu_udara);
                const y = parseFloat(d.kelembaban_tanah);
                sumX += x; sumY += y;
                sumXY += (x * y);
                sumX2 += (x * x);
                sumY2 += (y * y);
            });

            // Hitung r (Pearson Correlation)
            const numeratorR = (n * sumXY) - (sumX * sumY);
            const denominatorR = Math.sqrt((n * sumX2 - Math.pow(sumX, 2)) * (n * sumY2 - Math.pow(sumY, 2)));
            const r = denominatorR === 0 ? 0 : numeratorR / denominatorR;

            // Hitung Slope (b) dan Intercept (a) -> Y = a + bX
            const b = (n * sumXY - sumX * sumY) / (n * sumX2 - Math.pow(sumX, 2));
            const a = (sumY - b * sumX) / n;

            const xValues = data.map(d => parseFloat(d.suhu_udara));
            
            return {
                r: r,
                r2: r * r,
                a: a,
                b: b,
                minX: Math.min(...xValues),
                maxX: Math.max(...xValues)
            };
        }

        const stats = calculateRegressionStats(rawData);

        // ========================================
        // 1. GRAFIK TIME SERIES (Dual Axis)
        // ========================================
        const ctxTime = document.getElementById('timeSeriesChart').getContext('2d');
        new Chart(ctxTime, {
            type: 'line',
            data: {
                labels: rawData.map(d => {
                    const date = new Date(d.timestamp);
                    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                }),
                datasets: [
                    {
                        label: 'Suhu Udara (°C)',
                        data: rawData.map(d => d.suhu_udara),
                        borderColor: '#dc2626',
                        yAxisID: 'y',
                        tension: 0.3,
                        borderWidth: 2
                    },
                    {
                        label: 'Kelembaban Tanah (%)',
                        data: rawData.map(d => d.kelembaban_tanah),
                        borderColor: '#2563eb',
                        yAxisID: 'y1',
                        tension: 0.3,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { type: 'linear', position: 'left', title: { display: true, text: 'Suhu (°C)' } },
                    y1: { type: 'linear', position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Tanah (%)' } }
                }
            }
        });

        // ========================================
        // 2. GRAFIK SCATTER + GARIS REGRESI + STATS
        // ========================================
        const ctxScatter = document.getElementById('scatterChart').getContext('2d');
        new Chart(ctxScatter, {
            data: {
                datasets: [
                    {
                        type: 'scatter',
                        label: 'Titik Data Pengamatan',
                        data: rawData.map(d => ({ x: d.suhu_udara, y: d.kelembaban_tanah })),
                        backgroundColor: 'rgba(37, 99, 235, 0.6)',
                        borderColor: '#2563eb',
                        pointRadius: 5
                    },
                    {
                        type: 'line',
                        label: 'Garis Tren (Regresi Linear)',
                        data: stats ? [
                            { x: stats.minX, y: stats.a + (stats.b * stats.minX) },
                            { x: stats.maxX, y: stats.a + (stats.b * stats.maxX) }
                        ] : [],
                        borderColor: '#dc2626',
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    // Menampilkan rumus dan R2 di judul grafik
                    title: {
                        display: true,
                        text: stats ? 
                            `Persamaan: Y = ${stats.a.toFixed(2)} + (${stats.b.toFixed(2)})X  |  r = ${stats.r.toFixed(4)}  |  R² = ${stats.r2.toFixed(4)}` : 
                            'Data tidak cukup untuk analisis',
                        color: '#1e293b',
                        font: { size: 14, weight: 'bold' },
                        padding: { bottom: 20 }
                    }
                },
                scales: {
                    x: { type: 'linear', position: 'bottom', title: { display: true, text: 'Suhu Udara (X)' } },
                    y: { title: { display: true, text: 'Kelembaban Tanah (Y)' } }
                }
            }
        });

        // 3. Auto-Refresh Logic (tetap gunakan yang sudah ada)
        setInterval(async function() {
            // ... (fetch data terbaru untuk update kartu real-time)
        }, 10000);
    </script>
    @endpush
</x-app-layout>
