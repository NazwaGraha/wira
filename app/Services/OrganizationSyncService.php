<?php

namespace App\Services;

use App\Models\Member;
use App\Models\OrganizationMember;
use Illuminate\Support\Facades\Schema;

class OrganizationSyncService
{
    /**
     * Definisi master 5 bidang utama PMR Wira
     */
    public const BIDANG_DEFINITIONS = [
        'markas' => [
            'name' => 'Bidang Markas',
            'keywords' => ['markas'],
            'order' => 1,
            'icon' => 'fa-solid fa-boxes-stacked',
            'default_work_program' => "1. Pengelolaan dan inventarisasi obat-obatan serta tandu darurat UKS\n2. Pemeliharaan kebersihan dan kesiapan ruang markas PMR\n3. Pengadaan logistik medis darurat\n4. Jadwal piket harian siaga upacara",
        ],
        'pelayanan' => [
            'name' => 'Bidang Pelayanan',
            'keywords' => ['pelayanan', 'kesehatan', 'pertolongan'],
            'order' => 2,
            'icon' => 'fa-solid fa-hand-holding-heart',
            'default_work_program' => "1. Tim medis siaga upacara bendera dan event olahraga sekolah\n2. Pemeriksaan kesehatan berkala dan tensi darah siswa/guru\n3. Layanan donor darah sukarela bekerjasama dengan PMI Kabupaten Bogor\n4. Edukasi PHBS (Perilaku Hidup Bersih dan Sehat)",
        ],
        'diklat' => [
            'name' => 'Bidang Diklat',
            'keywords' => ['diklat', 'pelatihan', 'pendidikan'],
            'order' => 3,
            'icon' => 'fa-solid fa-graduation-cap',
            'default_work_program' => "1. Latihan rutin mingguan materi 7 Prinsip Palang Merah & PP (Pertolongan Pertama)\n2. Pelatihan pasang tandu darurat dan evakuasi korban bencana\n3. Simulasi tanggap darurat bencana gempa bumi & kebakaran sekolah\n4. Ujian pelantikan dan kenaikan tingkat anggota PMR Wira",
        ],
        'humas' => [
            'name' => 'Bidang Humas',
            'keywords' => ['humas', 'hubungan masyarakat', 'publikasi', 'media'],
            'order' => 4,
            'icon' => 'fa-solid fa-bullhorn',
            'default_work_program' => "1. Pengelolaan publikasi media sosial dan dokumentasi kegiatan resmi PMR\n2. Komunikasi eksternal dengan PMI Cabang, pembina sekolah, dan alumni\n3. Sosialisasi kepalangmerahan saat Masa Pengenalan Lingkungan Sekolah (MPLS)\n4. Penerbitan buletin berkala dan artikel kemanusiaan",
        ],
        'kreasi' => [
            'name' => 'Bidang Kreasi',
            'keywords' => ['kreasi', 'pemberdayaan', 'kewirausahaan', 'kreatif'],
            'order' => 5,
            'icon' => 'fa-solid fa-wand-magic-sparkles',
            'default_work_program' => "1. Pembuatan konten edukasi kesehatan visual, poster, dan infografis digital\n2. Penggalangan dana mandiri melalui merchandise & bazar kemanusiaan\n3. Inovasi bank sampah dan daur ulang untuk kelestarian lingkungan sekolah\n4. Pementasan drama simulasi pertolongan pertama pada event sekolah",
        ],
    ];

