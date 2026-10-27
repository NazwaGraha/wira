<?php

namespace App\Services;

use App\Models\CompetitionCategory;
use App\Models\CompetitionEvent;
use App\Models\CompetitionParticipantTeam;
use App\Models\CompetitionScore;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
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
     * Definisi 12 kolom Lembar Penilaian Juri Excel Resmi (Sesuai Mockup Disetujui)
     */
    public const SCORE_SHEET_COLUMNS = [
        'A' => ['key' => 'order_number', 'label' => 'NO. URUT', 'width' => 12, 'align' => 'center'],
        'B' => ['key' => 'reg_code', 'label' => 'KODE REG', 'width' => 20, 'align' => 'center'],
        'C' => ['key' => 'school_name', 'label' => 'ASAL SEKOLAH / PANGKALAN', 'width' => 36, 'align' => 'left'],
        'D' => ['key' => 'team_name', 'label' => 'NAMA REGU / KONTINGEN', 'width' => 28, 'align' => 'left'],
        'E' => ['key' => 'practical_score', 'label' => 'NILAI PRAKTIK', 'width' => 16, 'align' => 'center'],
        'F' => ['key' => 'time_recorded', 'label' => 'WAKTU (MM:SS)', 'width' => 16, 'align' => 'center'],
        'G' => ['key' => 'time_score', 'label' => 'POIN WAKTU', 'width' => 15, 'align' => 'center'],
        'H' => ['key' => 'final_score', 'label' => 'NILAI AKHIR', 'width' => 16, 'align' => 'center'],
        'I' => ['key' => 'rank', 'label' => 'PERINGKAT', 'width' => 18, 'align' => 'center'],
        'J' => ['key' => 'jury_name', 'label' => 'JURI PENILAI', 'width' => 26, 'align' => 'center'],
        'K' => ['key' => 'photos_count', 'label' => 'FOTO', 'width' => 12, 'align' => 'center'],
        'L' => ['key' => 'notes', 'label' => 'CATATAN JURI LAPANGAN', 'width' => 40, 'align' => 'left'],
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

    /**
     * Hasilkan file Excel (.xlsx) Lembar Penilaian Juri Resmi sama persis dengan mockup visual
     */
    public function exportCategoryScoreSheetXlsx(CompetitionCategory $category, string $round = 'Utama'): string
    {
        $category->loadMissing(['assignedJuries', 'event']);

        // Ambil seluruh tim yang terdaftar di kategori ini
        $teams = CompetitionParticipantTeam::where('competition_category_id', $category->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereHas('registration', function ($rq) {
                    $rq->where('status', 'verified');
                })->orWhereNull('competition_registration_id');
            })
            ->with(['registration'])
            ->orderBy('order_number')
            ->get();

        // Ambil data nilai
        $scores = CompetitionScore::where('competition_category_id', $category->id)
            ->where('round_name', $round)
            ->with(['jury', 'photos'])
            ->get()
            ->keyBy('competition_participant_team_id');

        // Ambil juri yang dipetakan pada cabang mata lomba ini
        $assignedJuries = $category->assignedJuries ?? collect();

        // Petakan data baris peserta dan pisahkan scored vs unscored
        $scoredItems = [];
        $unscoredItems = [];

        foreach ($teams as $tIdx => $team) {
            $score = $scores->get($team->id);
            $regCode = $team->registration?->registration_code ?: '-';
            $schoolName = $team->registration?->school_name ?: $team->team_name;
            $teamName = $team->team_label ?: $team->team_name;
            $orderNo = $team->order_number ?: '-';

            // Nilai Praktik / Teknis
            $details = is_array($score?->score_details) ? $score->score_details : (json_decode($score?->score_details ?? '', true) ?? []);
            $practicalScore = '-';
            if (isset($details['practical_score']) && is_numeric($details['practical_score'])) {
                $practicalScore = number_format((float) $details['practical_score'], 2);
            } elseif (isset($details['score']) && is_numeric($details['score'])) {
                $practicalScore = number_format((float) $details['score'], 2);
            } elseif ($score?->final_score !== null) {
                $practicalScore = number_format((float) $score->final_score, 2);
            }

            // Waktu
            $timeRecorded = $score?->time_recorded ?: '-';

            // Poin Waktu
            $timeScore = '-';
            if (isset($details['time_score']) && is_numeric($details['time_score'])) {
                $timeScore = number_format((float) $details['time_score'], 1);
            }

            // Nilai Akhir
            $finalScore = $score?->final_score !== null ? number_format((float) $score->final_score, 2) : '-';

            // Peringkat
            $rankNum = $score?->rank;
            $isDisqualified = (bool) $score?->is_disqualified;
            $rankLabel = '-';
            if ($isDisqualified) {
                $rankLabel = 'DISKUALIFIKASI';
            } elseif ($rankNum === 1) {
                $rankLabel = 'JUARA 1';
            } elseif ($rankNum === 2) {
                $rankLabel = 'JUARA 2';
            } elseif ($rankNum === 3) {
                $rankLabel = 'JUARA 3';
            } elseif ($rankNum === 4) {
                $rankLabel = 'Harapan 1';
            } elseif ($rankNum === 5) {
                $rankLabel = 'Harapan 2';
            } elseif ($rankNum === 6) {
                $rankLabel = 'Harapan 3';
            } elseif ($rankNum > 6) {
                $rankLabel = 'Peringkat '.$rankNum;
            }

            // Juri Penilai: Prioritaskan juri tersimpan di score; jika belum tersimpan, gunakan pemetaan dewan juri resmi untuk lomba ini
            $juryName = '-';
            if ($score?->jury?->name) {
                $juryName = $score->jury->name;
            } elseif ($score?->jury?->username) {
                $juryName = '@'.$score->jury->username;
            } elseif ($assignedJuries->isNotEmpty()) {
                if ($assignedJuries->count() === 1) {
                    $juryName = $assignedJuries->first()->name ?: ('@'.$assignedJuries->first()->username);
                } else {
                    // Jika ada 2 juri atau lebih, petakan sesuai pos/prefix regu (A-xx -> Juri 1, B-xx -> Juri 2) atau rotasi urutan regu
                    $upperOrder = strtoupper((string) $orderNo);
                    if (str_starts_with($upperOrder, 'B') && isset($assignedJuries[1])) {
                        $juryName = $assignedJuries[1]->name ?: ('@'.$assignedJuries[1]->username);
                    } elseif (str_starts_with($upperOrder, 'A') && isset($assignedJuries[0])) {
                        $juryName = $assignedJuries[0]->name ?: ('@'.$assignedJuries[0]->username);
                    } else {
                        $juryIndex = $tIdx % $assignedJuries->count();
                        $targetJury = $assignedJuries[$juryIndex];
                        $juryName = $targetJury->name ?: ('@'.$targetJury->username);
                    }
                }
            }

            // Foto
            $photosCount = $score && $score->photos ? count($score->photos) : 0;
            $photoText = $photosCount > 0 ? $photosCount.' Foto' : '-';

            // Catatan
            $notes = $score?->notes ?: '-';

            $rowPayload = [
                'order_number' => $orderNo,
                'reg_code' => $regCode,
                'school_name' => $schoolName,
                'team_name' => $teamName,
                'practical_score' => $practicalScore,
                'time_recorded' => $timeRecorded,
                'time_score' => $timeScore,
                'final_score' => $finalScore,
                'final_score_raw' => $score?->final_score !== null ? (float) $score->final_score : null,
                'rank_label' => $rankLabel,
                'rank_num' => $rankNum,
                'is_disqualified' => $isDisqualified,
                'jury_name' => $juryName,
                'photo_text' => $photoText,
                'notes' => $notes,
            ];

            if ($score && ($score->final_score !== null || $rankNum !== null)) {
                $scoredItems[] = $rowPayload;
            } else {
                $unscoredItems[] = $rowPayload;
            }
        }

        // Urutkan item yang sudah dinilai: rank 1, 2, 3...
        usort($scoredItems, function ($a, $b) {
            if ($a['rank_num'] !== null && $b['rank_num'] !== null) {
                return $a['rank_num'] <=> $b['rank_num'];
            }
            if ($a['rank_num'] !== null) {
                return -1;
            }
            if ($b['rank_num'] !== null) {
                return 1;
            }

            return ($b['final_score_raw'] ?? 0) <=> ($a['final_score_raw'] ?? 0);
        });

        // Gabungkan: Juara teratas disusul peserta lainnya
        $dataRows = array_merge($scoredItems, $unscoredItems);

        // Metadata lomba & event
        $eventName = $category->event?->name ?? 'SUA BHAKTI BERKARYA IV';
        $eventYear = $category->event?->start_date ? Carbon::parse($category->event->start_date)->format('Y') : date('Y');

        $levelText = match (strtolower($category->level ?? '')) {
            'mula' => 'MULA (SD/MI)',
            'wira' => 'WIRA (SMA/SMK)',
            default => 'MADYA (SMP/MTs)',
        };

        $scoringModel = match ($category->scoring_type) {
            'lpp' => 'Nilai Praktik, Tertulis & Waktu',
            'multi_criteria' => 'Multi Kriteria Terstandarisasi',
            'social_engagement' => 'Engagement Media Sosial (Likes & Stiker)',
            default => 'Nilai Praktik & Waktu Praktik',
        };

        // Dewan Juri Bertugas
        $juries = $category->assignedJuries ?? collect();
        $jury1Name = $juries->count() > 0 ? $juries[0]->name : 'Dewan Juri I Lapangan';
        $jury1User = $juries->count() > 0 && $juries[0]->username ? '@'.$juries[0]->username : '@juri1';
        $jury2Name = $juries->count() > 1 ? $juries[1]->name : 'Dewan Juri II Lapangan';
        $jury2User = $juries->count() > 1 && $juries[1]->username ? '@'.$juries[1]->username : '@juri2';

        // Hitung statistik
        $scoredCount = count($scoredItems);
        $avgScore = '-';
        $topScoreText = '-';
        $fastestTimeText = '-';

        if ($scoredCount > 0) {
            $sumFinal = 0;
            $countFinal = 0;
            $topScoreVal = -1;
            $topScoreTeam = '';

            $minSeconds = 99999999;
            $fastestTimeStr = '';
            $fastestOrderNo = '';

            foreach ($scoredItems as $item) {
                if ($item['final_score_raw'] !== null && ! $item['is_disqualified']) {
                    $sumFinal += $item['final_score_raw'];
                    $countFinal++;

                    if ($item['final_score_raw'] > $topScoreVal) {
                        $topScoreVal = $item['final_score_raw'];
                        $topScoreTeam = $item['school_name'];
                    }
                }

                if ($item['time_recorded'] !== '-' && ! empty($item['time_recorded'])) {
                    $tParts = explode(':', trim($item['time_recorded']));
                    $sec = (count($tParts) === 2) ? (((int) $tParts[0] * 60) + (int) $tParts[1]) : (int) $tParts[0];
                    if ($sec > 0 && $sec < $minSeconds) {
                        $minSeconds = $sec;
                        $fastestTimeStr = $item['time_recorded'];
                        $fastestOrderNo = $item['order_number'];
                    }
                }
            }

            if ($countFinal > 0) {
                $avgScore = number_format($sumFinal / $countFinal, 2);
            }
            if ($topScoreVal >= 0) {
                $topScoreText = number_format($topScoreVal, 2).' ('.$topScoreTeam.')';
            }
            if ($minSeconds < 99999999) {
                $fastestTimeText = $fastestTimeStr.' ('.$fastestOrderNo.')';
            }
        }

        $meta = [
            'event_title' => 'LOMBA KEPALANGMERAHAN "'.strtoupper($eventName).'" TAHUN '.$eventYear,
            'category_name' => $category->name.($category->gender_category && $category->gender_category !== 'Umum' ? ' ('.$category->gender_category.')' : ''),
            'code_round' => ($category->code ?: 'LOMBA').' • Babak '.$round,
            'level_text' => $levelText,
            'scoring_model' => $scoringModel,
            'jury_1' => $jury1Name.' ('.$jury1User.')',
            'jury_1_name' => $jury1Name,
            'jury_1_user' => $jury1User,
            'jury_2' => $jury2Name.' ('.$jury2User.')',
            'jury_2_name' => $jury2Name,
            'jury_2_user' => $jury2User,
            'juries_count' => $juries->count(),
            'export_date' => Carbon::now()->translatedFormat('d F Y'),
            'export_time' => Carbon::now()->format('d/m/Y H:i').' WIB',
            'stat_total_teams' => $scoredCount > 0 ? $scoredCount.' Regu' : count($dataRows).' Regu Terdaftar',
            'stat_avg_score' => $avgScore,
            'stat_top_score' => $topScoreText,
            'stat_fastest_time' => $fastestTimeText,
        ];

        return $this->createScoreSheetXlsxFile($meta, $dataRows);
    }

    /**
     * Bangun file Excel OpenXML XLSX Lembar Penilaian Juri sama persis 100% dengan mockup visual
     * Bersih & compliant dengan standar ECMA-376 OpenXML tanpa error recovery Excel
     *
     * @param  array<string, mixed>  $meta
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function createScoreSheetXlsxFile(array $meta, array $rows): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pmr_scoresheet_');
        $zip = new ZipArchive;
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // 1. Content Types (3 Worksheets: Lembar Nilai Utama, Rekap Juara Umum, Daftar Dewan Juri)
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
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
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
<Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. Workbook with workbookPr and bookViews
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<workbookPr date1904="false"/>
<bookViews>
<workbookView activeTab="0"/>
</bookViews>
<sheets>
<sheet name="Lembar Nilai Utama" sheetId="1" r:id="rId1"/>
<sheet name="Rekap Juara Umum" sheetId="2" r:id="rId2"/>
<sheet name="Daftar Dewan Juri" sheetId="3" r:id="rId3"/>
</sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. Styles
        $zip->addFromString('xl/styles.xml', $this->buildScoreSheetStylesXml());

        // 6. Sheet 1: Lembar Nilai Utama
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildMainScoreSheetXml($meta, $rows));

        // 7. Sheet 2: Rekap Juara Umum
        $zip->addFromString('xl/worksheets/sheet2.xml', $this->buildGeneralChampionSheetXml($meta, $rows));

        // 8. Sheet 3: Daftar Dewan Juri
        $zip->addFromString('xl/worksheets/sheet3.xml', $this->buildJuryListSheetXml($meta));

        $zip->close();

        return $tempFile;
    }

    /**
     * XML Styles OpenXML komprehensif compliant dengan standar ECMA-376
     */
    protected function buildScoreSheetStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<fonts count="19">
  <!-- 0: Normal Slate Calibri 10 -->
  <font><name val="Calibri"/><sz val="10"/><color rgb="FF0F172A"/></font>
  <!-- 1: Bold White Calibri 10 -->
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FFFFFFFF"/></font>
  <!-- 2: Bold Slate Calibri 10 -->
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF0F172A"/></font>
  <!-- 3: Bold Red Kop Calibri 11 -->
  <font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFDC2626"/></font>
  <!-- 4: Bold Title Kop Calibri 15 -->
  <font><b/><name val="Calibri"/><sz val="15"/><color rgb="FF0F172A"/></font>
  <!-- 5: Bold Subtitle Kop Calibri 10.5 -->
  <font><b/><name val="Calibri"/><sz val="10.5"/><color rgb="FF334155"/></font>
  <!-- 6: Bold Amber Juara 1 Calibri 10 -->
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF78350F"/></font>
  <!-- 7: Bold Blue Calibri 10 (Nilai Praktik, Harapan, Kode Lomba) -->
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF1D4ED8"/></font>
  <!-- 8: Bold Emerald Calibri 11 (Nilai Akhir, Official Score Sheet) -->
  <font><b/><name val="Calibri"/><sz val="11"/><color rgb="FF047857"/></font>
  <!-- 9: Italic Slate Notes Calibri 9.5 -->
  <font><i/><name val="Calibri"/><sz val="9.5"/><color rgb="FF64748B"/></font>
  <!-- 10: Slate Small Calibri 9 (Print Info, Stat Label) -->
  <font><name val="Calibri"/><sz val="9"/><color rgb="FF64748B"/></font>
  <!-- 11: Bold Bronze Juara 3 Calibri 10 -->
  <font><b/><name val="Calibri"/><sz val="10"/><color rgb="FF9A3412"/></font>
  <!-- 12: Bold Green Dark Foto Badge Calibri 9.5 -->
  <font><b/><name val="Calibri"/><sz val="9.5"/><color rgb="FF166534"/></font>
  <!-- 13: Bold Slate Stat Value Calibri 12 -->
  <font><b/><name val="Calibri"/><sz val="12"/><color rgb="FF0F172A"/></font>
  <!-- 14: Bold Blue Stat Value Calibri 12 -->
  <font><b/><name val="Calibri"/><sz val="12"/><color rgb="FF1D4ED8"/></font>
  <!-- 15: Bold Emerald Stat Value Calibri 12 -->
  <font><b/><name val="Calibri"/><sz val="12"/><color rgb="FF047857"/></font>
  <!-- 16: Bold Amber Stat Value Calibri 12 -->
  <font><b/><name val="Calibri"/><sz val="12"/><color rgb="FFD97706"/></font>
  <!-- 17: Bold Amber Jury Badge Calibri 9.5 -->
  <font><b/><name val="Calibri"/><sz val="9.5"/><color rgb="FF92400E"/></font>
  <!-- 18: Bold Blue Pill Calibri 9.5 -->
  <font><b/><name val="Calibri"/><sz val="9.5"/><color rgb="FF1E40AF"/></font>
