@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Anggota</h2>
            <p class="text-gray-500 dark:text-gray-400">Informasi lengkap anggota perpustakaan</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('members.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                Kembali
            </a>
            <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center justify-center rounded-lg bg-yellow-400 px-4 py-2.5 text-sm font-medium text-white hover:bg-yellow-500 focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:focus:ring-yellow-900">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Anggota
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Member Profile Card --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-6 md:flex-row">
                    <div class="shrink-0">
                        @if($member->photo)
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="h-40 w-40 rounded-lg object-cover shadow-md">
                        @else
                            <div class="flex h-40 w-40 items-center justify-center rounded-lg bg-gray-100 text-6xl font-bold text-gray-300 dark:bg-gray-700 dark:text-gray-500">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="mb-4">
                            <div class="mb-2 flex items-center gap-2">
                                @if($member->is_expired)
                                    <span class="inline-flex rounded-md bg-red-100 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-700/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/30">
                                        Kadaluarsa
                                    </span>
                                @elseif($member->status == 'active')
                                    <span class="inline-flex rounded-md bg-green-100 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-700/10 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/30">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-700/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30">
                                        Non-Aktif
                                    </span>
                                @endif
                                <span class="text-sm text-gray-500 dark:text-gray-400">#{{ $member->member_number }}</span>
                            </div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $member->name }}</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400">{{ $member->class ?? '-' }}</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $member->email ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Telepon</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $member->phone ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Bergabung Sejak</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $member->joined_at->format('d M Y') }}</p>
                            </div>
                             <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Masa Berlaku</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $member->expired_at->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                             <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Alamat</h4>
                             <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $member->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Peminjaman Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Buku</th>
                                <th class="px-6 py-3">Tgl Pinjam</th>
                                <th class="px-6 py-3">Tgl Kembali</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($member->transactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $transaction->book->title }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $transaction->borrowed_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $transaction->returned_at ? $transaction->returned_at->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($transaction->status == 'borrowed')
                                            <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                Dipinjam
                                            </span>
                                        @elseif($transaction->status == 'returned')
                                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Dikembalikan
                                            </span>
                                        @elseif($transaction->status == 'overdue')
                                            <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                Terlambat
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada riwayat peminjaman.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
             {{-- Recent Visits --}}
             <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Kunjungan Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Jam Masuk</th>
                                <th class="px-6 py-3">Keperluan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($member->visits as $visit)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $visit->check_in->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $visit->check_in->format('H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $visit->purpose }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada riwayat kunjungan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Stats & Actions --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Stats --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Statistik</h3>
                
                <div class="space-y-4">
                     <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Sedang Dipinjam</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">{{ $member->activeTransactions->count() }} / {{ $member->max_borrow }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Total Peminjaman</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $member->transactions()->count() }}</span>
                    </div>
                     <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Total Kunjungan</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $member->visits()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Denda</span>
                        <span class="font-bold text-red-600 dark:text-red-400">Rp {{ number_format($member->unpaid_fines ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Aksi Cepat</h3>
                <div class="flex flex-col gap-3">
                    <form action="{{ route('members.extend', $member) }}" method="POST">
                        @csrf
                        <input type="hidden" name="years" value="1">
                        <button type="submit" class="w-full justify-center rounded-lg bg-blue-50 px-4 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50">
                            Perpanjang 1 Tahun
                        </button>
                    </form>
                    <a href="{{ route('members.card', ['member_id' => $member->id]) }}" target="_blank" class="flex w-full justify-center items-center rounded-lg bg-gray-50 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Cetak Kartu
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
