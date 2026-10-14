<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use SimpleXMLElement;
use ZipArchive;

class MemberSpreadsheetService
{
    /**
     * Column definition mapping with widths and alignment styles
     */
    public const COLUMNS = [
        'A' => ['key' => 'nri', 'label' => 'NRI', 'width' => 18, 'align' => 'center'],
        'B' => ['key' => 'name', 'label' => 'Nama Lengkap', 'width' => 30, 'align' => 'left'],
        'C' => ['key' => 'position', 'label' => 'Jabatan', 'width' => 24, 'align' => 'left'],
        'D' => ['key' => 'gender', 'label' => 'Jenis Kelamin', 'width' => 16, 'align' => 'center'],
        'E' => ['key' => 'class_grade', 'label' => 'Kelas / Tingkat', 'width' => 16, 'align' => 'center'],
        'F' => ['key' => 'entry_year', 'label' => 'Tahun Masuk', 'width' => 15, 'align' => 'center'],
        'G' => ['key' => 'member_status', 'label' => 'Status Anggota', 'width' => 18, 'align' => 'center'],
        'H' => ['key' => 'birth_place', 'label' => 'Tempat Lahir', 'width' => 20, 'align' => 'left'],
        'I' => ['key' => 'birth_date', 'label' => 'Tanggal Lahir', 'width' => 16, 'align' => 'center'],
        'J' => ['key' => 'email', 'label' => 'Email', 'width' => 28, 'align' => 'left'],
        'K' => ['key' => 'phone', 'label' => 'Nomor HP / WA', 'width' => 20, 'align' => 'left'],
        'L' => ['key' => 'address', 'label' => 'Alamat Lengkap', 'width' => 38, 'align' => 'left'],
        'M' => ['key' => 'motto', 'label' => 'Motto Hidup', 'width' => 32, 'align' => 'left'],
        'N' => ['key' => 'vision', 'label' => 'Visi', 'width' => 32, 'align' => 'left'],
        'O' => ['key' => 'mission', 'label' => 'Misi', 'width' => 32, 'align' => 'left'],
    ];

    /**
     * Generate XLSX file for Export
     */
    public function export(Collection $members): string
    {
        $headers = array_column(self::COLUMNS, 'label');
        $rows = [$headers];

        foreach ($members as $m) {
            $birthDateStr = $m->birth_date ? $m->birth_date->format('Y-m-d') : '';
            $rows[] = [
                (string) ($m->nri ?? $m->nis ?? ''),
                (string) ($m->name ?? ''),
                (string) ($m->position ?? 'Anggota'),
                (string) ($m->gender ?? ''),
                (string) ($m->class_grade ?? ''),
                (string) ($m->entry_year ?? ''),
                (string) ($m->effective_status ?? 'Aktif'),
                (string) ($m->birth_place ?? ''),
                $birthDateStr,
                (string) ($m->email ?? ''),
                (string) ($m->phone ?? ''),
                (string) ($m->address ?? ''),
                (string) ($m->motto ?? ''),
                (string) ($m->vision ?? ''),
                (string) ($m->mission ?? ''),
            ];
        }

        return $this->createXlsxFile($rows, 'Data Anggota PMR');
    }

    /**
     * Generate Template XLSX file with example rows and instructions
     */
    public function exportTemplate(): string
    {
        $headers = array_column(self::COLUMNS, 'label');
        $currentYear = (int) date('Y');

        $rows = [
            $headers,
            // Contoh baris 1: Anggota Aktif Kelas X
            [
                '2425001',
                'Contoh Anggota Baru',
                'Anggota',
                'Laki-laki',
                'X',
                (string) $currentYear,
                'Aktif',
                'Bogor',
                '2009-08-17',
                'anggota1@gmail.com',
                '081234567890',
                'Jl. Raya Ciawi No. 10, Bogor',
                'Berbakti untuk kemanusiaan',
                'Menjadi relawan tanggap dan peduli sesama',
                'Mengikuti seluruh pelatihan dasar kepalangmerahan',
            ],
            // Contoh baris 2: Anggota Purna (Tahun masuk 3 tahun lalu)
            [
                '2122045',
                'Contoh Alumni PMR',
                'Purna',
                'Perempuan',
                'Alumni',
                (string) ($currentYear - 3),
                'Purna',
                'Jakarta',
                '2006-05-12',
                'alumni@gmail.com',
                '089876543210',
                'Jl. Veteran III No. 45, Ciawi Bogor',
                'Sekali relawan, selamanya relawan',
                'Menjaga tali persaudaraan antar generasi PMR',
                'Membimbing adik-adik tingkat PMR Wira',
            ],
        ];

        return $this->createXlsxFile($rows, 'Template Import Anggota');
    }

