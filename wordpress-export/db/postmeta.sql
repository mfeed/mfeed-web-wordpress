/*M!999999\- enable the sandbox mode */ 

DROP TABLE IF EXISTS `wp_postmeta`;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

LOCK TABLES `wp_postmeta` WRITE;
INSERT INTO `wp_postmeta` VALUES (1,2,'_wp_page_template','default');
INSERT INTO `wp_postmeta` VALUES (2,3,'_wp_page_template','default');
INSERT INTO `wp_postmeta` VALUES (3,5,'_edit_lock','1763704032:1');
INSERT INTO `wp_postmeta` VALUES (4,7,'_edit_lock','1763700945:1');
INSERT INTO `wp_postmeta` VALUES (5,5,'_edit_last','1');
INSERT INTO `wp_postmeta` VALUES (7,13,'_edit_lock','1763704029:1');
INSERT INTO `wp_postmeta` VALUES (8,19,'_edit_lock','1763703981:1');
INSERT INTO `wp_postmeta` VALUES (9,21,'_edit_lock','1763704003:1');
INSERT INTO `wp_postmeta` VALUES (10,23,'_edit_lock','1763704020:1');
INSERT INTO `wp_postmeta` VALUES (11,25,'_edit_lock','1763704079:1');
INSERT INTO `wp_postmeta` VALUES (12,31,'footnotes','');
INSERT INTO `wp_postmeta` VALUES (13,38,'_edit_lock','1763703979:1');
INSERT INTO `wp_postmeta` VALUES (14,40,'_edit_lock','1763704639:1');
INSERT INTO `wp_postmeta` VALUES (15,42,'_edit_lock','1763704609:1');
INSERT INTO `wp_postmeta` VALUES (16,44,'_edit_lock','1763704815:1');
INSERT INTO `wp_postmeta` VALUES (17,47,'_edit_lock','1764222036:1');
INSERT INTO `wp_postmeta` VALUES (18,49,'_edit_lock','1763704815:1');
INSERT INTO `wp_postmeta` VALUES (19,51,'_edit_lock','1763705021:1');
INSERT INTO `wp_postmeta` VALUES (20,53,'_edit_lock','1763705092:1');
INSERT INTO `wp_postmeta` VALUES (21,55,'_edit_lock','1763705266:1');
INSERT INTO `wp_postmeta` VALUES (22,57,'_edit_lock','1763705344:1');
INSERT INTO `wp_postmeta` VALUES (23,59,'_edit_lock','1763705406:1');
INSERT INTO `wp_postmeta` VALUES (24,61,'_edit_lock','1763705469:1');
INSERT INTO `wp_postmeta` VALUES (25,1,'_wp_trash_meta_status','publish');
INSERT INTO `wp_postmeta` VALUES (26,1,'_wp_trash_meta_time','1763705554');
INSERT INTO `wp_postmeta` VALUES (27,1,'_wp_desired_post_slug','hello-world');
INSERT INTO `wp_postmeta` VALUES (28,1,'_wp_trash_meta_comments_status','a:1:{i:1;s:1:\"1\";}');
INSERT INTO `wp_postmeta` VALUES (29,64,'_edit_lock','1764301734:1');
INSERT INTO `wp_postmeta` VALUES (30,66,'_edit_lock','1764301666:1');
INSERT INTO `wp_postmeta` VALUES (31,64,'_edit_last','1');
INSERT INTO `wp_postmeta` VALUES (32,68,'_edit_lock','1764301882:1');
INSERT INTO `wp_postmeta` VALUES (33,70,'_edit_lock','1764301858:1');
INSERT INTO `wp_postmeta` VALUES (34,72,'_edit_lock','1764301831:1');
INSERT INTO `wp_postmeta` VALUES (35,74,'_edit_lock','1764301881:1');
INSERT INTO `wp_postmeta` VALUES (36,79,'_edit_lock','1764301730:1');
INSERT INTO `wp_postmeta` VALUES (38,83,'_edit_lock','1764302409:1');
INSERT INTO `wp_postmeta` VALUES (39,85,'_edit_lock','1764305062:1');
INSERT INTO `wp_postmeta` VALUES (42,85,'_wp_old_date','2025-11-28');
INSERT INTO `wp_postmeta` VALUES (43,87,'_edit_lock','1764304513:1');
INSERT INTO `wp_postmeta` VALUES (46,87,'_wp_old_date','2025-11-28');
INSERT INTO `wp_postmeta` VALUES (47,89,'_edit_lock','1764679146:1');
INSERT INTO `wp_postmeta` VALUES (50,89,'_wp_old_date','2025-11-28');
INSERT INTO `wp_postmeta` VALUES (51,91,'_wp_attached_file','2025/11/20251001jpnap.png');
INSERT INTO `wp_postmeta` VALUES (52,91,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:188;s:6:\"height\";i:104;s:4:\"file\";s:25:\"2025/11/20251001jpnap.png\";s:8:\"filesize\";i:8361;s:5:\"sizes\";a:1:{s:9:\"thumbnail\";a:5:{s:4:\"file\";s:25:\"20251001jpnap-150x104.png\";s:5:\"width\";i:150;s:6:\"height\";i:104;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:8108;}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}');
INSERT INTO `wp_postmeta` VALUES (53,92,'_wp_attached_file','2025/11/20251001NTTsc.png');
INSERT INTO `wp_postmeta` VALUES (54,92,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:282;s:6:\"height\";i:70;s:4:\"file\";s:25:\"2025/11/20251001NTTsc.png\";s:8:\"filesize\";i:9065;s:5:\"sizes\";a:1:{s:9:\"thumbnail\";a:5:{s:4:\"file\";s:24:\"20251001NTTsc-150x70.png\";s:5:\"width\";i:150;s:6:\"height\";i:70;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:4439;}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}');
INSERT INTO `wp_postmeta` VALUES (55,93,'_wp_attached_file','2025/11/20251001optage.png');
INSERT INTO `wp_postmeta` VALUES (56,93,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:234;s:6:\"height\";i:124;s:4:\"file\";s:26:\"2025/11/20251001optage.png\";s:8:\"filesize\";i:10106;s:5:\"sizes\";a:1:{s:9:\"thumbnail\";a:5:{s:4:\"file\";s:26:\"20251001optage-150x124.png\";s:5:\"width\";i:150;s:6:\"height\";i:124;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:7861;}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}');
INSERT INTO `wp_postmeta` VALUES (57,94,'footnotes','');
INSERT INTO `wp_postmeta` VALUES (58,95,'_wp_attached_file','2025/11/20251001-ja.pdf');
INSERT INTO `wp_postmeta` VALUES (59,95,'_wp_attachment_metadata','a:1:{s:8:\"filesize\";i:295234;}');
INSERT INTO `wp_postmeta` VALUES (60,97,'_edit_lock','1764680691:1');
INSERT INTO `wp_postmeta` VALUES (63,97,'_wp_old_date','2025-12-02');
INSERT INTO `wp_postmeta` VALUES (65,100,'_wp_attached_file','2025/12/20250328-ja.pdf');
INSERT INTO `wp_postmeta` VALUES (66,100,'_wp_attachment_metadata','a:1:{s:8:\"filesize\";i:359547;}');
INSERT INTO `wp_postmeta` VALUES (67,102,'_edit_lock','1764680595:1');
INSERT INTO `wp_postmeta` VALUES (70,102,'_wp_old_date','2025-12-02');
INSERT INTO `wp_postmeta` VALUES (71,104,'_wp_attached_file','2025/12/20250115-ja.pdf');
INSERT INTO `wp_postmeta` VALUES (72,104,'_wp_attachment_metadata','a:1:{s:8:\"filesize\";i:573852;}');
INSERT INTO `wp_postmeta` VALUES (73,105,'_wp_attached_file','2025/12/20250115-ja.png');
INSERT INTO `wp_postmeta` VALUES (74,105,'_wp_attachment_metadata','a:6:{s:5:\"width\";i:528;s:6:\"height\";i:362;s:4:\"file\";s:23:\"2025/12/20250115-ja.png\";s:8:\"filesize\";i:215142;s:5:\"sizes\";a:2:{s:6:\"medium\";a:5:{s:4:\"file\";s:23:\"20250115-ja-300x206.png\";s:5:\"width\";i:300;s:6:\"height\";i:206;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:58702;}s:9:\"thumbnail\";a:5:{s:4:\"file\";s:23:\"20250115-ja-150x150.png\";s:5:\"width\";i:150;s:6:\"height\";i:150;s:9:\"mime-type\";s:9:\"image/png\";s:8:\"filesize\";i:27238;}}s:10:\"image_meta\";a:12:{s:8:\"aperture\";s:1:\"0\";s:6:\"credit\";s:0:\"\";s:6:\"camera\";s:0:\"\";s:7:\"caption\";s:0:\"\";s:17:\"created_timestamp\";s:1:\"0\";s:9:\"copyright\";s:0:\"\";s:12:\"focal_length\";s:1:\"0\";s:3:\"iso\";s:1:\"0\";s:13:\"shutter_speed\";s:1:\"0\";s:5:\"title\";s:0:\"\";s:11:\"orientation\";s:1:\"0\";s:8:\"keywords\";a:0:{}}}');
INSERT INTO `wp_postmeta` VALUES (76,108,'_edit_lock','1764681866:1');
INSERT INTO `wp_postmeta` VALUES (77,109,'_wp_attached_file','2025/12/20240117-ja.pdf');
INSERT INTO `wp_postmeta` VALUES (78,109,'_wp_attachment_metadata','a:1:{s:8:\"filesize\";i:460520;}');
INSERT INTO `wp_postmeta` VALUES (81,108,'_wp_old_date','2025-12-02');
INSERT INTO `wp_postmeta` VALUES (82,112,'_edit_lock','1766066114:1');
INSERT INTO `wp_postmeta` VALUES (83,112,'_pingme','1');
INSERT INTO `wp_postmeta` VALUES (84,112,'_encloseme','1');
INSERT INTO `wp_postmeta` VALUES (85,114,'_edit_lock','1766120766:1');
INSERT INTO `wp_postmeta` VALUES (86,115,'_edit_lock','1766120769:1');
INSERT INTO `wp_postmeta` VALUES (87,116,'_edit_lock','1766120628:1');
INSERT INTO `wp_postmeta` VALUES (88,117,'_edit_lock','1766120776:1');
INSERT INTO `wp_postmeta` VALUES (89,118,'_edit_lock','1766120638:1');
INSERT INTO `wp_postmeta` VALUES (90,119,'_edit_lock','1766149723:1');
INSERT INTO `wp_postmeta` VALUES (91,119,'_pingme','1');
INSERT INTO `wp_postmeta` VALUES (92,119,'_encloseme','1');
INSERT INTO `wp_postmeta` VALUES (93,121,'_edit_lock','1766149753:1');
INSERT INTO `wp_postmeta` VALUES (94,121,'_pingme','1');
INSERT INTO `wp_postmeta` VALUES (95,121,'_encloseme','1');
UNLOCK TABLES;


