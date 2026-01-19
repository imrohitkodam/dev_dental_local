CREATE TABLE IF NOT EXISTS `#__edocman_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `access` tinyint(3) unsigned DEFAULT '0',
  `category_layout` varchar(50) DEFAULT NULL,
  `metakey` varchar(255) DEFAULT NULL,
  `metadesc` tinytext,
  `asset_id` bigint(20) unsigned DEFAULT '0',
  `created_user_id` int(10) unsigned DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(10) unsigned DEFAULT '0',
  `modified_time` datetime DEFAULT NULL,
  `path` varchar(255) NOT NULL DEFAULT '',
  `checked_out` int(10) unsigned DEFAULT '0',
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(10) unsigned DEFAULT '0',
  `published` tinyint(3) unsigned DEFAULT '0',
  `user_ids` varchar(255) DEFAULT '',
  `language` varchar(255) DEFAULT '*',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `#__edocman_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(255) DEFAULT NULL,
  `config_value` text,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__edocman_documents`
--

CREATE TABLE IF NOT EXISTS `#__edocman_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `document_version` varchar(10) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL,
  `access` int(11) DEFAULT '0',
  `license_id` int(10) UNSIGNED DEFAULT '0',
  `short_description` text,
  `description` text,
  `metakey` text,
  `metadesc` text,
  `rating_count` bigint(20) DEFAULT '0',
  `rating_sum` decimal(10,2) DEFAULT '0.00',
  `hits` int(10) UNSIGNED DEFAULT '0',
  `downloads` int(11) DEFAULT '0',
  `asset_id` int(10) UNSIGNED DEFAULT '0',
  `created_user_id` int(10) UNSIGNED DEFAULT '0',
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(10) UNSIGNED DEFAULT '0',
  `modified_time` datetime DEFAULT NULL,
  `checked_out` int(10) UNSIGNED DEFAULT '0',
  `checked_out_time` datetime DEFAULT NULL,
  `ordering` int(10) UNSIGNED DEFAULT '0',
  `published` tinyint(3) UNSIGNED DEFAULT '0',
  `user_ids` varchar(255) DEFAULT '',
  `language` varchar(255) DEFAULT '*',
  `indexed_content` text,
  `tags` text,
  `indicators` varchar(50) DEFAULT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text,
  `view_url` varchar(255) DEFAULT '',
  `file_size` varchar(100) NOT NULL,
  `document_history` text NOT NULL,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `locked_by` int(11) NOT NULL DEFAULT '0',
  `locked_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__edocman_document_category`
--

CREATE TABLE IF NOT EXISTS `#__edocman_document_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) DEFAULT '0',
  `category_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__edocman_licenses`
--

CREATE TABLE IF NOT EXISTS `#__edocman_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `published` tinyint(3) unsigned DEFAULT '0',
  `default_license` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__edocman_statistics`
--

CREATE TABLE IF NOT EXISTS `#__edocman_statistics` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) DEFAULT '0',
  `user_id` int(11) DEFAULT '0',
  `download_time` datetime DEFAULT NULL,
  `user_ip` varchar(50) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__edocman_googledrive` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `element_id` int(11) NOT NULL DEFAULT '0',
  `element_name` varchar(255) NOT NULL,
  `cloud_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__edocman_googledrive_credentials` (
  `credentials` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__edocman_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(10) UNSIGNED DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `ordering` int(11) UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_document_id` (`document_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__edocman_unsubscribe_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;