CREATE TABLE IF NOT EXISTS `#__jfbconnect_autopost` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` INT NOT NULL,
  `opengraph_type` TEXT,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `#__jfbconnect_autopost_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `autopost_id` INT NOT NULL,
  `type` tinyint(2) DEFAULT NULL,
  `url` text,
  `provider` text,
  `channel_type` text,
  `ext` text,
  `layout` text,
  `item_id` INT NOT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `response` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`(30)),
  KEY `ext` (`url`(5)),
  KEY `layout` (`url`(5))
) DEFAULT CHARACTER SET utf8;

# Add indices to the tables ;
ALTER TABLE `#__jfbconnect_user_map` ADD INDEX `j_user_id` (`j_user_id`);
ALTER TABLE `#__jfbconnect_user_map` ADD INDEX `provider_user_id` (`provider_user_id`);
ALTER TABLE `#__jfbconnect_user_map` ADD INDEX `provider` (`provider`);