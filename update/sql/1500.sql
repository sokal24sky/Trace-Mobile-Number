UPDATE `settings` SET `value` = '{\"version\":\"15.0.0\", \"code\":\"1500\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('languages', '{}');

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`) VALUES ('content', '{"blog_is_enabled":true,"blog_share_is_enabled":true,"blog_search_widget_is_enabled":false,"blog_categories_widget_is_enabled":true,"blog_popular_widget_is_enabled":true,"blog_views_is_enabled":true,"pages_is_enabled":true,"pages_share_is_enabled":true,"pages_popular_widget_is_enabled":true,"pages_views_is_enabled":true}');
