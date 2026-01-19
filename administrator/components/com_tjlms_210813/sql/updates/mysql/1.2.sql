update `#__menu` set `link`="index.php?option=com_tjreports&client=com_tjlms&task=reports.defaultReport" where `link`="index.php?option=com_tjlms&view=reports&reportToBuild=userreport" AND `client_id`=1;
update `#__tjlms_lesson_track` set `lesson_status`="incomplete" where `lesson_status`="incompleted";
UPDATE `#__menu` SET `link` = 'index.php?option=com_tjlms&view=courses&courses_to_show=liked' WHERE `link` = 'index.php?option=com_tjlms&view=courses&layout=liked';
UPDATE `#__menu` SET `link` = 'index.php?option=com_tjlms&view=courses&courses_to_show=enrolled' WHERE `link` = 'index.php?option=com_tjlms&view=courses&layout=my';
