-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping structure for table hub-mangament.workspaces
CREATE TABLE IF NOT EXISTS `workspaces` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_account_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_payment_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_payment_supported` tinyint(1) NOT NULL DEFAULT '0',
  `features` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.workspaces: ~7 rows (approximately)
INSERT INTO `workspaces` (`id`, `name`, `location`, `bank_account_number`, `mobile_payment_number`, `bank_payment_supported`, `features`, `description`, `logo`, `created_at`, `updated_at`) VALUES
	(1, 'GhayaTech', 'Al Remal', '123456', '123456', 1, NULL, 'Description Of GhayaTech Workspace', NULL, '2025-05-11 07:33:04', '2025-05-12 07:52:51'),
	(2, 'مساحتك', 'الرمال', NULL, NULL, 1, NULL, 'مفترق فلسطين', NULL, '2025-05-11 07:42:22', '2025-05-11 09:13:33'),
	(3, 'نيو', 'الرمال', NULL, NULL, 0, NULL, 'كاظم', NULL, '2025-05-11 07:54:29', '2025-05-11 09:13:16'),
	(4, 'test Workspace', 'الرمال', NULL, NULL, 0, NULL, 'الرمال', NULL, '2025-05-11 09:12:23', '2025-05-11 09:12:57'),
	(5, 'omar', 'التايلاندي', '123456', '0123654798', 0, NULL, 'خدمات ممتازة جدا', NULL, '2025-05-12 08:46:00', '2025-05-12 08:46:00'),
	(6, 'الهندي', 'الجندي', '0123456', '0123456789', 1, '[{"value": "انترنت عالى السرعة"}, {"value": "خدمات رائعة"}]', 'الرمال الجندي المجهول', NULL, '2025-05-12 08:54:49', '2025-05-12 08:58:03'),
	(7, 'test Workspace2', 'الرمال', NULL, NULL, 0, '[{"value": "qwe"}, {"value": "qweggy"}, {"value": "ds"}]', 'qweasd', NULL, '2025-05-12 09:10:01', '2025-05-12 09:10:01');



-- Dumping structure for table hub-mangament.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verification_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `workspace_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_phone_unique` (`phone`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `users_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.users: ~8 rows (approximately)
INSERT INTO `users` (`id`, `name`, `phone`, `specialty`, `email`, `email_verified_at`, `phone_verified_at`, `phone_verification_code`, `password`, `profile_image`, `role`, `remember_token`, `created_at`, `updated_at`, `workspace_id`) VALUES
	(1, 'admin', NULL, NULL, 'admin@gmail.com', NULL, NULL, NULL, '$2y$12$QF8O.4HFEntcdJJmMr48/uRCpjNPwIStix/f51PRk8f13k7f.70xK', NULL, 'admin', NULL, '2025-05-10 09:49:01', '2025-05-10 09:49:01', NULL),
	(2, 'ahmed', '0591234567', 'IT', 'ahmed@gmail.com', NULL, NULL, NULL, '$2y$12$UrWpRWYM.I8h3mHfLLI1Mupa/DXzGv.HkGeL7CHPyHp6tIQb768bm', NULL, 'secretary', 'z5PF8Q4bxSVpKMy6BnX8VL2WsCFr85jiAijGNbHwN48UL8IcpOZBeJR8etNQ', '2025-05-11 06:36:03', '2025-05-11 07:37:49', 1),
	(3, 'احمد الخالدي', '0597896541', 'IT', 'ahmedkh@gmail.com', NULL, NULL, NULL, '$2y$12$ckoQQizYZIoZB8Xknfv7muXusM.YIYVnlF.n3abuHqGTFqRZjbrWK', NULL, 'secretary', NULL, '2025-05-11 07:43:45', '2025-05-11 07:43:45', 2),
	(4, 'سمر', '0597412365', NULL, 'smar@gmail.com', NULL, NULL, NULL, '$2y$12$A8U9N4pkdGIizrK7wTIlsujrWlhM8XUlXs4VumnJDCWhHiK.OWvCS', NULL, 'secretary', NULL, '2025-05-11 07:55:13', '2025-05-11 07:55:13', 3),
	(5, 'Updated User', '123122123', 'Fullstack Developer', NULL, NULL, NULL, NULL, '$2y$12$uLiHwdYBuq4Wdw29k.9PjesDF1zuh7QN5oUWjQNEuIydBxkMdGQTy', NULL, 'user', NULL, '2025-05-11 09:36:31', '2025-05-17 09:47:53', NULL),
	(6, 'tawfeeq', '0598999999', 'Developer', NULL, NULL, '2025-05-12 06:49:46', NULL, '$2y$12$uaVNCg2lJ4LT3Uzyf7kzu.Vam9kVtbb.3eTKW23Qvdnopy930TjVS', NULL, 'user', NULL, '2025-05-12 06:48:55', '2025-05-12 06:49:46', NULL),
	(7, 'Updated User', '123123123', 'Fullstack Developer', NULL, NULL, '2025-05-12 06:55:53', NULL, '$2y$12$gwoQUYxGcF.hFaeAGFU/DuXLqEnTdj6Qvt0JCG7.e1kyS2Gw6k5lm', 'uploads/profile_images/1747483620_profile.png', 'user', NULL, '2025-05-12 06:55:18', '2025-05-17 09:10:52', NULL),
	(8, 'Updated User', '05989999922229', 'Fullstack Developer', NULL, NULL, '2025-05-17 09:43:33', NULL, '$2y$12$h9e1R7NNzXOKU90cfjGSY..xzbv38k2lygNkQbDZ.EGVWkHj2TbSG', 'uploads/profile_images/8/1747568014.png', 'user', NULL, '2025-05-17 09:43:19', '2025-05-18 08:33:34', NULL);

