/*M!999999\- enable the sandbox mode */ 

DROP TABLE IF EXISTS `wp_options`;
CREATE TABLE `wp_options` (
  `option_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB AUTO_INCREMENT=234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_options` WRITE;
INSERT INTO `wp_options` VALUES (5,'blogdescription','','on');
INSERT INTO `wp_options` VALUES (4,'blogname','INTERNET MULTIFEED CO.','on');
INSERT INTO `wp_options` VALUES (3,'home','http://34.128.167.155','on');
INSERT INTO `wp_options` VALUES (29,'permalink_structure','/ja/%year%/%postname%/','on');
INSERT INTO `wp_options` VALUES (2,'siteurl','http://34.128.167.155','on');
INSERT INTO `wp_options` VALUES (42,'stylesheet','mfeed','on');
INSERT INTO `wp_options` VALUES (41,'template','mfeed','on');
UNLOCK TABLES;


