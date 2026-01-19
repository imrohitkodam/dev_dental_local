CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assessment_title` varchar(255) NOT NULL,
  `assessment_attempts` int(11) NOT NULL,
  `assessment_attempts_grade` int(11) NOT NULL,
  `assessment_answersheet` tinyint(1) NOT NULL,
  `answersheet_options` varchar(255) NOT NULL,
  `allow_attachments` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessmentset_lesson_xref`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessmentset_lesson_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `set_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


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
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

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
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

--
-- Assessment Tables : Table structure for table `#__tjlms_assessment_reviews`
--

CREATE TABLE IF NOT EXISTS `#__tjlms_assessment_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_track_id` int(11) NOT NULL COMMENT 'FK to tjlms_lesson_track	',
  `reviewer_id` int(11) NOT NULL COMMENT 'reviewed user id',
  `feedback` text NOT NULL COMMENT 'feedback/review of the user',
  `created_date` datetime NOT NULL COMMENT 'created date',
  `modified_date` datetime NOT NULL COMMENT 'modified date',
  `review_status` varchar(50) NOT NULL COMMENT 'value will be (draft / save)',
  `params` varchar(255) NOT NULL COMMENT 'params',
  `score` float( 10, 2 ) NOT NULL COMMENT 'score',
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

 --
 -- Sections Tables : Table structure for table `#__tmt_tests_sections`
 --

 CREATE TABLE IF NOT EXISTS `#__tmt_tests_sections` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `title` varchar(255) NOT NULL,
   `description` varchar(255) NOT NULL,
   `test_id` int(11) NOT NULL,
   `ordering` int(11) NOT NULL,
   `state` tinyint(1) NOT NULL DEFAULT '1',
   `min_questions` int(11) NOT NULL,
   `max_questions` int(11) NOT NULL,
   PRIMARY KEY (`id`)
 )DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 

ALTER TABLE `#__tmt_tests` DROP `notify_candidate_passed`;
ALTER TABLE `#__tmt_tests` DROP `notify_candidate_failed`;
ALTER TABLE `#__tmt_tests` DROP `notify_admin`;
ALTER TABLE `#__tmt_tests` ADD `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_on`;
ALTER TABLE `#__tmt_tests` ADD `gradingtype` varchar(255) NOT NULL;
UPDATE `#__tmt_tests` SET `gradingtype` = 'quiz'  where `gradingtype` is NULL OR `gradingtype` = '';
ALTER TABLE `#__tjlms_lessons` ADD `resume` int(11) NOT NULL DEFAULT 1 AFTER `ideal_time`;
ALTER TABLE `#__tjlms_lessons` ADD `total_marks` int(11) NOT NULL AFTER `resume`;
ALTER TABLE `#__tjlms_lessons` ADD `passing_marks` int(11) NOT NULL AFTER `total_marks`;
ALTER TABLE `#__tmt_tests_questions` ADD `section_id` INT(11) NOT NULL AFTER `test_id`;
ALTER TABLE `#__tmt_questions` ADD `gradingtype` varchar(255) NOT NULL AFTER `ideal_time`;
UPDATE `#__tmt_questions` SET `gradingtype` = 'quiz'  where `gradingtype` is NULL OR `gradingtype` = '';
ALTER TABLE `#__tmt_quiz_rules` ADD `section_id` INT(11) NOT NULL AFTER `quiz_id`;
ALTER TABLE `#__tjlms_lesson_track` ADD `modified_by` INT(11) NOT NULL AFTER `time_spent`;
ALTER TABLE `#__tmt_tests` ALTER COLUMN type SET DEFAULT 'plain';
UPDATE `#__tmt_tests` SET `type` = 'plain'  where `type` is NULL OR `type` = '';

