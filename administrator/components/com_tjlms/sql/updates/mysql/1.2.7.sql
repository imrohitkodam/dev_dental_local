ALTER TABLE `#__tmt_quiz_rules` ADD `pull_questions_count` int(11) NOT NULL DEFAULT 0 AFTER `questions_count`;
UPDATE `#__tmt_quiz_rules` SET pull_questions_count = questions_count * 2  where pull_questions_count is NULL OR pull_questions_count='';
