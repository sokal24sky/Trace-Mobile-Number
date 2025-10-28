UPDATE `settings` SET `value` = '{\"version\":\"29.0.0\", \"code\":\"2900\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table users add qrcode_ai_qr_codes_current_month bigint default 0 null;

-- SEPARATOR --

CREATE TABLE `ai_qr_codes` (
`ai_qr_code_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`link_id` bigint unsigned DEFAULT NULL,
`user_id` bigint unsigned NOT NULL,
`project_id` bigint unsigned DEFAULT NULL,
`name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
`content` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`prompt` varchar(512) DEFAULT NULL,
`ai_qr_code` varchar(64) NOT NULL,
`settings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`embedded_data` text COLLATE utf8mb4_unicode_ci,
`datetime` datetime NOT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`ai_qr_code_id`),
KEY `user_id` (`user_id`),
KEY `project_id` (`project_id`),
KEY `link_id` (`link_id`),
CONSTRAINT `ai_qr_codes_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `ai_qr_codes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `ai_qr_codes_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
