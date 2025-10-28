UPDATE `settings` SET `value` = '{\"version\":\"23.0.0\", \"code\":\"2300\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

CREATE TABLE `barcodes` (
`barcode_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` bigint unsigned NOT NULL,
`project_id` bigint unsigned DEFAULT NULL,
`name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
`type` varchar(32) DEFAULT NULL,
`value` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`barcode` varchar(40) NOT NULL,
`settings` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
`embedded_data` text COLLATE utf8mb4_unicode_ci,
`datetime` datetime NOT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`barcode_id`),
KEY `user_id` (`user_id`),
KEY `project_id` (`project_id`),
CONSTRAINT `barcodes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `barcodes_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

alter table plans add translations text null after description;

-- SEPARATOR --

alter table plans drop column monthly_price;

-- SEPARATOR --

alter table plans drop column annual_price;

-- SEPARATOR --

alter table plans drop column lifetime_price;

-- SEPARATOR --

alter table users modify plan_settings longtext null;

-- SEPARATOR --

alter table plans modify settings longtext not null;

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('codes', '{"qr_codes_is_enabled":1,"qr_reader_is_enabled":1,"barcodes_is_enabled":1,"barcode_reader_is_enabled":1,"logo_size_limit":1,"background_size_limit":1,"available_qr_codes":{"text":true,"url":true,"phone":true,"sms":true,"email":true,"whatsapp":true,"facetime":true,"location":true,"wifi":true,"event":true,"vcard":true,"crypto":true,"paypal":true,"upi":true,"epc":true,"pix":true},"available_barcodes":{"C32":true,"C39":true,"C39+":true,"C39E":true,"C39E+":true,"C93":true,"S25":true,"S25+":true,"I25":true,"I25+":true,"ITF14":true,"C128":true,"C128A":true,"C128B":true,"C128C":true,"EAN2":true,"EAN5":true,"EAN8":true,"EAN13":true,"UPCA":true,"UPCE":true,"MSI":true,"MSI+":true,"POSTNET":true,"PLANET":true,"TELEPENALPHA":true,"TELEPENNUMERIC":true,"RMS4CC":true,"KIX":true,"IMB":true,"CODABAR":true,"CODE11":true,"PHARMA":true,"PHARMA2T":true}}');

-- SEPARATOR --

alter table statistics add user_id bigint unsigned null after link_id;

-- SEPARATOR --

alter table statistics add constraint statistics_users_user_id_fk foreign key (user_id) references users (user_id) on update cascade on delete cascade;

-- SEPARATOR --

UPDATE statistics LEFT JOIN `links` ON `statistics`.`link_id` = `links`.`link_id` SET `statistics`.`user_id` = `links`.`user_id`;


