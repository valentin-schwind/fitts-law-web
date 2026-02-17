<?php
// data.php
// Generic data‑insertion functions for Fitts Law experiment
require_once __DIR__ . '/database.php';

function insertDemographics(array $d): bool {
    global $pdo;
    $sql = "
        INSERT INTO `demographics` (
            `subject_id`,`exp_count`,`timestamp`,`age`,`gender`,`nationality`,
            `occupation`,`degree`,`workplace`,`noise`,`lighting`,`posture`,
            `device`,`displays`,`fitts_familiar`,`handedness`,
            `screen_width`,`screen_height`,`avail_width`,`avail_height`,
            `device_pixel_ratio`,`language`,`platform`,`user_agent`,
            `touch_support`,`js_heap_limit`,`total_js_heap`,`used_js_heap`
        ) VALUES (
            :subject_id,:exp_count,:timestamp,:age,:gender,:nationality,
            :occupation,:degree,:workplace,:noise,:lighting,:posture,
            :device,:displays,:fitts_familiar,:handedness,
            :screen_width,:screen_height,:avail_width,:avail_height,
            :device_pixel_ratio,:language,:platform,:user_agent,
            :touch_support,:js_heap_limit,:total_js_heap,:used_js_heap
        )
    ";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($d);
}

function insertTrialLog(array $e): bool {
    global $pdo;
    $sql = "
        INSERT INTO `trial_log` (
            `subject_id`,`exp_count`,`timestamp`,
            `A`,`W`,`trial_index`,`from_x`,`from_y`,
            `to_x`,`to_y`,`click_x`,`click_y`,
            `time_ms`,`hit`
        ) VALUES (
            :subject_id,:exp_count,:timestamp,
            :A,:W,:trial_index,:from_x,:from_y,
            :to_x,:to_y,:click_x,:click_y,
            :time_ms,:hit
        )
    ";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($e);
}

function insertMovementLog(array $e): bool {
    global $pdo;
    $sql = "
        INSERT INTO `movement_log` (
            `subject_id`,`exp_count`,`timestamp`,
            `A`,`W`,`trial_index`,`from_x`,`from_y`,
            `to_x`,`to_y`,`path_time_ms`,`click_state`,
            `hit`,`mouse_x`,`mouse_y`
        ) VALUES (
            :subject_id,:exp_count,:timestamp,
            :A,:W,:trial_index,:from_x,:from_y,
            :to_x,:to_y,:path_time_ms,:click_state,
            :hit,:mouse_x,:mouse_y
        )
    ";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($e);
}

function insertOverallStats(array $s): bool {
    global $pdo;
    $sql = "
        INSERT INTO `overall_stats` (
            `subject_id`,`exp_count`,`timestamp`,
            `mean_mt_ms`,`error_rate_pct`,`mean_tp_bps`,
            `reg_a_ms`,`reg_b_ms_per_bit`,`r_squared`
        ) VALUES (
            :subject_id,:exp_count,:timestamp,
            :mean_mt_ms,:error_rate_pct,:mean_tp_bps,
            :reg_a_ms,:reg_b_ms_per_bit,:r_squared
        )
    ";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($s);
}
?>