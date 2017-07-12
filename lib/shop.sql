-- MySQL dump 10.16  Distrib 10.1.21-MariaDB, for Win32 (AMD64)
--
-- Host: localhost    Database: localhost
-- ------------------------------------------------------
-- Server version	10.1.21-MariaDB

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
-- Table structure for table `shop_category`
--

DROP TABLE IF EXISTS `shop_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) unsigned NOT NULL COMMENT 'Родительская категория',
  `is_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Флаг активности категории',
  `name` varchar(255) DEFAULT NULL COMMENT 'Название категории',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192 COMMENT='Категории товаров';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_category`
--

LOCK TABLES `shop_category` WRITE;
/*!40000 ALTER TABLE `shop_category` DISABLE KEYS */;
INSERT INTO `shop_category` VALUES (1,0,1,'root'),(2,1,1,'Бытовая техника'),(3,2,1,'Встраиваемая техника'),(4,2,1,'Крупная бытовая техника'),(5,2,1,'Мелкая бытовая техника'),(6,1,1,'Ноутбуки, планшеты, смартфоны'),(7,6,1,'Ноутбуки'),(8,6,1,'Планшеты'),(9,6,1,'Смартфоны'),(10,1,1,'Товары для дома'),(11,10,1,'Кухня'),(12,10,1,'Рабочий кабинет'),(13,1,0,'Новая категория');
/*!40000 ALTER TABLE `shop_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_product`
--

DROP TABLE IF EXISTS `shop_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_enabled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Флаг активности',
  `name` varchar(255) DEFAULT NULL COMMENT 'Название товара',
  `announce` varchar(255) DEFAULT NULL COMMENT 'Анонс',
  `description` text COMMENT 'Описание',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=16384 COMMENT='Товары';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_product`
--

LOCK TABLES `shop_product` WRITE;
/*!40000 ALTER TABLE `shop_product` DISABLE KEYS */;
INSERT INTO `shop_product` VALUES (1,1,'Холодильник',NULL,NULL),(2,1,'Газовая плита',NULL,NULL),(3,1,'Ноутбук Acer',NULL,NULL),(4,1,'Микроволновая печь',NULL,NULL),(5,1,'Стул',NULL,NULL),(6,1,'djlksjfl',NULL,NULL),(7,1,'djlksjfl',NULL,NULL),(8,1,'djlksjfl',NULL,NULL),(9,1,'Привет',NULL,NULL),(10,1,'мой товар',NULL,NULL);
/*!40000 ALTER TABLE `shop_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_product_category`
--

DROP TABLE IF EXISTS `shop_product_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop_product_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL COMMENT 'ID категории',
  `product_id` int(11) unsigned NOT NULL COMMENT 'ID товара',
  PRIMARY KEY (`id`),
  KEY `shop_product_category_idx` (`category_id`),
  KEY `shop_product_product_idx` (`product_id`),
  CONSTRAINT `FK_shop_product_category_to_category` FOREIGN KEY (`category_id`) REFERENCES `shop_category` (`id`) ON DELETE NO ACTION,
  CONSTRAINT `FK_shop_product_category_to_product` FOREIGN KEY (`product_id`) REFERENCES `shop_product` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=16384 COMMENT='Таблица соответствия категорий и товаров';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_product_category`
--

LOCK TABLES `shop_product_category` WRITE;
/*!40000 ALTER TABLE `shop_product_category` DISABLE KEYS */;
INSERT INTO `shop_product_category` VALUES (1,4,1),(2,11,1),(3,4,2),(4,7,3),(5,12,3),(6,5,4),(7,11,4),(8,12,5);
/*!40000 ALTER TABLE `shop_product_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'shop'
--
/*!50003 DROP PROCEDURE IF EXISTS `GetCategories` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCategories`(IN `category` INT)
    NO SQL
select tabC.id, tabC.name, tabC.parent
from `shop_category` tabC
where id=category
    
    union all 
 select  
		id,
		name,
		parent
 from    (    
	select tabC.id, tabC.name, tabC.parent
	from `shop_category` tabC
	where tabC.is_enabled
	order by parent, id) products_sorted,
	(select @pv := category) initialisation
where   find_in_set(parent, @pv) > 0 and @pv := concat(@pv, ',', id) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `GetCategoryProducts` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCategoryProducts`(IN `category` INT)
    NO SQL
BEGIN
CREATE TEMPORARY TABLE tmp_categories(id int);

INSERT INTO tmp_categories(id)
 select id
    from `shop_category` tabC
    where tabC.id=category
    
    union all 
	select  
		id
	from    (
		select distinct tabC.id, tabC.parent
		from `shop_category` tabC
		left join `shop_product_category` tabCP on tabC.id=tabCP.category_id
		where tabC.is_enabled
	    order by parent, id) products_sorted,
		(select @pv := category) initialisation
		where   find_in_set(parent, @pv) > 0 and @pv := concat(@pv, ',', id) ;
        

	select DISTINCT tabP.*, tabCP.category_id as category
    from shop_product as tabP
    inner join shop_product_category as tabCP on tabP.id=tabCP.product_id
    where tabCP.category_id in (select id from tmp_categories)
    order by id
   ;

DROP TEMPORARY TABLE tmp_categories;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-11 20:59:38
