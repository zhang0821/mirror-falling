-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-06-11 04:51:50
-- 服务器版本： 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mirror`
--

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '用户名',
  `schedule` text NOT NULL COMMENT '日程',
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `name`, `schedule`, `message`) VALUES
(1, 'hjf', 'a:2:{i:0;a:2:{s:8:"schedule";s:9:"play ball";s:4:"time";s:10:"2017-06-09";}i:1;a:2:{s:8:"schedule";s:4:"sing";s:4:"time";s:10:"2017-06-10";}}', 'a:2:{i:0;a:3:{s:7:"message";s:13:"test message1";s:4:"time";s:10:"2017-06-09";s:4:"from";s:3:"hjf";}i:1;a:3:{s:7:"message";s:13:"test message2";s:4:"time";s:10:"2017-06-10";s:4:"from";s:3:"hjk";}}'),
(2, 'hjk', 'a:2:{i:0;a:2:{s:8:"schedule";s:16:"xxxxxxxxxxxxxxxx";s:4:"time";s:10:"2017-06-09";}i:1;a:2:{s:8:"schedule";s:12:"aaaaaaaaaaaa";s:4:"time";s:10:"2017-06-10";}}', 'a:2:{i:0;a:3:{s:7:"message";s:13:"test message2";s:4:"time";s:10:"2017-06-09";s:4:"from";s:3:"hjf";}i:1;a:3:{s:7:"message";s:13:"test message2";s:4:"time";s:10:"2017-06-10";s:4:"from";s:3:"hjf";}}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
