CREATE TABLE IF NOT EXISTS `#__xdecaroanalytics_reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `provider_key` varchar(100) NOT NULL,
  `source_type` varchar(16) NOT NULL DEFAULT 'metric',
  `source_key` varchar(100) NOT NULL,
  `context_json` longtext NULL,
  `scheduled` tinyint(1) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  `modified` datetime NULL,
  `modified_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_provider_source` (`provider_key`,`source_type`,`source_key`),
  KEY `idx_state_scheduled` (`state`,`scheduled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__xdecaroanalytics_snapshots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `provider_key` varchar(100) NOT NULL,
  `metric_key` varchar(100) NOT NULL,
  `context_hash` char(64) NOT NULL,
  `value_numeric` decimal(30,8) NULL,
  `value_text` varchar(255) NULL,
  `payload_json` longtext NULL,
  `measured_at` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_metric_history` (`provider_key`,`metric_key`,`context_hash`,`measured_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__xdecaroanalytics_report_runs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int unsigned NOT NULL,
  `status` varchar(20) NOT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime NULL,
  `row_count` int unsigned NOT NULL DEFAULT 0,
  `result_json` longtext NULL,
  `error_text` text NULL,
  `created_by` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_report_status` (`report_id`,`status`),
  KEY `idx_started_at` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
