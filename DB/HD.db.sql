-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 01 2017 г., 14:38
-- Версия сервера: 5.5.35
-- Версия PHP: 5.3.10-1ubuntu3.26


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `hd`
--

-- --------------------------------------------------------

--
-- Структура таблицы `approved_info`
--

CREATE TABLE IF NOT EXISTS `approved_info` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `fio` varchar(256) DEFAULT NULL,
  `tel` varchar(256) DEFAULT NULL,
  `login` varchar(256) DEFAULT NULL,
  `unit_desc` varchar(1024) DEFAULT NULL,
  `adr` varchar(256) DEFAULT NULL,
  `email` varchar(256) DEFAULT NULL,
  `posada` varchar(256) DEFAULT NULL,
  `user_from` int(11) DEFAULT NULL,
  `date_app` datetime DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fio` varchar(512) DEFAULT NULL,
  `tel` varchar(128) DEFAULT NULL,
  `login` varchar(256) DEFAULT NULL,
  `unit_desc` varchar(1024) DEFAULT NULL,
  `adr` varchar(128) DEFAULT NULL,
  `tel_ext` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `posada` varchar(256) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `t_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment_text` longtext,
  `dt` datetime DEFAULT NULL,
  `hashname_comment` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `deps`
--

CREATE TABLE IF NOT EXISTS `deps` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

LOCK TABLES `deps` WRITE;
/*!40000 ALTER TABLE `deps` DISABLE KEYS */;

INSERT INTO `deps` (`id`, `name`)
VALUES
	(0,'Все'),
	(1,'Отдел WEB-разработки'),
	(2,'Сектор хостинга'),
	(3,'Отдел SEO, рекламы и маркетинга'),
	(4,'Отдел безопастности сети'),
	(5,'Отдел поддержки пользователей');

/*!40000 ALTER TABLE `deps` ENABLE KEYS */;
UNLOCK TABLES;
-- --------------------------------------------------------

--
-- Структура таблицы `dop_field`
--

CREATE TABLE IF NOT EXISTS `dop_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_hash` varchar(256) NOT NULL,
  `field_subj` int(11) DEFAULT '0',
  `field_name` varchar(256) NOT NULL,
  `field_placeholder` varchar(256) NOT NULL,
  `field_value` longtext NOT NULL,
  `field_type` varchar(100) NOT NULL DEFAULT 'text',
  `field_status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `dop_fields` ENABLE KEYS */;
UNLOCK TABLES;
-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_hash` varchar(512) DEFAULT NULL,
  `original_name` varchar(512) DEFAULT NULL,
  `file_hash` varchar(512) DEFAULT NULL,
  `file_type` varchar(512) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_ext` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `files_comment`
--

CREATE TABLE IF NOT EXISTS `files_comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_hash` varchar(512) DEFAULT NULL,
  `original_name` varchar(512) DEFAULT NULL,
  `file_hash` varchar(512) DEFAULT NULL,
  `file_type` varchar(512) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_ext` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `helper`
--

CREATE TABLE IF NOT EXISTS `helper` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_init_id` int(11) DEFAULT NULL,
  `unit_to_id` varchar(11) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `title` varchar(1024) DEFAULT NULL,
  `message` longtext,
  `hashname` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `subj` varchar(512) DEFAULT NULL,
  `msg` varchar(1024) DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `is_read` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hashname` varchar(512) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` longtext,
  `dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `noty`
--

CREATE TABLE IF NOT EXISTS `noty` (
  `id` int(11) NOT NULL,
  `noty_w` varchar(256) NOT NULL,
  `userid` varchar(512) NOT NULL DEFAULT '0',
  `user_read` varchar(512) NOT NULL DEFAULT '0',
  `dt` datetime NOT NULL,
  `message` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `perf`
--

CREATE TABLE IF NOT EXISTS `perf` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `param` varchar(512) NOT NULL DEFAULT '',
  `value` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

LOCK TABLES `perf` WRITE;
INSERT INTO `perf` (`id`, `param`, `value`)
VALUES
	(1, 'title_header', 'IT корпорация'),
	(2, 'hostname', 'http://localhost/web/HD.rustem/'),
	(3, 'mail', 'hd@hd.local'),
	(4, 'days2arch', '3'),
	(5, 'name_of_firm', 'IT корпорация'),
	(6, 'fix_subj', 'true'),
	(7, 'first_login', 'false'),
	(8, 'file_uploads', 'true'),
	(9, 'debug_mode', 'false'),
	(10, 'mail_active', 'false'),
	(11, 'mail_host', 'smtp.gmail.com'),
	(12, 'mail_port', '587'),
	(13, 'mail_auth', 'true'),
	(14, 'mail_auth_type', 'ssl'),
	(15, 'mail_username', 'your_login@gmail.com'),
	(16, 'mail_password', 'your_pass'),
	(17, 'mail_from', 'helpdesk'),
	(18, 'mail_debug', 'false'),
	(19, 'mail_type', 'sendmail'),
	(20, 'file_types', 'gif|jpe?g|png|doc|xls|rtf|pdf|zip|rar|bmp|docx|xlsx'),
	(21, 'file_size', '5000000'),
  (22, 'jabber_active', 'false'),
  (23, 'jabber_server', 'your_server_jabber'),
  (24, 'jabber_port', '5222'),
  (25, 'jabber_login', 'server_login_jabber'),
  (26, 'jabber_pass', 'server_jabber_password'),
  (27, 'time_zone', 'Europe/Moscow');

UNLOCK TABLES;
-- --------------------------------------------------------

--
-- Структура таблицы `posada`
--

CREATE TABLE IF NOT EXISTS `posada` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

LOCK TABLES `posada` WRITE;
/*!40000 ALTER TABLE `posada` DISABLE KEYS */;

INSERT INTO `posada` (`id`, `name`)
VALUES
	(1,'администратор');

/*!40000 ALTER TABLE `posada` ENABLE KEYS */;
UNLOCK TABLES;
-- --------------------------------------------------------

--
-- Структура таблицы `subj`
--

CREATE TABLE IF NOT EXISTS `subj` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(11) NOT NULL,
  `name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

