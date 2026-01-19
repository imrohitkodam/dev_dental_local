CREATE TABLE IF NOT EXISTS `#__ppinstaller_config` (
   `config_id` int(11) NOT NULL AUTO_INCREMENT,
   `key` varchar(255) NOT NULL,
   `value` text,
    PRIMARY KEY (`config_id`),
    UNIQUE KEY `idx_key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
