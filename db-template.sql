SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `guest-portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `guest-portal`;

CREATE TABLE IF NOT EXISTS `vouchers`
(
    `id`          char(10)             NOT NULL COMMENT 'Voucher ID',
    `uses`        tinyint(1) UNSIGNED       DEFAULT 1 COMMENT 'Voucher uses left',
    `expiry`      timestamp            NULL DEFAULT NULL COMMENT 'Voucher expiry date',
    `duration`    smallint(5) UNSIGNED NOT NULL COMMENT 'Session duration (min)',
    `speed_limit` int(1)                    DEFAULT NULL COMMENT 'Transfer speed limit (MiB/s)',
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;
COMMIT;

GRANT SELECT, INSERT, UPDATE (uses), DELETE ON `guest-portal`.`vouchers` TO `guest-portal`@`%`;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
