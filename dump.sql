
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
