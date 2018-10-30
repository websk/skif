# Services

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

CREATE TABLE `redirect_rewrites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src` varchar(255) NOT NULL,
  `dst` varchar(255) NOT NULL,
  `code` int(11) NOT NULL,
  `kind` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `src` (`src`,`kind`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `key_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at_ts` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `value` mediumtext,
  `description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
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

INSERT INTO `template` (`id`, `name`, `title`, `css`, `is_default`, `layout_template_file`)
VALUES
       (1, 'main', 'Сайт. Три колонки', 'main.css', 1, 'layout.main.tpl.php'),
       (2, 'admin', 'Система управления сайтом', 'admin.css', 0, 'layout.admin.tpl.php');


# Blocks

CREATE TABLE `page_regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_template_id` (`name`,`template_id`),
  KEY `template_id` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `page_regions` (`id`, `name`, `template_id`, `title`)
VALUES
       (1, 'right_column', 1, 'Правая колонка'),
       (2, 'left_column', 1, 'Левая колонка'),
       (3, 'under_content', 1, 'Под контентом'),
       (4, 'above_content', 1, 'Над контентом'),
       (5, 'inside_head', 1, 'Внутри head'),
       (6, 'right_column', 3, 'Правая колонка'),
       (7, 'above_content', 3, 'Над контентом'),
       (8, 'under_content', 3, 'Под контентом');

CREATE TABLE `blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT '0',
  `pages` text NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `cache` tinyint(4) NOT NULL DEFAULT '1',
  `body` longtext NOT NULL,
  `format` smallint(6) DEFAULT '0',
  `page_region_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`),
  KEY `title` (`title`),
  KEY `page_region_id` (`page_region_id`),
  KEY `list` (`page_region_id`,`weight`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `blocks_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `block_id_role_id` (`block_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Auth. Users

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `designation` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `designation` (`designation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`, `designation`) VALUES (1, 'Администраторы', 'ADMINS');

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
VALUES (1, 'support@websk.ru', '1f737832e84fb946d5a4f50c567334be', 'Администратор', '', '', '', '', '', '', '', '', '', 1, '', '', NULL, NULL, NULL);

CREATE TABLE `users_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_role_id` (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users_roles` (`id`, `user_id`, `role_id`) VALUES (1, 1, 1);

CREATE TABLE `sessions` (
  `user_id` int(10) unsigned NOT NULL,
  `session` varchar(64) NOT NULL DEFAULT '',
  `hostname` varchar(128) NOT NULL DEFAULT '',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session`),
  KEY `timestamp` (`timestamp`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Contents

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

# Forms

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `url` text,
  `url_md5` varbinary(32) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `comment` text,
  `date_time` datetime DEFAULT NULL,
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

