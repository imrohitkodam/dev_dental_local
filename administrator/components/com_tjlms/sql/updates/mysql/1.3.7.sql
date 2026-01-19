ALTER TABLE `#__tjlms_lessons` ADD `in_lib` TINYINT(1) NOT NULL DEFAULT 1;
ALTER TABLE `#__tjlms_lessons` ADD `catid` INT(10) NOT NULL DEFAULT 0 AFTER `alias`;
-- --------------------------------------------------------

--
-- Table structure for table `nfuv2_tjlms_courses_lessons`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `#__tjlms_lessons` CHANGE `free_lesson` `free_lesson` TINYINT NOT NULL DEFAULT 0;
ALTER TABLE `#__tjlms_lessons` CHANGE `consider_marks` `consider_marks` TINYINT NOT NULL DEFAULT 0;

INSERT INTO `#__tjlms_courses_lessons`(`lesson_id`, `course_id`, `mod_id`, `free_lesson`, `consider_marks`, `eligibility_criteria`)
SELECT `id`, `course_id`, `mod_id`, `free_lesson`, `consider_marks`, `eligibility_criteria` FROM `#__tjlms_lessons` WHERE `id` NOT IN (SELECT `lesson_id` from `#__tjlms_courses_lessons`);

