<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Rack;
use App\Models\Book;
use App\Models\Member;
use App\Models\Transaction;
use App\Models\Visitor;
use Carbon\Carbon;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Categories
        $categories = [
            ['code' => 'FIK', 'name' => 'Fiksi', 'description' => 'Novel, cerpen, dan karya fiksi lainnya'],
            ['code' => 'NON', 'name' => 'Non-Fiksi', 'description' => 'Buku referensi, ensiklopedia, dll'],
            ['code' => 'SCI', 'name' => 'Sains', 'description' => 'Fisika, Kimia, Biologi, dll'],
            ['code' => 'SOC', 'name' => 'Sosial', 'description' => 'Sejarah, Geografi, Ekonomi, dll'],
            ['code' => 'REL', 'name' => 'Agama', 'description' => 'Buku keagamaan'],
            ['code' => 'TEC', 'name' => 'Teknologi', 'description' => 'Komputer, Teknik, dll'],
            ['code' => 'ART', 'name' => 'Seni', 'description' => 'Musik, Seni Rupa, dll'],
            ['code' => 'LAN', 'name' => 'Bahasa', 'description' => 'Kamus, Linguistik, dll'],
            ['code' => 'MED', 'name' => 'Kesehatan', 'description' => 'Kedokteran, Kesehatan Masyarakat, dll'],
            ['code' => 'PHI', 'name' => 'Filsafat', 'description' => 'Filsafat Barat dan Timur'],
            ['code' => 'KID', 'name' => 'Anak-anak', 'description' => 'Cerita anak dan edukasi dini'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Seed Racks
        $locations = ['Lantai 1 - Sayap Kiri', 'Lantai 1 - Sayap Kanan', 'Lantai 2 - Ruang Referensi', 'Lantai 2 - Koleksi Umum'];
        $rackCount = 20;
        for ($i = 1; $i <= $rackCount; $i++) {
            Rack::create([
                'code' => chr(64 + ceil($i / 5)) . '-' . str_pad($i % 5 ?: 5, 2, '0', STR_PAD_LEFT),
                'name' => 'Rak ' . chr(64 + ceil($i / 5)) . ' Baris ' . ($i % 5 ?: 5),
                'location' => $locations[array_rand($locations)],
                'capacity' => rand(50, 200)
            ]);
        }

        // Seed Members
        $memberCount = 150;
        $realNames = [
            'Ahmad Fauzan', 'Budi Santoso', 'Siti Aminah', 'Dewi Lestari', 'Rizky Pratama', 
            'Anisa Putri', 'Fajar Ramadhan', 'Lestari Wahyuni', 'Indra Wijaya', 'Siska Amelia',
            'Dodi Hermawan', 'Maya Sari', 'Eko Prasetyo', 'Rina Melati', 'Agus Setiawan',
            'Yanti Rahayu', 'Hadi Kusuma', 'Nina Kurnia', 'Surya Saputra', 'Fitri Handayani',
            'Taufik Hidayat', 'Wulan Dari', 'Rian Ardianto', 'Intan Permata', 'Denny Cahyono',
            'Gita Savitri', 'Bambang Pamungkas', 'Yuliana Putri', 'Zaki Anwar', 'Ratna Galih'
        ];
        $classes = ['X IPA 1', 'X IPA 2', 'X IPS 1', 'X IPS 2', 'XI IPA 1', 'XI IPA 2', 'XI IPS 1', 'XI IPS 2', 'XII IPA 1', 'XII IPA 2', 'XII IPS 1', 'XII IPS 2', 'Guru', 'Staf'];
        for ($i = 1; $i <= $memberCount; $i++) {
            Member::create([
                'member_number' => '2024' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'name' => $realNames[array_rand($realNames)] . ' ' . (rand(0, 1) ? chr(rand(65, 90)) . '.' : ''),
                'email' => 'member' . $i . '@example.com',
                'phone' => '08' . rand(11111111, 99999999),
                'class' => $classes[array_rand($classes)],
                'joined_at' => Carbon::now()->subMonths(rand(1, 24)),
                'expired_at' => Carbon::now()->addYears(1),
                'status' => rand(0, 10) > 1 ? 'active' : 'inactive'
            ]);
        }

        // Seed Books (Hundreds of books)
        $bookTitles = [
            'Laskar Pelangi', 'Bumi Manusia', 'Dilan 1990', 'Negeri 5 Menara', 'Perahu Kertas',
            'Fisika Dasar', 'Kimia Organik', 'Pemrograman Python', 'Laravel for Beginners',
            'Sejarah Indonesia Modern', 'Filosofi Teras', 'Atomic Habits', 'The Psychology of Money',
            'Bicara Itu Ada Seninya', 'Sapiens', 'Home Organization', 'Masakan Nusantara',
            'Panduan Investasi', 'Sosiologi Perkembangan', 'Psikologi Pendidikan', 'Manajemen Bisnis',
            'Desain Grafis Professional', 'Fotografi Digital', 'Arsitektur Modern', 'Astronomi Populer',
            'Biologi Sel', 'Matematika Diskrit', 'Algoritma dan Struktur Data', 'Jaringan Komputer',
            'Keamanan Cyber', 'Kecerdasan Buatan', 'Internet of Things'
        ];
        
        $authors = ['Andrea Hirata', 'Pramoedya Ananta Toer', 'Pidi Baiq', 'Ahmad Fuadi', 'Dee Lestari', 'Henry Manampiring', 'James Clear', 'Morgan Housel', 'Yuval Noah Harari', 'Andi Prasetyo', 'Muhammad Zaki'];

        for ($i = 1; $i <= 300; $i++) {
            $stock = rand(2, 10);
            Book::create([
                'category_id' => rand(1, count($categories)),
                'rack_id' => rand(1, $rackCount),
                'title' => $bookTitles[array_rand($bookTitles)] . ' Vol. ' . rand(1, 5),
                'author' => $authors[array_rand($authors)],
                'isbn' => '978-' . rand(100, 999) . '-' . rand(100, 999) . '-' . rand(10, 99) . '-' . rand(0, 9),
                'publisher' => 'Penerbit ' . chr(rand(65, 90)) . chr(rand(65, 90)),
                'year' => rand(2010, 2024),
                'stock' => $stock,
                'available' => $stock - rand(0, 2),
                'description' => 'Deskripsi lengkap untuk buku contoh ke-' . $i . '. Buku ini membahas topik menarik secara mendalam.'
            ]);
        }

        // Seed Transactions (Balanced distribution)
        for ($i = 1; $i <= 400; $i++) {
            $rand = rand(1, 100);
            
            if ($rand <= 60) {
                // 60% Returned (Historical)
                $borrowedAt = Carbon::today()->subMonths(rand(1, 8))->subDays(rand(0, 30));
                $dueDate = $borrowedAt->copy()->addDays(7);
                $returnedAt = $borrowedAt->copy()->addDays(rand(1, 15)); // Some returned late, some early
                $status = 'returned';
            } elseif ($rand <= 80) {
                // 20% Active (On Time)
                $borrowedAt = Carbon::today()->subDays(rand(0, 10)); // Borrowed recently
                $dueDate = $borrowedAt->copy()->addDays(rand(7, 14)); // Varied due dates
                $returnedAt = null;
                $status = 'borrowed';
            } else {
                // 20% Overdue
                $borrowedAt = Carbon::today()->subDays(rand(15, 60)); // Borrowed older than 7 days
                $dueDate = $borrowedAt->copy()->addDays(7);
                $returnedAt = null;
                $status = 'overdue';
            }

            Transaction::create([
                'member_id' => rand(1, $memberCount),
                'book_id' => rand(1, 300),
                'borrowed_at' => $borrowedAt,
                'due_date' => $dueDate,
                'returned_at' => $returnedAt,
                'status' => $status
            ]);
        }

        // Seed Visitors (Bulk logs for charts)
        for ($i = 0; $i < 60; $i++) { // Past 60 days
            $date = Carbon::today()->subDays($i);
            $dailyCount = rand(20, 100);
            
            for ($j = 0; $j < $dailyCount; $j++) {
                $checkIn = $date->copy()->setTime(rand(8, 16), rand(0, 59));
                Visitor::create([
                    'member_id' => rand(1, $memberCount),
                    'check_in' => $checkIn,
                    'check_out' => rand(0, 3) > 0 ? $checkIn->copy()->addMinutes(rand(30, 240)) : null
                ]);
            }
        }

        // Seed Fines (Based on transactions)
        $overdueTransactions = Transaction::where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'returned')->whereColumn('returned_at', '>', 'due_date');
            })
            ->inRandomOrder()
            ->limit(30)
            ->get();

        foreach ($overdueTransactions as $index => $transaction) {
            $daysOverdue = $transaction->returned_at 
                ? $transaction->due_date->diffInDays($transaction->returned_at)
                : $transaction->due_date->diffInDays(Carbon::today());
            
            if ($daysOverdue <= 0) $daysOverdue = rand(1, 14); // Fallback for seeding logic

            $isPaid = $index < 12; // First 12 are paid, rest are unpaid
            
            \App\Models\Fine::create([
                'transaction_id' => $transaction->id,
                'days_overdue' => $daysOverdue,
                'amount_per_day' => 500,
                'total_amount' => $daysOverdue * 500,
                'is_paid' => $isPaid,
                'paid_at' => $isPaid ? Carbon::today()->subDays(rand(0, 7)) : null,
                'notes' => $isPaid ? 'Pembayaran lunas' : 'Belum dibayar'
            ]);
        }
    }
}
