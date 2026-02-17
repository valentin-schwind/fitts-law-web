<?php
require_once 'config.php'; // $pdo = deine vorbereitete PDO Verbindung

// Read incoming POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

// Extract data
$demographicData = $input['demographicData'] ?? null;
$clickLog = $input['clickLog'] ?? [];
$movementLog = $input['movementLog'] ?? [];
$overallStats = $input['overallStats'] ?? null;

if (!$demographicData || !$overallStats) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
    exit;
}

// Helper: Safe NULL insertion
function safe($value) {
    return isset($value) ? $value : null;
}

try {
    $pdo->beginTransaction();

    // 1. Insert Demographics
    $insertStmt = $pdo->prepare("
    INSERT INTO subjects (
        subject_code, created_at, exp_count, age, gender, nationality, occupation, degree, workplace,
        noise, lighting, posture, disability, device, device_type, displays, fitts_familiar, handedness,
        screen_width, screen_height, avail_width, avail_height, device_pixel_ratio,
        language, platform, user_agent, touch_support, js_heap_limit, total_js_heap, used_js_heap,
        fatigued, relaxed, content, worried, stressed, tense, upset, calm, focused, frustrated,
        motivated, secure, bored, confident, challenged, engaged, anxious, energized,
        confused, distracted, hungry,
        bfi_reserved, bfi_trusting, bfi_lazy, bfi_relaxed, bfi_artistic,
        bfi_sociable, bfi_find_fault, bfi_thorough_job, bfi_nervous, bfi_imagination
    ) VALUES (
        :subject_code, :created_at, :exp_count, :age, :gender, :nationality, :occupation, :degree, :workplace,
        :noise, :lighting, :posture, :disability, :device, :device_type, :displays, :fitts_familiar, :handedness,
        :screen_width, :screen_height, :avail_width, :avail_height, :device_pixel_ratio,
        :language, :platform, :user_agent, :touch_support, :js_heap_limit, :total_js_heap, :used_js_heap,
        :fatigued, :relaxed, :content, :worried, :stressed, :tense, :upset, :calm, :focused, :frustrated,
        :motivated, :secure, :bored, :confident, :challenged, :engaged, :anxious, :energized,
        :confused, :distracted, :hungry,
        :bfi_reserved, :bfi_trusting, :bfi_lazy, :bfi_relaxed, :bfi_artistic,
        :bfi_sociable, :bfi_find_fault, :bfi_thorough_job, :bfi_nervous, :bfi_imagination
    )
");

    $insertStmt->execute([
        ':subject_code' => $demographicData['subjectCode'],
        ':created_at' => date('Y-m-d H:i:s'),
        ':exp_count' => safe($demographicData['expCount']),
        ':age' => safe($demographicData['age']),
        ':gender' => safe($demographicData['gender']),
        ':nationality' => safe($demographicData['nationality']),
        ':occupation' => safe($demographicData['occupation']),
        ':degree' => safe($demographicData['degree']),
        ':workplace' => safe($demographicData['workplace']),
        ':noise' => safe($demographicData['noise']),
        ':lighting' => safe($demographicData['lighting']),
        ':posture' => safe($demographicData['posture']),
        ':disability' => safe($demographicData['disability']),
        ':device' => safe($demographicData['device']),
        ':displays' => safe($demographicData['displays']),
        ':fitts_familiar' => (safe($demographicData['fittsFamiliar']) === 'yes') ? 1 : 0,
        ':handedness' => safe($demographicData['handedness']),
        ':screen_width' => safe($demographicData['screenWidth']),
        ':screen_height' => safe($demographicData['screenHeight']),
        ':avail_width' => safe($demographicData['screenAvailWidth']),
        ':avail_height' => safe($demographicData['screenAvailHeight']),
        ':device_pixel_ratio' => safe($demographicData['devicePixelRatio']),
        ':language' => safe($demographicData['language']),
        ':platform' => safe($demographicData['platform']),
        ':user_agent' => safe($demographicData['userAgent']),
        ':device_type' => safe($demographicData['deviceType']),
        ':touch_support' => (safe($demographicData['touchSupport'])) ? 1 : 0,
        ':js_heap_limit' => safe($demographicData['jsHeapSizeLimit']),
        ':total_js_heap' => safe($demographicData['totalJSHeapSize']),
        ':used_js_heap' => safe($demographicData['usedJSHeapSize']),
        ':fatigued' => safe($demographicData['fatigued']),
        ':relaxed' => safe($demographicData['relaxed']),
        ':content' => safe($demographicData['content']),
        ':worried' => safe($demographicData['worried']),
        ':stressed' => safe($demographicData['stressed']),
        ':tense' => safe($demographicData['tense']),
        ':upset' => safe($demographicData['upset']),
        ':calm' => safe($demographicData['calm']),
        ':focused' => safe($demographicData['focused']),
        ':frustrated' => safe($demographicData['frustrated']),
        ':motivated' => safe($demographicData['motivated']),
        ':secure' => safe($demographicData['secure']),
        ':bored' => safe($demographicData['bored']),
        ':confident' => safe($demographicData['confident']),
        ':challenged' => safe($demographicData['challenged']),
        ':engaged' => safe($demographicData['engaged']),
        ':anxious' => safe($demographicData['anxious']),
        ':energized' => safe($demographicData['energized']),
        ':confused' => safe($demographicData['confused']),
        ':distracted' => safe($demographicData['distracted']),
        ':hungry' => safe($demographicData['hungry']),
        ':bfi_reserved' => safe($demographicData['bfi_reserved']),
        ':bfi_trusting' => safe($demographicData['bfi_trusting']),
        ':bfi_lazy' => safe($demographicData['bfi_lazy']),
        ':bfi_relaxed' => safe($demographicData['bfi_relaxed']),
        ':bfi_artistic' => safe($demographicData['bfi_artistic']),
        ':bfi_sociable' => safe($demographicData['bfi_sociable']),
        ':bfi_find_fault' => safe($demographicData['bfi_find_fault']),
        ':bfi_thorough_job' => safe($demographicData['bfi_thorough_job']),
        ':bfi_nervous' => safe($demographicData['bfi_nervous']),
        ':bfi_imagination' => safe($demographicData['bfi_imagination']),
    ]);

    $subjectId = $pdo->lastInsertId(); // use this for logging

    // 2. Insert Click Log
    $clickStmt = $pdo->prepare("
        INSERT INTO trial_log (
            subject_id, subject_code, exp_count, timestamp, A, W, IoD,
            from_x, from_y, to_x, to_y, click_x, click_y, time_ms, hit, device_type
        ) VALUES (
            :subject_id, :subject_code, :exp_count, :timestamp, :A, :W, :IoD,
            :from_x, :from_y, :to_x, :to_y, :click_x, :click_y, :time_ms, :hit, :device_type
        )
    ");

    foreach ($clickLog as $entry) {
        $clickStmt->execute([
            ':subject_id' => $subjectId, // <- NEW
            ':subject_code' => safe($entry['subjectCode']),
            ':exp_count' => safe($entry['expCount']),
            ':timestamp' => safe($entry['timestamp']),
            ':A' => safe($entry['A']),
            ':W' => safe($entry['W']),
            ':IoD' => safe($entry['IoD']),
            ':from_x' => safe($entry['fromX']),
            ':from_y' => safe($entry['fromY']),
            ':to_x' => safe($entry['toX']),
            ':to_y' => safe($entry['toY']),
            ':click_x' => safe($entry['clickX']),
            ':click_y' => safe($entry['clickY']),
            ':time_ms' => safe($entry['time']),
            ':hit' => isset($entry['hit']) ? (int)$entry['hit'] : null,
            ':device_type' => safe($entry['deviceType'])
        ]);
    }

    // 3. Insert Movement Log
    $moveStmt = $pdo->prepare("
        INSERT INTO movement_log (
            subject_id, subject_code, exp_count, timestamp, A, W, IoD,
            from_x, from_y, to_x, to_y, path_time_ms, click_state, hit, mouse_x, mouse_y, device_type
        ) VALUES (
            :subject_id, :subject_code, :exp_count, :timestamp, :A, :W, :IoD,
            :from_x, :from_y, :to_x, :to_y, :path_time_ms, :click_state, :hit, :mouse_x, :mouse_y, :device_type
        )
    ");

    foreach ($movementLog as $entry) {
        $moveStmt->execute([
            ':subject_id' => $subjectId, // <- NEW
            ':subject_code' => safe($entry['subjectCode']),
            ':exp_count' => safe($entry['expCount']),
            ':timestamp' => safe($entry['timestamp']),
            ':A' => safe($entry['A']),
            ':W' => safe($entry['W']),
            ':IoD' => safe($entry['IoD']),
            ':from_x' => safe($entry['fromX']),
            ':from_y' => safe($entry['fromY']),
            ':to_x' => safe($entry['toX']),
            ':to_y' => safe($entry['toY']),
            ':path_time_ms' => safe($entry['pathTime']),
            ':click_state' => isset($entry['click']) ? (int)$entry['click'] : null,
            ':hit' => isset($entry['hit']) ? (int)$entry['hit'] : null,
            ':mouse_x' => safe($entry['mouseX']),
            ':mouse_y' => safe($entry['mouseY']),
            ':device_type' => safe($entry['deviceType']),
        ]); 
    }


    // 4. Insert Overall Stats
    $statsStmt = $pdo->prepare("
        INSERT INTO overall_stats (
            subject_id, subject_code, exp_count, timestamp,
            mean_mt_ms, error_rate_pct, mean_tp_bps,
            reg_a_ms, reg_b_ms_per_bit, r_squared, rating, device_type
        ) VALUES (
            :subject_id, :subject_code, :exp_count, :timestamp,
            :mean_mt_ms, :error_rate_pct, :mean_tp_bps,
            :reg_a_ms, :reg_b_ms_per_bit, :r_squared, :rating, :device_type
        )
    ");

    $statsStmt->execute([
        ':subject_id' => $subjectId, // <- NEW
        ':subject_code' => safe($overallStats['subjectCode']),
        ':exp_count' => safe($overallStats['expCount']),
        ':timestamp' => safe($overallStats['timestamp']),
        ':mean_mt_ms' => safe($overallStats['mean MT (ms)']),
        ':error_rate_pct' => safe($overallStats['error rate (%)']),
        ':mean_tp_bps' => safe($overallStats['mean TP (bit/s)']),
        ':reg_a_ms' => safe($overallStats['regression a (ms)']),
        ':reg_b_ms_per_bit' => safe($overallStats['regression b (ms/bit)']),
        ':r_squared' => safe($overallStats['R²']),
        ':rating' => safe($overallStats['rating']),
        ':device_type' => safe($overallStats['deviceType']),
    ]);


    $pdo->commit();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
