-- phpMyAdmin SQL Dump
-- version 4.0.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 08, 2013 at 02:42 AM
-- Server version: 5.5.32-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `susieBotKnowledgeBase`
--

-- --------------------------------------------------------

--
-- Table structure for table `changeContextByPatternResponse`
--

CREATE TABLE IF NOT EXISTS `changeContextByPatternResponse` (
  `changeContextByPatternResponseID` int(11) NOT NULL AUTO_INCREMENT,
  `contextID` int(11) NOT NULL,
  `patternResponseID` int(11) NOT NULL,
  `newPriority` int(11) DEFAULT NULL,
  `relativePriority` enum('TOP','BOTTOM','MIDDLE') DEFAULT NULL,
  PRIMARY KEY (`changeContextByPatternResponseID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `changeContextByResponse`
--

CREATE TABLE IF NOT EXISTS `changeContextByResponse` (
  `changeContextByResponseID` int(11) NOT NULL AUTO_INCREMENT,
  `contextID` int(11) NOT NULL,
  `responseID` int(11) NOT NULL,
  `newPriority` int(11) DEFAULT NULL,
  `relativePriority` enum('TOP','BOTTOM','MIDDLE') DEFAULT NULL,
  PRIMARY KEY (`changeContextByResponseID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `context`
--

CREATE TABLE IF NOT EXISTS `context` (
  `contextID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`contextID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pattern`
--

CREATE TABLE IF NOT EXISTS `pattern` (
  `patternID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL COMMENT 'Human Friendly Description of trigger.',
  `regex` varchar(128) NOT NULL,
  `patternResponseID` int(11) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`patternID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pattern`
--

INSERT INTO `pattern` (`patternID`, `name`, `regex`, `patternResponseID`, `priority`) VALUES
(1, NULL, '^hi', 1, 0),
(2, NULL, '^hello', 1, 0),
(3, NULL, '^greetings', 1, 0),
(4, NULL, '^hi', 2, 10);

-- --------------------------------------------------------

--
-- Table structure for table `patternResponse`
--

CREATE TABLE IF NOT EXISTS `patternResponse` (
  `patternResponseID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `command` varchar(128) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `contextID` int(11) DEFAULT NULL,
  PRIMARY KEY (`patternResponseID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `patternResponse`
--

INSERT INTO `patternResponse` (`patternResponseID`, `name`, `command`, `priority`, `contextID`) VALUES
(1, 'Greetings', '$testVar = 1;', 0, NULL),
(2, 'Test Greeting Overwrite', NULL, 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `response`
--

CREATE TABLE IF NOT EXISTS `response` (
  `responseID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `responseString` varchar(128) NOT NULL,
  `patternResponseID` int(11) NOT NULL,
  PRIMARY KEY (`responseID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `response`
--

INSERT INTO `response` (`responseID`, `name`, `responseString`, `patternResponseID`) VALUES
(1, NULL, 'Hi there $testVar', 1),
(2, NULL, 'Hey, how are you?', 1),
(3, NULL, 'You talking to me?', 1),
(4, NULL, 'haha! I came first!', 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
