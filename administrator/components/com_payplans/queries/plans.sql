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

CREATE TABLE IF NOT EXISTS `#__payplans_plan` (
	`plan_id` INT NOT NULL  AUTO_INCREMENT,
	`title` VARCHAR(255)  NOT NULL,
	`published` TINYINT(1)  DEFAULT 1,
	`visible` TINYINT(1)  DEFAULT 1,
	`ordering` INT DEFAULT 0,
	`checked_out` INT DEFAULT NULL,
	`checked_out_time` DATETIME DEFAULT NULL,
	`modified_date` DATETIME DEFAULT '0000-00-00 00:00:00',
	`description` TEXT DEFAULT NULL,
	`details` TEXT DEFAULT NULL,
	`params` TEXT DEFAULT NULL,
	PRIMARY KEY (`plan_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__payplans_planapp` (
	`planapp_id`  INT NOT NULL AUTO_INCREMENT,
	`plan_id`   INT NOT NULL,
	`app_id`  INT NOT NULL,
	PRIMARY KEY (`planapp_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_plangroup` (
	`plangroup_id` int(11) NOT NULL AUTO_INCREMENT,
	`group_id` int(11) NOT NULL,
	`plan_id` int(11) NOT NULL,
	PRIMARY KEY (`plangroup_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_group` (
	`group_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	`parent` int(11) NOT NULL DEFAULT '0',
	`published` tinyint(1) DEFAULT '1',
	`visible` tinyint(1) DEFAULT '1',
	`ordering` int(11) DEFAULT '0',
	`description` text,
	`params` text,
	PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_order` (
	`order_id` INT NOT NULL AUTO_INCREMENT,
	`buyer_id` INT NOT NULL,
	`total` DECIMAL(15,5) NOT NULL DEFAULT '0.00000',
	`currency` CHAR(3) DEFAULT NULL,
	`status` INT NOT NULL DEFAULT 0,
	`checked_out` INT DEFAULT NULL,
	`checked_out_time` DATETIME NULL,
	`created_date` DATETIME NOT NULL,
	`modified_date` DATETIME NOT NULL,
	`params` text,
	PRIMARY KEY (`order_id`),
	INDEX `idx_buyer_id` (`buyer_id` ASC)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__payplans_payment` (
	`payment_id` INT NOT NULL AUTO_INCREMENT,
	`app_id` INT NOT NULL,
	`params` text,
	`invoice_id` INT NOT NULL DEFAULT 0,
	`user_id` INT NOT NULL DEFAULT 0,
	`gateway_params` text,
	`checked_out` INT DEFAULT NULL,
	`checked_out_time` DATETIME NULL,
	`created_date` DATETIME NOT NULL,
	`modified_date` DATETIME NOT NULL,
	PRIMARY KEY (`payment_id`) ,
	INDEX `idx_invoice_id` (`invoice_id` ASC)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__payplans_subscription` (
	`subscription_id` INT NOT NULL AUTO_INCREMENT,
	`order_id` INT NOT NULL,
	`user_id` INT NOT NULL,
	`plan_id` INT NOT NULL,
	`status` INT NOT NULL DEFAULT 0,
	`total` DECIMAL(15,5) DEFAULT '0.00000',
	`subscription_date` DATETIME  DEFAULT '0000-00-00 00:00:00',
	`expiration_date` DATETIME  DEFAULT '0000-00-00 00:00:00',
	`cancel_date` DATETIME  DEFAULT '0000-00-00 00:00:00',
	`checked_out` INT DEFAULT NULL,
	`checked_out_time` DATETIME DEFAULT NULL,
	`modified_date` DATETIME DEFAULT '0000-00-00 00:00:00',
	`params` TEXT NOT NULL,
	`lock` tinyint(1) default 0,
	PRIMARY KEY (`subscription_id`),
	KEY `idx_order_id` (`order_id`),
	KEY `idx_userid` (`user_id`),
	KEY `idx_user_subs` (`user_id`, `subscription_id`),
	KEY `idx_expired_subs` (`expiration_date`, `status`, `lock`),
	KEY `idx_params` (`params` (200))
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_transaction`(
	`transaction_id` INT NOT NULL AUTO_INCREMENT,
	`user_id` INT DEFAULT 0,
	`invoice_id` INT DEFAULT 0,
	`current_invoice_id` INT DEFAULT 0,
	`payment_id` INT DEFAULT 0,
	`gateway_txn_id` varchar(255) DEFAULT NULL,
	`gateway_parent_txn` varchar(255) DEFAULT NULL,
	`gateway_subscr_id` varchar(255) DEFAULT NULL,
	`amount` DECIMAL(15,5) DEFAULT '0.00000',
	`reference` varchar(255) NULL,
	`message` varchar(255) NULL,
	`created_date` datetime NOT NULL,
	`params` TEXT NULL,
	PRIMARY KEY (`transaction_id`),
	KEY `idx_user_id` (`user_id` ASC),
	KEY `idx_payments` (`payment_id`, `transaction_id`),
	KEY `idx_created_date` (`created_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_invoice`(
	`invoice_id` int(11) NOT NULL AUTO_INCREMENT,
	`serial` varchar(255) DEFAULT NULL,
	`object_id` int(11) NOT NULL DEFAULT '0',
	`object_type` varchar(255) DEFAULT NULL,
	`user_id` int(11) NOT NULL,
	`subtotal` decimal(15,5) DEFAULT '0.00000',
	`total` decimal(15,5) NOT NULL DEFAULT '0.00000',
	`currency` char(3) DEFAULT NULL,
	`counter` int(11) DEFAULT '0',
	`status` int(11) NOT NULL DEFAULT '0',
	`params` text,
	`created_date` datetime NOT NULL,
	`modified_date` datetime NOT NULL,
	`paid_date` datetime DEFAULT NULL,
	`checked_out` int(11) DEFAULT NULL,
	`checked_out_time` datetime DEFAULT NULL,
		PRIMARY KEY (`invoice_id`),
		INDEX `idx_user_id` (`user_id` ASC),
		INDEX `idx_order_id` (`object_id` ASC),
		KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__payplans_parentchild` (
	`dependent_plan` int(11) NOT NULL ,
	`base_plan` varchar(255),
	`relation` int(11) NULL default '-2', 
	`display_dependent_plan` int(11) NULL default 0,
	`params` text,
	 PRIMARY KEY (`dependent_plan`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_advancedpricing` (
	`advancedpricing_id` int(11) NOT NULL AUTO_INCREMENT,
	`plans` varchar(255),
	`title` varchar(255),
	`units_title` varchar(255),
	`units_min` int(11),
	`units_max` int(11),
	`description` varchar(255),
	`created_date` datetime,
	`modified_date` datetime,
	`published` tinyint(1) DEFAULT 1,
	`params` text,
	 PRIMARY KEY (`advancedpricing_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__payplans_planaddons` (
	`planaddons_id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) DEFAULT NULL,
	`description` text,
	`price` decimal(15,5) DEFAULT '0.00000',
	`consumed` int(11) DEFAULT NULL,
	`addons_condition` int(11) DEFAULT NULL,
	`price_type` tinyint(1) DEFAULT '0',
	`apply_on` tinyint(1) DEFAULT '0',
	`plans` varchar(255) DEFAULT NULL,
	`start_date` datetime DEFAULT NULL,
	`end_date` datetime DEFAULT NULL,
	`published` tinyint(1) DEFAULT NULL,
	`ordering` int(11) NOT NULL DEFAULT '0',
	`params` text,
	PRIMARY KEY (`planaddons_id`),
	KEY `idx_published` (`published`),
	KEY `idx_dates` (`start_date`, `end_date`),
	KEY `idx_avaible_dates` (`published`, `start_date`, `end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__payplans_planaddons_stats` (
	`planaddons_stats_id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`planaddons_id` int(11) NOT NULL,
	`title` varchar(255) DEFAULT NULL,
	`price` decimal(15,5) DEFAULT '0.00000',
	`addons_condition` int(11) DEFAULT NULL,
	`price_type` tinyint(1) DEFAULT '0',
	`reference` int(11) DEFAULT NULL,
	`status` int(11) DEFAULT NULL,
	`consumed` int(11) NOT NULL DEFAULT '0',
	`purchase_date` datetime DEFAULT NULL,
	`params` text,
	PRIMARY KEY (`planaddons_stats_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
