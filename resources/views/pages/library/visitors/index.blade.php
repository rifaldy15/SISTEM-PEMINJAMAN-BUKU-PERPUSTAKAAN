@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Absensi Pengunjung</h2>
            <p class="text-gray-500 dark:text-gray-400">Log kunjungan hari ini: {{ request('date') ?? now()->format('d M Y') }}</p>
        </div>
        <a href="{{ route('kiosk') }}" target="_blank" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
            🖥️ Buka Mode Kios
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-xl dark:bg-blue-900/30">
                    👥
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['today'] }}</p>
                    <p class="text-sm text-gray-500">Total Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-xl dark:bg-green-900/30">
                    🏠
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['still_in'] }}</p>
                    <p class="text-sm text-gray-500">Masih di Perpustakaan</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 text-xl dark:bg-purple-900/30">
                    📊
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['average'] }}</p>
                    <p class="text-sm text-gray-500">Rata-rata/Hari</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Date Filter --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('visitors.index') }}" method="GET" class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal:</label>
            <input type="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Visitors Table --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Anggota</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Check In</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Check Out</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Durasi</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($visitors as $visitor)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-lg dark:bg-blue-900/30">
                                    👤
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $visitor->member->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $visitor->member->class ?? $visitor->member->member_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-800 dark:text-white">{{ $visitor->check_in->format('H:i') }}</span>
                            <span class="text-gray-500">WIB</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($visitor->check_out)
                                <span class="font-medium text-gray-800 dark:text-white">{{ $visitor->check_out->format('H:i') }}</span>
                                <span class="text-gray-500">WIB</span>
                            @else
                                <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-600 dark:bg-green-900/30">
                                    Masih di sini
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            @if($visitor->duration)
                                {{ floor($visitor->duration / 60) }}j {{ $visitor->duration % 60 }}m
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if(!$visitor->check_out)
                                <form action="{{ route('visitors.checkout', $visitor) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700">
                                        Check Out
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Belum ada pengunjung pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($visitors->hasPages())
        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            {{ $visitors->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
