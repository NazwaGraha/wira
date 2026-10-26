<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use ZipArchive;

class CompetitionSpreadsheetService
{
    /**
     * Definisi kolom template Excel peserta regu lomba PMR
     */
    public const COLUMNS = [
        'A' => ['key' => 'no', 'label' => 'No', 'width' => 8, 'align' => 'center'],
        'B' => ['key' => 'name', 'label' => 'Nama Lengkap Peserta *', 'width' => 34, 'align' => 'left'],
        'C' => ['key' => 'gender', 'label' => 'Jenis Kelamin (L/P)', 'width' => 20, 'align' => 'center'],
        'D' => ['key' => 'nisn', 'label' => 'NISN / NIS', 'width' => 20, 'align' => 'center'],
        'E' => ['key' => 'class_grade', 'label' => 'Kelas / Tingkat', 'width' => 18, 'align' => 'center'],
        'F' => ['key' => 'role', 'label' => 'Jabatan / Peran dalam Regu', 'width' => 28, 'align' => 'left'],
    ];

    /**
     * Definisi 12 kolom template Excel Massal Kolektif (1 File Seluruh Regu)
     */
    public const MASS_COLUMNS = [
        'A' => ['key' => 'no', 'label' => 'No', 'width' => 6, 'align' => 'center'],
        'B' => ['key' => 'registration_code', 'label' => 'No. Pendaftaran *', 'width' => 20, 'align' => 'center'],
        'C' => ['key' => 'level', 'label' => 'Tingkatan *', 'width' => 16, 'align' => 'center'],
        'D' => ['key' => 'school_name', 'label' => 'Nama Sekolah *', 'width' => 28, 'align' => 'left'],
        'E' => ['key' => 'category_name', 'label' => 'Cabang Lomba *', 'width' => 32, 'align' => 'left'],
        'F' => ['key' => 'gender_category', 'label' => 'Kategori *', 'width' => 14, 'align' => 'center'],
        'G' => ['key' => 'team_label', 'label' => 'Nama Regu *', 'width' => 16, 'align' => 'center'],
        'H' => ['key' => 'nisn', 'label' => 'NISN / NIS', 'width' => 18, 'align' => 'center'],
        'I' => ['key' => 'name', 'label' => 'Nama Lengkap Peserta *', 'width' => 34, 'align' => 'left'],
        'J' => ['key' => 'gender', 'label' => 'L / P', 'width' => 10, 'align' => 'center'],
        'K' => ['key' => 'class_grade', 'label' => 'Kelas', 'width' => 14, 'align' => 'center'],
        'L' => ['key' => 'role', 'label' => 'Jabatan / Peran *', 'width' => 22, 'align' => 'left'],
    ];

    /**
     * Contoh data template peserta
     */
    public const SAMPLE_ROWS = [
        ['1', 'Ahmad Fauzi Pratama', 'L', '0081234567', 'VIII-A', 'Ketua Regu'],
        ['2', 'Siti Rahma Azzahra', 'P', '0087654321', 'VIII-B', 'Anggota'],
        ['3', 'Fajar Nugraha', 'L', '0089123456', 'VII-A', 'Anggota'],
        ['4', 'Nabila Rahmadani', 'P', '0082345678', 'VIII-C', 'Anggota'],
        ['5', 'Dimas Aditya Saputra', 'L', '0083456789', 'VII-B', 'Anggota'],
        ['6', 'Rizky Alamsyah', 'L', '0084567890', 'VII-C', 'Cadangan'],
    ];

    /**
     * Hasilkan file Excel XLSX template daftar peserta regu
     */
    public function downloadTemplate(?string $categoryName = null): string
    {
        $headers = array_column(self::COLUMNS, 'label');
        $rows = [$headers];

        foreach (self::SAMPLE_ROWS as $sample) {
            $rows[] = $sample;
        }

        return $this->createXlsxFile($rows, 'Daftar Peserta Regu');
    }

    /**
     * Parse file Excel (.xlsx, .xls, .csv) dan ekstrak daftar peserta
     *
     * @param  UploadedFile|string  $file
     * @return array<int, array<string, string>>
     */
    public function parseParticipantFile($file): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $extension = $file instanceof UploadedFile
            ? strtolower($file->getClientOriginalExtension())
            : strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $rows = [];
        if (in_array($extension, ['csv', 'txt'])) {
            $rows = $this->readCsv($filePath);
        } else {
            // Coba XLSX terlebih dahulu
            $rows = $this->readXlsx($filePath);

            // Fallback ke CSV jika file sebenarnya adalah CSV/teks yang dinamai .xls
            if (empty($rows)) {
                $rows = $this->readCsv($filePath);
            }
        }

        if (empty($rows)) {
            return [];
        }

        // Cari baris header yang mengandung kata 'nama' atau 'peserta'
        $headerRowIdx = null;
        $colMap = [];

        foreach ($rows as $idx => $row) {
            $rowLower = array_map(fn ($cell) => strtolower(trim((string) $cell)), $row);
            foreach ($rowLower as $cIdx => $cellText) {
                if (str_contains($cellText, 'nama') || str_contains($cellText, 'peserta') || str_contains($cellText, 'anggota')) {
                    $headerRowIdx = $idx;
                    break 2;
                }
            }
        }

        // Jika tidak ditemukan kata header, asumsikan baris 0 adalah header jika kolom B berisi string
        if ($headerRowIdx === null && count($rows) > 1) {
            $headerRowIdx = 0;
        }

