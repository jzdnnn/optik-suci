<?php

namespace Database\Seeders;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Frame;
use App\Models\FrameCategory;
use App\Models\Lens;
use App\Models\LensOwnershipCategory;
use App\Models\LensType;
use App\Models\Patient;
use App\Models\RiwayatBarangMasuk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Patients
        $patientCategories = ['BPJS Kelas 1', 'BPJS Kelas 2', 'BPJS Kelas 3', 'Umum'];
        $genders = ['Laki-laki', 'Perempuan'];
        $firstNames = ['Ahmad', 'Budi', 'Chandra', 'Dedi', 'Eko', 'Fajar', 'Giri', 'Hendra', 'Indra', 'Joko', 'Anna', 'Bella', 'Citra', 'Dewi', 'Elsa', 'Fitri', 'Gita', 'Hesti', 'Indah', 'Juli'];
        $lastNames = ['Pratama', 'Santoso', 'Wijaya', 'Hidayat', 'Kusuma', 'Sari', 'Lestari', 'Wulandari', 'Utami', 'Putri'];

        $patients = [];
        for ($i = 0; $i < 50; $i++) {
            $jk = $genders[array_rand($genders)];
            $fn = $firstNames[array_rand($firstNames)];
            $ln = $lastNames[array_rand($lastNames)];
            $patients[] = Patient::create([
                'nama' => $fn . ' ' . $ln,
                'jenis_kelamin' => $jk,
                'kategori' => $patientCategories[array_rand($patientCategories)],
                'alamat' => 'Alamat Dummy No. ' . ($i + 1),
                'no_hp' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
            ]);
        }

        // 2. Ensure Frame Categories
        $frameCategories = FrameCategory::all();
        if ($frameCategories->isEmpty()) {
            $fcNames = ['Iware', 'Sunglasses', 'Kacamata Baca'];
            foreach ($fcNames as $name) {
                $frameCategories[] = FrameCategory::create(['name' => $name]);
            }
        }

        // 3. Seed Frames
        $frameNames = ['BRENDA', 'LUGANO', 'YUPS', 'NO MERK', 'BERNIE', 'ZABDI', 'MEMPHIS', 'BRIDGE', 'DIAMOND', 'ZABDI M', 'CHAKASU', 'SEIMA', 'BAILEY', 'OSWALD', 'MARC JACOBS', 'CKS'];
        $frames = [];
        foreach ($frameNames as $name) {
            $frame = Frame::create([
                'frame_category_id' => $frameCategories[array_rand($frameCategories->toArray())]['id'],
                'name' => $name,
                'harga_beli' => rand(50, 150) * 1000,
                'harga_jual' => rand(200, 500) * 1000,
            ]);
            $frames[] = $frame;

            // Seed stock
            $bm = BarangMasuk::create([
                'barang_masukable_type' => Frame::class,
                'barang_masukable_id' => $frame->id,
                'stok' => 500,
                'tanggal_masuk' => now(),
            ]);

            RiwayatBarangMasuk::create([
                'barang_masuk_id' => $bm->id,
                'jenis_pergerakan' => 'masuk',
                'jumlah' => 500,
                'keterangan' => 'Stok Awal Dummy',
                'tanggal' => now(),
            ]);
        }

        // 4. Ensure Lens Ownership & Types
        $lensTypes = LensType::all();
        if ($lensTypes->isEmpty()) {
            $ltNames = ['Single Vision', 'Kryptok', 'Flat top', 'Progressive', 'Poly Carbonate'];
            foreach ($ltNames as $name) {
                $lensTypes[] = LensType::create(['name' => $name]);
            }
        }

        $lensOwnerships = LensOwnershipCategory::all();

        // 5. Seed Lenses
        $lensNames = ['SV CR MC', 'SV blueguard', 'sv photocromic', 'SV kaca', 'kryptok', 'progressive', 'KT CR MC', 'DOMAS MC'];
        $lenses = [];
        foreach ($lensNames as $i => $name) {
            $lens = Lens::create([
                'lens_type_id' => $lensTypes[array_rand($lensTypes->toArray())]['id'],
                'name' => $name,
                'bahan_lensa' => rand(0, 1) ? 'Plastic' : 'Glass',
                'lens_ownership_category_id' => $lensOwnerships[array_rand($lensOwnerships->toArray())]['id'],
                'harga_beli' => rand(30, 80) * 1000,
                'harga_jual' => rand(100, 250) * 1000,
            ]);
            $lenses[] = $lens;

            // Seed stock
            $bm = BarangMasuk::create([
                'barang_masukable_type' => Lens::class,
                'barang_masukable_id' => $lens->id,
                'stok' => 1000,
                'tanggal_masuk' => now(),
            ]);

            RiwayatBarangMasuk::create([
                'barang_masuk_id' => $bm->id,
                'jenis_pergerakan' => 'masuk',
                'jumlah' => 1000,
                'keterangan' => 'Stok Awal Dummy Lensa',
                'tanggal' => now(),
            ]);
        }

        // 6. Seed 100 BarangKeluar Transactions
        $types = ['frame', 'lensa', 'lengkap'];
        $statuses = ['lunas', 'dp', 'belum_bayar'];

        // We will seed spanning Jan 2026 to Jun 2026
        for ($i = 1; $i <= 100; $i++) {
            $type = $types[array_rand($types)];
            $patient = $patients[array_rand($patients)];
            $status = $statuses[array_rand($statuses)];

            $frameId = null;
            $hargaFrame = null;
            if (in_array($type, ['frame', 'lengkap'])) {
                $frame = $frames[array_rand($frames)];
                $frameId = $frame->id;
                $hargaFrame = $frame->harga_jual;
            }

            $lensId = null;
            $hargaLensa = null;
            $jumlahLensaPcs = null;
            if (in_array($type, ['lensa', 'lengkap'])) {
                $lens = $lenses[array_rand($lenses)];
                $lensId = $lens->id;
                $jumlahLensaPcs = rand(1, 4); // 0.5 to 2 pairs
                $hargaLensa = $lens->harga_jual * $jumlahLensaPcs;
            }

            $total = ($hargaFrame ?? 0) + ($hargaLensa ?? 0);
            $dp = 0;
            if ($status === 'dp') {
                $dp = round(($total * rand(30, 70) / 100) / 5000) * 5000;
            } elseif ($status === 'lunas') {
                $dp = $total;
            }

            // Distribute date randomly over the last 6 months of 2026
            $month = rand(1, 6);
            $day = rand(1, 28);
            $date = Carbon::create(2026, $month, $day, rand(8, 17), rand(0, 59));

            // Create BarangKeluar record
            BarangKeluar::create([
                'patient_id' => $patient->id,
                'tipe_transaksi' => $type,
                'frame_id' => $frameId,
                'lens_id' => $lensId,
                'harga_frame' => $hargaFrame,
                'harga_lensa' => $hargaLensa,
                'jumlah_lensa_pcs' => $jumlahLensaPcs,
                'total_transaksi' => $total,
                'status_pembayaran' => $status,
                'dp_dibayar' => $dp,
                'index_bias' => rand(0, 1) ? '1.56' : '1.61',
                'aksesoris' => rand(0, 1) ? ['Gosok'] : null,
                'tanggal_transaksi' => $date,
                'no_bon' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'tanggal_pengambilan' => (clone $date)->addDays(rand(1, 3)),
            ]);
        }

        // 7. Seed Pengeluaran (Expenses)
        $jenisPengeluarans = \App\Models\JenisPengeluaran::all();
        $expenseNotes = ['Beli ATK', 'Bahan Bakar Kurir', 'Konsumsi Rapat', 'Perbaikan Lampu Toko', 'Biaya Keamanan', 'Biaya Kebersihan', 'Pembelian Air Galon', 'Servis AC Toko'];

        for ($i = 0; $i < 50; $i++) {
            $jp = $jenisPengeluarans->random();
            $month = rand(1, 6);
            $day = rand(1, 28);
            $date = Carbon::create(2026, $month, $day);

            \App\Models\Pengeluaran::create([
                'jenis_pengeluaran_id' => $jp->id,
                'cabang' => 'OPTIK SUCI',
                'tanggal' => $date,
                'nominal' => rand(1, 20) * 25000, // 25k to 500k
                'keterangan' => $jp->nama . ' - ' . $expenseNotes[array_rand($expenseNotes)],
            ]);
        }

        // 8. Seed Setoran Mingguan (Weekly Deposits)
        for ($month = 1; $month <= 6; $month++) {
            for ($week = 1; $week <= 4; $week++) {
                // Tentukan tanggal berdasarkan minggu
                $day = ($week - 1) * 7 + rand(1, 5);
                $date = Carbon::create(2026, $month, $day);

                \App\Models\SetoranMingguan::create([
                    'cabang' => 'OPTIK SUCI',
                    'tanggal' => $date,
                    'minggu_ke' => $week,
                    'nominal' => rand(15, 45) * 100000, // 1.5M to 4.5M
                    'keterangan' => 'Setoran Mingguan ' . $week . ' Bulan ' . $date->translatedFormat('F Y'),
                ]);
            }
        }
    }
}
