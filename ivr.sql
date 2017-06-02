-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: ivr
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
-- Table structure for table `actions`
--

DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `number` varchar(10) NOT NULL,
  `body` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `updated_at` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `repeat_param` varchar(255) DEFAULT NULL,
  `confirm` varchar(255) DEFAULT NULL,
  `parameter` varchar(255) DEFAULT NULL,
  `request` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actions`
--

LOCK TABLES `actions` WRITE;
/*!40000 ALTER TABLE `actions` DISABLE KEYS */;
INSERT INTO `actions` VALUES (1,10,'*','http://google.com','subscribe','2016-11-17','2016-11-17',NULL,NULL,NULL,NULL),(2,10,'1','http://google.com.ng','subscribe','2016-11-17','2016-11-17',NULL,NULL,NULL,NULL),(3,10,'2','http://facebook.com','subscribe','2016-11-17','2016-11-17',NULL,NULL,NULL,NULL),(4,11,'*','http://freesia1.atp-sevas.com:8585/sevas/subengine?shortcode=38422&req_text=DEV&smscID=ETISALAT_BC&sub_source=MOBI_ETI&msisdn=%M','subscribe','2016-11-17','2016-11-17',NULL,NULL,NULL,NULL),(5,11,'1','http://google.com.ng','subscribe','2016-11-17','2016-11-17',NULL,NULL,NULL,NULL),(6,11,'2','http://facebook.com','subscribe','2016-11-17','2016-11-17',NULL,NULL,NULL,NULL),(7,12,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=WED&smscID=ETISALAT_BC&sub_source=IVR_ETI','subscribe','2016-11-28','2016-11-17',NULL,NULL,'msisdn','GET'),(8,13,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=GOAL&smscID=ETISALAT_BC&sub_source=IVR_ETI&msisdn=%M','subscribe','2016-11-23','2016-11-17',NULL,NULL,NULL,NULL),(9,15,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=SM&smscID=ETISALAT_BC&sub_source=IVR_ETI&msisdn=%M','subscribe','2016-11-23','2016-11-18',NULL,NULL,NULL,NULL),(12,17,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=WED&smscID=ETISALAT_BC&sub_source=IVR_ETI&msisdn=%M','subscribe','2016-11-23','2016-11-22',NULL,NULL,NULL,NULL),(14,16,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=WED&smscID=ETISALAT_BC&sub_source=IVR_ETI&msisdn=%M','subscribe','2016-11-23','2016-11-22','','','msisdn','GET'),(15,18,'3','http://google.com.ng','subscribe','2016-11-28','2016-11-28',NULL,NULL,'msisdn','GET'),(16,19,'*','http://78.110.170.51/go.php?text=HON','subscribe','2016-11-28','2016-11-28',NULL,NULL,'sender','GET'),(17,20,'*','http://5.189.166.153:8080/Subscription/SUBS?amount=50&mode=IVR&servicename=GETHIRED','subscribe','2016-11-28','2016-11-28',NULL,NULL,'ani','GET'),(18,21,'*','http://speeddial.iconcepts.com.ng/subscribe.aspx?servicename=COM&fullservicename=NAIJA%20Comedy','subscribe','2016-11-28','2016-11-28',NULL,NULL,'msisdn','GET'),(19,22,'*','http://speeddial.iconcepts.com.ng/subscribe.aspx?servicename=SHB&fullservicename=Skin%20Health%20Beauty','subscribe','2016-11-28','2016-11-28',NULL,NULL,'msisdn','GET'),(20,23,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=SOCIAL&smscID=ETISALAT_BC&sub_source=IVR_ETI','subscribe','2016-11-28','2016-11-28',NULL,NULL,'msisdn','GET'),(21,24,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=WED&smscID=ETISALAT_BC&sub_source=IVR_ETI','subscribe','2016-11-28','2016-11-28',NULL,NULL,'msisdn','GET'),(22,25,'*','http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=BALL&smscID=ETISALAT_BC&sub_source=IVR_ETI','subscribe','2016-11-28','2016-11-28',NULL,NULL,'msisdn','GET'),(23,26,'*','http://freesia1.atp-sevas.com:8585/sevas/subengine?shortcode=38422&req_text=CAR&smscID=ETISALAT_BC&sub_source=IVR_ETI','subscribe','2016-11-28','2016-11-28',NULL,NULL,'msisdn','GET'),(24,27,'2','http://google.com.ng','subscribe','2016-12-01','2016-12-01',NULL,NULL,'msisdn','GET'),(25,28,'3','http://google.com.ng','subscribe','2016-12-01','2016-12-01',NULL,NULL,'msisdn','GET');
/*!40000 ALTER TABLE `actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaigns` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `play_path` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `scheduled_time` time DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `body` varchar(255) DEFAULT NULL,
  `script` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `campaigns`
--

LOCK TABLES `campaigns` WRITE;
/*!40000 ALTER TABLE `campaigns` DISABLE KEYS */;
INSERT INTO `campaigns` VALUES (25,'Football Icon ','Get  Football Facts about your favorite football players, free for 5days\r\n','/opt/ivr/files/tm30/005.wav','/var/lib/asterisk/sounds/files/tm30/005.wav','2016-11-28','2016-12-10',NULL,1,'http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=BALL&smscID=ETISALAT_BC&sub_source=IVR_ETI',NULL,'subscribe','2016-11-28','2016-11-28','tm30'),(26,'Career Tips','Grab this wonderful opportunity to receive daily Career tips, Free for 5days.','/opt/ivr/files/tm30/007.wav','/var/lib/asterisk/sounds/files/tm30/007.wav','2016-11-28','2016-12-10',NULL,1,'http://freesia1.atp-sevas.com:8585/sevas/subengine?shortcode=38422&req_text=DEV&smscID=ETISALAT_BC&sub_source=IVR_ETI',NULL,'subscribe','2016-11-28','2016-11-28','tm30'),(24,'Wedding Tips','Get Daily tips on How to plan the next big Wedding, Free for 5days.\r\n','/opt/ivr/files/tm30/006.wav','/var/lib/asterisk/sounds/files/tm30/006.wav','2016-11-28','2016-12-01',NULL,0,'http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=WED&smscID=ETISALAT_BC&sub_source=IVR_ETI',NULL,'subscribe','2016-12-01','2016-11-28','tm30'),(23,'Social Media Engagement Tips','Increase your followership with Daily Tips on Social Media Engagement, Free  for 5days','/opt/ivr/files/tm30/008a.wav','/var/lib/asterisk/sounds/files/tm30/008a.wav','2016-11-28','2016-12-01',NULL,0,'http://spexweb.atp-sevas.com:8585/sevas/subengine?shortcode=55010&req_text=SOCIAL&smscID=ETISALAT_BC&sub_source=IVR_ETI',NULL,'subscribe','2016-12-01','2016-11-28','tm30'),(19,' Honda Maintenance tips','Subscribe to Honda Maintenance tips at N50 for 5days','/opt/ivr/files/etisalat/001.wav','/var/lib/asterisk/sounds/files/etisalat/001.wav','2016-11-28','2016-12-01',NULL,0,'http://78.110.170.51/go.php?text=HON',NULL,'subscribe','2016-12-01','2016-11-28','etisalat'),(20,'Job openings & interview tips','Enjoy job openings & interview tips at N50 for 7day','/opt/ivr/files/etisalat/002.wav','/var/lib/asterisk/sounds/files/etisalat/002.wav','2016-11-28','2016-12-01',NULL,0,'http://5.189.166.153:8080/Subscription/SUBS?amount=50&mode=IVR&servicename=GETHIRED',NULL,'subscribe','2016-12-01','2016-11-28','etisalat'),(21,'Funniest jokes from your favorites comedians','Get the funniest jokes from your favorites comedians for just N10 daily','/opt/ivr/files/etisalat/003.wav','/var/lib/asterisk/sounds/files/etisalat/003.wav','2016-11-28','2016-12-02',NULL,1,'http://speeddial.iconcepts.com.ng/subscribe.aspx?servicename=COM&fullservicename=NAIJA%20Comedy',NULL,'subscribe','2016-11-28','2016-11-28','etisalat'),(22,'Natural hair, skin and beauty tips','Enjoy natural hair, skin and beauty tips for just N10 daily','/opt/ivr/files/etisalat/004.wav','/var/lib/asterisk/sounds/files/etisalat/004.wav','2016-11-28','2016-12-02',NULL,1,'http://speeddial.iconcepts.com.ng/subscribe.aspx?servicename=SHB&fullservicename=Skin%20Health%20Beauty',NULL,'subscribe','2016-11-28','2016-11-28','etisalat');
/*!40000 ALTER TABLE `campaigns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `size` float NOT NULL,
  `username` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `updated_at` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
