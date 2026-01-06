@extends('layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Cetak Kartu Anggota</h2>
            <p class="text-gray-500 dark:text-gray-400">Pilih anggota untuk mencetak kartu perpustakaan</p>
        </div>
        <a href="{{ route('members.index') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Kembali
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">Daftar Anggota Aktif</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse($members as $member)
                    <div class="flex flex-col rounded-lg border border-gray-200 bg-gray-50 p-4 transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-900">
                        <div class="mb-4 flex items-center gap-4">
                            @if($member->photo)
                                <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}" class="h-12 w-12 rounded-full object-cover">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="overflow-hidden">
                                <h4 class="truncate text-base font-semibold text-gray-900 dark:text-white" title="{{ $member->name }}">{{ $member->name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->member_number }}</p>
                            </div>
                        </div>
                        
                        <div class="mt-auto pt-2">
                            <a href="{{ route('members.card', ['member_id' => $member->id]) }}" target="_blank" class="flex w-full items-center justify-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak Kartu
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-500 dark:text-gray-400">
                        Tidak ada anggota aktif yang ditemukan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
