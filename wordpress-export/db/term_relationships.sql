/*M!999999\- enable the sandbox mode */ 

DROP TABLE IF EXISTS `wp_term_relationships`;
CREATE TABLE `wp_term_relationships` (
  `object_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint unsigned NOT NULL DEFAULT '0',
  `term_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_term_relationships` WRITE;
INSERT INTO `wp_term_relationships` VALUES (1,1,0);
INSERT INTO `wp_term_relationships` VALUES (85,1,0);
INSERT INTO `wp_term_relationships` VALUES (87,1,0);
INSERT INTO `wp_term_relationships` VALUES (89,1,0);
INSERT INTO `wp_term_relationships` VALUES (97,1,0);
INSERT INTO `wp_term_relationships` VALUES (102,1,0);
INSERT INTO `wp_term_relationships` VALUES (108,1,0);
INSERT INTO `wp_term_relationships` VALUES (112,1,0);
INSERT INTO `wp_term_relationships` VALUES (119,1,0);
INSERT INTO `wp_term_relationships` VALUES (121,1,0);
UNLOCK TABLES;


