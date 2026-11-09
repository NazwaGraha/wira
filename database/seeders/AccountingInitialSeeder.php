<?php

namespace Database\Seeders;

use App\Models\AccountingAccount;
use App\Models\AccountingProject;
use Illuminate\Database\Seeder;

class AccountingInitialSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Chart of Accounts (Bagan Akun)
        $accounts = [
            // 1-xxxx: ASET / HARTA
            ['code' => '1-1010', 'name' => 'Kas Tunai BPH (Kas Besar)', 'type' => 'asset', 'category' => 'cash_and_bank', 'description' => 'Kas tunai di brankas Bendahara Umum'],
            ['code' => '1-1020', 'name' => 'Kas Kecil Markas UKS (Petty Cash)', 'type' => 'asset', 'category' => 'cash_and_bank', 'description' => 'Kas darurat harian markas & UKS'],
            ['code' => '1-1030', 'name' => 'Kas Tunai Panitia Lomba (SBB IV)', 'type' => 'asset', 'category' => 'cash_and_bank', 'description' => 'Kas operasional panitia pelaksana lomba'],
            ['code' => '1-1110', 'name' => 'Bank BJB Rekening Resmi PMR', 'type' => 'asset', 'category' => 'cash_and_bank', 'description' => 'Rekening utama giro/tabungan organisasi'],
            ['code' => '1-1120', 'name' => 'Bank Mandiri / Kas Digital (QRIS)', 'type' => 'asset', 'category' => 'cash_and_bank', 'description' => 'Penampungan pembayaran QRIS pendaftaran & parkir'],
            ['code' => '1-1200', 'name' => 'Piutang Pendaftaran Kontingen', 'type' => 'asset', 'category' => 'current_asset', 'description' => 'Biaya pendaftaran lomba yang belum lunas'],
            ['code' => '1-2010', 'name' => 'Peralatan Medis & Tandu (Aset Tetap)', 'type' => 'asset', 'category' => 'fixed_asset', 'description' => 'Inventaris fisik tandu, spalk, bidai, tensi'],

            // 2-xxxx: KEWAJIBAN / UTANG
            ['code' => '2-1010', 'name' => 'Utang Pembelian Obat & Medis', 'type' => 'liability', 'category' => 'current_liability', 'description' => 'Tagihan apotek atau vendor medis'],
            ['code' => '2-1020', 'name' => 'Titipan Dana Donasi Kemanusiaan', 'type' => 'liability', 'category' => 'current_liability', 'description' => 'Titipan dana aksi peduli bencana PMI'],

            // 3-xxxx: EKUITAS / MODAL
            ['code' => '3-1010', 'name' => 'Saldo Awal Kas Periode 2026', 'type' => 'equity', 'category' => 'equity', 'description' => 'Saldo peralihan masa bakti sebelumnya'],
            ['code' => '3-1020', 'name' => 'Surplus / Defisit Berjalan', 'type' => 'equity', 'category' => 'equity', 'description' => 'Akumulasi sisa lebih/kurang pembiayaan'],

            // 4-xxxx: PENDAPATAN / PEMASUKAN
            ['code' => '4-1010', 'name' => 'Pemasukan Iuran Kas Rutin Anggota', 'type' => 'revenue', 'category' => 'operational_revenue', 'description' => 'Iuran mingguan anggota aktif PMR'],
            ['code' => '4-1020', 'name' => 'Pemasukan Uang Pangkal Diklatdas', 'type' => 'revenue', 'category' => 'operational_revenue', 'description' => 'Biaya diklat & pelantikan calon anggota baru'],
            ['code' => '4-2010', 'name' => 'Pemasukan Registrasi Lomba SBB IV', 'type' => 'revenue', 'category' => 'project_revenue', 'description' => 'Pendaftaran tim Madya, Wira, dan Mula'],
            ['code' => '4-2020', 'name' => 'Pemasukan Karcis Parkir Lapangan SBB', 'type' => 'revenue', 'category' => 'project_revenue', 'description' => 'Karcis parkir motor, mobil, dan bus penonton'],
            ['code' => '4-2030', 'name' => 'Sponsorship & Donatur Proyek', 'type' => 'revenue', 'category' => 'grant_revenue', 'description' => 'Dana kemitraan sponsor atau donatur'],
            ['code' => '4-3010', 'name' => 'Bantuan Operasional Sekolah (BOS)', 'type' => 'revenue', 'category' => 'grant_revenue', 'description' => 'Alokasi pembinaan kesiswaan SMAN 1 Ciawi'],
            ['code' => '4-3020', 'name' => 'Usaha Dana (Danus) Mandiri', 'type' => 'revenue', 'category' => 'operational_revenue', 'description' => 'Penjualan atribut, merchandise, dan bazar'],

            // 5-xxxx: BEBAN / PENGELUARAN
            ['code' => '5-1010', 'name' => 'Beban Pengadaan Obat & Bedah UKS', 'type' => 'expense', 'category' => 'operational_expense', 'description' => 'Beli obat, kasa, perban, desinfektan UKS'],
            ['code' => '5-1020', 'name' => 'Beban Kesekretariatan & ATK', 'type' => 'expense', 'category' => 'admin_expense', 'description' => 'Kertas, tinta, map, laminating, materai'],
            ['code' => '5-1030', 'name' => 'Beban Latihan Rutin & Pemateri', 'type' => 'expense', 'category' => 'operational_expense', 'description' => 'Honor pelatih PMI & konsumsi latihan'],
            ['code' => '5-2010', 'name' => 'Beban Piala, Medali & Sertifikat SBB', 'type' => 'expense', 'category' => 'project_expense', 'description' => 'Pembuatan trofi bergilir & sertifikat lomba'],
            ['code' => '5-2020', 'name' => 'Beban Honorarium & Fasilitas Juri', 'type' => 'expense', 'category' => 'project_expense', 'description' => 'Akomodasi, transport, dan honor juri PMI'],
            ['code' => '5-2030', 'name' => 'Beban Tenda, Panggung & Sound System', 'type' => 'expense', 'category' => 'project_expense', 'description' => 'Sewa tenda pleton, sound, & perlengkapan'],
            ['code' => '5-2040', 'name' => 'Beban Konsumsi Panitia & Petugas', 'type' => 'expense', 'category' => 'project_expense', 'description' => 'Makan & snack hari-H perlombaan'],
            ['code' => '5-2050', 'name' => 'Beban Operasional Parkir & Lapangan', 'type' => 'expense', 'category' => 'project_expense', 'description' => 'Cetak karcis, tali pembatas, rompi petugas'],
        ];

        foreach ($accounts as $acc) {
            AccountingAccount::firstOrCreate(['code' => $acc['code']], $acc);
        }

        // 2. Proyek Awal (Lomba SBB IV & Operasional Kas BPH)
        AccountingProject::firstOrCreate(
            ['code' => 'PRJ-2026-SBB4'],
            [
                'name' => 'Lomba Akbar Sua Bhakti Berkarya (SBB) IV 2026',
                'category' => 'competition',
                'status' => 'active',
                'start_date' => '2026-02-01',
                'end_date' => '2026-04-30',
                'budget_target_revenue' => 50000000,
                'budget_limit_expense' => 35000000,
                'person_in_charge' => 'Sulthan Arkana Putra R.',
                'treasurer_name' => 'Bendahara Panitia SBB IV',
                'description' => 'Perhelatan akbar kompetisi kepalangmerahan tingkat Madya, Wira, dan Mula se-Jawa Barat, DKI Jakarta, dan Banten.',
            ]
        );

        AccountingProject::firstOrCreate(
            ['code' => 'PRJ-2026-OPS'],
            [
                'name' => 'Kas Rutin & Operasional Umum BPH 2026',
                'category' => 'routine_operational',
                'status' => 'active',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'budget_target_revenue' => 15000000,
                'budget_limit_expense' => 12000000,
                'person_in_charge' => 'Ketua Umum PMR Wira',
                'treasurer_name' => 'Bendahara Umum BPH',
                'description' => 'Pengelolaan kas operasional harian markas, iuran rutin anggota, dan logistik UKS.',
            ]
        );
    }
}
