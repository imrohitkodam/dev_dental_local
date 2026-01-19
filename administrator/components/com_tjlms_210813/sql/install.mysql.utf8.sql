--
-- Table structure for table `#__tjlms_activities`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `element` text NOT NULL,
  `element_id` int(11) NOT NULL,
  `element_url` text NOT NULL,
  `added_time` datetime NOT NULL,
  `params` text NOT NULL,
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
  `lesson_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lbeta_tjlms_assignments`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assign_to` int(11) NOT NULL COMMENT 'User to whom the course has been assign',
  `assign_by` int(11) NOT NULL COMMENT 'User who assign this course to another user',
  `start_date` datetime NOT NULL,
  `deu_date` datetime NOT NULL,
  `param` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_coupons`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0',
  `course_id` varchar(255) NOT NULL,
  `subscription_id` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `value` float( 10, 2 ) UNSIGNED NOT NULL ,
  `val_type` varchar(255) NOT NULL,
  `max_use` varchar(255) NOT NULL,
  `max_per_user` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `couponParams` text NOT NULL,
  `from_date` datetime NOT NULL,
  `exp_date` datetime NOT NULL,
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
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `catid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  `short_desc` text NOT NULL,
  `access` int(11) NOT NULL,
  `description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `certificate_term` varchar(255) NOT NULL,
  `certificate_id` int(11) NOT NULL DEFAULT '0',
  `expiry` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL DEFAULT '0' COMMENT 'Course free = 0, paid = 1',
  `group_id` int(11) NOT NULL DEFAULT '0',
  `params` text DEFAULT NULL,
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
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `enrolled_on_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `enrolled_by` int(100) NOT NULL,
  `modified_time` datetime NOT NULL,
  `state` int(11) NOT NULL,
  `unlimited_plan` tinyint(1) NOT NULL DEFAULT '0',
  `before_expiry_mail` tinyint(1) NOT NULL DEFAULT '0',
  `after_expiry_mail` tinyint(1) NOT NULL DEFAULT '0',
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
  `filename` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_file_download_stats`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_file_download_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `downloads` text NOT NULL,
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_lessons`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_lessons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `catid` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `mod_id` int(11) NOT NULL,
  `short_desc` text NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  `free_lesson` varchar(255) NOT NULL,
  `no_of_attempts` varchar(255) NOT NULL,
  `attempts_grade` varchar(255) NOT NULL,
  `consider_marks` varchar(255) NOT NULL,
  `format` varchar(255) NOT NULL,
  `media_id` int(11) NOT NULL,
  `eligibility_criteria` varchar(255) NOT NULL,
  `ideal_time` int(11) NOT NULL,
  `resume` int(11) NOT NULL DEFAULT 1,
  `total_marks` int(11) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  `in_lib` TINYINT(1) NOT NULL DEFAULT '0',
  `params` TEXT NOT NULL,
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
  `lesson_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempt` int(11) NOT NULL,
  `timestart` datetime NOT NULL,
  `timeend` datetime NOT NULL,
  `score` int(11) NOT NULL,
  `lesson_status` text NOT NULL,
  `last_accessed_on` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `total_content` float NOT NULL,
  `current_position` float NOT NULL,
  `time_spent` time NOT NULL,
  `live` tinyint NOT NULL,
  `modified_by` int(128) NOT NULL,
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
  `format` varchar(255) NOT NULL,
  `sub_format` varchar(255) NOT NULL COMMENT 'For video format',
  `org_filename` varchar(255) NOT NULL,
  `saved_filename` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `storage` varchar(50) NOT NULL DEFAULT 'local',
  `source` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_modules`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_modules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `course_id` int(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `storage` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `#__tjlms_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(23) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cdate` datetime DEFAULT NULL,
  `mdate` datetime DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payee_id` varchar(100) DEFAULT NULL,
  `original_amount` float(10,2) DEFAULT NULL,
  `coupon_discount` float(10,2) NOT NULL,
  `coupon_discount_details` text NOT NULL,
  `amount` float(10,2) NOT NULL,
  `coupon_code` varchar(100) NOT NULL,
  `status` varchar(100) DEFAULT NULL,
  `processor` varchar(100) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `extra` text,
  `order_tax` float(10,2) DEFAULT NULL,
  `order_tax_details` text NOT NULL,
  `customer_note` text NOT NULL,
  `accept_terms` int(11) NOT NULL,
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
  `order_id` int(15) NOT NULL,
  `course_id` int(15) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Table structure for table `#__tjlms_scorm`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `package` varchar(255) NOT NULL,
  `storage` varchar(40) NOT NULL DEFAULT 'local',
  `scormtype` varchar(20) NOT NULL,
  `version` varchar(20) NOT NULL,
  `grademethod` int(20) NOT NULL,
  `passing_score` int(20) NOT NULL,
  `entry` int(11) NOT NULL,
  `launch` int(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_scoes`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_scoes` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `scorm_id` bigint(10) NOT NULL DEFAULT '0',
  `manifest` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
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
  `sco_id` bigint(10) NOT NULL DEFAULT '0',
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
  `userid` int(11) NOT NULL DEFAULT '0',
  `scorm_id` int(11) NOT NULL DEFAULT '0',
  `sco_id` int(11) NOT NULL,
  `attempt` int(11) NOT NULL DEFAULT '1',
  `element` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `timemodified` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `scoes_track` (`userid`,`scorm_id`,`sco_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_seq_mapinfo`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_mapinfo` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT '0',
  `objectiveid` bigint(10) NOT NULL DEFAULT '0',
  `targetobjectiveid` bigint(10) NOT NULL DEFAULT '0',
  `readsatisfiedstatus` tinyint(1) NOT NULL DEFAULT '1',
  `readnormalizedmeasure` tinyint(1) NOT NULL DEFAULT '1',
  `writesatisfiedstatus` tinyint(1) NOT NULL DEFAULT '0',
  `writenormalizedmeasure` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 objective mapinfo description' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_scorm_seq_objective`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_objective` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT '0',
  `primaryobj` tinyint(1) NOT NULL DEFAULT '0',
  `objectiveid` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `satisfiedbymeasure` tinyint(1) NOT NULL DEFAULT '1',
  `minnormalizedmeasure` float(11,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 objective description' AUTO_INCREMENT=1;

-- --------------------------------------------------------


--
-- Table structure for table `#__tjlms_scorm_seq_rolluprule`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_scorm_seq_rolluprule` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `sco_id` bigint(10) NOT NULL DEFAULT '0',
  `childactivityset` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `minimumcount` bigint(10) NOT NULL DEFAULT '0',
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
  `sco_id` bigint(10) NOT NULL DEFAULT '0',
  `rollupruleid` bigint(10) NOT NULL DEFAULT '0',
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
  `sco_id` bigint(10) NOT NULL DEFAULT '0',
  `ruleconditionsid` bigint(10) NOT NULL DEFAULT '0',
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
  `sco_id` bigint(10) NOT NULL DEFAULT '0',
  `conditioncombination` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'all',
  `ruletype` tinyint(2) NOT NULL DEFAULT '0',
  `action` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='SCORM2004 rule conditions' AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__tjlms_storage_s3`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_storage_s3` (
  `storageid` varchar(255) NOT NULL,
  `resource_path` varchar(255) NOT NULL,
  UNIQUE KEY `storageid` (`storageid`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_subscription_plans`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_subscription_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(140) NOT NULL,
  `course_id` int(11) NOT NULL,
  `time_measure` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `duration` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_users`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `address_type` varchar(11) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `vat_number` varchar(250) NOT NULL,
  `tax_exempt` tinyint(4) NOT NULL,
  `country_code` varchar(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state_code` varchar(11) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='Tjlms User Information' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_tmtquiz`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_tmtquiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
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
  `user_id` int(11) NOT NULL,
  `plugin_name` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
  `params` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `__tjlms_course_track`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_course_track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestart` datetime NOT NULL,
  `timeend` datetime NOT NULL,
  `no_of_lessons` int(11) NOT NULL,
  `completed_lessons` int(11) NOT NULL,
  `status` varchar(40)  NOT NULL DEFAULT 'I',
  `last_accessed_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cert_gen_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE INDEX unique_course_completion (course_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__tjlms_reports_queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `query_name` varchar(255) NOT NULL,
  `colToshow` text NOT NULL,
  `filters` text NOT NULL,
  `sort` varchar(255) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `plugin_name` varchar(255) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `privacy` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_accessed_on` datetime NOT NULL,
  `hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__tjlms_enrolled_users_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------
-- Table structure for table `#__tjlms_certificate`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_certificate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cert_id` varchar(255) NOT NULL,
  `type` VARCHAR(255) NULL DEFAULT 'course',
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `certficate_src` varchar(50) NOT NULL,
  `grant_date` datetime NOT NULL,
  `exp_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_certificate` (`course_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------
-- Table structure for table `#__tjlms_certificate_template`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_certificate_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `template_css` text NULL,
  `access` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_reminders`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_reminders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `days` int(11) NOT NULL,
  `subject` varchar(600) NOT NULL,
  `email_template` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `#__tjlms_reminders_xref`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_reminders_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `reminder_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Table structure for table `#__tjlms_todos_reminder`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_todos_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `reminder_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Table structure for table `#__tjlms_migration`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_migration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(600) NOT NULL,
  `action` varchar(600) NOT NULL,
  `flag` tinyint(1) NOT NULL,
  `params` text NOT NULL,
  `migration_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_set`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_title` varchar(255) NOT NULL,
  `assessment_attempts` int(11) NOT NULL,
  `assessment_attempts_grade` int(11) NOT NULL,
  `assessment_answersheet` tinyint(1) NOT NULL,
  `answersheet_options` varchar(255) NOT NULL,
  `allow_attachments` int(11) NOT NULL,
  `assessment_student_name` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessmentset_lesson_xref`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessmentset_lesson_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `set_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;


--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_rating_parameters`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_rating_parameters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `description` text NOT NULL,
  `weightage` float( 10, 2 ) NOT NULL,
  `type` varchar(50) NOT NULL,
  `allow_comment` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_lesson_assessment_ratings`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_lesson_assessment_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_track_id` int(11) NOT NULL,
  `rating_id` int(11) NOT NULL,
  `rating_value` int(11) NOT NULL,
  `rating_comment` text NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_reviews`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_track_id` int(11) NOT NULL COMMENT 'FK to tjlms_lesson_track  ',
  `reviewer_id` int(11) NOT NULL COMMENT 'reviewed user id',
  `feedback` text NOT NULL COMMENT 'feedback/review of the user',
  `created_date` datetime NOT NULL COMMENT 'created date',
  `modified_date` datetime NOT NULL COMMENT 'modified date',
  `review_status` varchar(50) NOT NULL COMMENT 'value will be (draft / save)',
  `params` varchar(255) NOT NULL COMMENT 'params',
  `score` float( 10, 2 ) NOT NULL COMMENT 'score',
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--
-- Table structure for table `#__tjlms_courses_lessons`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_courses_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `mod_id` int(11) NOT NULL,
  `free_lesson` tinyint(4) NOT NULL,
  `consider_marks` tinyint(4) NOT NULL,
  `eligibility_criteria` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lesson_mapping` (`lesson_id`,`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
