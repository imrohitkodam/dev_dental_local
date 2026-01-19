--
-- Table structure for table `#__tj_media_files`
--

CREATE TABLE IF NOT EXISTS `#__tj_media_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8_bin NOT NULL,
  `type` varchar(250) COLLATE utf8_bin NOT NULL,
  `path` varchar(250) COLLATE utf8_bin NOT NULL,
  `state` tinyint(1) NOT NULL,
  `source` varchar(250) COLLATE utf8_bin NOT NULL,
  `original_filename` varchar(250) COLLATE utf8_bin NOT NULL,
  `size` int(11) NOT NULL,
  `storage` varchar(250) COLLATE utf8_bin NOT NULL,
  `created_by` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` varchar(500) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
-- Table structure for table `#__tj_media_files_xref`
--

CREATE TABLE IF NOT EXISTS `#__tj_media_files_xref` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `client` varchar(250) COLLATE utf8_bin NOT NULL,
  `is_gallery` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;
