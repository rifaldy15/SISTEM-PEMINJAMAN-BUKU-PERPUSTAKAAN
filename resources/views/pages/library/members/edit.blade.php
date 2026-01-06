@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Anggota</h2>
            <p class="text-gray-500 dark:text-gray-400">Perbarui data anggota: {{ $member->name }}</p>
        </div>
        <a href="{{ route('members.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="max-w-4xl">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">Formulir Edit Data Anggota</h3>
            </div>
            
            <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid gap-6 md:grid-cols-2">
                    {{-- Nama Lengkap --}}
                    <div class="md:col-span-2">
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $member->name) }}" required
                            class="w-full rounded-lg border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Masukkan nama lengkap anggota">
                        @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Kelas/Jabatan --}}
                    <div>
                        <label for="class" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Kelas / Jabatan</label>
                        <div x-data="{ isNew: {{ old('class_type', 'existing') === 'new' ? 'true' : 'false' }} }">
                            <div class="flex flex-col gap-2">
                                <template x-if="!isNew">
                                    <div class="flex gap-2">
                                        <select name="class_select" @change="if($el.value === '__new__') { isNew = true; } else { $refs.classInput.value = $el.value; }"
                                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Pilih Kelas/Jabatan</option>
                                            @if(is_array($classes))
                                                @foreach($classes as $cls)
                                                    <option value="{{ $cls }}" {{ old('class', $member->class) == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                                                @endforeach
                                            @endif
                                            <option value="__new__" class="font-bold text-blue-600">+ Tambah Baru</option>
                                        </select>
                                    </div>
                                </template>
                                
                                <div x-show="isNew" class="relative">
                                    <input type="text" name="class" x-ref="classInput" value="{{ old('class', $member->class) }}"
                                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="Ketik kelas/jabatan...">
                                    <button type="button" @click="isNew = false" class="mt-2 text-xs text-blue-600 hover:underline">
                                        ← Kembali ke pilihan
                                    </button>
                                </div>
                                <input type="hidden" name="class_type" :value="isNew ? 'new' : 'existing'">
                                <input type="hidden" name="class" x-model="$refs.classInput.value" x-show="!isNew">
                            </div>
                        </div>
                        @error('class')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="contoh@email.com">
                        @error('email')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $member->phone) }}"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="08xxxxxxxxxx">
                        @error('phone')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Keanggotaan <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="expired" {{ old('status', $member->status) == 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                        </select>
                    </div>

                    {{-- Expired At --}}
                    <div>
                        <label for="expired_at" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Masa Berlaku Hingga <span class="text-red-500">*</span></label>
                        <input type="date" name="expired_at" id="expired_at" value="{{ old('expired_at', $member->expired_at->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @error('expired_at')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Max Borrow --}}
                    <div>
                        <label for="max_borrow" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Batasan Pinjam Buku <span class="text-red-500">*</span></label>
                        <input type="number" name="max_borrow" id="max_borrow" value="{{ old('max_borrow', $member->max_borrow) }}" min="1" max="10" required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500">Maksimal buku yang dapat dipinjam sekaligus.</p>
                        @error('max_borrow')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Foto --}}
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Foto Profil</label>
                        @if($member->photo)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $member->photo) }}" alt="Current Photo" class="h-24 w-24 rounded-lg object-cover shadow-sm ring-1 ring-gray-200">
                                <p class="mt-1 text-xs text-gray-500">Foto saat ini</p>
                            </div>
                        @endif
                        <x-form.form-elements.dropzone name="photo" accept="image/png,image/jpeg,image/webp" label="Ubah Foto Profil" />
                        @error('photo')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="md:col-span-2">
                        <label for="address" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            placeholder="Masukkan alamat lengkap anggota...">{{ old('address', $member->address) }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-8 flex items-center gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-all">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('members.index') }}" class="rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
        </form>
    </div>
@endsection
