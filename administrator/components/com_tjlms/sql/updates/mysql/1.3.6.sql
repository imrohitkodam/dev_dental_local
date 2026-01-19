--
-- Table structure for table `#__tj_media_files`
--

CREATE TABLE IF NOT EXISTS `#__tj_media_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `type` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `path` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `source` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `original_filename` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `size` int(11) NOT NULL DEFAULT 0,
  `storage` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `access` tinyint(1) NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` varchar(500) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
-- Table structure for table `#__tj_media_files_xref`
--

CREATE TABLE IF NOT EXISTS `#__tj_media_files_xref` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int(11) NOT NULL DEFAULT 0,
  `client_id` int(11) NOT NULL DEFAULT 0,
  `client` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `is_gallery` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;
