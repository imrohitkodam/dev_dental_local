--
-- Table structure for table `#__tjlms_lesson_track_archive`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_lesson_track_archive` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_track_id` int(11) NOT NULL DEFAULT 0,
  `archive_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lesson_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `attempt` int(11) NOT NULL DEFAULT 0,
  `timestart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeend` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `score` int(11) NOT NULL DEFAULT 0,
  `lesson_status` text,
  `last_accessed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `total_content` float NOT NULL DEFAULT 0,
  `current_position` float NOT NULL DEFAULT 0,
  `time_spent` time NOT NULL,
  `live` tinyint NOT NULL DEFAULT 0,
  `modified_by` int(128) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `attempt` (`attempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_scoes_track_archive`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_scoes_track_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scorm_scoes_track_id` int(11) NOT NULL DEFAULT 0,
  `archive_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` int(11) NOT NULL DEFAULT 0,
  `scorm_id` int(11) NOT NULL DEFAULT 0,
  `sco_id` int(11) NOT NULL DEFAULT 0,
  `attempt` int(11) NOT NULL DEFAULT 1,
  `element` varchar(255) NOT NULL DEFAULT '',
  `value` text,
  `timemodified` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `scorm_id` (`scorm_id`),
  KEY `sco_id` (`sco_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
