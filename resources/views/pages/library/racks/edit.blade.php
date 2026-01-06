@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Rak</h2>
            <p class="text-gray-500 dark:text-gray-400">Perbarui informasi lokasi rak</p>
        </div>
        <a href="{{ route('racks.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="max-w-3xl">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">Formulir Informasi Rak</h3>
            </div>
            
            <form action="{{ route('racks.update', $rack) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- Kode Rak --}}
                    <div>
                        <label for="code" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kode Rak <span class="text-red-500">*</span></label>
                        <input type="text" name="code" id="code" value="{{ old('code', $rack->code) }}" required
                            class="w-full rounded-lg border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Contoh: R-001">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Rak --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Rak <span class="text-red-500">*</span></label>
                        <div x-data="{ isNew: {{ old('name_type', 'existing') === 'new' ? 'true' : 'false' }} }">
                            <div class="flex flex-col gap-2">
                                <template x-if="!isNew">
                                    <div class="flex gap-2">
                                        <select name="name_select" @change="if($el.value === '__new__') { isNew = true; } else { $refs.nameInput.value = $el.value; }"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Pilih Nama Rak</option>
                                            @foreach($names as $n)
                                                <option value="{{ $n }}" {{ old('name', $rack->name) == $n ? 'selected' : '' }}>{{ $n }}</option>
                                            @endforeach
                                            <option value="__new__" class="font-bold text-blue-600">+ Tambah Nama Baru</option>
                                        </select>
                                    </div>
                                </template>
                                
                                <div x-show="isNew" class="relative">
                                    <input type="text" name="name" x-ref="nameInput" value="{{ old('name', $rack->name) }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Masukkan nama rak baru...">
                                    <button type="button" @click="isNew = false" class="mt-2 text-xs text-blue-600 hover:underline">
                                        ← Kembali ke pilihan
                                    </button>
                                </div>
                                <input type="hidden" name="name_type" :value="isNew ? 'new' : 'existing'">
                                <input type="hidden" name="name" x-model="$refs.nameInput.value" x-show="!isNew">
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lokasi / Baris --}}
                    <div>
                        <label for="location" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi / Baris (Opsional)</label>
                        <div x-data="{ isNew: {{ old('location_type', 'existing') === 'new' ? 'true' : 'false' }} }">
                            <div class="flex flex-col gap-2">
                                <template x-if="!isNew">
                                    <div class="flex gap-2">
                                        <select name="location_select" @change="if($el.value === '__new__') { isNew = true; } else { $refs.locInput.value = $el.value; }"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Pilih Lokasi</option>
                                            @foreach($locations as $loc)
                                                <option value="{{ $loc }}" {{ old('location', $rack->location) == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                            @endforeach
                                            <option value="__new__" class="font-bold text-blue-600">+ Tambah Lokasi Baru</option>
                                        </select>
                                    </div>
                                </template>
                                
                                <div x-show="isNew" class="relative">
                                    <input type="text" name="location" x-ref="locInput" value="{{ old('location', $rack->location) }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Masukkan lokasi baru...">
                                    <button type="button" @click="isNew = false" class="mt-2 text-xs text-blue-600 hover:underline">
                                        ← Kembali ke pilihan
                                    </button>
                                </div>
                                <input type="hidden" name="location_type" :value="isNew ? 'new' : 'existing'">
                                {{-- Initial value for hidden input if not new --}}
                                <input type="hidden" x-ref="hiddenLoc" name="location" x-model="$refs.locInput.value" x-show="!isNew">
                            </div>
                        </div>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kapasitas --}}
                    <div>
                        <label for="capacity" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kapasitas Maksimal</label>
                        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $rack->capacity) }}" required min="1" max="500"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        @error('capacity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('racks.index') }}" class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
