<?php
// Main API File - api.php
date_default_timezone_set('Asia/Jerusalem');

// Error Handling (secure for production)
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/api_error_log.txt');

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ===== CONFIGURATION =====
define('AIRTABLE_API_KEY', 'pathuFCjvJf2CApny.c30c729d8cd0c65c04579cd7184dfb4f227f0bbe548dd84dcc105f4dd4e5feef');
define('AIRTABLE_BASE_ID', 'app4IGrifGnO7iqyl');
define('AIRTABLE_API_URL', 'https://api.airtable.com/v0/' . AIRTABLE_BASE_ID . '/');

// ===== HELPER FUNCTIONS =====

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

function logError($message) {
    $logFile = ini_get('error_log');
    $timestamp = date('[Y-m-d H:i:s]');
    file_put_contents($logFile, $timestamp . ' ' . $message . PHP_EOL, FILE_APPEND);
}

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function airtableRequest($url, $method = 'GET', $data = null) {
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
        logError('cURL Error: ' . $curlError);
        sendJsonResponse(['error' => ['message' => 'Network error']], 500);
    }

    $decoded = json_decode($response, true);

    if ($httpCode >= 400) {
        $errorMessage = $decoded['error']['message'] ?? 'Unknown Airtable error.';
        logError("Airtable API Error ({$httpCode}): {$errorMessage}");
        sendJsonResponse(['error' => ['message' => $errorMessage]], $httpCode);
    }
    
    return $decoded;
}

// ===== API FUNCTIONS =====

function getUpcomingAppointments() {
    $formula = "AND(OR({Date} = TODAY(), {Date} = DATEADD(TODAY(), 1, 'days')), NOT({Status} = 'בוטל'))";
    $url = AIRTABLE_API_URL . urlencode('Bookings') . '?filterByFormula=' . urlencode($formula);

    $allBookings = [];
    $offset = null;
    do {
        $requestUrl = $offset ? $url . '&offset=' . urlencode($offset) : $url;
        $response = airtableRequest($requestUrl);
        $allBookings = array_merge($allBookings, $response['records'] ?? []);
        $offset = $response['offset'] ?? null;
    } while ($offset);

    $processedAppointments = [];
    $now = new DateTime('now', new DateTimeZone('Asia/Jerusalem'));
    $in24Hours = (clone $now)->modify('+24 hours');

    foreach ($allBookings as $booking) {
        $fields = $booking['fields'];

        if (empty($fields['Date']) || empty($fields['Time']) || empty($fields['Phone Number'][0])) {
            continue;
        }
        
        try {
            $appointmentDateTime = new DateTime($fields['Date'] . ' ' . $fields['Time'], new DateTimeZone('Asia/Jerusalem'));
            
            if ($appointmentDateTime > $now && $appointmentDateTime <= $in24Hours) {
                $processedAppointments[] = [
                    'id' => $booking['id'],
                    'appointment_time' => $appointmentDateTime->format(DateTime::ATOM),
                    'client_name' => $fields['Client Name Lookup'][0] ?? 'לא ידוע',
                    'phone' => $fields['Phone Number'][0],
                    'cars' => $fields['Number of Cars'] ?? 1,
                ];
            }
        } catch (Exception $e) {
            logError("Invalid date/time for booking ID {$booking['id']}: {$fields['Date']} {$fields['Time']}");
            continue;
        }
    }

    return ['success' => true, 'appointments' => $processedAppointments];
}

function logSentReminder($input) {
    if (empty($input)) return ['success' => false, 'error' => 'No input data'];

    $fields = [
        'ClientName'   => $input['client_name'] ?? null,
        'Phone'        => $input['phone'] ?? null,
        'ReminderType' => $input['reminder_type'] ?? null,
        'Status'       => $input['status'] ?? null,
        'Details'      => $input['details'] ?? null,
    ];

    $response = airtableRequest(AIRTABLE_API_URL . urlencode('ReminderLogs'), 'POST', ['fields' => $fields]);
    return ['success' => true, 'log_id' => $response['id'] ?? null];
}

function getSentReminders() {
    $formula = "IS_AFTER({SentAt}, DATEADD(NOW(), -2, 'days'))";
    $sort = '&sort[0][field]=SentAt&sort[0][direction]=desc';
    $url = AIRTABLE_API_URL . urlencode('ReminderLogs') . '?filterByFormula=' . urlencode($formula) . $sort;
    
    $response = airtableRequest($url);
    $logs = $response['records'] ?? [];

    $processedHistory = [];
    foreach ($logs as $log) {
        $fields = $log['fields'];
        $processedHistory[] = [
            'id' => $log['id'],
            'client_name' => $fields['ClientName'] ?? 'N/A',
            'phone' => $fields['Phone'] ?? 'N/A',
            'reminder_type' => $fields['ReminderType'] ?? 'N/A',
            'status' => $fields['Status'] ?? 'N/A',
            'details' => $fields['Details'] ?? '',
            'sent_at' => $fields['SentAt'],
        ];
    }

    return ['success' => true, 'history' => $processedHistory];
}

function testDatabaseConnection() {
    try {
        airtableRequest(AIRTABLE_API_URL . urlencode('Clients') . '?maxRecords=1');
        return ['success' => true, 'message' => 'Connection successful.'];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ===== MAIN ROUTER =====

$action = sanitizeInput($_GET['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'];

$input = [];
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    if (!empty($rawInput)) {
        $decoded = json_decode($rawInput, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $input = sanitizeInput($decoded);
        }
    }
}

switch ($action) {
    case 'get_upcoming_appointments':
        sendJsonResponse(getUpcomingAppointments());
        break;
        
    case 'get_sent_reminders':
        sendJsonResponse(getSentReminders());
        break;

    case 'log_sent_reminder':
        if ($method !== 'POST') sendJsonResponse(['error' => 'Invalid method'], 405);
        sendJsonResponse(logSentReminder($input), 201);
        break;

    case 'test_connection':
        sendJsonResponse(testDatabaseConnection());
        break;

    default:
        sendJsonResponse(['error' => ['message' => "Unknown action requested: '{$action}'"]], 400);
        break;
}

?>