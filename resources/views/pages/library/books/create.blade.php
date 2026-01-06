@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <nav class="mb-3 text-sm text-gray-500">
                <a href="{{ route('books.index') }}" class="hover:text-blue-600">Data Buku</a>
                <span class="mx-2">/</span>
                <span class="text-gray-800 dark:text-white">Tambah Buku</span>
            </nav>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Tambah Buku Baru</h2>
        </div>
        <a href="{{ route('books.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    {{-- Form --}}
    <div class="max-w-4xl">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">Formulir Informasi Buku Baru</h3>
            </div>
            
            <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Judul Buku --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Judul Buku <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full rounded-lg border {{ $errors->has('title') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Masukkan judul lengkap buku">
                        @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Penulis --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Penulis <span class="text-red-500">*</span></label>
                        <input type="text" name="author" id="author" value="{{ old('author') }}" required
                            class="w-full rounded-lg border {{ $errors->has('author') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Nama penulis buku">
                        @error('author')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- ISBN --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">ISBN <span class="text-red-500">*</span></label>
                        <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}" required placeholder="978-xxx-xxx-xxx-x"
                            class="w-full rounded-lg border {{ $errors->has('isbn') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 font-mono focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @error('isbn')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required
                            class="w-full rounded-lg border {{ $errors->has('category_id') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Rak --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Rak</label>
                        <select name="rack_id"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">Pilih Rak</option>
                            @foreach($racks as $rack)
                                <option value="{{ $rack->id }}" {{ old('rack_id') == $rack->id ? 'selected' : '' }}>
                                    {{ $rack->code }} - {{ $rack->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Penerbit --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Penerbit</label>
                        <input type="text" name="publisher" value="{{ old('publisher') }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Contoh: Bentang Pustaka">
                    </div>

                    {{-- Tahun --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun Terbit</label>
                        <input type="number" name="year" value="{{ old('year') }}" min="1900" max="{{ date('Y') }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="{{ date('Y') }}">
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 1) }}" required min="0"
                            class="w-full rounded-lg border {{ $errors->has('stock') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @error('stock')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Cover Image --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Cover Buku</label>
                        <x-form.form-elements.dropzone name="cover_image" accept="image/png,image/jpeg,image/webp" label="Cover Buku" />
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Tulis ringkasan buku di sini...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-all">
                        Simpan Buku
                    </button>
                    <a href="{{ route('books.index') }}" class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