-- Dumping structure for table hub-mangament.packages
CREATE TABLE IF NOT EXISTS `packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `duration` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `packages_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `packages_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.packages: ~6 rows (approximately)
INSERT INTO `packages` (`id`, `workspace_id`, `name`, `price`, `duration`, `created_at`, `updated_at`) VALUES
	(1, 1, 'يومي', 20.00, 7, '2025-05-11 07:59:52', '2025-05-11 07:59:52'),
	(2, 1, 'ساعة', 5.00, 1, '2025-05-11 08:00:07', '2025-05-11 08:00:07'),
	(3, 1, 'اسبوع', 150.00, 42, '2025-05-11 08:00:55', '2025-05-11 08:00:55'),
	(4, 2, 'يوم', 20.00, 7, '2025-05-11 08:01:25', '2025-05-11 08:01:25'),
	(5, 2, 'يومي', 15.00, 7, '2025-05-11 08:01:49', '2025-05-11 08:01:49'),
	(6, 3, 'يومي', 10.00, 7, '2025-05-11 08:02:00', '2025-05-11 08:02:00'),
	(7, 5, 'يومي', 10.00, 1, '2025-05-14 10:22:22', '2025-05-14 10:22:22');

-- Dumping structure for table hub-mangament.bookings
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `workspace_id` bigint unsigned NOT NULL,
  `package_id` bigint unsigned NOT NULL,
  `seat_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wifi_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wifi_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_at` timestamp NULL DEFAULT NULL,
  `end_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bookings_user_id_foreign` (`user_id`),
  KEY `bookings_workspace_id_foreign` (`workspace_id`),
  KEY `bookings_package_id_foreign` (`package_id`),
  CONSTRAINT `bookings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.bookings: ~11 rows (approximately)
