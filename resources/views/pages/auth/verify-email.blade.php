@extends('layouts.app')

@section('content')
<div class="flex h-screen items-center justify-center bg-gray-100 dark:bg-brand-950">
    <div class="w-full max-w-md rounded-lg bg-white p-8 shadow-lg dark:bg-white/5">
        <div class="text-center">
            <h2 class="mb-4 text-2xl font-bold text-gray-800 dark:text-white">Verifikasi Email Anda</h2>
            <p class="mb-6 text-gray-600 dark:text-gray-400">
                Terima kasih telah mendaftar! Sebelum memulai, mohon verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan. 
                Jika Anda tidak menerima email, kami dengan senang hati akan mengirimkan yang baru.
            </p>

            @if (session('message'))
                <div class="mb-4 text-sm font-medium text-green-600">
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            @endif

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white underline">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
