/*
* @package    Payplans
* @copyright  Copyright (C) StackIdeas Private Limited. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* Payplans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


CREATE TABLE IF NOT EXISTS `#__payplans_support` (
		`support_id` INT NOT NULL AUTO_INCREMENT,
		`key` VARCHAR(45) NOT NULL ,
		`value` TEXT NULL,
		PRIMARY KEY (`support_id`) ,
		UNIQUE INDEX `idx_key` (`key` ASC)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_config` (
		`config_id` int(11) NOT NULL AUTO_INCREMENT,
		`key` varchar(255) NOT NULL,
		`value` text,
		PRIMARY KEY (`config_id`),
		UNIQUE KEY `idx_key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_user` (
		`user_id` INT NOT NULL,
		`params` TEXT NULL,
		`address` VARCHAR(255) NOT NULL DEFAULT '',
		`state` VARCHAR(255) DEFAULT '',
		`city` VARCHAR(255) DEFAULT '',
		`country` INT NOT NULL DEFAULT '0',
		`zipcode` VARCHAR(10) NOT NULL DEFAULT '',
		`preference` TEXT NOT NULL,
		PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_currency` (
		`currency_id` CHAR(3) NOT NULL,
		`title` varchar(255) DEFAULT NULL,
		`published` tinyint(1) DEFAULT 1,
		`params` text NULL,
		`symbol` char(5) DEFAULT NULL,
		PRIMARY KEY (`currency_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_country` (
		`country_id` INT NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`isocode2` CHAR(2) DEFAULT NULL,
		`isocode3` CHAR(3) DEFAULT NULL,
		`isocode3n` int(3) DEFAULT NULL,
		`published` tinyint(1) DEFAULT 1,
		`default` tinyint(1) DEFAULT 0,
		PRIMARY KEY (`country_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_app` (
		`app_id` int(11) NOT NULL AUTO_INCREMENT,
		`group` varchar(255) NOT NULL DEFAULT '',
		`title`  varchar(255) NULL DEFAULT '',
		`type` varchar(255) NOT NULL,
		`folder` varchar(255) NOT NULL DEFAULT '',
		`description` varchar(255) NULL DEFAULT '',
		`core_params` text,
		`app_params` text,
		`ordering` int(11) NOT NULL DEFAULT '0',
		`published` tinyint(3) NOT NULL DEFAULT '0',
		PRIMARY KEY (`app_id`),
		KEY `idx_published` (`published`),
		KEY `idx_ordering` (`ordering`),
		KEY `idx_type` (`type`),
		KEY `idx_publish_type` (`type`, `published`),
		KEY `idx_publish_order`(`published`, `ordering`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_log` (
	`log_id` int(11) NOT NULL AUTO_INCREMENT,
	`level` int(11) NOT NULL DEFAULT '0',
	`owner_id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`class` varchar(255) NOT NULL,
	`object_id` int(11) NOT NULL,
	`message` TEXT NULL,
	`user_ip` varchar(255) NOT NULL,
	`created_date` datetime NOT NULL,
	`content` TEXT NULL,
	`read` tinyint(1) DEFAULT '0',
	`position` TEXT NULL,
	`previous_token` TEXT NULL,
	`current_token` TEXT NULL,
	`legacy` TINYINT NOT NULL DEFAULT '0',
	PRIMARY KEY (`log_id`),
	KEY `idx_level` (`level` ASC),
	KEY `idx_class_object` (`class`, `object_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_resource` (
		`resource_id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
		`title` varchar(255) NOT NULL,
		`value` varchar(255) NOT NULL,
		`subscription_ids` TEXT NULL,
		`count` int(11) NOT NULL DEFAULT '0',
		PRIMARY KEY (`resource_id`),
		KEY `user_id` (`user_id`,`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_modifier` (
	`modifier_id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`invoice_id` int(11) DEFAULT NULL,
	`amount` decimal(15,5) DEFAULT '0.00000',
	`type` varchar(255) DEFAULT NULL,
	`reference` varchar(255) DEFAULT NULL,
	`message` text,
	`percentage` tinyint(1) NOT NULL DEFAULT '1',
	`serial` int(11) NOT NULL DEFAULT '0',
	`frequency` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`modifier_id`),
	KEY `idx_user_id` (`invoice_id`),
	KEY `idx_invoices` (`invoice_id`, `modifier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__payplans_statistics`(
		`statistics_id` INT NOT NULL AUTO_INCREMENT,
		`statistics_type` varchar(255) NULL,
		`purpose_id_1` INT NOT NULL,
		`purpose_id_2` INT DEFAULT 0,
		`count_1` INT DEFAULT 0,
		`count_2` DECIMAL(15,5) NOT NULL DEFAULT '0.00000',
		`count_3` INT DEFAULT 0,
		`count_4` INT DEFAULT 0,
		`count_5` DECIMAL(15,5) NOT NULL DEFAULT '0.00000',
		`count_6` DECIMAL(15,5) NOT NULL DEFAULT '0.00000',
		`count_7` INT DEFAULT 0,
		`count_8` INT DEFAULT 0,
		`count_9` INT DEFAULT 0,
		`count_10` INT DEFAULT 0,
		`details_1` varchar(255) NULL,
		`details_2` varchar(255) NULL,
		`message` varchar(255) NULL,
		`statistics_date` datetime NOT NULL,
		`modified_date` datetime NOT NULL,
		PRIMARY KEY (`statistics_id`),
		KEY `idx_statistics_date` (`statistics_date` ASC),
		KEY `idx_stats_type_purpose` (`statistics_type` (64), `purpose_id_1`, `statistics_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__payplans_languages` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`title` varchar(255) NOT NULL,
		`locale` varchar(255) NOT NULL,
		`updated` datetime NOT NULL,
		`state` tinyint(3) NOT NULL,
		`translator` varchar(255) NOT NULL,
		`progress` int(11) NOT NULL,
		`params` text NOT NULL,
		PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_ipn` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`payment_id` int(11) NOT NULL,
	`json` longtext NOT NULL,
	`raw` longtext NOT NULL,
	`php` longtext NOT NULL,
	`query` longtext NOT NULL,
	`ip` text NOT NULL,
	`created` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_prodiscount` (
	`prodiscount_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255),
	`coupon_code` varchar(255),
	`coupon_type` varchar(255),
	`core_discount` tinyint(1),
	`coupon_amount` decimal(15,5) DEFAULT 0.00000,
	`plans` varchar(255),
	`start_date` DATETIME NOT NULL,
	`end_date` DATETIME NOT NULL,
	`published` tinyint(1),
	`params` text,
	 PRIMARY KEY (`prodiscount_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_referral` (
	`referrar_id` int(11) NOT NULL,
	`referral_id` int(11) NOT NULL,
	`plan_id` int(11) NOT NULL,
	`amount` decimal(15,5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_customdetails` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` text NOT NULL,
	`type` varchar(255) NOT NULL,
	`created` datetime NOT NULL,
	`published` tinyint(3) NOT NULL,
	`data` longtext NOT NULL,
	`params` text NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__payplans_download` (
  `download_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT 0,
  `params` longtext,
  `created` DATETIME  DEFAULT '0000-00-00 00:00:00',
   PRIMARY KEY (`download_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;