    /**
     * Import members from an uploaded file (.xlsx or .csv)
     */
    public function import(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = [];

        if ($extension === 'csv' || $extension === 'txt') {
            $rows = $this->readCsv($file->getRealPath());
        } else {
            // Assume XLSX
            $rows = $this->readXlsx($file->getRealPath());
        }

        if (empty($rows)) {
            return [
                'success' => 0,
                'updated' => 0,
                'skipped' => 0,
                'total' => 0,
                'errors' => ['File Excel tidak berisi data atau format file tidak valid.'],
            ];
        }

        // Header check (skip row 1)
        $headerRow = array_shift($rows);

        // Build dynamic column mapping from header text
        $colMap = $this->buildHeaderColumnMapping($headerRow);

        $successCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $seenNriInFile = [];
        $currentYear = (int) date('Y');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // 1-indexed Excel row (header is 1)

            // Extract values using dynamic column mapping (with positional fallback)
            $nri = $this->getMappedVal($row, $colMap, 'nri', 0) ?: $this->getMappedVal($row, $colMap, 'nis', 0);
            $name = $this->getMappedVal($row, $colMap, 'name', 1);
            $position = $this->getMappedVal($row, $colMap, 'position', 2);
            $gender = $this->getMappedVal($row, $colMap, 'gender', 3);
            $classGrade = $this->getMappedVal($row, $colMap, 'class_grade', 4);
            $entryYearRaw = $this->getMappedVal($row, $colMap, 'entry_year', 5);
            $memberStatus = $this->getMappedVal($row, $colMap, 'member_status', 6);
            $birthPlace = $this->getMappedVal($row, $colMap, 'birth_place', 7);
            $birthDateRaw = $this->getMappedVal($row, $colMap, 'birth_date', 8);
            $email = $this->getMappedVal($row, $colMap, 'email', 9);
            $phone = $this->getMappedVal($row, $colMap, 'phone', 10);
            $address = $this->getMappedVal($row, $colMap, 'address', 11);
            $motto = $this->getMappedVal($row, $colMap, 'motto', 12);
            $vision = $this->getMappedVal($row, $colMap, 'vision', 13);
            $mission = $this->getMappedVal($row, $colMap, 'mission', 14);

            // Skip completely empty rows
            if (empty($name) && empty($nri)) {
                $skippedCount++;

                continue;
            }

            if (empty($name)) {
                $errors[] = "Baris {$rowNumber}: Nama Lengkap wajib diisi (dilewati).";
                $skippedCount++;

                continue;
            }

            // Validasi NRI: 1 anggota wajib 1 NRI dan tidak boleh kosong
            if (empty($nri)) {
                $errors[] = "Baris {$rowNumber} ({$name}): NRI wajib diisi (1 anggota 1 NRI tidak boleh kosong).";
                $skippedCount++;

                continue;
            }

            // Validasi Duplikasi NRI di dalam file Excel yang sama
            if (isset($seenNriInFile[$nri])) {
                $errors[] = "Baris {$rowNumber} ({$name}): NRI '{$nri}' duplikat dengan baris {$seenNriInFile[$nri]} dalam file (dilewati).";
                $skippedCount++;

                continue;
            }
            $seenNriInFile[$nri] = $rowNumber;

            // Bungkus proses penyimpanan per baris agar kesalahan satu baris tidak menggugurkan baris lainnya
            try {
                // Normalisasi Gender
                if ($gender) {
                    $genderLower = strtolower($gender);
                    if (str_contains($genderLower, 'laki') || $genderLower === 'l' || $genderLower === 'pria') {
                        $gender = 'Laki-laki';
                    } elseif (str_contains($genderLower, 'perempuan') || $genderLower === 'p' || $genderLower === 'wanita') {
                        $gender = 'Perempuan';
                    } else {
                        $gender = null;
                    }
                }

                // Normalisasi Kelas
                if ($classGrade) {
                    $cgUpper = strtoupper(trim($classGrade));
                    if (in_array($cgUpper, ['X', '10', 'SEPULUH'])) {
                        $classGrade = 'X';
                    } elseif (in_array($cgUpper, ['XI', '11', 'SEBELAS'])) {
                        $classGrade = 'XI';
                    } elseif (in_array($cgUpper, ['XII', '12', 'DUA BELAS'])) {
                        $classGrade = 'XII';
                    } elseif (str_contains(strtolower($classGrade), 'alumni')) {
                        $classGrade = 'Alumni';
                    }
                }

                // Normalisasi Tahun Masuk
                $entryYear = null;
                if (! empty($entryYearRaw) && is_numeric($entryYearRaw)) {
                    $entryYear = (int) $entryYearRaw;
                }

                // Normalisasi Status Anggota
                if (! empty($memberStatus)) {
                    $statusLower = strtolower($memberStatus);
                    if (str_contains($statusLower, 'purna') || str_contains($statusLower, 'alumni')) {
                        $memberStatus = 'Purna';
                    } else {
                        $memberStatus = 'Aktif';
                    }
                } else {
                    // Logika Otomatis: jika tahun masuk + 3 <= tahun sekarang -> Purna
                    if ($entryYear) {
                        $memberStatus = ($currentYear >= ($entryYear + 3)) ? 'Purna' : 'Aktif';
                    } else {
                        $memberStatus = 'Aktif';
                    }
                }

                // Normalisasi Tanggal Lahir
                $birthDate = null;
                if (! empty($birthDateRaw)) {
                    try {
                        // Cek jika numeric Excel date serial number (misal 39582)
                        if (is_numeric($birthDateRaw) && (int) $birthDateRaw > 10000 && (int) $birthDateRaw < 60000) {
                            $timestamp = ((int) $birthDateRaw - 25569) * 86400;
                            $birthDate = gmdate('Y-m-d', $timestamp);
                        } else {
                            $parsedDate = date('Y-m-d', strtotime(str_replace('/', '-', $birthDateRaw)));
                            if ($parsedDate && $parsedDate !== '1970-01-01') {
                                $birthDate = $parsedDate;
                            }
                        }
                    } catch (\Throwable $e) {
                        $birthDate = null;
                    }
                }

                $hasNri = Schema::hasColumn('members', 'nri');
                $hasNis = Schema::hasColumn('members', 'nis');
                $hasEntryYear = Schema::hasColumn('members', 'entry_year');
                $hasMemberStatus = Schema::hasColumn('members', 'member_status');

                if (! empty($position)) {
                    $position = preg_replace('/kepala\s+bidang/i', 'Ketua Bidang', $position);
                    $position = preg_replace('/\bkabid\b/i', 'Ketua Bidang', $position);
                }

                $memberData = [
                    'name' => $name,
                    'position' => $position ?: 'Anggota',
                    'gender' => $gender,
                    'class_grade' => $classGrade,
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'motto' => $motto,
                    'vision' => $vision,
                    'mission' => $mission,
                    'is_active' => true,
                ];

                if ($hasNri) {
                    $memberData['nri'] = $nri;
                }
                if ($hasNis) {
                    $memberData['nis'] = $nri;
                }
                if ($hasEntryYear) {
                    $memberData['entry_year'] = $entryYear;
                }
                if ($hasMemberStatus) {
                    $memberData['member_status'] = $memberStatus;
                }

                // 1. Cek apakah NRI unik sudah ada di database (Update data jika ada)
                $existingMember = Member::where(function ($q) use ($nri, $hasNri, $hasNis) {
                    if ($hasNri) {
                        $q->where('nri', $nri);
                    }
                    if ($hasNis) {
                        if ($hasNri) {
                            $q->orWhere('nis', $nri);
                        } else {
                            $q->where('nis', $nri);
                        }
                    }
                })->first();

                if ($existingMember) {
                    $existingMember->update($memberData);
                    $updatedCount++;

                    continue;
                }

                // 2. Pemulihan otomatis (Self-Healing): Cek jika ada anggota dengan nama sama tapi NRI/NIS korup ('Laki-laki' / 'Perempuan') akibat bug versi sebelumnya
                $corruptedMember = Member::where('name', $name)->where(function ($q) use ($hasNri, $hasNis) {
                    if ($hasNri) {
                        $q->whereIn('nri', ['Laki-laki', 'Perempuan']);
                    }
                    if ($hasNis) {
                        if ($hasNri) {
                            $q->orWhereIn('nis', ['Laki-laki', 'Perempuan']);
                        } else {
                            $q->whereIn('nis', ['Laki-laki', 'Perempuan']);
                        }
                    }
                })->first();

                if ($corruptedMember) {
                    $corruptedMember->update($memberData);
                    $updatedCount++;

                    continue;
                }

                // 3. Tambah anggota baru jika NRI belum pernah terdaftar
                Member::create($memberData);
                $successCount++;
            } catch (\Throwable $e) {
                $errors[] = "Baris {$rowNumber} ({$name}): ".$e->getMessage();
                $skippedCount++;
            }
        }

