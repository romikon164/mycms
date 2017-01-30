-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Янв 30 2017 г., 08:35
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `mycms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_data`
--

CREATE TABLE IF NOT EXISTS `mycms_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '1',
  `code` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `createdon` datetime NOT NULL,
  `editedon` datetime NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `access_level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `mycms_data`
--

INSERT INTO `mycms_data` (`id`, `type`, `code`, `parent`, `createdon`, `editedon`, `published`, `deleted`, `access_level`) VALUES
(1, 1, 'home', 0, '2017-01-29 14:08:00', '2017-01-29 14:08:00', 1, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_data_properties`
--

CREATE TABLE IF NOT EXISTS `mycms_data_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `data_id` int(11) NOT NULL,
  `value` text NOT NULL,
  `value_number` decimal(10,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `mycms_data_properties`
--

INSERT INTO `mycms_data_properties` (`id`, `property_id`, `data_id`, `value`, `value_number`) VALUES
(1, 1, 1, 'Главная страница', '0.0000'),
(2, 2, 1, 'Это содержимое главной страницы', '0.0000');

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_data_type`
--

CREATE TABLE IF NOT EXISTS `mycms_data_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `access_level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `mycms_data_type`
--

INSERT INTO `mycms_data_type` (`id`, `code`, `parent`, `access_level`) VALUES
(1, 'content', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_data_type_properties`
--

CREATE TABLE IF NOT EXISTS `mycms_data_type_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_type` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `enum` tinyint(1) NOT NULL DEFAULT '0',
  `default` text NOT NULL,
  `default_number` decimal(10,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `mycms_data_type_properties`
--

INSERT INTO `mycms_data_type_properties` (`id`, `data_type`, `code`, `type`, `enum`, `default`, `default_number`) VALUES
(1, 1, 'pagetitle', 'text', 0, '', '0.0000'),
(2, 1, 'content', 'text', 0, '', '0.0000');

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_groups`
--

CREATE TABLE IF NOT EXISTS `mycms_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `access_level` varchar(3) NOT NULL DEFAULT '000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `mycms_groups`
--

INSERT INTO `mycms_groups` (`id`, `name`, `access_level`) VALUES
(1, '(anonymous)', '000'),
(2, 'Administrator', '999'),
(3, 'User', '000');

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_roots`
--

CREATE TABLE IF NOT EXISTS `mycms_roots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `default_action` varchar(255) NOT NULL,
  `access_level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `mycms_roots`
--

INSERT INTO `mycms_roots` (`id`, `name`, `path`, `controller`, `default_action`, `access_level`) VALUES
(1, 'web', '', 'core.page', 'view', 0),
(2, 'admin', 'admin', 'core.admin', 'view', 0),
(3, 'catalog', 'catalog', 'core.catalog', 'view', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_root_actions`
--

CREATE TABLE IF NOT EXISTS `mycms_root_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `access_level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `mycms_root_actions`
--

INSERT INTO `mycms_root_actions` (`id`, `root_id`, `action`, `access_level`) VALUES
(1, 1, 'view', 1),
(2, 2, 'view', 9),
(3, 3, 'view', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_session`
--

CREATE TABLE IF NOT EXISTS `mycms_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `updatedon` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `mycms_session`
--

INSERT INTO `mycms_session` (`id`, `session_id`, `data`, `updatedon`) VALUES
(7, '3dtnaie1u3ucitgte148ad0ki3', 'test|s:5:"test2";', '2017-01-26 00:14:24'),
(8, 'fkn83cvdqh8sah93lnll9qvvs1', '', '2017-01-30 03:36:44');

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_settings`
--

CREATE TABLE IF NOT EXISTS `mycms_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `namespace` varchar(255) NOT NULL DEFAULT 'core',
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Дамп данных таблицы `mycms_settings`
--

INSERT INTO `mycms_settings` (`id`, `namespace`, `type`, `name`, `value`) VALUES
(14, 'core', 'text', 'site_name', 'myCMS'),
(15, 'core', 'text', 'request_class', 'request');

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_users`
--

CREATE TABLE IF NOT EXISTS `mycms_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(32) NOT NULL,
  `token` varchar(32) NOT NULL,
  `createdon` datetime NOT NULL,
  `loggedon` datetime NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `mycms_users`
--

INSERT INTO `mycms_users` (`id`, `username`, `password`, `token`, `createdon`, `loggedon`, `active`, `blocked`) VALUES
(1, 'admin', '38b6b7e04bc7b9679fd648795514b8cb', '5a20f315dfc69f2eb9081a8701f588f7', '2017-01-25 00:28:00', '2017-01-26 23:44:40', 1, 0),
(3, 'romikon164', 'c46be56f77dba027f3c8d3c0dbd70116', '36cad267ac00da92eb3299df32fd6e30', '2017-01-26 02:27:35', '2017-01-26 23:41:46', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `mycms_user_groups`
--

CREATE TABLE IF NOT EXISTS `mycms_user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `mycms_user_groups`
--

INSERT INTO `mycms_user_groups` (`id`, `user_id`, `group_id`) VALUES
(1, 1, 2),
(2, 3, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
