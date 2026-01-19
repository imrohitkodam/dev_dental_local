ALTER TABLE `#__tjlms_certificate` ALTER `type` SET DEFAULT 'course';
UPDATE `#__tjlms_certificate` SET `type` = 'course';
