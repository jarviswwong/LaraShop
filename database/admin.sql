-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: larashop
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu18.04.1

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
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'首页','fa-bar-chart','/',NULL,'2018-12-06 14:39:07'),(2,0,7,'系统管理','fa-tasks',NULL,NULL,'2019-01-15 20:10:33'),(3,2,8,' 管理员','fa-users','auth/users',NULL,'2019-01-15 20:10:33'),(4,2,9,'角色','fa-user','auth/roles',NULL,'2019-01-15 20:10:33'),(5,2,10,'权限','fa-ban','auth/permissions',NULL,'2019-01-15 20:10:33'),(6,2,11,'菜单','fa-bars','auth/menu',NULL,'2019-01-15 20:10:33'),(7,2,12,'操作日志','fa-history','auth/logs',NULL,'2019-01-15 20:10:33'),(8,0,2,'用户管理','fa-user','/users','2018-12-06 14:40:16','2018-12-06 14:40:21'),(9,0,4,'商品管理','fa-product-hunt','/products','2018-12-06 14:41:12','2019-01-15 20:10:33'),(10,0,5,'订单管理','fa-first-order','/orders','2018-12-06 14:41:43','2019-01-15 20:10:33'),(11,0,6,'优惠券管理','fa-credit-card-alt','/coupon_codes','2018-12-06 14:42:32','2019-01-15 20:10:33'),(12,0,3,'商品类目管理','fa-th-list','/categories','2019-01-15 20:10:17','2019-01-15 20:10:33');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'All permission','*','','*',NULL,NULL),(2,'Dashboard','dashboard','GET','/',NULL,NULL),(3,'Login','auth.login','','/auth/login\r\n/auth/logout',NULL,NULL),(4,'User setting','auth.setting','GET,PUT','/auth/setting',NULL,NULL),(5,'Auth management','auth.management','','/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs',NULL,NULL),(6,'用户管理','users','','/users*','2018-12-06 14:44:00','2018-12-06 14:44:00'),(7,'商品管理','products','','/products*\r\n/product_skus*','2018-12-06 14:44:30','2018-12-06 14:44:30'),(8,'订单管理','orders','','/orders*','2018-12-06 14:44:44','2018-12-06 14:44:44'),(9,'优惠券管理','coupon_codes','','/coupon_codes*','2018-12-06 14:45:08','2018-12-06 14:45:08');
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (1,1,NULL,NULL),(2,2,NULL,NULL),(2,3,NULL,NULL),(2,4,NULL,NULL),(2,6,NULL,NULL),(2,7,NULL,NULL),(2,8,NULL,NULL),(2,9,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,NULL,NULL),(2,3,NULL,NULL);
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'Administrator','administrator','2018-12-06 14:34:35','2018-12-06 14:34:35'),(2,'运营','operator','2018-12-06 14:46:30','2018-12-06 14:46:30');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_user_permissions`
--

LOCK TABLES `admin_user_permissions` WRITE;
/*!40000 ALTER TABLE `admin_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$zFDwCzU5rGyWtiwTV8Ub..FGuAzAT7rL/3hQSW5ryvobxpc64KChm','玄霄','images/c8f01ee05075d060c925b74e32aa5de3.jpg','v9EFv9cIM4Kztin55yB6yuKEpzqDTynSa6wTqjSXXHwKrypwB8y1LYu7WAVW','2018-12-06 14:34:35','2018-12-06 16:18:38'),(3,'Tim','$2y$10$yzvyVlbx5/hYgexdHKV/ZuxAUQp8g7sYzhDjFOKrlccMLOsho/jzK','Tim','images/admin_avatar.png','9eKDAB9xWnOEzrWcMk11kGZlo2Gj51I7rMj71ZMSAb5YNcxuNqoUcnOkQat9','2018-12-06 14:47:07','2018-12-06 14:47:07');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-01-15 12:53:05
