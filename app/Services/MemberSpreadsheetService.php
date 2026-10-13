<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use ZipArchive;
use SimpleXMLElement;

class MemberSpreadsheetService
{
    /**
     * Column definition mapping
     */
    public const COLUMNS = [
        'A' => ['key' => 'nis', 'label' => 'NIS', 'required' => false],
        'B' => ['key' => 'name', 'label' => 'Nama Lengkap', 'required' => true],
        'C' => ['key' => 'position', 'label' => 'Jabatan', 'required' => false],
        'D' => ['key' => 'gender', 'label' => 'Jenis Kelamin (Laki-laki / Perempuan)', 'required' => false],
        'E' => ['key' => 'class_grade', 'label' => 'Kelas / Tingkat (X / XI / XII / Alumni)', 'required' => false],
        'F' => ['key' => 'entry_year', 'label' => 'Tahun Masuk (contoh: 2024)', 'required' => false],
        'G' => ['key' => 'member_status', 'label' => 'Status Anggota (Aktif / Purna / Kosongkan untuk Otomatis)', 'required' => false],
        'H' => ['key' => 'birth_place', 'label' => 'Tempat Lahir', 'required' => false],
        'I' => ['key' => 'birth_date', 'label' => 'Tanggal Lahir (YYYY-MM-DD)', 'required' => false],
        'J' => ['key' => 'email', 'label' => 'Email', 'required' => false],
        'K' => ['key' => 'phone', 'label' => 'Nomor HP / WA', 'required' => false],
        'L' => ['key' => 'address', 'label' => 'Alamat Lengkap', 'required' => false],
        'M' => ['key' => 'motto', 'label' => 'Motto Hidup', 'required' => false],
        'N' => ['key' => 'vision', 'label' => 'Visi', 'required' => false],
        'O' => ['key' => 'mission', 'label' => 'Misi', 'required' => false],
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
                (string) ($m->nis ?? ''),
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

        $successCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errors = [];
        $currentYear = (int) date('Y');

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // 1-indexed Excel row (header is 1)

            // Extract columns by position (matching COLUMNS order)
            $nis = isset($row[0]) ? trim((string) $row[0]) : null;
            $name = isset($row[1]) ? trim((string) $row[1]) : '';
            $position = isset($row[2]) ? trim((string) $row[2]) : null;
            $gender = isset($row[3]) ? trim((string) $row[3]) : null;
            $classGrade = isset($row[4]) ? trim((string) $row[4]) : null;
            $entryYearRaw = isset($row[5]) ? trim((string) $row[5]) : null;
            $memberStatus = isset($row[6]) ? trim((string) $row[6]) : null;
            $birthPlace = isset($row[7]) ? trim((string) $row[7]) : null;
            $birthDateRaw = isset($row[8]) ? trim((string) $row[8]) : null;
            $email = isset($row[9]) ? trim((string) $row[9]) : null;
            $phone = isset($row[10]) ? trim((string) $row[10]) : null;
            $address = isset($row[11]) ? trim((string) $row[11]) : null;
            $motto = isset($row[12]) ? trim((string) $row[12]) : null;
            $vision = isset($row[13]) ? trim((string) $row[13]) : null;
            $mission = isset($row[14]) ? trim((string) $row[14]) : null;

            // Skip completely empty rows
            if (empty($name) && empty($nis)) {
                $skippedCount++;
                continue;
            }

            if (empty($name)) {
                $errors[] = "Baris {$rowNumber}: Nama Lengkap wajib diisi (dilewati).";
                $skippedCount++;
                continue;
            }

            // Normalisasi Gender
            if ($gender) {
                $genderLower = strtolower($gender);
                if (str_contains($genderLower, 'laki') || $genderLower === 'l' || $genderLower === 'pria') {
                    $gender = 'Laki-laki';
                } elseif (str_contains($genderLower, 'perempuan') || $genderLower === 'p' || $genderLower === 'wanita') {
                    $gender = 'Perempuan';
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
            if (!empty($entryYearRaw) && is_numeric($entryYearRaw)) {
                $entryYear = (int) $entryYearRaw;
            }

            // Normalisasi Status Anggota
            if (!empty($memberStatus)) {
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
            if (!empty($birthDateRaw)) {
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

            $memberData = [
                'name' => $name,
                'position' => $position ?: 'Anggota',
                'gender' => $gender,
                'class_grade' => $classGrade,
                'entry_year' => $entryYear,
                'member_status' => $memberStatus,
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

            // Cek apakah NIS sudah ada untuk update atau insert
            if (!empty($nis)) {
                $existingMember = Member::where('nis', $nis)->first();
                if ($existingMember) {
                    $existingMember->update($memberData);
                    $updatedCount++;
                    continue;
                }
                $memberData['nis'] = $nis;
            } else {
                // Jika tanpa NIS, cari berdasarkan nama sama persis jika ada
                $existingByName = Member::where('name', $name)->whereNull('nis')->first();
                if ($existingByName) {
                    $existingByName->update($memberData);
                    $updatedCount++;
                    continue;
                }
            }

            Member::create($memberData);
            $successCount++;
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
     * Read XLSX file rows using ZipArchive & SimpleXMLElement
     */
    protected function readXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // 1. Read Shared Strings (if available)
        $sharedStrings = [];
        if ($sstXml = $zip->getFromName('xl/sharedStrings.xml')) {
            $xml = @simplexml_load_string($sstXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read Sheet 1 XML
        $sheetXmlContent = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXmlContent) {
            // Coba cari nama sheet pertama
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (str_starts_with($filename, 'xl/worksheets/sheet') && str_ends_with($filename, '.xml')) {
                    $sheetXmlContent = $zip->getFromName($filename);
                    break;
                }
            }
        }

        $zip->close();

        if (!$sheetXmlContent) {
            return [];
        }

        $sheetXml = @simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowCells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $colIdx = $this->columnRefToIndex($ref);
                $type = (string) $c['t'];
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

            if (!empty($rowCells)) {
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
        if (!$handle) {
            return [];
        }

        // Read first line to detect delimiter (, or ;)
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
     * Helper to create a valid XLSX file via ZipArchive
     */
    protected function createXlsxFile(array $rows, string $sheetTitle = 'Data'): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pmr_xlsx_');
        $zip = new ZipArchive();
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Content types
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // Package relationships
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // Workbook relationships
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // Workbook definition
        $safeSheetTitle = htmlspecialchars(substr($sheetTitle, 0, 31), ENT_XML1);
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets><sheet name="' . $safeSheetTitle . '" sheetId="1" r:id="rId1"/></sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // Styles definition (Basic clean font and header background)
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<fonts count="2">
<font><name val="Calibri"/><sz val="11"/></font>
<font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFFFF"/></font>
</fonts>
<fills count="3">
<fill><patternFill patternType="none"/></fill>
<fill><patternFill patternType="gray125"/></fill>
<fill><patternFill patternType="solid"><fgColor rgb="FF991B1B"/></patternFill></fill>
</fills>
<borders count="1">
<border><left/><right/><top/><bottom/></border>
</borders>
<cellStyleXfs count="1">
<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
</cellStyleXfs>
<cellXfs count="2">
<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/>
</cellXfs>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // Sheet Data construction
        $sheetDataXml = '<sheetData>';
        foreach ($rows as $rowIndex => $cols) {
            $rNum = $rowIndex + 1;
            $isHeader = ($rNum === 1);
            $sheetDataXml .= '<row r="' . $rNum . '">';
            foreach ($cols as $colIndex => $val) {
                $colLetter = $this->indexToColumnLetter($colIndex);
                $cellRef = $colLetter . $rNum;
                $styleAttr = $isHeader ? ' s="1"' : '';

                if (is_numeric($val) && strlen((string)$val) < 11 && !str_starts_with((string)$val, '0')) {
                    $sheetDataXml .= '<c r="' . $cellRef . '"' . $styleAttr . '><v>' . $val . '</v></c>';
                } else {
                    $safeVal = htmlspecialchars((string) $val, ENT_XML1);
                    $sheetDataXml .= '<c r="' . $cellRef . '"' . $styleAttr . ' t="inlineStr"><is><t>' . $safeVal . '</t></is></c>';
                }
            }
            $sheetDataXml .= '</row>';
        }
        $sheetDataXml .= '</sheetData>';

        $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
' . $sheetDataXml . '
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
            $letter = chr(65 + $mod) . $letter;
            $index = intval(($index - $mod) / 26);
        }
        return $letter;
    }
}
