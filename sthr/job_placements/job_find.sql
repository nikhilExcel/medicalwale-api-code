-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 17, 2018 at 04:22 AM
-- Server version: 5.5.56-MariaDB
-- PHP Version: 7.0.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_find`
--

-- --------------------------------------------------------

--
-- Table structure for table `job_cat`
--

CREATE TABLE IF NOT EXISTS `job_cat` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `cat_image` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `job_cat`
--

INSERT INTO `job_cat` (`cat_id`, `cat_name`, `cat_image`) VALUES
(1, 'Part Time Job', ''),
(2, 'Full Time Job', ''),
(3, 'Internship', ''),
(4, 'Work From Home', ''),
(5, 'Dream Jobs', ''),
(26, 'Linux/system Admin', '3');

-- --------------------------------------------------------

--
-- Table structure for table `job_img`
--

CREATE TABLE IF NOT EXISTS `job_img` (
  `img_id` int(11) NOT NULL,
  `img_path` varchar(100) NOT NULL,
  `img_active` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `job_img`
--

INSERT INTO `job_img` (`img_id`, `img_path`, `img_active`) VALUES
(19, 'https://recharge.hostingduty.com/jobs/uploads/4.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `job_post`
--

CREATE TABLE IF NOT EXISTS `job_post` (
  `job_id` int(11) NOT NULL,
  `job_title` varchar(200) NOT NULL,
  `job_desc` varchar(500) NOT NULL,
  `job_category` varchar(100) NOT NULL,
  `job_type` int(11) NOT NULL,
  `job_min_salary` int(11) NOT NULL,
  `job_max_salary` int(11) NOT NULL,
  `job_companey_name` varchar(100) NOT NULL,
  `job_city` varchar(100) NOT NULL,
  `job_phone_no` varchar(20) NOT NULL,
  `job_email` varchar(100) NOT NULL,
  `job_status` int(11) NOT NULL DEFAULT '1',
  `job_post_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `job_post`
--

INSERT INTO `job_post` (`job_id`, `job_title`, `job_desc`, `job_category`, `job_type`, `job_min_salary`, `job_max_salary`, `job_companey_name`, `job_city`, `job_phone_no`, `job_email`, `job_status`, `job_post_date_time`) VALUES
(32, 'PHP Developer', 'Part time job for students can earn 5 to 6 K in hand per month', '2', 1, 5000, 8000, 'Medicalwale.com', 'Mumbai', '8899665544', 'medicale@medicalwale.com', 2, '2018-04-11 07:25:47'),
(34, 'Student Part time', 'ghar baithe kamaye 1000 se 1500 rs', '1', 1, 1000, 1500, 'Medicalwale.com', 'Gujrat', '8877445522', 'gujrat@medicalwale.com', 2, '2018-04-11 07:28:44'),
(36, ' ', 'full time job', '27', 2, 50000, 10000, 'surya.com', 'Ahemdabad', '1223355221', 'surya@surya.com', 2, '2018-04-11 07:35:41'),
(37, 'Ayurvedic Doctor', 'asd', '8', 2, 2, 2, 'India Online Ltd', 'Andheri, Mumbai', '8454922727', 'smwcem@gmail.com', 2, '2018-04-11 07:37:23'),
(38, 'Software engineer', 'be sce', '24', 3, 1000000, 2000000, 'aaa', 'Mumbai100', '8899664455', 'sameer9497@yahoo.in', 2, '2018-04-11 07:42:08'),
(42, 'Ayurvedic Doctor', 'http://recharge.hostingduty.com/medical/index.php/welcomehttp://recharge.hostingduty.com/medical/index.php/welcome', '8', 4, 5000, 10000, 'India Online Ltd', 'Andheri, Mumbai', '8983779523', 'sameer9497@yahoo.in', 1, '2018-04-11 07:50:21'),
(46, 'Gshshs', 'sbshz', '1', 1, 10000, 9000, 'ahsha', 'gahah', '8484676766', 'vvzhH@jzjjz', 1, '2018-04-11 12:59:51'),
(47, 'Php', 'gdhd', '3', 3, 10000, 10500, 'hd', 'pune', '885794255', 'sumit@', 1, '2018-04-11 13:03:53'),
(48, 'Android ', 'Android Developer', '2', 2, 20000, 28000, 'HD', 'Mumbai', '9004590640', 'vipul@hostingduty.com', 1, '2018-04-12 12:57:52'),
(49, 'Android', 'Android', '2', 2, 10000, 20000, 'hd', 'pune', '+919422208', 'sumit.speed@rediffmail.com', 1, '2018-04-12 13:03:46'),
(50, 'Macros Developer', 'Urgent required Macros developer', '2', 4, 15000, 75000, 'Contrailtech', 'Pune', '9579729178', 'srmujumdar609@gmail.com', 2, '2018-04-12 15:20:57'),
(51, 'Web developer', 'need html css bootstrap skills\n', '2', 2, 25000, 30000, 'agile systems', 'pune', '9925813261', 'recruit@agile.com', 1, '2018-04-12 17:06:31'),
(53, 'Aaaaaaaaaaaa', 'aaaaaaaaaaa', '2', 2, 25000, 30000, 'HD', 'pune', '8877665544', 'aaaaa@aaaaa.aaa', 1, '2018-04-13 11:34:05'),
(54, 'Linux Admin', 'We need an Immedicate Joiner for our Big CMM level company', '31', 2, 25000, 30000, 'Technext Security Solution', 'Pune', '8369081257', 'technext@gmail.com', 1, '2018-04-13 11:56:19'),
(55, ' ', ' ', '2', 3, 5000, 500000, 's', 'Surat', '7894563210', '...@yah.in', 1, '2018-04-13 12:22:32'),
(56, 'Project manager', 'project management tracking', '31', 2, 50000, 80000, 'alltech solns', 'pune', '9820866321', 'alltech@gmail.com', 2, '2018-04-13 12:50:36');

