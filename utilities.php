<?php
// utilities.php
// קובץ זה מרכז פונקציות עזר טכניות לשימוש בכל חלקי המערכת.

require_once __DIR__ . '/config.php';

/**
 * כותב הודעה לקובץ לוג אחיד של המערכת.
 */
function log_event($message, $data = null) {
    $logFile = __DIR__ . '/system_log_' . date('Y-m-d') . '.txt';
    $time = date('[Y-m-d H:i:s]');
    $logEntry = "$time $message";
    if ($data) {
        $logEntry .= " | DATA: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    file_put_contents($logFile, $logEntry . "\n", FILE_APPEND);
}

/**
 * שולח בקשת API ל-Airtable (גרסה משופרת עם טיפול שגיאות חזק)
 */
function airtableRequest($url, $method = 'GET', $data = null) {
    // Check if cURL extension is loaded
    if (!function_exists('curl_init')) {
        log_event('Fatal Error: cURL extension not found.');
        throw new Exception('Server configuration error: cURL extension is missing.');
    }

    $ch = curl_init();
    $headers = ['Authorization: Bearer ' . AIRTABLE_API_KEY, 'Content-Type: application/json'];
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_TIMEOUT => 30,
    ]);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        log_event('cURL Error', ['error' => $curlError, 'url' => $url]);
        throw new Exception('Network error during Airtable request');
    }

    $decoded = json_decode($response, true);

    if ($httpCode >= 400) {
        $errorMessage = "Airtable API Error (HTTP {$httpCode})";
        if (is_array($decoded) && isset($decoded['error']['message'])) {
            $errorMessage = $decoded['error']['message'];
        } else if (!empty($response)) {
            $errorMessage .= ". Non-JSON response received.";
            log_event('Airtable Non-JSON Error Body', ['body_snippet' => substr($response, 0, 200)]);
        }
        log_event('Airtable API Error', ['http_code' => $httpCode, 'message' => $errorMessage]);
        throw new Exception($errorMessage);
    }
    
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        log_event('JSON Decode Error', ['http_code' => $httpCode, 'response_start' => substr($response, 0, 200)]);
        throw new Exception('Failed to decode JSON response from Airtable.');
    }
    
    return $decoded;
}

/**
 * שולח תגובת JSON סטנדרטית בחזרה ללקוח.
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

/**
 * מנקה ומחטא קלט מהמשתמש.
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * ממיר תאריך לפורמט עברי קריא.
 */
function formatHebrewDate($date) {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    $days = ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'];
    $months = ['ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר'];
    
    $dayName = $days[date('w', $timestamp)];
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp) - 1]; // <-- This line was fixed
    $year = date('Y', $timestamp);
    
    return "יום {$dayName}, {$day} ב{$month} {$year}";
}
?>