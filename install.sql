-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2017 年 12 月 13 日 16:02
-- 伺服器版本: 5.7.20-0ubuntu0.16.04.1
-- PHP 版本： 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `wolfoj`
--

-- --------------------------------------------------------

--
-- 資料表結構 `class_prob`
--

CREATE TABLE `class_prob` (
  `class_id` varchar(12) CHARACTER SET latin1 NOT NULL,
  `prob_order` int(11) DEFAULT NULL,
  `problem_id` varchar(20) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `class_user`
--

CREATE TABLE `class_user` (
  `class_id` varchar(12) CHARACTER SET latin1 NOT NULL,
  `user_id` varchar(50) CHARACTER SET latin1 NOT NULL,
  `nick` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `solution`
--

CREATE TABLE `solution` (
  `solution_id` int(11) NOT NULL,
  `problem_id` varchar(7) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `in_date` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `result` enum('wait','CE','RE','TLE','autoWA','WA','AC','codeAC') NOT NULL,
  `judge_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `class_prob`
--
ALTER TABLE `class_prob`
  ADD PRIMARY KEY (`class_id`,`problem_id`),
  ADD KEY `class_id` (`class_id`,`prob_order`);

--
-- 資料表索引 `class_user`
--
ALTER TABLE `class_user`
  ADD PRIMARY KEY (`class_id`,`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- 資料表索引 `solution`
--
ALTER TABLE `solution`
  ADD PRIMARY KEY (`solution_id`),
  ADD KEY `problem-user` (`problem_id`,`user_id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `solution`
--
ALTER TABLE `solution`
  MODIFY `solution_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
