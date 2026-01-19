ALTER TABLE `#__tjlms_coupons` change params couponParams text;
ALTER TABLE `#__tjlms_lessons` change despcription description text;
ALTER TABLE `#__tjlms_scorm_seq_rulecond` change refrencedobjective referencedobjective varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `#__tjlms_lessons` change name title varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__tjlms_lesson_assessment_ratings` CHANGE `rating_comment` `rating_comment` TEXT;
