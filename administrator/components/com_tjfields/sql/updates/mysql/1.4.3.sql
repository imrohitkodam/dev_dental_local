ALTER TABLE `#__tj_country` ADD com_tjvendors tinyint(1)  NOT NULL DEFAULT '1' AFTER com_tjlms;
ALTER TABLE `#__tj_region` ADD com_tjvendors tinyint(1)  NOT NULL DEFAULT '1' AFTER com_tjlms;
ALTER TABLE `#__tj_city` ADD com_tjvendors tinyint(1)  NOT NULL DEFAULT '1' AFTER com_tjlms;
