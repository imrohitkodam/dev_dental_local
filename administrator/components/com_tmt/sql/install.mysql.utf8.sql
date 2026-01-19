-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_answers`
--

CREATE TABLE IF NOT EXISTS `#__tmt_answers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `answer` text,
  `marks` int(3) NOT NULL DEFAULT 0,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(3) NOT NULL DEFAULT 0,
  `comments` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_questions`
--

CREATE TABLE IF NOT EXISTS `#__tmt_questions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ordering` int(11) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` text,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `type` varchar(255) NOT NULL DEFAULT '',
  `level` varchar(255) NOT NULL DEFAULT '',
  `marks` int(3) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `ideal_time` int(3) NOT NULL DEFAULT 0,
  `gradingtype` varchar(255) NOT NULL DEFAULT '',
  `category_id` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_tests`
--

CREATE TABLE IF NOT EXISTS `#__tmt_tests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(100) NOT NULL DEFAULT 'plain',
  `ordering` int(11) NOT NULL DEFAULT 0,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL DEFAULT 0,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT 0,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `description` text,
  `reviewers` text,
  `show_time` tinyint(1) NOT NULL DEFAULT 0,
  `time_duration` int(11) NOT NULL DEFAULT 0,
  `show_time_finished` tinyint(1) NOT NULL DEFAULT 0,
  `time_finished_duration` int(11) NOT NULL DEFAULT 0,
  `total_marks` int(11) NOT NULL DEFAULT 0,
  `passing_marks` int(11) NOT NULL DEFAULT 0,
  `isObjective` tinyint(1) NOT NULL DEFAULT 0,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `termscondi` tinyint(1) NOT NULL DEFAULT 0,
  `answer_sheet` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'show answer sheet',
  `show_correct_answer` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'show correct answer',
  `print_answersheet` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'show print answer sheet',
  `questions_shuffle` tinyint(1) NOT NULL DEFAULT 0,
  `answers_shuffle` tinyint(1) NOT NULL DEFAULT 0,
  `gradingtype` VARCHAR(15) NOT NULL DEFAULT '',
  `show_thankyou_page` TINYINT(1) NOT NULL DEFAULT 1,
  `show_all_questions` tinyint(1) DEFAULT 0,
  `pagination_limit` int(4) NOT NULL DEFAULT 0,
  `show_questions_overview` tinyint(1) NOT NULL DEFAULT 0,
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_tests_answers`
--

CREATE TABLE IF NOT EXISTS `#__tmt_tests_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `test_id` int(11) NOT NULL DEFAULT 0,
  `invite_id` int(11) NOT NULL DEFAULT 0 COMMENT 'p key of invite xref',
  `answer` text,
  `anss_order` varchar(255) NOT NULL DEFAULT '',
  `marks` int(11) NOT NULL DEFAULT 0,
  `flagged` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX unique_answer (question_id, invite_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_tests_attendees`
--

CREATE TABLE IF NOT EXISTS `#__tmt_tests_attendees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invite_id` int(11) NOT NULL DEFAULT 0 COMMENT 'p key of common invites table',
  `test_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `result_status` varchar(50) NOT NULL DEFAULT '',
  `score` int(11) NOT NULL DEFAULT 0,
  `attempt_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 - interrupted, 1 – complete, 2 - rejected',
  `review_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 – draft, 1 – complete',
  `time_taken` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX unique_test_attendee (invite_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_tests_photo_captures`
--

CREATE TABLE IF NOT EXISTS `#__tmt_tests_photo_captures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `invite_id` int(11) NOT NULL DEFAULT 0 COMMENT 'p key of invite xref',
  `image` text,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_tests_questions`
--

CREATE TABLE IF NOT EXISTS `#__tmt_tests_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL DEFAULT 0,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `is_compulsory` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_tests_reviewers`
--

CREATE TABLE IF NOT EXISTS `#__tmt_tests_reviewers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `company_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_answers_image`
--

CREATE TABLE IF NOT EXISTS `#__tmt_answers_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a_id` int(11) unsigned NOT NULL DEFAULT 0,
  `q_id` int(11) NOT NULL DEFAULT 0,
  `img_title` mediumtext,
  `img_path` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__tmt_questions_image`
--

CREATE TABLE IF NOT EXISTS `#__tmt_questions_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `q_id` int(11) unsigned NOT NULL DEFAULT 0,
  `img_title` mediumtext,
  `img_path` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

--
-- Table structure for table `#__tmt_quiz_rules`
--

CREATE TABLE IF NOT EXISTS `#__tmt_quiz_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL DEFAULT 0,
  `section_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `order` int(11) NOT NULL DEFAULT 0,
  `questions_count` int(11) NOT NULL DEFAULT 0,
  `pull_questions_count` int(11) NOT NULL DEFAULT 0,
  `marks` int(11) NOT NULL DEFAULT 0,
  `category` int(11) NOT NULL DEFAULT 0,
  `difficulty_level` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `question_type` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

 --
 -- Sections Tables : Table structure for table `#__tmt_tests_sections`
 --

 CREATE TABLE IF NOT EXISTS `#__tmt_tests_sections` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL DEFAULT '',
   `description` TEXT,
   `test_id` int(11) NOT NULL DEFAULT 0,
   `ordering` int(11) NOT NULL DEFAULT 0,
   `state` tinyint(1) NOT NULL DEFAULT 1,
   `min_questions` int(11) NOT NULL DEFAULT 0,
   `max_questions` int(11) NOT NULL DEFAULT 0,
   PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
