UPDATE `settings` SET `value` = '{\"version\":\"5.0.0\", \"code\":\"500\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('cookie_consent', '{}');

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('discord', '{}');

-- SEPARATOR --

alter table pages add open_in_new_tab tinyint default 1 null after position;

