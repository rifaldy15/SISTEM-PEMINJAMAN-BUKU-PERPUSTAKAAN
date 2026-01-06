@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Data Denda</h2>
            <p class="text-gray-500 dark:text-gray-400">Kelola dan pantau denda keterlambatan</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        {{-- Total Unpaid --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Belum Dibayar</p>
                    <h4 class="text-2xl font-bold text-red-600">Rp {{ number_format($stats['total_unpaid'], 0, ',', '.') }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/30">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Collected --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Terkumpul</p>
                    <h4 class="text-2xl font-bold text-green-600">Rp {{ number_format($stats['total_collected'], 0, ',', '.') }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/30">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Unpaid Count --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Kasus Belum Lunas</p>
                    <h4 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['unpaid_count'] }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/30">
                    <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('fines.index') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <select name="status" class="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
            </select>
            @if(request()->has('status'))
                <a href="{{ route('fines.index') }}" class="text-sm text-red-600 hover:text-red-800 hover:underline">Reset Filter</a>
            @endif
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Fines Table --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Anggota</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Buku</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Tgl Pinjam/Kembali</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Jumlah Denda</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($fines as $fine)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-800 dark:text-white">
                                {{ $fine->transaction->member->name }}
                            </span>
                            <span class="block text-xs text-gray-500">{{ $fine->transaction->member->member_code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600 dark:text-gray-300">{{ Str::limit($fine->transaction->book->title, 30) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-xs">
                                <span class="block text-gray-500">Pinjam: {{ $fine->transaction->borrowed_at->format('d M Y') }}</span>
                                <span class="block text-gray-500">Jatuh Tempo: {{ $fine->transaction->due_date->format('d M Y') }}</span>
                                <span class="block text-red-500">Kembali: {{ $fine->transaction->returned_at ? $fine->transaction->returned_at->format('d M Y') : '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-bold text-red-600">
                            Rp {{ number_format($fine->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($fine->is_paid)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Lunas
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    Belum Lunas
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if(!$fine->is_paid)
                                    <form action="{{ route('fines.mark-as-paid', $fine) }}" method="POST" class="inline" onsubmit="return confirmAction(event, 'Tandai denda ini sebagai sudah dibayar?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700" title="Bayar">
                                            Bayar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Sudah Lunas</span>
                                @endif

                                <form action="{{ route('fines.destroy', $fine) }}" method="POST" class="inline" onsubmit="return confirmAction(event, 'Apakah Anda yakin ingin menghapus data denda ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data denda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($fines->hasPages())
        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            {{ $fines->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
