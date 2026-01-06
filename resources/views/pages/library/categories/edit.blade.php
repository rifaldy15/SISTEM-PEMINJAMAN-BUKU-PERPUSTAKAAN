@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Kategori</h2>
            <p class="text-gray-500 dark:text-gray-400">Perbarui informasi kategori buku</p>
        </div>
        <a href="{{ route('categories.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="max-w-4xl">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">Formulir Edit Kategori</h3>
            </div>
            
            <form action="{{ route('categories.update', $category) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    {{-- Kode Kategori --}}
                    <div>
                        <label for="code" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $category->code) }}" required
                            class="w-full rounded-lg border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: NOVEL, AGAMA, dll">
                        @error('code')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nama Kategori --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kategori <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                            class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: Novel Fiksi, Pengetahuan Umum">
                        @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi (Opsional)</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Tulis deskripsi singkat kategori buku ini...">{{ old('description', $category->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-all">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('categories.index') }}" class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
