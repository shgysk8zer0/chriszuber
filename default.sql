-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: user
-- ------------------------------------------------------
-- Server version	5.5.37-0ubuntu0.13.10.1

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
-- Table structure for table `CSP_errors`
--

DROP TABLE IF EXISTS `CSP_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CSP_errors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blocked-uri` varchar(100) NOT NULL,
  `document-uri` varchar(100) NOT NULL,
  `violated-directive` varchar(100) NOT NULL,
  `source-file` varchar(100) NOT NULL,
  `script-sample` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CSP_errors`
--

LOCK TABLES `CSP_errors` WRITE;
/*!40000 ALTER TABLE `CSP_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `CSP_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PHP_errors`
--

DROP TABLE IF EXISTS `PHP_errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PHP_errors` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `error_type` varchar(100) NOT NULL,
  `datetime` datetime NOT NULL,
  `error_message` varchar(100) NOT NULL,
  `file` varchar(100) DEFAULT NULL,
  `line` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PHP_errors`
--

LOCK TABLES `PHP_errors` WRITE;
/*!40000 ALTER TABLE `PHP_errors` DISABLE KEYS */;
/*!40000 ALTER TABLE `PHP_errors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `author` varchar(45) NOT NULL,
  `author_url` varchar(45) DEFAULT NULL,
  `author_email` varchar(45) DEFAULT NULL,
  `post` varchar(45) NOT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `footer_links`
--

DROP TABLE IF EXISTS `footer_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `footer_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(45) NOT NULL,
  `icon` varchar(45) NOT NULL,
  `alt` varchar(45) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_UNIQUE` (`url`),
  UNIQUE KEY `icon_UNIQUE` (`icon`),
  UNIQUE KEY `order_UNIQUE` (`order`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `footer_links`
--

LOCK TABLES `footer_links` WRITE;
/*!40000 ALTER TABLE `footer_links` DISABLE KEYS */;
INSERT INTO `footer_links` VALUES (1,'https://github.com/shgysk8zer0/chriszuber/','logos/github.svg','View my code on GitHub',1);
/*!40000 ALTER TABLE `footer_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `head`
--

DROP TABLE IF EXISTS `head`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `head` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `head`
--

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL,
  `content` longtext NOT NULL,
  `description` varchar(160) NOT NULL,
  `keywords` varchar(200) NOT NULL,
  `author` varchar(50) NOT NULL,
  `author_url` varchar(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (2,'Lorem Ipsum','<div id=\"lipsum\">\r\n<p>\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Proin dolor \r\nnunc, ullamcorper a quam eget, posuere vehicula diam. Praesent ante est,\r\n pellentesque sit amet convallis eget, bibendum et sem. Phasellus felis \r\nrisus, efficitur nec nunc quis, scelerisque vehicula nulla. In rutrum \r\nenim ut erat molestie tristique. Maecenas efficitur, sapien eget \r\nvolutpat consequat, quam est finibus mi, eu tempus est orci ac diam. Sed\r\n bibendum et augue in pellentesque. Cras semper felis nisl, sit amet \r\nmattis dolor facilisis vel. Etiam libero tortor, sagittis at est ut, \r\ncursus porta metus. Sed vitae suscipit eros. Phasellus mollis \r\ncondimentum felis, eu blandit libero eleifend a. Vestibulum viverra in \r\nnisl non semper. Integer consectetur viverra eros, vitae semper neque \r\ntempus vitae. Integer vel pellentesque nibh, sit amet consequat sem.\r\n</p>\r\n<p>\r\nMaecenas sit amet arcu sit amet erat elementum fringilla non vitae \r\naugue. Duis in orci ut lorem ornare condimentum. Quisque urna ex, mattis\r\n nec scelerisque in, condimentum pellentesque enim. Etiam bibendum \r\ninterdum ex sit amet lacinia. Cras ac bibendum mauris, ac tincidunt \r\nfelis. Duis accumsan placerat pellentesque. Duis cursus velit eget metus\r\n fermentum, et scelerisque nisi varius. Fusce erat leo, porttitor at \r\nnisi eu, placerat semper velit. Ut scelerisque facilisis elit a semper. \r\nFusce elementum, lectus sit amet accumsan varius, erat elit volutpat \r\ntortor, sit amet vulputate tortor libero sed diam.\r\n</p>\r\n<p>\r\nDonec placerat erat diam. Vivamus eleifend magna magna. Cras lacinia sem\r\n vel nunc elementum mattis. Maecenas elementum, nisi id commodo mollis, \r\ndolor metus dignissim libero, sit amet imperdiet purus turpis quis eros.\r\n Quisque in ex id justo dignissim finibus. Nulla vel ex massa. Nunc \r\nplacerat dictum fermentum. Cum sociis natoque penatibus et magnis dis \r\nparturient montes, nascetur ridiculus mus. Integer sed dui iaculis nunc \r\nfeugiat condimentum. Phasellus malesuada nisl sed lorem condimentum \r\nsagittis.\r\n</p>\r\n<p>\r\nAenean in tempus mi, sollicitudin rhoncus ipsum. Mauris sit amet \r\neleifend nisl, ut lobortis ipsum. Pellentesque commodo luctus justo quis\r\n rhoncus. Vestibulum auctor condimentum odio in tempor. Proin ornare \r\njusto nunc, at tristique magna convallis in. Donec pretium pulvinar \r\nporta. Phasellus metus urna, dictum vel nulla non, tincidunt iaculis \r\nlacus. Sed vel erat tempor, fringilla velit at, lobortis elit.\r\n</p>\r\n<p>\r\nCras dui erat, tincidunt non lectus quis, lobortis porttitor sapien. Sed\r\n vitae quam et tortor ultrices feugiat. Phasellus pulvinar massa \r\nvulputate nisi consectetur molestie. Vestibulum iaculis augue pharetra, \r\nvehicula quam eget, pretium sapien. Etiam egestas, massa in aliquet \r\nmalesuada, est metus faucibus magna, sit amet cursus lorem ante et \r\nlacus. Aenean congue, justo a accumsan pretium, odio metus facilisis \r\nest, sit amet suscipit dui orci non ante. Morbi ultricies velit erat, \r\nquis efficitur augue tincidunt at. Integer a accumsan turpis, ut \r\nporttitor urna. Duis laoreet mi dui, non rutrum urna scelerisque at. \r\nPhasellus vel est a arcu dignissim rutrum. Proin varius quam in tempor \r\nsuscipit. Phasellus quis lectus vel turpis lobortis euismod. Ut erat \r\nlectus, molestie vitae semper non, tempor eu ipsum. Maecenas nisl leo, \r\ncommodo vitae nulla et, efficitur tempor tellus. Sed efficitur, odio ac \r\naliquam tincidunt, lacus ante dictum libero, viverra iaculis massa \r\ntortor sed libero. Aliquam accumsan a est eu consectetur.\r\n</p></div>','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin dolor nunc, ullamcorper a quam eget, posuere vehicula diam. Praesent ante est, pellentesque sit a','Lorem, Ipsum','Chris Zuber','+ChrisZuber','2014-05-04 06:19:16','');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(80) NOT NULL,
  `password` varchar(60) NOT NULL,
  `role` varchar(20) NOT NULL,
  `g_plus` varchar(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`),
  UNIQUE KEY `g_plus` (`g_plus`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
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

-- Dump completed on 2014-08-23 16:26:29
