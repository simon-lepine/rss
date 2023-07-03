CREATE DATABASE IF NOT EXISTS `rss` COLLATE 'utf8mb3_general_ci';

GRANT CREATE ROUTINE, CREATE TEMPORARY TABLES, LOCK TABLES, ALTER, CREATE, CREATE VIEW, DELETE, DELETE HISTORY, DROP, INDEX, INSERT, REFERENCES, SELECT, SHOW VIEW, TRIGGER, UPDATE, ALTER ROUTINE, EXECUTE ON `rss`.* TO 'app_root'@'%';

CREATE TABLE IF NOT EXISTS `rss`.`user_feeds` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY
) ENGINE='InnoDB';

ALTER TABLE `rss`.`user_feeds` ADD COLUMN IF NOT EXISTS `user_id` varchar(256) NULL;
ALTER TABLE `rss`.`user_feeds` ADD COLUMN IF NOT EXISTS `feed_url` varchar(256) NULL;

CREATE TABLE IF NOT EXISTS `rss`.`feed_entries` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY
) ENGINE='InnoDB';

ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `user_id` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `feed_url` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `title` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `title_hash` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `entry_id` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `link` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `timestamp_published` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `timestamp_read` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `date_published` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `description` varchar(256) NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `content` longtext NULL;
ALTER TABLE `rss`.`feed_entries` ADD COLUMN IF NOT EXISTS `unique_hash` varchar(256) NOT NULL;

ALTER TABLE `rss`.`feed_entries` ADD UNIQUE IF NOT EXISTS `unique_hash` (`unique_hash`);