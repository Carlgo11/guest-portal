SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `guest-portal` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `guest-portal`;

CREATE TABLE `users` (
                         `username` varchar(16) NOT NULL,
                         `password` varchar(72) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `vouchers` (
                                          `id` char(10) NOT NULL COMMENT 'Voucher ID',
                                          `uses` tinyint(1) UNSIGNED DEFAULT 1 COMMENT 'Voucher uses left',
                                          `expiry` int(11) UNSIGNED DEFAULT NULL COMMENT 'Voucher expiry date',
                                          `duration` int(11) UNSIGNED NOT NULL COMMENT 'Session duration (min)',
                                          `speed_limit` int(5) UNSIGNED DEFAULT NULL COMMENT 'Transfer speed limit (MiB/s)',
                                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

GRANT SELECT, INSERT, UPDATE (uses), DELETE ON `guest-portal`.`vouchers` TO `guest-portal`@`%`;

SET GLOBAL event_scheduler = "ON";

DELIMITER $$
CREATE DEFINER=`guest-portal`@`%` EVENT `Delete old vouchers` ON SCHEDULE EVERY 1 HOUR STARTS '2021-01-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM `vouchers` WHERE `vouchers`.`expiry` <= UNIX_TIMESTAMP(NOW())$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
