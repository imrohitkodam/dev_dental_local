--
-- Installation package for Notes.
--
-- @package		Notes
CREATE TABLE IF NOT EXISTS `#__social_notes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `title` text NOT NULL,
  `alias` text,
  `content` text NOT NULL,
  `created` datetime NOT NULL,
  `params` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
