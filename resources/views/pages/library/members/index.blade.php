@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Data Anggota</h2>
            <p class="text-gray-500 dark:text-gray-400">Kelola anggota perpustakaan</p>
        </div>
        <a href="{{ route('members.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Tambah Anggota
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('members.index') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex-1">
                <x-form.input.search 
                    placeholder="Cari nama atau nomor anggota..." 
                    value="{{ request('search') }}" />
            </div>
            <select name="status" class="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
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
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Members Table --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Foto</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Nama & No. Anggota</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Kelas</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Kontak</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Berlaku Sampai</th>
                        <th class="px-4 py-3 font-medium text-gray-600 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($members as $member)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                        <td class="px-4 py-3">
                            @if($member->photo)
                                <img src="{{ Storage::url($member->photo) }}" alt="Photo" class="h-10 w-10 rounded-full object-cover">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-lg dark:bg-blue-900/30">
                                    👤
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $member->name }}</p>
                                <p class="text-xs font-mono text-gray-500">{{ $member->member_number }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $member->class ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-600 dark:text-gray-400">{{ $member->email ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $member->phone ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($member->status === 'active' && !$member->is_expired)
                                <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-600 dark:bg-green-900/30">
                                    ✅ Aktif
                                </span>
                            @elseif($member->is_expired || $member->status === 'expired')
                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-600 dark:bg-red-900/30">
                                    ❌ Expired
                                </span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700">
                                    ⏸️ Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $member->expired_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('members.show', $member) }}" class="text-blue-600 hover:text-blue-800" title="Lihat">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('members.edit', $member) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            Tidak ada data anggota. <a href="{{ route('members.create') }}" class="text-blue-600 hover:underline">Tambah anggota baru</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($members->hasPages())
        <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
            {{ $members->withQueryString()->links() }}
        </div>
        @endif
    </div>
@endsection
