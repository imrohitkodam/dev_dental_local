ALTER TABLE `#__tjlms_lessons` DROP KEY `lesson_alias`;
ALTER TABLE `#__tjlms_lessons` ADD KEY `lesson_alias` (`alias`(191));
ALTER TABLE `#__tjlms_courses` DROP KEY `course_alias`;
ALTER TABLE `#__tjlms_courses` ADD KEY `course_alias` (`alias`(191));
ALTER TABLE `#__tjlms_storage_s3` DROP KEY `storageid`;
ALTER TABLE `#__tjlms_storage_s3` ADD UNIQUE KEY `storageid` (`storageid`(191));
