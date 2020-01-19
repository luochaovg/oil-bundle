DROP TABLE IF EXISTS `{TABLE_NAME}`;
CREATE TABLE `{TABLE_NAME}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `command` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition` json DEFAULT NULL,
  `resource_need` tinyint unsigned NOT NULL DEFAULT '1',
  `done_percent` float unsigned NOT NULL DEFAULT '0',
  `remark` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
