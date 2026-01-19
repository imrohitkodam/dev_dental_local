--
-- Table structure for table `#__tjlms_activities`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_id` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `action` text,
  `element` text,
  `element_id` int(11) NOT NULL DEFAULT 0,
  `element_url` text,
  `added_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text,
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  KEY `comp_activity` (`action`(50),`element`(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_associated_files`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_associated_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL DEFAULT 0,
  `media_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lbeta_tjlms_assignments`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assign_to` int(11) NOT NULL COMMENT 'User to whom the course has been assign' DEFAULT 0,
  `assign_by` int(11) NOT NULL COMMENT 'User who assign this course to another user' DEFAULT 0,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deu_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `param` varchar(255) NOT NULL DEFAULT '',
  `course_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_coupons`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT 0,
  `course_id` varchar(255) NOT NULL DEFAULT '',
  `subscription_id` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `code` varchar(255) NOT NULL DEFAULT '',
  `value` float( 10, 2 ) UNSIGNED NOT NULL DEFAULT 0,
  `val_type` varchar(255) NOT NULL DEFAULT '',
  `max_use` varchar(255) NOT NULL DEFAULT '',
  `max_per_user` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `couponParams` text,
  `from_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exp_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `used_count` int(11) NOT NULL DEFAULT '0',
  `privacy` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `ordering` (`ordering`),
  UNIQUE KEY `coupon_code` (`code`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_courses`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_courses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `catid` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  `short_desc` text,
  `access` int(11) NOT NULL DEFAULT 0,
  `description` text,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `metakey` text,
  `metadesc` text,
  `certificate_term` varchar(255) NOT NULL DEFAULT '',
  `certificate_id` int(11) NOT NULL DEFAULT 0,
  `expiry` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT '0' COMMENT 'Course free = 0, paid = 1',
  `group_id` int(11) NOT NULL DEFAULT 0,
  `admin_approval` tinyint(1) NOT NULL DEFAULT 0,
  `auto_enroll` tinyint(1) NOT NULL DEFAULT 0,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `course_alias` (`alias`(191)),
  KEY `catid` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
--
-- Table structure for table `#__tjlms_enrolled_users`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_enrolled_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `enrolled_on_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enrolled_by` int(100) NOT NULL DEFAULT 0,
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` int(11) NOT NULL DEFAULT 0,
  `unlimited_plan` tinyint(1) NOT NULL DEFAULT 0,
  `before_expiry_mail` tinyint(1) NOT NULL DEFAULT 0,
  `after_expiry_mail` tinyint(1) NOT NULL DEFAULT 0,
  `params` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`course_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_files`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `path` varchar(255) NOT NULL DEFAULT '',
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_file_download_stats`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_file_download_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `downloads` text,
  `file_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_lessons`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_lessons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `catid` int(11) NOT NULL DEFAULT 0,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `mod_id` int(11) NOT NULL DEFAULT 0,
  `short_desc` text,
  `description` text,
  `image` varchar(255) NOT NULL DEFAULT '',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  `free_lesson` varchar(255) NOT NULL DEFAULT '',
  `no_of_attempts` varchar(255) NOT NULL DEFAULT '',
  `attempts_grade` varchar(255) NOT NULL DEFAULT '',
  `consider_marks` varchar(255) NOT NULL DEFAULT '',
  `format` varchar(255) NOT NULL DEFAULT '',
  `media_id` int(11) NOT NULL DEFAULT 0,
  `eligibility_criteria` varchar(255) NOT NULL DEFAULT '',
  `ideal_time` int(11) NOT NULL DEFAULT 0,
  `resume` int(11) NOT NULL DEFAULT 1,
  `total_marks` int(11) NOT NULL DEFAULT 0,
  `passing_marks` int(11) NOT NULL DEFAULT 0,
  `in_lib` TINYINT(1) NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`),
  KEY `lesson_alias` (`alias`(191)),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_lesson_track`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_lesson_track` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
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
  UNIQUE KEY `unique_lesson_entry` (`lesson_id`,`user_id`,`attempt`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_media`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `format` varchar(255) NOT NULL DEFAULT '',
  `sub_format` varchar(255) NOT NULL COMMENT 'For video format' DEFAULT '',
  `org_filename` varchar(255) NOT NULL DEFAULT '',
  `saved_filename` varchar(255) NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `path` varchar(255) NOT NULL DEFAULT '',
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  `source` text,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_modules`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `course_id` int(255) NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `storage` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__tjlms_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(23) NOT NULL DEFAULT '',
  `course_id` int(11) NOT NULL DEFAULT 0,
  `enrollment_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) DEFAULT NULL DEFAULT '',
  `email` varchar(100) DEFAULT NULL DEFAULT '',
  `user_id` int(11) DEFAULT NULL DEFAULT 0,
  `cdate` datetime DEFAULT '0000-00-00 00:00:00',
  `mdate` datetime DEFAULT '0000-00-00 00:00:00',
  `transaction_id` varchar(100) DEFAULT NULL DEFAULT '',
  `payee_id` varchar(100) DEFAULT NULL DEFAULT '',
  `original_amount` float(10,2) DEFAULT 0,
  `coupon_discount` float(10,2) NOT NULL DEFAULT 0,
  `coupon_discount_details` text,
  `amount` float(10,2) NOT NULL DEFAULT 0,
  `coupon_code` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(100) DEFAULT NULL DEFAULT '',
  `processor` varchar(100) DEFAULT NULL DEFAULT '',
  `ip_address` varchar(50) DEFAULT NULL DEFAULT '',
  `extra` text,
  `order_tax` float(10,2) DEFAULT 0,
  `order_tax_details` text,
  `customer_note` text,
  `accept_terms` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`),
  KEY `course_id` (`course_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_order_items`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(15) NOT NULL DEFAULT 0,
  `course_id` int(15) NOT NULL DEFAULT 0,
  `plan_id` int(11) NOT NULL DEFAULT 0,
  `amount` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Table structure for table `#__tjlms_scorm`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL DEFAULT 0,
  `package` varchar(255) NOT NULL DEFAULT '',
  `storage` varchar(40) NOT NULL DEFAULT 'local',
  `scormtype` varchar(20) NOT NULL DEFAULT '',
  `version` varchar(20) NOT NULL DEFAULT '',
  `grademethod` int(20) NOT NULL DEFAULT 0,
  `passing_score` int(20) NOT NULL DEFAULT 0,
  `entry` int(11) NOT NULL DEFAULT 0,
  `launch` int(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_scoes`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_scoes` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scorm_id` bigint(10) NOT NULL DEFAULT 0,
  `manifest` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `organization` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `parent` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `launch` longtext COLLATE utf8_unicode_ci NOT NULL,
  `scormtype` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `scorm_id` (`scorm_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_scoes_data`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_scoes_data` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT 0,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='Contains variable data get from packages' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_scoes_track`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_scoes_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0,
  `scorm_id` int(11) NOT NULL DEFAULT 0,
  `sco_id` int(11) NOT NULL DEFAULT 0,
  `attempt` int(11) NOT NULL DEFAULT 1,
  `element` varchar(255) NOT NULL DEFAULT '',
  `value` text,
  `timemodified` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `scoes_track` (`userid`,`scorm_id`,`sco_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_seq_mapinfo`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_mapinfo` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT 0,
  `objectiveid` bigint(10) NOT NULL DEFAULT 0,
  `targetobjectiveid` bigint(10) NOT NULL DEFAULT 0,
  `readsatisfiedstatus` tinyint(1) NOT NULL DEFAULT 1,
  `readnormalizedmeasure` tinyint(1) NOT NULL DEFAULT 1,
  `writesatisfiedstatus` tinyint(1) NOT NULL DEFAULT 0,
  `writenormalizedmeasure` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 objective mapinfo description' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_seq_objective`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_objective` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT 0,
  `primaryobj` tinyint(1) NOT NULL DEFAULT 0,
  `objectiveid` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `satisfiedbymeasure` tinyint(1) NOT NULL DEFAULT 1,
  `minnormalizedmeasure` float(11,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 objective description' AUTO_INCREMENT=1;

-- --------------------------------------------------------


--
-- Table structure for table `#__tjlms_scorm_seq_rolluprule`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_rolluprule` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT 0,
  `childactivityset` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `minimumcount` bigint(10) NOT NULL DEFAULT 0,
  `minimumpercent` float(11,4) NOT NULL DEFAULT '0.0000',
  `conditioncombination` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
  `action` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 sequencing rule' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_seq_rolluprulecond`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_rolluprulecond` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT 0,
  `rollupruleid` bigint(10) NOT NULL DEFAULT 0,
  `operator` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'noOp',
  `cond` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 sequencing rule' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `demo_tjlms_scorm_seq_rulecond`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_rulecond` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT 0,
  `ruleconditionsid` bigint(10) NOT NULL DEFAULT 0,
  `referencedobjective` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `measurethreshold` float(11,4) NOT NULL DEFAULT '0.0000',
  `operator` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'noOp',
  `cond` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'always',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 rule condition' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_seq_ruleconds`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_ruleconds` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT 0,
  `conditioncombination` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
  `ruletype` tinyint(2) NOT NULL DEFAULT 0,
  `action` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 rule conditions' AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__tjlms_storage_s3`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_storage_s3` (
  `storageid` varchar(255) NOT NULL DEFAULT '',
  `resource_path` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `storageid` (`storageid`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_subscription_plans`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_subscription_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(140) NOT NULL DEFAULT '',
  `course_id` int(11) NOT NULL DEFAULT 0,
  `time_measure` varchar(50) NOT NULL DEFAULT '',
  `price` float(10,2) NOT NULL DEFAULT 0,
  `duration` int(10) NOT NULL DEFAULT 0,
  `access` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_users`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `user_email` varchar(255) NOT NULL DEFAULT '',
  `address_type` varchar(11) NOT NULL DEFAULT '',
  `firstname` varchar(250) NOT NULL DEFAULT '',
  `lastname` varchar(250) NOT NULL DEFAULT '',
  `vat_number` varchar(250) NOT NULL DEFAULT '',
  `tax_exempt` tinyint(4) NOT NULL DEFAULT 0,
  `country_code` varchar(11) NOT NULL DEFAULT '',
  `address` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state_code` varchar(11) NOT NULL DEFAULT '',
  `zipcode` varchar(255) NOT NULL DEFAULT '',
  `country_mobile_code` tinyint(6) NOT NULL DEFAULT 0,
  `phone` varchar(50) NOT NULL DEFAULT '',
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='Tjlms User Information' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_tmtquiz`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_tmtquiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL DEFAULT 0,
  `test_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `test_id` (`test_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='Tjlms relation between lesson & test' AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Table structure for table `__tjlms_dashboard`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_dashboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `plugin_name` varchar(255) NOT NULL DEFAULT '',
  `size` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `__tjlms_course_track`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_course_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `timestart` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeend` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `no_of_lessons` int(11) NOT NULL DEFAULT 0,
  `completed_lessons` int(11) NOT NULL DEFAULT 0,
  `status` varchar(40)  NOT NULL DEFAULT 'I',
  `last_accessed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cert_gen_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE INDEX unique_course_completion (course_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__tjlms_reports_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query_name` varchar(255) NOT NULL DEFAULT '',
  `colToshow` text,
  `filters` text,
  `sort` varchar(255) NOT NULL DEFAULT '',
  `report_name` varchar(255) NOT NULL DEFAULT '',
  `plugin_name` varchar(255) NOT NULL DEFAULT '',
  `creator_id` int(11) NOT NULL DEFAULT 0,
  `privacy` varchar(255) NOT NULL DEFAULT '',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_accessed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hash` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__tjlms_enrolled_users_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL DEFAULT 0,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------
-- Table structure for table `#__tjlms_certificate`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_certificate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cert_id` varchar(255) NOT NULL DEFAULT '',
  `type` VARCHAR(255) NULL DEFAULT 'course',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `certficate_src` varchar(50) NOT NULL DEFAULT '',
  `grant_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `exp_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_certificate` (`course_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
-- Table structure for table `#__tjlms_certificate_template`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_certificate_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` text,
  `template_css` text,
  `access` varchar(255) NOT NULL DEFAULT '',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_reminders`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_reminders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `modified_by` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `days` int(11) NOT NULL DEFAULT 0,
  `subject` varchar(600) NOT NULL DEFAULT '',
  `email_template` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_reminders_xref`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_reminders_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `reminder_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Table structure for table `#__tjlms_todos_reminder`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_todos_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `reminder_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Table structure for table `#__tjlms_migration`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_migration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(600) NOT NULL DEFAULT '',
  `action` varchar(600) NOT NULL DEFAULT '',
  `flag` tinyint(1) NOT NULL DEFAULT 0,
  `params` text,
  `migration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_set`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_title` varchar(255) NOT NULL DEFAULT '',
  `assessment_attempts` int(11) NOT NULL DEFAULT 0,
  `assessment_attempts_grade` int(11) NOT NULL DEFAULT 0,
  `assessment_answersheet` tinyint(1) NOT NULL DEFAULT 0,
  `answersheet_options` varchar(255) NOT NULL DEFAULT '',
  `allow_attachments` int(11) NOT NULL DEFAULT 0,
  `assessment_student_name` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessmentset_lesson_xref`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessmentset_lesson_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL DEFAULT 0,
  `set_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;


--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_rating_parameters`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_rating_parameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `value` int(11) NOT NULL DEFAULT 0,
  `description` text,
  `weightage` float( 10, 2 ) NOT NULL DEFAULT 0,
  `type` varchar(50) NOT NULL DEFAULT '',
  `allow_comment` int(11) NOT NULL DEFAULT 0,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_lesson_assessment_ratings`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_lesson_assessment_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_track_id` int(11) NOT NULL DEFAULT 0,
  `rating_id` int(11) NOT NULL DEFAULT 0,
  `rating_value` int(11) NOT NULL DEFAULT 0,
  `rating_comment` text,
  `reviewer_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_reviews`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_track_id` int(11) NOT NULL COMMENT 'FK to tjlms_lesson_track' DEFAULT 0,
  `reviewer_id` int(11) NOT NULL COMMENT 'reviewed user id' DEFAULT 0,
  `feedback` text COMMENT 'feedback/review of the user',
  `created_date` datetime NOT NULL COMMENT 'created date' DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime NOT NULL COMMENT 'modified date' DEFAULT '0000-00-00 00:00:00',
  `review_status` varchar(50) NOT NULL COMMENT 'value will be (draft / save)' DEFAULT '',
  `params` varchar(255) NOT NULL COMMENT 'params' DEFAULT '',
  `score` float( 10, 2 ) NOT NULL COMMENT 'score' DEFAULT 0,
  `modified_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
-- Table structure for table `#__tjlms_courses_lessons`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_courses_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL DEFAULT 0,
  `course_id` int(11) NOT NULL DEFAULT 0,
  `mod_id` int(11) NOT NULL DEFAULT 0,
  `free_lesson` tinyint(4) NOT NULL DEFAULT 0,
  `consider_marks` tinyint(4) NOT NULL DEFAULT 0,
  `eligibility_criteria` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lesson_mapping` (`lesson_id`,`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

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
