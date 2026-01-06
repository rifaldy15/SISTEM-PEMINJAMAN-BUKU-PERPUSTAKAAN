<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Anggota - {{ $member->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none;
            }
        }
        
        .id-card {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .pattern-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
            background-image: radial-gradient(#ffffff 1px, transparent 1px);
            background-size: 10px 10px;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="no-print mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pratinjau Kartu Anggota</h1>
        <p class="text-gray-600 mb-4">Pastikan layout sesuai sebelum mencetak.</p>
        <div class="flex justify-center gap-4">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Kartu
            </button>
            <button onclick="window.close()" class="bg-white text-gray-700 border border-gray-300 px-6 py-2 rounded-lg font-medium hover:bg-gray-50 transition-colors shadow-sm">
                Tutup
            </button>
        </div>
    </div>

    <!-- ID Card Container -->
    <div class="id-card flex flex-col p-4">
        <!-- Background Pattern -->
        <div class="pattern-overlay"></div>

        <!-- Header -->
        <div class="relative z-10 flex justify-between items-start mb-3">
            <div class="flex items-center gap-2">
                <!-- Logo Placehoder - Replace with your logo -->
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xs font-bold uppercase tracking-wider opacity-90">Perpustakaan</h1>
                    <p class="text-[0.6rem] opacity-75 leading-tight">Digital Library System</p>
                </div>
            </div>
            <div class="text-[0.6rem] font-mono bg-white/20 px-2 py-0.5 rounded backdrop-blur-sm">
                MEMBER CARD
            </div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex gap-4 items-center flex-1">
            <!-- Photo -->
            <div class="shrink-0">
                @if($member->photo)
                    <img src="{{ asset('storage/' . $member->photo) }}" alt="" class="w-16 h-16 rounded-lg object-cover border-2 border-white/30 shadow-sm bg-white">
                @else
                    <div class="w-16 h-16 rounded-lg bg-white/20 backdrop-blur-sm border-2 border-white/30 flex items-center justify-center text-white font-bold text-xl shadow-sm">
                        {{ substr($member->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <!-- Details -->
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-bold truncate leading-tight mb-1">{{ $member->name }}</h2>
                <div class="space-y-0.5">
                    <p class="text-[0.6rem] opacity-75 uppercase tracking-wide">Nomor Anggota</p>
                    <p class="font-mono text-xs font-semibold tracking-wide">{{ $member->member_number }}</p>
                </div>
                <div class="mt-2 space-y-0.5">
                    <p class="text-[0.6rem] opacity-75 uppercase tracking-wide">Berlaku Hingga</p>
                    <p class="text-[0.65rem] font-semibold">{{ $member->expired_at->format('d M Y') }}</p>
                </div>
            </div>

            <!-- QR Code -->
            <div class="shrink-0 bg-white p-1 rounded-lg">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $member->member_number }}" alt="QR" class="w-12 h-12">
            </div>
        </div>

        <!-- Footer Strip -->
        <div class="relative z-10 mt-auto pt-2 border-t border-white/20 flex justify-between items-center text-[0.5rem] opacity-75">
            <span>www.library.com</span>
            <span>Syarat & Ketentuan Berlaku</span>
        </div>
    </div>

</body>
</html>
