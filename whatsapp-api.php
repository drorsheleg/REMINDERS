<?php
/**
 * WhatsApp API Service - GREEN API VERSION
 * Location: /app/calendar/whatsapp-api.php
 * Version: 15.0 - Final & Complete
 */

// For future debugging, you can uncomment these lines
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

date_default_timezone_set('Asia/Jerusalem');

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/utilities.php';

// Green API Constants
define('GREEN_API_INSTANCE', '7105302600');
define('GREEN_API_TOKEN', 'bb12357af3e647b7a48e7b744cc57db5620065d9f9d8493197');
define('GREEN_API_URL', 'https://7105.api.greenapi.com');

function sendWhatsAppMessage($phone, $message, $context = '') {
    if (empty($phone) || empty($message)) {
        log_event('WhatsApp send failed: empty phone or message', ['context' => $context]);
        return ['success' => false, 'error' => 'Empty phone or message.'];
    }
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (substr($phone, 0, 1) == '0') { $phone = '972' . substr($phone, 1); } 
    elseif (strlen($phone) < 12 && substr($phone, 0, 3) != '972') { $phone = '972' . $phone; }
    $chatId = $phone . '@c.us';
    $url = GREEN_API_URL . "/waInstance" . GREEN_API_INSTANCE . "/sendMessage/" . GREEN_API_TOKEN;
    $data = ["chatId" => $chatId, "message" => $message];
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($data), CURLOPT_HTTPHEADER => ["Content-Type: application/json"], CURLOPT_TIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => false]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error) {
        log_event("WhatsApp cURL Error", ['error' => $error, 'context' => $context]);
        return ['success' => false, 'error' => $error];
    }
    $result = json_decode($response, true);
    $success = ($httpCode == 200 && isset($result['idMessage']));
    log_event($success ? "WhatsApp Success" : "WhatsApp Failed", ['response' => $result, 'http_code' => $httpCode, 'context' => $context]);
    return ['success' => $success, 'response' => $result, 'error' => $success ? null : ($result['error'] ?? 'Unknown error')];
}

function getClientFromAirtable($clientId) {
    if (empty($clientId)) return null;
    try {
        return airtableRequest(AIRTABLE_API_URL . 'Clients/' . $clientId)['fields'] ?? null;
    } catch (Exception $e) {
        log_event("Failed to get client from Airtable", ['client_id' => $clientId, 'error' => $e->getMessage()]);
        return null;
    }
}

function getBookingsForDate($date) {
    $formula = urlencode("AND({Date} = '{$date}', NOT({Status} = 'בוטל'))");
    $url = AIRTABLE_API_URL . urlencode('Bookings') . '?filterByFormula=' . $formula;
    try {
        $data = airtableRequest($url);
        $processedBookings = [];
        foreach ($data['records'] ?? [] as $booking) {
            $fields = $booking['fields'];
            if (empty($fields['Phone Number'][0])) continue;
            $processedBookings[] = ['client_name' => $fields['Client Name Lookup'][0] ?? 'לא ידוע', 'phone' => $fields['Phone Number'][0], 'time' => $fields['Time'] ?? 'לא צוין', 'cars' => $fields['Number of Cars'] ?? '1', 'notes' => $fields['Notes'] ?? '', 'address' => $fields['Address'][0] ?? '', 'city' => $fields['City'][0] ?? ''];
        }
        return ['success' => true, 'bookings' => $processedBookings];
    } catch (Exception $e) {
        return ['success' => false, 'bookings' => []];
    }
}

function getTomorrowBookings() {
    return getBookingsForDate(date('Y-m-d', strtotime('+1 day')));
}

