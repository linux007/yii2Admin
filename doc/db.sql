-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (i386)
--
-- Host: localhost    Database: yii2admin
-- ------------------------------------------------------
-- Server version	5.1.73

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `yii2admin`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `yii2admin` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `yii2admin`;

--
-- Table structure for table `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_assignment`
--

LOCK TABLES `auth_assignment` WRITE;
/*!40000 ALTER TABLE `auth_assignment` DISABLE KEYS */;
INSERT INTO `auth_assignment` VALUES ('root','1',1478162919);
/*!40000 ALTER TABLE `auth_assignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item`
--

LOCK TABLES `auth_item` WRITE;
/*!40000 ALTER TABLE `auth_item` DISABLE KEYS */;
INSERT INTO `auth_item` VALUES ('/admin/member/ajax-create',2,'创建管理员',NULL,NULL,1478246704,1478246704),('/admin/member/ajax-edit',2,'编辑管理员',NULL,NULL,1478246704,1478246704),('/admin/member/ajax-status-change',2,'用户状态修改',NULL,NULL,1478246704,1478246704),('/admin/member/index',2,'成员管理',NULL,NULL,1478246704,1478246704),('/admin/menu/index',2,'菜单管理',NULL,NULL,1478246704,1478246704),('/admin/permission/ajax-assign',2,'权限分配',NULL,NULL,1478246704,1478246704),('/admin/permission/ajax-manager',2,'权限管理',NULL,NULL,1478246704,1478246704),('/admin/permission/do-flush',2,'刷新权限',NULL,NULL,1478246704,1478246704),('/admin/role/ajax-create',2,'创建角色',NULL,NULL,1478246704,1478246704),('/admin/role/assign-member',2,'成员列表',NULL,NULL,1478246704,1478246704),('/admin/role/index',2,'角色管理',NULL,NULL,1478246704,1478246704),('/site/index',2,'Displays homepage.',NULL,NULL,1478246704,1478246704),('/site/login',2,'Login action.',NULL,NULL,1478246704,1478246704),('/site/logout',2,'Logout action.',NULL,NULL,1478246704,1478246704),('/treemanager/node/manage',2,'View, create, or update a tree node via ajax',NULL,NULL,1478246704,1478246704),('/treemanager/node/move',2,'Move a tree node',NULL,NULL,1478246704,1478246704),('/treemanager/node/remove',2,'Remove a tree node',NULL,NULL,1478246704,1478246704),('/treemanager/node/save',2,'Saves a node once form is submitted',NULL,NULL,1478246704,1478246704),('admin',2,NULL,NULL,NULL,1478246704,1478246704),('admin/member',2,NULL,NULL,NULL,1478246704,1478246704),('admin/menu',2,NULL,NULL,NULL,1478246704,1478246704),('admin/permission',2,NULL,NULL,NULL,1478246704,1478246704),('admin/role',2,NULL,NULL,NULL,1478246704,1478246704),('app',2,NULL,NULL,NULL,1478246704,1478246704),('editor',1,'编辑',NULL,NULL,1478585237,1478585237),('pm',1,NULL,NULL,NULL,1478577486,1478577486),('root',1,NULL,NULL,NULL,1478162892,1478162892),('site',2,NULL,NULL,NULL,1478246704,1478246704),('treemanager',2,NULL,NULL,NULL,1478246704,1478246704),('treemanager/node',2,NULL,NULL,NULL,1478246704,1478246704);
/*!40000 ALTER TABLE `auth_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_item_child`
--

LOCK TABLES `auth_item_child` WRITE;
/*!40000 ALTER TABLE `auth_item_child` DISABLE KEYS */;
INSERT INTO `auth_item_child` VALUES ('root','/admin/member/ajax-create'),('root','/admin/member/ajax-edit'),('root','/admin/member/ajax-status-change'),('root','/admin/member/index'),('root','/admin/menu/index'),('root','/admin/permission/ajax-assign'),('root','/admin/permission/ajax-manager'),('root','/admin/permission/do-flush'),('root','/admin/role/ajax-create'),('root','/admin/role/assign-member'),('root','/admin/role/index'),('root','/site/index'),('root','/site/login'),('root','/site/logout'),('root','/treemanager/node/manage'),('root','/treemanager/node/move'),('root','/treemanager/node/remove'),('root','/treemanager/node/save'),('root','admin'),('root','admin/member'),('root','admin/menu'),('root','admin/permission'),('root','admin/role'),('root','app'),('root','site'),('root','treemanager'),('root','treemanager/node');
/*!40000 ALTER TABLE `auth_item_child` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_menu`
--

DROP TABLE IF EXISTS `auth_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root` int(11) DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `lvl` smallint(5) NOT NULL,
  `name` varchar(60) NOT NULL,
  `route` varchar(200) NOT NULL DEFAULT '',
  `icon` varchar(255) DEFAULT NULL,
  `icon_type` tinyint(1) NOT NULL DEFAULT '1',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `display` tinyint(1) NOT NULL DEFAULT '1',
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `collapsed` tinyint(1) NOT NULL DEFAULT '0',
  `movable_u` tinyint(1) NOT NULL DEFAULT '1',
  `movable_d` tinyint(1) NOT NULL DEFAULT '1',
  `movable_l` tinyint(1) NOT NULL DEFAULT '1',
  `movable_r` tinyint(1) NOT NULL DEFAULT '1',
  `removable` tinyint(1) NOT NULL DEFAULT '1',
  `removable_all` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tbl_product_NK1` (`root`),
  KEY `tbl_product_NK2` (`lft`),
  KEY `tbl_product_NK3` (`rgt`),
  KEY `tbl_product_NK4` (`lvl`),
  KEY `tbl_product_NK5` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_menu`
--

LOCK TABLES `auth_menu` WRITE;
/*!40000 ALTER TABLE `auth_menu` DISABLE KEYS */;
INSERT INTO `auth_menu` VALUES (1,1,1,32,0,'admin','admin','',1,1,1,0,0,0,1,1,1,1,1,1,1,0),(2,1,2,11,1,'成员管理','admin/member','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(3,1,3,4,2,'成员管理','/admin/member/index','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(4,1,5,6,2,'创建管理员','/admin/member/ajax-create','',1,1,0,0,0,0,1,0,1,1,1,1,1,0),(5,1,7,8,2,'编辑管理员','/admin/member/ajax-edit','',1,1,0,0,0,0,1,0,1,1,1,1,1,0),(6,1,9,10,2,'用户状态修改','/admin/member/ajax-status-change','',1,1,0,0,0,0,1,0,1,1,1,1,1,0),(7,1,12,15,1,'菜单管理','admin/menu','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(8,1,13,14,2,'菜单管理','/admin/menu/index','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(9,1,16,23,1,'权限管理','admin/permission','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(10,1,17,18,2,'刷新权限','/admin/permission/do-flush','',1,1,0,0,0,0,1,0,1,1,1,1,1,0),(11,1,19,20,2,'权限管理','/admin/permission/ajax-manager','',1,1,0,0,0,0,1,0,1,1,1,1,1,0),(12,1,21,22,2,'权限分配','/admin/permission/ajax-assign','',1,1,0,0,0,0,1,0,1,1,1,1,1,0),(13,1,24,31,1,'角色管理','admin/role','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(14,1,25,26,2,'角色管理','/admin/role/index','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(15,1,27,28,2,'创建角色','/admin/role/ajax-create','',1,1,0,0,0,0,1,0,1,1,1,1,1,0),(16,1,29,30,2,'成员列表','/admin/role/assign-member','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(17,17,1,12,0,'treemanager','treemanager','',1,1,1,0,0,0,1,1,1,1,1,1,1,0),(18,17,2,11,1,'菜单管理','treemanager/node','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(19,17,3,4,2,'Saves a node once form is submitted','/treemanager/node/save','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(20,17,5,6,2,'View, create, or update a tree node via ajax','/treemanager/node/manage','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(21,17,7,8,2,'Remove a tree node','/treemanager/node/remove','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(22,17,9,10,2,'Move a tree node','/treemanager/node/move','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(23,23,1,10,0,'app','app','',1,1,1,0,0,0,1,1,1,1,1,1,1,0),(24,23,2,9,1,'Site controller','site','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(25,23,3,4,2,'Displays homepage.','/site/index','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(26,23,5,6,2,'Login action.','/site/login','',1,1,1,0,0,0,1,0,1,1,1,1,1,0),(27,23,7,8,2,'Logout action.','/site/logout','',1,1,1,0,0,0,1,0,1,1,1,1,1,0);
/*!40000 ALTER TABLE `auth_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_rule`
--

LOCK TABLES `auth_rule` WRITE;
/*!40000 ALTER TABLE `auth_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1474283248),('m130524_201442_init',1474283251),('m140506_102106_rbac_init',1474512231);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin','6YrcDPYehQbjFaAfSTTA9Jp-a_k15EzN','$2y$13$6fycwUdiWEiso4N7OW1mGuU494/9WnTwucYTjT8H8YMIc5zUIJs9i',NULL,'admin@163.com',10,1478253550,1478254016),(2,'pm','PdLY3MbYYE0IihPSzCcGK128Ulfyf8as','$2y$13$lXDk6VJNKgbTlsojg.jy7ugpFVMLZf/8cgABC3dYTJrjlUMM/aVym',NULL,'pm@qq.com',10,1478254231,1478576629);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-09 17:02:41