    /**
     * Sinkronkan seluruh bagan kepengurusan secara otomatis dari data anggota
     */
    public static function syncAll(): array
    {
        if (! Schema::hasTable('members') || ! Schema::hasTable('organization_members')) {
            return ['status' => false, 'message' => 'Tabel belum tersedia'];
        }

        // Ambil seluruh anggota berstatus Aktif
        $query = Member::query();
        if (Schema::hasColumn('members', 'member_status')) {
            $query->where('member_status', 'Aktif');
        } elseif (Schema::hasColumn('members', 'is_active')) {
            $query->where('is_active', true);
        }
        $activeMembers = $query->get();

        $syncedCount = 0;
        $staffCount = 0;

        // 1. Sinkronisasi Ketua Umum (Level 2)
        $ketuaMember = $activeMembers->first(function ($m) {
            $pos = strtolower($m->position ?? '');

            return (str_contains($pos, 'ketua umum') || str_contains($pos, 'ketua pmr') || $pos === 'ketua')
                && ! str_contains($pos, 'wakil')
                && ! str_contains($pos, 'bidang');
        });

        if ($ketuaMember) {
            $orgKetua = OrganizationMember::firstOrNew(['level' => 2]);
            $orgKetua->member_id = $ketuaMember->id;
            $orgKetua->name = $ketuaMember->name;
            $orgKetua->position = 'Ketua Umum 2026/2027';
            $orgKetua->subtitle = 'Masa Bakti Ragana Dwi Pantara';
            $orgKetua->order_position = 1;
            $orgKetua->icon = 'fa-solid fa-crown';
            $orgKetua->is_active = true;
            if (! empty($ketuaMember->photo)) {
                $orgKetua->photo = $ketuaMember->photo;
            }
            $orgKetua->save();
            $syncedCount++;
        }

        // 2. Sinkronisasi Wakil Ketua (Level 3)
        $wakilMember = $activeMembers->first(function ($m) {
            $pos = strtolower($m->position ?? '');

            return str_contains($pos, 'wakil') || str_contains($pos, 'waka');
        });

        if ($wakilMember) {
            $orgWakil = OrganizationMember::firstOrNew(['level' => 3]);
            $orgWakil->member_id = $wakilMember->id;
            $orgWakil->name = $wakilMember->name;
            $orgWakil->position = 'Wakil Ketua';
            $orgWakil->subtitle = 'Wakil Ketua PMR Wira';
            $orgWakil->order_position = 1;
            $orgWakil->icon = 'fa-solid fa-user-shield';
            $orgWakil->is_active = true;
            if (! empty($wakilMember->photo)) {
                $orgWakil->photo = $wakilMember->photo;
            }
            $orgWakil->save();
            $syncedCount++;
        }

        // 3. Sinkronisasi Sekretaris (Level 4, Order 1)
        $sekretarisMember = $activeMembers->first(function ($m) {
            $pos = strtolower($m->position ?? '');

            return str_contains($pos, 'sekretaris');
        });

        if ($sekretarisMember) {
            $orgSekretaris = OrganizationMember::where('level', 4)->where('position', 'like', '%Sekretaris%')->first()
                ?: OrganizationMember::firstOrNew(['level' => 4, 'order_position' => 1]);
            $orgSekretaris->level = 4;
            $orgSekretaris->order_position = 1;
            $orgSekretaris->member_id = $sekretarisMember->id;
            $orgSekretaris->name = $sekretarisMember->name;
            $orgSekretaris->position = 'Sekretaris';
            $orgSekretaris->subtitle = $orgSekretaris->subtitle ?: 'Administrasi & Kesekretariatan';
            $orgSekretaris->icon = 'fa-solid fa-file-signature';
            $orgSekretaris->is_active = true;
            if (! empty($sekretarisMember->photo)) {
                $orgSekretaris->photo = $sekretarisMember->photo;
            }
            $orgSekretaris->save();
            $syncedCount++;
        }

        // 4. Sinkronisasi Bendahara (Level 4, Order 2)
        $bendaharaMember = $activeMembers->first(function ($m) {
            $pos = strtolower($m->position ?? '');

            return str_contains($pos, 'bendahara');
        });

        if ($bendaharaMember) {
            $orgBendahara = OrganizationMember::where('level', 4)->where('position', 'like', '%Bendahara%')->first()
                ?: OrganizationMember::firstOrNew(['level' => 4, 'order_position' => 2]);
            $orgBendahara->level = 4;
            $orgBendahara->order_position = 2;
            $orgBendahara->member_id = $bendaharaMember->id;
            $orgBendahara->name = $bendaharaMember->name;
            $orgBendahara->position = 'Bendahara';
            $orgBendahara->subtitle = $orgBendahara->subtitle ?: 'Keuangan & Kas Organisasi';
            $orgBendahara->icon = 'fa-solid fa-wallet';
            $orgBendahara->is_active = true;
            if (! empty($bendaharaMember->photo)) {
                $orgBendahara->photo = $bendaharaMember->photo;
            }
            $orgBendahara->save();
            $syncedCount++;
        }

        // 5. Sinkronisasi 5 Bidang Utama (Level 5) & Staf Bidang
        foreach (self::BIDANG_DEFINITIONS as $key => $def) {
            // Cari Ketua Bidang
            $leaderMember = $activeMembers->first(function ($m) use ($def) {
                $pos = strtolower($m->position ?? '');
                if (str_contains($pos, 'staf')) {
                    return false;
                }
                foreach ($def['keywords'] as $kw) {
                    if (str_contains($pos, $kw)) {
                        return true;
                    }
                }

                return false;
            });

            // Cari Staf Bidang ini
            $stafMembers = $activeMembers->filter(function ($m) use ($def, $leaderMember) {
                if ($leaderMember && $m->id === $leaderMember->id) {
                    return false;
                }
                $pos = strtolower($m->position ?? '');
                if (str_contains($pos, 'staf')) {
                    foreach ($def['keywords'] as $kw) {
                        if (str_contains($pos, $kw)) {
                            return true;
                        }
                    }
                }

                return false;
            });

            // Format data staf JSON
            $stafArray = [];
            foreach ($stafMembers as $st) {
                $stafArray[] = [
                    'member_id' => $st->id,
                    'name' => $st->name,
                    'class_grade' => $st->class_grade ?? '',
                    'photo' => $st->photo_url,
                ];
                $staffCount++;
            }

            // Temukan atau buat kotak bidang di Level 5
            $orgBidang = OrganizationMember::where('level', 5)
                ->where(function ($q) use ($def) {
                    $q->where('position', 'like', '%'.$def['name'].'%')
                        ->orWhere('subtitle', 'like', '%'.$def['name'].'%');
                })->first() ?: OrganizationMember::firstOrNew(['level' => 5, 'order_position' => $def['order']]);

            $orgBidang->level = 5;
            $orgBidang->order_position = $def['order'];
            $orgBidang->position = $def['name'];
            $orgBidang->subtitle = 'Ketua '.$def['name'];
            $orgBidang->icon = $def['icon'];
            $orgBidang->is_active = true;

            if ($leaderMember) {
                $orgBidang->member_id = $leaderMember->id;
                $orgBidang->name = $leaderMember->name;
                if (! empty($leaderMember->photo)) {
                    $orgBidang->photo = $leaderMember->photo;
                }
                $syncedCount++;
            }

            // Pertahankan work_program jika sudah ada, atau berikan default resmi jika masih kosong
            if (empty(trim($orgBidang->work_program ?? ''))) {
                $orgBidang->work_program = $def['default_work_program'];
            }

            // Pasang staf yang terdeteksi
            if (! empty($stafArray)) {
                $orgBidang->staff_members = $stafArray;
            }

            $orgBidang->save();
        }

        return [
            'status' => true,
            'synced_leaders' => $syncedCount,
            'synced_staff' => $staffCount,
            'message' => "Berhasil menyinkronkan {$syncedCount} pejabat utama dan {$staffCount} staf bidang ke Bagan Kepengurusan!",
        ];
    }
}
