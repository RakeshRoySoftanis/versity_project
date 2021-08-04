-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 04, 2021 at 12:24 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 7.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `versity_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint(20) NOT NULL,
  `name` char(255) NOT NULL,
  `email` char(255) NOT NULL,
  `password` char(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `phone` char(255) NOT NULL,
  `assignedAccounts` text DEFAULT NULL,
  `admin_token` text DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `name`, `email`, `password`, `remember_token`, `phone`, `assignedAccounts`, `admin_token`, `expires_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Dev Test Admin', 'devtest@catalystconnect.com', '$2y$10$48AcqgALF7vIeN/08MHfKumGl9IyEu33Tuysm5ufftbMTYf7.L5wm', NULL, '2999871070', '[\"3461186000001236003\",\"1632952000003428539\"]', NULL, NULL, '2020-01-20 13:54:20', '2020-01-27 11:43:51', NULL),
(2, 'Dev Test Admin 2', 'devtestRex@catalystconnect.com', '$2y$10$0S1GYXPcmWNLiEyPEH.gFexiDbSREqcGDBphJ7t2.eAbb9yx2qJ06', NULL, '2999871070', '[\"1632952000011090164\",\"1632952000011115002\"]', NULL, NULL, '2020-01-20 13:54:20', '2021-07-14 22:15:02', NULL),
(24, 'Rajib test Roy', 'rakesh@gmail.com', '$2y$10$pBT7kO5xcs/YfGPBdV1mBOvoIiOU7qecCrHz2bIfwcNKy9ERyhIkm', NULL, '01638127876', NULL, NULL, NULL, '2021-08-02 15:28:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_token`
--

CREATE TABLE `admin_user_token` (
  `id` int(11) NOT NULL,
  `token` text NOT NULL,
  `admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin_user_token`
--

INSERT INTO `admin_user_token` (`id`, `token`, `admin_id`, `created_at`, `updated_at`, `expires_at`) VALUES
(1, '$2y$10$5WtK1lX.zQBI3bsmu3eWIeHtPUEMgBdcyIu2LjRRlmhCm873Qym3K', 2, '2021-07-15 04:01:45', '2021-07-15 04:01:45', '2021-07-20 00:00:00'),
(2, '$2y$10$hEJuhwRp0MZz2R8OZrsvIuW4IxoRMWa0G9LWTydp33DB1FQ1fNs3e', 23, '2021-07-26 04:33:24', '2021-07-26 04:33:24', '2021-07-27 00:00:00'),
(3, '$2y$10$eLS5St/FPUPHFR58kvjPveHRW364ZDceC49NVvb/H1ixhTWMLbvDG', 24, '2021-08-02 15:30:47', '2021-08-02 15:30:47', '2021-08-03 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `google_api_setting`
--

CREATE TABLE `google_api_setting` (
  `id` int(11) NOT NULL,
  `api_key` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `google_api_setting`
--

INSERT INTO `google_api_setting` (`id`, `api_key`) VALUES
(1, 'AIzaSyBKRHVbJPnhvyJADCtRLY6l2DnXpyjO3X4');

-- --------------------------------------------------------

--
-- Table structure for table `google_fields_setting`
--

CREATE TABLE `google_fields_setting` (
  `id` int(11) NOT NULL,
  `module` text DEFAULT NULL,
  `google_fields` text DEFAULT NULL,
  `zoho_fields` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `google_fields_setting`
--

INSERT INTO `google_fields_setting` (`id`, `module`, `google_fields`, `zoho_fields`) VALUES
(1, 'Accounts', 'Street', 'Billing_Street'),
(2, 'Accounts', 'City', 'Billing_City'),
(3, 'Accounts', 'State', 'Billing_State'),
(4, 'Accounts', 'Zip_Code', 'Billing_Code'),
(5, 'Accounts', 'County', 'Billing_State'),
(6, 'Accounts', 'Country', 'Billing_Country');

-- --------------------------------------------------------

--
-- Table structure for table `master`
--

CREATE TABLE `master` (
  `id` int(11) NOT NULL,
  `crm_id` varchar(30) DEFAULT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `fax` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `skype` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master`
--

INSERT INTO `master` (`id`, `crm_id`, `fname`, `lname`, `email`, `password`, `phone`, `fax`, `mobile`, `skype`) VALUES
(5, NULL, 'Master', 'Admin', 'admin', 'c335fbdb0e88e78867d043aae181ee0d', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `master_token`
--

CREATE TABLE `master_token` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `master_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `master_token`
--

INSERT INTO `master_token` (`id`, `token`, `master_id`, `created_at`, `updated_at`, `expires_at`) VALUES
(164, 'tzJyjjphpFsKfntsZVtLUWgbqYUlG3TuoEeAnEvGO7GvCBjSvW7WlkMravlaxtuRQpRDMaUa2ChoGi4E6m6b1sRM5y9YatyZ0IAPzWxmy046YPkbRUBgmxDfnjyevLm7', 5, '2021-08-04 11:25:16', NULL, '2021-08-05 05:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('amin+con1@catalystconnect.com', '49u3znxQdMuqGSEZAYhmNDT1h029UHlm7PnAOBzaMPL0s8vSAnLyRCJpZXgw', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(13) NOT NULL,
  `contact_id` text DEFAULT NULL,
  `logo` text DEFAULT NULL,
  `minilogo` text DEFAULT NULL,
  `logo_default` tinyint(2) DEFAULT 1,
  `head` text DEFAULT NULL,
  `footer` text DEFAULT NULL,
  `nav` text DEFAULT NULL,
  `font` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `fax` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `website` text DEFAULT NULL,
  `youtubelink` text DEFAULT NULL,
  `client_dashboard` text DEFAULT NULL,
  `street` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `state` text DEFAULT NULL,
  `zip` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `heading` text DEFAULT NULL,
  `deafult` int(3) NOT NULL DEFAULT 0,
  `quick_title` text NOT NULL,
  `quick_link` text NOT NULL,
  `want_crm` varchar(10) DEFAULT NULL,
  `want_books` text DEFAULT NULL,
  `want_zsubscriptions` text DEFAULT NULL,
  `want_zprojects` text DEFAULT NULL,
  `want_zdesks` text DEFAULT NULL,
  `want_zvaults` text DEFAULT NULL,
  `want_inventory` text DEFAULT NULL,
  `want_zworkdrive` text DEFAULT NULL,
  `want_zsign` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `contact_id`, `logo`, `minilogo`, `logo_default`, `head`, `footer`, `nav`, `font`, `name`, `phone`, `fax`, `email`, `website`, `youtubelink`, `client_dashboard`, `street`, `city`, `state`, `zip`, `country`, `heading`, `deafult`, `quick_title`, `quick_link`, `want_crm`, `want_books`, `want_zsubscriptions`, `want_zprojects`, `want_zdesks`, `want_zvaults`, `want_inventory`, `want_zworkdrive`, `want_zsign`) VALUES
(1, '5', 'Catalyst-Connect-Logo-new.png', 'asad.png', 0, 'linear-gradient(45deg, #59a3e8 0%, #646cd2 100%)', 'linear-gradient(45deg, #59a3e8 0%, #646cd2 100%)', '#7e97fc', '#1F2537', 'Solar Catalyst', '454-545-4545', '1245345646', 'solar@test.com', 'https://solar.thecatalystcloud.com', NULL, 'https://www.youtube.com/embed/tVNaiAK9txE', '456 Main St.', 'Phoenix', 'CA', '88888', 'United States', '#646cd2', 0, '[\"Catalyst\",\"Facebook\",null]', '[\"http:\\/\\/catalystconnect.com\",\"http:\\/\\/facebook.com\",null]', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `template_setting`
--

CREATE TABLE `template_setting` (
  `id` int(11) NOT NULL,
  `name` varchar(230) NOT NULL,
  `template_api_name` text NOT NULL,
  `font_size` varchar(230) DEFAULT NULL,
  `font_weight` varchar(230) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `template_setting`
--

INSERT INTO `template_setting` (`id`, `name`, `template_api_name`, `font_size`, `font_weight`, `image_url`, `status`) VALUES
(1, 'Template-1', 'template_one', '14px', 'normal', 'asset_custom/template/demo_image/template-1.jpg', 1),
(2, 'Template-2', 'template_two', '15px', 'normal', 'asset_custom/template/demo_image/template-2.jpg', 0),
(4, 'Template-3', 'template_three', '15px', 'normal', 'asset_custom/template/demo_image/template-3.jpg', 0),
(5, 'Template-4', 'template_four', '40px', 'normal', 'asset_custom/template/demo_image/template-4.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portal_user_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pass_change` tinyint(2) DEFAULT 0,
  `attCount` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `jobsAttch` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `portal_layout_role` int(11) DEFAULT NULL,
  `portal_layout_role_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `contact_id`, `portal_user_name`, `first_name`, `last_name`, `full_name`, `email`, `password`, `photo_url`, `remember_token`, `role`, `status`, `pass_change`, `attCount`, `jobsAttch`, `created_at`, `updated_at`, `portal_layout_role`, `portal_layout_role_name`) VALUES
(1, '1632952000003590005', 'softanis.mahidulislam@gmail.com', 'Developer U', 'Arifin', 'Developer U Arifin', 'softanis.mahidulislam@gmail.com', '$2y$10$5LLXHUJROFFZFcL6A1kGHeUlWfzK4xaBQKjjR05tBIGOvaWTC2C4a', NULL, '', '', 'Active', 0, '0', '0', '2021-02-22 06:32:53', '2021-06-17 22:20:12', 5, 'Test Role'),
(2, '1632952000004365057', 'nzaman@catalystconnect.com', 'Dev', 'Arifin', 'Mr. Dev Arifin', 'nzaman@catalystconnect.com', '$2y$10$t6F8CK4ie8xoA7nJ7ASch.MaWsceIq6FOeUTuAzrKrorqbiyfKs2e', 'asset_custom/images/client/profile/1632952000004365057.png', '', '', 'Active', 0, '0', '0', '2021-02-22 06:32:53', '2021-06-17 22:22:32', 1, 'Solar Administrator'),
(3, '1632952000003428385', 'tony@tonystarkindustries.com', 'Tony', 'Stark', 'Tony Stark', 'tony@tonystarkindustries.com', '$2y$10$.VKbRUPyL1uhrx2tDRO21.qHTBVkW/aEeH71O6CjYWz66/xmPM5LC', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 21:59:51', '2021-06-17 22:20:11', NULL, NULL),
(4, '1632952000008251001', 'amin+am@catalystconnect.com', 'Anna', 'Maria', 'Mr. Anna Maria', 'amin+am@catalystconnect.com', '$2y$10$BelBvQafW1ddD5HMEvOfqeswae.1VpLGGqB6d7T4hXPYgRJmyTlsa', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 21:59:52', '2021-06-17 22:20:12', NULL, NULL),
(5, '1632952000007866001', 'skandpal@catalystconnect.com', 'Stephen', 'Strange', 'Stephen Strange', 'skandpal@catalystconnect.com', '', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 21:59:52', '2021-06-17 22:20:12', NULL, NULL),
(6, '1632952000009649001', 'sumon@gmail.com', 'Sazzad', 'Hossain', 'Mr. Sazzad Hossain', 'sumon@gmail.com', '', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 21:59:52', '2021-06-17 22:20:12', NULL, NULL),
(7, '1632952000010015128', 'amin+jacobs@catalystconnect.com', 'Jacob', 'Stephen', 'Jacob Stephen', 'amin+jacobs@catalystconnect.com', '$2y$10$3cpBndJYh3oi2lZEbUDd/eVaCGcBGZm0lfL2XE2GvaZL2KM1/QFdG', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 21:59:52', '2021-06-17 22:20:12', NULL, NULL),
(8, '1632952000010015228', 'amin+rose@catalystconnect.com', 'Merry', 'Rose', 'Merry Rose', 'amin+rose@catalystconnect.com', '$2y$10$zPwYRtVuQ6wJLBhXtdJf6uS/6L0p8pJ3h9S5cTyb/cVmkpEglHUlO', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 21:59:52', '2021-06-17 22:20:12', NULL, NULL),
(9, '1632952000010015304', 'amin+vinyle@catalystconnect.com', 'Vinyle', 'Mose', 'Mrs. Vinyle Mose', 'amin+vinyle@catalystconnect.com', '$2y$10$L3w1y/gyUGfr3fMTncjdiOM6ZVRgQ2udrAgo0lsd0nu8MBPLlXdGG', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:10', '2021-06-17 22:20:12', NULL, NULL),
(10, '1632952000010015380', 'amin+michel@catalystconnect.com', 'Michel', 'Fridge', 'Michel Fridge', 'amin+michel@catalystconnect.com', '$2y$10$qjyLSGUMPOP1uUeF6tDqQuzUGzzHj9ZH7YKBveVH9exeUBJBFxDhC', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:10', '2021-06-17 22:20:12', NULL, NULL),
(11, '1632952000010015438', 'amin+maria@catalystconnect.com', 'Maria', 'Gura', 'Maria Gura', 'amin+maria@catalystconnect.com', '$2y$10$DTlR/Wj0UfPtDE1GLnWFI.kqXrGKfvPzWqqjhBerXERLkPd6c41ei', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:10', '2021-06-17 22:20:12', NULL, NULL),
(12, '1632952000010015476', 'amin+kevin@catalystconnect.com', 'Kevin', 'Peter', 'Kevin Peter', 'amin+kevin@catalystconnect.com', '$2y$10$2vcZQr2Y/JHdKzfqsSGzJ.NPx4lH3YikEc98d4e0aNGJBbIdhAVOW', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:10', '2021-06-17 22:20:12', NULL, NULL),
(13, '1632952000010015530', 'amin+morpf@catalystconnect.com', 'Morpf', 'Peter', 'Morpf Peter', 'amin+morpf@catalystconnect.com', '$2y$10$4DRoNjXYep7K4ErRpPh38ezR8LKt73t4p2fknmmulcwsALJ9VogmS', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:10', '2021-06-17 22:20:12', NULL, NULL),
(14, '1632952000010015568', 'amin+lieam@catalystconnect.com', 'Lieam', 'Cruse', 'Lieam Cruse', 'amin+lieam@catalystconnect.com', '$2y$10$9qWOEBw4BMwQQZ/ymN2Te.ZND6Pkxaq831jBwfwekjtNT1vD14uK2', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:10', '2021-06-17 22:20:12', NULL, NULL),
(15, '1632952000010904012', 'amin+C15JUN21-1@catalystconnect.com', 'CC React Test', 'Contact 15JUN21-1', 'CC React Test Contact 15JUN21-1', 'amin+C15JUN21-1@catalystconnect.com', '', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:11', '2021-06-17 22:20:12', NULL, NULL),
(16, '1632952000010896003', 'amin+C15JUN21-2@catalystconnect.com', 'CC React Test', 'Contact 15JUN21-2', 'CC React Test Contact 15JUN21-2', 'amin+C15JUN21-2@catalystconnect.com', '', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:11', '2021-06-17 22:20:12', NULL, NULL),
(17, '1632952000010514017', 'amin+rs-con1@catalystconnect.com', 'React Shell', 'Contact 1 1', 'Mr. React Shell Contact 1 1', 'amin+rs-con1@catalystconnect.com', '$2y$10$Yp7JjbI3yWpHx0lRS8S6/u7QVtz.OtBjnp50eXSUPXqyeo.XD/.Oy', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:11', '2021-06-17 22:20:13', NULL, NULL),
(18, '1632952000010514089', 'amin+rs-con2@catalystconnect.com', 'React Shell', 'Contact 2', 'React Shell Contact 2', 'amin+rs-con2@catalystconnect.com', '$2y$10$p8E3zuH3EEfxySxH5Yj/qu7kckeyJDgD9KaB6t2ya/Fp1E9Rcekni', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:11', '2021-06-17 22:20:13', NULL, NULL),
(19, '1632952000008131001', 'tamin1@catalystconnect.com', 'Dev Mi', 'Tamim 1', 'Dev Mi Tamim 1', 'tamin1@catalystconnect.com', '', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:11', '2021-06-17 22:20:13', NULL, NULL),
(20, '1632952000010015266', 'amin+rose_@catalystconnect.com', 'Jack', 'Black', 'Mr. Jack Black', 'amin+rose_@catalystconnect.com', '$2y$10$9v563JFAOvZUngGzgu0QlexITQ5GrpD9wC1wcMYN0HeoxksAyjhM2', NULL, '', '', 'Active', 0, '0', '0', '2021-06-17 22:15:11', '2021-06-17 22:20:13', NULL, NULL),
(21, '1632952000011104026', 'amin+con1@catalystconnect.com', 'CC Test', 'Contact 1', 'Mr. CC Test Contact 1', 'amin+con1@catalystconnect.com', '', NULL, '', '', 'Active', 0, '0', '0', '2021-06-22 22:15:10', '2021-06-23 04:22:32', NULL, NULL),
(22, '1632952000011137601', 'amin+con5@catalystconnect.com', 'CC Test', 'Contact 5', 'Mr. CC Test Contact 5', 'amin+con5@catalystconnect.com', '', NULL, '', '', 'Active', 0, '0', '0', '2021-06-22 22:15:10', '2021-06-23 04:22:32', NULL, NULL),
(23, '1632952000011123001', 'amin+peter@catalystconnect.com', 'Peter', 'Parker', 'Mr. Peter Parker', 'amin+peter@catalystconnect.com', '$2y$10$rGta6SVjcWwmDz3f5P7zAOifXoxD0yPoxHAqFYKP86FQpuJ4JXcXW', NULL, '', '', 'Active', 0, '0', '0', '2021-06-22 22:15:10', '2021-07-08 23:12:23', 1, 'Solar Administrator');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_user_token`
--
ALTER TABLE `admin_user_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `google_api_setting`
--
ALTER TABLE `google_api_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `google_fields_setting`
--
ALTER TABLE `google_fields_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master`
--
ALTER TABLE `master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `master_token`
--
ALTER TABLE `master_token`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_setting`
--
ALTER TABLE `template_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `admin_user_token`
--
ALTER TABLE `admin_user_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `google_api_setting`
--
ALTER TABLE `google_api_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `google_fields_setting`
--
ALTER TABLE `google_fields_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `master`
--
ALTER TABLE `master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `master_token`
--
ALTER TABLE `master_token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(13) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `template_setting`
--
ALTER TABLE `template_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
