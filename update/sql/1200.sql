UPDATE `settings` SET `value` = '{\"version\":\"12.0.0\", \"code\":\"1200\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table users add city_name varchar(32) null after country;

-- SEPARATOR --

alter table users add continent_code varchar(8) null after city_name;

-- SEPARATOR --

alter table users_logs add city_name varchar(32) null after country_code;

-- SEPARATOR --

alter table users_logs add continent_code varchar(8) null after city_name;

-- SEPARATOR --

alter table blog_posts modify url varchar(256) not null;

-- SEPARATOR --

alter table blog_posts modify title varchar(256) not null DEFAULT '';

-- SEPARATOR --

alter table blog_posts modify description varchar(256) DEFAULT NULL;

-- SEPARATOR --

alter table blog_posts_categories modify url varchar(256) not null;

-- SEPARATOR --

alter table blog_posts_categories modify description varchar(256) DEFAULT NULL;

-- SEPARATOR --

alter table blog_posts_categories modify title varchar(256) not null DEFAULT '';

-- SEPARATOR --

alter table pages modify url varchar(256) not null;

-- SEPARATOR --

alter table pages modify title varchar(256) not null default '';

-- SEPARATOR --

alter table pages modify description varchar(256) default null;

-- SEPARATOR --

alter table pages_categories modify url varchar(256) not null;

-- SEPARATOR --

alter table pages_categories modify title varchar(256) not null default '';

-- SEPARATOR --

alter table pages_categories modify description varchar(256) default null;
