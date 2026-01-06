@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Daftar Peminjaman Aktif</h2>
            <p class="text-gray-500 dark:text-gray-400">Buku yang sedang dipinjam oleh anggota</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('circulation.borrow') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                📥 Peminjaman Baru
            </a>
            <a href="{{ route('circulation.return') }}" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700">
                📤 Pengembalian
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('circulation.active') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex-1">
                <x-form.input.search 
                    placeholder="Cari judul buku atau nama anggota..." 
                    value="{{ request('search') }}" />
            </div>
            <select name="status" class="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Belum Jatuh Tempo</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
            </select>
            <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                Filter
            </button>
        </form>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    {{-- Transactions Table --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Buku</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Peminjam</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Tgl Pinjam</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Jatuh Tempo</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">{{ Str::limit($transaction->book->title, 35) }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $transaction->book->isbn }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $transaction->member->name }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->member->member_number }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $transaction->borrowed_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $transaction->due_date->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($transaction->is_overdue)
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-600 dark:bg-red-900/30">
                                    ⚠️ Terlambat {{ $transaction->due_date->diffInDays(today()) }} hari
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-600 dark:bg-green-900/30">
                                    ✅ {{ today()->diffInDays($transaction->due_date) }} hari lagi
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada peminjaman aktif.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
