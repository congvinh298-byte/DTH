/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `source_code` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `source_code`;

CREATE TABLE IF NOT EXISTS `biendongsodu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `truoc` bigint DEFAULT '0',
  `sau` bigint DEFAULT '0',
  `note` varchar(250) DEFAULT NULL,
  `tongtien` bigint DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `blockip` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(250) DEFAULT NULL,
  `time` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `chuyentien` (
  `id` int NOT NULL AUTO_INCREMENT,
  `userchuyen` varchar(250) DEFAULT NULL,
  `usernhan` varchar(250) DEFAULT NULL,
  `sotien` bigint DEFAULT NULL,
  `time` varchar(250) DEFAULT NULL,
  `ip` varchar(250) DEFAULT NULL,
  `hethong` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `danhmucmuacode` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `mota` varchar(250) DEFAULT NULL,
  `img` varchar(250) DEFAULT NULL,
  `status` varchar(250) DEFAULT 'SHOW',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `danhmuctaoweb` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `mota` varchar(250) DEFAULT NULL,
  `img` varchar(250) DEFAULT NULL,
  `status` varchar(250) DEFAULT 'SHOW',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `danhsachmien` (
  `id` int NOT NULL AUTO_INCREMENT,
  `domain` varchar(250) DEFAULT NULL,
  `money` bigint DEFAULT '0',
  `giahan` bigint DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `danhsachmuacode` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_danhmuc` varchar(250) DEFAULT NULL,
  `title` text,
  `mota` text,
  `img` varchar(250) DEFAULT NULL,
  `listimg` text,
  `money` bigint DEFAULT NULL,
  `demo` varchar(250) DEFAULT NULL,
  `download` varchar(250) DEFAULT NULL,
  `download_link1s` varchar(250) DEFAULT NULL,
  `luotxem` bigint DEFAULT '0',
  `luottai` bigint DEFAULT '0',
  `hienthi` varchar(250) DEFAULT 'SHOW',
  `ngaydang` datetime DEFAULT NULL,
  `timeupdate` datetime DEFAULT NULL,
  `statusmua` varchar(250) DEFAULT 'ON',
  `partner` varchar(250) DEFAULT 'adminori',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=312 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `danhsachtaoweb` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_danhmuc` varchar(250) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `mota` varchar(250) DEFAULT NULL,
  `img` varchar(250) DEFAULT NULL,
  `listimg` text,
  `money` varchar(250) DEFAULT NULL,
  `demo` varchar(250) DEFAULT NULL,
  `hienthi` varchar(250) DEFAULT 'SHOW',
  `status` varchar(3) DEFAULT 'ON',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `domainclf` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(250) DEFAULT NULL,
  `auth` varchar(250) DEFAULT NULL,
  `accountid` varchar(250) DEFAULT NULL,
  `status` varchar(250) DEFAULT NULL,
  `ns1` varchar(250) DEFAULT NULL,
  `ns2` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `giahanmien` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `id_domain` int DEFAULT NULL,
  `tenmien` varchar(250) DEFAULT NULL,
  `tongtien` varchar(250) DEFAULT NULL,
  `thoigian` varchar(250) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `status` varchar(250) DEFAULT 'xuly',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `giohang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `id_code` varchar(250) DEFAULT NULL,
  `sotien` varchar(250) DEFAULT NULL,
  `time` varchar(250) DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  `status` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `hoadon_vi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `magd` varchar(250) DEFAULT NULL,
  `sotien` bigint DEFAULT '0',
  `thucnhan` bigint DEFAULT '0',
  `status` varchar(25) DEFAULT 'xuly',
  `thoigian` datetime DEFAULT NULL,
  `uptime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `key_apis` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `whois` varchar(250) DEFAULT 'OFF',
  `list_web` varchar(250) DEFAULT 'OFF',
  `list_code` varchar(250) DEFAULT 'OFF',
  `buy_web` varchar(250) DEFAULT 'OFF',
  `buy_code` varchar(250) DEFAULT 'OFF',
  `buy_domain` varchar(250) DEFAULT 'OFF',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `lichsugiahan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `id_web` int DEFAULT NULL,
  `tenmien` varchar(250) DEFAULT NULL,
  `tongtien` varchar(250) DEFAULT NULL,
  `thoigian` varchar(250) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `status` varchar(250) DEFAULT 'xuly',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `lichsumuacode` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `magd` varchar(250) DEFAULT NULL,
  `username` varchar(250) DEFAULT NULL,
  `id_code` bigint DEFAULT NULL,
  `magiamgia` varchar(250) DEFAULT NULL,
  `tongtien` bigint DEFAULT NULL,
  `hinhthuc` varchar(250) DEFAULT 'log',
  `time` varchar(100) DEFAULT NULL,
  `time2` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124453 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `lichsumuacode2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `magd` varchar(250) DEFAULT NULL,
  `id_code` varchar(250) DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  `time` varchar(250) DEFAULT NULL,
  `tongtien` varchar(250) DEFAULT NULL,
  `magiamgia` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8080 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `lichsumuamien` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `domain` varchar(250) DEFAULT NULL,
  `ns` text,
  `thoihan` int DEFAULT NULL,
  `timemua` datetime DEFAULT NULL,
  `timedie` datetime DEFAULT NULL,
  `tongtien` bigint NOT NULL,
  `type` varchar(250) DEFAULT NULL,
  `status` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=416 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `lichsutaoweb` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `allhosting` int DEFAULT NULL,
  `id_allhosting` varchar(250) DEFAULT NULL,
  `domainclf` varchar(250) DEFAULT NULL,
  `tenmien` varchar(250) DEFAULT NULL,
  `id_code` varchar(250) DEFAULT NULL,
  `ngaytao` varchar(250) DEFAULT NULL,
  `ngayduyet` varchar(250) DEFAULT NULL,
  `ngayhethan` varchar(250) DEFAULT NULL,
  `thangmua` int DEFAULT NULL,
  `taikhoan` varchar(250) DEFAULT NULL,
  `matkhau` varchar(250) DEFAULT NULL,
  `tongtien` bigint DEFAULT NULL,
  `magiamgia` varchar(250) DEFAULT NULL,
  `moneygiahan` int DEFAULT '120000',
  `domainid` varchar(250) DEFAULT NULL,
  `id_addmien` varchar(250) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `token_web` varchar(250) DEFAULT NULL,
  `note` text,
  `username_ref` varchar(250) DEFAULT NULL,
  `buoc` int DEFAULT '1',
  `linklogin` varchar(250) DEFAULT NULL,
  `tkhs` varchar(250) DEFAULT NULL,
  `mkhs` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=878 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `listbank` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bank` varchar(250) DEFAULT NULL,
  `stk` varchar(250) DEFAULT NULL,
  `img` text,
  `name` varchar(250) DEFAULT NULL,
  `status` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `logclient` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip` varchar(250) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1971 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `magiamgia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `magiamgia` varchar(250) DEFAULT NULL,
  `giambaonhieu` varchar(250) DEFAULT NULL,
  `theloai` varchar(250) DEFAULT NULL,
  `conlai` int DEFAULT NULL,
  `dasudung` int DEFAULT NULL,
  `hienthi` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `napatm` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `hinhthuc` varchar(250) DEFAULT NULL,
  `magd` varchar(250) DEFAULT NULL,
  `sotien` varchar(250) DEFAULT NULL,
  `ndnaptien` varchar(250) DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1553 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `napcard` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `loaithe` varchar(250) DEFAULT NULL,
  `menhgia` varchar(250) DEFAULT NULL,
  `pin` varchar(250) DEFAULT NULL,
  `seri` varchar(250) DEFAULT NULL,
  `thucnhan` varchar(250) NOT NULL,
  `requestid` varchar(250) DEFAULT NULL,
  `status` varchar(250) DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  `uptime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=928 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `nappaypal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `trans_id` varchar(255) DEFAULT NULL,
  `donap` varchar(255) DEFAULT '0',
  `thucnhan` varchar(255) DEFAULT '0',
  `create_date` datetime DEFAULT NULL,
  `create_time` varchar(255) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` text,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `partner_biendongsodu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `truoc` bigint DEFAULT '0',
  `sau` bigint DEFAULT '0',
  `note` varchar(250) DEFAULT NULL,
  `tongtien` bigint DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `id_code` int DEFAULT '0',
  `usermua` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `partner_code` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `name` text,
  `sotien` int NOT NULL DEFAULT '0',
  `mota` text,
  `id_danhmuc` int DEFAULT NULL,
  `img` varchar(250) DEFAULT NULL,
  `list_img` text,
  `link` varchar(250) DEFAULT NULL,
  `hdsd` text,
  `zalo` varchar(13) DEFAULT NULL,
  `timeupdate` datetime DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  `id_public` bigint DEFAULT '0',
  `status` varchar(25) DEFAULT 'xuly',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `partner_ruttien` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `atm` varchar(250) DEFAULT NULL,
  `stk` varchar(250) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `sotien` bigint DEFAULT NULL,
  `status` varchar(25) DEFAULT 'xuly',
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `upload_hoso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `mattruoc` varchar(100) DEFAULT NULL,
  `matsau` varchar(100) DEFAULT NULL,
  `chandung` varchar(100) DEFAULT NULL,
  `thoigian` datetime DEFAULT NULL,
  `note` text,
  `status` varchar(250) DEFAULT 'xuly',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(250) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `passwordc2` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `money` bigint DEFAULT '0',
  `money_partner` bigint DEFAULT '0',
  `total_money` bigint DEFAULT '0',
  `level` varchar(20) DEFAULT NULL,
  `tokenlog` varchar(250) DEFAULT NULL,
  `token_api` varchar(250) DEFAULT NULL,
  `timereg` datetime DEFAULT NULL,
  `timereg2` varchar(250) DEFAULT NULL,
  `timeon` datetime DEFAULT NULL,
  `online` varchar(25) DEFAULT 'OFFLINE',
  `banned` varchar(250) DEFAULT 'ON',
  `ip` varchar(250) DEFAULT NULL,
  `user_agent` text,
  `verify` int DEFAULT '0',
  `zalo` varchar(250) DEFAULT NULL,
  `token_resetpas` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26696 DEFAULT CHARSET=utf8mb3;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
