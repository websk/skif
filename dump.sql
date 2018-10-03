CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `passw` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `photo` varchar(100) NOT NULL DEFAULT '',
  `birthday` varchar(20) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `company` varchar(200) DEFAULT NULL,
  `comment` mediumtext,
  `confirm` smallint(6) DEFAULT '0',
  `confirm_code` varchar(50) DEFAULT '',
  `provider` varchar(100) NOT NULL DEFAULT '',
  `provider_uid` varchar(255) DEFAULT '',
  `profile_url` varchar(1000) DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `confirm_code` (`confirm_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `users` (`id`, `email`, `passw`, `name`, `first_name`, `last_name`, `photo`, `birthday`, `phone`, `city`, `address`, `company`, `comment`, `confirm`, `confirm_code`, `provider`, `provider_uid`, `profile_url`, `created_at`)
VALUES
       (2, 'sergey.kulkov@gmail.com', '1f737832e84fb946d5a4f50c567334be', 'Кульков Сергей Сергеевич', '', '', '2425687555a543479031adeaae68a3ff55e98622ee548890594593.jpg', '04.04.1981', '+7 (499) 978-75-98', '', 'sergey', '', '', 1, '', '', NULL, NULL, NULL);

CREATE TABLE `users_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_role_id` (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ts` datetime DEFAULT NULL,
  `ip` varchar(15) NOT NULL,
  `entity_id` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `object` text,
  PRIMARY KEY (`id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sessions` (
  `user_id` int(10) unsigned NOT NULL,
  `session` varchar(64) NOT NULL DEFAULT '',
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session`),
  KEY `timestamp` (`timestamp`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `short_title` varchar(255) NOT NULL DEFAULT '',
  `annotation` text NOT NULL,
  `body` mediumtext NOT NULL,
  `main_rubric_id` int(11) DEFAULT NULL,
  `published_at` date DEFAULT NULL,
  `unpublished_at` date DEFAULT NULL,
  `is_published` smallint(6) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `image` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(1000) NOT NULL DEFAULT '',
  `content_type_id` int(11) DEFAULT NULL,
  `last_modified_at` datetime NOT NULL,
  `redirect_url` varchar(1000) NOT NULL DEFAULT '',
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `main_rubric_id` (`main_rubric_id`),
  KEY `content_type_id` (`content_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `content_rubrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) DEFAULT NULL,
  `rubric_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `rubric_id` (`rubric_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `content_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(20) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `content_types` (`id`, `type`, `name`, `url`, `template_id`)
VALUES
       (1, 'page', 'Страницы', '/', 1),
       (2, 'news', 'Новости', '/news', 1),
       (3, 'photo', 'Фото', '/photo', 1);

CREATE TABLE `rubrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `comment` text,
  `content_type_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `url` varchar(1000) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `content_type_id` (`content_type_id`),
  KEY `url` (`url`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(100) NOT NULL DEFAULT '',
  `css` varchar(50) NOT NULL DEFAULT '',
  `is_default` smallint(6) DEFAULT '0',
  `layout_template_file` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `is_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `email_copy` varchar(100) DEFAULT NULL,
  `button_label` varchar(100) DEFAULT NULL,
  `comment` mediumtext,
  `response_mail_message` mediumtext,
  `url` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `form_field` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) DEFAULT NULL,
  `name` mediumtext,
  `type` int(1) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `weight` int(4) DEFAULT NULL,
  `size` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

