DROP TABLE IF EXISTS `#__autotweet_channeltypes`;
CREATE TABLE IF NOT EXISTS `#__autotweet_channeltypes` (
  `id` int(11) NOT NULL,
  `name` varchar(64),
  `description` varchar(1024),
  `max_chars` int(4),
  `joocial_exclusive` tinyint(1),
  `frontend` tinyint(1),
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    1,
    'Twitter',
    'COM_AUTOTWEET_CHANNEL_TWITTER_DESC',
    280,
    0,
    1
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    2,
    'Facebook',
    'COM_AUTOTWEET_CHANNEL_FACEBOOK_DESC',
    420,
    0,
    1
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    3,
    'Mail',
    'COM_AUTOTWEET_CHANNEL_MAIL_DESC',
    16384,
    0,
    1
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    6,
    '― Deprecated ― LinkedIn Group',
    'COM_AUTOTWEET_CHANNEL_LINKEDINGROUP_DESC',
    1300,
    0,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    8,
    '― Deprecated ― Facebook Photo',
    'COM_AUTOTWEET_CHANNEL_FACEBOOKPHOTO_DESC',
    420,
    0,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    11,
    '― Deprecated ― VK',
    'COM_AUTOTWEET_CHANNEL_VK_DESC',
    320,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    12,
    '― Deprecated ― VK Communities',
    'COM_AUTOTWEET_CHANNEL_VK_DESC',
    320,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    14,
    '― Deprecated ― Scoop.it',
    'COM_AUTOTWEET_CHANNEL_SCOOPIT_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    15,
    '― Deprecated ― Xing',
    'COM_AUTOTWEET_CHANNEL_XING_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    16,
    'Tumblr',
    'COM_AUTOTWEET_CHANNEL_TUMBLR_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    17,
    'Google Blogger',
    'COM_AUTOTWEET_CHANNEL_BLOGGER_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    18,
    '― Deprecated ― JomSocial',
    'COM_AUTOTWEET_CHANNEL_JOMSOCIAL_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    19,
    'EasySocial',
    'COM_AUTOTWEET_CHANNEL_EASYSOCIAL_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    20,
    'LinkedIn User',
    'COM_AUTOTWEET_CHANNEL_LIOAUTH2_DESC',
    1300,
    0,
    1
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    21,
    'LinkedIn Company',
    'COM_AUTOTWEET_CHANNEL_LINKEDINCOMPANY5015_DESC',
    1300,
    0,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    23,
    'Telegram',
    'COM_AUTOTWEET_CHANNEL_TELEGRAM_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    24,
    'Medium',
    'COM_AUTOTWEET_CHANNEL_MEDIUM_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    25,
    'Web Push - Pushwoosh',
    'COM_AUTOTWEET_CHANNEL_PUSHWOOSH_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    26,
    'Web Push - OneSignal',
    'COM_AUTOTWEET_CHANNEL_ONESIGNAL_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    27,
    'Push Notification - Pushwoosh',
    'COM_AUTOTWEET_CHANNEL_PUSHWOOSH_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    28,
    'Push Notification - OneSignal',
    'COM_AUTOTWEET_CHANNEL_ONESIGNAL_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    29,
    'Push Notification - PushAlert',
    'COM_AUTOTWEET_CHANNEL_PUSHALERT_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    30,
    'Google PageSpeed Insights',
    'COM_AUTOTWEET_CHANNEL_PAGESPEED_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    31,
    'Pinterest',
    'COM_AUTOTWEET_CHANNEL_PINTEREST_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    32,
    'Google My Business',
    'COM_AUTOTWEET_CHANNEL_MY_BUSINESS_DESC',
    420,
    1,
    0
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    33,
    'Instagram',
    'COM_AUTOTWEET_CHANNEL_INSTAGRAM_DESC',
    420,
    0,
    1
  );
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    34,
    'Telegram - Photo',
    'COM_AUTOTWEET_CHANNEL_TELEGRAM_PHOTO_DESC',
    200,
    0,
    1
  );

DROP TABLE IF EXISTS `#__autotweet_ruletypes`;
CREATE TABLE IF NOT EXISTS `#__autotweet_ruletypes` (
  `id` int(11) NOT NULL,
  `name` varchar(64),
  `description` varchar(512),
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    1,
    'category: IN',
    'COM_AUTOTWEET_RULE_CATEGORYIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    2,
    'category: NOT IN',
    'COM_AUTOTWEET_RULE_CATEGORYNOTIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (3, 'term: OR', 'COM_AUTOTWEET_RULE_TERMOR_DESC');
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    4,
    'term: AND',
    'COM_AUTOTWEET_RULE_TERMAND_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    5,
    'catch all not fits',
    'COM_AUTOTWEET_RULE_CATCHALLNOTFITS_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    6,
    'word term: OR',
    'COM_AUTOTWEET_RULE_WORDTERMOR_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    7,
    'word term: AND',
    'COM_AUTOTWEET_RULE_WORDTERMAND_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    8,
    'regular expression match',
    'COM_AUTOTWEET_RULE_REGEX_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    9,
    'term: NOT IN',
    'COM_AUTOTWEET_RULE_TERMNOTIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    10,
    'word term: NOT IN',
    'COM_AUTOTWEET_RULE_WORDTERMNOTIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    11,
    'author: IN',
    'COM_AUTOTWEET_RULE_AUTHORIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    12,
    'author: NOT IN',
    'COM_AUTOTWEET_RULE_AUTHORNOTIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    13,
    'catch all',
    'COM_AUTOTWEET_RULE_CATCHALL_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    14,
    'language: IN',
    'COM_AUTOTWEET_RULE_LANGUAGEIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    15,
    'language: NOT IN',
    'COM_AUTOTWEET_RULE_LANGUAGENOTIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    16,
    'access: IN',
    'COM_AUTOTWEET_RULE_ACCESSIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    17,
    'access: NOT IN',
    'COM_AUTOTWEET_RULE_ACCESSNOTIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    18,
    'channel scope: IS User',
    'COM_AUTOTWEET_RULE_SOURCEBACK_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    19,
    'channel scope: IS Site',
    'COM_AUTOTWEET_RULE_SOURCEBACK_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    20,
    'channel owner: IN Group',
    'COM_AUTOTWEET_RULE_SOURCEBACK_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    21,
    'channel owner: NOT IN Group',
    'COM_AUTOTWEET_RULE_SOURCEBACK_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    22,
    'author group: IN',
    'COM_AUTOTWEET_RULE_AUTHORIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    23,
    'author group: NOT IN',
    'COM_AUTOTWEET_RULE_AUTHORNOTIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    24,
    'featured: IS',
    'COM_AUTOTWEET_RULE_FEATURED_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    25,
    'featured: IS NOT',
    'COM_AUTOTWEET_RULE_FEATUREDISNOT_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    26,
    'media: HAS',
    'COM_AUTOTWEET_RULE_MEDIAHAS_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    27,
    'media: HAS NOT',
    'COM_AUTOTWEET_RULE_MEDIAHASNOT_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    28,
    'evergreen: IS',
    'COM_AUTOTWEET_RULE_EVERGREEN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    29,
    'evergreen: IS NOT',
    'COM_AUTOTWEET_RULE_EVERGREENNOT_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    30,
    'tags: IN',
    'COM_AUTOTWEET_RULE_TAGSIN_DESC'
  );
INSERT INTO `#__autotweet_ruletypes` (`id`, `name`, `description`)
VALUES (
    31,
    'tags: NOT IN',
    'COM_AUTOTWEET_RULE_TAGSNOTIN_DESC'
  );