function buildReminderMessage($booking, $date) {
    $hebrewDate = formatHebrewDate($date);
    $message = "🔔 תזכורת מקאר וושר\n\nשלום {$booking['client_name']},\n\nמזכירים לך על הזמנת הרחיצה שלך מחר:\n\n📅 תאריך: {$hebrewDate}\n⏰ שעה: {$booking['time']}\n🚗 מספר כלי רכב: {$booking['cars']}";
    if (!empty($booking['address'])) { $message .= "\n📍 כתובת: {$booking['address']}" . (!empty($booking['city']) ? ", {$booking['city']}" : ""); }
    if (!empty($booking['notes'])) { $message .= "\n💬 הערות: {$booking['notes']}"; }
    $message .= "\n\nאנא הקפידו להכין את הרכב לרחיצה:\n• חנו אותו במקום מוצל או מקורה\n• ודאו גישה נוחה לרכב\n• במקרה של ביטול - נא ליצור קשר מראש\n\nנשמח לראותכם מחר! 🚗✨\n\nצוות קאר וושר\n📞 ליצירת קשר: 054-995-2960";
    return $message;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
log_event("WhatsApp API Request", ['action' => $action, 'method' => $_SERVER['REQUEST_METHOD']]);
$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    switch ($action) {
        
        case 'send_pending_confirmation_to_client':
        case 'send_pending_notification_to_manager':
            $date = $input['date'] ?? '';
            $time = $input['time'] ?? '';
            if (empty($date) || empty($time)) { sendJsonResponse(['success' => false, 'error' => 'Missing data for notification.'], 400); }
            $formattedDate = date("d/m/Y", strtotime($date));
            if ($action === 'send_pending_confirmation_to_client') {
                $clientName = $input['clientName'] ?? 'לקוח/ה יקר/ה';
                $clientPhone = $input['clientPhone'] ?? '';
                if(empty($clientPhone)) { sendJsonResponse(['success' => false, 'error' => 'Missing client phone.'], 400); }
                $message = "היי {$clientName} 👋\n\nקיבלנו את בקשתך לתיאום תור!\n\nפרטי הבקשה:\n📅 תאריך: {$formattedDate}\n⏰ שעה: {$time}\n\nההזמנה ממתינה כעת לאישור סופי ממנהל היומן 🗓️✅\nניצור איתך קשר בהקדם האפשרי לאישור.\n\nתודה שבחרת בקאר וושר! 🚙💦";
                $result = sendWhatsAppMessage($clientPhone, $message, 'pending_confirmation_client');
            } else {
                $managerPhone = '972549952960';
                $clientName = $input['clientName'] ?? 'לקוח לא ידוע';
                $clientPhone = $input['clientPhone'] ?? 'לא נמסר';
                $message = "🔔 התראה: התקבלה בקשה חדשה לתיאום תור.\n\n👤 שם: {$clientName}\n📞 טלפון: {$clientPhone}\n🗓️ תאריך: {$formattedDate}\n⏰ שעה: {$time}\n\nהבקשה ממתינה לאישורך במערכת ניהול היומן.";
                $result = sendWhatsAppMessage($managerPhone, $message, 'pending_notification_manager');
            }
            sendJsonResponse($result);
            break;

        case 'send_booking_confirmation':
        case 'send_booking_update':
        case 'send_booking_cancellation':
            $clientId = $input['clientId'] ?? '';
            $bookingDate = $input['date'] ?? '';
            $bookingTime = $input['time'] ?? '';
            if (empty($clientId) || empty($bookingDate) || empty($bookingTime)) { sendJsonResponse(['success' => false, 'error' => "Missing data for action: $action."], 400); }
            $client = getClientFromAirtable($clientId);
            if (!$client) { sendJsonResponse(['success' => false, 'error' => 'Client not found'], 404); }
            if (empty($client['Phone Number'])) { sendJsonResponse(['success' => false, 'error' => 'Client has no phone number.'], 400); }
            $message = "שלום {$client['Full Name']} 👋\n\n";
            if ($action === 'send_booking_confirmation') { $message .= "✅ ההזמנה שלך אושרה בהצלחה!\n\n📅 תאריך: " . formatHebrewDate($bookingDate) . "\n⏰ שעה: {$bookingTime}"; } 
            elseif ($action === 'send_booking_update') { $message .= "🔄 ההזמנה שלך עודכנה בהצלחה!\n\n📅 תאריך מעודכן: " . formatHebrewDate($bookingDate) . "\n⏰ שעה מעודכנת: {$bookingTime}"; }
            elseif ($action === 'send_booking_cancellation') { $message .= "❌ ההזמנה שלך בוטלה.\n\n📅 תאריך שבוטל: " . formatHebrewDate($bookingDate) . "\n⏰ שעה שבוטלה: {$bookingTime}\n\nניתן לקבוע תור חדש בכל עת."; }
            $message .= "\n\nצוות קאר וושר 🚙💦";
            $result = sendWhatsAppMessage($client['Phone Number'], $message, $action);
            sendJsonResponse($result);
            break;
        
        case 'send_recurring_booking_confirmation': // Action sent by index.php
        case 'send_recurring_limited': // Kept for backward compatibility
            $clientId = $input['clientId'] ?? '';
            $occurrences = $input['nextOccurrences'] ?? ($input['dates'] ?? []);
            $bookingTime = $input['time'] ?? '';
            if (empty($clientId) || empty($occurrences)) { sendJsonResponse(['success' => false, 'error' => 'Missing data for recurring notification'], 400); }
            $client = getClientFromAirtable($clientId);
            if (!$client) { sendJsonResponse(['success' => false, 'error' => 'Client not found'], 404); }
            if (empty($client['Phone Number'])) { sendJsonResponse(['success' => false, 'error' => 'Client has no phone number.'], 400); }
            $dateStrings = is_array($occurrences[0]) ? array_column($occurrences, 'hebrew') : $occurrences;
            $message = "שלום {$client['Full Name']} 👋\n\n✅ נקבע לך תיאום מחזורי!\n\n📅 הפגישות הקרובות:\n";
            foreach (array_slice($dateStrings, 0, 3) as $index => $date) { $message .= ($index + 1) . ". " . $date . "\n"; }
            $message .= "\n...וכך הלאה.\n⏰ שעה קבועה: {$bookingTime}\n\nנשמח לראותך!\nצוות קאר וושר 🚙💦";
            $result = sendWhatsAppMessage($client['Phone Number'], $message, 'recurring_confirmation');
            sendJsonResponse($result);
            break;

        case 'send_booking_upgraded_to_recurring':
            $clientId = $input['clientId'] ?? '';
            $startDateStr = $input['date'] ?? '';
            $time = $input['time'] ?? '';
            $frequency = $input['frequency'] ?? 'weekly';
            if (empty($clientId) || empty($startDateStr) || empty($time)) { sendJsonResponse(['success' => false, 'error' => 'Missing client ID, date, or time'], 400); break; }
            $client = getClientFromAirtable($clientId);
            if (!$client) { sendJsonResponse(['success' => false, 'error' => 'Client not found'], 404); break; }
            $nextDates = [];
            $currentDate = new DateTime($startDateStr);
            $interval = new DateInterval(($frequency === 'biweekly') ? 'P2W' : 'P1W');
            for ($i = 0; $i < 3; $i++) { $nextDates[] = formatHebrewDate($currentDate->format('Y-m-d')); $currentDate->add($interval); }
            $hebrewFrequency = ($frequency === 'biweekly') ? 'דו-שבועי' : 'שבועי';
            $messageBody = "שלום {$client['Full Name']} 👋\n\n✅ התיאום שלך שודרג בהצלחה לתיאום {$hebrewFrequency}!\n\nלהלן שלושת המועדים הקרובים שלך:\n📅 " . implode("\n📅 ", $nextDates) . "\n\n...וכך הלאה, באותה השעה ({$time}).\n\nנשמח לראותך!\nצוות קאר וושר 🚙💦";
            $result = sendWhatsAppMessage($client['Phone Number'], $messageBody, 'booking_upgraded_to_recurring');
            sendJsonResponse($result);
            break;

        case 'send_subscription_update':
            $clientId = $input['clientId'] ?? '';
            if (empty($clientId)) { sendJsonResponse(['success' => false, 'error' => 'Missing client ID'], 400); }
            $client = getClientFromAirtable($clientId);
            if (!$client) { sendJsonResponse(['success' => false, 'error' => 'Client not found'], 404); }
            $totalWashes = $input['totalWashes'] ?? 0;
            $remainingWashes = $input['remainingWashes'] ?? 0;
            $usedWashes = $totalWashes - $remainingWashes;
            $message = "שלום {$client['Full Name']} 👋\n\n📊 עדכון מצב הכרטיסייה שלך:\n\n✅ נוצלו: {$usedWashes} שטיפות\n📌 נותרו: {$remainingWashes} שטיפות\n📋 סה\"כ במנוי: {$totalWashes} שטיפות\n\n";
            if ($remainingWashes == 0) { $message .= "⚠️ שים לב! הכרטיסייה שלך הסתיימה.\n\nלחידוש המנוי:\n📱 פייבוקס: 054-995-2960\n💳 תשלום אונליין: https://pay.grow.link/cd84ac7b14e593cb4522049c4c9742cd-MTk2MjQ2OQ\n\n"; }
            elseif ($remainingWashes <= 2) { $message .= "⚠️ שים לב - נותרו לך רק {$remainingWashes} שטיפות!\nמומלץ לחדש את המנוי בקרוב.\n\n"; }
            $message .= "תודה שבחרת בקאר וושר! 🚙💦";
            $result = sendWhatsAppMessage($client['Phone Number'], $message, 'subscription_update');
            sendJsonResponse($result);
            break;
            
        case 'send_new_client_with_pin':
            $clientId = $input['clientId'] ?? '';
            $pinCode = $input['pinCode'] ?? '';
            if (empty($clientId) || empty($pinCode)) { sendJsonResponse(['success' => false, 'error' => 'Missing client ID or PIN'], 400); }
            $client = getClientFromAirtable($clientId);
            if (!$client) { sendJsonResponse(['success' => false, 'error' => 'Client not found'], 404); }
            $bookingDate = $input['date'] ?? '';
            $bookingTime = $input['time'] ?? '';
            $message = "שלום {$client['Full Name']} 👋\n\n🎉 ברוכים הבאים לקאר וושר!\n\n📱 פרטי הכניסה שלך לאזור האישי:\n🔢 קוד PIN: {$pinCode}\n📞 טלפון: {$client['Phone Number']}\n🔗 לינק לכניסה: https://carwasher.co.il/app/client/\n\n━━━━━━━━━━━━━━━━━━━━\n\n✅ ההזמנה שלך התקבלה בהצלחה!\n\n📅 תאריך: " . formatHebrewDate($bookingDate) . "\n⏰ שעה: {$bookingTime}";
            $message .= "\n\nנשמח לראותך!\nצוות קאר וושר 🚙💦";
            $result = sendWhatsAppMessage($client['Phone Number'], $message, 'new_client_with_pin');
            sendJsonResponse($result);
            break;
        
        case 'send_daily_reminders':
            log_event("Starting daily reminders process");
            $bookingsResult = getTomorrowBookings();
            if (!$bookingsResult['success']) { sendJsonResponse(['success' => false, 'error' => 'Failed to get tomorrow bookings'], 500); break; }
            $bookings = $bookingsResult['bookings'];
            $results = []; $successCount = 0; $failCount = 0;
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            foreach ($bookings as $booking) {
                $message = buildReminderMessage($booking, $tomorrow);
                $result = sendWhatsAppMessage($booking['phone'], $message, 'daily_reminder');
                if ($result['success']) { $successCount++; } else { $failCount++; }
                $results[] = ['client' => $booking['client_name'], 'success' => $result['success'], 'error' => $result['error'] ?? null];
                sleep(1);
            }
            log_event("Daily reminders complete", ['success' => $successCount, 'failed' => $failCount]);
            sendJsonResponse(['success' => true, 'date' => $tomorrow, 'sent' => count($bookings), 'successful' => $successCount, 'failed' => $failCount, 'details' => $results]);
            break;
        
        case 'send_custom':
            $phone = $input['phone'] ?? '';
            $message = $input['message'] ?? '';
            if (empty($phone) || empty($message)) { sendJsonResponse(['success' => false, 'error' => 'Missing phone or message'], 400); }
            $result = sendWhatsAppMessage($phone, $message, 'custom_message');
            sendJsonResponse($result);
            break;

        case 'test':
            sendJsonResponse(['success' => true, 'message' => 'WhatsApp API is responding correctly.']);
            break;

        default:
            sendJsonResponse(['success' => false, 'error' => "Invalid action '$action' specified."], 400);
            break;
    }
} catch (Throwable $e) { // Catches any fatal error or exception
    log_event('FATAL ERROR in whatsapp-api.php', ['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
    if (!headers_sent()) {
        sendJsonResponse(['success' => false, 'error' => 'An unexpected server error occurred. Check system logs.'], 500);
    }
}
?>