        if ($headerRowIdx !== null) {
            $headerRow = $rows[$headerRowIdx];
            $colMap = $this->buildHeaderColumnMapping($headerRow);
            $dataRows = array_slice($rows, $headerRowIdx + 1);
        } else {
            $dataRows = $rows;
            $colMap = [
                'no' => 0,
                'name' => 1,
                'gender' => 2,
                'nisn' => 3,
                'class_grade' => 4,
                'role' => 5,
            ];
        }

        $participants = [];
        $sampleNames = array_map(fn ($s) => strtolower(trim($s[1])), self::SAMPLE_ROWS);

        foreach ($dataRows as $row) {
            if (empty($row) || ! is_array($row)) {
                continue;
            }

            $name = $this->getMappedVal($row, $colMap, 'name', 1);

            // Lewati baris kosong atau baris judul/catatan
            if (! $name || strlen(trim($name)) < 2) {
                continue;
            }

            // Bersihkan nama dari whitespace & angka di depan (misal: "1. Ahmad")
            $cleanName = preg_replace('/^\d+[\.\)\-\s]+/', '', trim($name));
            if (strlen($cleanName) < 2) {
                continue;
            }

            // Lewati jika masih merupakan baris header yang terulang
            $lowerName = strtolower($cleanName);
            if (in_array($lowerName, ['nama lengkap', 'nama peserta', 'nama anggota', 'nama lengkap peserta *', 'nama lengkap peserta', 'nama'])) {
                continue;
            }

            // Catatan: Jika pengguna mengunggah template tanpa mengubah data contoh, kita tetap izinkan atau proses
            $gender = $this->getMappedVal($row, $colMap, 'gender', 2);
            $nisn = $this->getMappedVal($row, $colMap, 'nisn', 3);
            $classGrade = $this->getMappedVal($row, $colMap, 'class_grade', 4);
            $role = $this->getMappedVal($row, $colMap, 'role', 5);

            // Normalisasi jenis kelamin
            $genderClean = '';
            if ($gender) {
                $gUpper = strtoupper(trim($gender));
                if (str_starts_with($gUpper, 'L') || str_contains($gUpper, 'PUTRA') || str_contains($gUpper, 'LAKI')) {
                    $genderClean = 'L';
                } elseif (str_starts_with($gUpper, 'P') || str_contains($gUpper, 'PUTRI') || str_contains($gUpper, 'PEREMPUAN')) {
                    $genderClean = 'P';
                } else {
                    $genderClean = substr($gUpper, 0, 1);
                }
            }

            $participants[] = [
                'name' => ucwords(strtolower($cleanName)),
                'gender' => $genderClean,
                'nisn' => $nisn ? trim($nisn) : null,
                'class_grade' => $classGrade ? trim($classGrade) : null,
                'role' => $role ? ucwords(strtolower(trim($role))) : 'Anggota',
            ];
        }