LOCK TABLES `subj` WRITE;
/*!40000 ALTER TABLE `subj` DISABLE KEYS */;

INSERT INTO `subj` (`id`, `name`)
VALUES
	(25,'Система'),
	(28,'Интернет и локальная сеть'),
	(30,'Телефония'),
	(31,'Другое'),
	(32,'Компьютеры и переферия'),
	(33,'Принтеры (обслуживание)'),
	(35,'Видеонаблюдение'),
	(36,'Установка ПО');

/*!40000 ALTER TABLE `subj` ENABLE KEYS */;
UNLOCK TABLES;
-- --------------------------------------------------------

--
-- Структура таблицы `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_init_id` int(11) DEFAULT NULL,
  `user_to_id` varchar(128) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `subj` varchar(512) DEFAULT NULL,
  `msg` longtext,
  `client_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `hash_name` varchar(512) DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  `arch` int(11) DEFAULT '0',
  `is_read` int(11) DEFAULT '0',
  `lock_by` int(11) DEFAULT '0',
  `last_edit` datetime DEFAULT NULL,
  `ok_by` int(11) DEFAULT '0',
  `prio` int(4) NOT NULL DEFAULT '0',
  `familiar` varchar(128) DEFAULT '0',
  `ok_date` datetime NOT NULL,
  `last_update` datetime DEFAULT NULL,
  `lock_t` datetime DEFAULT NULL,
  `work_t` longtext,
  `deadline_t` datetime NOT NULL,
  `permit_ok` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tickets_fields`
--

CREATE TABLE IF NOT EXISTS `tickets_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_hash` varchar(256) NOT NULL,
  `field_name` varchar(256) NOT NULL,
  `field_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `ticket_log`
--

CREATE TABLE IF NOT EXISTS `ticket_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date_op` datetime DEFAULT NULL,
  `msg` varchar(1024) CHARACTER SET latin1 DEFAULT NULL,
  `init_user_id` int(11) DEFAULT NULL,
  `to_user_id` varchar(128) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `to_unit_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

CREATE TABLE IF NOT EXISTS `units` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;

INSERT INTO `units` (`id`, `name`)
VALUES
	(1,'Бухгалтерия'),
	(2,'Кадры');

/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;
-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fio` varchar(512) DEFAULT NULL,
  `login` varchar(64) DEFAULT NULL,
  `pass` varchar(64) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `priv` int(11) DEFAULT '0',
  `unit` varchar(255) DEFAULT NULL,
  `is_admin` int(4) NOT NULL DEFAULT '0',
  `email` varchar(128) DEFAULT NULL,
  `jabber` varchar(128) DEFAULT NULL,
  `messages` varchar(2048) DEFAULT NULL,
  `lang` varchar(11) DEFAULT NULL,
  `priv_add_client` int(11) NOT NULL DEFAULT '0',
  `priv_edit_client` int(11) NOT NULL DEFAULT '1',
  `last_time` datetime NOT NULL,
  `jabber_noty` int(11) NOT NULL DEFAULT '0',
  `jabber_noty_show` varchar(128) DEFAULT '1',
  `show_noty` varchar(128) DEFAULT 'bottomRight',
  `noty` varchar(128) DEFAULT '1,2,3,4,5,6,7,8,9,10',
  `us_kill` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `fio`, `login`, `pass`, `status`, `priv`, `unit`, `is_admin`, `email`)
VALUES
	(1,'Main system account','system','81dc9bdb52d04dc20036dbd8313ed055',1,2,'1,2,3',8,'');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
