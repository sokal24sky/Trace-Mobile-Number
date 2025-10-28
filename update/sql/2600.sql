UPDATE `settings` SET `value` = '{\"version\":\"26.0.0\", \"code\":\"2600\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table blog_posts add image_description varchar(256) null after description;
