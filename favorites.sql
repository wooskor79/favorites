-- 파일명: favorites.sql

-- 데이터베이스가 없다면 생성합니다.
CREATE DATABASE IF NOT EXISTS `favorites_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 해당 데이터베이스를 사용합니다.
USE `favorites_db`;

-- 기존 테이블 삭제 (초기화용)
DROP TABLE IF EXISTS `info_card_items`;
DROP TABLE IF EXISTS `info_card_groups`;
DROP TABLE IF EXISTS `quick_links`;
DROP TABLE IF EXISTS `memos`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `favorites`;
DROP TABLE IF EXISTS `groups`;

-- 'groups' 테이블 생성
CREATE TABLE IF NOT EXISTS `groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

INSERT IGNORE INTO `groups` (name) VALUES ('기본 그룹');

-- 'favorites' 테이블 생성
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(2048) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `group_id` INT(11),
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 'users' 테이블 생성
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- 'memos' 테이블 생성
CREATE TABLE IF NOT EXISTS `memos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `images` JSON,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 'quick_links' 테이블 생성 (sort_order 추가됨)
CREATE TABLE IF NOT EXISTS `quick_links` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `url` VARCHAR(2048) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 'info_card_groups' 테이블 생성
CREATE TABLE IF NOT EXISTS `info_card_groups` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 'info_card_items' 테이블 생성
CREATE TABLE IF NOT EXISTS `info_card_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `content` TEXT,
  `url` VARCHAR(2048) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`group_id`) REFERENCES `info_card_groups`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- [기존 DB 수정용]
-- ALTER TABLE `quick_links` ADD `sort_order` INT NOT NULL DEFAULT 0 AFTER `url`;