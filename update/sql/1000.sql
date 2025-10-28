UPDATE `settings` SET `value` = '{\"version\":\"10.0.0\", \"code\":\"1000\"}' WHERE `key` = 'product_info';
-- EXTENDED SEPARATOR --

alter table taxes modify value float null;
