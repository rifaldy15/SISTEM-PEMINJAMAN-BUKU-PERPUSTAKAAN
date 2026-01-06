@extends('layouts.app')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Buku</h2>
            <p class="text-gray-500 dark:text-gray-400">Informasi lengkap buku</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('books.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                Kembali
            </a>
            <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center justify-center rounded-lg bg-yellow-400 px-4 py-2.5 text-sm font-medium text-white hover:bg-yellow-500 focus:outline-none focus:ring-4 focus:ring-yellow-300 dark:focus:ring-yellow-900">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Buku
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Book Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-6 md:flex-row">
                    <div class="shrink-0">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-64 w-48 rounded-lg object-cover shadow-md">
                        @else
                            <div class="flex h-64 w-48 items-center justify-center rounded-lg bg-gray-100 text-gray-400 dark:bg-gray-700">
                                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="mb-4">
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30">
                                {{ $book->category->name }}
                            </span>
                            <h1 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $book->title }}</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400">{{ $book->author }}</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">ISBN</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $book->isbn }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Penerbit</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $book->publisher ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Tahun Terbit</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $book->year ?? '-' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi Rak</h4>
                                <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $book->rack->code ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Deskripsi</h4>
                            <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                {{ $book->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- History Table --}}
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Peminjaman Terakhir</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Peminjam</th>
                                <th class="px-6 py-3">Tanggal Pinjam</th>
                                <th class="px-6 py-3">Tanggal Kembali</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($book->transactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        {{ $transaction->member->name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $transaction->borrowed_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                        {{ $transaction->returned_at ? $transaction->returned_at->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($transaction->status === 'returned')
                                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Dikembalikan
                                            </span>
                                        @elseif($transaction->status === 'lost')
                                            <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                Hilang
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                Dipinjam
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
        </div>

        {{-- Availability Status --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Status Buku</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Total Stok</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $book->stock }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Tersedia</span>
                        <span class="font-bold text-green-600 dark:text-green-400">{{ $book->available }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Sedang Dipinjam</span>
                        <span class="font-bold text-yellow-600 dark:text-yellow-400">{{ $book->stock - $book->available }}</span>
                    </div>
                </div>

                <div class="mt-6">
                     @if($book->available > 0)
                        <div class="flex w-full items-center justify-center rounded-lg bg-green-100 p-3 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-medium">Tersedia untuk dipinjam</span>
                        </div>
                    @else
                        <div class="flex w-full items-center justify-center rounded-lg bg-red-100 p-3 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                            <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="font-medium">Stok Kosong</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">QR Code</h3>
                <div class="flex flex-col items-center justify-center space-y-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $book->isbn }}" alt="QR Code ISBN" class="rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $book->isbn }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