-- --------------------------------------------------------

--
-- Table structure for table `job_title_category`
--

CREATE TABLE IF NOT EXISTS `job_title_category` (
  `jc_id` int(50) NOT NULL,
  `jc_name` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `job_title_category`
--

INSERT INTO `job_title_category` (`jc_id`, `jc_name`) VALUES
(1, 'Accountant'),
(2, 'Web Developer'),
(3, 'Java Developer'),
(8, 'Automation tester'),
(23, 'Doctors'),
(24, 'Engineers'),
(27, 'Medical Officers'),
(31, 'System Admin');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE IF NOT EXISTS `user_profile` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_mobile` varchar(20) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_dob` date NOT NULL,
  `user_gender` varchar(20) NOT NULL,
  `user_job_title` varchar(100) NOT NULL,
  `user_min_salary` int(11) NOT NULL,
  `user_max_salary` int(11) NOT NULL,
  `user_exp_year` varchar(100) NOT NULL,
  `user_exp_month` varchar(100) NOT NULL,
  `user_city` varchar(100) NOT NULL,
  `user_resume` varchar(200) NOT NULL,
  `user_is_active` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `user_name`, `user_mobile`, `user_email`, `user_dob`, `user_gender`, `user_job_title`, `user_min_salary`, `user_max_salary`, `user_exp_year`, `user_exp_month`, `user_city`, `user_resume`, `user_is_active`) VALUES
