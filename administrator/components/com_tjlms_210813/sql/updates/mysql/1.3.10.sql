SET SQL_MODE='ALLOW_INVALID_DATES';

ALTER TABLE `#__tmt_tests` ADD `show_all_questions` TINYINT(1) NOT NULL DEFAULT '1' AFTER `show_thankyou_page`;
ALTER TABLE `#__tmt_tests` ADD `pagination_limit` INT(4) NOT NULL AFTER `show_all_questions`;
ALTER TABLE `#__tmt_tests` ADD `show_questions_overview` TINYINT(1) NOT NULL DEFAULT '0' AFTER `pagination_limit`;
ALTER TABLE `#__tmt_tests_answers` ADD `flagged` TINYINT(1) NOT NULL DEFAULT '0' AFTER `marks`;
ALTER TABLE `#__tjlms_courses` CHANGE `params` `params` TEXT DEFAULT NULL;