ALTER TABLE `#__jlike_likes` ADD INDEX `userid` (`userid`);
ALTER TABLE `#__jlike_todos` ADD COLUMN `spent_time` INT(11) NOT NULL DEFAULT 0 AFTER `ideal_time`;
ALTER TABLE `#__jlike_content` DROP INDEX uk_element_pair;
ALTER TABLE `#__jlike_content` MODIFY element_id varchar(150);
ALTER TABLE `#__jlike_content` ADD CONSTRAINT uk_element_pair UNIQUE INDEX (`element_id`, `element`);
ALTER TABLE `#__jlike_content` ADD COLUMN `description` TEXT NULL AFTER `title`;

CREATE TABLE IF NOT EXISTS `#__jlike_todo_track` (
  `session_id` varbinary(200) NOT NULL,
  `todo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestart` datetime NOT NULL,
  `timeend` datetime NOT NULL,
  `spent_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`),
  KEY `todo_id` (`todo_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
