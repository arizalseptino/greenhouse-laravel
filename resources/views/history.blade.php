<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Data Sensor') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Info Bar -->
            <div class="bg-white border-l-4 border-blue-500 p-4 rounded-lg shadow-sm mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                        <span class="font-medium text-gray-800">Auto-refresh aktif</span>
                        <span class="ml-2 text-sm text-gray-600">Update setiap 30 detik</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total: <span class="font-semibold text-gray-800">{{ $data->total() }}</span> data
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="text-lg font-semibold text-gray-800">Data History</h5>
                    <p class="text-sm text-gray-500 mt-1">{{ $data->count() }} data tercatat</p>
                </div>

                <div class="overflow-x-auto" id="history-table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Waktu
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Suhu Udara (°C)
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Kelembaban Udara (%)
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Soil Analog (RAW)
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                    Kelembaban Tanah (%)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="history-tbody">
                            @forelse($data as $row)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $row->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $row->formatted_timestamp }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ number_format($row->suhu_udara, 1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ number_format($row->kelembaban_udara, 1) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $row->soil_analog }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ number_format($row->kelembaban_tanah, 1) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="mt-2">Tidak ada data history</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // ========================================
        // AUTO-REFRESH TABLE SETIAP 30 DETIK
        // ========================================
        let currentPage = {{ $data->currentPage() }};

        setInterval(async function() {
            try {
                // Reload halaman yang sama dengan pagination yang sama
                const url = new URL(window.location.href);
                url.searchParams.set('page', currentPage);
                
                // Reload halaman
                window.location.href = url.toString();
                
            } catch (error) {
                console.error('Error refreshing table:', error);
            }
        }, 30000); // 30 detik

        // Track current page dari pagination clicks
        document.addEventListener('DOMContentLoaded', function() {
            const paginationLinks = document.querySelectorAll('nav[role="navigation"] a');
            
            paginationLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    if (page) {
                        currentPage = parseInt(page);
                    }
                });
            });
        });

        // Smooth scroll to top on page load
        window.addEventListener('load', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    @endpush
</x-app-layout>
