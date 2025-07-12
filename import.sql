-- MySQL dump 10.13  Distrib 5.6.34, for Linux (x86_64)
--
-- Host: localhost    Database: djnice2d_ds
-- ------------------------------------------------------
-- Server version	5.6.34

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
-- Table structure for table `comingsoon`
--

DROP TABLE IF EXISTS `comingsoon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comingsoon` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comingsoon`
--

LOCK TABLES `comingsoon` WRITE;
/*!40000 ALTER TABLE `comingsoon` DISABLE KEYS */;
/*!40000 ALTER TABLE `comingsoon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `download_history`
--

DROP TABLE IF EXISTS `download_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `download_history` (
  `did` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(10) unsigned NOT NULL,
  `date` varchar(8) NOT NULL,
  `hits` int(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`did`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `download_history`
--

LOCK TABLES `download_history` WRITE;
/*!40000 ALTER TABLE `download_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `download_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `files` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `path` text NOT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `dcount` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `disporder` smallint(5) unsigned NOT NULL,
  `isdir` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `use_icon` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `files`
--

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;
/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `sid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `gid` smallint(5) unsigned NOT NULL,
  `title` varchar(120) NOT NULL,
  `name` varchar(120) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `optionscode` text NOT NULL,
  `disporder` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`sid`, `gid`, `title`, `name`, `value`, `type`, `description`, `optionscode`, `disporder`) VALUES (1,1,'Cookie Prefix','cookieprefix','ss_','text','Prefix to be added in cookies','',1),(2,1,'Cookie Domain','cookiedomain','','text','Cookie domain for which cookies will work','',2),(3,1,'Cookie Path','cookiepath','/','text','Path where the cookies will work','',3),(4,2,'Site URL','url','http://djnice.cf','text','Site url to be used','',1),(5,2,'Site Title','title','DjNice.CF ','text','Site Title will be displayed on title bar,header,footer etc..','',2),(6,2,'Site Logo','logo','http://uctop.in/logo.png','text','Logo image path or url...','',3),(7,2,'Fb Page Link','fbpagename','thesahil2','text','Fb page username without slash(/)..','',4),(8,2,'Show Searchbox','show_searchbox','1','yesno','Show searchbox on index & filelist pages?','',5),(9,2,'Maximum Paging Link','maxmultipagelinks','5','text','Number of page links to show','',6),(11,3,'Updates on Page','updates_per_page','12','select','Total number of update messages to show on updates page?','\r\n10=10\r\n11=11\r\n12=12\r\n13=13\r\n14=14\r\n15=15',2),(10,3,'Updates on Index','updates_on_index','8','select','Total number of update messages to show on index page?','\r\n5=5\r\n6=6\r\n7=7\r\n8=8\r\n9=9\r\n10=10',1),(12,4,'Related files per Page','related_files_per_page','12','select','Select total number of folders to show','\r\n5=5\r\n6=6\r\n7=7\r\n8=8\r\n9=9\r\n10=10\r\n11=11\r\n12=12\r\n13=13',1),(13,4,'Files Per Page','files_per_page','8','select','Number of files to show in filelist page','\r\n5=5\r\n6=6\r\n7=7\r\n8=8\r\n9=9\r\n10=10\r\n11=11\r\n12=12\r\n13=13',3),(14,4,'Default Sort Option','sort','new2old','select','Select default sorting option for files','\r\nnew2old=New to Old\r\na2z=A to Z\r\nz2a=Z to A\r\ndownload=Most Download',3),(15,0,'Admin Password','adminpass','8cb2237d0679ca88db6464eac60da96345513964','ap','The admincp password','',1),(16,4,'Show Total File','show_filecount','0','yesno','Show total number of files after folder name?','',3),(17,5,'Watermark Thumb','watermark_thumb','1','yesno','Watermark generated/uploaded thumbs','',1),(18,5,'Watermark Images','watermark_images','1','yesno','Watermark uploaded images?','',2),(19,5,'Watermark Videos','watermark_videos','1','yesno','Watermark uploaded videos?','',3),(20,5,'Watermark Image','watermark_image','/assets/images/watermark.png','text','Image to be watermarked on video...','',4),(22,6,' Auto Tag','auto_tag','1','yesno','Auto tags mp3 files with default tags','',1),(24,6,'Song Year','mp3_year','2015','text','','',3),(23,6,'Auto Bitrate','auto_bitrate','1','yesno','Auto convert bitrate of MP3 files','',2),(25,6,'Composer','mp3_composer','DjNice.CF','text','','',4),(21,5,'Watermark text','watermark_text','DjNice.CF','text','Text to be watermarked on thumbs/images','',5),(26,6,'Publishers','mp3_publisher','DjNice.CF','text','','',5),(27,6,'Artist','mp3_artist','DjNice.CF','text','','',6),(28,6,'Album Art','mp3_albumart','assets/images/logo.png','text','','',7),(29,6,'Genre','mp3_genre','DjNice.CF','text','','',7),(30,6,'Band','mp3_band','DjNice.CF','text','','',8),(31,6,'Track','mp3_track','DjNice.CF','text','','',9),(32,4,'Related Files','related_files','1','yesno','','',2),(33,6,'Encoded By','mp3_encoded_by','DjNice.CF','text','','',11),(34,6,'Original Artist','mp3_original_artist','DjNice.CF','text','','',12),(35,6,'Comment','mp3_comment','DjNice.CF','text','','',13),(36,6,'User url','mp3_url_user','DjNice.CF','text','','',14);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settingsgroups`
--

DROP TABLE IF EXISTS `settingsgroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settingsgroups` (
  `gid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `disporder` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settingsgroups`
--

LOCK TABLES `settingsgroups` WRITE;
/*!40000 ALTER TABLE `settingsgroups` DISABLE KEYS */;
INSERT INTO `settingsgroups` (`gid`, `title`, `description`, `disporder`) VALUES (1,'Cookie Settings','Set cookie prefix,path or domain...',1),(2,'General Settings','Edit various settings like title,url etc..',2),(3,'Updates Settings','Updates settings like updates per page etc...',3),(4,'Files Settings','Change file per page,sort order etc..',4),(5,'Watermark Settings','Set various options for watermark',5),(6,'Mp3 Settings','Set various options for mp3 files',6);
/*!40000 ALTER TABLE `settingsgroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `updates`
--

DROP TABLE IF EXISTS `updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `updates` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `status` varchar(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `updates`
--

LOCK TABLES `updates` WRITE;
/*!40000 ALTER TABLE `updates` DISABLE KEYS */;
/*!40000 ALTER TABLE `updates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'djnice2d_ds'
--

--
-- Dumping routines for database 'djnice2d_ds'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-12-17 11:29:45
