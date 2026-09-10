<?php
/**
 * Swamini Photography - Contact & Booking Enquiry API Endpoint
 */

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/mailer.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

// 1. Rate Limiting by IP
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateKey = 'rate_' . md5($clientIp);
$currentTime = time();

if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = ['count' => 1, 'first_request' => $currentTime];
} else {
    if ($currentTime - $_SESSION[$rateKey]['first_request'] > RATE_LIMIT_WINDOW) {
        $_SESSION[$rateKey] = ['count' => 1, 'first_request' => $currentTime];
    } else {
        $_SESSION[$rateKey]['count']++;
        if ($_SESSION[$rateKey]['count'] > RATE_LIMIT_MAX) {
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'message' => 'Too many requests. Please wait a few minutes before trying again or reach us directly on WhatsApp.'
            ]);
            exit;
        }
    }
}

// Read POST data (handles both form-data and application/json)
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);
$data = is_array($jsonData) ? $jsonData : $_POST;

// 2. Honeypot check (anti-bot)
if (!empty($data['website_url_hp']) || !empty($data['phone_hp'])) {
    // Silently accept bots without doing anything
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your enquiry has been received.'
    ]);
    exit;
}

// 3. CSRF Validation
$csrfToken = $data['csrf_token'] ?? '';
if (!validate_csrf_token($csrfToken)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Session expired or invalid security token. Please refresh the page.',
        'new_csrf_token' => generate_csrf_token()
    ]);
    exit;
}

// 4. Input Sanitization & Validation
$errors = [];

// Name
$name = trim($data['name'] ?? '');
if (empty($name) || mb_strlen($name) < 2) {
    $errors['name'] = 'Please enter your full name.';
} elseif (mb_strlen($name) > 100) {
    $name = mb_substr($name, 0, 100);
}

// Phone
$phone = preg_replace('/[^\d+]/', '', trim($data['phone'] ?? ''));
if (empty($phone) || strlen($phone) < 10 || strlen($phone) > 15) {
    $errors['phone'] = 'Please enter a valid 10-digit phone number.';
}

// Email (Optional, but validated if present)
$email = trim($data['email'] ?? '');
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}

// Service selection
$service = trim($data['service'] ?? '');
if (empty($service) || !array_key_exists($service, $VALID_SERVICES)) {
    $errors['service'] = 'Please select a photography service.';
}

// Event Date (Optional)
$eventDate = trim($data['event_date'] ?? '');
if (!empty($eventDate)) {
    $eventDate = htmlspecialchars(substr($eventDate, 0, 50), ENT_QUOTES, 'UTF-8');
}

// Message (Optional)
$message = trim($data['message'] ?? '');
if (!empty($message)) {
    $message = mb_substr($message, 0, 2000);
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Please correct the highlighted fields.',
        'errors' => $errors
    ]);
    exit;
}

// 5. Prepare and Dispatch Notifications
$serviceLabel = $VALID_SERVICES[$service] ?? $service;
$payload = [
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'service' => $service,
    'service_label' => $serviceLabel,
    'event_date' => $eventDate,
    'message' => $message,
    'ip' => $clientIp
];

// Admin notification
$adminSubject = "New Shoot Booking Enquiry: {$name} - {$serviceLabel}";
$adminHtml = SwaminiMailer::buildAdminNotificationEmail($payload);
$adminSent = SwaminiMailer::sendMail(BUSINESS_EMAIL, 'Swamini Photography Admin', $adminSubject, $adminHtml);

// Customer confirmation (if email was provided)
if (!empty($email)) {
    $customerSubject = "Thank you for contacting Swamini Photography & Cinematography";
    $customerHtml = SwaminiMailer::buildCustomerConfirmationEmail($payload);
    SwaminiMailer::sendMail($email, $name, $customerSubject, $customerHtml);
}

// Log enquiry to secure log file if required
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logLine = date('[Y-m-d H:i:s]') . " | {$name} | {$phone} | {$email} | {$serviceLabel} | {$eventDate} | IP: {$clientIp}\n";
@file_put_contents($logDir . '/enquiries.log', $logLine, FILE_APPEND | LOCK_EX);

// Refresh CSRF token for subsequent requests
$newToken = generate_csrf_token();

echo json_encode([
    'success' => true,
    'message' => 'Thank you, ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '! Your enquiry has been received. Navanit Patil will get in touch with you shortly.',
    'new_csrf_token' => $newToken
]);
