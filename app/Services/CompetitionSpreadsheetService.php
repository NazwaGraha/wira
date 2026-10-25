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