</fonts>
<fills count="16">
  <fill><patternFill patternType="none"/></fill>
  <fill><patternFill patternType="gray125"/></fill>
  <!-- 2: Green Table Header #107C41 -->
  <fill><patternFill patternType="solid"><fgColor rgb="FF107C41"/></patternFill></fill>
  <!-- 3: Dark Green Table Header Nilai Akhir #0B542B -->
  <fill><patternFill patternType="solid"><fgColor rgb="FF0B542B"/></patternFill></fill>
  <!-- 4: Gold Table Header Peringkat #F59E0B -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFF59E0B"/></patternFill></fill>
  <!-- 5: Emerald Table Header Juri Penilai #15803D -->
  <fill><patternFill patternType="solid"><fgColor rgb="FF15803D"/></patternFill></fill>
  <!-- 6: Juara 1 Soft Amber #FEF3C7 -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFFEF3C7"/></patternFill></fill>
  <!-- 7: Juara 2 Soft Slate #F1F5F9 -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFF1F5F9"/></patternFill></fill>
  <!-- 8: Juara 3 Soft Orange #FFEDD5 -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFFFEDD5"/></patternFill></fill>
  <!-- 9: Harapan Soft Blue #EFF6FF -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFEFF6FF"/></patternFill></fill>
  <!-- 10: Zebra Light Slate & Stat Box Label #F8FAFC -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFF8FAFC"/></patternFill></fill>
  <!-- 11: Soft Emerald Highlight Nilai Akhir #ECFDF5 -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFECFDF5"/></patternFill></fill>
  <!-- 12: Light Green Foto & Official Badge #DCFCE7 -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFDCFCE7"/></patternFill></fill>
  <!-- 13: Soft Amber Jury Box Header #FFFBEB -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFFFFBEB"/></patternFill></fill>
  <!-- 14: White Fill #FFFFFF -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFFFFFFF"/></patternFill></fill>
  <!-- 15: Warning Red Fill #FEE2E2 -->
  <fill><patternFill patternType="solid"><fgColor rgb="FFFEE2E2"/></patternFill></fill>
</fills>
<borders count="7">
  <!-- 0: None -->
  <border><left/><right/><top/><bottom/><diagonal/></border>
  <!-- 1: Thin Gray All #CBD5E1 -->
  <border>
    <left style="thin"><color rgb="FFCBD5E1"/></left>
    <right style="thin"><color rgb="FFCBD5E1"/></right>
    <top style="thin"><color rgb="FFCBD5E1"/></top>
    <bottom style="thin"><color rgb="FFCBD5E1"/></bottom>
    <diagonal/>
  </border>
  <!-- 2: Double Bottom Black (Kop Separator) -->
  <border>
    <left/><right/><top/>
    <bottom style="double"><color rgb="FF0F172A"/></bottom>
    <diagonal/>
  </border>
  <!-- 3: Medium Bottom Black (Signature Line) -->
  <border>
    <left/><right/><top/>
    <bottom style="medium"><color rgb="FF0F172A"/></bottom>
    <diagonal/>
  </border>
  <!-- 4: Green Pill Border #86EFAC -->
  <border>
    <left style="thin"><color rgb="FF86EFAC"/></left>
    <right style="thin"><color rgb="FF86EFAC"/></right>
    <top style="thin"><color rgb="FF86EFAC"/></top>
    <bottom style="thin"><color rgb="FF86EFAC"/></bottom>
    <diagonal/>
  </border>
  <!-- 5: Amber Pill Border #FCD34D -->
  <border>
    <left style="thin"><color rgb="FFFCD34D"/></left>
    <right style="thin"><color rgb="FFFCD34D"/></right>
    <top style="thin"><color rgb="FFFCD34D"/></top>
    <bottom style="thin"><color rgb="FFFCD34D"/></bottom>
    <diagonal/>
  </border>
  <!-- 6: Blue Pill Border #BFDBFE -->
  <border>
    <left style="thin"><color rgb="FFBFDBFE"/></left>
    <right style="thin"><color rgb="FFBFDBFE"/></right>
    <top style="thin"><color rgb="FFBFDBFE"/></top>
    <bottom style="thin"><color rgb="FFBFDBFE"/></bottom>
    <diagonal/>
  </border>
</borders>
<cellStyleXfs count="1">
  <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
</cellStyleXfs>
<cellXfs count="49">
  <!-- 0: Default Normal Left -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 1: Default Normal Center -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 2: Table Header Green Center -->
  <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
  <!-- 3: Table Header Dark Green Center (Nilai Akhir) -->
  <xf numFmtId="0" fontId="1" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
  <!-- 4: Table Header Gold Center (Peringkat) -->
  <xf numFmtId="0" fontId="2" fillId="4" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
  <!-- 5: Table Header Emerald Center (Juri Penilai) -->
  <xf numFmtId="0" fontId="1" fillId="5" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>
  <!-- 6: Kop Red Left -->
  <xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 7: Kop Title Left -->
  <xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 8: Kop Subtitle Left -->
  <xf numFmtId="0" fontId="5" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 9: Kop Double Border Bottom -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="2" xfId="0" applyBorder="1"/>
  <!-- 10: Official Badge Pill Right -->
  <xf numFmtId="0" fontId="8" fillId="12" borderId="4" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 11: Official Print Info Right -->
  <xf numFmtId="0" fontId="10" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
  <!-- 12: Info Box Header Left -->
  <xf numFmtId="0" fontId="2" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 13: Jury Box Header Left -->
  <xf numFmtId="0" fontId="6" fillId="13" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 14: Jury Badge Pill Header Right -->
  <xf numFmtId="0" fontId="17" fillId="6" borderId="5" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 15: Box Label Right -->
  <xf numFmtId="0" fontId="10" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
  <!-- 16: Box Value Left -->
  <xf numFmtId="0" fontId="2" fillId="14" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 17: Box Value Blue Left -->
  <xf numFmtId="0" fontId="7" fillId="14" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 18: Box Badge Blue Pill Center (WIRA) -->
  <xf numFmtId="0" fontId="18" fillId="9" borderId="6" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 19: Box Badge Pill White Center (@username) -->
  <xf numFmtId="0" fontId="10" fillId="14" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 20: Juara 1 Center -->
  <xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 21: Juara 1 Left -->
  <xf numFmtId="0" fontId="6" fillId="6" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 22: Juara 2 Center -->
  <xf numFmtId="0" fontId="2" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 23: Juara 2 Left -->
  <xf numFmtId="0" fontId="2" fillId="7" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 24: Juara 3 Center -->
  <xf numFmtId="0" fontId="11" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 25: Juara 3 Left -->
  <xf numFmtId="0" fontId="11" fillId="8" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 26: Harapan Center -->
  <xf numFmtId="0" fontId="7" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 27: Harapan Left -->
  <xf numFmtId="0" fontId="7" fillId="9" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 28: Regular Nilai Praktik Blue Center -->
  <xf numFmtId="0" fontId="7" fillId="14" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 29: Regular Nilai Akhir Emerald Highlight Center -->
  <xf numFmtId="0" fontId="8" fillId="11" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 30: Zebra Nilai Praktik Blue Center -->
  <xf numFmtId="0" fontId="7" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 31: Zebra Nilai Akhir Emerald Highlight Center -->
  <xf numFmtId="0" fontId="8" fillId="11" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 32: Zebra Center -->
  <xf numFmtId="0" fontId="0" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 33: Zebra Left -->
  <xf numFmtId="0" fontId="0" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 34: Foto Badge Green Center -->
  <xf numFmtId="0" fontId="12" fillId="12" borderId="4" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 35: Catatan Juri Italic Left -->
  <xf numFmtId="0" fontId="9" fillId="14" borderId="1" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 36: Catatan Juri Italic Zebra Left -->
  <xf numFmtId="0" fontId="9" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="center"/></xf>
  <!-- 37: Stat Card Label Center -->
  <xf numFmtId="0" fontId="10" fillId="10" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 38: Stat Card Value Black Center -->
  <xf numFmtId="0" fontId="13" fillId="14" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 39: Stat Card Value Blue Center -->
  <xf numFmtId="0" fontId="14" fillId="14" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 40: Stat Card Value Green Center -->
  <xf numFmtId="0" fontId="15" fillId="14" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 41: Stat Card Value Orange Center -->
  <xf numFmtId="0" fontId="16" fillId="14" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 42: TTD Date Right -->
  <xf numFmtId="0" fontId="9" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="right" vertical="center"/></xf>
  <!-- 43: TTD Jabatan Center -->
  <xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 44: TTD Nama Center (Medium Bottom Border) -->
  <xf numFmtId="0" fontId="2" fillId="0" borderId="3" xfId="0" applyFont="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 45: TTD Sub Center -->
  <xf numFmtId="0" fontId="9" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 46: Blank Separator -->
  <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
  <!-- 47: Disqualified Center -->
  <xf numFmtId="0" fontId="3" fillId="15" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <!-- 48: Active Jury Status Emerald Center -->
  <xf numFmtId="0" fontId="8" fillId="11" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
</cellXfs>
<cellStyles count="1">
  <cellStyle name="Normal" xfId="0" builtinId="0"/>