INSERT INTO `bookings` (`id`, `user_id`, `workspace_id`, `package_id`, `seat_number`, `wifi_username`, `wifi_password`, `start_at`, `end_at`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 1, NULL, NULL, NULL, '2025-05-11 12:09:00', '2025-05-11 12:09:05', 'confirmed', '2025-05-11 09:09:16', '2025-05-11 09:09:16'),
	(2, 5, 1, 1, NULL, NULL, NULL, '2025-05-15 07:00:00', '2025-05-15 07:00:00', 'confirmed', '2025-05-11 09:44:19', '2025-05-11 09:44:19'),
	(3, 7, 1, 1, NULL, NULL, NULL, '2025-05-15 07:00:00', '2025-05-15 07:00:00', 'pending', '2025-05-13 12:00:28', '2025-05-13 12:00:28'),
	(4, 7, 1, 1, NULL, NULL, NULL, '2025-05-15 07:00:00', '2025-05-15 07:00:00', 'pending', '2025-05-14 09:27:44', '2025-05-14 09:27:44'),
	(5, 7, 1, 2, NULL, NULL, NULL, '2025-10-10 09:00:00', '2025-10-10 12:00:00', 'pending', '2025-05-14 09:57:41', '2025-05-14 09:57:41'),
	(6, 7, 5, 1, NULL, NULL, NULL, '2025-10-09 21:00:00', '2025-10-10 21:00:00', 'confirmed', '2025-05-14 10:00:54', '2025-05-14 10:00:54'),
	(7, 7, 1, 1, NULL, NULL, NULL, '2025-10-09 21:00:00', '2025-10-10 21:00:00', 'confirmed', '2025-05-14 10:06:05', '2025-05-14 10:06:05'),
	(8, 7, 1, 2, NULL, NULL, NULL, '2025-10-10 09:00:00', '2025-10-10 12:00:00', 'pending', '2025-05-17 05:28:53', '2025-05-17 05:28:53'),
	(9, 7, 1, 1, NULL, NULL, NULL, '2025-10-09 21:00:00', '2025-10-10 21:00:00', 'pending', '2025-05-17 05:29:29', '2025-05-17 05:29:29'),
	(10, 8, 1, 1, NULL, NULL, NULL, '2025-10-09 21:00:00', '2025-10-10 21:00:00', 'pending', '2025-05-17 10:21:04', '2025-05-17 10:21:04'),
	(11, 8, 1, 2, NULL, NULL, NULL, '2025-10-10 09:00:00', '2025-10-10 12:00:00', 'confirmed', '2025-05-17 10:21:42', '2025-05-17 10:21:42'),
	(12, 8, 1, 2, NULL, NULL, NULL, '2025-10-10 09:00:00', '2025-10-10 12:00:00', 'confirmed', '2025-05-18 08:56:21', '2025-05-18 08:56:21'),
	(13, 8, 1, 1, NULL, NULL, NULL, '2025-10-09 21:00:00', '2025-10-10 21:00:00', 'pending', '2025-05-18 08:56:42', '2025-05-18 08:56:42'),
	(14, 8, 1, 1, NULL, NULL, NULL, '2025-10-09 21:00:00', '2025-10-10 21:00:00', 'pending', '2025-05-18 08:56:44', '2025-05-18 08:56:44'),
	(15, 8, 1, 1, NULL, NULL, NULL, '2025-10-09 21:00:00', '2025-10-10 21:00:00', 'pending', '2025-05-18 08:56:47', '2025-05-18 08:56:47');

-- Dumping structure for table hub-mangament.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.cache: ~0 rows (approximately)

-- Dumping structure for table hub-mangament.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.cache_locks: ~0 rows (approximately)

