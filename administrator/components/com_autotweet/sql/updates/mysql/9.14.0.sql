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
    'Twitter v1',
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
    'Pinterest - Deprecated',
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
INSERT INTO `#__autotweet_channeltypes` (
    `id`,
    `name`,
    `description`,
    `max_chars`,
    `joocial_exclusive`,
    `frontend`
  )
VALUES (
    35,
    'Twitter X - API v2',
    'COM_AUTOTWEET_CHANNEL_TWITTER_DESC',
    280,
    0,
    1
  );