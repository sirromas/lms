-- phpMyAdmin SQL Dump
-- version 4.0.10.14
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Sep 29, 2016 at 02:20 AM
-- Server version: 5.6.33
-- PHP Version: 5.6.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cnausa_lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `mdl_states`
--

DROP TABLE IF EXISTS `mdl_states`;
CREATE TABLE IF NOT EXISTS `mdl_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(4) NOT NULL,
  `state` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=52 ;

--
-- Dumping data for table `mdl_states`
--

INSERT INTO `mdl_states` (`id`, `code`, `state`) VALUES
(1, 'AL', 'Alabama'),
(2, 'AK', 'Alaska'),
(3, 'AR', 'Arkansas'),
(4, 'AZ', 'Arizona'),
(5, 'CA', 'California'),
(6, 'CO', 'Colorado'),
(7, 'CT', 'Connecticut'),
(8, 'DE', 'Delaware'),
(9, 'CO', 'District of Colombia'),
(10, 'FL', 'Florida'),
(11, 'GA', 'Georgia'),
(12, 'HI', 'Hawaii'),
(13, '	ID', 'Idaho'),
(14, 'IL', 'Illinois'),
(15, 'IN', 'Indiana'),
(16, 'IA', 'Iowa'),
(17, '	KS', 'Kansas'),
(18, 'KY', 'Kentucky'),
(19, 'LA', 'Louisiana'),
(20, 'ME', 'Maine'),
(21, 'MD', 'Maryland'),
(22, 'MA', 'Massachusetts'),
(23, 'MI', 'Michigan'),
(24, 'MN', 'Minnesota'),
(25, '	MS', 'Mississippi'),
(26, '	MO', 'Missouri'),
(27, 'MT', 'Montana'),
(28, '	NE', 'Nebraska'),
(29, 'NV', 'Nevada'),
(30, 'NH', 'New Hampshire'),
(31, '	NJ', 'New Jersey'),
(32, 'NM', 'New Mexico'),
(33, '	NY', 'New York'),
(34, 'NC', 'North Carolina'),
(35, 'ND', 'North Dakota'),
(36, 'OH', 'Ohio'),
(37, '	OK', 'Oklahoma'),
(38, 'OR', 'Oregon'),
(39, 'PA', 'Pennsylvania'),
(40, 'RI', 'Rhode Island'),
(41, '	SC', 'South Carolina'),
(42, 'SD', 'South Dakota'),
(43, 'TN', 'Tennessee'),
(44, 'TX', 'Texas'),
(45, 'UT', 'Utah'),
(46, 'VT', 'Vermont'),
(47, 'VA', 'Virginia'),
(48, '	WA', 'Washington'),
(49, 'WV', 'West Virginia'),
(50, 'WI', 'Wisconsin'),
(51, '	WY', 'Wyoming');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
