UPDATE `settings` SET `value` = '{\"version\":\"18.0.0\", \"code\":\"1800\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table users add os_name varchar(16) null after city_name;

-- SEPARATOR --

alter table users add browser_name varchar(32) null after city_name;

-- SEPARATOR --

alter table users add browser_language varchar(32) null after city_name;

-- SEPARATOR --

alter table users add device_type varchar(16) null after city_name;

-- SEPARATOR --

alter table users drop column last_user_agent;

-- SEPARATOR --

alter table users_logs add browser_name varchar(32) null;

-- SEPARATOR --

alter table users_logs add browser_language varchar(32) null;

-- SEPARATOR --

alter table qr_codes add embedded_data text null after settings;
