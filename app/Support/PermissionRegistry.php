<?php

namespace App\Support;

class PermissionRegistry
{
    /**
     * Seluruh matriks permission backoffice PMR Wira SMAN 1 Ciawi
     * Dikelompokkan per modul secara detail & granular
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            'dashboard' => [
                'name' => 'Menu Utama & Dashboard',
                'icon' => 'fa-solid fa-gauge-high',
                'color' => 'text-slate-700 bg-slate-100',
                'items' => [
                    'dashboard.view' => [
                        'label' => 'Lihat Dashboard Utama',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.dashboard'],
                    ],
                    'dashboard.clear_cache' => [
                        'label' => 'Bersihkan Cache Sistem',
                        'type' => 'action',
                        'badge' => 'Aksi',
                        'routes' => ['admin.clear-cache'],
                    ],
                ],
            ],
            'articles' => [
                'name' => 'Artikel & Edukasi P3K',
                'icon' => 'fa-solid fa-newspaper',
                'color' => 'text-rose-600 bg-rose-50',
                'items' => [
                    'articles.view' => [
                        'label' => 'Lihat Daftar Artikel',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.articles.index', 'admin.artikel.show'],
                    ],
                    'articles.create' => [
                        'label' => 'Tulis Artikel Baru',
                        'type' => 'create',
                        'badge' => 'Tambah',
                        'routes' => ['admin.articles.create', 'admin.articles.store'],
                    ],
                    'articles.edit' => [
                        'label' => 'Edit / Ubah Artikel',
                        'type' => 'edit',
                        'badge' => 'Ubah',
                        'routes' => ['admin.articles.edit', 'admin.articles.update'],
                    ],
                    'articles.delete' => [
                        'label' => 'Hapus Artikel',
                        'type' => 'delete',
                        'badge' => 'Hapus',
                        'routes' => ['admin.articles.destroy'],
                    ],
                    'categories.view' => [
                        'label' => 'Lihat Kategori Artikel',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.categories.index'],
                    ],
                    'categories.manage' => [
                        'label' => 'Kelola Kategori Artikel',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.categories.store', 'admin.categories.update', 'admin.categories.destroy'],
                    ],
                ],
            ],
            'advisors' => [
                'name' => 'Data Pembina PMR',
                'icon' => 'fa-solid fa-user-shield',
                'color' => 'text-amber-600 bg-amber-50',
                'items' => [
                    'advisors.view' => [
                        'label' => 'Lihat Daftar Pembina',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.advisors.index', 'admin.advisors.show'],
                    ],
                    'advisors.create' => [
                        'label' => 'Input Data Pembina Baru',
                        'type' => 'create',
                        'badge' => 'Tambah',
                        'routes' => ['admin.advisors.create', 'admin.advisors.store'],
                    ],
                    'advisors.edit' => [
                        'label' => 'Edit Profil & Status Pembina',
                        'type' => 'edit',
                        'badge' => 'Ubah',
                        'routes' => ['admin.advisors.edit', 'admin.advisors.update'],
                    ],
                    'advisors.delete' => [
                        'label' => 'Hapus Data Pembina',
                        'type' => 'delete',
                        'badge' => 'Hapus',
                        'routes' => ['admin.advisors.destroy'],
                    ],
                ],
            ],
            'members' => [
                'name' => 'Data Anggota Siswa (Calon & Aktif)',
                'icon' => 'fa-solid fa-users',
                'color' => 'text-blue-600 bg-blue-50',
                'items' => [
                    'member_registrations.view' => [
                        'label' => 'Lihat Calon Anggota Baru',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.member-registrations.index', 'admin.member-registrations.show'],
                    ],
                    'member_registrations.verify' => [
                        'label' => 'Verifikasi / Terima Calon Anggota',
                        'type' => 'action',
                        'badge' => 'Aksi',
                        'routes' => ['admin.member-registrations.update', 'admin.member-registrations.approve'],
                    ],
                    'member_registrations.delete' => [
                        'label' => 'Hapus Pendaftaran Calon Anggota',
                        'type' => 'delete',
                        'badge' => 'Hapus',
                        'routes' => ['admin.member-registrations.destroy'],
                    ],
                    'members.view' => [
                        'label' => 'Lihat Daftar Anggota PMR',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.members.index', 'admin.members.show', 'admin.members.lookup'],
                    ],
                    'members.create' => [
                        'label' => 'Input Data Anggota Baru',
                        'type' => 'create',
                        'badge' => 'Tambah',
                        'routes' => ['admin.members.create', 'admin.members.store'],
                    ],
                    'members.edit' => [
                        'label' => 'Edit Biodata & Jabatan Anggota',
                        'type' => 'edit',
                        'badge' => 'Ubah',
                        'routes' => ['admin.members.edit', 'admin.members.update'],
                    ],
                    'members.delete' => [
                        'label' => 'Hapus Data Anggota',
                        'type' => 'delete',
                        'badge' => 'Hapus',
                        'routes' => ['admin.members.destroy'],
                    ],
                    'members.print_kta' => [
                        'label' => 'Cetak KTA Digital',
                        'type' => 'export',
                        'badge' => 'Cetak',
                        'routes' => ['admin.members.kta', 'admin.members.print-card'],
                    ],
                    'members.export' => [
                        'label' => 'Export Data Anggota ke Excel',
                        'type' => 'export',
                        'badge' => 'Excel',
                        'routes' => ['admin.members.export-excel', 'admin.members.export'],
                    ],
                ],
            ],
            'content_setup' => [
                'name' => 'Setup Konten Website Publik',
                'icon' => 'fa-solid fa-desktop',
                'color' => 'text-purple-600 bg-purple-50',
                'items' => [
                    'content_setup.home' => [
                        'label' => 'Setup Konten Beranda (Home)',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.content-setup.home*'],
                    ],
                    'content_setup.about' => [
                        'label' => 'Setup Konten Tentang Kami (Profil)',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.content-setup.about*'],
                    ],
                ],
            ],
            'media_content' => [
                'name' => 'Media & Konten Web Publik',
                'icon' => 'fa-solid fa-photo-film',
                'color' => 'text-pink-600 bg-pink-50',
                'items' => [
                    'media_manager.view' => [
                        'label' => 'Buka File & Media Manager',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.media-manager.index'],
                    ],
                    'media_manager.upload' => [
                        'label' => 'Upload Media & Hero Carousel',
                        'type' => 'create',
                        'badge' => 'Upload',
                        'routes' => ['admin.media-manager.store', 'admin.hero-slides.*'],
                    ],
                    'media_manager.delete' => [
                        'label' => 'Hapus File Media',
                        'type' => 'delete',
                        'badge' => 'Hapus',
                        'routes' => ['admin.media-manager.destroy'],
                    ],
                    'popup_banner.manage' => [
                        'label' => 'Kelola Popup Banner Iklan',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.popup-banner.*'],
                    ],
                    'background_music.manage' => [
                        'label' => 'Kelola Audio Mars PMI',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.background-music.*'],
                    ],
                    'uks_hotline.manage' => [
                        'label' => 'Kelola Hotline & Siaga UKS',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.uks-hotline.*'],
                    ],
                    'activities.view' => [
                        'label' => 'Lihat Agenda Kegiatan',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.activities.index', 'admin.activities.show'],
                    ],
                    'activities.manage' => [
                        'label' => 'Kelola Agenda Kegiatan',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.activities.create', 'admin.activities.store', 'admin.activities.edit', 'admin.activities.update', 'admin.activities.destroy'],
                    ],
                    'gallery.view' => [
                        'label' => 'Lihat Galeri Foto',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.gallery.index', 'admin.gallery.show'],
                    ],
                    'gallery.manage' => [
                        'label' => 'Kelola Galeri & Kategori Foto',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.gallery.create', 'admin.gallery.store', 'admin.gallery.edit', 'admin.gallery.update', 'admin.gallery.destroy', 'admin.gallery-categories.*'],
                    ],
                    'organization.view' => [
                        'label' => 'Lihat Bagan Kepengurusan',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.organization.index'],
                    ],
                    'organization.manage' => [
                        'label' => 'Kelola Bagan Kepengurusan',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.organization.*'],
                    ],
                    'prestasi.view' => [
                        'label' => 'Lihat Prestasi & Penghargaan',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.prestasi.index', 'admin.prestasi.show'],
                    ],
                    'prestasi.manage' => [
                        'label' => 'Kelola Prestasi & Unggulan',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.prestasi.create', 'admin.prestasi.store', 'admin.prestasi.edit', 'admin.prestasi.update', 'admin.prestasi.destroy', 'admin.prestasi.toggle-featured'],
                    ],
                    'organization_guidelines.view' => [
                        'label' => 'Lihat Pedoman Organisasi (GBHO & PPUO)',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.organization-guidelines.index'],
                    ],
                    'organization_guidelines.manage' => [
                        'label' => 'Kelola Bab Pedoman & File PDF',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.organization-guidelines.*'],
                    ],
                ],
            ],
            'competition' => [
                'name' => 'Sua Bhakti Berkarya (Manajemen Event Lomba)',
                'icon' => 'fa-solid fa-trophy',
                'color' => 'text-amber-500 bg-amber-50',
                'items' => [
                    'competition_event.view' => [
                        'label' => 'Lihat Info & Status Event Lomba',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.competition-event.index'],
                    ],
                    'competition_event.manage' => [
                        'label' => 'Pengaturan Event Lomba',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.competition-event.*'],
                    ],
                    'competition_bank_accounts.manage' => [
                        'label' => 'Kelola Rekening Transfer Lomba',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.competition-bank-accounts.*'],
                    ],
                    'competition_fees.manage' => [
                        'label' => 'Jadwal & Biaya Gelombang',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.competition-fees.*'],
                    ],
                    'competition_quotas.manage' => [
                        'label' => 'Pengaturan Kuota Regu & Cabang',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.competition-quotas.*'],
                    ],
                    'competition_registrations.view' => [
                        'label' => 'Lihat Pendaftar Lomba',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.competition-registrations.index', 'admin.competition-registrations.show'],
                    ],
                    'competition_registrations.verify' => [
                        'label' => 'Verifikasi Pendaftar & Kwitansi',
                        'type' => 'action',
                        'badge' => 'Verifikasi',
                        'routes' => ['admin.competition-registrations.verify', 'admin.competition-registrations.status', 'admin.competition-registrations.receipt'],
                    ],
                    'competition_registrations.export' => [
                        'label' => 'Export Pendaftar ke Excel',
                        'type' => 'export',
                        'badge' => 'Excel',
                        'routes' => ['admin.competition-registrations.export-excel', 'admin.competition-registrations.export'],
                    ],
                    'competition_checkin.view' => [
                        'label' => 'Buka Meja Registrasi Ulang (Hari-H)',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.competition-checkin.index'],
                    ],
                    'competition_checkin.scan' => [
                        'label' => 'Scanner QR & Check-in Kontingen',
                        'type' => 'action',
                        'badge' => 'Scanner',
                        'routes' => ['admin.competition-checkin.lookup', 'admin.competition-checkin.process'],
                    ],
                    'competition_checkin.cancel' => [
                        'label' => 'Batalkan Check-in Kehadiran',
                        'type' => 'action',
                        'badge' => 'Batal',
                        'routes' => ['admin.competition-checkin.cancel'],
                    ],
                    'competition_juries.view' => [
                        'label' => 'Lihat Daftar Akun Juri',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.competition-juries.index'],
                    ],
                    'competition_juries.manage' => [
                        'label' => 'Kelola & Registrasi Akun Juri',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.competition-juries.*'],
                    ],
                    'competition_scores.view' => [
                        'label' => 'Buka Form Input Nilai',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.competition-scores.index'],
                    ],
                    'competition_scores.input' => [
                        'label' => 'Input & Edit Nilai Lomba',
                        'type' => 'edit',
                        'badge' => 'Nilai',
                        'routes' => ['admin.competition-scores.store', 'admin.competition-scores.update', 'admin.competition-scores.input'],
                    ],
                    'competition_scores.master_sheet' => [
                        'label' => 'Akses Lembar Skor Utama (Rekap)',
                        'type' => 'view',
                        'badge' => 'Master',
                        'routes' => ['admin.competition-scores.master-sheet*'],
                    ],
                    'competition_scores.export' => [
                        'label' => 'Export Lembar Skor ke Excel',
                        'type' => 'export',
                        'badge' => 'Excel',
                        'routes' => ['admin.competition-scores.export-excel', 'admin.competition-scores.export'],
                    ],
                    'competition_mascots.manage' => [
                        'label' => 'Kelola Maskot & Foto Lomba',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.competition-mascots.*'],
                    ],
                    'competition_leaderboard.view' => [
                        'label' => 'Lihat Rekap Juara Umum & Klasemen',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.competition-leaderboard.index'],
                    ],
                    'competition_leaderboard.publish' => [
                        'label' => 'Publikasi Juara ke Website',
                        'type' => 'action',
                        'badge' => 'Publikasi',
                        'routes' => ['admin.competition-leaderboard.publish', 'admin.competition-leaderboard.toggle'],
                    ],
                    'competition_participants.view' => [
                        'label' => 'Lihat Peserta & Juara Lomba',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.competition-participants.index'],
                    ],
                    'competition_participants.manage' => [
                        'label' => 'Cetak Piagam & Sertifikat',
                        'type' => 'export',
                        'badge' => 'Piagam',
                        'routes' => ['admin.competition-participants.*', 'admin.competition-certificates.*'],
                    ],
                    'competition_broadcast.send' => [
                        'label' => 'Kirim Siaran Email Broadcast',
                        'type' => 'action',
                        'badge' => 'Email',
                        'routes' => ['admin.competition-broadcast.*'],
                    ],
                    'competition_info_menus.manage' => [
                        'label' => 'Kelola Menu Informasi Lomba',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.competition-info-menus.*'],
                    ],
                ],
            ],
            'parking' => [
                'name' => 'Tiket & Kantong Parkir',
                'icon' => 'fa-solid fa-square-parking',
                'color' => 'text-emerald-600 bg-emerald-50',
                'items' => [
                    'parking_locations.view' => [
                        'label' => 'Lihat Daftar Kantong Parkir',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.parking.index', 'admin.parking.locations.index'],
                    ],
                    'parking_locations.manage' => [
                        'label' => 'Kelola Kantong Parkir',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.parking.locations.*'],
                    ],
                    'parking_tickets.view' => [
                        'label' => 'Lihat Tiket Kendaraan',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.parking.tickets.index'],
                    ],
                    'parking_tickets.assign' => [
                        'label' => 'Alokasikan / Floating Lokasi',
                        'type' => 'action',
                        'badge' => 'Floating',
                        'routes' => ['admin.parking.tickets.assign-location', 'admin.parking.auto-allocate-priority'],
                    ],
                    'parking_tickets.scan' => [
                        'label' => 'Scanner QR & Validasi Gerbang',
                        'type' => 'action',
                        'badge' => 'Scanner',
                        'routes' => ['admin.parking.tickets.status', 'admin.parking.scanner'],
                    ],
                    'parking_tickets.print' => [
                        'label' => 'Cetak Tiket Parkir',
                        'type' => 'export',
                        'badge' => 'Cetak',
                        'routes' => ['admin.parking.tickets.print'],
                    ],
                    'parking_quotas.manage' => [
                        'label' => 'Pengaturan Kuota & Batas Waktu H+1',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.parking.quotas*'],
                    ],
                    'parking.fares_manage' => [
                        'label' => 'Pengaturan Tarif Parkir Umum',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.parking.fares.*'],
                    ],
                    'parking.export' => [
                        'label' => 'Export Rekap Parkir ke Excel',
                        'type' => 'export',
                        'badge' => 'Excel',
                        'routes' => ['admin.parking.export-excel'],
                    ],
                ],
            ],
            'blood_donation' => [
                'name' => 'Kelola Donor Darah Sukarela',
                'icon' => 'fa-solid fa-hand-holding-droplet',
                'color' => 'text-red-600 bg-red-50',
                'items' => [
                    'blood_stocks.view' => [
                        'label' => 'Lihat Stok Darah PMI',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.blood-stocks.index', 'admin.blood-stocks.show'],
                    ],
                    'blood_stocks.update' => [
                        'label' => 'Update Stok Kantong Darah',
                        'type' => 'edit',
                        'badge' => 'Update',
                        'routes' => ['admin.blood-stocks.create', 'admin.blood-stocks.store', 'admin.blood-stocks.edit', 'admin.blood-stocks.update'],
                    ],
                    'blood_donation_events.view' => [
                        'label' => 'Lihat Jadwal Donor Darah',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.blood-donation-events.index', 'admin.blood-donation-events.show'],
                    ],
                    'blood_donation_events.manage' => [
                        'label' => 'Kelola Jadwal & Event Donor',
                        'type' => 'manage',
                        'badge' => 'Kelola',
                        'routes' => ['admin.blood-donation-events.create', 'admin.blood-donation-events.store', 'admin.blood-donation-events.edit', 'admin.blood-donation-events.update', 'admin.blood-donation-events.destroy'],
                    ],
                    'blood_donor_registrations.view' => [
                        'label' => 'Lihat Pendaftar Donor Darah',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.blood-donor-registrations.index', 'admin.blood-donor-registrations.show'],
                    ],
                    'blood_donor_registrations.verify' => [
                        'label' => 'Verifikasi Kehadiran Pendonor',
                        'type' => 'action',
                        'badge' => 'Verifikasi',
                        'routes' => ['admin.blood-donor-registrations.edit', 'admin.blood-donor-registrations.update'],
                    ],
                    'blood_donor_registrations.export' => [
                        'label' => 'Export Data Pendonor ke Excel',
                        'type' => 'export',
                        'badge' => 'Excel',
                        'routes' => ['admin.blood-donor-registrations.export'],
                    ],
                ],
            ],
            'user_permissions' => [
                'name' => 'Manajemen Hak Akses Pengguna',
                'icon' => 'fa-solid fa-shield-halved',
                'color' => 'text-indigo-600 bg-indigo-50',
                'items' => [
                    'user_permissions.view' => [
                        'label' => 'Lihat Matriks Hak Akses Pengguna',
                        'type' => 'view',
                        'badge' => 'Lihat',
                        'routes' => ['admin.user-permissions.index'],
                    ],
                    'user_permissions.manage' => [
                        'label' => 'Ubah Hak Akses Pengguna Lain',
                        'type' => 'root',
                        'badge' => 'Root',
                        'routes' => ['admin.user-permissions.edit', 'admin.user-permissions.update', 'admin.user-permissions.reset'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Dapatkan daftar template preset cepat (1-klik)
     *
     * @return array<string, array<string, mixed>>
     */
    public static function presets(): array
    {
        return [
            'panitia_lomba' => [
                'name' => 'Template Panitia Lomba',
                'icon' => 'fa-solid fa-trophy',
                'description' => 'Akses penuh ke modul Sua Bhakti Berkarya & Tiket Parkir',
                'permissions' => [
                    'dashboard.view',
                    'competition_event.view',
                    'competition_registrations.view',
                    'competition_registrations.verify',
                    'competition_registrations.export',
                    'competition_checkin.view',
                    'competition_checkin.scan',
                    'competition_checkin.cancel',
                    'competition_juries.view',
                    'competition_scores.view',
                    'competition_scores.input',
                    'competition_scores.master_sheet',
                    'competition_scores.export',
                    'competition_leaderboard.view',
                    'competition_participants.view',
                    'competition_participants.manage',
                    'competition_info_menus.manage',
                    'parking_locations.view',
                    'parking_tickets.view',
                    'parking_tickets.assign',
                    'parking_tickets.scan',
                    'parking_tickets.print',
                    'parking.export',
                ],
                'can_delete' => false,
                'can_export' => true,
            ],
            'petugas_parkir' => [
                'name' => 'Petugas Parkir Lapangan',
                'icon' => 'fa-solid fa-square-parking',
                'description' => 'Fokus pada scanner QR tiket & alokasi kendaraan',
                'permissions' => [
                    'dashboard.view',
                    'parking_locations.view',
                    'parking_tickets.view',
                    'parking_tickets.assign',
                    'parking_tickets.scan',
                    'parking_tickets.print',
                ],
                'can_delete' => false,
                'can_export' => false,
            ],
            'redaksi_artikel' => [
                'name' => 'Tim Redaksi & Jurnalistik',
                'icon' => 'fa-solid fa-newspaper',
                'description' => 'Kelola artikel, materi P3K, media, dan galeri kegiatan',
                'permissions' => [
                    'dashboard.view',
                    'articles.view',
                    'articles.create',
                    'articles.edit',
                    'categories.view',
                    'media_manager.view',
                    'media_manager.upload',
                    'activities.view',
                    'gallery.view',
                ],
                'can_delete' => false,
                'can_export' => true,
            ],
            'tim_donor_darah' => [
                'name' => 'Tim Donor Darah PMI',
                'icon' => 'fa-solid fa-hand-holding-droplet',
                'description' => 'Update live stok darah, event donor, dan verifikasi pendonor',
                'permissions' => [
                    'dashboard.view',
                    'blood_stocks.view',
                    'blood_stocks.update',
                    'blood_donation_events.view',
                    'blood_donation_events.manage',
                    'blood_donor_registrations.view',
                    'blood_donor_registrations.verify',
                    'blood_donor_registrations.export',
                ],
                'can_delete' => false,
                'can_export' => true,
            ],
            'sekretariat' => [
                'name' => 'Tim Sekretariat & Administrasi',
                'icon' => 'fa-solid fa-id-card-clip',
                'description' => 'Penerimaan calon anggota baru, biodata, KTA, dan verifikasi pendaftar',
                'permissions' => [
                    'dashboard.view',
                    'member_registrations.view',
                    'member_registrations.verify',
                    'members.view',
                    'members.create',
                    'members.edit',
                    'members.print_kta',
                    'members.export',
                    'competition_registrations.view',
                    'competition_registrations.verify',
                ],
                'can_delete' => false,
                'can_export' => true,
            ],
        ];
    }
}
