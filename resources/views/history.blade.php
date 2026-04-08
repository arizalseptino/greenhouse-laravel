<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Data Sensor') }}
            </h2>
            <a href="{{ route('download.csv') }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition duration-150 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download CSV
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                <form action="{{ route('history') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 w-full">
                        <x-input-label for="search" :value="__('Cari Waktu / ID')" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" 
                                      placeholder="Contoh: 08/04/2026 atau ID" :value="request('search')" />
                    </div>
                    <div class="w-full md:w-48">
                        <x-primary-button class="w-full justify-center py-2.5">
                            {{ __('Filter Data') }}
                        </x-primary-button>
                    </div>
                    @if(request('search'))
                        <div class="w-full md:w-auto">
                            <a href="{{ route('history') }}" class="text-sm text-red-600 hover:underline">Reset Filter</a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="bg-white border-l-4 border-blue-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-2 h-2 {{ request('search') ? 'bg-yellow-500' : 'bg-green-500 animate-pulse' }} rounded-full mr-2"></div>
                        <span class="font-medium text-gray-800">
                            {{ request('search') ? 'Auto-refresh dinonaktifkan (Mode Search)' : 'Auto-refresh aktif' }}
                        </span>
                        @if(!request('search'))
                            <span class="ml-2 text-sm text-gray-600">Update setiap 30 detik</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">
                        Total: <span class="font-semibold text-gray-800">{{ $data->total() }}</span> data
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="text-lg font-semibold text-gray-800">Data History</h5>
                    <p class="text-sm text-gray-500 mt-1">Menampilkan {{ $data->count() }} data pada halaman ini</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu (WIB)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Suhu (°C)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelembaban Udara (%)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Soil Analog</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelembaban Tanah (%)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($data as $row)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $row->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $row->timestamp }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">{{ number_format($row->suhu_udara, 1) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($row->kelembaban_udara, 1) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->soil_analog }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">{{ number_format($row->kelembaban_tanah, 1) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">Data tidak ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $data->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-refresh hanya jalan jika tidak sedang searching
        @if(!request('search'))
            setInterval(function() {
                window.location.reload();
            }, 30000); // 30 detik
        @endif
    </script>
    @endpush
</x-app-layout>