        return [
            'success' => $successCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'total' => $successCount + $updatedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Build column index map by matching header names
     */
    protected function buildHeaderColumnMapping(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $idx => $cellText) {
            $h = strtolower(trim((string) $cellText));
            if (empty($h)) {
                continue;
            }

            // Prioritaskan NRI / NIS: pastikan BUKAN kata 'jenis' (seperti 'jenis kelamin')
            if (! str_contains($h, 'jenis') && (preg_match('/\b(nri|nis)\b/i', $h) || str_contains($h, 'registrasi') || str_contains($h, 'induk') || $h === 'nri' || $h === 'nis')) {
                $map['nri'] = $idx;
                $map['nis'] = $idx;
            } elseif (str_contains($h, 'nama')) {
                $map['name'] = $idx;
            } elseif (str_contains($h, 'jabatan') || str_contains($h, 'posisi')) {
                $map['position'] = $idx;
            } elseif (str_contains($h, 'kelamin') || str_contains($h, 'gender') || str_contains($h, 'jenis')) {
                $map['gender'] = $idx;
            } elseif (str_contains($h, 'kelas') || str_contains($h, 'tingkat')) {
                $map['class_grade'] = $idx;
            } elseif (str_contains($h, 'tahun') || str_contains($h, 'masuk')) {
                $map['entry_year'] = $idx;
            } elseif (str_contains($h, 'status')) {
                $map['member_status'] = $idx;
            } elseif (str_contains($h, 'tempat')) {
                $map['birth_place'] = $idx;
            } elseif (str_contains($h, 'tanggal') || str_contains($h, 'tgl') || (str_contains($h, 'lahir') && ! str_contains($h, 'tempat'))) {
                $map['birth_date'] = $idx;
            } elseif (str_contains($h, 'email')) {
                $map['email'] = $idx;
            } elseif (str_contains($h, 'hp') || str_contains($h, 'wa') || str_contains($h, 'telepon') || str_contains($h, 'kontak') || str_contains($h, 'phone')) {
                $map['phone'] = $idx;
            } elseif (str_contains($h, 'alamat')) {
                $map['address'] = $idx;
            } elseif (str_contains($h, 'motto')) {
                $map['motto'] = $idx;
            } elseif (str_contains($h, 'visi')) {
                $map['vision'] = $idx;
            } elseif (str_contains($h, 'misi')) {
                $map['mission'] = $idx;
            }
        }

        return $map;
    }

