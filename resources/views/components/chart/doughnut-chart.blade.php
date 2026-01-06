<div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 h-full">
    <div class="mb-4">
        <h4 class="text-lg font-semibold text-gray-800 dark:text-white">
            📊 Kondisi Koleksi Buku
        </h4>
        <p class="text-xs text-gray-500">Statistik ketersediaan buku saat ini</p>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        {{-- Chart container --}}
        <div class="relative flex justify-center items-center w-full sm:w-1/2">
            <div id="bookConditionChart"></div>
        </div>

        {{-- Legend Indicator --}}
        <div class="flex flex-col gap-y-3 w-full sm:w-1/2 px-2">
            <div class="flex items-center justify-between">
                <div class="inline-flex items-center">
                    <span class="w-3 h-3 inline-block bg-blue-600 rounded-full me-2"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Tersedia</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $bookCondition['tersedia_pct'] }}%</span>
                    <p class="text-[10px] text-gray-500">{{ $bookCondition['available'] }} buku</p>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="inline-flex items-center">
                    <span class="w-3 h-3 inline-block bg-cyan-500 rounded-full me-2"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Dipinjam</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $bookCondition['dipinjam_pct'] }}%</span>
                    <p class="text-[10px] text-gray-500">{{ $bookCondition['borrowed'] }} buku</p>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <div class="inline-flex items-center">
                    <span class="w-3 h-3 inline-block bg-gray-300 rounded-full me-2 dark:bg-gray-700"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">Rusak/Hilang</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $bookCondition['rusak_pct'] }}%</span>
                    <p class="text-[10px] text-gray-500">0 buku</p>
                </div>
            </div>
        </div>
    </div>
</div>
