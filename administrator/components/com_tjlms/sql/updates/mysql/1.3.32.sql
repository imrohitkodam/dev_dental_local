UPDATE `#__menu` SET `component_id` = (SELECT `extension_id` FROM `#__extensions` WHERE `element` = 'com_tjcertificate') WHERE `link` = 'index.php?option=com_tjlms&view=certificates&layout=my';
UPDATE `#__menu` SET `link` = 'index.php?option=com_tjcertificate&view=certificates&layout=my' WHERE `link` = 'index.php?option=com_tjlms&view=certificates&layout=my';
