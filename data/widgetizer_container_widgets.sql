
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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
  `widget_uid` varchar(255) NOT NULL COMMENT 'identifier relation to widget table',
  PRIMARY KEY (`container_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
