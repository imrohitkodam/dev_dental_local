ALTER TABLE `#__tmt_questions` ADD `alias` VARCHAR(255) NOT NULL AFTER `title`;
UPDATE `#__tmt_questions` SET alias = SUBSTRING(MD5(RAND()) FROM 1 FOR 10) where alias is NULL OR alias='';
ALTER TABLE `#__tmt_questions` ADD  UNIQUE (`alias`);
