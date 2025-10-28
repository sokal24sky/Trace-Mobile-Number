UPDATE `settings` SET `value` = '{\"version\":\"36.0.0\", \"code\":\"3600\"}' WHERE `key` = 'product_info';

-- SEPARATOR --

alter table pages add plans_ids text null after pages_category_id;

-- SEPARATOR --

alter table statistics add project_id bigint unsigned null after user_id;

-- SEPARATOR --

UPDATE statistics JOIN links ON statistics.link_id = links.link_id SET statistics.project_id = links.project_id;

-- SEPARATOR --

alter table statistics add constraint statistics_projects_project_id_fk foreign key (project_id) references projects (project_id) on update cascade on delete set null;

