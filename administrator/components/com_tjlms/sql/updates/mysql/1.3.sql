CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_title` varchar(255) NOT NULL DEFAULT '',
  `assessment_attempts` int(11) NOT NULL DEFAULT 0,
  `assessment_attempts_grade` int(11) NOT NULL DEFAULT 0,
  `assessment_answersheet` tinyint(1) NOT NULL DEFAULT 0,
  `answersheet_options` varchar(255) NOT NULL DEFAULT '',
  `allow_attachments` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessmentset_lesson_xref`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessmentset_lesson_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL DEFAULT 0,
  `set_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


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
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

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
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_reviews`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_track_id` int(11) NOT NULL DEFAULT 0 COMMENT 'FK to tjlms_lesson_track	',
  `reviewer_id` int(11) NOT NULL DEFAULT 0 COMMENT 'reviewed user id',
  `feedback` text COMMENT 'feedback/review of the user',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'created date',
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'modified date',
  `review_status` varchar(50) NOT NULL DEFAULT '' COMMENT 'value will be (draft / save)',
  `params` varchar(255) NOT NULL DEFAULT '' COMMENT 'params',
  `score` float( 10, 2 ) NOT NULL DEFAULT 0 COMMENT 'score',
  `modified_by` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

 --
 -- Sections Tables : Table structure for table `#__tmt_tests_sections`
 --

 CREATE TABLE IF NOT EXISTS `#__tmt_tests_sections` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL DEFAULT '',
   `description` varchar(255) NOT NULL DEFAULT '',
   `test_id` int(11) NOT NULL DEFAULT 0,
   `ordering` int(11) NOT NULL DEFAULT 0,
   `state` tinyint(1) NOT NULL DEFAULT 1,
   `min_questions` int(11) NOT NULL DEFAULT 0,
   `max_questions` int(11) NOT NULL DEFAULT 0,
   PRIMARY KEY (`id`)
 )DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `#__tmt_tests` DROP `notify_candidate_passed`;
ALTER TABLE `#__tmt_tests` DROP `notify_candidate_failed`;
ALTER TABLE `#__tmt_tests` DROP `notify_admin`;
ALTER TABLE `#__tmt_tests` ADD `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_on`;
ALTER TABLE `#__tmt_tests` ADD `gradingtype` varchar(255) NOT NULL DEFAULT '';
UPDATE `#__tmt_tests` SET `gradingtype` = 'quiz'  where `gradingtype` is NULL OR `gradingtype` = '';
ALTER TABLE `#__tjlms_lessons` ADD `resume` int(11) NOT NULL DEFAULT 1 AFTER `ideal_time`;
ALTER TABLE `#__tjlms_lessons` ADD `total_marks` int(11) NOT NULL DEFAULT 0 AFTER `resume`;
ALTER TABLE `#__tjlms_lessons` ADD `passing_marks` int(11) NOT NULL DEFAULT 0 AFTER `total_marks`;
ALTER TABLE `#__tmt_tests_questions` ADD `section_id` INT(11) NOT NULL DEFAULT 0 AFTER `test_id`;
ALTER TABLE `#__tmt_questions` ADD `gradingtype` varchar(255) NOT NULL DEFAULT '' AFTER `ideal_time`;
UPDATE `#__tmt_questions` SET `gradingtype` = 'quiz'  where `gradingtype` is NULL OR `gradingtype` = '';
ALTER TABLE `#__tmt_quiz_rules` ADD `section_id` INT(11) NOT NULL DEFAULT 0 AFTER `quiz_id`;
ALTER TABLE `#__tjlms_lesson_track` ADD `modified_by` INT(11) NOT NULL DEFAULT 0 AFTER `time_spent`;
ALTER TABLE `#__tmt_tests` ALTER COLUMN type SET DEFAULT 'plain';
UPDATE `#__tmt_tests` SET `type` = 'plain'  where `type` is NULL OR `type` = '';