-- Dumping structure for table hub-mangament.conversations
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `secretary_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_user_id_foreign` (`user_id`),
  KEY `conversations_secretary_id_foreign` (`secretary_id`),
  CONSTRAINT `conversations_secretary_id_foreign` FOREIGN KEY (`secretary_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.conversations: ~5 rows (approximately)
INSERT INTO `conversations` (`id`, `user_id`, `secretary_id`, `created_at`, `updated_at`) VALUES
	(1, 5, 2, '2025-05-11 09:57:39', '2025-05-11 09:57:39'),
	(2, 5, 2, '2025-05-11 10:44:46', '2025-05-11 10:44:46'),
	(4, 8, 2, '2025-05-18 06:27:04', '2025-05-18 06:27:04'),
	(6, 8, 2, '2025-05-18 06:33:33', '2025-05-18 06:33:33'),
	(7, 8, 2, '2025-05-18 09:12:34', '2025-05-18 09:12:34');

-- Dumping structure for table hub-mangament.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table hub-mangament.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.jobs: ~0 rows (approximately)

-- Dumping structure for table hub-mangament.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.job_batches: ~0 rows (approximately)

-- Dumping structure for table hub-mangament.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint unsigned NOT NULL,
  `sender_id` bigint unsigned NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.messages: ~12 rows (approximately)
INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `body`, `attachment`, `created_at`, `updated_at`) VALUES
	(1, 1, 5, 'asd', NULL, '2025-05-11 10:17:24', '2025-05-11 10:17:24'),
	(2, 1, 5, 'asd', NULL, '2025-05-11 10:28:23', '2025-05-11 10:28:23'),
	(3, 1, 5, 'asd', 'uploads/1746970250_branch_building_middle.jpg', '2025-05-11 10:30:50', '2025-05-11 10:30:50'),
	(4, 1, 5, 'asd', NULL, '2025-05-11 10:42:01', '2025-05-11 10:42:01'),
	(5, 1, 5, 'asd', 'uploads/1746970926_branch_building_middle.jpg', '2025-05-11 10:42:06', '2025-05-11 10:42:06'),
	(6, 1, 5, 'asd', 'uploads/1746971040_branch_building_middle.jpg', '2025-05-11 10:44:00', '2025-05-11 10:44:00'),
	(7, 1, 5, 'asd', 'uploads/1746971054_branch_building_middle.jpg', '2025-05-11 10:44:14', '2025-05-11 10:44:14'),
	(9, 6, 8, 'asd', 'uploads1747562044.png', '2025-05-18 06:54:04', '2025-05-18 06:54:04'),
	(10, 6, 8, 'asd', 'uploads/1747562103.png', '2025-05-18 06:55:03', '2025-05-18 06:55:03'),
	(11, 6, 8, 'asd', 'uploads/6/8//1747562329.png', '2025-05-18 06:58:49', '2025-05-18 06:58:49'),
	(12, 6, 8, 'asd', 'uploads/6/8/1747562352.png', '2025-05-18 06:59:12', '2025-05-18 06:59:12'),
	(13, 6, 8, 'asd', 'uploads/6/8/1747562412.png', '2025-05-18 07:00:12', '2025-05-18 07:00:12'),
	(14, 6, 8, 'asd', 'uploads/6/8/1747570556.png', '2025-05-18 09:15:56', '2025-05-18 09:15:56');

-- Dumping structure for table hub-mangament.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.migrations: ~23 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2025_05_10_084004_create_personal_access_tokens_table', 1),
	(5, '2025_05_10_103831_create_workspaces_table', 1),
	(6, '2025_05_10_104210_create_packages_table', 1),
	(7, '2025_05_10_104920_create_bookings_table', 1),
	(8, '2025_05_10_105357_create_service_requests_table', 1),
	(9, '2025_05_10_110158_create_conversations_table', 1),
	(10, '2025_05_10_110211_create_messages_table', 1),
	(11, '2025_05_11_100534_add_workspace_id_to_users_table', 2),
	(12, '2025_05_11_114613_change_payment_method_to_json_in_workspaces_table', 3),
	(13, '2025_05_12_093755_add_otp_fields_to_users_table', 4),
	(14, '2025_05_12_101032_add_seat_and_wifi_to_bookings_table', 5),
	(15, '2025_05_12_104550_add_financial_fields_to_workspaces_table', 6),
	(16, '2025_05_12_111500_update_payment_field_in_workspaces_table', 7),
	(17, '2025_05_12_112644_remove_payment_fields_from_bookings_table', 8),
	(18, '2025_05_12_114954_add_features_to_workspaces_table', 9),
	(19, '2025_05_14_081331_create_workspace_images_table', 10),
	(20, '2025_05_17_092032_create_services_table', 11),
	(21, '2025_05_17_101422_make_booking_id_nullable_on_service_requests', 12),
	(22, '2025_05_17_114312_add_profile_image_to_users_table', 13),
	(23, '2025_05_17_114617_create_settings_table', 14),
	(24, '2025_05_18_141032_create_notifications_table', 15);

-- Dumping structure for table hub-mangament.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.notifications: ~1 rows (approximately)
INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
	('6b06f6b0-100c-4953-89a6-75ec38b727ba', 'App\\Notifications\\ServiceRequestStatusUpdated', 'App\\Models\\User', 8, '{"message":"\\u062a\\u0645 \\u062a\\u062d\\u062f\\u064a\\u062b \\u062d\\u0627\\u0644\\u0629 \\u0637\\u0644\\u0628\\u0643 \\u0625\\u0644\\u0649: \\u062c\\u0627\\u0631\\u064d \\u0627\\u0644\\u062a\\u0646\\u0641\\u064a\\u0630","request_id":12}', NULL, '2025-05-18 11:11:07', '2025-05-18 11:11:07'),
	('6e8766f7-0460-4f07-9211-3dc3fdea0cb1', 'App\\Notifications\\ServiceRequestStatusUpdated', 'App\\Models\\User', 8, '{"message":"\\u062a\\u0645 \\u062a\\u062d\\u062f\\u064a\\u062b \\u062d\\u0627\\u0644\\u0629 \\u0637\\u0644\\u0628\\u0643 \\u0625\\u0644\\u0649: \\u0645\\u0631\\u0641\\u0648\\u0636","request_id":13}', NULL, '2025-05-18 11:12:16', '2025-05-18 11:12:16');


-- Dumping structure for table hub-mangament.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table hub-mangament.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.personal_access_tokens: ~7 rows (approximately)
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 5, 'api_token', '6067c5553adaf11e03b49322453ae496af6d042bc323715c6dc978fd09d8d3eb', '["*"]', '2025-05-13 09:45:58', NULL, '2025-05-11 09:37:06', '2025-05-13 09:45:58'),
	(2, 'App\\Models\\User', 5, 'api_token', '1def626226b24833b5828b1e7d2aaf9a398af858039ada42c655d2cf735d4a7a', '["*"]', '2025-05-13 09:47:18', NULL, '2025-05-13 09:46:05', '2025-05-13 09:47:18'),
	(3, 'App\\Models\\User', 7, 'api_token', '94980633463f4054c25b2982aa77e2a870e5955aa85e826046d08d2b48943100', '["*"]', '2025-05-17 07:02:20', NULL, '2025-05-13 09:47:36', '2025-05-17 07:02:20'),
	(5, 'App\\Models\\User', 5, 'api_token', '1a94509caf938ac4c5dfaef29598753c1a7aecec393620cbbfa7800960aab09a', '["*"]', NULL, NULL, '2025-05-17 09:42:28', '2025-05-17 09:42:28'),
	(8, 'App\\Models\\User', 8, 'api_token', 'bed8c57fd1753e0352a75f986e4d13c353b2a690013075efb28000eb268a0c60', '["*"]', NULL, NULL, '2025-05-18 04:46:23', '2025-05-18 04:46:23'),
	(10, 'App\\Models\\User', 8, 'api_token', '5ca3fd7ae86544cbacbef2b03d4549b177de01db7fb55eade97015607c468cbd', '["*"]', NULL, NULL, '2025-05-18 06:00:40', '2025-05-18 06:00:40'),
	(11, 'App\\Models\\User', 8, 'api_token', '2a531d5b733d69ec5bdadf2d3c2d8eb8772944414b764b9c18bfc54fc0110201', '["*"]', '2025-05-18 11:12:21', NULL, '2025-05-18 06:21:08', '2025-05-18 11:12:21');

-- Dumping structure for table hub-mangament.services
CREATE TABLE IF NOT EXISTS `services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `services_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.services: ~7 rows (approximately)
INSERT INTO `services` (`id`, `workspace_id`, `category`, `name`, `created_at`, `updated_at`) VALUES
	(1, 1, 'مشروبات ساخنة', 'شاي', '2025-05-17 06:39:37', '2025-05-17 06:39:37'),
	(2, 1, 'مشروبات ساخنة', 'قهوة', '2025-05-17 06:39:51', '2025-05-17 06:39:51'),
	(3, 1, 'مشروبات ساخنة', 'نسكافيه', '2025-05-17 06:39:57', '2025-05-17 06:39:57'),
	(4, 1, 'مشروبات باردة', 'اسكيمو', '2025-05-17 06:40:13', '2025-05-17 06:40:13'),
	(5, 1, 'مشروبات باردة', 'عصير فراولة', '2025-05-17 06:40:20', '2025-05-17 06:40:20'),
	(6, 1, 'حلويات', 'كنافة عربية', '2025-05-17 06:40:30', '2025-05-17 06:40:30'),
	(7, 1, 'حلويات', 'مهلبية', '2025-05-17 06:40:43', '2025-05-17 06:40:43');

-- Dumping structure for table hub-mangament.service_requests
CREATE TABLE IF NOT EXISTS `service_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `booking_id` bigint unsigned DEFAULT NULL,
  `type` enum('seat_change','cafe_request') COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','in_progress','completed','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_requests_user_id_foreign` (`user_id`),
  KEY `service_requests_booking_id_foreign` (`booking_id`),
  CONSTRAINT `service_requests_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.service_requests: ~11 rows (approximately)
INSERT INTO `service_requests` (`id`, `user_id`, `booking_id`, `type`, `details`, `status`, `created_at`, `updated_at`) VALUES
	(1, 5, 1, 'seat_change', 'I want to move to a window seat', 'completed', '2025-05-11 09:45:49', '2025-05-18 10:24:15'),
	(2, 7, NULL, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 07:15:12', '2025-05-17 07:15:12'),
	(3, 7, 5, 'seat_change', 'أريد مقعد بجانب الشباك', 'completed', '2025-05-17 07:16:32', '2025-05-18 11:08:14'),
	(4, 7, 6, 'seat_change', 'أريد مقعد بجانب الشباك', 'pending', '2025-05-17 07:29:19', '2025-05-17 07:29:19'),
	(5, 7, NULL, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 07:29:30', '2025-05-17 07:29:30'),
	(6, 7, NULL, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 07:46:17', '2025-05-17 07:46:17'),
	(7, 7, NULL, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 07:46:20', '2025-05-17 07:46:20'),
	(8, 7, NULL, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 08:00:53', '2025-05-17 08:00:53'),
	(9, 7, NULL, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 08:01:04', '2025-05-17 08:01:04'),
	(10, 7, NULL, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 08:01:07', '2025-05-17 08:01:07'),
	(11, 7, 6, 'cafe_request', 'أريد شاي بالحليب وسكر', 'pending', '2025-05-17 08:21:24', '2025-05-17 08:21:24'),
	(12, 8, 11, 'seat_change', 'أريد مقعد بجانب الشباك', 'in_progress', '2025-05-17 10:25:09', '2025-05-18 11:11:07'),
	(13, 8, 11, 'cafe_request', 'asd', 'rejected', '2025-05-17 10:25:34', '2025-05-18 11:12:16'),
	(14, 8, 12, 'cafe_request', 'asssdda', 'pending', '2025-05-18 09:09:43', '2025-05-18 09:09:43'),
	(15, 8, 12, 'seat_change', 'asssdda', 'pending', '2025-05-18 09:10:16', '2025-05-18 09:10:16');

-- Dumping structure for table hub-mangament.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.sessions: ~1 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('VwrSUOM0HsUIWqpEmNLgqggVLKtQDJ2eP5R5NoNP', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWVN1SlRCNWRTTVh0OVJLeHF4UnpSZ2MzR0xuSEZZQkRmclpXVVFzWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly9odWItbWFuZ2FtZW50LnRlc3QvYWRtaW4vc2VydmljZXMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkVXJXcFJXWU0uSThoM21IZkxMSTFNdXBhL0RYekd2LkhrR2VMN0NIUHlIcDZ0SVFiNzY4Ym0iO30=', 1747577641);

-- Dumping structure for table hub-mangament.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.settings: ~2 rows (approximately)
INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
	(1, 'about', 'تطبيق هَب لإدارة مساحات العمل\n\nيوفّر تطبيق هَب (HUB) تجربة ذكية لحجز مساحات العمل المشتركة بكل سهولة وسرعة. يتيح للمستخدمين استعراض المساحات المتاحة، الاطلاع على الباقات والعروض، إجراء الحجوزات المسبقة، والدفع من خلال طرق متعددة.\n\nكما يسهّل التواصل الفوري بين المستخدم والسكرتيرة المسؤولة عن كل مساحة، لتأكيد الحجوزات وتقديم الخدمات الإضافية مثل طلب المشروبات أو تغيير المقاعد.\n\nتم تصميم التطبيق ليوفّر بيئة عمل مريحة، مرنة، وآمنة، ويخدم بشكل خاص:\n\nالمستقلين وأصحاب الأعمال الحرة\n\nطلاب الجامعات\n\nرواد الأعمال\n\nالفرق الصغيرة والشركات الناشئة\n\nيهدف "هَب" إلى تعزيز ثقافة العمل المرن، ودعم بيئة ريادة الأعمال في المجتمع، من خلال تنظيم تجربة الحجز والتواصل داخل مساحات العمل بكل احترافية وسلاسة.', '2025-05-17 09:19:36', '2025-05-17 09:19:36'),
	(2, 'terms', 'مرحبًا بك في تطبيق هَب لإدارة مساحات العمل. يرجى قراءة الشروط والأحكام التالية بعناية قبل استخدام التطبيق. إن استخدامك للتطبيق يعني موافقتك الكاملة على جميع البنود أدناه:\n\n1. التسجيل والاستخدام\nيجب أن تكون المعلومات التي تُدخلها صحيحة ومحدثة (الاسم، رقم الجوال، التخصص... إلخ).\nيُمنع إنشاء أكثر من حساب لنفس الشخص أو استخدام بيانات غير حقيقية.\nالمستخدم مسؤول بشكل كامل عن أي نشاط يتم من خلال حسابه.\n\n2. الحجوزات والدفع\nيتم حجز المساحات بناءً على الباقات المتوفرة لكل مساحة.\nيجب تأكيد الدفع بإرفاق إيصال التحويل البنكي عند الحاجة.\nلا يُعتبر الحجز مؤكدًا إلا بعد موافقة السكرتيرة أو الإدارة.\nقد يتم إلغاء الحجز في حال عدم الالتزام بشروط الدفع أو الاستخدام.\n\n\n3. سياسة الإلغاء والاسترداد\nيمكن إلغاء الحجز خلال فترة زمنية محددة قبل وقت البدء.\nلا يُضمن استرداد أي مبالغ بعد تأكيد الحجز، إلا في حالات استثنائية يتم تقييمها من الإدارة.\n\n\n4. استخدام الخدمات داخل المساحة\nيُمنع استخدام المرافق لأي نشاط غير قانوني أو مخالف للآداب العامة.\nالحفاظ على نظافة المكان واحترام خصوصية الآخرين إلزامي.\nيمنع العبث بالمعدات أو استخدام الإنترنت لأغراض غير مشروعة.\n\n\n5. التواصل والمحادثات\nيمكن للمستخدم التواصل مع السكرتيرة لتأكيد الحجز أو طلب خدمات إضافية.\nيُمنع استخدام خاصية المحادثة لأي رسائل مسيئة أو غير متعلقة بالخدمة.\nيتم مراقبة المحادثات لأغراض أمنية وضمان الجودة.\n\n6. المحتوى والملكية\nجميع العلامات التجارية، التصميمات، والبرمجيات داخل التطبيق مملوكة لإدارة "هَب".\nيُمنع نسخ أو إعادة استخدام أي جزء من التطبيق بدون إذن رسمي.\n\n7. تعديل الشروط\nتحتفظ إدارة التطبيق بحق تعديل هذه الشروط في أي وقت.\nسيتم إعلام المستخدمين بأي تغييرات جوهرية عبر إشعار داخل التطبيق.\n\n8. الدعم والتواصل\nلأي استفسار أو مساعدة، يمكنك التواصل مع فريق الدعم عبر البريد الإلكتروني أو الرقم المخصص داخل التطبيق.\n\n✅ باستخدامك لهذا التطبيق، فأنت توافق على جميع الشروط أعلاه.\n\n', '2025-05-17 09:22:26', '2025-05-17 09:22:26');

-- Dumping structure for table hub-mangament.workspace_images
CREATE TABLE IF NOT EXISTS `workspace_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workspace_id` bigint unsigned NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workspace_images_workspace_id_foreign` (`workspace_id`),
  CONSTRAINT `workspace_images_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table hub-mangament.workspace_images: ~2 rows (approximately)
INSERT INTO `workspace_images` (`id`, `workspace_id`, `image`, `created_at`, `updated_at`) VALUES
	(2, 1, 'uploads/1747219124_branch_building_gaza.jpg', '2025-05-14 07:38:44', '2025-05-14 07:38:44'),
	(3, 2, 'uploads/1747219816_branch_building_north.jpg', '2025-05-14 07:50:16', '2025-05-14 07:50:16'),
	(4, 2, 'uploads/1747219816_branch_building_gaza.jpg', '2025-05-14 07:50:16', '2025-05-14 07:50:16');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
