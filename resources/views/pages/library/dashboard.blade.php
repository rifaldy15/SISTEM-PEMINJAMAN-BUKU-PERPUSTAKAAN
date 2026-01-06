@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Dashboard Perpustakaan</h2>
            <div x-data="{ 
                date: '{{ now()->translatedFormat('l, d F Y') }}',
                time: '{{ now()->format('H:i:s') }}',
                init() {
                    setInterval(() => {
                        const now = new Date();
                        // Format time manually to match local timezone
                        this.time = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        // Update date optionally if it changes
                    }, 1000);
                }
            }" class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                <span x-text="date"></span>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <span x-text="time" class="font-mono font-medium text-blue-600 dark:text-blue-400"></span>
                <span class="text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 px-1.5 py-0.5 rounded">WITA</span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
        {{-- Visitors Today --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="flex items-center gap-1 text-sm font-medium {{ $stats['visitors_change']['direction'] === 'up' ? 'text-green-600' : 'text-red-500' }}">
                    {{ $stats['visitors_change']['direction'] === 'up' ? '↑' : '↓' }}
                    {{ $stats['visitors_change']['value'] }}%
                </span>
            </div>
            <div class="mt-4">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['visitors_today'] }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pengunjung Hari Ini</p>
            </div>
        </div>

        {{-- Books Borrowed --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['books_borrowed'] }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">Buku Dipinjam</p>
            </div>
        </div>

        {{-- Returns Today --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/30">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['returned_today'] }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kembali Hari Ini</p>
            </div>
        </div>

        {{-- Overdue --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                @if($stats['overdue'] > 0)
                <span class="inline-flex items-center justify-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600 dark:bg-red-900/30">
                    {{ $stats['overdue'] }} buku
                </span>
                @endif
            </div>
            <div class="mt-4">
                <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['overdue'] }}</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">Terlambat</p>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mb-6">
        {{-- Visitor Chart --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">📈 Grafik Kunjungan (7 Hari Terakhir)</h3>
            <div id="visitorChart" style="height: 300px;"></div>
        </div>

        {{-- Top Books --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">📊 Buku Terpopuler Bulan Ini</h3>
            <div class="space-y-3">
                @forelse($topBooks as $index => $book)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-600 dark:bg-blue-900/30">
                            {{ $index + 1 }}
                        </span>
                        <span class="text-sm font-medium text-gray-800 dark:text-white">{{ Str::limit($book['title'], 30) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @php $width = (int)($book['count'] * 4); @endphp
                        <div class="h-2 rounded-full bg-blue-500" style="width:{{ $width }}px;"></div>
                        <span class="text-sm text-gray-500">{{ $book['count'] }}</span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Belum ada data peminjaman bulan ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Doughnut Chart --}}
        <x-chart.doughnut-chart :bookCondition="$bookCondition" />

        {{-- Recent Activity --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 h-full">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">🔔 Aktivitas Terbaru</h3>
            <div class="space-y-3 max-h-[280px] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($recentActivity as $activity)
                <div class="flex items-start gap-3 rounded-lg p-2 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <span class="text-xl shrink-0">{{ $activity['icon'] }}</span>
                    <div class="flex-1">
                        <p class="text-sm text-gray-800 dark:text-white leading-snug">{{ $activity['description'] }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $activity['time'] }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">Belum ada aktivitas hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom Row (Full Width or 2 cols) --}}
    <div class="grid grid-cols-1 mb-6">
        {{-- Overdue Books --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">⚠️ Buku Terlambat</h3>
                <a href="{{ route('circulation.active', ['status' => 'overdue']) }}" class="text-sm text-blue-600 hover:underline">Lihat Semua →</a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($overdueBooks as $item)
                <div class="flex items-center justify-between rounded-lg bg-red-50 dark:bg-red-900/20 p-3 border border-red-100 dark:border-red-900/30">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white text-sm">{{ Str::limit($item['title'], 25) }}</p>
                        <p class="text-[11px] text-gray-500">{{ $item['member'] }}</p>
                    </div>
                    <span class="rounded-full bg-red-100 px-2 py-1 text-[10px] font-bold text-red-600 dark:bg-red-900/30 shrink-0">
                        {{ $item['days_overdue'] }} hari
                    </span>
                </div>
                @empty
                <div class="col-span-full py-4 text-center">
                    <p class="text-gray-500 text-sm italic">Tidak ada buku terlambat! 🎉</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Visitor Chart
    var options = {
        series: [{
            name: 'Pengunjung',
            data: {!! json_encode($visitorChart['data']) !!}
        }],
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: false
            },
            fontFamily: 'Inter, sans-serif',
        },
        colors: ['#2563eb'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
            }
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: {!! json_encode($visitorChart['labels']) !!},
            labels: {
                style: {
                    colors: '#64748b'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#64748b'
                }
            }
        },
        grid: {
            borderColor: '#e2e8f0',
            strokeDashArray: 4
        },
        tooltip: {
            theme: 'dark'
        }
    };

    var chart = new ApexCharts(document.querySelector("#visitorChart"), options);
    chart.render();

    // Book Condition Doughnut Chart
    var doughnutOptions = {
        series: [{{ $bookCondition['available'] }}, {{ $bookCondition['borrowed'] }}, {{ $bookCondition['lost'] }}],
        chart: {
            type: 'donut',
            width: '100%',
            height: 250,
            fontFamily: 'Inter, sans-serif',
        },
        labels: ['Tersedia', 'Dipinjam', 'Rusak/Hilang'],
        colors: ['#2563eb', '#06b6d4', '#e2e8f0'],
        legend: {
            show: false
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            offsetY: -10
                        },
                        value: {
                            show: true,
                            offsetY: 10,
                            formatter: function (val) {
                                return val + " Buku";
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0) + " Buku"
                            }
                        }
                    }
                }
            }
        },
        stroke: {
            width: 0
        },
        tooltip: {
            theme: 'dark'
        }
    };

    var doughnutChart = new ApexCharts(document.querySelector("#bookConditionChart"), doughnutOptions);
    doughnutChart.render();
</script>
@endpush
