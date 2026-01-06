<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mode Kios - Perpustakaan Digital</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        }
        .kiosk-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .scan-area {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 3px dashed #3b82f6;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.5; }
            50% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(0.8); opacity: 0.5; }
        }
        .pulse-ring {
            animation: pulse-ring 2s ease-in-out infinite;
        }
        @keyframes success-bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .success-bounce {
            animation: success-bounce 0.5s ease-in-out;
        }
    </style>
</head>
<body class="h-full overflow-hidden">
    <div id="kioskApp" class="flex h-full flex-col items-center justify-center p-8">
        {{-- Header --}}
        <div class="mb-8 text-center text-white">
            <h1 class="mb-2 text-4xl font-bold">🏛️ PERPUSTAKAAN DIGITAL</h1>
            <p class="text-xl opacity-80">Selamat Datang di Perpustakaan</p>
        </div>

        {{-- Main Kiosk Card --}}
        <div class="kiosk-card w-full max-w-xl rounded-3xl p-8 shadow-2xl">
            {{-- Scan Area --}}
            <div id="scanArea" class="scan-area relative mb-6 rounded-2xl p-8 text-center">
                <div class="pulse-ring absolute inset-0 rounded-2xl bg-blue-200"></div>
                <div class="relative">
                    <div class="mb-4 text-6xl">📱</div>
                    <p class="text-xl font-semibold text-gray-700">SCAN KARTU ANGGOTA</p>
                    <p class="mt-2 text-gray-500">atau masukkan nomor anggota di bawah</p>
                </div>
            </div>

            {{-- Input Field --}}
            <div class="mb-6">
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔢</span>
                    <input type="text" id="memberNumber" placeholder="Masukkan Nomor Anggota..."
                        class="w-full rounded-xl border-2 border-gray-200 py-4 pl-12 pr-4 text-center text-xl font-medium focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200"
                        autofocus autocomplete="off">
                </div>
            </div>

            {{-- Check-in Button --}}
            <button id="checkinBtn" onclick="processCheckin()"
                class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 py-4 text-xl font-bold text-white shadow-lg transition hover:from-blue-700 hover:to-blue-800 hover:shadow-xl">
                ✅ CHECK-IN
            </button>

            {{-- Divider --}}
            <div class="my-6 flex items-center">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-4 text-sm text-gray-400">atau</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            {{-- Date Time --}}
            <div class="text-center text-gray-600">
                <p class="text-lg font-medium" id="currentDate">{{ now()->translatedFormat('l, d F Y') }}</p>
                <p class="text-3xl font-bold text-blue-600" id="currentTime">{{ now()->format('H:i') }}</p>
            </div>
        </div>

        {{-- Stats Footer --}}
        <div class="mt-8 rounded-xl bg-white/20 px-8 py-4 backdrop-blur">
            <div class="flex items-center gap-8 text-white">
                <div class="text-center">
                    <p class="text-3xl font-bold" id="todayCount">{{ $todayCount }}</p>
                    <p class="text-sm opacity-80">Pengunjung Hari Ini</p>
                </div>
                <div class="h-10 w-px bg-white/30"></div>
                <div class="text-center">
                    <p class="text-lg">📈 Rata-rata: <span class="font-bold">98</span>/hari</p>
                </div>
            </div>
        </div>

        {{-- Exit Link --}}
        <a href="{{ route('dashboard') }}" class="mt-6 text-white/60 hover:text-white">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- Welcome Popup (Hidden by default) --}}
    <div id="welcomePopup" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur">
        <div class="success-bounce rounded-3xl bg-white p-10 text-center shadow-2xl">
            <div id="popupIcon" class="mb-4 text-7xl">✅</div>
            <h2 id="popupTitle" class="mb-2 text-3xl font-bold text-gray-800">SELAMAT DATANG!</h2>
            <div id="popupMemberInfo" class="mb-6">
                <div class="mx-auto mb-4 h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center text-4xl">
                    👤
                </div>
                <p class="text-2xl font-semibold text-gray-800" id="popupName">Ahmad Fauzan Rahman</p>
                <p class="text-gray-500" id="popupNumber">NIS: 2024001234</p>
                <p class="text-gray-500" id="popupClass">Kelas: XII IPA 1</p>
            </div>
            <div class="rounded-lg bg-green-100 p-3">
                <p class="text-lg text-green-700">
                    🕐 Check-in: <span id="popupTime" class="font-bold">14:00</span> WIB
                </p>
            </div>
            <p class="mt-6 text-gray-400">Selamat membaca! 📚</p>
        </div>
    </div>

    {{-- Error Popup --}}
    <div id="errorPopup" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur">
        <div class="rounded-3xl bg-white p-10 text-center shadow-2xl">
            <div class="mb-4 text-7xl">❌</div>
            <h2 class="mb-2 text-2xl font-bold text-gray-800">Oops!</h2>
            <p id="errorMessage" class="text-lg text-red-600">Anggota tidak ditemukan</p>
        </div>
    </div>

    <script>
        // Update clock
        function updateClock() {
            const now = new Date();
            document.getElementById('currentTime').textContent = 
                now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        setInterval(updateClock, 1000);

        // Handle Enter key
        document.getElementById('memberNumber').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                processCheckin();
            }
        });

        // Process check-in
        async function processCheckin() {
            const memberNumber = document.getElementById('memberNumber').value.trim();
            
            if (!memberNumber) {
                showError('Masukkan nomor anggota!');
                return;
            }

            try {
                const response = await fetch('{{ route("kiosk.checkin") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ member_number: memberNumber })
                });

                const data = await response.json();

                if (data.success) {
                    showWelcome(data);
                    document.getElementById('todayCount').textContent = data.today_count || 
                        (parseInt(document.getElementById('todayCount').textContent) + 1);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError('Terjadi kesalahan. Silakan coba lagi.');
            }

            document.getElementById('memberNumber').value = '';
            document.getElementById('memberNumber').focus();
        }

        function showWelcome(data) {
            const popup = document.getElementById('welcomePopup');
            const isCheckout = data.type === 'checkout';
            
            document.getElementById('popupIcon').textContent = isCheckout ? '👋' : '✅';
            document.getElementById('popupTitle').textContent = isCheckout ? 'SAMPAI JUMPA!' : 'SELAMAT DATANG!';
            document.getElementById('popupName').textContent = data.member.name;
            document.getElementById('popupNumber').textContent = 'No: ' + data.member.member_number;
            document.getElementById('popupClass').textContent = data.member.class || '';
            document.getElementById('popupTime').textContent = isCheckout ? data.check_out : data.check_in;

            popup.classList.remove('hidden');
            popup.classList.add('flex');

            // Play sound (optional)
            // new Audio('/sounds/ding.mp3').play();

            setTimeout(() => {
                popup.classList.add('hidden');
                popup.classList.remove('flex');
            }, 3000);
        }

        function showError(message) {
            const popup = document.getElementById('errorPopup');
            document.getElementById('errorMessage').textContent = message;
            
            popup.classList.remove('hidden');
            popup.classList.add('flex');

            setTimeout(() => {
                popup.classList.add('hidden');
                popup.classList.remove('flex');
            }, 3000);
        }

        // Screensaver after 5 minutes of inactivity (optional)
        let idleTimeout;
        function resetIdleTimer() {
            clearTimeout(idleTimeout);
            idleTimeout = setTimeout(() => {
                // Could redirect to screensaver or dim screen
            }, 300000); // 5 minutes
        }
        document.addEventListener('mousemove', resetIdleTimer);
        document.addEventListener('keypress', resetIdleTimer);
        resetIdleTimer();
    </script>
</body>
</html>