INSERT INTO `files` VALUES (35,'Press 0 to confirm your subscription','Press 0 to confirm your subscription to this service',36.2402,'tm30','/opt/ivr/files/tm30/012.wav','audio/x-wav','00:00:05','2016-11-28','2016-11-28','prompt'),(34,'Social Media Engagement Tips','Increase your followership with Daily Tips on Social Media Engagement, Free  for 5days',52.0625,'tm30','/opt/ivr/files/tm30/008a.wav','audio/x-wav','00:00:07','2016-11-28','2016-11-28','advert'),(33,'Thank you for listening','Thank you for listening and good day.',31.4355,'etisalat','/opt/ivr/files/etisalat/015a.wav','audio/x-wav','00:00:04','2016-11-28','2016-11-28','prompt'),(32,'Wedding Tips','Get Daily tips on How to plan the next big Wedding, Free for 5days.\r\n',48.2793,'tm30','/opt/ivr/files/tm30/006.wav','audio/x-wav','00:00:06','2016-11-28','2016-11-28','advert'),(31,'Football Icon Ad','Get  Football Facts about your favorite football players, free for 5days\r\n',57.7969,'tm30','/opt/ivr/files/tm30/005.wav','audio/x-wav','00:00:07','2016-11-28','2016-11-28','advert'),(26,'Honda Maintenance','Subscribe to Honda Maintenance tips at N50 for 5days',45.3809,'etisalat','/opt/ivr/files/etisalat/001.wav','audio/x-wav','00:00:06','2016-11-28','2016-11-28','advert'),(27,'Job openings & interview tips','Enjoy job openings & interview tips at N50 for 7day',48.6797,'etisalat','/opt/ivr/files/etisalat/002.wav','audio/x-wav','00:00:06','2016-11-28','2016-11-28','advert'),(28,'Funniest jokes from your favorites comedians','Get the funniest jokes from your favorites comedians for just N10 daily',44.9492,'etisalat','/opt/ivr/files/etisalat/003.wav','audio/x-wav','00:00:06','2016-11-28','2016-11-28','advert'),(29,'Natural hair, skin and beauty tips','Enjoy natural hair, skin and beauty tips for just N10 daily',50.3496,'etisalat','/opt/ivr/files/etisalat/004.wav','audio/x-wav','00:00:06','2016-11-28','2016-11-28','advert'),(30,'Press * to subscribe to this service','Press * to subscribe to this service',31.7734,'etisalat','/opt/ivr/files/etisalat/011.wav','audio/x-wav','00:00:04','2016-11-28','2016-11-28','prompt'),(36,'To listen to this advert again','To listen to this advert again, press #',46.6816,'tm30','/opt/ivr/files/tm30/009.wav','audio/x-wav','00:00:06','2016-11-28','2016-11-28','prompt'),(37,'You have successfully subscribed','You have successfully subscribed to this service.',33.0918,'tm30','/opt/ivr/files/tm30/013.wav','audio/x-wav','00:00:04','2016-11-28','2016-11-28','prompt'),(38,'You have not made a selection','You have not made a selection',26.1934,'tm30','/opt/ivr/files/tm30/016.wav','audio/x-wav','00:00:03','2016-11-28','2016-11-28','prompt'),(39,'You haven\'t made a selection. To confirm subscription press 0','You haven\'t made a selection. To confirm subscription press 0',51.7031,'tm30','/opt/ivr/files/tm30/010.wav','audio/x-wav','00:00:07','2016-11-28','2016-11-28','prompt'),(40,'You have entered a wrong selection, please check and try again','You have entered a wrong selection, please check and try again',43.1973,'tm30','/opt/ivr/files/tm30/017.wav','audio/x-wav','00:00:06','2016-11-28','2016-11-28','prompt'),(41,'Please stay on the line to listen to the next advert','Please stay on the line to listen to the next advert',34.127,'tm30','/opt/ivr/files/tm30/018.wav','audio/x-wav','00:00:04','2016-11-28','2016-11-28','prompt'),(45,'Your subscription failed - Already subscribed','Your subscription failed because you have subscribed to this service previously',35.0605,'tm30','/opt/ivr/files/tm30/010a.wav','audio/x-wav','00:00:04','2016-12-01','2016-12-01','prompt'),(43,'Career tips','Grab this wonderful opportunity to receive daily Career tips, Free for 5days.\r\n',51.8828,'tm30','/opt/ivr/files/tm30/007.wav','audio/x-wav','00:00:07','2016-11-28','2016-11-28','advert'),(44,'Your subscription failed - Insufficient balance','Your subscription failed because you have insufficient balance in your account',36.9414,'tm30','/opt/ivr/files/tm30/009a.wav','audio/x-wav','00:00:05','2016-12-01','2016-12-01','prompt');
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `incorrect_path` varchar(255) NOT NULL,
  `repeat_path` varchar(255) NOT NULL,
  `confirmation_path` varchar(255) NOT NULL,
  `advert_limit` int(11) NOT NULL,
  `wrong_path` varchar(255) NOT NULL,
  `success_path` varchar(255) NOT NULL,
  `failure_path` varchar(255) NOT NULL,
  `goodbye_path` varchar(255) NOT NULL,
  `continue_path` varchar(255) NOT NULL,
  `no_selection_path` varchar(255) NOT NULL,
  `selection_confirmation_path` varchar(255) NOT NULL,
  `subscription_failure_path` varchar(255) NOT NULL,
  `default_settings` tinyint(1) DEFAULT '1',
  `updated_at` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (2,'/opt/ivr/files/tm30/012.wav','/opt/ivr/files/tm30/009.wav','/opt/ivr/files/tm30/012.wav',4,'','','','','','','','',1,'2016-11-28','2016-11-28');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `impressions_count` int(11) NOT NULL,
  `success_count` int(11) NOT NULL,
  `updated_at` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `updated_at` date DEFAULT NULL,
  `created_at` date DEFAULT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'etisalat','d8f2d633ce47d16ecd59b8d56db74fdfc7d5ecb3b4641d5ac6e593c7cefcd55c99cf33b861d9b565d985978a1b55daea8ec9a32bbf801f7e2155d1f251806fe3',NULL,NULL),(2,'tm30','a5e948c637c668d6813b6cec4e98dcac13f8c6d837e138b8a688265bad357d1d5a79df0dacda734dfe2ae0257cf8de1d5994585226419e04fcd22c2ca6ad8b77',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-18 23:03:51