(1, 'Akshay', '9898989898', 'akshay@hostingduty.com', '2018-04-02', 'male', 'Pune', 1000000, 2147483647, '10', '0', 'pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Joloapi_recharge_and_bill_integration_api_info2.pdf', 1),
(2, 'sumit', '656465', '564654dsg4asdajb', '0000-00-00', 'male', 'sdjfbk', 45564, 545, '51', '1', 'pu', 'http://recharge.hostingduty.com/medical/uploads/resume/_8432_07-04-2018.PDF', 1),
(3, 'sumit', '656465', '564654dsg4asdajb', '0000-00-00', 'male', 'sdjfbk', 45564, 545, '51', '1', 'pu', 'http://recharge.hostingduty.com/medical/uploads/resume/sumit_5031_07-04-2018.PDF', 1),
(4, 'sumit', '656465', '564654dsg4asdajb', '0000-00-00', 'male', 'sdjfbk', 45564, 545, '51', '1', 'pu', 'http://recharge.hostingduty.com/medical/uploads/resume/sumit_1815_07-04-2018.pdf', 1),
(5, 'sumit12', '656465', '564654dsg4asdajb', '0000-00-00', 'male', 'sdjfbk', 45564, 545, '51', '1', 'pu', 'http://recharge.hostingduty.com/medical/uploads/resume/sumit12_2256_07-04-2018.pdf', 1),
(6, 'sumit', '8857942555', 'sumit@gmial.com', '0000-00-00', 'male', 'manager', 180000, 35000, '3', '2', 'pune', 'http://recharge.hostingduty.com/medical/uploads/resume/648e589899190a9e.pdf', 1),
(7, 'sumit', '8857942555', 'sumit@gmial.com', '0000-00-00', 'male', 'manager', 180000, 35000, '3', '2', 'pune', 'http://recharge.hostingduty.com/medical/uploads/resume/6f620b2f508d73d9.java', 1),
(8, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_3051_11-04-2018.pdf', 1),
(9, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_4761_11-04-2018.pdf', 1),
(10, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_627_11-04-2018.pdf', 1),
(11, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_3857_11-04-2018.pdf', 1),
(12, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_5615_11-04-2018.pdf', 1),
(13, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_943_11-04-2018.pdf', 1),
(14, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_3865_11-04-2018.pdf', 1),
(15, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_9658_11-04-2018.pdf', 1),
(16, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_5518_11-04-2018.pdf', 1),
(17, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_7616_11-04-2018.pdf', 1),
(18, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_5751_11-04-2018.pdf', 1),
(19, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_2933_11-04-2018.pdf', 1),
(20, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_942_11-04-2018.pdf', 1),
(21, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_8428_11-04-2018.pdf', 1),
(22, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_328_11-04-2018.pdf', 1),
(23, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_3168_11-04-2018.pdf', 1),
(24, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_5052_11-04-2018.pdf', 1),
(25, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_6914_11-04-2018.pdf', 1),
(26, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_5454_11-04-2018.pdf', 1),
(27, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_938_11-04-2018.pdf', 1),
(28, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_4331_11-04-2018.pdf', 1),
(29, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_3836_11-04-2018.pdf', 1),
(30, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_4008_11-04-2018.pdf', 1),
(31, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_8701_11-04-2018.pdf', 1),
(32, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_3682_11-04-2018.pdf', 1),
(33, 'Ahhaha', '7667676767', 'jsnsjsj@njdjjx', '0000-00-00', 'Male', 'Ahsjsj', 10000, 25000, '10', '5', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/Ahhaha_4902_11-04-2018.pdf', 1),
(34, 'Sss', '8857942555', 'sumit@gmail.com', '0000-00-00', 'Male', 'Developer', 10000, 40000, '5', '12', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/c3df326b18877c2b.pdf', 1),
(35, 'Sss', '8857942555', 'sumit@gmail.com', '0000-00-00', 'Male', 'Developer', 10000, 40000, '5', '12', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/c3df326b18877c2b.pdf', 1),
(36, 'Sss', '8857942555', 'sumit@gmail.com', '0000-00-00', 'Male', 'Dev', 10000, 20000, '2', '11', 'Pune', 'http://recharge.hostingduty.com/medical/uploads/resume/6e2ec894982fae85.docx', 1),
(37, 'speed', '8857942588', 'sumit', '0000-00-00', 'Male', 'dev', 10000, 30000, '3', '12', 'pune', 'http://recharge.hostingduty.com/medical/uploads/resume/a23c8376405d3e1c.pdf', 1),
(38, 'amol', '9920596045', 'amolb@gmail.com', '0000-00-00', 'Male', 'programmer', 10000, 40000, '2', '10', 'mumbai', '', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `job_cat`
--
ALTER TABLE `job_cat`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `job_img`
--
ALTER TABLE `job_img`
  ADD PRIMARY KEY (`img_id`);

--
-- Indexes for table `job_post`
--
ALTER TABLE `job_post`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `job_title_category`
--
ALTER TABLE `job_title_category`
  ADD PRIMARY KEY (`jc_id`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `job_cat`
--
ALTER TABLE `job_cat`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `job_img`
--
ALTER TABLE `job_img`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `job_post`
--
ALTER TABLE `job_post`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=57;
--
-- AUTO_INCREMENT for table `job_title_category`
--
ALTER TABLE `job_title_category`
  MODIFY `jc_id` int(50) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=39;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
