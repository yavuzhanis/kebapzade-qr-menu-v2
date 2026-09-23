-- MySQL dump 10.13  Distrib 9.6.0, for macos26.2 (arm64)
--
-- Host: 127.0.0.1    Database: kebapzade_menu
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Yönetici','admin@kebapzade.com','$2y$12$fMyDN5Jx.KTGFeegnY4pAOksSp8TQF9OJITukwtlpHDRdcCzu4p1u','2026-09-20 11:42:52','2026-09-20 11:42:52');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name_tr` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_tr` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_cat_sort` (`is_active`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Çorba & Salatalar','Soup & Salads','corba-salatalar',NULL,NULL,'/assets/images/soup_salad.jpg',10,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(2,'Mezeler','Cold Appetizers / Mezze','mezeler',NULL,NULL,'/assets/images/mezze.jpg',20,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(3,'Ara Sıcaklar','Hot Appetizers','ara-sicaklar',NULL,NULL,'/assets/images/ara_sicak.jpg',30,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(4,'Tavuk Yemekleri','Chicken Foods','tavuk-yemekleri',NULL,NULL,'/assets/images/tavuk_yemekleri.jpg',40,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(5,'Yöresel Yemekler','Local Foods','yoresel-yemekler',NULL,NULL,'/assets/images/testi_kebabi.jpg',50,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(6,'Izgaralar','Grilled Kebabs','izgaralar',NULL,NULL,'/assets/images/kuzu_pirzola.jpg',60,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(7,'Kebaplar','Special Kebabs','kebaplar',NULL,NULL,'/assets/images/kuzu_pirzola.jpg',70,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(8,'Karışık Izgaralar','Mixed Grills','karisik-izgaralar',NULL,NULL,'/assets/images/karisik_izgara.jpg',80,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(9,'Özel Yemekler','Special Foods','ozel-yemekler',NULL,NULL,'/assets/images/testi_kebabi.jpg',90,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(10,'Tatlılar','Desserts','tatlilar',NULL,NULL,'/assets/images/dessert.jpg',100,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(11,'Yöresel İçecekler','Traditional Drinks','yoresel-icecekler',NULL,NULL,'/assets/images/drinks_cold.jpg',110,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(12,'Meşrubatlar','Soft Drinks','mesrubatlar',NULL,NULL,'/assets/images/drinks_cold.jpg',120,1,'2026-09-20 11:42:52','2026-09-22 12:53:52'),(13,'Sıcak İçecekler','Hot Drinks','sicak-icecekler',NULL,NULL,'/assets/images/drinks_hot.jpg',130,1,'2026-09-20 11:42:52','2026-09-22 12:53:52');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_variants`
--

DROP TABLE IF EXISTS `item_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_variants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int unsigned NOT NULL,
  `label_tr` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_en` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_variant_item` (`item_id`,`sort_order`),
  CONSTRAINT `fk_variant_item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_variants`
--

LOCK TABLES `item_variants` WRITE;
/*!40000 ALTER TABLE `item_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name_tr` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_tr` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) DEFAULT NULL,
  `price_note_tr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_note_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_vegetarian` tinyint(1) NOT NULL DEFAULT '0',
  `is_spicy` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_item_cat` (`category_id`,`is_active`,`sort_order`),
  CONSTRAINT `fk_items_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,1,'Mercimek Çorbası','Red Lentil Soup','Kırmızı mercimek, tereyağı, taze sebze ve baharatlarla.','Red lentils, butter, vegetables and spices.',140.00,NULL,NULL,'/assets/images/soup_salad.jpg',1,1,1,0,10,'2026-09-20 11:42:52','2026-09-21 07:06:44'),(2,1,'Çoban Salatası','Shepherd’s Salad','Domates, salatalık, biber, maydanoz, kuru soğan, zeytinyağı ve nar ekşili sos.','Tomato, cucumber, pepper, parsley, onion, olive oil and pomegranate sauce.',140.00,NULL,NULL,'/assets/images/soup_salad.jpg',1,0,1,0,20,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(3,1,'Nurdağ Salatası','Walnut Salad','Ceviz, domates, salatalık, biber, maydanoz, kuru soğan, zeytinyağı ve nar ekşili sos.','Walnut, tomato, cucumber, pepper, parsley, onion, olive oil and pomegranate sauce.',140.00,NULL,NULL,'/assets/images/soup_salad.jpg',1,0,1,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(4,1,'Mevsim Salata','Seasonal Salad','Mevsim yeşillikleri, domates, salatalık, zeytinyağı ve nar ekşili sos.','Seasonal greens, tomato, cucumber, olive oil and pomegranate sauce.',140.00,NULL,NULL,'/assets/images/soup_salad.jpg',1,0,1,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(5,2,'Közlenmiş Patlıcan Salatası','Grilled Aubergine Salad','Köz patlıcan, köz domates, köz biber, sarımsak ve zeytinyağı.','Roasted aubergine, tomato, pepper, garlic and olive oil.',180.00,NULL,NULL,'/assets/images/mezze.jpg',1,0,1,0,10,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(6,2,'Hatay Humus','Hummus','Nohut ezmesi, kimyon, sarımsak, limon, tahin ve zeytinyağı.','Chickpea purée, cumin, garlic, lemon, tahini and olive oil.',190.00,NULL,NULL,'/assets/images/mezze.jpg',1,1,1,0,20,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(7,2,'Antep Ezme','Spicy Tomato Mezze','Taze sebzeler, biber salçası, baharat, zeytinyağı ve nar ekşili sos.','Fresh vegetables, pepper paste, spices, olive oil and pomegranate sauce.',180.00,NULL,NULL,'/assets/images/mezze.jpg',1,0,1,1,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(8,2,'Zeytinyağlı Enginar','Artichoke with Olive Oil','Zeytinyağlı enginar ve sebzeler.','Artichoke and vegetables with olive oil.',180.00,NULL,NULL,'/assets/images/mezze.jpg',1,0,1,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(9,2,'Haydari','Haydari','Süzme yoğurt, kuru nane, dereotu, sarımsak, baharat ve zeytinyağı.','Thick yoghurt, mint, dill, garlic, spices and olive oil.',180.00,NULL,NULL,'/assets/images/mezze.jpg',1,0,1,0,50,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(10,2,'Yöresel Yoğurt','Plain Yoghurt','Doğal sütten yöresel yoğurt.','Regional plain yoghurt made from natural milk.',180.00,NULL,NULL,'/assets/images/mezze.jpg',1,0,1,0,60,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(11,2,'Közde Sarımsak','Roasted Garlic','Taş fırında közlenmiş sarımsak, nar ekşili baharatlı özel sos ile.','Stone-oven roasted garlic with pomegranate sauce.',180.00,NULL,NULL,'/assets/images/mezze.jpg',1,0,1,0,70,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(12,2,'Karışık Meze Tabağı','Mixed Mezze Platter','Patlıcan, humus, ezme ve haydari.','Roasted aubergine, hummus, spicy mezze and haydari.',340.00,NULL,NULL,'/assets/images/mezze.jpg',1,1,1,0,80,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(13,3,'Kaşarlı Mantar Kiremit','Mushroom with Cheese','Taze mantar, tereyağı ve kaşar peyniri taş fırında.','Fresh mushroom, butter and cheese cooked in a stone oven.',220.00,NULL,NULL,'/assets/images/ara_sicak.jpg',1,0,1,0,10,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(14,3,'Humus Tereyağlı Cevizli','Baked Walnut Hummus','Hatay usulü humus, ceviz ve tereyağı ile taş fırında.','Hummus with walnut and butter, baked in a stone oven.',220.00,NULL,NULL,'/assets/images/ara_sicak.jpg',1,1,1,0,20,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(15,3,'Falafel','Falafel','Nohut, taze sebzeler ve baharatlarla.','Chickpea, fresh herbs and spices.',220.00,NULL,NULL,'/assets/images/ara_sicak.jpg',1,0,1,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(16,3,'İçli Köfte (1 Adet)','Stuffed Meatball (1 Piece)','Kıyma, ceviz, üzüm, bulgur ve baharatlarla.','Bulgur shell stuffed with minced beef, walnut and spices.',220.00,NULL,NULL,'/assets/images/ara_sicak.jpg',1,1,0,0,40,'2026-09-20 11:42:52','2026-09-21 07:06:44'),(17,3,'Patates Kızartması','French Fries','Patates kızartması.','French fries.',220.00,NULL,NULL,'/assets/images/ara_sicak.jpg',1,0,1,0,50,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(18,4,'Tavuk Köri','Chicken Curry','Köri sosu, taze süt, mantar, tavuk ve sebzeler; taş fırında.','Chicken, curry sauce, milk, mushroom and vegetables; stone-oven cooked.',380.00,NULL,NULL,'/assets/images/tavuk_yemekleri.jpg',1,1,0,0,10,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(19,4,'Tavuk Güveç','Chicken Casserole','Tavuk, közlenmiş patlıcan ve sebzeler taş fırında.','Chicken, smoked aubergine and vegetables cooked in a stone oven.',380.00,NULL,NULL,'/assets/images/tavuk_yemekleri.jpg',1,0,0,0,20,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(20,4,'Kiremitte Kaşarlı Tavuk','Baked Chicken with Cheese','Tavuk, mantar, domates, sarımsak ve kaşar peyniri taş fırında.','Chicken, mushroom, tomato, garlic and cheese cooked in a stone oven.',380.00,NULL,NULL,'/assets/images/tavuk_yemekleri.jpg',1,0,0,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(21,4,'Tereyağlı Pirinç Pilavı','White Rice with Butter','Tereyağlı pirinç pilavı.','White rice with butter.',380.00,NULL,NULL,'/assets/images/tavuk_yemekleri.jpg',1,0,1,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(22,5,'Testi Kebabı','Pottery Kebab – Beef','Dana eti, domates, biber, soğan, sarımsak ve baharatlar; tereyağlı pirinç pilavı ile.','Beef, tomato, pepper, onion, garlic and spices; served with rice.',680.00,NULL,NULL,'/assets/images/testi_kebabi.jpg',1,1,0,0,10,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(23,5,'Tavuk Testi','Pottery Kebab – Chicken','Tavuk, domates, biber, soğan, sarımsak ve baharatlar; pirinç pilavı ile.','Chicken, tomato, pepper, onion, garlic and spices; served with rice.',540.00,NULL,NULL,'/assets/images/testi_kebabi.jpg',1,0,0,0,20,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(24,5,'Kurufasulye','White Beans Stew','Geleneksel kuru fasulye; pirinç pilavı ile.','Traditional white beans stew served with rice.',520.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,0,0,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(25,5,'Sebzeli Testi','Vegetarian Pottery Kebab','Sebzeler testi içinde taş fırında pişirilir.','Vegetables cooked in pottery in a stone oven.',480.00,NULL,NULL,'/assets/images/testi_kebabi.jpg',1,0,1,0,40,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(26,5,'Kiremitte Köfte Izgara','Meatball Casserole','Izgara köfte ve özel domates sosu.','Grilled meatballs with special tomato sauce.',520.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,0,0,0,50,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(27,5,'Kebapzade Güveç','Kebapzade Beef Casserole','Köz patlıcan, tereyağı, domates, sarımsak ve dana eti taş fırında.','Beef, roasted aubergine, butter, tomato and garlic, stone-oven cooked.',520.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,1,0,0,60,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(28,5,'Dana Kavurma','Beef Kavurma','Yavaş pişmiş dana kavurma, tereyağlı pirinç pilavı ile.','Slow-cooked beef kavurma served with buttered rice.',520.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,0,0,0,70,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(29,6,'Köfte Izgara','Grilled Meatball','Sumaklı soğan piyazı, domates, yeşil biber ızgara ve lavaş.','Grilled meatball with sumac onion, tomato, green pepper and lavash.',460.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,1,0,0,10,'2026-09-20 11:42:52','2026-09-21 07:06:44'),(30,6,'Tavuk Şiş','Chicken Shish Kebab','Sumaklı soğan piyazı, domates, yeşil biber ızgara ve lavaş.','Chicken shish with sumac onion, tomato, green pepper and lavash.',460.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,1,0,0,20,'2026-09-20 11:42:52','2026-09-21 07:06:44'),(31,6,'Tavuk Kanat','Chicken Wings','Sumaklı soğan piyazı, domates, yeşil biber ızgara ve lavaş.','Chicken wings with sumac onion, tomato, green pepper and lavash.',460.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,0,0,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(32,7,'Adana Kebap','Adana Kebab','Kömür ateşinde; sumaklı soğan, domates, biber ve lavaş ile.','Charcoal-grilled Adana kebab with sumac onion, tomato, pepper and lavash.',440.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,1,0,1,10,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(33,7,'Antep Fıstıklı Kebap','Pistachio Kebab','Kıyma ve Antep fıstığı; sumaklı soğan, domates, biber ve lavaş ile.','Minced meat and pistachio kebab with sumac onion, tomato, pepper and lavash.',490.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,1,0,0,20,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(34,7,'Kuzu Pirzola','Lamb Chops','Sumaklı soğan piyazı, domates, yeşil biber ızgara ve lavaş.','Lamb chops with sumac onion, tomato, green pepper and lavash.',460.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,0,0,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(35,8,'Karışık Izgara','Mixed Grill','Dana şiş, Adana kebap, tavuk kanat ve tavuk şiş.','Beef skewers, Adana kebab, chicken wings and chicken skewers.',890.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,1,0,0,10,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(36,8,'Karışık Kebapzade Izgara','Mixed Kebapzade Grill','Kuzu pirzola, dana şiş, Adana kebap, fıstıklı kebap ve içli köfte.','Lamb chops, beef shish, Adana kebab, pistachio kebab and stuffed meatball.',1150.00,NULL,NULL,'/assets/images/karisik_izgara.jpg',1,1,0,0,20,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(37,9,'Kuzu Pirzola Taş Fırında','Lamb Chops on Pottery','Taş fırında pişirilen kuzu pirzola; karışık meze ile.','Stone-oven lamb chops served with mixed mezze.',580.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,1,0,0,10,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(38,9,'Tuzda Tavuk Fırında','Salt-Crust Chicken','İç pilavla doldurulan tavuk kaya tuzuyla kaplanıp taş fırında pişirilir. Ön sipariş gerektirir.','Chicken stuffed with rice, covered in salt and roasted. Advance order required.',580.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,1,0,0,20,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(39,9,'Taş Fırında Tavuk Pirzola','Chicken Chops on Pottery','Taş fırında pişirilir ve karışık meze ile.','Stone-oven chicken chops served with mixed mezze.',580.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,0,0,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(40,9,'Taş Fırında Somon','Oven-Baked Salmon','Taş fırında pişirilir ve karışık meze ile.','Stone-oven salmon served with mixed mezze.',580.00,NULL,NULL,'/assets/images/kuzu_pirzola.jpg',1,0,0,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(41,10,'Hatay Kabak Tatlısı','Crispy Pumpkin Dessert','Kireçte kıtır kabak tatlısı, ceviz ve tahin ile.','Crispy pumpkin dessert with walnut and tahini.',240.00,NULL,NULL,'/assets/images/dessert.jpg',1,1,1,0,10,'2026-09-20 11:42:52','2026-09-20 11:52:58'),(42,10,'Afyon Kabak Tatlısı','Creamy Pumpkin Dessert','Kabak tatlısı, tahin ve ceviz ile.','Pumpkin dessert with tahini and walnut.',210.00,NULL,NULL,'/assets/images/dessert.jpg',1,0,1,0,20,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(43,10,'Dondurmalı Afyon Kabak Tatlısı','Creamy Pumpkin Dessert & Ice Cream','Afyon kabak tatlısı, dondurma, tahin ve ceviz ile.','Pumpkin dessert with ice cream, tahini and walnut.',210.00,NULL,NULL,'/assets/images/dessert.jpg',1,0,1,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(44,10,'Havuç Dilimi Baklava','Pistachio Baklava','Tereyağlı, Antep fıstıklı havuç dilimi baklava.','Butter and pistachio baklava.',210.00,NULL,NULL,'/assets/images/dessert.jpg',1,1,1,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(45,10,'Dondurmalı Havuç Dilimi Baklava','Pistachio Baklava with Ice Cream','Havuç dilimi baklava ve dondurma.','Pistachio baklava with ice cream.',210.00,NULL,NULL,'/assets/images/dessert.jpg',1,0,1,0,50,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(46,10,'Fıstıklı Maraş Dondurması','Pistachio Ice Cream','Maraş usulü fıstıklı dondurma.','Maraş-style pistachio ice cream.',210.00,NULL,NULL,'/assets/images/dessert.jpg',1,0,1,0,60,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(47,11,'Şıra (Kuru Üzüm Suyu)','Grape Juice','Geleneksel kuru üzüm içeceği.','Traditional grape drink.',95.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,1,1,0,10,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(48,11,'Yöresel Ayran','Ayran Drink','Yöresel ayran.','Traditional yoghurt drink.',95.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,1,1,0,20,'2026-09-20 11:42:52','2026-09-21 07:06:44'),(49,11,'Multimix Meyve Suyu %100','Multimix Fruit Juice 100%','%100 meyve suyu.','100% fruit juice.',95.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(50,11,'Antioksidan Meyve Suyu %100','Antioxidant Juice 100%','%100 meyve suyu.','100% fruit juice.',95.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(51,12,'Küçük Su (0,33 L)','Water Small (0.33 L)','Şişe su.','Bottled water.',75.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,10,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(52,12,'Büyük Su (0,75 L)','Water Large (0.75 L)','Şişe su.','Bottled water.',75.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,20,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(53,12,'Maden Suyu (20 cl)','Sparkling Water (20 cl)','Maden suyu.','Sparkling water.',75.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(54,12,'Kola / Portakal / Gazoz / Ice Tea','Cola / Orange / Soda / Ice Tea','Soğuk meşrubat seçenekleri.','Assorted soft drinks.',75.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(55,12,'Meyve Suyu','Fruit Juice','Meyve suyu.','Fruit juice.',75.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,50,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(56,12,'Şalgam Suyu','Turnip Juice','Şalgam suyu.','Turnip juice.',75.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,1,60,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(57,12,'Kapalı Ayran','Ayran in a Cup','Kapalı ayran.','Packaged ayran.',75.00,NULL,NULL,'/assets/images/drinks_cold.jpg',1,0,1,0,70,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(58,13,'Çay','Tea','Siyah çay; elma, limon, portakal çayı veya sıcak su seçenekleri.','Black tea; apple, lemon, orange tea or hot water options.',50.00,NULL,NULL,'/assets/images/drinks_hot.jpg',1,0,1,0,10,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(59,13,'Yeşil Çay','Green Tea','Yeşil çay.','Green tea.',50.00,NULL,NULL,'/assets/images/drinks_hot.jpg',1,0,1,0,20,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(60,13,'Türk Kahvesi','Turkish Coffee','Geleneksel Türk kahvesi.','Traditional Turkish coffee.',50.00,NULL,NULL,'/assets/images/drinks_hot.jpg',1,1,1,0,30,'2026-09-20 11:42:52','2026-09-21 06:19:45'),(61,13,'Neskafe Klasik','Nescafe Classic','Klasik hazır kahve.','Classic instant coffee.',50.00,NULL,NULL,'/assets/images/drinks_hot.jpg',1,0,1,0,40,'2026-09-20 11:42:52','2026-09-21 06:19:45');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `item_id` int unsigned DEFAULT NULL,
  `name_tr` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `line_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_order_items` (`order_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,52,'Adana Kebap',NULL,420.00,2,840.00,NULL),(2,1,26,'Hatay Humus',NULL,180.00,1,180.00,NULL),(3,2,1,'Mercimek Çorbası',NULL,140.00,1,140.00,NULL),(4,2,2,'Çoban Salatası',NULL,140.00,1,140.00,NULL),(5,2,6,'Hatay Humus',NULL,190.00,1,190.00,NULL),(6,3,1,'Mercimek Çorbası',NULL,140.00,1,140.00,NULL),(7,3,6,'Hatay Humus',NULL,190.00,1,190.00,NULL),(8,3,15,'Falafel',NULL,220.00,1,220.00,NULL),(9,3,17,'Patates Kızartması',NULL,220.00,1,220.00,NULL),(10,3,18,'Tavuk Köri',NULL,380.00,1,380.00,NULL),(11,3,22,'Testi Kebabı',NULL,680.00,1,680.00,NULL),(12,4,1,'Mercimek Çorbası',NULL,140.00,1,140.00,NULL),(13,4,6,'Hatay Humus',NULL,190.00,1,190.00,NULL),(14,4,15,'Falafel',NULL,220.00,1,220.00,NULL),(15,4,17,'Patates Kızartması',NULL,220.00,1,220.00,NULL),(16,4,18,'Tavuk Köri',NULL,380.00,1,380.00,NULL),(17,4,22,'Testi Kebabı',NULL,680.00,1,680.00,NULL),(18,4,38,'Tuzda Tavuk Fırında',NULL,580.00,1,580.00,NULL),(19,4,39,'Taş Fırında Tavuk Pirzola',NULL,580.00,1,580.00,NULL),(20,4,40,'Taş Fırında Somon',NULL,580.00,1,580.00,NULL),(21,5,1,'Mercimek Çorbası',NULL,140.00,2,280.00,NULL),(22,5,6,'Hatay Humus',NULL,190.00,1,190.00,NULL),(23,5,15,'Falafel',NULL,220.00,1,220.00,NULL),(24,5,17,'Patates Kızartması',NULL,220.00,1,220.00,NULL),(25,5,18,'Tavuk Köri',NULL,380.00,1,380.00,NULL),(26,5,22,'Testi Kebabı',NULL,680.00,1,680.00,NULL),(27,5,38,'Tuzda Tavuk Fırında',NULL,580.00,1,580.00,NULL),(28,5,39,'Taş Fırında Tavuk Pirzola',NULL,580.00,1,580.00,NULL),(29,5,40,'Taş Fırında Somon',NULL,580.00,1,580.00,NULL),(30,5,11,'Közde Sarımsak',NULL,180.00,1,180.00,NULL),(31,6,2,'Çoban Salatası',NULL,140.00,1,140.00,NULL),(32,6,3,'Nurdağ Salatası',NULL,140.00,1,140.00,NULL);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','preparing','served','paid','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `customer_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_no` (`order_no`),
  KEY `idx_order_status` (`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'#KZ-215F03','Salon 1',1020.00,'served','Lütfen lavaş sıcak gelsin','2026-09-21 08:11:33','2026-09-21 08:40:01'),(2,'#KZ-21CF1B','Masa Servisi',470.00,'paid','Deneme','2026-09-21 08:43:17','2026-09-21 09:00:36'),(3,'#KZ-21859E','Table Service',1830.00,'served',NULL,'2026-09-21 08:45:38','2026-09-21 08:50:29'),(4,'#KZ-21E86D','Masa Servisi',3570.00,'served',NULL,'2026-09-21 08:49:55','2026-09-21 08:50:27'),(5,'#KZ-213339','Masa Servisi',3890.00,'served',NULL,'2026-09-21 08:58:03','2026-09-21 09:33:20'),(6,'#KZ-21C3F0','Masa Bahe1',280.00,'served',NULL,'2026-09-21 08:58:50','2026-09-21 09:33:19');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `guest_name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `guest_count` tinyint unsigned NOT NULL DEFAULT '2',
  `note` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','cancelled','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `source` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'website',
  `language` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reservation_date` (`reservation_date`,`reservation_time`),
  KEY `idx_reservation_status` (`status`,`reservation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservations`
--

LOCK TABLES `reservations` WRITE;
/*!40000 ALTER TABLE `reservations` DISABLE KEYS */;
/*!40000 ALTER TABLE `reservations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('address','Bilal Eroğlu Cd. No:3, Göreme / Nevşehir'),('announcement_en','Stone oven, charcoal fire and regional recipes.'),('announcement_tr','Taş fırın, kömür ateşi ve yöresel reçeteler.'),('email','info@kebapzade.com'),('hero_image','/assets/images/hero.jpg'),('hero_title_en','Traditional Flavours at the Table of Cappadocia'),('hero_title_tr','Kapadokya’nın Sofrasında Geleneksel Lezzetler'),('hours_en','Every day 10:00 – 23:00'),('hours_tr','Her gün 10.00 – 23.00'),('instagram',''),('logo_image','/assets/images/logo.svg'),('maps_url','https://www.google.com/maps/search/?api=1&query=Kebapzade+Goreme'),('phone','+90 384 271 30 12'),('restaurant_name','Kapadokya Kebapzade Restaurant'),('tagline_en','Great taste and quality are never a coincidence.'),('tagline_tr','Lezzetli ve kaliteli yemek tesadüf değildir.'),('whatsapp','903842713012'),('wifi_name','Kebapzade_Guest'),('wifi_pass','kebapzade2026');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `table_requests`
--

DROP TABLE IF EXISTS `table_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `table_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `table_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_type` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_req_status` (`status`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `table_requests`
--

LOCK TABLES `table_requests` WRITE;
/*!40000 ALTER TABLE `table_requests` DISABLE KEYS */;
INSERT INTO `table_requests` VALUES (1,'Masa 4','Garson Çağrısı','completed','Masa servisi istendi','2026-09-21 07:16:48','2026-09-21 07:40:16'),(2,'Masa 7','Hesap (Kredi Kartı)','completed','POS cihazı istendi','2026-09-21 07:18:48','2026-09-21 07:40:15'),(3,'Masa 10','Garson Çağrısı','completed','Özel istek','2026-09-21 07:21:59','2026-09-21 07:40:15'),(4,'Masa Servisi','Garson Çağrısı','completed',NULL,'2026-09-21 07:40:08','2026-09-21 07:40:14'),(5,'Masa Servisi','Hesap (Nakit)','completed',NULL,'2026-09-21 07:40:58','2026-09-21 07:41:12'),(6,'Masa Servisi','Garson Çağrısı','completed',NULL,'2026-09-21 07:43:13','2026-09-21 08:39:40'),(7,'Masa 8','Garson Çağrısı','completed','Menü hakkında soru sormak istiyoruz','2026-09-21 07:52:17','2026-09-21 07:53:54'),(8,'Masa Servisi','Hesap (Kredi Kartı)','completed',NULL,'2026-09-21 07:57:52','2026-09-21 08:03:25'),(9,'Salon 1','Masa Siparişi','completed','Sipariş #KZ-215F03: 2x Adana Kebap, 1x Hatay Humus (1.020 ₺)','2026-09-21 08:11:33','2026-09-21 08:39:40'),(11,'Masa Servisi','Masa Siparişi','completed','Sipariş #KZ-21CF1B: 1x Mercimek Çorbası, 1x Çoban Salatası, 1x Hatay Humus (470 ₺)','2026-09-21 08:43:17','2026-09-21 08:49:29'),(12,'Masa Servisi','Garson Çağrısı','completed',NULL,'2026-09-21 08:43:42','2026-09-21 08:49:28'),(13,'Masa Servisi','Hesap (Kredi Kartı)','completed',NULL,'2026-09-21 08:43:52','2026-09-21 08:49:28'),(14,'Table Service','Masa Siparişi','completed','Sipariş #KZ-21859E: 1x Mercimek Çorbası, 1x Hatay Humus, 1x Falafel, 1x Patates Kızartması, 1x Tavuk Köri, 1x Testi Kebabı (1.830 ₺)','2026-09-21 08:45:38','2026-09-21 08:49:27'),(15,'Masa Servisi','Garson Çağrısı','completed',NULL,'2026-09-21 08:49:37','2026-09-21 08:50:32'),(16,'Masa Servisi','Masa Siparişi','completed','Sipariş #KZ-21E86D: 1x Mercimek Çorbası, 1x Hatay Humus, 1x Falafel, 1x Patates Kızartması, 1x Tavuk Köri, 1x Testi Kebabı, 1x Tuzda Tavuk Fırında, 1x Taş Fırında Tavuk Pirzola, 1x Taş Fırında Somon (3.570 ₺)','2026-09-21 08:49:55','2026-09-21 08:50:31'),(17,'Masa Servisi','Masa Siparişi','completed','Sipariş #KZ-213339: 2x Mercimek Çorbası, 1x Hatay Humus, 1x Falafel, 1x Patates Kızartması, 1x Tavuk Köri, 1x Testi Kebabı, 1x Tuzda Tavuk Fırında, 1x Taş Fırında Tavuk Pirzola, 1x Taş Fırında Somon, 1x Közde Sarımsak (3.890 ₺)','2026-09-21 08:58:03','2026-09-21 09:01:28'),(18,'Masa Bahe1','Masa Siparişi','completed','Sipariş #KZ-21C3F0: 1x Çoban Salatası, 1x Nurdağ Salatası (280 ₺)','2026-09-21 08:58:50','2026-09-21 09:00:12'),(19,'Table Service','Garson Çağrısı','completed',NULL,'2026-09-21 09:01:13','2026-09-21 09:01:27');
/*!40000 ALTER TABLE `table_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `table_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'İç Salon',
  `capacity` int unsigned NOT NULL DEFAULT '4',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_no` (`table_no`),
  KEY `idx_table_sort` (`section`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tables`
--

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
INSERT INTO `tables` VALUES (1,'Salon 1','İç Salon',4,10,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(2,'Salon 2','İç Salon',4,20,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(3,'Salon 3','İç Salon',4,30,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(4,'Salon 4','İç Salon',4,40,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(5,'Salon 5','İç Salon',6,50,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(6,'Salon 6','İç Salon',6,60,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(7,'Salon 7','İç Salon',6,70,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(8,'Salon 8','İç Salon',6,80,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(9,'Bahçe 1','Bahçe',4,110,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(10,'Bahçe 2','Bahçe',4,120,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(11,'Bahçe 3','Bahçe',4,130,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(12,'Bahçe 4','Bahçe',4,140,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(13,'Bahçe 5','Bahçe',4,150,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(14,'Bahçe 6','Bahçe',4,160,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(15,'Teras 1','Teras Manzara',4,210,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(16,'Teras 2','Teras Manzara',4,220,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(17,'Teras 3','Teras Manzara',4,230,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(18,'Teras 4','Teras Manzara',4,240,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(19,'Kapadokya VIP','Özel Oda',10,300,1,'2026-09-21 08:07:25','2026-09-21 08:07:25'),(20,'test','İç Salon',4,11,1,'2026-09-21 08:56:12','2026-09-21 08:56:12');
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-22 18:25:15
