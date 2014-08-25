-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 25, 2014 at 10:51 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `yima`
--

-- --------------------------------------------------------

--
-- Table structure for table `widgetizer_container_widgets`
--

CREATE TABLE IF NOT EXISTS `widgetizer_container_widgets` (
  `container_id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(40) DEFAULT NULL,
  `template_layout` varchar(40) DEFAULT NULL,
  `template_area` varchar(40) NOT NULL,
  `route_name` varchar(80) DEFAULT NULL,
  `identifier_params` varchar(255) DEFAULT NULL COMMENT 'this identifier help to mix four up tables with other params, suggest path/scheme/params as identifier value',
  `widget_id` int(11) NOT NULL COMMENT 'identifier relation to widget table',
  PRIMARY KEY (`container_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
