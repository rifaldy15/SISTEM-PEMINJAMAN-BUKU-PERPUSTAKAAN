@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pengembalian Buku</h2>
            <p class="text-gray-500 dark:text-gray-400">Proses pengembalian buku dan perhitungan denda</p>
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

    {{-- Return Form --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <form action="{{ route('circulation.processReturn') }}" method="POST" id="returnForm">
            @csrf

            {{-- Book Scan --}}
            <div class="mb-6">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Scan Barcode Buku</label>
                <div class="flex gap-4">
                    <input type="text" id="bookIsbnInput" placeholder="Scan barcode ISBN buku yang dikembalikan..."
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-3 text-lg font-mono focus:border-blue-500 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" autofocus>
                    <button type="button" onclick="lookupTransaction()" 
                        class="rounded-lg bg-blue-600 px-6 py-3 font-medium text-white hover:bg-blue-700">
                        🔍 Cari
                    </button>
                </div>
                <input type="hidden" name="transaction_id" id="transactionId">
            </div>

            {{-- Multiple Transactions Selection --}}
            <div id="selectionSection" class="mb-6 hidden">
                <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">👥 Pilih Peminjam</h3>
                <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3">Nama Anggota</th>
                                <th class="px-4 py-3">Tgl Pinjam</th>
                                <th class="px-4 py-3">Jatuh Tempo</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="transactionList" class="divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- Dynamic rows --}}
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Transaction Details (Hidden by default) --}}
            <div id="transactionDetails" class="hidden">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white">📚 Detail Peminjaman</h3>
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">Judul Buku</p>
                            <p class="text-lg font-medium text-gray-800 dark:text-white" id="bookTitle">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Peminjam</p>
                            <p class="text-lg font-medium text-gray-800 dark:text-white" id="memberName">-</p>
                            <p class="text-sm text-gray-500" id="memberNumber">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Pinjam</p>
                            <p class="font-medium text-gray-800 dark:text-white" id="borrowDate">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Harus Kembali</p>
                            <p class="font-medium text-gray-800 dark:text-white" id="dueDate">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Hari Ini</p>
                            <p class="font-medium text-gray-800 dark:text-white">{{ now()->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>

                    {{-- Overdue Warning --}}
                    <div id="overdueSection" class="mt-6 hidden rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                        <div class="flex items-center gap-3">
                            <span class="text-3xl">⚠️</span>
                            <div>
                                <p class="text-lg font-bold text-red-600">KETERLAMBATAN: <span id="daysOverdue">0</span> Hari</p>
                            </div>
                        </div>
                        <div class="mt-4 rounded bg-white p-3 dark:bg-gray-800">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Denda per hari</span>
                                <span class="font-medium">Rp 500</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-2 mt-2 dark:border-gray-700">
                                <span class="font-semibold text-gray-800 dark:text-white">Total Denda</span>
                                <span class="font-bold text-red-600" id="fineAmount">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    {{-- Book Condition --}}
                    <div class="mt-6">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status Kondisi Buku</label>
                        <div class="flex flex-wrap gap-3">
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700">
                                <input type="radio" name="book_condition" value="good" checked class="text-blue-600">
                                <span>✅ Baik</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700">
                                <input type="radio" name="book_condition" value="minor_damage" class="text-yellow-600">
                                <span>⚠️ Rusak Ringan</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700">
                                <input type="radio" name="book_condition" value="major_damage" class="text-orange-600">
                                <span>🔴 Rusak Berat</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700">
                                <input type="radio" name="book_condition" value="lost" class="text-red-600">
                                <span>❌ Hilang</span>
                            </label>
                        </div>
                    </div>

                    {{-- Pay Fine Checkbox --}}
                    <div id="payFineSection" class="mt-4 hidden">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="pay_fine" value="1" class="rounded text-blue-600">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Bayar denda sekarang</span>
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('circulation.active') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                        Batal
                    </a>
                    <button type="submit" class="rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white hover:bg-green-700">
                        ✅ Proses Pengembalian
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    let currentOverdueFine = 0;

    async function lookupTransaction() {
        const isbn = document.getElementById('bookIsbnInput').value.trim();
        if (!isbn) return;

        try {
            const response = await fetch(`/api/circulation/transaction/${isbn}`);
            const data = await response.json();

            if (data.error) {
                alert(data.error);
                return;
            }

            if (data.count > 1) {
                showSelection(data.transactions);
            } else {
                selectTransaction(data.transactions[0]);
            }
        } catch (error) {
            console.error(error);
            alert('Transaksi tidak ditemukan.');
        }
    }

    function showSelection(transactions) {
        const list = document.getElementById('transactionList');
        list.innerHTML = '';
        
        transactions.forEach(t => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 dark:hover:bg-gray-800';
            
            const isOverdue = t.is_overdue;
            const statusLabel = isOverdue 
                ? `<span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Terlambat ${t.days_overdue} hari</span>`
                : `<span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Aktif</span>`;

            row.innerHTML = `
                <td class="px-4 py-3 text-gray-800 dark:text-gray-200 font-medium">${t.member.name}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">${moment(t.borrowed_at).format('DD MMM YYYY')}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">${moment(t.due_date).format('DD MMM YYYY')}</td>
                <td class="px-4 py-3">${statusLabel}</td>
                <td class="px-4 py-3 text-right">
                    <button type="button" onclick='selectTransaction(${JSON.stringify(t)})' 
                        class="text-blue-600 hover:text-blue-800 font-medium">Pilih</button>
                </td>
            `;
            list.appendChild(row);
        });

        document.getElementById('selectionSection').classList.remove('hidden');
        document.getElementById('transactionDetails').classList.add('hidden');
    }

    function selectTransaction(transaction) {
        document.getElementById('transactionId').value = transaction.id;
        document.getElementById('bookTitle').textContent = transaction.book.title;
        document.getElementById('memberName').textContent = transaction.member.name;
        document.getElementById('memberNumber').textContent = 'No: ' + transaction.member.member_number;
        document.getElementById('borrowDate').textContent = moment(transaction.borrowed_at).format('DD MMMM YYYY');
        document.getElementById('dueDate').textContent = moment(transaction.due_date).format('DD MMMM YYYY');

        // Calculate overdue fine
        currentOverdueFine = 0;
        if (transaction.is_overdue) {
            currentOverdueFine = transaction.days_overdue * 2000;
            document.getElementById('daysOverdue').textContent = transaction.days_overdue;
            document.getElementById('overdueSection').classList.remove('hidden');
        } else {
            document.getElementById('daysOverdue').textContent = 0;
            document.getElementById('overdueSection').classList.add('hidden');
        }
        
        updateTotalFine();
        document.getElementById('selectionSection').classList.add('hidden');
        document.getElementById('transactionDetails').classList.remove('hidden');
    }

    function updateTotalFine() {
        const condition = document.querySelector('input[name="book_condition"]:checked').value;
        let conditionFine = 0;

        switch (condition) {
            case 'minor_damage': conditionFine = 20000; break;
            case 'major_damage': conditionFine = 50000; break;
            case 'lost': conditionFine = 100000; break;
            default: conditionFine = 0;
        }

        const totalFine = currentOverdueFine + conditionFine;
        
        document.getElementById('fineAmount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalFine);
        
        if (totalFine > 0) {
            document.getElementById('overdueSection').classList.remove('hidden');
            document.getElementById('payFineSection').classList.remove('hidden');
        } else {
            document.getElementById('overdueSection').classList.add('hidden');
            document.getElementById('payFineSection').classList.add('hidden');
        }
    }

    // Add event listeners to radio buttons
    document.querySelectorAll('input[name="book_condition"]').forEach(radio => {
        radio.addEventListener('change', updateTotalFine);
    });

    document.getElementById('bookIsbnInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            lookupTransaction();
        }
    });
</script>
@endpush
