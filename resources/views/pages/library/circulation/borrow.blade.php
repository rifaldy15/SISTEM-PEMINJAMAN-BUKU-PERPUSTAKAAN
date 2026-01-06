@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Peminjaman Buku</h2>
            <p class="text-gray-500 dark:text-gray-400">Proses peminjaman buku untuk anggota</p>
        </div>
        <a href="{{ route('circulation.active') }}" class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-700 dark:bg-red-900/30 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- Borrowing Form --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('circulation.processBorrow') }}" method="POST" id="borrowForm">
            @csrf

            {{-- Step Indicator --}}
            <div class="mb-8 flex items-center justify-center">
                <div class="flex items-center">
                    <div id="step1Indicator" class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white font-bold">1</div>
                    <span class="ml-2 font-medium text-blue-600">Scan Anggota</span>
                </div>
                <div class="mx-4 h-1 w-16 bg-gray-200 dark:bg-gray-700" id="line1"></div>
                <div class="flex items-center">
                    <div id="step2Indicator" class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-500 font-bold dark:bg-gray-700">2</div>
                    <span class="ml-2 text-gray-500" id="step2Label">Scan Buku</span>
                </div>
                <div class="mx-4 h-1 w-16 bg-gray-200 dark:bg-gray-700" id="line2"></div>
                <div class="flex items-center">
                    <div id="step3Indicator" class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 text-gray-500 font-bold dark:bg-gray-700">3</div>
                    <span class="ml-2 text-gray-500" id="step3Label">Konfirmasi</span>
                </div>
            </div>

            {{-- Step 1: Member --}}
            <div id="step1" class="mb-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Step 1: Identifikasi Anggota</h3>
                <div class="flex gap-4">
                    <input type="text" id="memberNumberInput" placeholder="Scan atau ketik Nomor Anggota..."
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-3 text-lg focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <button type="button" onclick="lookupMember()" 
                        class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
                        🔍 Cari
                    </button>
                </div>
                <input type="hidden" name="member_number" id="memberNumber">

                {{-- Member Info Card (Hidden by default) --}}
                <div id="memberInfo" class="mt-4 hidden rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-2xl dark:bg-blue-900/30">
                            👤
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white" id="memberName">-</h4>
                            <p class="text-sm text-gray-500">
                                <span id="memberClass">-</span> | No: <span id="memberNum">-</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Status Keanggotaan</p>
                            <span id="memberStatus" class="inline-block rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-600">
                                ✅ Aktif
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-500">Buku Dipinjam</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white"><span id="currentBorrow">0</span> dari <span id="maxBorrow">3</span></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sisa Kuota</p>
                            <p class="text-lg font-semibold text-blue-600" id="remainingQuota">3 buku</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Denda</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white" id="unpaidFines">Rp 0</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Book (Hidden by default) --}}
            <div id="step2" class="mb-6 hidden">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">Step 2: Scan Buku</h3>
                <div class="flex gap-4">
                    <input type="text" id="bookIsbnInput" placeholder="Scan barcode ISBN buku..."
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-3 text-lg font-mono focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <button type="button" onclick="lookupBook()" 
                        class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
                        🔍 Cari
                    </button>
                </div>
                <input type="hidden" name="book_isbn" id="bookIsbn">

                {{-- Book Info Card (Hidden by default) --}}
                <div id="bookInfo" class="mt-4 hidden rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex items-start gap-4">
                        <div class="flex h-20 w-14 items-center justify-center rounded bg-green-100 text-2xl dark:bg-green-900/30">
                            📚
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-white" id="bookTitle">-</h4>
                            <p class="text-sm text-gray-500" id="bookAuthor">-</p>
                            <div class="mt-2 flex gap-2">
                                <span class="rounded bg-blue-100 px-2 py-0.5 text-xs text-blue-600" id="bookCategory">-</span>
                                <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600" id="bookRack">-</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Tersedia</p>
                            <p class="text-lg font-semibold text-green-600" id="bookAvailable">0/0</p>
                        </div>
                    </div>
                </div>

                {{-- Due Date --}}
                <div id="dueDateSection" class="mt-4 hidden">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Lama Peminjaman</label>
                    <select name="due_days" class="rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="1">1 Hari</option>
                        <option value="2">2 Hari</option>
                        <option value="3">3 Hari</option>
                        <option value="4">4 Hari</option>
                        <option value="5">5 Hari</option>
                        <option value="7">7 Hari</option>
                        <option value="14">14 Hari</option>
                        <option value="21">21 Hari</option>
                        <option value="30">30 Hari</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-500">
                        Tanggal wajib kembali: <span class="font-medium text-blue-600" id="dueDate">-</span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                <a href="{{ route('circulation.active') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                    Batal
                </a>
                <button type="button" id="nextBtn" onclick="nextStep()" class="hidden rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-blue-700">
                    Lanjutkan →
                </button>
                <button type="submit" id="submitBtn" class="hidden rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-green-700">
                    ✅ Proses Peminjaman
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    let currentStep = 1;
    let memberData = null;
    let bookData = null;

    async function lookupMember() {
        const memberNumber = document.getElementById('memberNumberInput').value.trim();
        if (!memberNumber) return;

        try {
            const response = await fetch(`/api/members/${memberNumber}`);
            const data = await response.json();

            if (data.error) {
                alert(data.error);
                return;
            }

            memberData = data;
            document.getElementById('memberNumber').value = data.member.member_number;
            document.getElementById('memberName').textContent = data.member.name;
            document.getElementById('memberClass').textContent = data.member.class || '-';
            document.getElementById('memberNum').textContent = data.member.member_number;
            document.getElementById('currentBorrow').textContent = data.member.max_borrow - data.remaining_quota;
            document.getElementById('maxBorrow').textContent = data.member.max_borrow;
            document.getElementById('remainingQuota').textContent = data.remaining_quota + ' buku';
            document.getElementById('unpaidFines').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.unpaid_fines);

            if (data.is_expired) {
                document.getElementById('memberStatus').innerHTML = '<span class="text-red-600">❌ Expired</span>';
                document.getElementById('memberStatus').className = 'inline-block rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-600';
            } else if (!data.can_borrow) {
                document.getElementById('memberStatus').innerHTML = '⚠️ Kuota Penuh';
                document.getElementById('memberStatus').className = 'inline-block rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-600';
            }

            document.getElementById('memberInfo').classList.remove('hidden');
            
            if (data.can_borrow) {
                document.getElementById('nextBtn').classList.remove('hidden');
            }
        } catch (error) {
            alert('Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    async function lookupBook() {
        const isbn = document.getElementById('bookIsbnInput').value.trim();
        if (!isbn) return;

        try {
            const response = await fetch(`/api/books/isbn/${isbn}`);
            const data = await response.json();

            if (data.error) {
                alert(data.error);
                return;
            }

            bookData = data;
            document.getElementById('bookIsbn').value = data.isbn;
            document.getElementById('bookTitle').textContent = data.title;
            document.getElementById('bookAuthor').textContent = data.author;
            document.getElementById('bookCategory').textContent = data.category?.name || '-';
            document.getElementById('bookRack').textContent = '📍 ' + (data.rack?.code || '-');
            document.getElementById('bookAvailable').textContent = data.available + '/' + data.stock;

            document.getElementById('bookInfo').classList.remove('hidden');
            
            if (data.available > 0) {
                document.getElementById('dueDateSection').classList.remove('hidden');
                document.getElementById('submitBtn').classList.remove('hidden');
                updateDueDate();
            } else {
                alert('Buku tidak tersedia!');
            }
        } catch (error) {
            alert('Buku tidak ditemukan.');
        }
    }

    function nextStep() {
        if (currentStep === 1) {
            currentStep = 2;
            document.getElementById('step2').classList.remove('hidden');
            document.getElementById('step2Indicator').className = 'flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-white font-bold';
            document.getElementById('step2Label').className = 'ml-2 font-medium text-blue-600';
            document.getElementById('line1').className = 'mx-4 h-1 w-16 bg-blue-600';
            document.getElementById('nextBtn').classList.add('hidden');
            document.getElementById('bookIsbnInput').focus();
        }
    }

    function updateDueDate() {
        const days = document.querySelector('select[name="due_days"]').value;
        const dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + parseInt(days));
        document.getElementById('dueDate').textContent = dueDate.toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    document.querySelector('select[name="due_days"]')?.addEventListener('change', updateDueDate);

    // Handle Enter key
    document.getElementById('memberNumberInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            lookupMember();
        }
    });

    document.getElementById('bookIsbnInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            lookupBook();
        }
    });
</script>
@endpush
