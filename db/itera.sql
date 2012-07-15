/*
SQLyog Enterprise - MySQL GUI v8.02 RC
MySQL - 5.5.15-log : Database - itera
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`itera` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `itera`;

/*Table structure for table `area` */

DROP TABLE IF EXISTS `area`;

CREATE TABLE `area` (
  `IdArea` int(11) NOT NULL AUTO_INCREMENT,
  `NombreArea` char(200) DEFAULT NULL,
  PRIMARY KEY (`IdArea`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `area` */

LOCK TABLES `area` WRITE;

insert  into `area`(`IdArea`,`NombreArea`) values (1,'area1'),(2,'area2');

UNLOCK TABLES;

/*Table structure for table `contacto` */

DROP TABLE IF EXISTS `contacto`;

CREATE TABLE `contacto` (
  `IdContacto` int(11) NOT NULL AUTO_INCREMENT,
  `NombresContacto` char(200) DEFAULT NULL,
  `TelefonoContacto` char(20) DEFAULT NULL,
  `Email` char(200) DEFAULT NULL,
  `FechaRegistro` datetime DEFAULT NULL,
  `Ip` char(20) DEFAULT NULL,
  PRIMARY KEY (`IdContacto`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

/*Data for the table `contacto` */

LOCK TABLES `contacto` WRITE;

insert  into `contacto`(`IdContacto`,`NombresContacto`,`TelefonoContacto`,`Email`,`FechaRegistro`,`Ip`) values (1,'asdsad','asdasd','na.zar.tj@gmail.com','2012-07-12 14:53:49','127.0.0.1'),(2,'asdasd','asda','na.zar.tj@gmail.com','2012-07-12 14:17:50','127.0.0.1'),(3,'asdasd','asdasd asdasd','na.zar.tj@gmail.com','2012-07-12 14:24:52','127.0.0.1'),(4,'asdsd','asdasd','na.zar.tj@gmail.com','2012-07-12 14:29:54','127.0.0.1'),(5,'na','asdsd','na.zar.tj@gmail.com','2012-07-12 14:38:54','127.0.0.1'),(6,'adsda','asdasd','na.zar.tj@gmail.com','2012-07-12 14:10:55','127.0.0.1'),(7,'asdasd','asdasd','na.zar.tj@gmail.com','2012-07-12 14:58:59','127.0.0.1'),(8,'naasdad','asdasd','na.zar.tj@gmail.com','2012-07-12 15:12:00','127.0.0.1'),(9,'asdasd','asdasd','na.zar.tj@gmail.com','2012-07-12 15:29:00','127.0.0.1'),(10,'asdasd','asdasd','na.zar.tj@gmail.com','2012-07-12 15:42:00','127.0.0.1'),(11,'naasdad','asdasd','na.zar.tj@gmail.com','2012-07-12 15:11:01','127.0.0.1'),(12,'asdasd','asdad','na.zar.tj@gmail.com','2012-07-12 15:23:01','127.0.0.1'),(13,'asdasd','asdasd','na.zar.tj@gmail.com','2012-07-12 15:29:02','127.0.0.1'),(14,'naasdad','asdasd','na.zar.tj@gmail.com','2012-07-12 15:56:02','127.0.0.1'),(15,'asdasd','asdasd','na.zar.tj@gmail.com','2012-07-12 15:46:19','127.0.0.1'),(16,'asdasdnasdds','1231231123','nazartj@gmail.com.pe','2012-07-14 20:33:22','127.0.0.1'),(17,'asdasd','2412234234','na.zar.tj@gmail.com','2012-07-14 22:03:20','127.0.0.1'),(18,'asdasd','2412234234','na.zar.tj@gmail.com','2012-07-14 22:18:21','127.0.0.1'),(19,'asdasd','2412234234','na.zar.tj@gmail.com','2012-07-14 22:04:22','127.0.0.1'),(20,'asdasd','2412234234','na.zar.tj@gmail.com','2012-07-14 22:06:26','127.0.0.1'),(21,'adasd','782323234','na.zar.tj@gmail.com','2012-07-14 22:19:29','127.0.0.1'),(22,'adasd','782323234','na.zar.tj@gmail.com','2012-07-14 22:29:30','127.0.0.1'),(23,'adasd','782323234','na.zar.tj@gmail.com','2012-07-14 22:36:38','127.0.0.1');

UNLOCK TABLES;

/*Table structure for table `producto` */

DROP TABLE IF EXISTS `producto`;

CREATE TABLE `producto` (
  `IdProducto` int(11) NOT NULL AUTO_INCREMENT,
  `NombreProducto` char(150) DEFAULT NULL,
  `IdArea` int(11) DEFAULT NULL,
  PRIMARY KEY (`IdProducto`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `producto` */

LOCK TABLES `producto` WRITE;

insert  into `producto`(`IdProducto`,`NombreProducto`,`IdArea`) values (1,'Caramelo',1),(2,'Azucar',2);

UNLOCK TABLES;

/*Table structure for table `usuarioareacorreocontactenos` */

DROP TABLE IF EXISTS `usuarioareacorreocontactenos`;

CREATE TABLE `usuarioareacorreocontactenos` (
  `IdUsuario` int(11) DEFAULT NULL,
  `IdArea` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `usuarioareacorreocontactenos` */

LOCK TABLES `usuarioareacorreocontactenos` WRITE;

insert  into `usuarioareacorreocontactenos`(`IdUsuario`,`IdArea`) values (1,1),(2,1);

UNLOCK TABLES;

/*Table structure for table `usuariosistema` */

DROP TABLE IF EXISTS `usuariosistema`;

CREATE TABLE `usuariosistema` (
  `IdUsuario` int(11) NOT NULL AUTO_INCREMENT,
  `NombreUsuario` char(200) DEFAULT NULL,
  `LoginUsuario` char(200) DEFAULT NULL,
  `PasswordUsuario` char(20) DEFAULT NULL,
  `Correo` char(200) DEFAULT NULL,
  PRIMARY KEY (`IdUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `usuariosistema` */

LOCK TABLES `usuariosistema` WRITE;

insert  into `usuariosistema`(`IdUsuario`,`NombreUsuario`,`LoginUsuario`,`PasswordUsuario`,`Correo`) values (1,'Nazart','nazartj@gmail.com','123456','nazartj@gmail.com'),(2,'Nazart','nazarjb@hotmail.com','123456','nazarjb@hotmail.com');

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
