@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Laporan Kunjungan</h2>
            <p class="text-gray-500 dark:text-gray-400">Analisis data pengunjung perpustakaan</p>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('reports.visitors') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
            <div class="flex-1 sm:flex-none">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:w-40">
            </div>
            <div class="flex-1 sm:flex-none">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:w-40">
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900 sm:w-auto">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter
            </button>
        </form>
    </div>

    {{-- Summary Stats --}}
    <div class="mb-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kunjungan</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['total_visits']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata/Hari</h3>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['average_daily']) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-300">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Hari Tersibuk</h3>
                    @if($summary['peak_day'])
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($summary['peak_day']->date)->translatedFormat('l, d M') }}</p>
                        <p class="text-xs text-gray-500">({{ $summary['peak_day']->visitor_count }} pengunjung)</p>
                    @else
                        <p class="text-lg font-bold text-gray-900 dark:text-white">-</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Hourly Distribution --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">Kesibukan per Jam</h3>
            </div>
            <div class="p-6">
                @if($hourlyDistribution->count() > 0)
                    <div class="space-y-4">
                        @foreach($hourlyDistribution as $hourStats)
                            <div>
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ sprintf('%02d:00', $hourStats->hour) }} - {{ sprintf('%02d:00', $hourStats->hour + 1) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $hourStats->count }} orang</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-700">
                                    @php
                                        $max = $hourlyDistribution->max('count');
                                        $width = ($hourStats->count / $max) * 100;
                                    @endphp
                                    @php $barWidth = ($hourStats->count / $max) * 100; @endphp
                                    <div class="h-2 rounded-full bg-blue-600" style="width:{{ $barWidth }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-gray-500 dark:text-gray-400">Tidak ada data kunjungan.</div>
                @endif
            </div>
        </div>

        {{-- Top Visitors --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white">Pengunjung Terrajin</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Nama Anggota</th>
                                <th class="px-4 py-3 text-center">Jumlah Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($topVisitors as $visitor)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $visitor->member->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $visitor->member->class }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-purple-600 dark:text-purple-400">
                                        {{ number_format($visitor->visit_count) }}x
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
