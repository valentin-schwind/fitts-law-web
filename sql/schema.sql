-- Drop existing tables if they exist
DROP TABLE IF EXISTS `movement_log`;
DROP TABLE IF EXISTS `overall_stats`;
DROP TABLE IF EXISTS `trial_log`;
DROP TABLE IF EXISTS `subjects`;

-- Create new tables with adjusted NULL settings

CREATE TABLE `subjects` (
  `subject_id` bigint NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(64) DEFAULT NULL,
  `exp_count` int DEFAULT NULL,
  `created_at` varchar(64) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `gender` varchar(32) DEFAULT NULL,
  `nationality` varchar(64) DEFAULT NULL,
  `occupation` varchar(128) DEFAULT NULL,
  `degree` varchar(128) DEFAULT NULL,
  `workplace` varchar(64) DEFAULT NULL,
  `noise` varchar(32) DEFAULT NULL,
  `lighting` varchar(32) DEFAULT NULL,
  `posture` varchar(32) DEFAULT NULL,
  `device` varchar(64) DEFAULT NULL,
  `displays` int DEFAULT NULL,
  `fitts_familiar` tinyint(1) DEFAULT NULL,
  `handedness` varchar(32) DEFAULT NULL,
  `screen_width` int DEFAULT NULL,
  `screen_height` int DEFAULT NULL,
  `avail_width` int DEFAULT NULL,
  `avail_height` int DEFAULT NULL,
  `device_pixel_ratio` float DEFAULT NULL,
  `language` varchar(16) DEFAULT NULL,
  `platform` varchar(64) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `touch_support` tinyint(1) DEFAULT NULL,
  `js_heap_limit` bigint DEFAULT NULL,
  `total_js_heap` bigint DEFAULT NULL,
  `used_js_heap` bigint DEFAULT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `movement_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `subject_id` bigint DEFAULT NULL,
  `subject_code` varchar(64) DEFAULT NULL,
  `exp_count` int DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `A` int DEFAULT NULL,
  `W` int DEFAULT NULL,
  `IoD` int DEFAULT NULL,
  `from_x` double DEFAULT NULL,
  `from_y` double DEFAULT NULL,
  `to_x` double DEFAULT NULL,
  `to_y` double DEFAULT NULL,
  `path_time_ms` double DEFAULT NULL,
  `click_state` tinyint(1) DEFAULT NULL,
  `hit` tinyint(1) DEFAULT NULL,
  `mouse_x` double DEFAULT NULL,
  `mouse_y` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `movement_log_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `trial_log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `subject_id` bigint DEFAULT NULL,
  `subject_code` varchar(64) DEFAULT NULL,
  `exp_count` int DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `A` int DEFAULT NULL,
  `W` int DEFAULT NULL,
  `IoD` int DEFAULT NULL,
  `from_x` double DEFAULT NULL,
  `from_y` double DEFAULT NULL,
  `to_x` double DEFAULT NULL,
  `to_y` double DEFAULT NULL,
  `click_x` double DEFAULT NULL,
  `click_y` double DEFAULT NULL,
  `time_ms` double DEFAULT NULL,
  `hit` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `trial_log_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `overall_stats` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `subject_id` bigint DEFAULT NULL,
  `subject_code` varchar(64) DEFAULT NULL,
  `exp_count` int DEFAULT NULL,
  `timestamp` varchar(64) DEFAULT NULL,
  `mean_mt_ms` double DEFAULT NULL,
  `error_rate_pct` double DEFAULT NULL,
  `mean_tp_bps` double DEFAULT NULL,
  `reg_a_ms` double DEFAULT NULL,
  `reg_b_ms_per_bit` double DEFAULT NULL,
  `r_squared` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `overall_stats_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;