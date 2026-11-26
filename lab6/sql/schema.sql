CREATE DATABASE IF NOT EXISTS `moja_strona`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `moja_strona`;

CREATE TABLE IF NOT EXISTS `page_list` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `alias` VARCHAR(20) NOT NULL,
  `page_title` VARCHAR(255) NOT NULL,
  `page_content` TEXT NOT NULL,
  `status` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_page_list_alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;