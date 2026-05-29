-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 11:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `activity_id` varchar(20) NOT NULL,
  `activity_content` text NOT NULL,
  `activity_company` varchar(100) NOT NULL,
  `activity_branch` varchar(100) NOT NULL,
  `activity_type` varchar(30) NOT NULL,
  `activity_on` datetime NOT NULL,
  `activity_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `alertnotes`
--

CREATE TABLE `alertnotes` (
  `id` int(11) NOT NULL,
  `alert_id` varchar(10) NOT NULL,
  `alert_title` varchar(255) NOT NULL,
  `alert_message` longtext NOT NULL,
  `alert_date` date NOT NULL,
  `alert_for_company` varchar(100) NOT NULL,
  `alert_createdby` varchar(50) NOT NULL,
  `alert_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `branch_id` varchar(10) NOT NULL,
  `branch_of_company` varchar(255) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `branch_gst` varchar(20) NOT NULL,
  `branch_contact` varchar(15) NOT NULL,
  `branch_email` varchar(50) NOT NULL,
  `branch_address` text NOT NULL,
  `branch_registration_number` varchar(50) NOT NULL,
  `branch_bank_name` varchar(50) NOT NULL,
  `branch_bank_branch` varchar(50) NOT NULL,
  `branch_account_number` varchar(20) NOT NULL,
  `branch_ifsc_code` varchar(20) NOT NULL,
  `branch_term_and_condition` text NOT NULL,
  `branch_status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `company_id` varchar(20) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `user_limit` varchar(15) NOT NULL,
  `created_on` varchar(15) NOT NULL,
  `created_time` varchar(15) NOT NULL,
  `company_status` int(1) NOT NULL DEFAULT 0,
  `company_createdby` varchar(50) NOT NULL,
  `company_updatedby` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `employee_name` varchar(50) NOT NULL,
  `profile_pic` varchar(255) NOT NULL,
  `employee_address` text NOT NULL,
  `employee_email` varchar(50) NOT NULL,
  `employee_contact` varchar(15) NOT NULL,
  `employee_personal_contact` varchar(12) NOT NULL,
  `employee_of_company` varchar(255) NOT NULL,
  `employee_of_branch` varchar(100) NOT NULL,
  `employee_dob` date NOT NULL,
  `employee_user_role` varchar(20) NOT NULL,
  `employee_username` varchar(50) NOT NULL,
  `employee_user_password` varchar(30) NOT NULL,
  `employee_father_name` varchar(50) NOT NULL,
  `employee_father_phone` varchar(50) NOT NULL,
  `employee_bank_name` varchar(20) NOT NULL,
  `employee_bank_account` varchar(20) NOT NULL,
  `employee_bank_ifsc` varchar(20) NOT NULL,
  `employee_adhar_number` varchar(20) NOT NULL,
  `employee_status` tinyint(1) DEFAULT 0,
  `created_by` varchar(50) NOT NULL,
  `updated_by` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `importleads`
--

CREATE TABLE `importleads` (
  `id` int(11) NOT NULL,
  `lead_customer_name` varchar(255) NOT NULL,
  `lead_customer_contact` varchar(50) NOT NULL,
  `lead_email` varchar(100) DEFAULT NULL,
  `lead_whatsapp` varchar(20) DEFAULT NULL,
  `lead_for_company` varchar(255) DEFAULT NULL,
  `lead_for_branch` varchar(255) DEFAULT NULL,
  `lead_customer_type` varchar(50) NOT NULL,
  `lead_type` varchar(100) DEFAULT NULL,
  `lead_status` varchar(100) DEFAULT NULL,
  `lead_followup_date` date DEFAULT NULL,
  `lead_next_followup_date` date DEFAULT NULL,
  `lead_next_followup_time` time DEFAULT NULL,
  `lead_delivery_address` text DEFAULT NULL,
  `lead_createdby` varchar(100) DEFAULT NULL,
  `lead_assign_to` varchar(100) DEFAULT NULL,
  `lead_customer_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` int(11) NOT NULL,
  `lead_id` varchar(20) NOT NULL,
  `lead_customer_name` varchar(50) NOT NULL,
  `lead_customer_contact` varchar(15) NOT NULL,
  `lead_alternate_contact` varchar(15) NOT NULL,
  `lead_for_company` varchar(255) NOT NULL,
  `lead_for_branch` varchar(50) NOT NULL,
  `lead_state` varchar(50) NOT NULL,
  `lead_reference` varchar(50) NOT NULL,
  `lead_customer_gst` varchar(25) NOT NULL,
  `lead_customer_cin` varchar(30) NOT NULL,
  `lead_product_services` varchar(200) NOT NULL,
  `lead_delivery_address` text NOT NULL,
  `lead_delivery_subject` text NOT NULL,
  `lead_product_regular_price` varchar(10) NOT NULL,
  `lead_product_quantity` varchar(5) NOT NULL,
  `lead_product_offer_price` varchar(10) NOT NULL,
  `lead_product_remark` text NOT NULL,
  `lead_sub_total` varchar(10) NOT NULL,
  `lead_other_charges` varchar(10) NOT NULL DEFAULT '0',
  `lead_grand_total` varchar(10) NOT NULL,
  `lead_email` varchar(50) NOT NULL,
  `lead_followup_date` date NOT NULL,
  `lead_delivery_date` date NOT NULL,
  `lead_next_followup_date` date NOT NULL,
  `lead_next_followup_time` time NOT NULL,
  `lead_whatsapp` varchar(15) NOT NULL,
  `lead_assign_to` varchar(50) NOT NULL,
  `assign_technician` varchar(100) NOT NULL,
  `lead_instalation_aggreement` varchar(255) NOT NULL,
  `lead_instalation_date` date NOT NULL,
  `assign_technician_date` date NOT NULL,
  `lead_status` varchar(50) NOT NULL,
  `lead_remark` text NOT NULL,
  `lead_createdby` varchar(50) NOT NULL,
  `lead_updatedby` varchar(50) NOT NULL,
  `lead_type` varchar(20) NOT NULL,
  `lead_customer_type` varchar(10) NOT NULL,
  `lead_customer_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `message_id` varchar(20) NOT NULL,
  `messages` text NOT NULL,
  `message_to` varchar(50) NOT NULL,
  `message_by` varchar(50) NOT NULL,
  `message_aton` datetime NOT NULL,
  `message_event` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_id` varchar(20) NOT NULL,
  `permission_options` varchar(50) NOT NULL,
  `permission_assign_to` varchar(50) NOT NULL,
  `permission_assined_by` varchar(50) NOT NULL,
  `permission_aton` datetime NOT NULL DEFAULT current_timestamp(),
  `permission_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_id`, `permission_options`, `permission_assign_to`, `permission_assined_by`, `permission_aton`, `permission_status`) VALUES
(1, '#PER2265731164', 'States Lists', 'Manager, Staff, Technician', 'aDMIN', '2025-05-13 14:46:36', 1),
(2, '#PER704047169', 'Users Control', 'Manager, Staff, Technician', 'aDMIN', '2025-05-13 14:47:01', 1),
(3, '#PER7944914637', 'Products Control', 'Manager, Staff, Technician', 'aDMIN', '2025-05-13 14:47:08', 1),
(4, '#PER4451190622', 'Leads Control', 'Manager, Staff, Technician', 'aDMIN', '2025-05-13 14:47:24', 1),
(5, '#PER7893705227', 'Report', 'Manager, Staff, Technician', 'aDMIN', '2025-05-13 14:47:32', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_id` varchar(20) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_varient` varchar(255) NOT NULL,
  `product_short_description` text NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `product_for_company` varchar(200) NOT NULL,
  `product_createdby` varchar(100) NOT NULL,
  `product_updatedby` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `references`
--

CREATE TABLE `references` (
  `id` int(11) NOT NULL,
  `reference_id` varchar(20) NOT NULL,
  `reference_name` varchar(50) NOT NULL,
  `reference_contact` varchar(15) NOT NULL,
  `reference_for_company` varchar(255) NOT NULL,
  `reference_for_branch` varchar(255) NOT NULL,
  `reference_createdby` varchar(50) NOT NULL,
  `reference_updatedby` varchar(50) NOT NULL,
  `reference_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `setting_aton` datetime NOT NULL,
  `setting_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting`, `setting_aton`, `setting_status`) VALUES
(1, 'maintenance_mode', '2025-06-05 15:11:03', 0);

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) NOT NULL,
  `state_name` varchar(30) NOT NULL,
  `state_status` int(1) NOT NULL DEFAULT 0,
  `created_by` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notify_id` (`activity_id`);

--
-- Indexes for table `alertnotes`
--
ALTER TABLE `alertnotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alert_id` (`alert_id`),
  ADD UNIQUE KEY `alert_title` (`alert_title`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_name` (`branch_name`),
  ADD UNIQUE KEY `branch_id` (`branch_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_id` (`company_id`),
  ADD UNIQUE KEY `company_name` (`company_name`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `employee_username` (`employee_username`),
  ADD UNIQUE KEY `employee_contact` (`employee_contact`);

--
-- Indexes for table `importleads`
--
ALTER TABLE `importleads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lead_id` (`lead_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `message_id` (`message_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_id` (`permission_id`),
  ADD UNIQUE KEY `permission_options` (`permission_options`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indexes for table `references`
--
ALTER TABLE `references`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_id` (`reference_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting` (`setting`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `state_name` (`state_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `alertnotes`
--
ALTER TABLE `alertnotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `importleads`
--
ALTER TABLE `importleads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `references`
--
ALTER TABLE `references`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
