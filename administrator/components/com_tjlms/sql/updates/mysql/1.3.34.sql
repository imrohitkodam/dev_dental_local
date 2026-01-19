ALTER TABLE `#__tmt_tests`  ADD `show_correct_answer` TINYINT NOT NULL DEFAULT 1 COMMENT 'show correct answer' AFTER `answer_sheet`;
ALTER TABLE `#__tmt_tests`  ADD `print_answersheet` TINYINT NOT NULL DEFAULT 1 COMMENT 'show print answer sheet' AFTER `show_correct_answer`;
