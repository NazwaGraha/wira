<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationSubmittedMail;
use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionInfoMenu;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionScore;
use App\Models\Gallery;
use App\Models\ParkingFareSetting;
use App\Models\ParkingLocation;
use App\Models\ParkingTicket;
use App\Services\CompetitionSpreadsheetService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CompetitionController extends Controller
{
    public function index()
    {
        $event = CompetitionEvent::where('is_active', true)->with(['categories' => function ($q) {
            $q->where('is_active', true)->orderBy('order_position');
        }])->first();

        $pageState = 'empty';
        $pastEvent = null;

        if ($event) {
            $pageState = $event->getDisplayState();
        } else {
            // Find most recent previous event to display in history/hall of fame
            $pastEvent = CompetitionEvent::latest()->first();
        }

        $categoriesByLevel = $event ? $event->categories->groupBy('level') : ($pastEvent ? $pastEvent->categories()->where('is_active', true)->get()->groupBy('level') : collect());

        // Load Menu Informasi Lomba (Backoffice Managed)
        $infoMenus = collect();
        $targetEvent = $event ?: $pastEvent;
        try {
            if (Schema::hasTable('competition_info_menus')) {
                $infoMenus = CompetitionInfoMenu::where('is_active', true)
                    ->when($targetEvent, function ($q) use ($targetEvent) {
                        $q->where(function ($sub) use ($targetEvent) {
                            $sub->where('competition_event_id', $targetEvent->id)
                                ->orWhereNull('competition_event_id');
                        });
                    })
                    ->orderBy('order_position')
                    ->orderBy('id')
                    ->get();
            }
        } catch (\Throwable $e) {
            $infoMenus = collect();
        }

        // Fallback to default 8 items if database records are empty
        if ($infoMenus->isEmpty()) {
            $defaultItems = CompetitionInfoMenu::defaultItems();
            $infoMenus = collect(array_map(function ($item) use ($targetEvent) {
                if ($targetEvent && $targetEvent->handbook_file && in_array($item['title'], ['Juklak Juknis', 'Buku Panduan Lomba'])) {
                    $item['action_type'] = 'file';
                    $item['file_path'] = $targetEvent->handbook_file;
                }

                return new CompetitionInfoMenu($item);
            }, $defaultItems));
        }

        // Past galleries / highlight documentation
        $pastGalleries = collect();
        try {
            if (Schema::hasTable('galleries')) {
                $pastGalleries = Gallery::where('is_active', true)
                    ->latest()
                    ->take(6)
                    ->get();
            }
        } catch (\Throwable $e) {
            $pastGalleries = collect();
        }

        // Konfigurasi Penayangan Menu Informasi Lomba di Halaman Publik
        $isInfoMenuVisible = true;
        if ($targetEvent) {
            $isInfoMenuVisible = $targetEvent->isInfoMenuVisible();
        }

        // Pengaturan Tarif Parkir Umum
        $parkingFareSetting = ParkingFareSetting::current();

        return view('pages.lomba.index', compact('event', 'pastEvent', 'pageState', 'categoriesByLevel', 'infoMenus', 'isInfoMenuVisible', 'pastGalleries', 'parkingFareSetting'));
    }

    public function register()
    {
        $event = CompetitionEvent::where('is_active', true)->with(['categories' => function ($q) {
            $q->where('is_active', true)->orderBy('order_position');
        }])->first();

        if (! $event || ! $event->is_registration_open) {
            return redirect()->route('lomba.index')->with('error', 'Pendaftaran lomba saat ini sedang ditutup.');
        }

        $activeWave = $event->getActiveWave();
        if ($activeWave === 'closed') {
            return redirect()->route('lomba.index')->with('error', 'Pendaftaran lomba saat ini sudah ditutup (Periode pendaftaran telah berakhir).');
        }

        if ($activeWave === 'upcoming') {
            $startDate = $event->wave1_start_date ? $event->wave1_start_date->translatedFormat('d F Y, H:i') : 'segera';

            return redirect()->route('lomba.index')->with('info', "Pendaftaran lomba baru akan dibuka pada {$startDate} WIB.");
        }

        $categoriesByLevel = $event->categories->groupBy('level');
        $waveLabel = $event->getActiveWaveLabel();
        $countdownTarget = $event->getCountdownTarget();
        $bankAccounts = $event->getAllBankAccounts();
        $previewRegistrationCode = CompetitionRegistration::generateRegistrationCode('Madya');

        return view('pages.lomba.register', compact('event', 'categoriesByLevel', 'activeWave', 'waveLabel', 'countdownTarget', 'bankAccounts', 'previewRegistrationCode'));
    }

    public function store(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->firstOrFail();

        $activeWave = $event->getActiveWave();
        if ($activeWave === 'closed' || ! $event->is_registration_open) {
            return redirect()->route('lomba.index')->with('error', 'Pendaftaran lomba saat ini sudah ditutup.');
        }
        if ($activeWave === 'upcoming') {
            return redirect()->route('lomba.index')->with('info', 'Pendaftaran lomba belum dibuka.');
        }

        $validated = $request->validate([
            'school_name' => 'required|string|max:255',
            'level' => 'required|in:Mula,Madya,Wira',
            'advisor_name' => 'required|string|max:255',
            'advisor_phone' => 'required|string|max:20',
            'advisor_email' => 'required|email|max:255',
            'school_address' => 'nullable|string',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:competition_categories,id',
            'registration_code' => 'nullable|string|max:50',
            'mass_participants_file' => 'nullable|file|max:10240',
            'team_labels' => 'nullable|array',
            'team_members' => 'nullable|array',
            'team_files' => 'nullable|array',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        $schoolNameClean = strtoupper(trim($validated['school_name']));
        $categoriesMap = CompetitionCategory::whereIn('id', $validated['categories'])->get()->keyBy('id');

        // 1. Validasi Kuota Global Cabang Lomba & Batasan Kuota per Sekolah
        foreach ($validated['categories'] as $catId) {
            $cat = $categoriesMap->get($catId);
            if (! $cat) {
                continue;
            }

            $genderLabel = $cat->gender_category !== 'Umum' ? " ({$cat->gender_category})" : '';

            // Cek apakah kuota global sudah terpenuhi penuh
            if ($cat->is_quota_enforced && $cat->isQuotaFull()) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'categories' => "Cabang lomba '{$cat->name}{$genderLabel}' sudah ditutup karena kuota peserta ({$cat->getMaxQuota()} regu) telah terpenuhi secara penuh.",
                    ]);
            }

            $labels = $request->input("team_labels.{$catId}");
            $count = is_array($labels) ? count(array_filter($labels, fn ($l) => $l !== null && trim($l) !== '')) : 1;

            // Cek sisa kuota global yang tersedia
            if ($cat->is_quota_enforced) {
                $remainingQuota = $cat->getRemainingQuota();
                if ($count > $remainingQuota) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'categories' => "Cabang lomba '{$cat->name}{$genderLabel}' hanya tersisa {$remainingQuota} slot regu. Anda mencoba mendaftarkan {$count} regu. Silakan sesuaikan jumlah regu yang dikirimkan.",
                        ]);
                }
            }

            // Cek kuota maksimal per sekolah
            if (! $cat->isUnlimitedTeams()) {
                $maxAllowed = $cat->maxTeamsPerSchool();
                if ($count > $maxAllowed) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'categories' => "Cabang lomba '{$cat->name}{$genderLabel}' dibatasi maksimal {$maxAllowed} regu per sekolah. Anda mencoba mendaftarkan {$count} regu.",
                        ]);
                }
            }
        }

        // Cek apakah sekolah sudah pernah terdaftar di kategori yang sama pada event ini
        $existingRegs = CompetitionRegistration::where('competition_event_id', $event->id)
            ->where('school_name', $schoolNameClean)
            ->where('status', '!=', 'rejected')
            ->with('teams.category')
            ->get();

        if ($existingRegs->isNotEmpty()) {
            foreach ($validated['categories'] as $catId) {
                $cat = $categoriesMap->get($catId);
                if (! $cat || $cat->isUnlimitedTeams()) {
                    continue;
                }

                $labels = $request->input("team_labels.{$catId}");
                $newCount = is_array($labels) ? count(array_filter($labels, fn ($l) => $l !== null && trim($l) !== '')) : 1;

                $previouslyRegisteredCount = 0;
                foreach ($existingRegs as $er) {
                    $previouslyRegisteredCount += $er->teams->where('competition_category_id', $catId)->count();
                }

                $maxAllowed = $cat->maxTeamsPerSchool();
                if (($previouslyRegisteredCount + $newCount) > $maxAllowed) {
                    $genderLabel = $cat->gender_category !== 'Umum' ? " {$cat->gender_category}" : '';

                    return redirect()->back()
                        ->withInput()
                        ->withErrors([
                            'categories' => "Sekolah {$schoolNameClean} sudah mendaftarkan {$previouslyRegisteredCount} regu pada cabang '{$cat->name}{$genderLabel}'. Cabang ini dibatasi maksimal {$maxAllowed} regu per sekolah (Total menjadi ".($previouslyRegisteredCount + $newCount).' regu).',
                        ]);
                }
            }
        }

        $userCode = trim($request->input('registration_code', ''));
        if (! empty($userCode) && ! CompetitionRegistration::where('registration_code', $userCode)->exists()) {
            $code = $userCode;
        } else {
            $code = CompetitionRegistration::generateRegistrationCode($validated['level'] ?? null);
        }

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = 'proof_'.time().'_'.Str::random(6).'.'.$file->getClientOriginalExtension();
            $proofPath = $file->storeAs('competition_payments', $filename, 'public');

            // Salin fallback jika public/storage adalah folder fisik biasa (bukan symlink) di shared hosting
            try {
                if (file_exists(public_path('storage')) && ! is_link(public_path('storage'))) {
                    File::ensureDirectoryExists(public_path('storage/competition_payments'), 0755, true);
                    File::copy(storage_path('app/public/'.$proofPath), public_path('storage/'.$proofPath));
                }
            } catch (\Throwable $e) {
                // Ignore fallback copy exception
            }
        }

        $teamsToCreate = [];
        foreach ($validated['categories'] as $catId) {
            $cat = $categoriesMap->get($catId);
            $labels = $request->input("team_labels.{$catId}");
            if (is_array($labels)) {
                $filtered = array_values(array_filter($labels, fn ($l) => $l !== null && trim($l) !== ''));
                if (empty($filtered)) {
                    $filtered = [''];
                }
                // Batasi jumlah regu sesuai kuota kategori
                $maxAllowed = $cat ? $cat->maxTeamsPerSchool() : 1;
                $filtered = array_slice($filtered, 0, $maxAllowed);

                foreach ($filtered as $idx => $lbl) {
                    $teamsToCreate[] = [
                        'category_id' => $catId,
                        'label' => trim($lbl),
                        'index' => $idx,
                    ];
                }
            } else {
                $lbl = trim($labels ?? '');
                $teamsToCreate[] = [
                    'category_id' => $catId,
                    'label' => $lbl,
                    'index' => 0,
                ];
            }
        }

        $totalTeams = count($teamsToCreate);
        $totalPayment = 0;
        $waveInt = is_numeric($activeWave) ? intval($activeWave) : 1;
        foreach ($teamsToCreate as $teamData) {
            $cat = $categoriesMap->get($teamData['category_id']);
            $fee = $cat ? $cat->getFeeForWave($waveInt) : floatval($event->registration_fee ?: 150000);
            $totalPayment += $fee;
        }

        $registration = CompetitionRegistration::create([
            'competition_event_id' => $event->id,
            'registration_code' => $code,
            'school_name' => strtoupper(trim($validated['school_name'])),
            'level' => $validated['level'],
            'advisor_name' => $validated['advisor_name'],
            'advisor_phone' => $validated['advisor_phone'],
            'advisor_email' => $validated['advisor_email'] ?? null,
            'school_address' => $validated['school_address'] ?? null,
            'payment_proof' => $proofPath,
            'total_payment' => $totalPayment,
            'registration_wave' => $waveInt,
            'status' => 'pending',
        ]);

        $spreadsheetService = app(CompetitionSpreadsheetService::class);

        // Pastikan kolom members_file tersedia di database secara aman
        $hasMembersFileColumn = false;
        try {
            $hasMembersFileColumn = Schema::hasColumn('competition_participant_teams', 'members_file');
            if (! $hasMembersFileColumn) {
                Schema::table('competition_participant_teams', function (Blueprint $table) {
                    $table->string('members_file')->nullable()->after('members_list');
                });
                $hasMembersFileColumn = true;
            }
        } catch (\Throwable $e) {
            $hasMembersFileColumn = false;
        }

        // Proses unggah massal jika pendaftar mengunggah 1 file Excel untuk semua regu
        $parsedMass = null;
        $massFilePath = null;
        if ($request->hasFile('mass_participants_file') && $request->file('mass_participants_file')->isValid()) {
            try {
                $massFile = $request->file('mass_participants_file');
                $filename = 'mass_participants_'.time().'_'.Str::random(6).'.'.$massFile->getClientOriginalExtension();
                $massFilePath = $massFile->storeAs('competition_team_files', $filename, 'public');

                if (file_exists(public_path('storage')) && ! is_link(public_path('storage'))) {
                    File::ensureDirectoryExists(public_path('storage/competition_team_files'), 0755, true);
                    @copy(storage_path('app/public/'.$massFilePath), public_path('storage/'.$massFilePath));
                }

                $parsedMass = $spreadsheetService->parseMassParticipantFile($massFile);
            } catch (\Throwable $e) {
                Log::warning("Gagal parse berkas Excel peserta massal [{$code}]: ".$e->getMessage());
            }
        }

        foreach ($teamsToCreate as $teamData) {
            $catId = $teamData['category_id'];
            $label = $teamData['label'];
            $idx = $teamData['index'] ?? 0;
            $cat = $categoriesMap->get($catId);
            $teamName = $registration->school_name.($label ? " {$label}" : '');

            $membersList = [];
            $membersFilePath = null;

            // 1. Ekstrak dari file Excel yang diunggah khusus untuk regu ini (jika ada)
            $uploadedFile = $request->file("team_files.{$catId}.{$idx}")
                ?? $request->file("team_files_{$catId}_{$idx}");

            if ($uploadedFile && $uploadedFile->isValid()) {
                try {
                    $filename = 'team_members_'.time().'_'.Str::random(6).'.'.$uploadedFile->getClientOriginalExtension();
                    $membersFilePath = $uploadedFile->storeAs('competition_team_files', $filename, 'public');

                    // Salin fallback jika di shared hosting
                    if (file_exists(public_path('storage')) && ! is_link(public_path('storage'))) {
                        File::ensureDirectoryExists(public_path('storage/competition_team_files'), 0755, true);
                        @copy(storage_path('app/public/'.$membersFilePath), public_path('storage/'.$membersFilePath));
                    }

                    $membersList = $spreadsheetService->parseParticipantFile($uploadedFile);
                } catch (\Throwable $e) {
                    Log::warning("Gagal parse file Excel peserta regu {$teamName}: ".$e->getMessage());
                }
            }

            // 2. Jika tidak ada file regu khusus, gunakan hasil parsing dari file unggah massal kolektif
            if (empty($membersList) && ! empty($parsedMass['grouped']) && $cat) {
                foreach ($parsedMass['grouped'] as $grp) {
                    $catMatches = (strcasecmp(trim($grp['category_name']), trim($cat->name)) === 0);
                    $genderMatches = true;
                    if ($cat->gender_category && in_array($cat->gender_category, ['Putra', 'Putri'])) {
                        $genderMatches = (strcasecmp(trim($grp['gender_category']), trim($cat->gender_category)) === 0);
                    }
                    $labelMatches = true;
                    if (! empty($label) && ! empty($grp['team_label'])) {
                        $cleanLbl1 = preg_replace('/^regu\s+/i', '', trim($label));
                        $cleanLbl2 = preg_replace('/^regu\s+/i', '', trim($grp['team_label']));
                        $labelMatches = (strcasecmp($cleanLbl1, $cleanLbl2) === 0);
                    }

                    if ($catMatches && $genderMatches && $labelMatches) {
                        $membersList = $grp['members'];
                        $membersFilePath = $massFilePath;
                        break;
                    }
                }

                // Fallback kedua: Jika label tidak cocok persis tapi kategori cocok dan hanya ada 1 regu pada kategori ini
                if (empty($membersList)) {
                    foreach ($parsedMass['grouped'] as $grp) {
                        $catMatches = (strcasecmp(trim($grp['category_name']), trim($cat->name)) === 0);
                        $genderMatches = true;
                        if ($cat->gender_category && in_array($cat->gender_category, ['Putra', 'Putri'])) {
                            $genderMatches = (strcasecmp(trim($grp['gender_category']), trim($cat->gender_category)) === 0);
                        }

                        if ($catMatches && $genderMatches) {
                            $membersList = $grp['members'];
                            $membersFilePath = $massFilePath;
                            break;
                        }
                    }
                }
            }

            // 3. Jika masih belum ada anggota, cek input JSON/array manual nama anggota
            if (empty($membersList)) {
                $rawMembers = $request->input("team_members.{$catId}.{$idx}")
                    ?? $request->input("team_members_{$catId}_{$idx}");

                if (is_array($rawMembers)) {
                    $membersList = $rawMembers;
                } elseif (is_string($rawMembers) && trim($rawMembers) !== '') {
                    $decoded = json_decode($rawMembers, true);
                    if (is_array($decoded)) {
                        $membersList = $decoded;
                    } else {
                        $lines = explode("\n", str_replace("\r", '', $rawMembers));
                        foreach ($lines as $line) {
                            $t = trim($line);
                            if ($t) {
                                $membersList[] = ['name' => $t, 'role' => 'Anggota'];
                            }
                        }
                    }
                }
            }

            $orderNumber = $cat ? $cat->generateNextOrderNumber($idx, $label) : null;

            $teamPayload = [
                'competition_registration_id' => $registration->id,
                'competition_category_id' => $catId,
                'order_number' => $orderNumber,
                'team_name' => $teamName,
                'team_label' => $label,
                'members_list' => $membersList,
            ];

            if ($hasMembersFileColumn && ! empty($membersFilePath)) {
                $teamPayload['members_file'] = $membersFilePath;
            }

            CompetitionParticipantTeam::create($teamPayload);
        }

        // Kirim email notifikasi tanda terima registrasi beserta lampiran PDF e-Kwitansi & Kartu Peserta
        if (! empty($registration->advisor_email)) {
            try {
                Mail::to($registration->advisor_email)->send(new RegistrationSubmittedMail($registration));
            } catch (\Throwable $e) {
                Log::warning("Gagal mengirim email konfirmasi pendaftaran [{$code}] ke {$registration->advisor_email}: ".$e->getMessage());
            }
        }

        return redirect()->route('lomba.status', ['code' => $code])->with('success', "Pendaftaran berhasil dikirim untuk {$totalTeams} regu! Kode Pendaftaran Anda: {$code}. Silakan simpan kode ini untuk mengecek status verifikasi panitia.");
    }

    public function status(Request $request)
    {
        $code = trim($request->query('code', ''));

        if (strtoupper($code) === 'SBB-TOCSEA') {
            try {
                CompetitionRegistration::where('registration_code', 'SBB-TOCSEA')
                    ->update(['registration_code' => 'SBB-W54342H']);
            } catch (\Throwable $e) {
                // Ignore if already updated or connection issue
            }

            return redirect('/lomba/status?code=SBB-W54342H');
        }

        $registration = null;

        if ($code) {
            $registration = CompetitionRegistration::where('registration_code', $code)
                ->with(['event', 'teams.category', 'parkingTickets.parkingLocation'])
                ->first();
        }

        $fareSetting = ParkingFareSetting::current();

        return view('pages.lomba.status', compact('registration', 'code', 'fareSetting'));
    }

    /**
     * Mendaftarkan data kendaraan kontingen dari Halaman Cek Status
     */
    public function registerVehicles(Request $request)
    {
        $validated = $request->validate([
            'registration_code' => 'required|string|max:50',
            'vehicles' => 'required|array|min:1',
            'vehicles.*.vehicle_type' => 'required|in:r4_car,r4_elf,r4_bus,r2_motor',
            'vehicles.*.license_plate' => 'required|string|max:30',
            'vehicles.*.vehicle_brand' => 'nullable|string|max:100',
            'vehicles.*.vehicle_color' => 'nullable|string|max:50',
            'vehicles.*.driver_name' => 'nullable|string|max:100',
            'vehicles.*.driver_phone' => 'nullable|string|max:30',
            'payment_method' => 'nullable|in:cash,qris',
        ]);

        $code = trim($validated['registration_code']);
        $registration = CompetitionRegistration::where('registration_code', $code)->first();

        if (! $registration) {
            return back()->with('error', 'Nomor pendaftaran tidak ditemukan.');
        }

        if ($registration->status !== 'verified') {
            return back()->with('error', 'Pendaftaran kendaraan hanya dapat dilakukan setelah status pendaftaran dinyatakan TERVERIFIKASI / LUNAS.');
        }

        $vehicles = $validated['vehicles'];
        $isExpired = $registration->is_parking_registration_expired;
        $fareSetting = ParkingFareSetting::current();

        if (! $isExpired) {
            // ================= JALUR 1: SEBELUM BATAS WAKTU (KUOTA GRATIS KONTINGEN RESMI) =================
            $newR4Count = 0;
            $newR2Count = 0;

            foreach ($vehicles as $v) {
                if (str_starts_with($v['vehicle_type'], 'r2')) {
                    $newR2Count++;
                } else {
                    $newR4Count++;
                }
            }

            // Validasi kuota tersisa
            if ($newR4Count > $registration->remaining_quota_r4) {
                return back()->with('error', "Jumlah kendaraan Roda 4 ({$newR4Count}) melebihi sisa kuota gratis yang diizinkan ({$registration->remaining_quota_r4} mobil). Silakan sesuaikan.");
            }

            if ($newR2Count > $registration->remaining_quota_r2) {
                return back()->with('error', "Jumlah kendaraan Roda 2 ({$newR2Count}) melebihi sisa kuota gratis yang diizinkan ({$registration->remaining_quota_r2} motor). Silakan sesuaikan.");
            }

            // Tentukan lokasi parkir yang dialokasikan (sesuai prioritas kontingen)
            $assignedLocationId = $registration->parkingTickets()->whereNotNull('parking_location_id')->value('parking_location_id');
            if (! $assignedLocationId && ! empty($registration->parking_priority_criteria['assigned_location_code'])) {
                $assignedLocationId = ParkingLocation::where('code', $registration->parking_priority_criteria['assigned_location_code'])->value('id');
            }

            $createdCount = 0;
            foreach ($vehicles as $v) {
                ParkingTicket::create([
                    'competition_registration_id' => $registration->id,
                    'parking_location_id' => $assignedLocationId,
                    'vehicle_type' => $v['vehicle_type'],
                    'license_plate' => strtoupper(trim($v['license_plate'])),
                    'vehicle_brand' => trim($v['vehicle_brand'] ?? ''),
                    'vehicle_color' => trim($v['vehicle_color'] ?? ''),
                    'driver_name' => trim($v['driver_name'] ?? ''),
                    'driver_phone' => trim($v['driver_phone'] ?? ''),
                    'is_public' => false,
                    'fee_amount' => 0,
                    'payment_method' => 'free_contingent',
                    'payment_status' => 'free',
                    'status' => 'booked',
                ]);
                $createdCount++;
            }

            return redirect()->route('lomba.status', ['code' => $code])
                ->with('success', "Alhamdulillah! Berhasil mendaftarkan {$createdCount} kendaraan kontingen resmi (Gratis). E-Tiket parkir telah diterbitkan!");
        } else {
            // ================= JALUR 2: SETELAH BATAS WAKTU (TARIF PARKIR UMUM & FLOATING PARKIR) =================
            $paymentMethod = $validated['payment_method'] ?? 'cash';
            $paymentStatus = ($paymentMethod === 'qris') ? 'paid' : 'unpaid';
            $totalFee = 0;
            $createdCount = 0;

            foreach ($vehicles as $v) {
                $vType = $v['vehicle_type'];
                $isR4 = in_array($vType, ['r4_car', 'r4_elf', 'r4_bus']);
                $fare = $fareSetting->getFareForVehicleType($vType);
                $totalFee += $fare;

                // Floating parkir disesuaikan dengan ketersediaan kuota slot di masing-masing kantong parkir
                $floatingLocation = ParkingLocation::where('status', 'open')
                    ->get()
                    ->first(function ($loc) use ($isR4) {
                        return $isR4 ? ($loc->remaining_r4 > 0) : ($loc->remaining_r2 > 0);
                    }) ?: ParkingLocation::where('status', 'open')->first();

                ParkingTicket::create([
                    'competition_registration_id' => $registration->id,
                    'parking_location_id' => $floatingLocation?->id,
                    'vehicle_type' => $vType,
                    'license_plate' => strtoupper(trim($v['license_plate'])),
                    'vehicle_brand' => trim($v['vehicle_brand'] ?? ''),
                    'vehicle_color' => trim($v['vehicle_color'] ?? ''),
                    'driver_name' => trim($v['driver_name'] ?? ''),
                    'driver_phone' => trim($v['driver_phone'] ?? ''),
                    'is_public' => true,
                    'fee_amount' => $fare,
                    'payment_method' => $paymentMethod,
                    'payment_status' => $paymentStatus,
                    'status' => 'booked',
                ]);
                $createdCount++;
            }

            $formattedTotal = 'Rp '.number_format($totalFee, 0, ',', '.');
            $methodLabel = $paymentMethod === 'qris' ? 'QRIS Digital' : 'Tunai saat tiba di gerbang';

            return redirect()->route('lomba.status', ['code' => $code])
                ->with('success', "Pendaftaran kendaraan kontingen berhasil! Karena pendaftaran kendaraan dilakukan setelah batas waktu yang ditentukan (melewati batas waktu 1 hari setelah penutupan pendaftaran lomba Gelombang ke-2), {$createdCount} kendaraan dikenakan Tarif Parkir Umum dengan sistem Floating Parkir sebesar {$formattedTotal} ({$methodLabel}). E-Tiket parkir resmi telah diterbitkan!");
        }
    }

    /**
     * Hapus tiket kendaraan kontingen oleh sekolah (selama belum check-in)
     */
    public function deleteVehicleTicket(Request $request)
    {
        $validated = $request->validate([
            'registration_code' => 'required|string',
            'ticket_id' => 'required|exists:parking_tickets,id',
        ]);

        $registration = CompetitionRegistration::where('registration_code', $validated['registration_code'])->firstOrFail();
        $ticket = ParkingTicket::where('id', $validated['ticket_id'])
            ->where('competition_registration_id', $registration->id)
            ->firstOrFail();

        if ($ticket->status === 'parked') {
            return back()->with('error', 'Tiket tidak dapat dihapus karena kendaraan telah check-in di lokasi parkir.');
        }

        $plate = $ticket->license_plate;
        $ticket->delete();

        return redirect()->route('lomba.status', ['code' => $registration->registration_code])
            ->with('success', "Kendaraan dengan nomor polisi {$plate} berhasil dihapus dari daftar parkir.");
    }

    /**
     * Pembelian E-Tiket Parkir Resmi Pengunjung Umum dari Portal Lomba
     */
    public function storePublicParkingTicket(Request $request)
    {
        $fareSetting = ParkingFareSetting::current();

        if (! $fareSetting->is_public_parking_active) {
            return back()->with('error', 'Mohon maaf, pemesanan tiket parkir umum saat ini sedang ditutup oleh panitia.');
        }

        $validated = $request->validate([
            'vehicle_type' => 'required|in:r2_motor,r4_car,r4_elf,r4_bus',
            'license_plate' => 'required|string|max:30',
            'vehicle_brand' => 'nullable|string|max:100',
            'vehicle_color' => 'nullable|string|max:50',
            'driver_name' => 'required|string|max:100',
            'driver_phone' => 'required|string|max:30',
            'payment_method' => 'required|in:qris,cash',
        ]);

        $validated['license_plate'] = strtoupper(trim($validated['license_plate']));
        $validated['is_public'] = true;
        $validated['fee_amount'] = $fareSetting->getFareForVehicleType($validated['vehicle_type']);
        $validated['payment_status'] = ($validated['payment_method'] === 'qris') ? 'paid' : 'unpaid';
        $validated['status'] = 'booked';

        // Auto-assign ke kantong parkir umum yang tersedia
        $isR4 = in_array($validated['vehicle_type'], ['r4_car', 'r4_elf', 'r4_bus']);
        $location = ParkingLocation::where('status', 'open')
            ->get()
            ->first(function ($loc) use ($isR4) {
                return $isR4 ? ($loc->remaining_r4 > 0) : ($loc->remaining_r2 > 0);
            }) ?: ParkingLocation::where('status', 'open')->first();

        if ($location) {
            $validated['parking_location_id'] = $location->id;
        }

        $ticket = ParkingTicket::create($validated);

        return redirect()->route('parking.public-ticket', $ticket->qr_code_token)
            ->with('success', 'E-Tiket parkir umum Anda berhasil diterbitkan! Silakan simpan atau screenshot tiket ini.');
    }

    /**
     * Lacak / Cek E-Tiket Parkir Pengunjung Umum berdasarkan Plat Nomor atau Kode Tiket
     */
    public function checkPublicParkingTicket(Request $request)
    {
        $query = trim($request->input('ticket_search', ''));

        if (! $query) {
            return back()->with('error', 'Silakan masukkan nomor polisi (plat) atau kode tiket parkir.');
        }

        $cleanQuery = strtoupper(str_replace(' ', '', $query));

        $ticket = ParkingTicket::where(function ($q) use ($query, $cleanQuery) {
            $q->where('ticket_code', 'like', "%{$query}%")
                ->orWhereRaw("UPPER(REPLACE(license_plate, ' ', '')) = ?", [$cleanQuery])
                ->orWhere('driver_phone', 'like', "%{$query}%");
        })->latest()->first();

        if (! $ticket) {
            return back()->with('error', "Tiket parkir untuk '{$query}' tidak ditemukan. Pastikan nomor plat atau kode tiket benar.");
        }

        return redirect()->route('parking.public-ticket', $ticket->qr_code_token);
    }

    /**
     * Download format template Excel untuk upload daftar anggota per regu
     */
    public function downloadParticipantTemplate(CompetitionSpreadsheetService $spreadsheetService)
    {
        $filePath = $spreadsheetService->downloadTemplate();
        $filename = 'Template_Daftar_Peserta_Regu_PMR.xlsx';

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * API AJAX untuk membaca & memvalidasi file Excel daftar anggota regu (Live Preview)
     */
    public function parseExcelPeserta(Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        $request->validate([
            'excel_file' => 'required|file|max:5120',
        ]);

        $file = $request->file('excel_file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format file harus berupa Excel (.xlsx) atau CSV (.csv).',
            ], 422);
        }

        try {
            $members = $spreadsheetService->parseParticipantFile($file);

            return response()->json([
                'success' => true,
                'count' => count($members),
                'members' => $members,
                'filename' => $file->getClientOriginalName(),
                'message' => count($members).' peserta berhasil dibaca dari file Excel.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file Excel: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download format template Excel Massal Kolektif (1 File Seluruh Regu) dengan nomor registrasi & identitas sekolah
     */
    public function downloadMassParticipantTemplate(Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        $schoolName = trim($request->input('school_name', ''));
        $level = trim($request->input('level', 'Madya'));
        $advisorName = trim($request->input('advisor_name', ''));
        $advisorPhone = trim($request->input('advisor_phone', ''));
        $advisorEmail = trim($request->input('advisor_email', ''));
        $schoolAddress = trim($request->input('school_address', ''));
        $regCode = trim($request->input('registration_code', ''));

        if (empty($regCode)) {
            $regCode = CompetitionRegistration::generateRegistrationCode($level);
        }

        $categoryIds = (array) $request->input('categories', []);
        $categories = CompetitionCategory::whereIn('id', $categoryIds)->get();
        $categoryNames = $categories->pluck('name')->unique()->values()->toArray();

        $teams = [];
        foreach ($categories as $cat) {
            $labels = $request->input("team_labels.{$cat->id}");
            if (is_array($labels)) {
                $filtered = array_values(array_filter($labels, fn ($l) => $l !== null && trim($l) !== ''));
                if (empty($filtered)) {
                    $filtered = ['Regu A'];
                }
                foreach ($filtered as $lbl) {
                    $teams[] = [
                        'category_name' => $cat->name,
                        'gender' => $cat->gender_category ?: 'Umum',
                        'team_label' => trim($lbl),
                    ];
                }
            } else {
                $teams[] = [
                    'category_name' => $cat->name,
                    'gender' => $cat->gender_category ?: 'Umum',
                    'team_label' => 'Regu A',
                ];
            }
        }

        $filePath = $spreadsheetService->downloadMassTemplate([
            'registration_code' => $regCode,
            'level' => $level,
            'school_name' => $schoolName ?: 'SEKOLAH_PMR',
            'advisor_name' => $advisorName,
            'advisor_phone' => $advisorPhone,
            'advisor_email' => $advisorEmail,
            'school_address' => $schoolAddress,
            'category_names' => $categoryNames,
            'teams' => $teams,
        ]);

        $cleanSchoolSlug = Str::slug($schoolName ?: 'PMR');
        $filename = "Template_Massal_Peserta_{$cleanSchoolSlug}.xlsx";

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download format template Excel Massal khusus untuk sekolah yang telah terverifikasi
     */
    public function downloadVerifiedSchoolMassTemplate($code, CompetitionSpreadsheetService $spreadsheetService)
    {
        $code = trim($code);
        $registration = CompetitionRegistration::where('registration_code', $code)
            ->orWhere('id', $code)
            ->with(['teams.category'])
            ->firstOrFail();

        $categoryNames = $registration->teams->pluck('category.name')->filter()->unique()->values()->toArray();
        if (empty($categoryNames)) {
            $categoryNames = ['Pertolongan Pertama', 'Ketangkasan Tandu Reguler'];
        }

        $teams = [];
        foreach ($registration->teams as $team) {
            $cat = $team->category;
            $teams[] = [
                'category_name' => $cat?->name ?? 'Cabang Lomba',
                'gender' => $cat?->gender_category ?: 'Umum',
                'team_label' => $team->team_label ?: 'Regu A',
            ];
        }

        $filePath = $spreadsheetService->downloadMassTemplate([
            'registration_code' => $registration->registration_code,
            'level' => $registration->level,
            'school_name' => $registration->school_name,
            'advisor_name' => $registration->advisor_name,
            'advisor_phone' => $registration->advisor_phone,
            'advisor_email' => $registration->advisor_email,
            'school_address' => $registration->school_address,
            'category_names' => $categoryNames,
            'teams' => $teams,
        ]);

        $cleanSchoolSlug = Str::slug($registration->school_name ?: 'PMR');
        $filename = "Template_Massal_Peserta_{$cleanSchoolSlug}.xlsx";

        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Mengunggah berkas Excel massal peserta dari Halaman Cek Status (khusus status Terverifikasi)
     */
    public function uploadMassParticipantsStatus(Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        $validated = $request->validate([
            'registration_code' => 'required|string|max:50',
            'mass_participants_file' => 'required|file|max:10240',
        ]);

        $code = trim($validated['registration_code']);
        $registration = CompetitionRegistration::where('registration_code', $code)
            ->with('teams.category')
            ->first();

        if (! $registration) {
            return back()->with('error', 'Nomor registrasi tidak ditemukan.');
        }

        if ($registration->status !== 'verified') {
            return back()->with('error', 'Unggah berkas massal peserta hanya dapat dilakukan setelah status pendaftaran dinyatakan TERVERIFIKASI / LUNAS.');
        }

        $file = $request->file('mass_participants_file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return back()->with('error', 'Format berkas harus berupa Excel (.xlsx, .xls) atau CSV.');
        }

        try {
            $filename = 'mass_participants_'.time().'_'.Str::random(6).'.'.$ext;
            $massFilePath = $file->storeAs('competition_team_files', $filename, 'public');

            if (file_exists(public_path('storage')) && ! is_link(public_path('storage'))) {
                File::ensureDirectoryExists(public_path('storage/competition_team_files'), 0755, true);
                @copy(storage_path('app/public/'.$massFilePath), public_path('storage/'.$massFilePath));
            }

            $parsedMass = $spreadsheetService->parseMassParticipantFile($file);

            if (! $parsedMass['success'] || empty($parsedMass['grouped'])) {
                return back()->with('error', 'Berkas Excel berhasil diunggah namun tidak ada data nama peserta yang terbaca. Pastikan Anda mengunduh dan menggunakan Template Excel resmi.');
            }

            $updatedTeams = 0;
            $totalParticipants = 0;

            foreach ($registration->teams as $team) {
                $cat = $team->category;
                $label = $team->team_label ?? 'Regu A';

                foreach ($parsedMass['grouped'] as $grp) {
                    $catMatches = (strcasecmp(trim($grp['category_name']), trim($cat?->name ?? '')) === 0);
                    $genderMatches = true;
                    if ($cat?->gender_category && in_array($cat->gender_category, ['Putra', 'Putri'])) {
                        $genderMatches = (strcasecmp(trim($grp['gender_category']), trim($cat->gender_category)) === 0);
                    }
                    $labelMatches = true;
                    if (! empty($label) && ! empty($grp['team_label'])) {
                        $cleanLbl1 = preg_replace('/^regu\s+/i', '', trim($label));
                        $cleanLbl2 = preg_replace('/^regu\s+/i', '', trim($grp['team_label']));
                        $labelMatches = (strcasecmp($cleanLbl1, $cleanLbl2) === 0);
                    }

                    if ($catMatches && $genderMatches && $labelMatches && ! empty($grp['members'])) {
                        $team->members_list = $grp['members'];
                        $team->members_file = $massFilePath;
                        $team->save();
                        $updatedTeams++;
                        $totalParticipants += count($grp['members']);
                        break;
                    }
                }
            }

            if ($updatedTeams === 0) {
                // Fallback: jika label tidak persis cocok, petakan berdasarkan nama kategori secara berurutan
                $usedGroupIdx = [];
                foreach ($registration->teams as $team) {
                    $cat = $team->category;
                    foreach ($parsedMass['grouped'] as $gIdx => $grp) {
                        if (in_array($gIdx, $usedGroupIdx)) {
                            continue;
                        }
                        if (strcasecmp(trim($grp['category_name']), trim($cat?->name ?? '')) === 0 && ! empty($grp['members'])) {
                            $team->members_list = $grp['members'];
                            $team->members_file = $massFilePath;
                            $team->save();
                            $usedGroupIdx[] = $gIdx;
                            $updatedTeams++;
                            $totalParticipants += count($grp['members']);
                            break;
                        }
                    }
                }
            }

            return redirect()->route('lomba.status', ['code' => $registration->registration_code])
                ->with('success', "Berkas Excel massal peserta berhasil diunggah! Sebanyak {$totalParticipants} nama anggota untuk {$updatedTeams} regu berhasil diperbarui.");
        } catch (\Throwable $e) {
            Log::error("Gagal proses berkas massal peserta status [{$code}]: ".$e->getMessage());

            return back()->with('error', 'Terjadi kendala saat memproses berkas Excel: '.$e->getMessage());
        }
    }

    /**
     * Menyimpan data anggota peserta lomba langsung dari formulir web (Status Page)
     */
    public function saveWebParticipantsStatus(Request $request)
    {
        $validated = $request->validate([
            'registration_code' => 'required|string|max:50',
            'target_team_id' => 'nullable|integer',
            'teams' => 'required|array|min:1',
            'teams.*.team_id' => 'required|integer',
            'teams.*.members' => 'nullable|array',
            'teams.*.members.*.name' => 'nullable|string|max:150',
            'teams.*.members.*.nisn' => 'nullable|string|max:30',
            'teams.*.members.*.gender' => 'nullable|string|max:10',
            'teams.*.members.*.class_grade' => 'nullable|string|max:30',
            'teams.*.members.*.role' => 'nullable|string|max:50',
        ]);

        $code = trim($validated['registration_code']);
        $registration = CompetitionRegistration::where('registration_code', $code)
            ->with('teams.category')
            ->first();

        if (! $registration) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Nomor registrasi tidak ditemukan.'], 404);
            }

            return back()->with('error', 'Nomor registrasi tidak ditemukan.');
        }

        if ($registration->status !== 'verified') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Pengisian data peserta hanya dapat dilakukan setelah status terverifikasi.'], 403);
            }

            return back()->with('error', 'Pengisian data peserta hanya dapat dilakukan setelah status terverifikasi.');
        }

        $targetTeamId = ! empty($validated['target_team_id']) ? (int) $validated['target_team_id'] : null;
        $updatedTeams = 0;
        $totalMembers = 0;

        foreach ($validated['teams'] as $teamInput) {
            $teamId = (int) ($teamInput['team_id'] ?? 0);
            if ($targetTeamId && $teamId !== $targetTeamId) {
                continue;
            }

            $team = $registration->teams->firstWhere('id', $teamId);
            if (! $team) {
                continue;
            }

            $members = [];
            $requiredQuota = $team->category ? $team->category->getRequiredTeamMembers() : 2;

            if (! empty($teamInput['members']) && is_array($teamInput['members'])) {
                $idxCount = 0;
                foreach ($teamInput['members'] as $idx => $m) {
                    $name = trim($m['name'] ?? '');
                    if ($name === '') {
                        continue;
                    }

                    $role = trim($m['role'] ?? '');
                    if ($role === '') {
                        if ($requiredQuota === 1) {
                            $role = 'Peserta';
                        } elseif ($idxCount === 0) {
                            $role = 'Ketua Regu';
                        } else {
                            $role = 'Anggota '.$idxCount;
                        }
                    }

                    $gender = strtoupper(trim($m['gender'] ?? ''));
                    if (empty($gender) && $team->category?->gender_category) {
                        $gender = in_array($team->category->gender_category, ['Putra', 'Putri'])
                            ? ($team->category->gender_category === 'Putra' ? 'L' : 'P')
                            : '';
                    }

                    $members[] = [
                        'name' => ucwords(strtolower($name)),
                        'nisn' => ! empty($m['nisn']) ? trim($m['nisn']) : null,
                        'gender' => $gender ?: null,
                        'class_grade' => ! empty($m['class_grade']) ? trim($m['class_grade']) : null,
                        'role' => $role,
                    ];
                    $idxCount++;
                }
            }

            $team->members_list = $members;
            $team->save();
            $updatedTeams++;
            $totalMembers += count($members);
        }

        $message = "Berhasil menyimpan {$totalMembers} anggota peserta untuk {$updatedTeams} regu!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_teams' => $updatedTeams,
                'total_members' => $totalMembers,
            ]);
        }

        return redirect()->route('lomba.status', ['code' => $registration->registration_code])
            ->with('success', $message);
    }

    /**
     * API AJAX untuk membaca & memvalidasi file Excel Massal Kolektif (Live Preview)
     */
    public function parseMassExcelPeserta(Request $request, CompetitionSpreadsheetService $spreadsheetService)
    {
        $request->validate([
            'excel_file' => 'required|file|max:10240',
        ]);

        $file = $request->file('excel_file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['xlsx', 'xls', 'csv', 'txt'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format file harus berupa Excel (.xlsx) atau CSV (.csv).',
            ], 422);
        }

        try {
            $result = $spreadsheetService->parseMassParticipantFile($file);

            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca berkas Excel Massal: '.$e->getMessage(),
            ], 500);
        }
    }

    public function receipt($code)
    {
        if (strtoupper($code) === 'SBB-TOCSEA') {
            try {
                CompetitionRegistration::where('registration_code', 'SBB-TOCSEA')
                    ->update(['registration_code' => 'SBB-W54342H']);
            } catch (\Throwable $e) {
                // Ignore if already updated or connection issue
            }

            return redirect('/lomba/kwitansi/SBB-W54342H');
        }

        $registration = CompetitionRegistration::where('registration_code', $code)
            ->orWhere('id', $code)
            ->with(['event', 'teams.category'])
            ->firstOrFail();

        return view('pages.lomba.receipt', compact('registration'));
    }

    public function participantCards($code)
    {
        if (strtoupper($code) === 'SBB-TOCSEA') {
            try {
                CompetitionRegistration::where('registration_code', 'SBB-TOCSEA')
                    ->update(['registration_code' => 'SBB-W54342H']);
            } catch (\Throwable $e) {
                // Ignore if already updated or connection issue
            }

            return redirect('/lomba/kartu-peserta/SBB-W54342H');
        }

        $registration = CompetitionRegistration::where('registration_code', $code)
            ->orWhere('id', $code)
            ->with(['event', 'teams.category'])
            ->firstOrFail();

        return view('pages.lomba.cards', compact('registration'));
    }

    public function liveScoreboard(Request $request)
    {
        $event = CompetitionEvent::where('is_active', true)->first();
        if (! $event) {
            $event = CompetitionEvent::first();
        }

        $level = $request->query('level', 'Madya');
        $categoryId = $request->query('category_id');

        $categories = CompetitionCategory::where('competition_event_id', $event?->id)
            ->where('level', $level)
            ->where('is_active', true)
            ->orderBy('order_position')
            ->get();

        $selectedCategory = null;
        $scores = collect();

        if ($categoryId) {
            $selectedCategory = CompetitionCategory::find($categoryId);
        } elseif ($categories->isNotEmpty()) {
            $selectedCategory = $categories->first();
        }

        if ($selectedCategory) {
            if ($selectedCategory->has_rounds) {
                $round = $request->query('round');
                if ($round) {
                    $scores = CompetitionScore::where('competition_category_id', $selectedCategory->id)
                        ->where('round_name', $round)
                        ->with('team.registration')
                        ->orderBy('is_disqualified', 'asc')
                        ->orderByRaw('`rank` IS NULL, `rank` ASC')
                        ->orderBy('final_score', 'desc')
                        ->get();
                } else {
                    // Default untuk kategori berbabak: tampilkan pemenang resmi (Juara 1, 2, 3 Final + Harapan 1, 2 Semifinal)
                    $scores = $selectedCategory->getWinners();
                    if ($scores->isEmpty()) {
                        $scores = CompetitionScore::where('competition_category_id', $selectedCategory->id)
                            ->with('team.registration')
                            ->orderBy('is_disqualified', 'asc')
                            ->orderByRaw('`rank` IS NULL, `rank` ASC')
                            ->orderBy('final_score', 'desc')
                            ->get();
                    }
                }
            } else {
                $scores = CompetitionScore::where('competition_category_id', $selectedCategory->id)
                    ->with('team.registration')
                    ->orderBy('is_disqualified', 'asc')
                    ->orderByRaw('`rank` IS NULL, `rank` ASC')
                    ->orderBy('final_score', 'desc')
                    ->get();
            }
        }

        // Juara Umum Calculation per level
        $overallStandings = $this->calculateOverallStandings($event?->id, $level);

        return view('pages.lomba.scoreboard', compact('event', 'level', 'categories', 'selectedCategory', 'scores', 'overallStandings'));
    }

    private function calculateOverallStandings($eventId, $level)
    {
        if (! $eventId) {
            return collect();
        }

        $categories = CompetitionCategory::where('competition_event_id', $eventId)
            ->where('level', $level)
            ->where('is_active', true)
            ->get();

        $schoolPoints = [];

        foreach ($categories as $cat) {
            $topScores = $cat->getWinners();

            foreach ($topScores as $score) {
                $schoolName = $score->team?->registration?->school_name ?? $score->team?->team_name;
                // Remove team suffix (A), (B) for overall school accumulation
                $schoolNameClean = preg_replace('/\s*\([A-Za-z0-9\s]+\)$/', '', $schoolName);

                if (! isset($schoolPoints[$schoolNameClean])) {
                    $schoolPoints[$schoolNameClean] = [
                        'school_name' => $schoolNameClean,
                        'details' => [],
                        'total_points' => 0,
                    ];
                }

                $effectiveRank = $score->official_rank ?? $score->rank;
                $points = $cat->getPointsForRank($effectiveRank);

                $schoolPoints[$schoolNameClean]['details'][$cat->name.($cat->gender_category !== 'Umum' ? " {$cat->gender_category}" : '')] = $points;
                $schoolPoints[$schoolNameClean]['total_points'] += $points;
            }
        }

        return collect($schoolPoints)->sortByDesc('total_points')->values()->map(function ($item, $index) {
            $item['overall_rank'] = $index + 1;

            return $item;
        });
    }
}
