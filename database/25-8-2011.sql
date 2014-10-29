-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.5.8-log


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema short_url
--

CREATE DATABASE IF NOT EXISTS short_url;
USE short_url;

--
-- Definition of table `shorty`
--

DROP TABLE IF EXISTS `shorty`;
CREATE TABLE `shorty` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `real_url` varchar(200) NOT NULL,
  `short_code` varchar(50) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `full_code` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shorty`
--

/*!40000 ALTER TABLE `shorty` DISABLE KEYS */;
INSERT INTO `shorty` (`id`,`real_url`,`short_code`,`hits`,`full_code`) VALUES 
 (2,'http://localhost/tests/shorturl/','cecc',0,'18aef0c0db1d822f59c81a5973c68562'),
 (4,'http://localhost/tests/shorturl/?cecc','3ff7',0,'b878af8c4ea7c14afa49626620a0964e'),
 (5,'http://www.totallyphp.co.uk/code/page_load_time.htm','d345',0,'55e26ebf8a1ca0c82654a0f58c9e6189'),
 (6,'http://www.phpjabbers.com/measuring-php-page-load-time-php17.html','f976',0,'d1dcc0be53129c75dce15ecaec5a6564'),
 (7,'http://www.casualcode.com/2004/12/17/page-loading-time-in-php/','5005',0,'e33b721c5da8d3fc48ddb4e337dfbd22');
/*!40000 ALTER TABLE `shorty` ENABLE KEYS */;


--
-- Definition of procedure `saveShortUrly`
--

DROP PROCEDURE IF EXISTS `saveShortUrly`;

DELIMITER $$

/*!50003 SET @TEMP_SQL_MODE=@@SQL_MODE, SQL_MODE='' */ $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `saveShortUrly`(
in p_real_url varchar(200),
in p_short_code varchar(50),
in p_full_code varchar(100)
)
BEGIN
       insert into shorty(real_url,short_code,full_code)
       values(p_real_url,p_short_code,p_full_code);
END $$
/*!50003 SET SESSION SQL_MODE=@TEMP_SQL_MODE */  $$

DELIMITER ;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
