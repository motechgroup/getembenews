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
            $cells = [];
            $maxCol = 0;

            foreach ($rowNode->c as $cell) {
                $cellRef = (string) $cell['r'];
                $colIdx = !empty($cellRef) ? static::cellRefToColIndex($cellRef) : count($cells);
                
                $type = (string) $cell['t'];
                $val = '';

                if ($type === 's') {
                    $sIndex = (int) $cell->v;
                    if (isset($sharedStrings[$sIndex])) {
                        $val = $sharedStrings[$sIndex];
                    }
                } elseif ($type === 'inlineStr') {
                    if (isset($cell->is->t)) {
                        $val = (string) $cell->is->t;
                    } elseif (isset($cell->is->r)) {
                        $str = '';
                        foreach ($cell->is->r as $r) {
                            $str .= (string) $r->t;
                        }
                        $val = $str;
                    }
                } else {
                    $val = (string) $cell->v;
                }

                $val = trim($val);
                $cells[$colIdx] = $val;
                if ($colIdx > $maxCol) {
                    $maxCol = $colIdx;
                }
            }

            if (!empty($cells)) {
                $row = [];
                for ($i = 0; $i <= $maxCol; $i++) {
                    $row[$i] = $cells[$i] ?? '';
                }

                if (array_filter($row, fn($v) => (string)$v !== '')) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    /**
     * Convert Excel cell reference (e.g. "A1", "C2", "AB10") to 0-indexed column integer.
     */
    private static function cellRefToColIndex(string $cellRef): int
    {
        preg_match('/^([A-Z]+)/i', $cellRef, $matches);
        if (empty($matches[1])) {
            return 0;
        }
        $colStr = strtoupper($matches[1]);
        $length = strlen($colStr);
        $colIndex = 0;
        for ($i = 0; $i < $length; $i++) {
            $colIndex = $colIndex * 26 + (ord($colStr[$i]) - 64);
        }
        return $colIndex - 1;
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

        // Column search candidates
        $nameCandidates = ['name', 'agent name', 'agent_name', 'fullname', 'full name'];
        $businessCandidates = ['business', 'business name', 'business_name', 'company', 'company name', 'company_name', 'shop', 'shop name'];
        $phoneCandidates = [
            'phone', 'phone number', 'phone_number', 'phonenumber', 'phone no', 'phone no.', 'phone_no', 'phoneno',
            'phone#', 'phone #', 'phone num', 'phone_num',
            'mobile', 'mobile number', 'mobile_number', 'mobilenumber', 'mobile no', 'mobile no.', 'mobile_no', 'mobileno',
            'contact', 'contact number', 'contact_number', 'contactnumber', 'contact phone', 'contact_phone', 'contact no', 'contact no.', 'contact_no', 'contactno',
            'telephone', 'telephone number', 'tel', 'tel no', 'tel no.', 'tel_no', 'telno',
            'msisdn', 'cell', 'cellphone', 'cell no', 'cell no.', 'cell_no', 'number', 'phone/mobile'
        ];
        $locationCandidates = ['location', 'agent location', 'agent_location', 'town', 'city', 'address'];
        $commissionCandidates = ['commission', 'commission percentage', 'commission_percentage', 'rate', 'commission rate', 'commission_rate', '%'];
        $pinCandidates = ['pin', 'agent pin', 'agent_pin', 'security pin', 'code', 'pin code'];

        // Detect column indices
        $nameIdx = static::findColumnIndex($headerRow, $nameCandidates);
        $businessNameIdx = static::findColumnIndex($headerRow, $businessCandidates);
        $phoneIdx = static::findColumnIndex($headerRow, $phoneCandidates);
        $locationIdx = static::findColumnIndex($headerRow, $locationCandidates);
        $commissionIdx = static::findColumnIndex($headerRow, $commissionCandidates);
        $pinIdx = static::findColumnIndex($headerRow, $pinCandidates);

        // Fallback: If header row doesn't contain recognized name header, test if line 1 was actually data
        if ($nameIdx === null) {
            if (!empty($firstRowRaw[0])) {
                $cleanFirstCell = preg_replace('/[^a-z]/', '', strtolower($firstRowRaw[0]));
                if (!in_array($cleanFirstCell, ['name', 'agentname', 'location'])) {
                    // Re-insert first row as data
                    array_unshift($rows, $firstRowRaw);
                }
            }

            // Auto-detect columns based on first data row
            $firstDataRow = $rows[0] ?? [];
            $nameIdx = 0;
            $locationIdx = null;
            $commissionIdx = null;
            $pinIdx = null;
            $phoneIdx = null;

            foreach ($firstDataRow as $cIdx => $cVal) {
                $strVal = trim((string)$cVal);
                if (empty($strVal)) continue;

                if ($phoneIdx === null && static::cleanPhoneNumber($strVal) !== null) {
                    $phoneIdx = $cIdx;
                } elseif ($pinIdx === null && strlen($strVal) === 4 && ctype_digit($strVal)) {
                    $pinIdx = $cIdx;
                } elseif ($commissionIdx === null && is_numeric($strVal) && (float)$strVal >= 0 && (float)$strVal <= 100 && $cIdx > 0) {
                    $commissionIdx = $cIdx;
                } elseif ($cIdx > 0 && $locationIdx === null && !static::cleanPhoneNumber($strVal)) {
                    $locationIdx = $cIdx;
                }
            }
        }

        $successCount = 0;
        $skippedCount = 0;
        $errors = [];

        foreach ($rows as $rowNum => $row) {
            $name = isset($row[$nameIdx]) ? trim((string)$row[$nameIdx]) : '';
            $cleanName = strtolower(preg_replace('/[^a-z]/', '', $name));
            if (empty($name) || in_array($cleanName, ['name', 'agentname', 'fullname'])) {
                $skippedCount++;
                continue;
            }

            $businessName = ($businessNameIdx !== null && isset($row[$businessNameIdx]) && trim((string)$row[$businessNameIdx]) !== '') ? trim((string)$row[$businessNameIdx]) : null;
            
            // Extract & Clean Phone Number
            $rawPhone = ($phoneIdx !== null && isset($row[$phoneIdx])) ? (string)$row[$phoneIdx] : '';
            $phone = static::cleanPhoneNumber($rawPhone);

            // Fallback: If phone is still empty or phoneIdx wasn't found, check other cells in this row
            if (empty($phone)) {
                foreach ($row as $cIdx => $cVal) {
                    if ($cIdx === $nameIdx || $cIdx === $pinIdx || $cIdx === $commissionIdx) {
                        continue;
                    }
                    $candidatePhone = static::cleanPhoneNumber((string)$cVal);
                    if (!empty($candidatePhone)) {
                        $phone = $candidatePhone;
                        break;
                    }
                }
            }

            $location = ($locationIdx !== null && isset($row[$locationIdx]) && trim((string)$row[$locationIdx]) !== '') ? trim((string)$row[$locationIdx]) : 'General';
            
            $commission = 10;
            if ($commissionIdx !== null && isset($row[$commissionIdx]) && is_numeric(trim((string)$row[$commissionIdx]))) {
                $commission = max(0, min(100, (int) round(floatval(trim((string)$row[$commissionIdx])))));
            }

            $pin = ($pinIdx !== null && isset($row[$pinIdx])) ? trim((string)$row[$pinIdx]) : '';
            
            // Validate pin or generate unique
            if (empty($pin) || strlen($pin) !== 4 || !ctype_digit($pin) || Agent::where('pin', $pin)->exists()) {
                $pin = Agent::generateUniquePin();
            }

            try {
                Agent::create([
                    'name' => $name,
                    'business_name' => $businessName,
                    'phone' => $phone,
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

    /**
     * Clean and normalize phone numbers.
     */
    public static function cleanPhoneNumber(string $val): ?string
    {
        $val = trim($val);
        // Strip Excel formula / quote wrappers like '0712345678 or ="0712345678" or double quotes
        $val = preg_replace('/^=\s*"([^"]+)"$/', '$1', $val);
        $val = trim($val, "'\"\t\r\n ");

        if (empty($val)) {
            return null;
        }

        // Handle scientific notation e.g., 2.54712E+11 or 7.12345678E+8
        if (stripos($val, 'e+') !== false || (is_numeric($val) && strpos($val, '.') !== false)) {
            $floatVal = floatval($val);
            if ($floatVal > 100000) {
                $val = sprintf('%.0f', $floatVal);
            }
        }

        // Strip floating point decimals like 712345678.0
        if (preg_match('/^(\d+)\.0+$/', $val, $m)) {
            $val = $m[1];
        }

        // Extract digits and optional leading +
        $hasPlus = str_starts_with($val, '+');
        $digitsOnly = preg_replace('/[^0-9]/', '', $val);

        // Check if digits length is plausible for phone number (7 to 15 digits)
        if (strlen($digitsOnly) < 7 || strlen($digitsOnly) > 15) {
            return null;
        }

        // Normalize Kenyan phone numbers:
        // 9 digits starting with 7 or 1 -> e.g. 712345678 -> +254712345678
        if (strlen($digitsOnly) === 9 && in_array($digitsOnly[0], ['7', '1'])) {
            return '+254' . $digitsOnly;
        }

        // 10 digits starting with 07 or 01 -> e.g. 0712345678 -> +254712345678
        if (strlen($digitsOnly) === 10 && str_starts_with($digitsOnly, '0')) {
            return '+254' . substr($digitsOnly, 1);
        }

        // 12 digits starting with 254 -> e.g. 254712345678 -> +254712345678
        if (strlen($digitsOnly) === 12 && str_starts_with($digitsOnly, '254')) {
            return '+' . $digitsOnly;
        }

        return $hasPlus ? '+' . $digitsOnly : '+' . $digitsOnly;
    }

    /**
     * Helper to match column headers against candidate list.
     */
    private static function findColumnIndex(array $headers, array $candidates): ?int
    {
        foreach ($headers as $idx => $header) {
            // Clean header: remove punctuation like ., #, _, -, and extra spaces
            $cleanHeader = strtolower(trim($header));
            $cleanHeader = preg_replace('/[._\-#]/', ' ', $cleanHeader);
            $cleanHeader = preg_replace('/\s+/', ' ', $cleanHeader);
            $cleanHeader = trim($cleanHeader);

            foreach ($candidates as $candidate) {
                $cleanCandidate = strtolower(trim($candidate));
                $cleanCandidate = preg_replace('/[._\-#]/', ' ', $cleanCandidate);
                $cleanCandidate = preg_replace('/\s+/', ' ', $cleanCandidate);
                $cleanCandidate = trim($cleanCandidate);

                if ($cleanHeader === $cleanCandidate) {
                    return $idx;
                }
            }
        }
        return null;
    }
}