    /**
     * Get value from row using mapped index or default position
     */
    protected function getMappedVal(array $row, array $colMap, string $key, int $defaultIdx): ?string
    {
        $idx = $colMap[$key] ?? $defaultIdx;
        if (isset($row[$idx])) {
            $val = trim((string) $row[$idx]);

            return $val !== '' ? $val : null;
        }

        return null;
    }

    /**
     * Read XLSX file rows using ZipArchive & SimpleXMLElement
     */
    protected function readXlsx(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Read Shared Strings (if available)
        $sharedStrings = [];
        if ($sstXml = $zip->getFromName('xl/sharedStrings.xml')) {
            $xml = @simplexml_load_string($sstXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) ($r->t ?? '');
                        }
                        $sharedStrings[] = $text;
                    } elseif (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } else {
                        $sharedStrings[] = trim((string) $si);
                    }
                }
            }
        }

        // 2. Read Sheet 1 XML
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (! $sheetXmlContent) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (str_starts_with($filename, 'xl/worksheets/sheet') && str_ends_with($filename, '.xml')) {
                    $sheetXmlContent = $zip->getFromName($filename);
                    break;
                }
            }
        }

        $zip->close();

        if (! $sheetXmlContent) {
            return [];
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (! $sheetXml || ! isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowCells = [];
            $nextColIdx = 0;
            foreach ($row->c as $c) {
                $ref = (string) ($c['r'] ?? '');
                if ($ref !== '') {
                    $colIdx = $this->columnRefToIndex($ref);
                    $nextColIdx = $colIdx + 1;
                } else {
                    $colIdx = $nextColIdx++;
                }

                $type = (string) ($c['t'] ?? '');
                $val = '';

                if ($type === 's') {
                    $sstIndex = (int) $c->v;
                    $val = $sharedStrings[$sstIndex] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string) ($c->is->t ?? '');
                } else {
                    $val = (string) ($c->v ?? '');
                }

                $rowCells[$colIdx] = trim($val);
            }

            if (! empty($rowCells)) {
                $maxCol = max(array_keys($rowCells));
                $normalizedRow = [];
                for ($col = 0; $col <= $maxCol; $col++) {
                    $normalizedRow[$col] = $rowCells[$col] ?? '';
                }
                $rows[] = $normalizedRow;
            }
        }

        return $rows;
    }

    /**
     * Read CSV file rows
     */
    protected function readCsv(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [];
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        while (($data = fgetcsv($handle, 4096, $delimiter)) !== false) {
            $rows[] = array_map('trim', $data);
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Helper to create a beautifully styled XLSX file via ZipArchive
     */
    protected function createXlsxFile(array $rows, string $sheetTitle = 'Data'): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pmr_xlsx_');
        $zip = new ZipArchive;
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // 1. Content types
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. Package relationships
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. Workbook relationships
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. Workbook definition
        $safeSheetTitle = htmlspecialchars(substr($sheetTitle, 0, 31), ENT_XML1);
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets><sheet name="'.$safeSheetTitle.'" sheetId="1" r:id="rId1"/></sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. Professional Styles definition
        // Style 0: Default
        // Style 1: Header (Merah PMR #B91C1C, Font Putih Bold 11pt, Center, Thin Border)
        // Style 2: Data Center (Font Calibri 10pt, Center, Thin Border)
        // Style 3: Data Left (Font Calibri 10pt, Left, Thin Border)
        // Style 4: Data Center Zebra Light (#F8FAFC, Center, Thin Border)
        // Style 5: Data Left Zebra Light (#F8FAFC, Left, Thin Border)
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<fonts count="2">
<font><name val="Calibri"/><sz val="10"/><color rgb="FF0F172A"/></font>
<font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFFFF"/></font>
</fonts>
<fills count="4">
<fill><patternFill patternType="none"/></fill>
<fill><patternFill patternType="gray125"/></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FFB91C1C"/></patternFill></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
</fills>
<borders count="2">
<border><left/><right/><top/><bottom/></border>
<border>
<left style="thin"><color rgb="FFCBD5E1"/></left>
<right style="thin"><color rgb="FFCBD5E1"/></right>
<top style="thin"><color rgb="FFCBD5E1"/></top>
<bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
</border>
</borders>
<cellStyleXfs count="1">
<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
</cellStyleXfs>
<cellXfs count="6">
<!-- 0: Default -->
<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>
<!-- 1: Header (Red, Bold, Center) -->
<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
<alignment horizontal="center" vertical="center" wrapText="1"/>
</xf>
<!-- 2: Data Center -->
<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">
<alignment horizontal="center" vertical="center"/>
</xf>
<!-- 3: Data Left -->
<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">
<alignment horizontal="left" vertical="center"/>
</xf>
<!-- 4: Data Center Zebra -->
<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
<alignment horizontal="center" vertical="center"/>
</xf>
<!-- 5: Data Left Zebra -->
<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">
<alignment horizontal="left" vertical="center"/>
</xf>
</cellXfs>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. Column Widths Definition (<cols>)
        $colsXml = '<cols>';
        $colIdx = 1;
        foreach (self::COLUMNS as $colMeta) {
            $w = $colMeta['width'] ?? 20;
            $colsXml .= '<col min="'.$colIdx.'" max="'.$colIdx.'" width="'.$w.'" customWidth="1"/>';
            $colIdx++;
        }
        $colsXml .= '</cols>';

        // 7. Sheet Data construction
        $alignMap = array_values(array_column(self::COLUMNS, 'align'));

        $sheetDataXml = '<sheetData>';
        foreach ($rows as $rowIndex => $cols) {
            $rNum = $rowIndex + 1;
            $isHeader = ($rNum === 1);
            $rowHeight = $isHeader ? ' ht="28" customHeight="1"' : ' ht="22" customHeight="1"';
            $isZebra = (! $isHeader && $rNum % 2 === 1);

            $sheetDataXml .= '<row r="'.$rNum.'"'.$rowHeight.'>';
            foreach ($cols as $colIndex => $val) {
                $colLetter = $this->indexToColumnLetter($colIndex);
                $cellRef = $colLetter.$rNum;

                if ($isHeader) {
                    $styleId = 1; // Header red
                } else {
                    $align = $alignMap[$colIndex] ?? 'left';
                    if ($align === 'center') {
                        $styleId = $isZebra ? 4 : 2;
                    } else {
                        $styleId = $isZebra ? 5 : 3;
                    }
                }

                $styleAttr = ' s="'.$styleId.'"';
                $safeVal = htmlspecialchars((string) $val, ENT_XML1);
                $sheetDataXml .= '<c r="'.$cellRef.'"'.$styleAttr.' t="inlineStr"><is><t>'.$safeVal.'</t></is></c>';
            }
            $sheetDataXml .= '</row>';
        }
        $sheetDataXml .= '</sheetData>';

        // 8. Worksheet XML with Frozen Header Pane & Custom Column Widths
        $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews>
<sheetView tabSelected="1" workbookViewId="0">
<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>
</sheetView>
</sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
</worksheet>';
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);

        $zip->close();

        return $tempFile;
    }

    /**
     * Convert cell ref like "C12" to 0-based column index (2)
     */
    protected function columnRefToIndex(string $ref): int
    {
        preg_match('/([A-Z]+)/', strtoupper($ref), $matches);
        $letters = $matches[1] ?? 'A';
        $num = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($letters[$i]) - ord('A') + 1);
        }

        return max(0, $num - 1);
    }

    /**
     * Convert 0-based index to column letters (0 -> A, 27 -> AB)
     */
    protected function indexToColumnLetter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = intval(($index - $mod) / 26);
        }

        return $letter;
    }
}
