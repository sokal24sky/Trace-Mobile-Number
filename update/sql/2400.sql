UPDATE `settings` SET `value` = '{\"version\":\"24.0.0\", \"code\":\"2400\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table qr_codes add qr_code_foreground varchar(40) null after qr_code_logo;

-- SEPARATOR --

alter table users add extra text null after preferences;
