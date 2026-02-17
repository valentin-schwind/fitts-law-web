<?php
// submit_all.php
// Single endpoint to receive and insert all experiment data with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
require_once __DIR__ . '/data.php';

// Ensure POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'POST required']);
    exit;
}

// Parse input
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'JSON parse error: ' . json_last_error_msg(), 'raw' => $raw]);
    exit;
}

// Validate structure
if (!isset($payload['demographics'], $payload['trials'], $payload['movements'], $payload['summary'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid payload structure', 'payload' => $payload]);
    exit;
}

try {
    insertDemographics($payload['demographics']);
    foreach ($payload['trials'] as $e) insertTrialLog($e);
    foreach ($payload['movements'] as $e) insertMovementLog($e);
    insertOverallStats($payload['summary']);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
