-- 데이터베이스가 없다면 생성합니다.
CREATE DATABASE IF NOT EXISTS `favorites_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 해당 데이터베이스를 사용합니다.
USE `favorites_db`;

-- 기존 info_cards 테이블이 있다면 삭제합니다.
DROP TABLE IF EXISTS `info_card_items`;
DROP TABLE IF EXISTS `info_cards`;

-- 'groups' 테이블을 생성합니다.
CREATE TABLE IF NOT EXISTS `groups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- '기본 그룹'을 추가합니다. (오류가 나도 무시)
INSERT IGNORE INTO `groups` (name) VALUES ('기본 그룹');

-- 'favorites' 테이블을 생성합니다.
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `url` VARCHAR(2048) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `group_id` INT(11),
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`group_id`) REFERENCES `groups`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 'users' 테이블을 생성합니다.
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- 'memos' 테이블을 생성합니다.
CREATE TABLE IF NOT EXISTS `memos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `images` JSON,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 'quick_links' 테이블을 생성합니다.
CREATE TABLE IF NOT EXISTS `quick_links` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `url` VARCHAR(2048) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- === 새로 추가된 정보 카드 관련 테이블 ===

-- 정보 카드 '그룹' 테이블 (최대 4개)
CREATE TABLE IF NOT EXISTS `info_card_groups` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 정보 카드 '내용물(아이템)' 테이블 (그룹당 최대 5개)
CREATE TABLE IF NOT EXISTS `info_card_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `group_id` INT NOT NULL,
  `content` TEXT,
  `url` VARCHAR(2048) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`group_id`) REFERENCES `info_card_groups`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;