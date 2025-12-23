<?php
/**
 * Link Poster for Telegram - Backend
 * Posts links to Telegram groups via Bot API
 * Modify this code to suit your requirements
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ============================================
// CONFIGURATION - Update these values!
// ============================================

// Your Telegram Bot Token (get from @BotFather)
$BOT_TOKEN = 'YOUR_BOT_TOKEN_HERE';

// Array of Telegram Chat IDs (index 0-3 corresponds to Group1-Group4)
// To get Chat ID: Add bot to group, send a message, then visit:
// https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates
$GROUPS = [
    '-1001234567890',  // Index 0: Group1 - Replace with actual Chat ID
    '-1001234567891',  // Index 1: Group2 - Replace with actual Chat ID
    '-1001234567892',  // Index 2: Group3 - Replace with actual Chat ID
    '-1001234567893',  // Index 3: Group4 - Replace with actual Chat ID
];


// ============================================
// Logging Function
// ============================================

function logRequest($url, $groupIndex, $success, $message) {
    $GROUP_NAMES = [
        "Group1",
        "Group2",
        "Group3",
        "Group4",
    ];
    $timestamp = date('Y-m-d H:i:s');
    $status = $success ? 'SUCCESS' : 'FAILED';
    $logLine = "[$timestamp] [$status] Group:".$GROUP_NAMES[$groupIndex]." | URL: $url | Message: $message" . PHP_EOL;
    file_put_contents(__DIR__ . '/postlink.log', $logLine, FILE_APPEND | LOCK_EX);
    error_log("Link Poster Extension - $logLine");
}

// ============================================
// Main Logic
// ============================================

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logRequest('N/A', -1, false, 'Only POST requests allowed');
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests allowed'
    ]);
    exit();
}

// Get POST data
$url = isset($_POST['url']) ? trim($_POST['url']) : '';
$groupIndex = isset($_POST['group']) ? intval($_POST['group']) : -1;

// Validate inputs
if (empty($url)) {
    logRequest($url, $groupIndex, false, 'URL is required');
    echo json_encode([
        'success' => false,
        'message' => 'URL is required'
    ]);
    exit();
}

if ($groupIndex < 0 || $groupIndex >= count($GROUPS)) {
    logRequest($url, $groupIndex, false, 'Invalid group selected');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid group selected'
    ]);
    exit();
}

// Validate URL format
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    logRequest($url, $groupIndex, false, 'Invalid URL format');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid URL format'
    ]);
    exit();
}

// Check if bot token is configured
if ($BOT_TOKEN === 'YOUR_BOT_TOKEN_HERE') {
    logRequest($url, $groupIndex, false, 'Bot token not configured');
    echo json_encode([
        'success' => false,
        'message' => 'Bot token not configured. Please update postlink.php'
    ]);
    exit();
}

// Get chat ID for selected group
$chatId = $GROUPS[$groupIndex];

// Send message to Telegram
$telegramUrl = "https://api.telegram.org/bot{$BOT_TOKEN}/sendMessage";
$message = "🔗 " . $url;

$postData = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML',
    'disable_web_page_preview' => false
];

//testing
logRequest($url, $groupIndex, true, 'Link posted successfully (test mode)');
echo json_encode([
    'success' => true,
    'message' => 'Link posted successfully! ✅'
]);
die();
//end testing

// Use cURL to send request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Handle response
if ($curlError) {
    logRequest($url, $groupIndex, false, 'Connection error: ' . $curlError);
    echo json_encode([
        'success' => false,
        'message' => 'Connection error: ' . $curlError
    ]);
    exit();
}

$result = json_decode($response, true);

if ($httpCode === 200 && isset($result['ok']) && $result['ok']) {
    logRequest($url, $groupIndex, true, 'Link posted successfully');
    echo json_encode([
        'success' => true,
        'message' => 'Link posted successfully! ✅'
    ]);
} else {
    $errorMsg = isset($result['description']) ? $result['description'] : 'Unknown error';
    logRequest($url, $groupIndex, false, 'Telegram error: ' . $errorMsg);
    echo json_encode([
        'success' => false,
        'message' => 'Telegram error: ' . $errorMsg
    ]);
}
