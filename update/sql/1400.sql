UPDATE `settings` SET `value` = '{\"version\":\"14.0.0\", \"code\":\"1400\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('theme', '');

-- SEPARATOR --

create index users_logs_ip_type_datetime_index on users_logs (ip, type, datetime);

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('linkedin', '{}');

-- SEPARATOR --

alter table broadcasts add views bigint unsigned default 0 null;

-- SEPARATOR --

alter table broadcasts add clicks bigint unsigned default 0 null after views;

-- SEPARATOR --

CREATE TABLE `broadcasts_statistics` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned DEFAULT NULL,
`broadcast_id` bigint unsigned DEFAULT NULL,
`type` enum('view','click') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`target` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `broadcast_id` (`broadcast_id`),
KEY `broadcasts_statistics_user_id_broadcast_id_type_index` (`broadcast_id`,`user_id`,`type`),
CONSTRAINT `broadcasts_statistics_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `broadcasts_statistics_ibfk_2` FOREIGN KEY (`broadcast_id`) REFERENCES `broadcasts` (`broadcast_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('internal_notifications', '{}');

-- SEPARATOR --

alter table users add has_pending_internal_notifications tinyint default 0 null after is_newsletter_subscribed;

-- SEPARATOR --

CREATE TABLE `internal_notifications` (
`internal_notification_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned DEFAULT NULL,
`for_who` varchar(16) DEFAULT NULL,
`from_who` varchar(16) DEFAULT NULL,
`icon` varchar(64) DEFAULT NULL,
`title` varchar(128) DEFAULT NULL,
`description` varchar(1024) DEFAULT NULL,
`url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`is_read` tinyint DEFAULT '0',
`datetime` datetime DEFAULT NULL,
`read_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`internal_notification_id`),
KEY `user_id` (`user_id`),
KEY `users_notifications_for_who_idx` (`for_who`) USING BTREE,
CONSTRAINT `internal_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

alter table broadcasts add settings text null after segment;