</cellStyles>
</styleSheet>';
    }

    /**
     * XML Sheet 1: Lembar Nilai Utama (100% Match Visual Mockup)
     *
     * @param  array<string, mixed>  $meta
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function buildMainScoreSheetXml(array $meta, array $rows): string
    {
        // 1. Column Widths
        $colsXml = '<cols>';
        $cIdx = 1;
        foreach (self::SCORE_SHEET_COLUMNS as $colMeta) {
            $w = $colMeta['width'] ?? 20;
            $colsXml .= '<col min="'.$cIdx.'" max="'.$cIdx.'" width="'.$w.'" customWidth="1"/>';
            $cIdx++;
        }
        $colsXml .= '</cols>';

        // 2. Sheet Data
        $sheetDataXml = '<sheetData>';

        // Baris 1: Kop Red Title (Kiri A1:H1) & Badge Official Score Sheet (Kanan I1:L1)
        $sheetDataXml .= '<row r="1" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A1" s="6" t="inlineStr"><is><t>PALANG MERAH INDONESIA • PMR WIRA SMA NEGERI 1 CIAWI</t></is></c>';
        for ($c = 1; $c < 8; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'1" s="6"/>';
        }
        $sheetDataXml .= '<c r="I1" s="10" t="inlineStr"><is><t>OFFICIAL SCORE SHEET</t></is></c>';
        for ($c = 9; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'1" s="10"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 2: Kop Big Title (Kiri A2:H2) & Info Dicetak Sah (Kanan I2:L2)
        $sheetDataXml .= '<row r="2" ht="28" customHeight="1">';
        $sheetDataXml .= '<c r="A2" s="7" t="inlineStr"><is><t>LEMBAR PENILAIAN RESMI DEWAN JURI LAPANGAN</t></is></c>';
        for ($c = 1; $c < 8; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'2" s="7"/>';
        }
        $printInfo = 'Dokumen Sah Panitia • Dicetak: '.($meta['export_time'] ?? date('d/m/Y H:i').' WIB');
        $sheetDataXml .= '<c r="I2" s="11" t="inlineStr"><is><t>'.htmlspecialchars($printInfo, ENT_XML1).'</t></is></c>';
        for ($c = 9; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'2" s="11"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 3: Kop Subtitle Event (Kiri A3:H3) & Kanan (I3:L3)
        $sheetDataXml .= '<row r="3" ht="22" customHeight="1">';
        $safeEventTitle = htmlspecialchars($meta['event_title'] ?? 'LOMBA KEPALANGMERAHAN "SUA BHAKTI BERKARYA IV" TAHUN 2026', ENT_XML1);
        $sheetDataXml .= '<c r="A3" s="8" t="inlineStr"><is><t>'.$safeEventTitle.'</t></is></c>';
        for ($c = 1; $c < 8; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'3" s="8"/>';
        }
        for ($c = 8; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'3" s="46"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 4: Double border separator (A4:L4 tanpa perlu di-merge agar border ganda utuh di semua sel)
        $sheetDataXml .= '<row r="4" ht="8" customHeight="1">';
        for ($c = 0; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'4" s="9"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 5: Spacing blank
        $sheetDataXml .= '<row r="5" ht="8" customHeight="1">';
        for ($c = 0; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'5" s="46"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 6: Box Headers (A6:F6 Informasi Cabang Lomba, G6:J6 Dewan Juri, K6:L6 Badge Juri Terpetakan)
        $sheetDataXml .= '<row r="6" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A6" s="12" t="inlineStr"><is><t>ℹ️ INFORMASI CABANG MATA LOMBA:</t></is></c>';
        for ($c = 1; $c < 6; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'6" s="12"/>';
        }
        $sheetDataXml .= '<c r="G6" s="13" t="inlineStr"><is><t>⚖️ DEWAN JURI BERTUGAS:</t></is></c>';
        for ($c = 7; $c < 10; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'6" s="13"/>';
        }
        $juryBadgeText = ($meta['juries_count'] ?? 2).' JURI TERPETAKAN';
        $sheetDataXml .= '<c r="K6" s="14" t="inlineStr"><is><t>'.htmlspecialchars($juryBadgeText, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="L6" s="14"/>';
        $sheetDataXml .= '</row>';

        // Baris 7: Metadata Row 1 (Mata Lomba & Juri 1)
        $sheetDataXml .= '<row r="7" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A7" s="15" t="inlineStr"><is><t>Mata Lomba:</t></is></c>';
        $sheetDataXml .= '<c r="B7" s="15"/>';
        $sheetDataXml .= '<c r="C7" s="16" t="inlineStr"><is><t>'.htmlspecialchars($meta['category_name'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="D7" s="16"/>';
        $sheetDataXml .= '<c r="E7" s="16"/>';
        $sheetDataXml .= '<c r="F7" s="16"/>';
        $sheetDataXml .= '<c r="G7" s="14" t="inlineStr"><is><t>1</t></is></c>';
        $sheetDataXml .= '<c r="H7" s="16" t="inlineStr"><is><t>'.htmlspecialchars($meta['jury_1_name'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="I7" s="16"/>';
        $sheetDataXml .= '<c r="J7" s="16"/>';
        $jury1UserText = '@'.ltrim((string) ($meta['jury_1_user'] ?? 'juri1'), '@');
        $sheetDataXml .= '<c r="K7" s="19" t="inlineStr"><is><t>'.htmlspecialchars($jury1UserText, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="L7" s="19"/>';
        $sheetDataXml .= '</row>';

        // Baris 8: Metadata Row 2 (Kode & Babak, Juri 2)
        $sheetDataXml .= '<row r="8" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A8" s="15" t="inlineStr"><is><t>Kode &amp; Babak:</t></is></c>';
        $sheetDataXml .= '<c r="B8" s="15"/>';
        $sheetDataXml .= '<c r="C8" s="17" t="inlineStr"><is><t>'.htmlspecialchars($meta['code_round'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="D8" s="17"/>';
        $sheetDataXml .= '<c r="E8" s="17"/>';
        $sheetDataXml .= '<c r="F8" s="17"/>';
        $sheetDataXml .= '<c r="G8" s="14" t="inlineStr"><is><t>2</t></is></c>';
        $sheetDataXml .= '<c r="H8" s="16" t="inlineStr"><is><t>'.htmlspecialchars($meta['jury_2_name'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="I8" s="16"/>';
        $sheetDataXml .= '<c r="J8" s="16"/>';
        $jury2UserText = '@'.ltrim((string) ($meta['jury_2_user'] ?? 'juri2'), '@');
        $sheetDataXml .= '<c r="K8" s="19" t="inlineStr"><is><t>'.htmlspecialchars($jury2UserText, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="L8" s="19"/>';
        $sheetDataXml .= '</row>';

        // Baris 9: Metadata Row 3 (Tingkatan, Model Penilaian, Status Juri)
        $sheetDataXml .= '<row r="9" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A9" s="15" t="inlineStr"><is><t>Tingkatan:</t></is></c>';
        $sheetDataXml .= '<c r="B9" s="15"/>';
        $sheetDataXml .= '<c r="C9" s="18" t="inlineStr"><is><t>'.htmlspecialchars($meta['level_text'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="D9" s="15" t="inlineStr"><is><t>Model Penilaian:</t></is></c>';
        $sheetDataXml .= '<c r="E9" s="16" t="inlineStr"><is><t>'.htmlspecialchars($meta['scoring_model'] ?? '', ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="F9" s="16"/>';
        $sheetDataXml .= '<c r="G9" s="15" t="inlineStr"><is><t>Status Juri:</t></is></c>';
        $sheetDataXml .= '<c r="H9" s="15"/>';
        $sheetDataXml .= '<c r="I9" s="48" t="inlineStr"><is><t>Aktif &amp; Terverifikasi Menilai Lapangan</t></is></c>';
        $sheetDataXml .= '<c r="J9" s="48"/>';
        $sheetDataXml .= '<c r="K9" s="48"/>';
        $sheetDataXml .= '<c r="L9" s="48"/>';
        $sheetDataXml .= '</row>';

        // Baris 10: Spacing blank
        $sheetDataXml .= '<row r="10" ht="8" customHeight="1">';
        for ($c = 0; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'10" s="46"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 11: Main Column Headers (Hijau #107C41, Nilai Akhir Hijau Gelap #0B542B, Peringkat Gold #F59E0B)
        $sheetDataXml .= '<row r="11" ht="28" customHeight="1">';
        $colKeys = array_keys(self::SCORE_SHEET_COLUMNS);
        foreach ($colKeys as $cIdx => $colKey) {
            $colLetter = $this->indexToColumnLetter($cIdx);
            $metaCol = self::SCORE_SHEET_COLUMNS[$colKey];

            $sId = match ($colKey) {
                'H' => 3, // Nilai Akhir Dark Green
                'I' => 4, // Peringkat Gold
                'J' => 5, // Juri Penilai Emerald
                default => 2,
            };

            $safeLabel = htmlspecialchars($metaCol['label'], ENT_XML1);
            $sheetDataXml .= '<c r="'.$colLetter.'11" s="'.$sId.'" t="inlineStr"><is><t>'.$safeLabel.'</t></is></c>';
        }
        $sheetDataXml .= '</row>';

        // Baris 12+: Data Rows
        $currentRow = 12;
        foreach ($rows as $rIdx => $rowItem) {
            $isZebra = ($currentRow % 2 === 1);
            $rankNum = $rowItem['rank_num'] ?? null;
            $isDisq = $rowItem['is_disqualified'] ?? false;

            $tier = 'regular';
            if ($isDisq) {
                $tier = 'disqualified';
            } elseif ($rankNum === 1) {
                $tier = 'juara1';
            } elseif ($rankNum === 2) {
                $tier = 'juara2';
            } elseif ($rankNum === 3) {
                $tier = 'juara3';
            } elseif ($rankNum >= 4 && $rankNum <= 6) {
                $tier = 'harapan';
            }

            $sheetDataXml .= '<row r="'.$currentRow.'" ht="24" customHeight="1">';

            // A: No Urut
            $styleCenter = match ($tier) {
                'juara1' => 20,
                'juara2' => 22,
                'juara3' => 24,
                'harapan' => 26,
                'disqualified' => 47,
                default => ($isZebra ? 32 : 1),
            };
            $sheetDataXml .= '<c r="A'.$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['order_number'], ENT_XML1).'</t></is></c>';

            // B: Kode Reg (Bold Center)
            $sheetDataXml .= '<c r="B'.$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['reg_code'], ENT_XML1).'</t></is></c>';

            // C: Asal Sekolah (Left Bold)
            $styleLeft = match ($tier) {
                'juara1' => 21,
                'juara2' => 23,
                'juara3' => 25,
                'harapan' => 27,
                default => ($isZebra ? 33 : 0),
            };
            $sheetDataXml .= '<c r="C'.$currentRow.'" s="'.$styleLeft.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['school_name'], ENT_XML1).'</t></is></c>';

            // D: Nama Regu (Left)
            $sheetDataXml .= '<c r="D'.$currentRow.'" s="'.$styleLeft.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['team_name'], ENT_XML1).'</t></is></c>';

            // E: Nilai Praktik (FONT BIRU BOLD #1D4ED8)
            $stylePractical = match ($tier) {
                'juara1' => 20,
                'juara2' => 22,
                'juara3' => 24,
                'harapan' => 26,
                default => ($isZebra ? 30 : 28),
            };
            $sheetDataXml .= '<c r="E'.$currentRow.'" s="'.$stylePractical.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['practical_score'], ENT_XML1).'</t></is></c>';

            // F: Waktu MM:SS
            $sheetDataXml .= '<c r="F'.$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['time_recorded'], ENT_XML1).'</t></is></c>';

            // G: Poin Waktu
            $sheetDataXml .= '<c r="G'.$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['time_score'], ENT_XML1).'</t></is></c>';

            // H: Nilai Akhir (FONT HIJAU BOLD #047857 & SOFT GREEN BG #ECFDF5)
            $styleFinal = match ($tier) {
                'juara1' => 20,
                'juara2' => 22,
                'juara3' => 24,
                default => ($isZebra ? 31 : 29),
            };
            $sheetDataXml .= '<c r="H'.$currentRow.'" s="'.$styleFinal.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['final_score'], ENT_XML1).'</t></is></c>';

            // I: Peringkat (Pill Color Emas, Perak, Perunggu, Soft Blue)
            $sheetDataXml .= '<c r="I'.$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['rank_label'], ENT_XML1).'</t></is></c>';

            // J: Juri Penilai
            $sheetDataXml .= '<c r="J'.$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['jury_name'], ENT_XML1).'</t></is></c>';

            // K: Foto (Badge Pill Hijau Muda #DCFCE7)
            $sheetDataXml .= '<c r="K'.$currentRow.'" s="34" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['photo_text'], ENT_XML1).'</t></is></c>';

            // L: Catatan Juri (Italic Gray)
            $styleNotes = ($isZebra ? 36 : 35);
            $sheetDataXml .= '<c r="L'.$currentRow.'" s="'.$styleNotes.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $rowItem['notes'], ENT_XML1).'</t></is></c>';

            $sheetDataXml .= '</row>';
            $currentRow++;
        }

        // Baris Spasi Setelah Tabel
        $sheetDataXml .= '<row r="'.$currentRow.'" ht="10" customHeight="1">';
        for ($c = 0; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$currentRow.'" s="46"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Ringkasan Statistik: 4 CARD BERDAMPINGAN (2 Baris: Label & Nilai Utama)
        $statLabelRow = $currentRow;
        $sheetDataXml .= '<row r="'.$statLabelRow.'" ht="18" customHeight="1">';
        // Card 1 Label (A:C)
        $sheetDataXml .= '<c r="A'.$statLabelRow.'" s="37" t="inlineStr"><is><t>TOTAL PESERTA TAMPIL:</t></is></c>';
        $sheetDataXml .= '<c r="B'.$statLabelRow.'" s="37"/>';
        $sheetDataXml .= '<c r="C'.$statLabelRow.'" s="37"/>';
        // Card 2 Label (D:F)
        $sheetDataXml .= '<c r="D'.$statLabelRow.'" s="37" t="inlineStr"><is><t>RATA-RATA NILAI AKHIR:</t></is></c>';
        $sheetDataXml .= '<c r="E'.$statLabelRow.'" s="37"/>';
        $sheetDataXml .= '<c r="F'.$statLabelRow.'" s="37"/>';
        // Card 3 Label (G:I)
        $sheetDataXml .= '<c r="G'.$statLabelRow.'" s="37" t="inlineStr"><is><t>NILAI TERTINGGI:</t></is></c>';
        $sheetDataXml .= '<c r="H'.$statLabelRow.'" s="37"/>';
        $sheetDataXml .= '<c r="I'.$statLabelRow.'" s="37"/>';
        // Card 4 Label (J:L)
        $sheetDataXml .= '<c r="J'.$statLabelRow.'" s="37" t="inlineStr"><is><t>WAKTU TERCEPAT:</t></is></c>';
        $sheetDataXml .= '<c r="K'.$statLabelRow.'" s="37"/>';
        $sheetDataXml .= '<c r="L'.$statLabelRow.'" s="37"/>';
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Nilai Statistik Utama
        $statValRow = $currentRow;
        $sheetDataXml .= '<row r="'.$statValRow.'" ht="26" customHeight="1">';
        // Card 1 Value (A:C) - Hitam Bold
        $sheetDataXml .= '<c r="A'.$statValRow.'" s="38" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_total_teams'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="B'.$statValRow.'" s="38"/>';
        $sheetDataXml .= '<c r="C'.$statValRow.'" s="38"/>';
        // Card 2 Value (D:F) - Biru Bold #1D4ED8
        $sheetDataXml .= '<c r="D'.$statValRow.'" s="39" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_avg_score'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="E'.$statValRow.'" s="39"/>';
        $sheetDataXml .= '<c r="F'.$statValRow.'" s="39"/>';
        // Card 3 Value (G:I) - Hijau Bold #047857
        $sheetDataXml .= '<c r="G'.$statValRow.'" s="40" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_top_score'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="H'.$statValRow.'" s="40"/>';
        $sheetDataXml .= '<c r="I'.$statValRow.'" s="40"/>';
        // Card 4 Value (J:L) - Oranye Bold #D97706
        $sheetDataXml .= '<c r="J'.$statValRow.'" s="41" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_fastest_time'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="K'.$statValRow.'" s="41"/>';
        $sheetDataXml .= '<c r="L'.$statValRow.'" s="41"/>';
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Spasi Sebelum TTD
        $sheetDataXml .= '<row r="'.$currentRow.'" ht="16" customHeight="1">';
        for ($c = 0; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$currentRow.'" s="46"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Tanggal Pengesahan (I:L)
        $ttdDateRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdDateRow.'" ht="20" customHeight="1">';
        for ($c = 0; $c < 8; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdDateRow.'" s="46"/>';
        }
        $dateText = 'Bogor, '.($meta['export_date'] ?? date('d F Y'));
        $sheetDataXml .= '<c r="I'.$ttdDateRow.'" s="42" t="inlineStr"><is><t>'.htmlspecialchars($dateText, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="J'.$ttdDateRow.'" s="42"/>';
        $sheetDataXml .= '<c r="K'.$ttdDateRow.'" s="42"/>';
        $sheetDataXml .= '<c r="L'.$ttdDateRow.'" s="42"/>';
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Jabatan TTD
        $ttdPosRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdPosRow.'" ht="22" customHeight="1">';
        // Juri I (A:C)
        $sheetDataXml .= '<c r="A'.$ttdPosRow.'" s="43" t="inlineStr"><is><t>Dewan Juri I,</t></is></c>';
        $sheetDataXml .= '<c r="B'.$ttdPosRow.'" s="43"/>';
        $sheetDataXml .= '<c r="C'.$ttdPosRow.'" s="43"/>';
        // Juri II (D:E)
        $sheetDataXml .= '<c r="D'.$ttdPosRow.'" s="43" t="inlineStr"><is><t>Dewan Juri II,</t></is></c>';
        $sheetDataXml .= '<c r="E'.$ttdPosRow.'" s="43"/>';
        // Koordinator (F:H)
        $sheetDataXml .= '<c r="F'.$ttdPosRow.'" s="43" t="inlineStr"><is><t>Koordinator Penjurian,</t></is></c>';
        $sheetDataXml .= '<c r="G'.$ttdPosRow.'" s="43"/>';
        $sheetDataXml .= '<c r="H'.$ttdPosRow.'" s="43"/>';
        // Ketua Panitia (I:L)
        $sheetDataXml .= '<c r="I'.$ttdPosRow.'" s="43" t="inlineStr"><is><t>Ketua Panitia Pelaksana,</t></is></c>';
        $sheetDataXml .= '<c r="J'.$ttdPosRow.'" s="43"/>';
        $sheetDataXml .= '<c r="K'.$ttdPosRow.'" s="43"/>';
        $sheetDataXml .= '<c r="L'.$ttdPosRow.'" s="43"/>';
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Spasi TTD
        $ttdSpaceRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdSpaceRow.'" ht="48" customHeight="1">';
        for ($c = 0; $c < 12; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdSpaceRow.'" s="46"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Nama TTD (Underline / Medium Bottom Border)
        $ttdNameRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdNameRow.'" ht="22" customHeight="1">';
        // Juri I (A:C)
        $sheetDataXml .= '<c r="A'.$ttdNameRow.'" s="44" t="inlineStr"><is><t>( '.htmlspecialchars((string) $meta['jury_1_name'], ENT_XML1).' )</t></is></c>';
        $sheetDataXml .= '<c r="B'.$ttdNameRow.'" s="44"/>';
        $sheetDataXml .= '<c r="C'.$ttdNameRow.'" s="44"/>';
        // Juri II (D:E)
        $sheetDataXml .= '<c r="D'.$ttdNameRow.'" s="44" t="inlineStr"><is><t>( '.htmlspecialchars((string) $meta['jury_2_name'], ENT_XML1).' )</t></is></c>';
        $sheetDataXml .= '<c r="E'.$ttdNameRow.'" s="44"/>';
        // Koordinator (F:H)
        $sheetDataXml .= '<c r="F'.$ttdNameRow.'" s="44" t="inlineStr"><is><t>( Drs. H. Mulyadi, M.Pd )</t></is></c>';
        $sheetDataXml .= '<c r="G'.$ttdNameRow.'" s="44"/>';
        $sheetDataXml .= '<c r="H'.$ttdNameRow.'" s="44"/>';
        // Ketua Panitia (I:L)
        $sheetDataXml .= '<c r="I'.$ttdNameRow.'" s="44" t="inlineStr"><is><t>( Muhammad Dwi Pantara )</t></is></c>';
        $sheetDataXml .= '<c r="J'.$ttdNameRow.'" s="44"/>';
        $sheetDataXml .= '<c r="K'.$ttdNameRow.'" s="44"/>';
        $sheetDataXml .= '<c r="L'.$ttdNameRow.'" s="44"/>';
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Subtitle TTD
        $ttdSubRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdSubRow.'" ht="18" customHeight="1">';
        // Juri I (A:C)
        $sheetDataXml .= '<c r="A'.$ttdSubRow.'" s="45" t="inlineStr"><is><t>Juri Teknis Lapangan • @'.htmlspecialchars(ltrim((string) $meta['jury_1_user'], '@'), ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="B'.$ttdSubRow.'" s="45"/>';
        $sheetDataXml .= '<c r="C'.$ttdSubRow.'" s="45"/>';
        // Juri II (D:E)
        $sheetDataXml .= '<c r="D'.$ttdSubRow.'" s="45" t="inlineStr"><is><t>Juri Medis &amp; Tandu • @'.htmlspecialchars(ltrim((string) $meta['jury_2_user'], '@'), ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="E'.$ttdSubRow.'" s="45"/>';
        // Koordinator (F:H)
        $sheetDataXml .= '<c r="F'.$ttdSubRow.'" s="45" t="inlineStr"><is><t>Pembina PMR Wira SMAN 1 Ciawi</t></is></c>';
        $sheetDataXml .= '<c r="G'.$ttdSubRow.'" s="45"/>';
        $sheetDataXml .= '<c r="H'.$ttdSubRow.'" s="45"/>';
        // Ketua Panitia (I:L)
        $sheetDataXml .= '<c r="I'.$ttdSubRow.'" s="45" t="inlineStr"><is><t>Ketua PMR Wira SMAN 1 Ciawi</t></is></c>';
        $sheetDataXml .= '<c r="J'.$ttdSubRow.'" s="45"/>';
        $sheetDataXml .= '<c r="K'.$ttdSubRow.'" s="45"/>';
        $sheetDataXml .= '<c r="L'.$ttdSubRow.'" s="45"/>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '</sheetData>';

        // 3. Merge Cells Sesuai Mockup
        $mergeCells = [
            'A1:H1',
            'I1:L1',
            'A2:H2',
            'I2:L2',
            'A3:H3',
            'I3:L3',
            'A6:F6',
            'G6:J6',
            'K6:L6',
            'A7:B7',
            'C7:F7',
            'H7:J7',
            'K7:L7',
            'A8:B8',
            'C8:F8',
            'H8:J8',
            'K8:L8',
            'A9:B9',
            'E9:F9',
            'G9:H9',
            'I9:L9',
            'A'.$statLabelRow.':C'.$statLabelRow,
            'D'.$statLabelRow.':F'.$statLabelRow,
            'G'.$statLabelRow.':I'.$statLabelRow,
            'J'.$statLabelRow.':L'.$statLabelRow,
            'A'.$statValRow.':C'.$statValRow,
            'D'.$statValRow.':F'.$statValRow,
            'G'.$statValRow.':I'.$statValRow,
            'J'.$statValRow.':L'.$statValRow,
            'I'.$ttdDateRow.':L'.$ttdDateRow,
            'A'.$ttdPosRow.':C'.$ttdPosRow,
            'D'.$ttdPosRow.':E'.$ttdPosRow,
            'F'.$ttdPosRow.':H'.$ttdPosRow,
            'I'.$ttdPosRow.':L'.$ttdPosRow,
            'A'.$ttdNameRow.':C'.$ttdNameRow,
            'D'.$ttdNameRow.':E'.$ttdNameRow,
            'F'.$ttdNameRow.':H'.$ttdNameRow,
            'I'.$ttdNameRow.':L'.$ttdNameRow,
            'A'.$ttdSubRow.':C'.$ttdSubRow,
            'D'.$ttdSubRow.':E'.$ttdSubRow,
            'F'.$ttdSubRow.':H'.$ttdSubRow,
            'I'.$ttdSubRow.':L'.$ttdSubRow,
        ];

        $mergeCellsXml = '<mergeCells count="'.count($mergeCells).'">';
        foreach ($mergeCells as $mRange) {
            $mergeCellsXml .= '<mergeCell ref="'.$mRange.'"/>';
        }
        $mergeCellsXml .= '</mergeCells>';

        // 4. Worksheet XML with Frozen Pane at Row 11 & proper selection
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews>
<sheetView tabSelected="1" workbookViewId="0">
<pane ySplit="11" topLeftCell="A12" activePane="bottomLeft" state="frozen"/>
<selection pane="bottomLeft" activeCell="A12" sqref="A12"/>
</sheetView>
</sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
'.$mergeCellsXml.'
</worksheet>';
    }

    /**
     * XML Sheet 2: Rekap Juara Umum
     *
     * @param  array<string, mixed>  $meta
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function buildGeneralChampionSheetXml(array $meta, array $rows): string
    {
        $colsXml = '<cols>
<col min="1" max="1" width="8" customWidth="1"/>
<col min="2" max="2" width="18" customWidth="1"/>
<col min="3" max="3" width="16" customWidth="1"/>
<col min="4" max="4" width="34" customWidth="1"/>
<col min="5" max="5" width="22" customWidth="1"/>
<col min="6" max="6" width="16" customWidth="1"/>
<col min="7" max="7" width="28" customWidth="1"/>
<col min="8" max="8" width="25" customWidth="1"/>
</cols>';

        $sheetDataXml = '<sheetData>';

        // Title
        $sheetDataXml .= '<row r="1" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A1" s="6" t="inlineStr"><is><t>PALANG MERAH INDONESIA • PMR WIRA SMA NEGERI 1 CIAWI</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="2" ht="28" customHeight="1">';
        $sheetDataXml .= '<c r="A2" s="7" t="inlineStr"><is><t>REKAPITULASI PEROLEHAN JUARA &amp; TIKET JUARA UMUM</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="3" ht="20" customHeight="1">';
        $sub = 'Cabang Mata Lomba: '.($meta['category_name'] ?? '').' • Tingkat '.($meta['level_text'] ?? '');
        $sheetDataXml .= '<c r="A3" s="8" t="inlineStr"><is><t>'.htmlspecialchars($sub, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="4" ht="8" customHeight="1">';
        for ($c = 0; $c < 8; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'4" s="9"/>';
        }
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="5" ht="12" customHeight="1"/>';

        // Headers
        $sheetDataXml .= '<row r="6" ht="26" customHeight="1">';
        $headers = ['NO.', 'PERINGKAT', 'NO. REG', 'SEKOLAH / PANGKALAN', 'NAMA TIM', 'NILAI AKHIR', 'PENGHARGAAN / TROPHY', 'KETERANGAN'];
        foreach ($headers as $hIdx => $hText) {
            $sId = ($hIdx === 5 ? 3 : ($hIdx === 1 ? 4 : 2));
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($hIdx).'6" s="'.$sId.'" t="inlineStr"><is><t>'.htmlspecialchars($hText, ENT_XML1).'</t></is></c>';
        }
        $sheetDataXml .= '</row>';

        // Tampilkan 6 Besar (Juara 1 s.d. Harapan 3)
        $cRow = 7;
        $trophyMap = [
            1 => 'Trophy Juara 1 + Piagam Emas',
            2 => 'Trophy Juara 2 + Piagam Perak',
            3 => 'Trophy Juara 3 + Piagam Perunggu',
            4 => 'Piagam Penghargaan Harapan 1',
            5 => 'Piagam Penghargaan Harapan 2',
            6 => 'Piagam Penghargaan Harapan 3',
        ];

        $rankCount = 0;
        foreach ($rows as $item) {
            $rankNum = $item['rank_num'] ?? null;
            if ($rankNum && $rankNum <= 6) {
                $rankCount++;
                $styleCenter = match ($rankNum) {
                    1 => 20,
                    2 => 22,
                    3 => 24,
                    default => 26,
                };
                $styleLeft = match ($rankNum) {
                    1 => 21,
                    2 => 23,
                    3 => 25,
                    default => 27,
                };
                $trophy = $trophyMap[$rankNum] ?? 'Piagam Partisipasi';

                $sheetDataXml .= '<row r="'.$cRow.'" ht="24" customHeight="1">';
                $sheetDataXml .= '<c r="A'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.$rankNum.'</t></is></c>';
                $sheetDataXml .= '<c r="B'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $item['rank_label'], ENT_XML1).'</t></is></c>';
                $sheetDataXml .= '<c r="C'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $item['reg_code'], ENT_XML1).'</t></is></c>';
                $sheetDataXml .= '<c r="D'.$cRow.'" s="'.$styleLeft.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $item['school_name'], ENT_XML1).'</t></is></c>';
                $sheetDataXml .= '<c r="E'.$cRow.'" s="'.$styleLeft.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $item['team_name'], ENT_XML1).'</t></is></c>';
                $sheetDataXml .= '<c r="F'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $item['final_score'], ENT_XML1).'</t></is></c>';
                $sheetDataXml .= '<c r="G'.$cRow.'" s="'.$styleLeft.'" t="inlineStr"><is><t>'.htmlspecialchars($trophy, ENT_XML1).'</t></is></c>';
                $sheetDataXml .= '<c r="H'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>Sah Dewan Juri</t></is></c>';
                $sheetDataXml .= '</row>';
                $cRow++;
            }
        }

        if ($rankCount === 0) {
            $sheetDataXml .= '<row r="7" ht="24" customHeight="1">';
            $sheetDataXml .= '<c r="A7" s="1" t="inlineStr"><is><t>Belum ada data nilai terkunci untuk penetapan juara</t></is></c>';
            for ($c = 1; $c < 8; $c++) {
                $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'7" s="1"/>';
            }
            $sheetDataXml .= '</row>';
            $cRow = 8;
        }

        $sheetDataXml .= '</sheetData>';

        $merge = ['A1:H1', 'A2:H2', 'A3:H3', 'A4:H4'];
        if ($rankCount === 0) {
            $merge[] = 'A7:H7';
        }
        $mergeXml = '<mergeCells count="'.count($merge).'">';
        foreach ($merge as $m) {
            $mergeXml .= '<mergeCell ref="'.$m.'"/>';
        }
        $mergeXml .= '</mergeCells>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews><sheetView workbookViewId="0"/></sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
'.$mergeXml.'
</worksheet>';
    }

    /**
     * XML Sheet 3: Daftar Dewan Juri Bertugas
     *
     * @param  array<string, mixed>  $meta
     */
    protected function buildJuryListSheetXml(array $meta): string
    {
        $colsXml = '<cols>
<col min="1" max="1" width="8" customWidth="1"/>
<col min="2" max="2" width="30" customWidth="1"/>
<col min="3" max="3" width="20" customWidth="1"/>
<col min="4" max="4" width="25" customWidth="1"/>
<col min="5" max="5" width="25" customWidth="1"/>
<col min="6" max="6" width="25" customWidth="1"/>
<col min="7" max="7" width="20" customWidth="1"/>
</cols>';

        $sheetDataXml = '<sheetData>';

        // Title
        $sheetDataXml .= '<row r="1" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A1" s="6" t="inlineStr"><is><t>PALANG MERAH INDONESIA • PMR WIRA SMA NEGERI 1 CIAWI</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="2" ht="28" customHeight="1">';
        $sheetDataXml .= '<c r="A2" s="7" t="inlineStr"><is><t>DAFTAR RESMI DEWAN JURI PENILAI LAPANGAN</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="3" ht="20" customHeight="1">';
        $sub = 'Pemetaan Tugas Juri: '.($meta['category_name'] ?? '').' • Kode: '.($meta['code_round'] ?? '');
        $sheetDataXml .= '<c r="A3" s="8" t="inlineStr"><is><t>'.htmlspecialchars($sub, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="4" ht="8" customHeight="1">';
        for ($c = 0; $c < 7; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'4" s="9"/>';
        }
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="5" ht="12" customHeight="1"/>';

        // Headers
        $sheetDataXml .= '<row r="6" ht="26" customHeight="1">';
        $headers = ['NO.', 'NAMA LENGKAP & GELAR', 'USERNAME AKUN', 'BIDANG PENJURIAN', 'MATA LOMBA DITUGASKAN', 'STATUS AKUN', 'TANDA TANGAN'];
        foreach ($headers as $hIdx => $hText) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($hIdx).'6" s="2" t="inlineStr"><is><t>'.htmlspecialchars($hText, ENT_XML1).'</t></is></c>';
        }
        $sheetDataXml .= '</row>';

        // Juri 1
        $sheetDataXml .= '<row r="7" ht="26" customHeight="1">';
        $sheetDataXml .= '<c r="A7" s="1" t="inlineStr"><is><t>1</t></is></c>';
        $sheetDataXml .= '<c r="B7" s="16" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['jury_1_name'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="C7" s="19" t="inlineStr"><is><t>@'.htmlspecialchars(ltrim((string) $meta['jury_1_user'], '@'), ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="D7" s="0" t="inlineStr"><is><t>Juri Teknis Lapangan</t></is></c>';
        $sheetDataXml .= '<c r="E7" s="0" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['category_name'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="F7" s="48" t="inlineStr"><is><t>Aktif &amp; Terverifikasi</t></is></c>';
        $sheetDataXml .= '<c r="G7" s="1" t="inlineStr"><is><t>[ Terlampir di Dokumen ]</t></is></c>';
        $sheetDataXml .= '</row>';

        // Juri 2
        $sheetDataXml .= '<row r="8" ht="26" customHeight="1">';
        $sheetDataXml .= '<c r="A8" s="1" t="inlineStr"><is><t>2</t></is></c>';
        $sheetDataXml .= '<c r="B8" s="16" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['jury_2_name'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="C8" s="19" t="inlineStr"><is><t>@'.htmlspecialchars(ltrim((string) $meta['jury_2_user'], '@'), ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="D8" s="0" t="inlineStr"><is><t>Juri Medis &amp; Tandu</t></is></c>';
        $sheetDataXml .= '<c r="E8" s="0" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['category_name'], ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '<c r="F8" s="48" t="inlineStr"><is><t>Aktif &amp; Terverifikasi</t></is></c>';
        $sheetDataXml .= '<c r="G8" s="1" t="inlineStr"><is><t>[ Terlampir di Dokumen ]</t></is></c>';
        $sheetDataXml .= '</row>';

        // Koordinator
        $sheetDataXml .= '<row r="9" ht="26" customHeight="1">';
        $sheetDataXml .= '<c r="A9" s="1" t="inlineStr"><is><t>3</t></is></c>';
        $sheetDataXml .= '<c r="B9" s="16" t="inlineStr"><is><t>Drs. H. Mulyadi, M.Pd</t></is></c>';
        $sheetDataXml .= '<c r="C9" s="19" t="inlineStr"><is><t>@pembina_pmr</t></is></c>';
        $sheetDataXml .= '<c r="D9" s="0" t="inlineStr"><is><t>Koordinator Penjurian</t></is></c>';
        $sheetDataXml .= '<c r="E9" s="0" t="inlineStr"><is><t>Seluruh Cabang Lomba</t></is></c>';
        $sheetDataXml .= '<c r="F9" s="48" t="inlineStr"><is><t>Penanggung Jawab Teknis</t></is></c>';
        $sheetDataXml .= '<c r="G9" s="1" t="inlineStr"><is><t>[ Terlampir di Dokumen ]</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '</sheetData>';

        $merge = ['A1:G1', 'A2:G2', 'A3:G3', 'A4:G4'];
        $mergeXml = '<mergeCells count="'.count($merge).'">';
        foreach ($merge as $m) {
            $mergeXml .= '<mergeCell ref="'.$m.'"/>';
        }
        $mergeXml .= '</mergeCells>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews><sheetView workbookViewId="0"/></sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
'.$mergeXml.'
</worksheet>';
    }

    /**
     * Hasilkan file Excel (.xlsx) Rekapitulasi Poin & Klasemen Akhir Juara Umum sama persis dengan mockup visual
     */
    public function exportLeaderboardStandingsXlsx(?CompetitionEvent $event, string $level, Collection $categories, Collection $standings): string
    {
        $eventName = $event?->name ?? 'SUA BHAKTI BERKARYA IV';
        $eventYear = $event?->start_date ? Carbon::parse($event->start_date)->format('Y') : date('Y');

        $levelText = match (strtolower($level)) {
            'mula' => 'PMR MULA (SD/MI)',
            'madya' => 'PMR MADYA (SMP/MTs)',
            'wira' => 'PMR WIRA (SMA/SMK/MA)',
            default => 'PMR '.ucwords($level),
        };

        $totalSchools = $standings->count();
        $totalCategories = $categories->count();
        $totalMedals = $totalCategories * 3;

        $topSchool = $standings->first();
        $topScoreText = $topSchool ? $topSchool['total_points'].' Pts ('.$topSchool['school_name'].')' : '0 Pts';
        $avgScore = $totalSchools > 0 ? number_format($standings->avg('total_points'), 1).' Pts' : '0 Pts';

        $meta = [
            'event_title' => 'LOMBA KEPALANGMERAHAN "'.$eventName.'" TAHUN '.$eventYear,
            'level' => $level,
            'level_text' => $levelText,
            'categories_count' => $totalCategories,
            'schools_count' => $totalSchools,
            'export_date' => Carbon::now()->translatedFormat('d F Y'),
            'export_time' => Carbon::now()->format('d/m/Y H:i').' WIB',
            'stat_total_schools' => $totalSchools.' Pangkalan',
            'stat_total_medals' => $totalMedals.' Trophy Juara',
            'stat_top_points' => $topScoreText,
            'stat_avg_points' => $avgScore,
        ];

        return $this->createLeaderboardXlsxFile($meta, $categories, $standings);
    }

    /**
     * Bangun file Excel OpenXML XLSX Rekapitulasi Klasemen Juara Umum
     */
    protected function createLeaderboardXlsxFile(array $meta, Collection $categories, Collection $standings): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'pmr_leaderboard_');
        $zip = new ZipArchive;
        $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // 1. Content Types (3 Worksheets: Klasemen Juara Umum, Distribusi Medali Lomba, Daftar Kontingen Sekolah)
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
<Override PartName="/xl/worksheets/sheet3.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
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
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet3.xml"/>
<Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. Workbook with 3 tabs matching mockup
        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<workbookPr date1904="false"/>
<bookViews>
<workbookView activeTab="0"/>
</bookViews>
<sheets>
<sheet name="Klasemen Juara Umum" sheetId="1" r:id="rId1"/>
<sheet name="Distribusi Medali Lomba" sheetId="2" r:id="rId2"/>
<sheet name="Daftar Kontingen Sekolah" sheetId="3" r:id="rId3"/>
</sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $workbook);

        // 5. Styles (gunakan buildScoreSheetStylesXml yang sudah lengkap dan teruji)
        $zip->addFromString('xl/styles.xml', $this->buildScoreSheetStylesXml());

        // 6. Sheet 1: Klasemen Juara Umum
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildMainLeaderboardSheetXml($meta, $categories, $standings));

        // 7. Sheet 2: Distribusi Medali Lomba
        $zip->addFromString('xl/worksheets/sheet2.xml', $this->buildCategoryMedalDistributionSheetXml($meta, $categories));

        // 8. Sheet 3: Daftar Kontingen Sekolah
        $zip->addFromString('xl/worksheets/sheet3.xml', $this->buildLeaderboardSchoolListSheetXml($meta, $standings));

        $zip->close();

        return $tempFile;
    }

    /**
     * Bangun XML Sheet 1: Klasemen Juara Umum (Sama persis 100% dengan Mockup Visual)
     */
    protected function buildMainLeaderboardSheetXml(array $meta, Collection $categories, Collection $standings): string
    {
        $catCount = $categories->count();
        // Kolom: Rank (0), Sekolah (1), Kategori (2 s.d. 2+catCount-1), Emas, Perak, Perunggu, Total Poin, Status Prestasi
        // Total Kolom = 2 + catCount + 3 + 1 + 1 = catCount + 7
        $totalCols = $catCount + 7;
        $lastColIdx = $totalCols - 1;
        $lastColLetter = $this->indexToColumnLetter($lastColIdx);

        // Titik tengah untuk Kop & Box
        $midColIdx = max(3, (int) floor($totalCols / 2));
        $midColLetter = $this->indexToColumnLetter($midColIdx - 1);
        $nextColLetter = $this->indexToColumnLetter($midColIdx);

        // 1. Column Widths
        $colsXml = '<cols>';
        $colsXml .= '<col min="1" max="1" width="8" customWidth="1"/>'; // A: Rank
        $colsXml .= '<col min="2" max="2" width="34" customWidth="1"/>'; // B: Nama Sekolah
        $cMin = 3;
        foreach ($categories as $cat) {
            $colsXml .= '<col min="'.$cMin.'" max="'.$cMin.'" width="14" customWidth="1"/>';
            $cMin++;
        }
        $colsXml .= '<col min="'.$cMin.'" max="'.$cMin.'" width="9" customWidth="1"/>'; // Emas
        $cMin++;
        $colsXml .= '<col min="'.$cMin.'" max="'.$cMin.'" width="9" customWidth="1"/>'; // Perak
        $cMin++;
        $colsXml .= '<col min="'.$cMin.'" max="'.$cMin.'" width="9" customWidth="1"/>'; // Perunggu
        $cMin++;
        $colsXml .= '<col min="'.$cMin.'" max="'.$cMin.'" width="16" customWidth="1"/>'; // Total Poin
        $cMin++;
        $colsXml .= '<col min="'.$cMin.'" max="'.$cMin.'" width="24" customWidth="1"/>'; // Status Prestasi
        $colsXml .= '</cols>';

        $sheetDataXml = '<sheetData>';

        // Baris 1: Kop Red Title (Kiri) & Badge Official Leaderboard Sheet (Kanan)
        $sheetDataXml .= '<row r="1" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A1" s="6" t="inlineStr"><is><t>PALANG MERAH INDONESIA • PMR WIRA SMA NEGERI 1 CIAWI</t></is></c>';
        for ($c = 1; $c < $midColIdx; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'1" s="6"/>';
        }
        $sheetDataXml .= '<c r="'.$nextColLetter.'1" s="10" t="inlineStr"><is><t>OFFICIAL LEADERBOARD SHEET</t></is></c>';
        for ($c = $midColIdx + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'1" s="10"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 2: Kop Big Title (Kiri) & Info Dicetak Sah (Kanan)
        $sheetDataXml .= '<row r="2" ht="28" customHeight="1">';
        $sheetDataXml .= '<c r="A2" s="7" t="inlineStr"><is><t>REKAPITULASI POIN &amp; KLASEMEN AKHIR JUARA UMUM</t></is></c>';
        for ($c = 1; $c < $midColIdx; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'2" s="7"/>';
        }
        $printInfo = 'Dokumen Sah Panitia • Dicetak: '.($meta['export_time'] ?? date('d/m/Y H:i').' WIB');
        $sheetDataXml .= '<c r="'.$nextColLetter.'2" s="11" t="inlineStr"><is><t>'.htmlspecialchars($printInfo, ENT_XML1).'</t></is></c>';
        for ($c = $midColIdx + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'2" s="11"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 3: Kop Subtitle Event (Kiri)
        $sheetDataXml .= '<row r="3" ht="22" customHeight="1">';
        $safeEventTitle = htmlspecialchars($meta['event_title'].' • TINGKAT '.$meta['level_text'], ENT_XML1);
        $sheetDataXml .= '<c r="A3" s="8" t="inlineStr"><is><t>'.$safeEventTitle.'</t></is></c>';
        for ($c = 1; $c < $midColIdx; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'3" s="8"/>';
        }
        for ($c = $midColIdx; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'3" s="46"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 4: Double border separator
        $sheetDataXml .= '<row r="4" ht="8" customHeight="1">';
        for ($c = 0; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'4" s="9"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 5: Spacing blank
        $sheetDataXml .= '<row r="5" ht="8" customHeight="1">';
        for ($c = 0; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'5" s="46"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 6: Box Headers (Informasi Tingkatan di Kiri, Sistem Poin di Kanan)
        $sheetDataXml .= '<row r="6" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A6" s="12" t="inlineStr"><is><t>ℹ️ INFORMASI TINGKATAN &amp; EVENT:</t></is></c>';
        for ($c = 1; $c < $midColIdx; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'6" s="12"/>';
        }
        $sheetDataXml .= '<c r="'.$nextColLetter.'6" s="13" t="inlineStr"><is><t>🏆 SISTEM PEROLEHAN POIN JUARA UMUM:</t></is></c>';
        for ($c = $midColIdx + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'6" s="13"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 7: Metadata Row 1
        $sheetDataXml .= '<row r="7" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A7" s="15" t="inlineStr"><is><t>Tingkatan Lomba:</t></is></c>';
        $sheetDataXml .= '<c r="B7" s="18" t="inlineStr"><is><t>'.htmlspecialchars($meta['level_text'], ENT_XML1).'</t></is></c>';
        for ($c = 2; $c < $midColIdx; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'7" s="18"/>';
        }
        $sheetDataXml .= '<c r="'.$nextColLetter.'7" s="15" t="inlineStr"><is><t>Tier 1 (Utama):</t></is></c>';
        $tier1Rule = 'Juara 1: 12 pts | Juara 2: 10 pts | Juara 3: 8 pts | Harapan 1: 6 pts';
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($midColIdx + 1).'7" s="16" t="inlineStr"><is><t>'.htmlspecialchars($tier1Rule, ENT_XML1).'</t></is></c>';
        for ($c = $midColIdx + 2; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'7" s="16"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 8: Metadata Row 2
        $sheetDataXml .= '<row r="8" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A8" s="15" t="inlineStr"><is><t>Cabang Lomba:</t></is></c>';
        $sheetDataXml .= '<c r="B8" s="16" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_total_medals'].' ('.$meta['categories_count'].' Cabang Mata Lomba)', ENT_XML1).'</t></is></c>';
        for ($c = 2; $c < $midColIdx; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'8" s="16"/>';
        }
        $sheetDataXml .= '<c r="'.$nextColLetter.'8" s="15" t="inlineStr"><is><t>Tier 2 (Pendukung):</t></is></c>';
        $tier2Rule = 'Juara 1: 10 pts | Juara 2: 8 pts | Juara 3: 6 pts | Harapan 1: 4 pts';
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($midColIdx + 1).'8" s="16" t="inlineStr"><is><t>'.htmlspecialchars($tier2Rule, ENT_XML1).'</t></is></c>';
        for ($c = $midColIdx + 2; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'8" s="16"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 9: Metadata Row 3
        $sheetDataXml .= '<row r="9" ht="22" customHeight="1">';
        $sheetDataXml .= '<c r="A9" s="15" t="inlineStr"><is><t>Total Kontingen:</t></is></c>';
        $sheetDataXml .= '<c r="B9" s="16" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_total_schools'], ENT_XML1).'</t></is></c>';
        for ($c = 2; $c < $midColIdx; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'9" s="16"/>';
        }
        $sheetDataXml .= '<c r="'.$nextColLetter.'9" s="15" t="inlineStr"><is><t>Aturan Seri (Tie-Break):</t></is></c>';
        $tieRule = 'Keunggulan Jumlah Medali Emas > Perak > Perunggu';
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($midColIdx + 1).'9" s="48" t="inlineStr"><is><t>'.htmlspecialchars($tieRule, ENT_XML1).'</t></is></c>';
        for ($c = $midColIdx + 2; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'9" s="48"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 10: Spacing blank
        $sheetDataXml .= '<row r="10" ht="8" customHeight="1">';
        for ($c = 0; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'10" s="46"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 11-13: Podium 3 Besar Juara Umum (Highlight Cards)
        $podium1 = $standings->get(0);
        $podium2 = $standings->get(1);
        $podium3 = $standings->get(2);

        $podCol1End = max(3, (int) floor($totalCols / 3));
        $podCol2End = max($podCol1End + 3, (int) floor($totalCols * 2 / 3));

        // Podium Header Row (Baris 11)
        $sheetDataXml .= '<row r="11" ht="22" customHeight="1">';
        // Card 1: Juara Umum 1 (Gold)
        $sheetDataXml .= '<c r="A11" s="20" t="inlineStr"><is><t>🥇 JUARA UMUM I (PIALA BERGILIR)</t></is></c>';
        for ($c = 1; $c < $podCol1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'11" s="20"/>';
        }
        // Card 2: Juara Umum 2 (Silver)
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($podCol1End).'11" s="22" t="inlineStr"><is><t>🥈 JUARA UMUM II (TROPHY TETAP)</t></is></c>';
        for ($c = $podCol1End + 1; $c < $podCol2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'11" s="22"/>';
        }
        // Card 3: Juara Umum 3 (Bronze)
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($podCol2End).'11" s="24" t="inlineStr"><is><t>🥉 JUARA UMUM III (TROPHY TETAP)</t></is></c>';
        for ($c = $podCol2End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'11" s="24"/>';
        }
        $sheetDataXml .= '</row>';

        // Podium School Name Row (Baris 12)
        $sheetDataXml .= '<row r="12" ht="26" customHeight="1">';
        // P1 School
        $p1School = $podium1['school_name'] ?? 'Belum Ditentukan';
        $sheetDataXml .= '<c r="A12" s="21" t="inlineStr"><is><t>'.htmlspecialchars($p1School, ENT_XML1).'</t></is></c>';
        for ($c = 1; $c < $podCol1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'12" s="21"/>';
        }
        // P2 School
        $p2School = $podium2['school_name'] ?? 'Belum Ditentukan';
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($podCol1End).'12" s="23" t="inlineStr"><is><t>'.htmlspecialchars($p2School, ENT_XML1).'</t></is></c>';
        for ($c = $podCol1End + 1; $c < $podCol2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'12" s="23"/>';
        }
        // P3 School
        $p3School = $podium3['school_name'] ?? 'Belum Ditentukan';
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($podCol2End).'12" s="25" t="inlineStr"><is><t>'.htmlspecialchars($p3School, ENT_XML1).'</t></is></c>';
        for ($c = $podCol2End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'12" s="25"/>';
        }
        $sheetDataXml .= '</row>';

        // Podium Points Row (Baris 13)
        $sheetDataXml .= '<row r="13" ht="24" customHeight="1">';
        // P1 Score
        $p1ScoreText = ($podium1 ? $podium1['total_points'].' Poin' : '0 Poin').' • '.($podium1['gold_count'] ?? 0).' Emas, '.($podium1['silver_count'] ?? 0).' Perak, '.($podium1['bronze_count'] ?? 0).' Perunggu';
        $sheetDataXml .= '<c r="A13" s="20" t="inlineStr"><is><t>'.htmlspecialchars($p1ScoreText, ENT_XML1).'</t></is></c>';
        for ($c = 1; $c < $podCol1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'13" s="20"/>';
        }
        // P2 Score
        $p2ScoreText = ($podium2 ? $podium2['total_points'].' Poin' : '0 Poin').' • '.($podium2['gold_count'] ?? 0).' Emas, '.($podium2['silver_count'] ?? 0).' Perak, '.($podium2['bronze_count'] ?? 0).' Perunggu';
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($podCol1End).'13" s="22" t="inlineStr"><is><t>'.htmlspecialchars($p2ScoreText, ENT_XML1).'</t></is></c>';
        for ($c = $podCol1End + 1; $c < $podCol2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'13" s="22"/>';
        }
        // P3 Score
        $p3ScoreText = ($podium3 ? $podium3['total_points'].' Poin' : '0 Poin').' • '.($podium3['gold_count'] ?? 0).' Emas, '.($podium3['silver_count'] ?? 0).' Perak, '.($podium3['bronze_count'] ?? 0).' Perunggu';
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($podCol2End).'13" s="24" t="inlineStr"><is><t>'.htmlspecialchars($p3ScoreText, ENT_XML1).'</t></is></c>';
        for ($c = $podCol2End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'13" s="24"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 14: Spacing blank
        $sheetDataXml .= '<row r="14" ht="8" customHeight="1">';
        for ($c = 0; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'14" s="46"/>';
        }
        $sheetDataXml .= '</row>';

        // Baris 15: Main Table Headers
        $sheetDataXml .= '<row r="15" ht="28" customHeight="1">';
        $sheetDataXml .= '<c r="A15" s="4" t="inlineStr"><is><t>RANK</t></is></c>'; // Gold
        $sheetDataXml .= '<c r="B15" s="2" t="inlineStr"><is><t>NAMA SEKOLAH / PANGKALAN</t></is></c>'; // Green

        $colIter = 2;
        foreach ($categories as $cat) {
            $catLabel = $cat->code ?? $cat->name;
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($colIter).'15" s="2" t="inlineStr"><is><t>'.htmlspecialchars($catLabel, ENT_XML1).'</t></is></c>';
            $colIter++;
        }

        // Medals Header
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($colIter).'15" s="2" t="inlineStr"><is><t>🥇 E</t></is></c>';
        $colIter++;
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($colIter).'15" s="2" t="inlineStr"><is><t>🥈 P</t></is></c>';
        $colIter++;
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($colIter).'15" s="2" t="inlineStr"><is><t>🥉 B</t></is></c>';
        $colIter++;

        // Total Poin & Status Prestasi
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($colIter).'15" s="3" t="inlineStr"><is><t>TOTAL POIN</t></is></c>'; // Dark Green
        $colIter++;
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($colIter).'15" s="5" t="inlineStr"><is><t>STATUS PRESTASI</t></is></c>'; // Emerald
        $sheetDataXml .= '</row>';

        // Baris 16+: Standings Rows
        $currentRow = 16;
        foreach ($standings as $stIdx => $st) {
            $isZebra = ($currentRow % 2 === 1);
            $rankNum = $st['rank'];

            $tier = 'regular';
            if ($rankNum === 1) {
                $tier = 'juara1';
            } elseif ($rankNum === 2) {
                $tier = 'juara2';
            } elseif ($rankNum === 3) {
                $tier = 'juara3';
            } elseif ($rankNum >= 4 && $rankNum <= 6) {
                $tier = 'harapan';
            }

            $styleCenter = match ($tier) {
                'juara1' => 20,
                'juara2' => 22,
                'juara3' => 24,
                'harapan' => 26,
                default => ($isZebra ? 32 : 1),
            };

            $styleLeft = match ($tier) {
                'juara1' => 21,
                'juara2' => 23,
                'juara3' => 25,
                'harapan' => 27,
                default => ($isZebra ? 33 : 0),
            };

            $styleTotal = match ($tier) {
                'juara1' => 20,
                'juara2' => 22,
                'juara3' => 24,
                default => ($isZebra ? 31 : 29),
            };

            $predikat = match ($rankNum) {
                1 => 'JUARA UMUM I',
                2 => 'JUARA UMUM II',
                3 => 'JUARA UMUM III',
                4 => 'Peringkat Harapan I',
                5 => 'Peringkat Harapan II',
                6 => 'Peringkat Harapan III',
                default => 'Peringkat '.$rankNum,
            };

            $sheetDataXml .= '<row r="'.$currentRow.'" ht="24" customHeight="1">';

            // A: Rank
            $sheetDataXml .= '<c r="A'.$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.$rankNum.'</t></is></c>';

            // B: Nama Sekolah
            $sheetDataXml .= '<c r="B'.$currentRow.'" s="'.$styleLeft.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $st['school_name'], ENT_XML1).'</t></is></c>';

            // Kolom Cabang Lomba
            $cCol = 2;
            foreach ($categories as $cat) {
                $pts = $st['categories'][$cat->id] ?? 0;
                $ptsText = $pts > 0 ? (string) $pts : '-';
                $ptsStyle = $pts > 0 ? 47 : $styleCenter; // 47: red highlight badge chip jika > 0
                $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($cCol).$currentRow.'" s="'.$ptsStyle.'" t="inlineStr"><is><t>'.$ptsText.'</t></is></c>';
                $cCol++;
            }

            // Emas, Perak, Perunggu
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($cCol).$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.($st['gold_count'] ?? 0).'</t></is></c>';
            $cCol++;
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($cCol).$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.($st['silver_count'] ?? 0).'</t></is></c>';
            $cCol++;
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($cCol).$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.($st['bronze_count'] ?? 0).'</t></is></c>';
            $cCol++;

            // Total Poin
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($cCol).$currentRow.'" s="'.$styleTotal.'" t="inlineStr"><is><t>'.$st['total_points'].'</t></is></c>';
            $cCol++;

            // Status Prestasi
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($cCol).$currentRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.$predikat.'</t></is></c>';

            $sheetDataXml .= '</row>';
            $currentRow++;
        }

        // Baris Spasi Setelah Tabel
        $sheetDataXml .= '<row r="'.$currentRow.'" ht="10" customHeight="1">';
        for ($c = 0; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$currentRow.'" s="46"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // 4 Card Ringkasan Statistik
        $statLabelRow = $currentRow;
        $sheetDataXml .= '<row r="'.$statLabelRow.'" ht="18" customHeight="1">';

        $cStep = max(2, (int) floor($totalCols / 4));
        $c1End = $cStep;
        $c2End = $cStep * 2;
        $c3End = $cStep * 3;

        // Card 1 Label
        $sheetDataXml .= '<c r="A'.$statLabelRow.'" s="37" t="inlineStr"><is><t>TOTAL PANGKALAN:</t></is></c>';
        for ($c = 1; $c < $c1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statLabelRow.'" s="37"/>';
        }
        // Card 2 Label
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c1End).$statLabelRow.'" s="37" t="inlineStr"><is><t>TOTAL MEDALI DIPEREBUTKAN:</t></is></c>';
        for ($c = $c1End + 1; $c < $c2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statLabelRow.'" s="37"/>';
        }
        // Card 3 Label
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c2End).$statLabelRow.'" s="37" t="inlineStr"><is><t>POIN TERTINGGI:</t></is></c>';
        for ($c = $c2End + 1; $c < $c3End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statLabelRow.'" s="37"/>';
        }
        // Card 4 Label
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c3End).$statLabelRow.'" s="37" t="inlineStr"><is><t>RATA-RATA POIN:</t></is></c>';
        for ($c = $c3End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statLabelRow.'" s="37"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Nilai Statistik
        $statValRow = $currentRow;
        $sheetDataXml .= '<row r="'.$statValRow.'" ht="26" customHeight="1">';
        // Card 1 Value (Hitam Bold)
        $sheetDataXml .= '<c r="A'.$statValRow.'" s="38" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_total_schools'], ENT_XML1).'</t></is></c>';
        for ($c = 1; $c < $c1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statValRow.'" s="38"/>';
        }
        // Card 2 Value (Biru Bold)
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c1End).$statValRow.'" s="39" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_total_medals'], ENT_XML1).'</t></is></c>';
        for ($c = $c1End + 1; $c < $c2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statValRow.'" s="39"/>';
        }
        // Card 3 Value (Hijau Bold)
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c2End).$statValRow.'" s="40" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_top_points'], ENT_XML1).'</t></is></c>';
        for ($c = $c2End + 1; $c < $c3End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statValRow.'" s="40"/>';
        }
        // Card 4 Value (Oranye Bold)
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c3End).$statValRow.'" s="41" t="inlineStr"><is><t>'.htmlspecialchars((string) $meta['stat_avg_points'], ENT_XML1).'</t></is></c>';
        for ($c = $c3End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$statValRow.'" s="41"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Spasi Sebelum TTD
        $sheetDataXml .= '<row r="'.$currentRow.'" ht="16" customHeight="1">';
        for ($c = 0; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$currentRow.'" s="46"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Tanggal Pengesahan
        $ttdDateRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdDateRow.'" ht="20" customHeight="1">';
        for ($c = 0; $c < $c3End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdDateRow.'" s="46"/>';
        }
        $dateText = 'Bogor, '.($meta['export_date'] ?? date('d F Y'));
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c3End).$ttdDateRow.'" s="42" t="inlineStr"><is><t>'.htmlspecialchars($dateText, ENT_XML1).'</t></is></c>';
        for ($c = $c3End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdDateRow.'" s="42"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Jabatan TTD (4 Kolom)
        $ttdPosRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdPosRow.'" ht="22" customHeight="1">';
        // Kolom 1
        $sheetDataXml .= '<c r="A'.$ttdPosRow.'" s="43" t="inlineStr"><is><t>Koordinator Rekapitulasi &amp; Skoring,</t></is></c>';
        for ($c = 1; $c < $c1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdPosRow.'" s="43"/>';
        }
        // Kolom 2
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c1End).$ttdPosRow.'" s="43" t="inlineStr"><is><t>Dewan Juri Kehormatan,</t></is></c>';
        for ($c = $c1End + 1; $c < $c2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdPosRow.'" s="43"/>';
        }
        // Kolom 3
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c2End).$ttdPosRow.'" s="43" t="inlineStr"><is><t>Pembina PMR Wira SMAN 1 Ciawi,</t></is></c>';
        for ($c = $c2End + 1; $c < $c3End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdPosRow.'" s="43"/>';
        }
        // Kolom 4
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c3End).$ttdPosRow.'" s="43" t="inlineStr"><is><t>Ketua Panitia Pelaksana,</t></is></c>';
        for ($c = $c3End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdPosRow.'" s="43"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Spasi TTD
        $ttdSpaceRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdSpaceRow.'" ht="48" customHeight="1">';
        for ($c = 0; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdSpaceRow.'" s="46"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Nama TTD (Underline / Medium Bottom Border)
        $ttdNameRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdNameRow.'" ht="22" customHeight="1">';
        // Kolom 1
        $sheetDataXml .= '<c r="A'.$ttdNameRow.'" s="44" t="inlineStr"><is><t>( Drs. H. Mulyadi, M.Pd )</t></is></c>';
        for ($c = 1; $c < $c1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdNameRow.'" s="44"/>';
        }
        // Kolom 2
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c1End).$ttdNameRow.'" s="44" t="inlineStr"><is><t>( Dr. H. Ahmad Fauzi, M.Pd )</t></is></c>';
        for ($c = $c1End + 1; $c < $c2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdNameRow.'" s="44"/>';
        }
        // Kolom 3
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c2End).$ttdNameRow.'" s="44" t="inlineStr"><is><t>( Dra. Hj. Siti Rohmah, M.M )</t></is></c>';
        for ($c = $c2End + 1; $c < $c3End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdNameRow.'" s="44"/>';
        }
        // Kolom 4
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c3End).$ttdNameRow.'" s="44" t="inlineStr"><is><t>( Muhammad Dwi Pantara )</t></is></c>';
        for ($c = $c3End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdNameRow.'" s="44"/>';
        }
        $sheetDataXml .= '</row>';
        $currentRow++;

        // Baris Subtitle TTD
        $ttdSubRow = $currentRow;
        $sheetDataXml .= '<row r="'.$ttdSubRow.'" ht="18" customHeight="1">';
        // Kolom 1
        $sheetDataXml .= '<c r="A'.$ttdSubRow.'" s="45" t="inlineStr"><is><t>Koordinator Teknis Skoring</t></is></c>';
        for ($c = 1; $c < $c1End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdSubRow.'" s="45"/>';
        }
        // Kolom 2
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c1End).$ttdSubRow.'" s="45" t="inlineStr"><is><t>Koordinator Dewan Juri Lapangan</t></is></c>';
        for ($c = $c1End + 1; $c < $c2End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdSubRow.'" s="45"/>';
        }
        // Kolom 3
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c2End).$ttdSubRow.'" s="45" t="inlineStr"><is><t>Pembina PMR PMI Unit SMAN 1 Ciawi</t></is></c>';
        for ($c = $c2End + 1; $c < $c3End; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdSubRow.'" s="45"/>';
        }
        // Kolom 4
        $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c3End).$ttdSubRow.'" s="45" t="inlineStr"><is><t>Ketua Pelaksana Sua Bhakti Berkarya IV</t></is></c>';
        for ($c = $c3End + 1; $c < $totalCols; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).$ttdSubRow.'" s="45"/>';
        }
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '</sheetData>';

        // 3. Merge Cells Sesuai Mockup
        $p1EndLetter = $this->indexToColumnLetter($podCol1End - 1);
        $p2StartLetter = $this->indexToColumnLetter($podCol1End);
        $p2EndLetter = $this->indexToColumnLetter($podCol2End - 1);
        $p3StartLetter = $this->indexToColumnLetter($podCol2End);

        $c1EndLetter = $this->indexToColumnLetter($c1End - 1);
        $c2StartLetter = $this->indexToColumnLetter($c1End);
        $c2EndLetter = $this->indexToColumnLetter($c2End - 1);
        $c3StartLetter = $this->indexToColumnLetter($c2End);
        $c3EndLetter = $this->indexToColumnLetter($c3End - 1);
        $c4StartLetter = $this->indexToColumnLetter($c3End);

        $mergeCells = [
            'A1:'.$midColLetter.'1',
            $nextColLetter.'1:'.$lastColLetter.'1',
            'A2:'.$midColLetter.'2',
            $nextColLetter.'2:'.$lastColLetter.'2',
            'A3:'.$midColLetter.'3',
            $nextColLetter.'3:'.$lastColLetter.'3',
            'A6:'.$midColLetter.'6',
            $nextColLetter.'6:'.$lastColLetter.'6',
            'B7:'.$midColLetter.'7',
            $this->indexToColumnLetter($midColIdx + 1).'7:'.$lastColLetter.'7',
            'B8:'.$midColLetter.'8',
            $this->indexToColumnLetter($midColIdx + 1).'8:'.$lastColLetter.'8',
            'B9:'.$midColLetter.'9',
            $this->indexToColumnLetter($midColIdx + 1).'9:'.$lastColLetter.'9',
            // Podium Cards
            'A11:'.$p1EndLetter.'11',
            $p2StartLetter.'11:'.$p2EndLetter.'11',
            $p3StartLetter.'11:'.$lastColLetter.'11',
            'A12:'.$p1EndLetter.'12',
            $p2StartLetter.'12:'.$p2EndLetter.'12',
            $p3StartLetter.'12:'.$lastColLetter.'12',
            'A13:'.$p1EndLetter.'13',
            $p2StartLetter.'13:'.$p2EndLetter.'13',
            $p3StartLetter.'13:'.$lastColLetter.'13',
            // Statistik
            'A'.$statLabelRow.':'.$c1EndLetter.$statLabelRow,
            $c2StartLetter.$statLabelRow.':'.$c2EndLetter.$statLabelRow,
            $c3StartLetter.$statLabelRow.':'.$c3EndLetter.$statLabelRow,
            $c4StartLetter.$statLabelRow.':'.$lastColLetter.$statLabelRow,
            'A'.$statValRow.':'.$c1EndLetter.$statValRow,
            $c2StartLetter.$statValRow.':'.$c2EndLetter.$statValRow,
            $c3StartLetter.$statValRow.':'.$c3EndLetter.$statValRow,
            $c4StartLetter.$statValRow.':'.$lastColLetter.$statValRow,
            // TTD
            $c4StartLetter.$ttdDateRow.':'.$lastColLetter.$ttdDateRow,
            'A'.$ttdPosRow.':'.$c1EndLetter.$ttdPosRow,
            $c2StartLetter.$ttdPosRow.':'.$c2EndLetter.$ttdPosRow,
            $c3StartLetter.$ttdPosRow.':'.$c3EndLetter.$ttdPosRow,
            $c4StartLetter.$ttdPosRow.':'.$lastColLetter.$ttdPosRow,
            'A'.$ttdNameRow.':'.$c1EndLetter.$ttdNameRow,
            $c2StartLetter.$ttdNameRow.':'.$c2EndLetter.$ttdNameRow,
            $c3StartLetter.$ttdNameRow.':'.$c3EndLetter.$ttdNameRow,
            $c4StartLetter.$ttdNameRow.':'.$lastColLetter.$ttdNameRow,
            'A'.$ttdSubRow.':'.$c1EndLetter.$ttdSubRow,
            $c2StartLetter.$ttdSubRow.':'.$c2EndLetter.$ttdSubRow,
            $c3StartLetter.$ttdSubRow.':'.$c3EndLetter.$ttdSubRow,
            $c4StartLetter.$ttdSubRow.':'.$lastColLetter.$ttdSubRow,
        ];

        $mergeCellsXml = '<mergeCells count="'.count($mergeCells).'">';
        foreach ($mergeCells as $mRange) {
            $mergeCellsXml .= '<mergeCell ref="'.$mRange.'"/>';
        }
        $mergeCellsXml .= '</mergeCells>';

        // 4. Worksheet XML with Frozen Pane at Row 15 & selection
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews>
<sheetView tabSelected="1" workbookViewId="0">
<pane ySplit="15" topLeftCell="A16" activePane="bottomLeft" state="frozen"/>
<selection pane="bottomLeft" activeCell="A16" sqref="A16"/>
</sheetView>
</sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
'.$mergeCellsXml.'
</worksheet>';
    }

    /**
     * XML Sheet 2: Distribusi Medali Lomba
     */
    protected function buildCategoryMedalDistributionSheetXml(array $meta, Collection $categories): string
    {
        $colsXml = '<cols>
<col min="1" max="1" width="8" customWidth="1"/>
<col min="2" max="2" width="16" customWidth="1"/>
<col min="3" max="3" width="30" customWidth="1"/>
<col min="4" max="4" width="18" customWidth="1"/>
<col min="5" max="5" width="28" customWidth="1"/>
<col min="6" max="6" width="28" customWidth="1"/>
<col min="7" max="7" width="28" customWidth="1"/>
</cols>';

        $sheetDataXml = '<sheetData>';

        // Title
        $sheetDataXml .= '<row r="1" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A1" s="6" t="inlineStr"><is><t>PALANG MERAH INDONESIA • PMR WIRA SMA NEGERI 1 CIAWI</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="2" ht="28" customHeight="1">';
        $sheetDataXml .= '<c r="A2" s="7" t="inlineStr"><is><t>DISTRIBUSI PEROLEHAN MEDALI PER CABANG LOMBA</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="3" ht="20" customHeight="1">';
        $sub = 'Kegiatan: '.$meta['event_title'].' • Tingkat '.$meta['level_text'];
        $sheetDataXml .= '<c r="A3" s="8" t="inlineStr"><is><t>'.htmlspecialchars($sub, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="4" ht="8" customHeight="1">';
        for ($c = 0; $c < 7; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'4" s="9"/>';
        }
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="5" ht="12" customHeight="1"/>';

        // Headers
        $sheetDataXml .= '<row r="6" ht="26" customHeight="1">';
        $headers = ['NO.', 'KODE', 'MATA LOMBA', 'TIER POIN', '🥇 JUARA 1 (EMAS)', '🥈 JUARA 2 (PERAK)', '🥉 JUARA 3 (PERUNGGU)'];
        foreach ($headers as $hIdx => $hText) {
            $sId = match ($hIdx) {
                4 => 4,
                5 => 2,
                6 => 2,
                default => 2,
            };
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($hIdx).'6" s="'.$sId.'" t="inlineStr"><is><t>'.htmlspecialchars($hText, ENT_XML1).'</t></is></c>';
        }
        $sheetDataXml .= '</row>';

        $cRow = 7;
        foreach ($categories as $idx => $cat) {
            $topScores = CompetitionScore::where('competition_category_id', $cat->id)
                ->where('is_disqualified', false)
                ->whereIn('rank', [1, 2, 3])
                ->with('team.registration')
                ->get()
                ->keyBy('rank');

            $j1 = $topScores->get(1)?->team?->registration?->school_name ?? ($topScores->get(1)?->team?->team_name ?? '-');
            $j2 = $topScores->get(2)?->team?->registration?->school_name ?? ($topScores->get(2)?->team?->team_name ?? '-');
            $j3 = $topScores->get(3)?->team?->registration?->school_name ?? ($topScores->get(3)?->team?->team_name ?? '-');

            $tierName = match ($cat->point_tier) {
                'tier_2' => 'Tier 2 (10 pts)',
                'tier_3' => 'Tier 3 (3 pts)',
                'tier_4' => 'Tier 4 (0 pts)',
                default => 'Tier 1 (12 pts)',
            };

            $isZebra = ($cRow % 2 === 1);
            $sCenter = $isZebra ? 32 : 1;
            $sLeft = $isZebra ? 33 : 0;

            $sheetDataXml .= '<row r="'.$cRow.'" ht="24" customHeight="1">';
            $sheetDataXml .= '<c r="A'.$cRow.'" s="'.$sCenter.'" t="inlineStr"><is><t>'.($idx + 1).'</t></is></c>';
            $sheetDataXml .= '<c r="B'.$cRow.'" s="'.$sCenter.'" t="inlineStr"><is><t>'.htmlspecialchars($cat->code ?? '-', ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '<c r="C'.$cRow.'" s="'.$sLeft.'" t="inlineStr"><is><t>'.htmlspecialchars($cat->name, ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '<c r="D'.$cRow.'" s="'.$sCenter.'" t="inlineStr"><is><t>'.htmlspecialchars($tierName, ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '<c r="E'.$cRow.'" s="20" t="inlineStr"><is><t>'.htmlspecialchars($j1, ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '<c r="F'.$cRow.'" s="22" t="inlineStr"><is><t>'.htmlspecialchars($j2, ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '<c r="G'.$cRow.'" s="24" t="inlineStr"><is><t>'.htmlspecialchars($j3, ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '</row>';
            $cRow++;
        }

        $sheetDataXml .= '</sheetData>';

        $merge = ['A1:G1', 'A2:G2', 'A3:G3', 'A4:G4'];
        $mergeXml = '<mergeCells count="'.count($merge).'">';
        foreach ($merge as $m) {
            $mergeXml .= '<mergeCell ref="'.$m.'"/>';
        }
        $mergeXml .= '</mergeCells>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews><sheetView workbookViewId="0"/></sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
'.$mergeXml.'
</worksheet>';
    }

    /**
     * XML Sheet 3: Daftar Kontingen Sekolah
     */
    protected function buildLeaderboardSchoolListSheetXml(array $meta, Collection $standings): string
    {
        $colsXml = '<cols>
<col min="1" max="1" width="8" customWidth="1"/>
<col min="2" max="2" width="36" customWidth="1"/>
<col min="3" max="3" width="20" customWidth="1"/>
<col min="4" max="4" width="12" customWidth="1"/>
<col min="5" max="5" width="12" customWidth="1"/>
<col min="6" max="6" width="12" customWidth="1"/>
<col min="7" max="7" width="16" customWidth="1"/>
<col min="8" max="8" width="22" customWidth="1"/>
</cols>';

        $sheetDataXml = '<sheetData>';

        // Title
        $sheetDataXml .= '<row r="1" ht="24" customHeight="1">';
        $sheetDataXml .= '<c r="A1" s="6" t="inlineStr"><is><t>PALANG MERAH INDONESIA • PMR WIRA SMA NEGERI 1 CIAWI</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="2" ht="28" customHeight="1">';
        $sheetDataXml .= '<c r="A2" s="7" t="inlineStr"><is><t>DAFTAR PANGKALAN SEKOLAH &amp; PEROLEHAN AKHIR</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="3" ht="20" customHeight="1">';
        $sub = 'Peserta Kejuaraan: '.$meta['event_title'].' • Tingkat '.$meta['level_text'];
        $sheetDataXml .= '<c r="A3" s="8" t="inlineStr"><is><t>'.htmlspecialchars($sub, ENT_XML1).'</t></is></c>';
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="4" ht="8" customHeight="1">';
        for ($c = 0; $c < 8; $c++) {
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($c).'4" s="9"/>';
        }
        $sheetDataXml .= '</row>';

        $sheetDataXml .= '<row r="5" ht="12" customHeight="1"/>';

        // Headers
        $sheetDataXml .= '<row r="6" ht="26" customHeight="1">';
        $headers = ['RANK', 'NAMA SEKOLAH / PANGKALAN', 'TINGKATAN', '🥇 EMAS', '🥈 PERAK', '🥉 PERUNGGU', 'TOTAL POIN', 'STATUS PRESTASI'];
        foreach ($headers as $hIdx => $hText) {
            $sId = match ($hIdx) {
                0 => 4,
                6 => 3,
                default => 2,
            };
            $sheetDataXml .= '<c r="'.$this->indexToColumnLetter($hIdx).'6" s="'.$sId.'" t="inlineStr"><is><t>'.htmlspecialchars($hText, ENT_XML1).'</t></is></c>';
        }
        $sheetDataXml .= '</row>';

        $cRow = 7;
        foreach ($standings as $st) {
            $isZebra = ($cRow % 2 === 1);
            $rankNum = $st['rank'];

            $predikat = match ($rankNum) {
                1 => 'JUARA UMUM I',
                2 => 'JUARA UMUM II',
                3 => 'JUARA UMUM III',
                4 => 'Peringkat Harapan I',
                5 => 'Peringkat Harapan II',
                6 => 'Peringkat Harapan III',
                default => 'Peringkat '.$rankNum,
            };

            $styleCenter = match ($rankNum) {
                1 => 20,
                2 => 22,
                3 => 24,
                default => ($isZebra ? 32 : 1),
            };

            $styleLeft = match ($rankNum) {
                1 => 21,
                2 => 23,
                3 => 25,
                default => ($isZebra ? 33 : 0),
            };

            $styleTotal = match ($rankNum) {
                1 => 20,
                2 => 22,
                3 => 24,
                default => ($isZebra ? 31 : 29),
            };

            $sheetDataXml .= '<row r="'.$cRow.'" ht="24" customHeight="1">';
            $sheetDataXml .= '<c r="A'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.$rankNum.'</t></is></c>';
            $sheetDataXml .= '<c r="B'.$cRow.'" s="'.$styleLeft.'" t="inlineStr"><is><t>'.htmlspecialchars($st['school_name'], ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '<c r="C'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.htmlspecialchars($meta['level'], ENT_XML1).'</t></is></c>';
            $sheetDataXml .= '<c r="D'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.($st['gold_count'] ?? 0).'</t></is></c>';
            $sheetDataXml .= '<c r="E'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.($st['silver_count'] ?? 0).'</t></is></c>';
            $sheetDataXml .= '<c r="F'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.($st['bronze_count'] ?? 0).'</t></is></c>';
            $sheetDataXml .= '<c r="G'.$cRow.'" s="'.$styleTotal.'" t="inlineStr"><is><t>'.$st['total_points'].'</t></is></c>';
            $sheetDataXml .= '<c r="H'.$cRow.'" s="'.$styleCenter.'" t="inlineStr"><is><t>'.$predikat.'</t></is></c>';
            $sheetDataXml .= '</row>';
            $cRow++;
        }

        $sheetDataXml .= '</sheetData>';

        $merge = ['A1:H1', 'A2:H2', 'A3:H3', 'A4:H4'];
        $mergeXml = '<mergeCells count="'.count($merge).'">';
        foreach ($merge as $m) {
            $mergeXml .= '<mergeCell ref="'.$m.'"/>';
        }
        $mergeXml .= '</mergeCells>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetViews><sheetView workbookViewId="0"/></sheetViews>
<sheetFormatPr defaultRowHeight="20"/>
'.$colsXml.'
'.$sheetDataXml.'
'.$mergeXml.'
</worksheet>';
    }
}
