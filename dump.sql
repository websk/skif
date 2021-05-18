
# Forms

CREATE TABLE `form` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `email` varchar(100) NOT NULL DEFAULT '',
    `email_copy` varchar(100) NOT NULL DEFAULT '',
    `button_label` varchar(100) NOT NULL DEFAULT '',
    `comment` mediumtext,
    `response_mail_message` mediumtext,
    `url` varchar(1000) DEFAULT NULL,
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `form_field` (
    `id` int NOT NULL AUTO_INCREMENT,
    `form_id` int NOT NULL,
    `name` varchar(255) NOT NULL DEFAULT  '',
    `type` tinyint NOT NULL,
    `required` tinyint NOT NULL DEFAULT 0,
    `weight` smallint NOT NULL DEFAULT 0,
    `size` smallint DEFAULT NULL,
    `comment` varchar(255) NOT NULL DEFAULT  '',
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Site Menu

CREATE TABLE `site_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '0',
  `url` varchar(512) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `site_menu_item` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) NOT NULL DEFAULT '',
  `url` varchar(512) NOT NULL DEFAULT '',
  `content_id` int(11) unsigned DEFAULT NULL,
  `weight` int(11) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `menu_id` int(11) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `parent_weight` (`parent_id`,`weight`),
  KEY `menu_type_id` (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Comments

CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at_ts` int NOT NULL DEFAULT '0',
  `parent_id` int DEFAULT NULL,
  `url` varchar(2000),
  `url_md5` varbinary(32) DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  KEY `url_md5` (`url_md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Poll

CREATE TABLE `poll` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL DEFAULT '',
    `is_default` smallint(6) NOT NULL DEFAULT '0',
    `is_published` smallint(6) NOT NULL DEFAULT '0',
    `published_at` date NOT NULL,
    `unpublished_at` date NOT NULL,
    `created_at_ts` int(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `is_default` (`is_default`),
    KEY `is_published` (`is_published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `poll_question` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `poll_id` int(11) DEFAULT NULL,
    `title` varchar(255) NOT NULL DEFAULT '',
    `votes` int(11) NOT NULL DEFAULT '0',
    `weight` smallint(6) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Ratings

CREATE TABLE `rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `rating` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `rating_voice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating_id` int(11) NOT NULL,
  `rating` float NOT NULL DEFAULT '0',
  `comment` text,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rating_id_user_id` (`rating_id`,`user_id`),
  KEY `rating_id` (`rating_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
