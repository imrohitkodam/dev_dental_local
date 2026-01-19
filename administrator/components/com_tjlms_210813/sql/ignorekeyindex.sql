ALTER TABLE `#__tjlms_course_track` ADD UNIQUE unique_course_completion (course_id, user_id);
ALTER TABLE `#__tjlms_lesson_track` ADD UNIQUE unique_lesson_entry (lesson_id, user_id, attempt);
ALTER TABLE `#__tmt_tests_answers` ADD UNIQUE unique_answer (question_id,user_id,invite_id);
ALTER TABLE `#__tmt_tests_attendees` ADD UNIQUE unique_test_attendee (invite_id, user_id);
ALTER TABLE `#__tjlms_enrolled_users` ADD UNIQUE unique_enrollment (course_id, user_id);
ALTER TABLE `#__tjlms_coupons` ADD UNIQUE coupon_code (code);
ALTER TABLE `#__tmt_tests_questions` DROP INDEX unique_test_question;
ALTER TABLE `#__tmt_tests_questions` ADD UNIQUE unique_test_question (test_id, section_id, question_id);

