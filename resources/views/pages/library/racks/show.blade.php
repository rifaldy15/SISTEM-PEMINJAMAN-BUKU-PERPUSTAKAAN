@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Detail Lokasi Rak</h2>
            <p class="text-gray-500 dark:text-gray-400">Informasi lengkap dan daftar buku di {{ $rack->code }}</p>
        </div>
        <a href="{{ route('racks.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Rack Info Card --}}
        <div class="space-y-6 lg:col-span-1">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Informasi Rak</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Kode Rak</p>
                        <p class="text-lg font-bold text-blue-600">{{ $rack->code }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Nama Rak</p>
                        <p class="text-gray-800 dark:text-white font-medium">{{ $rack->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Lokasi / Baris</p>
                        <p class="text-gray-800 dark:text-white">{{ $rack->location ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Kapasitas</p>
                        <div class="mt-1 flex items-center gap-3">
                            <div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-gray-800">
                                @php
                                    $current = (int) ($rack->books_sum_stock ?? 0);
                                    $percentage = $rack->capacity > 0 ? ($current / $rack->capacity) * 100 : 0;
                                    $colorClass = $percentage > 90 ? 'bg-red-500' : ($percentage > 70 ? 'bg-yellow-500' : 'bg-blue-500');
                                @endphp
                                @php $barWidth = min($percentage, 100); @endphp
                                <div class="h-2 rounded-full {{ $colorClass }} transition-all duration-500" style="width:{{ $barWidth }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ (int) ($rack->books_sum_stock ?? 0) }} / {{ $rack->capacity }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-2">
                    <a href="{{ route('racks.edit', $rack) }}" class="inline-flex flex-1 items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Edit Rak
                    </a>
                </div>
            </div>
        </div>

        {{-- Books List Card --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-800 dark:text-white">Daftar Buku di Rak Ini</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Judul Buku</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Stok</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @forelse($rack->books as $book)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($book->cover_image)
                                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-10 w-8 rounded object-cover shadow-sm">
                                            @else
                                                <div class="flex h-10 w-8 items-center justify-center rounded bg-gray-100 text-gray-400 dark:bg-gray-800">
                                                    📚
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800 dark:text-white">{{ $book->title }}</p>
                                                <p class="text-xs text-gray-500">ISBN: {{ $book->isbn }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                            {{ $book->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">
                                        {{ $book->stock }} eks
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('books.edit', $book) }}" class="font-medium text-blue-600 hover:underline dark:text-blue-500">
                                            Kelola
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada buku yang disimpan di rak ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