        return $participants;
    }

    /**
     * Bangun pemetaan kolom berdasarkan teks header
     */
    protected function buildHeaderColumnMapping(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $idx => $cell) {
            $t = strtolower(trim((string) $cell));

            if ($t === 'no' || $t === 'nomor' || $t === '#') {
                $map['no'] = $idx;
            } elseif (str_contains($t, 'nama')) {
                $map['name'] = $idx;
            } elseif (str_contains($t, 'kelamin') || str_contains($t, 'gender') || $t === 'jk' || $t === 'l/p') {
                $map['gender'] = $idx;
            } elseif (str_contains($t, 'nisn') || str_contains($t, 'nis')) {
                $map['nisn'] = $idx;
            } elseif (str_contains($t, 'kelas') || str_contains($t, 'tingkat')) {
                $map['class_grade'] = $idx;
            } elseif (str_contains($t, 'peran') || str_contains($t, 'jabatan') || str_contains($t, 'tugas') || str_contains($t, 'posisi')) {
                $map['role'] = $idx;
            }
        }

        // Pastikan 'name' selalu ada pemetaan
        if (! isset($map['name'])) {
            // Cari kolom pertama yang bertipe string dan bukan nomor urut
            foreach ($headerRow as $idx => $cell) {
                if ($idx !== ($map['no'] ?? -1)) {
                    $map['name'] = $idx;
                    break;
                }
            }
        }

        return $map;
    }

    protected function getMappedVal(array $row, array $colMap, string $key, int $defaultIdx): ?string
    {
        $idx = $colMap[$key] ?? $defaultIdx;
        if (isset($row[$idx]) && trim((string) $row[$idx]) !== '') {
            return trim((string) $row[$idx]);
        }

        return null;
    }

    /**
     * Baca file XLSX via ZipArchive & OpenXML murni
     */
    protected function readXlsx(string $filePath): array
    {
        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // Shared strings
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

        // Sheet 1 XML
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
     * Baca file CSV
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
     * Helper membuat file XLSX dengan format cantik OpenXML
     */
    protected function createXlsxFile(array $rows, string $sheetTitle = 'Peserta'): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pmr_regu_xlsx_');
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

        // 4. Workbook
        $safeSheetTitle = htmlspecialchars(substr($sheetTitle, 0, 31), ENT_XML1);
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets><sheet name="'.$safeSheetTitle.'" sheetId="1" r:id="rId1"/></sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. Styles (Merah PMR #B91C1C, Font Putih Bold)
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<fonts count="3">
<font><name val="Calibri"/><sz val="10"/><color rgb="FF0F172A"/></font>
<font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFFFF"/></font>
<font><i/><name val="Calibri"/><sz val="9.5"/><color rgb="FF64748B"/></font>
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
<xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1">
<alignment horizontal="left" vertical="center"/>
</xf>
</cellXfs>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. Column Widths
        $colsXml = '<cols>';
        $colIdx = 1;
        foreach (self::COLUMNS as $colMeta) {
            $w = $colMeta['width'] ?? 20;
            $colsXml .= '<col min="'.$colIdx.'" max="'.$colIdx.'" width="'.$w.'" customWidth="1"/>';
            $colIdx++;
        }
        $colsXml .= '</cols>';

        // 7. Sheet Data
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

        // 8. Worksheet XML
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
     * Hasilkan file Excel XLSX template unggah massal kolektif (1 file untuk seluruh regu & cabang)
     *
     * @param  array<string, mixed>  $params
     */
    public function downloadMassTemplate(array $params): string
    {
        $regCode = trim($params['registration_code'] ?? 'SBB-M54342H');
        $level = trim($params['level'] ?? 'Madya');
        $levelText = match (strtolower($level)) {
            'mula' => 'PMR Mula (SD/MI)',
            'wira' => 'PMR Wira (SMA/SMK)',
            default => 'PMR Madya (SMP/MTs)',
        };
        $schoolName = strtoupper(trim($params['school_name'] ?? 'SMP NEGERI 1 CIAWI BOGOR'));
        $advisorName = trim($params['advisor_name'] ?? 'Drs. Budi Santoso, M.Pd.');
        $advisorPhone = trim($params['advisor_phone'] ?? '0812-3456-7890');
        $advisorEmail = trim($params['advisor_email'] ?? 'smpn1ciawiofficial@gmail.com');
        $schoolAddress = trim($params['school_address'] ?? 'Jl. Veteran III Banjarwaru, Ciawi, Kabupaten Bogor, Jawa Barat 16720');

        $categoryNames = $params['category_names'] ?? [];
        if (empty($categoryNames)) {
            $categoryNames = ['Pertolongan Pertama', 'Ketangkasan Tandu Reguler'];
        }
        $categoryNames = array_values(array_unique(array_filter($categoryNames)));
        $catListStr = implode(', ', $categoryNames);

        $metadata = [
            'registration_code' => $regCode,
            'level' => $levelText,
            'school_name' => $schoolName,
            'advisor_info' => $advisorName.($advisorPhone ? " ({$advisorPhone})" : ''),
            'advisor_email' => $advisorEmail,
            'school_address' => $schoolAddress,
            'info_text' => "• Ketentuan Jumlah Anggota per Regu: Pertolongan Pertama (3 orang), Ketangkasan Tandu (2 orang), Ketangkasan Cuci Tangan (2 orang), Mewarnai (1 orang), Cepat Tepat (3 orang), Pendidikan Remaja Sebaya (2 orang), Olimpiade Kepalangmerahan (1 orang). • Kolom E otomatis hanya memuat pilihan cabang yang dicentang: {$catListStr}. Cukup 1x upload file ini untuk semua regu.",
        ];

        $dataRows = [];
        $rowNum = 1;

        $sampleNamesMale = [
            'Ahmad Fauzi Pratama', 'Fajar Nugraha', 'Dimas Aditya Saputra', 'Rizky Alamsyah',
            'Kevin Maulana', 'Bagus Setiawan', 'Bayu Pratama', 'Doni Kurniawan',
        ];
        $sampleNamesFemale = [
            'Siti Rahma Azzahra', 'Nabila Rahmadani', 'Putri Ayu Lestari', 'Dwi Anggraeni',
            'Zahra Nurul Aini', 'Anisa Rahmawati', 'Tiara Maharani', 'Salma Aurelia',
        ];

        if (! empty($params['teams']) && is_array($params['teams'])) {
            $mIdx = 0;
            $fIdx = 0;

            foreach ($params['teams'] as $team) {
                $cName = $team['category_name'] ?? $categoryNames[0];
                $gCat = $team['gender'] ?? ($team['gender_category'] ?? 'Putra');
                $tLabel = $team['team_label'] ?? 'Regu A';

                $cNameLower = strtolower($cName);
                $teamSize = match (true) {
                    str_contains($cNameLower, 'pertolongan pertama') => 3,
                    str_contains($cNameLower, 'tandu') => 2,
                    str_contains($cNameLower, 'cuci tangan') => 2,
                    str_contains($cNameLower, 'mewarnai') => 1,
                    str_contains($cNameLower, 'cepat tepat') => 3,
                    str_contains($cNameLower, 'pendidikan remaja sebaya') || str_contains($cNameLower, 'prs') || str_contains($cNameLower, 'favorite') || str_contains($cNameLower, 'favorit') => 2,
                    str_contains($cNameLower, 'olimpiade') || str_contains($cNameLower, 'olympiade') => 1,
                    str_contains($cNameLower, 'mading') => 2,
                    default => 2,
                };

                $isFemale = str_contains(strtoupper($gCat), 'PUTRI');
                $defaultG = $isFemale ? 'P' : 'L';

                // Ketua Regu / Peserta Utama
                $leaderName = $isFemale
                    ? ($sampleNamesFemale[$fIdx++ % count($sampleNamesFemale)])
                    : ($sampleNamesMale[$mIdx++ % count($sampleNamesMale)]);

                $dataRows[] = [
                    (string) $rowNum++,
                    $regCode,
                    $levelText,
                    $schoolName,
                    $cName,
                    $gCat,
                    $tLabel,
                    '00912'.str_pad((string) $rowNum, 5, '0', STR_PAD_LEFT),
                    $leaderName,
                    $defaultG,
                    'VIII-A',
                    $teamSize === 1 ? 'Peserta' : 'Ketua Regu',
                ];

                // Anggota tambahan sesuai kuota cabang (2 orang total => +1 anggota; 3 orang total => +2 anggota)
                for ($s = 1; $s < $teamSize; $s++) {
                    $mName = $isFemale
                        ? ($sampleNamesFemale[$fIdx++ % count($sampleNamesFemale)])
                        : ($sampleNamesMale[$mIdx++ % count($sampleNamesMale)]);

                    $dataRows[] = [
                        (string) $rowNum++,
                        $regCode,
                        $levelText,
                        $schoolName,
                        $cName,
                        $gCat,
                        $tLabel,
                        '00912'.str_pad((string) $rowNum, 5, '0', STR_PAD_LEFT),
                        $mName,
                        $defaultG,
                        'VIII-'.chr(65 + $s),
                        'Anggota',
                    ];
                }
            }
        } else {
            // Default sample rows sesuai ketentuan jumlah anggota per regu
            $dataRows = [
                // Pertolongan Pertama (3 orang per regu)
                ['1', $regCode, $levelText, $schoolName, 'Pertolongan Pertama', 'Putra', 'Regu A', '0091234561', 'Ahmad Fauzi Pratama', 'L', 'VIII-A', 'Ketua Regu'],
                ['2', $regCode, $levelText, $schoolName, 'Pertolongan Pertama', 'Putra', 'Regu A', '0091234562', 'Fajar Nugraha', 'L', 'VIII-C', 'Anggota'],
                ['3', $regCode, $levelText, $schoolName, 'Pertolongan Pertama', 'Putra', 'Regu A', '0091234563', 'Dimas Aditya Saputra', 'L', 'VII-B', 'Anggota'],
                ['4', $regCode, $levelText, $schoolName, 'Pertolongan Pertama', 'Putri', 'Regu A', '0097654321', 'Siti Rahma Azzahra', 'P', 'VIII-B', 'Ketua Regu'],
                ['5', $regCode, $levelText, $schoolName, 'Pertolongan Pertama', 'Putri', 'Regu A', '0092345678', 'Nabila Rahmadani', 'P', 'VIII-C', 'Anggota'],
                ['6', $regCode, $levelText, $schoolName, 'Pertolongan Pertama', 'Putri', 'Regu A', '0093456789', 'Putri Ayu Lestari', 'P', 'VII-A', 'Anggota'],
                // Ketangkasan Tandu Reguler (2 orang per regu)
                ['7', $regCode, $levelText, $schoolName, 'Ketangkasan Tandu Reguler', 'Putra', 'Regu A', '0094567890', 'Rizky Alamsyah', 'L', 'VII-C', 'Ketua Regu'],
                ['8', $regCode, $levelText, $schoolName, 'Ketangkasan Tandu Reguler', 'Putra', 'Regu A', '0095678901', 'Kevin Maulana', 'L', 'VII-A', 'Anggota'],
            ];
        }

        $validations = [
            'E' => implode(',', $categoryNames),
            'F' => 'Putra,Putri,Campuran',
            'J' => 'L,P',
            'L' => 'Ketua Regu,Anggota,Cadangan,Peserta',
        ];

        return $this->createMassXlsxFile($metadata, self::MASS_COLUMNS, $dataRows, $validations);
    }

    /**
     * Bangun file Excel OpenXML XLSX untuk unggah massal lengkap dengan Dropdown Validasi & Section Header
     */
    protected function createMassXlsxFile(array $meta, array $columns, array $rows, array $validations = []): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pmr_mass_xlsx_');
        $zip = new ZipArchive;
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // 1. Content Types
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

        // 4. Workbook
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets><sheet name="Daftar Peserta Massal" sheetId="1" r:id="rId1"/></sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. Styles
        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<fonts count="10">
  <font><name val="Calibri"/><sz val="10"/><color rgb="FF0F172A"/></font>
  <font><b/><name val="Calibri"/><sz val="10.5"/><color rgb="FFFFFFFF"/></font>
  <font><b/><name val="Calibri"/><sz val="10.5"/><color rgb="FF0F172A"/></font>
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF475569"/></font>
  <font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFDC2626"/></font>
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF1D4ED8"/></font>
  <font><b/><name val="Calibri"/><sz val="9.5"/><color rgb="FF78350F"/></font>
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF047857"/></font>
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF1E293B"/></font>
  <font><i/><name val="Calibri"/><sz val="9.5"/><color rgb="FF64748B"/></font>
</fonts>
<fills count="10">
  <fill><patternFill patternType="none"/></fill>
  <fill><patternFill patternType="gray125"/></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FF107C41"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFFEF2F2"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FF047857"/></patternFill></fill>
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
<cellXfs count="17">
  <!-- 0: Default Left -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 1: Header Green Bold Center -->
  <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
  <!-- 2: Data Center -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 3: Data Left -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 4: Data Center Zebra -->
  <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 5: Data Left Zebra -->
  <xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 6: Section A Header (Slate-200 fill, Bold, Left) -->
  <xf numFmtId="0" fontId="2" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 7: Meta Label (Slate-100 fill, Bold Slate, Right) -->
  <xf numFmtId="0" fontId="3" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
  <!-- 8: Reg Code (Red-50 fill, Bold Red, Center) -->
  <xf numFmtId="0" fontId="4" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 9: Tingkat (Blue-50 fill, Bold Blue, Center) -->
  <xf numFmtId="0" fontId="5" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 10: Meta Value Bold Left -->
  <xf numFmtId="0" fontId="8" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 11: Meta Value Normal Left -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 12: Info Banner (Amber fill, Amber font, Left) -->
  <xf numFmtId="0" fontId="6" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center" wrapText="1"/></xf>
  <!-- 13: Header Emerald Filter Bold Center -->
  <xf numFmtId="0" fontId="1" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
  <!-- 14: Ketua Regu Role (Green Bold) -->
  <xf numFmtId="0" fontId="7" fillId="0" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 15: Ketua Regu Role Zebra (Green Bold) -->
  <xf numFmtId="0" fontId="7" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 16: Blank separator -->
  <xf numFmtId="0" fontId="0" fillId="5" borderId="0" xfId="0" applyFill="1"/>
</cellXfs>
</styleSheet>';
        $zip->addFromString('xl/styles.xml', $styles);

        // 6. Column Widths
        $colsXml = '<cols>';
        $colIdx = 1;
        foreach ($columns as $colMeta) {
            $w = $colMeta['width'] ?? 20;
            $colsXml .= '<col min="'.$colIdx.'" max="'.$colIdx.'" width="'.$w.'" customWidth="1"/>';
            $colIdx++;
        }
        $colsXml .= '</cols>';

        // 7. Sheet Data
        $sheetDataXml = '<sheetData>';

        // Row 1: Section A Header Banner (A1:L1)
        $sheetDataXml .= '<row r="1" ht="26" customHeight="1">';
        $sheetDataXml .= '<c r="A1" s="6" t="inlineStr"><is><t>📌 [BAGIAN A] IDENTITAS RESMI PENDAFTARAN &amp; SEKOLAH (Otomatis Terisi &amp; Terkunci oleh Sistem)</t></is></c>';
        for ($c = 1; $c < 12; $c++) {
            $colL = $this->indexToColumnLetter($c);
            $sheetDataXml .= '<c r="'.$colL.'1" s="6"/>';
        }
        $sheetDataXml .= '</row>';

        // Row 2: Metadata Row 1 (No. Registrasi, Tingkatan, Nama Sekolah, Email Resmi)
        $sheetDataXml .= '<row r="2" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A2" s="7" t="inlineStr"><is><t>No. Registrasi:</t></is></c>';
        $sheetDataXml .= '<c r="B2" s="8" t="inlineStr"><is><t>'.htmlspecialchars($meta['registration_code'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="C2" s="7" t="inlineStr"><is><t>Tingkatan:</t></is></c>';
        $sheetDataXml .= '<c r="D2" s="9" t="inlineStr"><is><t>'.htmlspecialchars($meta['level'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="E2" s="7" t="inlineStr"><is><t>Nama Sekolah:</t></is></c>';
        $sheetDataXml .= '<c r="F2" s="10" t="inlineStr"><is><t>'.htmlspecialchars($meta['school_name'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="G2" s="10"/>';
        $sheetDataXml .= '<c r="H2" s="10"/>';
        $sheetDataXml .= '<c r="I2" s="7" t="inlineStr"><is><t>Email Resmi:</t></is></c>';
        $sheetDataXml .= '<c r="J2" s="11" t="inlineStr"><is><t>'.htmlspecialchars($meta['advisor_email'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="K2" s="11"/>';
        $sheetDataXml .= '<c r="L2" s="11"/>';
        $sheetDataXml .= '</row>';

        // Row 3: Metadata Row 2 (Pembina/Pelatih, Alamat Sekolah)
        $sheetDataXml .= '<row r="3" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A3" s="7" t="inlineStr"><is><t>Pembina/Pelatih:</t></is></c>';
        $sheetDataXml .= '<c r="B3" s="10" t="inlineStr"><is><t>'.htmlspecialchars($meta['advisor_info'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="C3" s="10"/>';
        $sheetDataXml .= '<c r="D3" s="10"/>';
        $sheetDataXml .= '<c r="E3" s="7" t="inlineStr"><is><t>Alamat Sekolah:</t></is></c>';
        $sheetDataXml .= '<c r="F3" s="11" t="inlineStr"><is><t>'.htmlspecialchars($meta['school_address'] ?? '', ENT_XML1).'</t></is></c>';
        for ($c = 6; $c < 12; $c++) {
            $colL = $this->indexToColumnLetter($c);
            $sheetDataXml .= '<c r="'.$colL.'3" s="11"/>';
        }
        $sheetDataXml .= '</row>';

        // Row 4: Petunjuk Pengisian
        $sheetDataXml .= '<row r="4" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A4" s="12" t="inlineStr"><is><t>Info:</t></is></c>';
        $sheetDataXml .= '<c r="B4" s="12" t="inlineStr"><is><t>'.htmlspecialchars($meta['info_text'] ?? '', ENT_XML1).'</t></is></c>';
        for ($c = 2; $c < 12; $c++) {
            $colL = $this->indexToColumnLetter($c);
            $sheetDataXml .= '<c r="'.$colL.'4" s="12"/>';
        }
        $sheetDataXml .= '</row>';

        // Row 5: Spacing Blank Row
        $sheetDataXml .= '<row r="5" ht="8" customHeight="1">';
        for ($c = 0; $c < 12; $c++) {
            $colL = $this->indexToColumnLetter($c);
            $sheetDataXml .= '<c r="'.$colL.'5" s="16"/>';
        }
        $sheetDataXml .= '</row>';

        // Row 6: Main Column Headers
        $sheetDataXml .= '<row r="6" ht="28" customHeight="1">';
        $cIdx = 0;
        foreach ($columns as $colKey => $colMeta) {
            $colLetter = $this->indexToColumnLetter($cIdx);
            $styleId = ($colKey === 'E') ? 13 : 1;
            $safeLabel = htmlspecialchars($colMeta['label'], ENT_XML1);
            $sheetDataXml .= '<c r="'.$colLetter.'6" s="'.$styleId.'" t="inlineStr"><is><t>'.$safeLabel.'</t></is></c>';
            $cIdx++;
        }
        $sheetDataXml .= '</row>';

        // Row 7+: Data Rows
        $alignMap = array_values(array_column($columns, 'align'));
        foreach ($rows as $rIdx => $rowValues) {
            $rNum = $rIdx + 7;
            $isZebra = ($rNum % 2 === 1);
            $sheetDataXml .= '<row r="'.$rNum.'" ht="22" customHeight="1">';

            foreach ($rowValues as $colIndex => $val) {
                $colLetter = $this->indexToColumnLetter($colIndex);
                $cellRef = $colLetter.$rNum;
                $align = $alignMap[$colIndex] ?? 'left';

                if ($colIndex === 11 && str_contains(strtolower((string) $val), 'ketua')) {
                    $styleId = $isZebra ? 15 : 14;
                } elseif ($align === 'center') {
                    $styleId = $isZebra ? 4 : 2;
                } else {
                    $styleId = $isZebra ? 5 : 3;
                }

                $safeVal = htmlspecialchars((string) $val, ENT_XML1);
                $sheetDataXml .= '<c r="'.$cellRef.'" s="'.$styleId.'" t="inlineStr"><is><t>'.$safeVal.'</t></is></c>';
            }

            $sheetDataXml .= '</row>';
        }

        $sheetDataXml .= '</sheetData>';

        // 8. Merge Cells
        $mergeCellsXml = '<mergeCells count="6">
<mergeCell ref="A1:L1"/>
<mergeCell ref="F2:H2"/>
<mergeCell ref="J2:L2"/>
<mergeCell ref="B3:D3"/>
<mergeCell ref="F3:L3"/>
<mergeCell ref="B4:L4"/>
</mergeCells>';

        // 9. Data Validations
        $dataValXml = '';
        if (! empty($validations)) {
            $vCount = count($validations);
            $dataValXml .= '<dataValidations count="'.$vCount.'">';
            foreach ($validations as $colLet => $listStr) {
                $safeFormula = htmlspecialchars('&quot;'.$listStr.'&quot;', ENT_XML1);
                $dataValXml .= '<dataValidation type="list" allowBlank="1" showInputMessage="1" showErrorMessage="1" sqref="'.$colLet.'7:'.$colLet.'500">';
                $dataValXml .= '<formula1>'.$safeFormula.'</formula1>';
                $dataValXml .= '</dataValidation>';
            }
            $dataValXml .= '</dataValidations>';
        }

        // 10. Worksheet XML
        $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews>
<sheetView tabSelected="1" workbookViewId="0">
<pane ySplit="6" topLeftCell="A7" activePane="bottomLeft" state="frozen"/>
</sheetView>
</sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
'.$mergeCellsXml.'
'.$dataValXml.'
</worksheet>';

        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);
        $zip->close();

        return $tempFile;
    }

    /**
     * Parse file Excel Massal Kolektif dan ekstrak daftar peserta terpetakan per regu & cabang
     *
     * @param  UploadedFile|string  $file
     * @return array<string, mixed>
     */
    public function parseMassParticipantFile($file): array
    {
        $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $extension = $file instanceof UploadedFile
            ? strtolower($file->getClientOriginalExtension())
            : strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $rows = [];
        if (in_array($extension, ['csv', 'txt'])) {
            $rows = $this->readCsv($filePath);
        } else {
            $rows = $this->readXlsx($filePath);
            if (empty($rows)) {
                $rows = $this->readCsv($filePath);
            }
        }

        if (empty($rows)) {
            return [
                'success' => false,
                'message' => 'Berkas Excel kosong atau tidak dapat dibaca.',
                'total_participants' => 0,
                'participants' => [],
                'teams_count' => 0,
                'grouped' => [],
            ];
        }

        // 1. Ekstrak Metadata dari baris 1-4 (jika ada)
        $metaSchoolName = null;
        $metaRegCode = null;
        $metaLevel = null;

        foreach (array_slice($rows, 0, 5) as $rIdx => $r) {
            $rLower = array_map(fn ($c) => strtolower(trim((string) $c)), $r);
            foreach ($rLower as $cIdx => $cellText) {
                if (str_contains($cellText, 'registrasi') && isset($r[$cIdx + 1])) {
                    $metaRegCode = trim((string) $r[$cIdx + 1]);
                } elseif (str_contains($cellText, 'tingkatan') && isset($r[$cIdx + 1])) {
                    $metaLevel = trim((string) $r[$cIdx + 1]);
                } elseif (str_contains($cellText, 'nama sekolah') && isset($r[$cIdx + 1])) {
                    $metaSchoolName = trim((string) $r[$cIdx + 1]);
                }
            }
        }

        // 2. Cari Baris Header Tabel
        $headerRowIdx = null;
        $colMap = [];

        foreach ($rows as $idx => $row) {
            $rowLower = array_map(fn ($c) => strtolower(trim((string) $c)), $row);
            $matchedHeaderCount = 0;
            foreach ($rowLower as $cIdx => $cellText) {
                if (str_contains($cellText, 'cabang') || str_contains($cellText, 'nama lengkap') || str_contains($cellText, 'pendaftaran') || str_contains($cellText, 'regu')) {
                    $matchedHeaderCount++;
                }
            }

            if ($matchedHeaderCount >= 2) {
                $headerRowIdx = $idx;
                break;
            }
        }

        if ($headerRowIdx === null) {
            foreach ($rows as $idx => $row) {
                $rowLower = array_map(fn ($c) => strtolower(trim((string) $c)), $row);
                foreach ($rowLower as $cIdx => $cellText) {
                    if (str_contains($cellText, 'nama')) {
                        $headerRowIdx = $idx;
                        break 2;
                    }
                }
            }
        }

        if ($headerRowIdx !== null) {
            $headerRow = $rows[$headerRowIdx];
            $colMap = $this->buildMassHeaderMapping($headerRow);
            $dataRows = array_slice($rows, $headerRowIdx + 1);
        } else {
            $dataRows = $rows;
            $colMap = [
                'no' => 0,
                'registration_code' => 1,
                'level' => 2,
                'school_name' => 3,
                'category_name' => 4,
                'gender_category' => 5,
                'team_label' => 6,
                'nisn' => 7,
                'name' => 8,
                'gender' => 9,
                'class_grade' => 10,
                'role' => 11,
            ];
        }

        $participants = [];
        $groupedTeams = [];

        foreach ($dataRows as $row) {
            if (empty($row) || ! is_array($row)) {
                continue;
            }

            $name = $this->getMappedVal($row, $colMap, 'name', 8);
            if (! $name || strlen(trim($name)) < 2) {
                continue;
            }

            $cleanName = preg_replace('/^\d+[\.\)\-\s]+/', '', trim($name));
            if (strlen($cleanName) < 2) {
                continue;
            }

            $lowerName = strtolower($cleanName);
            if (in_array($lowerName, ['nama lengkap', 'nama peserta', 'nama siswa', 'nama lengkap peserta *', 'nama lengkap peserta', 'nama'])) {
                continue;
            }

            $regCode = $this->getMappedVal($row, $colMap, 'registration_code', 1) ?: $metaRegCode;
            $level = $this->getMappedVal($row, $colMap, 'level', 2) ?: $metaLevel;
            $schoolName = $this->getMappedVal($row, $colMap, 'school_name', 3) ?: $metaSchoolName;
            $catName = $this->getMappedVal($row, $colMap, 'category_name', 4);
            $gCat = $this->getMappedVal($row, $colMap, 'gender_category', 5) ?: 'Umum';
            $tLabel = $this->getMappedVal($row, $colMap, 'team_label', 6) ?: 'Regu A';
            $nisn = $this->getMappedVal($row, $colMap, 'nisn', 7);
            $gender = $this->getMappedVal($row, $colMap, 'gender', 9);
            $classGrade = $this->getMappedVal($row, $colMap, 'class_grade', 10);
            $role = $this->getMappedVal($row, $colMap, 'role', 11);

            // Normalisasi Gender
            $genderClean = '';
            if ($gender) {
                $gUpper = strtoupper(trim($gender));
                if (str_starts_with($gUpper, 'L') || str_contains($gUpper, 'PUTRA') || str_contains($gUpper, 'LAKI')) {
                    $genderClean = 'L';
                } elseif (str_starts_with($gUpper, 'P') || str_contains($gUpper, 'PUTRI') || str_contains($gUpper, 'PEREMPUAN')) {
                    $genderClean = 'P';
                } else {
                    $genderClean = substr($gUpper, 0, 1);
                }
            }

            // Normalisasi Kategori Regu
            $gCatUpper = strtoupper(trim($gCat));
            $gCatClean = 'Umum';
            if (str_contains($gCatUpper, 'PUTRA')) {
                $gCatClean = 'Putra';
            } elseif (str_contains($gCatUpper, 'PUTRI')) {
                $gCatClean = 'Putri';
            } elseif (str_contains($gCatUpper, 'CAMPUR')) {
                $gCatClean = 'Campuran';
            }

            // Normalisasi Role
            $roleClean = 'Anggota';
            if ($role) {
                $rUpper = strtoupper(trim($role));
                if (str_contains($rUpper, 'KETUA') || str_contains($rUpper, 'PINRU') || str_contains($rUpper, 'LEADER')) {
                    $roleClean = 'Ketua Regu';
                } elseif (str_contains($rUpper, 'CADANG') || str_contains($rUpper, 'SUB')) {
                    $roleClean = 'Cadangan';
                } elseif (str_contains($rUpper, 'PESERTA') || str_contains($rUpper, 'TUNGGAL') || str_contains($rUpper, 'INDIVIDU')) {
                    $roleClean = 'Peserta';
                } else {
                    $roleClean = 'Anggota';
                }
            }

            $participantItem = [
                'name' => ucwords(strtolower($cleanName)),
                'gender' => $genderClean,
                'nisn' => $nisn ? trim($nisn) : null,
                'class_grade' => $classGrade ? trim($classGrade) : null,
                'role' => $roleClean,
                'registration_code' => $regCode,
                'level' => $level,
                'school_name' => $schoolName,
                'category_name' => $catName ? trim($catName) : 'Umum',
                'gender_category' => $gCatClean,
                'team_label' => trim($tLabel),
            ];

            $participants[] = $participantItem;

            // Grouping by Team
            $teamKey = strtolower(trim($participantItem['category_name']).'___'.trim($participantItem['gender_category']).'___'.trim($participantItem['team_label']));
            if (! isset($groupedTeams[$teamKey])) {
                $groupedTeams[$teamKey] = [
                    'category_name' => $participantItem['category_name'],
                    'gender_category' => $participantItem['gender_category'],
                    'team_label' => $participantItem['team_label'],
                    'members_count' => 0,
                    'members' => [],
                ];
            }

            $groupedTeams[$teamKey]['members'][] = [
                'name' => $participantItem['name'],
                'gender' => $participantItem['gender'],
                'nisn' => $participantItem['nisn'],
                'class_grade' => $participantItem['class_grade'],
                'role' => $participantItem['role'],
            ];
            $groupedTeams[$teamKey]['members_count']++;
        }

        // Hitung kuota standar per regu & validasi kesesuaian
        foreach ($groupedTeams as $k => $grp) {
            $cLower = strtolower($grp['category_name']);
            $expected = match (true) {
                str_contains($cLower, 'pertolongan pertama') => 3,
                str_contains($cLower, 'tandu') => 2,
                str_contains($cLower, 'cuci tangan') => 2,
                str_contains($cLower, 'mewarnai') => 1,
                str_contains($cLower, 'cepat tepat') => 3,
                str_contains($cLower, 'pendidikan remaja sebaya') || str_contains($cLower, 'prs') || str_contains($cLower, 'favorite') || str_contains($cLower, 'favorit') => 2,
                str_contains($cLower, 'olimpiade') || str_contains($cLower, 'olympiade') => 1,
                str_contains($cLower, 'mading') => 2,
                default => 2,
            };

            $groupedTeams[$k]['expected_members_count'] = $expected;
            $groupedTeams[$k]['is_valid_size'] = ($grp['members_count'] === $expected);
        }

        return [
            'success' => true,
            'total_participants' => count($participants),
            'participants' => $participants,
            'teams_count' => count($groupedTeams),
            'grouped' => array_values($groupedTeams),
            'categories_detected' => array_values(array_unique(array_filter(array_column($participants, 'category_name')))),
            'school_name' => $metaSchoolName,
            'registration_code' => $metaRegCode,
            'message' => count($participants).' peserta terdeteksi untuk '.count($groupedTeams).' regu.',
        ];
    }

    protected function buildMassHeaderMapping(array $headerRow): array
    {
        $map = [];
        foreach ($headerRow as $idx => $cell) {
            $t = strtolower(trim((string) $cell));

            if ($t === 'no' || $t === 'nomor' || $t === '#') {
                $map['no'] = $idx;
            } elseif (str_contains($t, 'pendaftaran') || str_contains($t, 'registrasi')) {
                $map['registration_code'] = $idx;
            } elseif (str_contains($t, 'tingkat')) {
                $map['level'] = $idx;
            } elseif (str_contains($t, 'sekolah') || str_contains($t, 'unit')) {
                $map['school_name'] = $idx;
            } elseif (str_contains($t, 'cabang') || str_contains($t, 'mata lomba')) {
                $map['category_name'] = $idx;
            } elseif (str_contains($t, 'kategori')) {
                $map['gender_category'] = $idx;
            } elseif (str_contains($t, 'regu')) {
                $map['team_label'] = $idx;
            } elseif (str_contains($t, 'nisn') || str_contains($t, 'nis')) {
                $map['nisn'] = $idx;
            } elseif (str_contains($t, 'nama')) {
                $map['name'] = $idx;
            } elseif (str_contains($t, 'kelamin') || str_contains($t, 'gender') || $t === 'l / p' || $t === 'l/p' || $t === 'jk') {
                $map['gender'] = $idx;
            } elseif (str_contains($t, 'kelas') || str_contains($t, 'tingkat kelas')) {
                $map['class_grade'] = $idx;
            } elseif (str_contains($t, 'peran') || str_contains($t, 'jabatan') || str_contains($t, 'posisi')) {
                $map['role'] = $idx;
            }
        }

        return $map;
    }

    protected function columnRefToIndex(string $ref): int
    {
        preg_match('/^([A-Z]+)/i', $ref, $matches);
        $colLetters = strtoupper($matches[1] ?? 'A');

        $index = 0;
        $len = strlen($colLetters);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($colLetters[$i]) - 64);
        }

        return $index - 1;
    }

    protected function indexToColumnLetter(int $index): string
    {
        $letter = '';
        $index += 1;
        while ($index > 0) {
            $rem = ($index - 1) % 26;
            $letter = chr(65 + $rem).$letter;
            $index = (int) (($index - 1) / 26);
        }

        return $letter;
    }
}
