<?php

namespace App\Support;

use App\Models\Agent;
use ZipArchive;

class AgentImporter
{
    /**
     * Import agents from a file path (CSV, TSV, or XLSX).
     *
     * @param string $filePath
     * @param string|null $originalExtension
     * @return array ['success' => int, 'skipped' => int, 'errors' => array]
     */
    public static function importFromFile(string $filePath, ?string $originalExtension = null): array
    {
        $extension = strtolower($originalExtension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, ['xlsx', 'xls']) && class_exists(ZipArchive::class)) {
            $rows = static::parseXlsx($filePath);
            if (empty($rows)) {
                $rows = static::parseCsv($filePath);
            }
        } else {
            $rows = static::parseCsv($filePath);
        }

        if (empty($rows)) {
            return [
                'success' => 0,
                'skipped' => 0,
                'errors' => ['The uploaded file is empty or could not be parsed.']
            ];
        }

        return static::processRows($rows);
    }

    /**
     * Parse CSV / TSV file into array of rows.
     */
    public static function parseCsv(string $filePath): array
    {
        $rows = [];
        if (!file_exists($filePath)) {
            return $rows;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return $rows;
        }

        // Remove UTF-8 BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        
        $lines = preg_split('/\r\n|\r|\n/', $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Auto-detect delimiter: comma, semicolon, tab
            $delimiter = ',';
            if (substr_count($line, ';') > substr_count($line, ',')) {
                $delimiter = ';';
            } elseif (substr_count($line, "\t") > substr_count($line, ',')) {
                $delimiter = "\t";
            }

            $data = str_getcsv($line, $delimiter);
            if (array_filter($data, fn($val) => trim((string)$val) !== '')) {
                $rows[] = array_map('trim', $data);
            }
        }

        return $rows;
    }

    /**
     * Parse XLSX file natively using ZipArchive & SimpleXML.
     */
    public static function parseXlsx(string $filePath): array
    {
        $rows = [];
        $zip = new ZipArchive();

        if ($zip->open($filePath) !== true) {
            return $rows;
        }

        // Load shared strings if available
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $val) {
                    if (isset($val->t)) {
                        $sharedStrings[] = (string) $val->t;
                    } elseif (isset($val->r)) {
                        $str = '';
                        foreach ($val->r as $r) {
                            $str .= (string) $r->t;
                        }
                        $sharedStrings[] = $str;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // Load worksheet
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXml) {
            $sheetXml = $zip->getFromName('xl/worksheets/sheet.xml');
        }
        $zip->close();

        if (!$sheetXml) {
            return $rows;
        }

        $xml = @simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData->row)) {
            return $rows;
        }

        foreach ($xml->sheetData->row as $rowNode) {
            $row = [];
            foreach ($rowNode->c as $cell) {
                $type = (string) $cell['t'];
                $val = (string) $cell->v;

                if ($type === 's' && isset($sharedStrings[(int) $val])) {
                    $val = $sharedStrings[(int) $val];
                }

                $row[] = trim($val);
            }

            if (array_filter($row, fn($v) => (string)$v !== '')) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Process parsed rows into Agent database records.
     */
    public static function processRows(array $rows): array
    {
        if (count($rows) < 1) {
            return ['success' => 0, 'skipped' => 0, 'errors' => ['File contains no valid data rows.']];
        }

        $firstRowRaw = array_shift($rows);
        $headerRow = array_map('strtolower', array_map('trim', $firstRowRaw));

        // Detect column indices
        $nameIdx = static::findColumnIndex($headerRow, ['name', 'agent name', 'agent_name', 'fullname', 'full name']);
        $businessNameIdx = static::findColumnIndex($headerRow, ['business', 'business name', 'business_name', 'company', 'company name', 'company_name', 'shop', 'shop name']);
        $locationIdx = static::findColumnIndex($headerRow, ['location', 'agent location', 'agent_location', 'town', 'city']);
        $commissionIdx = static::findColumnIndex($headerRow, ['commission', 'commission percentage', 'commission_percentage', 'rate', 'commission rate', 'commission_rate', '%']);
        $pinIdx = static::findColumnIndex($headerRow, ['pin', 'agent pin', 'agent_pin', 'security pin', 'code']);

        // Fallback: If header row doesn't contain recognized names, test if line 1 was actually data
        if ($nameIdx === null) {
            if (!empty($firstRowRaw[0]) && !in_array($headerRow[0], ['name', 'agent_name', 'location'])) {
                // Re-insert first row as data
                array_unshift($rows, $firstRowRaw);
            }
            $nameIdx = 0;
            $locationIdx = 1;
            $commissionIdx = 2;
            $pinIdx = 3;
        }

        $successCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($rows as $rowNum => $row) {
            $name = isset($row[$nameIdx]) ? trim((string)$row[$nameIdx]) : '';
            if (empty($name) || strtolower($name) === 'name' || strtolower($name) === 'agent name') {
                $skippedCount++;
                continue;
            }

            $businessName = ($businessNameIdx !== null && isset($row[$businessNameIdx]) && trim((string)$row[$businessNameIdx]) !== '') ? trim((string)$row[$businessNameIdx]) : null;
            $location = (isset($row[$locationIdx]) && trim((string)$row[$locationIdx]) !== '') ? trim((string)$row[$locationIdx]) : 'General';
            
            $commission = 10;
            if (isset($row[$commissionIdx]) && is_numeric(trim((string)$row[$commissionIdx]))) {
                $commission = max(0, min(100, (int) round(floatval(trim((string)$row[$commissionIdx])))));
            }

            $pin = isset($row[$pinIdx]) ? trim((string)$row[$pinIdx]) : '';
            
            // Validate pin or generate unique
            if (empty($pin) || strlen($pin) !== 4 || !ctype_digit($pin) || Agent::where('pin', $pin)->exists()) {
                $pin = Agent::generateUniquePin();
            }

            try {
                Agent::create([
                    'name' => $name,
                    'business_name' => $businessName,
                    'location' => $location,
                    'commission_percentage' => $commission,
                    'pin' => $pin,
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Row #" . ($rowNum + 2) . " ($name): " . $e->getMessage();
            }
        }

        return [
            'success' => $successCount,
            'skipped' => $skippedCount,
            'errors' => $errors,
        ];
    }

    private static function findColumnIndex(array $headers, array $candidates): ?int
    {
        foreach ($headers as $idx => $header) {
            $cleanHeader = str_replace(['_', '-'], ' ', trim(strtolower($header)));
            foreach ($candidates as $candidate) {
                if ($cleanHeader === str_replace(['_', '-'], ' ', $candidate)) {
                    return $idx;
                }
            }
        }
        return null;
    }
}
