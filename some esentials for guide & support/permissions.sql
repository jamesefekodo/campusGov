-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 31, 2026 at 02:46 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `campus_gov`
--

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `module` varchar(255) DEFAULT NULL,
  `advanced_management` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `slug`, `name`, `module`, `advanced_management`, `created_at`, `updated_at`) VALUES
(1, 'verify_users', 'Verify Members & Make Profile Changes', 'Users', 0, NULL, NULL),
(2, 'media_platform.read', 'Read Media Platforms', 'Media Platforms', 1, NULL, NULL),
(3, 'media_platform.create', 'Create Media Platforms', 'Media Platforms', 1, NULL, NULL),
(4, 'media_platform.update', 'Update Media Platforms', 'Media Platforms', 1, NULL, NULL),
(5, 'media_platform.delete', 'Delete Media Platforms', 'Media Platforms', 1, NULL, NULL),
(6, 'misconducts.read', 'View Misconducts', 'Misconducts', 0, NULL, NULL),
(7, 'misconducts.create', 'Create Misconducts', 'Misconducts', 0, NULL, NULL),
(8, 'misconducts.update', 'Update Misconducts', 'Misconducts', 0, NULL, NULL),
(9, 'misconducts.delete', 'Delete Misconducts', 'Misconducts', 0, NULL, NULL),
(10, 'transactions.read', 'View Payment Transactions', 'Payment Transactions', 0, NULL, NULL),
(11, 'transactions.update', 'Update Payment Transactions', 'Payment Transactions', 0, NULL, NULL),
(12, 'transactions.create', 'Create Payment Transactions', 'Payment Transactions', 0, NULL, NULL),
(13, 'transactions.delete', 'Delete Payment Transactions', 'Payment Transactions', 0, NULL, NULL),
(14, 'dues.read', 'View Dues', 'Dues', 0, NULL, NULL),
(15, 'dues.create', 'Create Dues', 'Dues', 0, NULL, NULL),
(16, 'dues.update', 'Update Dues', 'Dues', 0, NULL, NULL),
(17, 'dues.delete', 'Delete Dues', 'Dues', 0, NULL, NULL),
(18, 'manage_payment_settings', 'Manage Payment Settings', 'Payment Settings', 0, NULL, NULL),
(19, 'faculty.read', 'View Faculty', 'Faculty', 0, NULL, NULL),
(20, 'faculty.create', 'Create Faculty', 'Faculty', 0, NULL, NULL),
(21, 'faculty.update', 'Update Faculty', 'Faculty', 0, NULL, NULL),
(22, 'faculty.delete', 'Delete Faculty', 'Faculty', 0, NULL, NULL),
(23, 'department.read', 'Read Department', 'Department', 0, NULL, NULL),
(24, 'department.create', 'Create Department', 'Department', 0, NULL, NULL),
(25, 'department.update', 'Update Department', 'Department', 0, NULL, NULL),
(26, 'department.delete', 'Delete Department', 'Department', 0, NULL, NULL),
(27, 'program.read', 'View Program', 'Program', 0, NULL, NULL),
(28, 'program.create', 'Create Program', 'Program', 0, NULL, NULL),
(29, 'program.update', 'Update Program', 'Program', 0, NULL, NULL),
(30, 'program.delete', 'Delete Program', 'Program', 0, NULL, NULL),
(31, 'basic_system_config.read', 'View Basic System Config', 'Basic System Config', 0, NULL, NULL),
(32, 'basic_system_config.create', 'Create Basic System Config', 'Basic System Config', 0, NULL, NULL),
(33, 'basic_system_config.update', 'Update Basic System Config', 'Basic System Config', 0, NULL, NULL),
(34, 'basic_system_config.delete', 'Delete Basic System Config', 'Basic System Config', 0, NULL, NULL),
(35, 'roles.read', 'Read Roles', 'Roles', 0, NULL, NULL),
(36, 'roles.create', 'Create Roles', 'Roles', 0, NULL, NULL),
(37, 'roles.update', 'Update Roles', 'Roles', 0, NULL, NULL),
(38, 'roles.delete', 'Delete Roles', 'Roles', 0, NULL, NULL),
(39, 'policy.read', 'View Policy', 'Policy', 0, NULL, NULL),
(40, 'policy.create', 'Create Policy', 'Policy', 0, NULL, NULL),
(41, 'policy.update', 'Update Policy', 'Policy', 0, NULL, NULL),
(42, 'policy.delete', 'Delete Policy', 'Policy', 0, NULL, NULL),
(43, 'elections.read', 'Read Elections', 'Elections', 0, NULL, NULL),
(44, 'elections.create', 'Create Elections', 'Elections', 0, NULL, NULL),
(45, 'elections.update', 'Update Elections', 'Elections', 0, NULL, NULL),
(46, 'elections.delete', 'Delete Elections', 'Elections', 0, NULL, NULL),
(47, 'elections.approve', 'Approve Elections', 'Elections', 0, '2026-08-15 17:28:53', '2026-08-15 17:28:53'),
(48, 'elections.results', 'Read Election Results', 'Elections', 0, '2026-08-24 13:21:25', '2026-08-24 13:21:25'),
(49, 'voters.read', 'Read Voters', 'Elections', 0, NULL, NULL),
(50, 'financial_claim.read', 'Read Financial Claim', 'Financial Claims', 0, NULL, NULL),
(51, 'financial_claim.create', 'Create Financial Claims', 'Financial Claims', 0, NULL, NULL),
(52, 'financial_claim.update', 'Update Financial Claim', 'Financial Claims', 0, NULL, NULL),
(53, 'financial_claim.delete', 'Delete Financial Claim', 'Financial Claims', 0, NULL, NULL),
(54, 'invoice.read', 'View Invoices', 'Invoice', 0, NULL, NULL),
(55, 'invoice.create', 'Create Invoice', 'Invoice', 0, NULL, NULL),
(56, 'invoice.update', 'Update Invoice', 'Invoice', 0, NULL, NULL),
(57, 'invoice.delete', 'Delete Invoice', 'Invoice', 0, NULL, NULL),
(58, 'donation.create', 'Create Donation', 'Donations', 0, NULL, NULL),
(59, 'donation.update', 'Update Donation', 'Donations', 0, NULL, NULL),
(60, 'donation.read', 'Read Donations', 'Donations', 0, NULL, NULL),
(61, 'donation.delete', 'Delete Donation', 'Donations', 0, NULL, NULL),
(62, 'roles.assign', 'Assign Roles/Permissions', 'Roles', 0, NULL, NULL),
(63, 'member_profile.read', 'View Member Profile', 'Users', 0, NULL, NULL),
(64, 'manage_admin', 'Manage Admin Profiles', 'Users', 0, NULL, NULL),
(65, 'admin.read', 'View Admin Profiles', 'Users', 0, NULL, NULL),
(66, 'see_account_balance', 'See Account Balance', 'Payment Transactions', 0, NULL, NULL),
(67, 'manage_seo', 'Manage SEO', 'SEO', 0, NULL, NULL),
(68, 'manage_whatsapp_support', 'Manage Whatsapp Support', 'Whatsapp Support', 0, NULL, NULL),
(73, 'media_publication.create', 'Create Media Publications', 'Media Publications', 0, NULL, NULL),
(74, 'media_publication.update', 'Update/Approve Media Publications', 'Media Publications', 0, NULL, NULL),
(75, 'media_publication.read', 'View Media Publications', 'Media Publications', 0, NULL, NULL),
(76, 'media_publication.delete', 'Delete Media Publications', 'Media Publications', 0, NULL, NULL),
(77, 'media_publication.manage_tags', 'Manage Tags', 'Media Publications', 0, NULL, NULL),
(78, 'past_questions.read', 'View Past Questions', 'Past Questions', 0, NULL, NULL),
(79, 'past_questions.create', 'Create Past Questions', 'Past Questions', 0, NULL, NULL),
(80, 'past_questions.update', 'Update Past Questions', 'Past Questions', 0, NULL, NULL),
(81, 'past_questions.delete', 'Delete Past Questions', 'Past Questions', 0, NULL, NULL),
(82, 'recommended_book.create', 'Create Recommended Book', 'Recommended Book', 0, NULL, NULL),
(83, 'recommended_book.update', 'Update Recommended Book', 'Recommended Book', 0, NULL, NULL),
(84, 'recommended_book.read', 'View Recommended Book', 'Recommended Book', 0, NULL, NULL),
(85, 'recommended_book.delete', 'Delete Recommended Book', 'Recommended Book', 0, NULL, NULL),
(86, 'recommended_book.manage_tags', 'Manage Tags', 'Recommended Book', 0, NULL, NULL),
(87, 'recommended_course.read', 'View Recommended Courses', 'Recommended Courses', 0, NULL, NULL),
(88, 'recommended_course.update', 'Update Recommended Courses', 'Recommended Courses', 0, NULL, NULL),
(89, '		\r\nrecommended_course.create\r\n', 'Create Recommended Courses', 'Recommended Courses', 0, NULL, NULL),
(90, 'recommended_course.delete', 'Delete Recommended Courses', 'Recommended Courses', 0, NULL, NULL),
(91, 'recommended_course.manage_tags', 'Manage Course Tags', 'Recommended Courses', 0, NULL, NULL),
(92, 'archive.create', 'Create Archive Record', 'Archive', 0, NULL, NULL),
(93, 'archive.read', 'See Archive Records', 'Archive', 0, NULL, NULL),
(94, 'archive.update', 'Update Archive Records', 'Archive', 0, NULL, NULL),
(95, 'archive.delete', 'Delete Archive Records', 'Archive', 0, NULL, NULL),
(96, 'industry_affiliate.create', 'Create Records', 'Ecosystem & Industry Affiliates', 0, NULL, NULL),
(97, 'industry_affiliate.update', 'Update Records', 'Ecosystem & Industry Affiliates', 0, NULL, NULL),
(98, 'industry_affiliate.read', 'View Records', 'Ecosystem & Industry Affiliates', 0, NULL, NULL),
(99, 'industry_affiliate.delete', 'Delete records', 'Ecosystem & Industry Affiliates', 0, NULL, NULL),
(100, 'archive.manage_tags', 'Manage Tags', 'Archive', 0, NULL, NULL),
(101, 'leadership.manage', 'Manage Leadership Records', 'Leadership Records', 0, NULL, NULL),
(102, 'themes.manage', 'Manage themes', 'Themes', 0, NULL, NULL),
(103, 'events.create', 'Create Events', 'Events', 0, NULL, NULL),
(104, 'events.read', 'View Events', 'Events', 0, NULL, NULL),
(105, 'events.update', 'Update Events', 'Events', 0, NULL, NULL),
(106, 'events.delete', 'Delete Events', 'Events', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
