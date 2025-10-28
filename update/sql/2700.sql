UPDATE `settings` SET `value` = '{\"version\":\"27.0.0\", \"code\":\"2700\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

UPDATE settings SET `value` = JSON_SET(`value`, '$.blacklisted_domains', JSON_ARRAY()) WHERE `key` = 'users';

-- SEPARATOR --

UPDATE settings SET `value` = JSON_SET(`value`, '$.blacklisted_domains', JSON_ARRAY()) WHERE `key` = 'links';

-- SEPARATOR --

UPDATE settings SET `value` = JSON_SET(`value`, '$.blacklisted_keywords', JSON_ARRAY()) WHERE `key` = 'links';

-- SEPARATOR --

alter table statistics add continent_code varchar(8) null after country_code;
