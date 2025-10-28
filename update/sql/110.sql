UPDATE `settings` SET `value` = '{\"version\":\"1.1.0\", \"code\":\"110\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table links modify location_url varchar(2048) null;
