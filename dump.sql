# Services

CREATE TABLE `redirect_rewrites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at_ts` int NOT NULL DEFAULT '0',
  `src` varchar(255) NOT NULL,
  `dst` varchar(255) NOT NULL,
  `code` int(11) NOT NULL,
  `kind` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `src` (`src`,`kind`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Key Value
# https://github.com/websk/php-keyvalue/blob/master/dump.sql

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
       (2, 'admin', 'Система управления сайтом', 'skif.css', 0, 'layout.admin.tpl.php');


# Logger
# https://github.com/websk/php-logger/blob/master/dump.sql

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
  KEY `list` (`page_region_id`,`weight`,`title`),
  CONSTRAINT `FK_blocks_template` FOREIGN KEY (`template_id`) REFERENCES `template` (`id`),
  CONSTRAINT `FK_blocks_page_regions` FOREIGN KEY (`page_region_id`) REFERENCES `page_regions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `blocks_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `block_id_role_id` (`block_id`,`role_id`),
  CONSTRAINT `FK_blocks_roles_blocks` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`id`),
  CONSTRAINT `FK_blocks_roles_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Auth. Users
# https://github.com/websk/php-auth/blob/master/dump.sql

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
  `description` text NOT NULL,
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(1000) NOT NULL DEFAULT '',
  `content_type_id` int(11) DEFAULT NULL,
  `last_modified_at` datetime NOT NULL,
  `redirect_url` varchar(1000) NOT NULL DEFAULT '',
  `template_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `main_rubric_id` (`main_rubric_id`),
  KEY `content_type_id` (`content_type_id`),
  CONSTRAINT `FK_content_rubrics` FOREIGN KEY (`main_rubric_id`) REFERENCES `rubrics` (`id`),
  CONSTRAINT `FK_content_content_types` FOREIGN KEY (`content_type_id`) REFERENCES `content_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `content_rubrics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) DEFAULT NULL,
  `rubric_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `rubric_id` (`rubric_id`),
  CONSTRAINT `content_rubrics_content_id_FK` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`),
  CONSTRAINT `content_rubrics_rubric_id_FK` FOREIGN KEY (`rubric_id`) REFERENCES `content_rubrics` (`id`)
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

CREATE TABLE `content_photo` (
   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
   `content_id` int(11) DEFAULT NULL,
   `is_default` tinyint(4) NOT NULL DEFAULT '0',
   `photo` varchar(255) DEFAULT NULL,
   PRIMARY KEY (`id`),
   KEY `content_id` (`content_id`),
   CONSTRAINT `content_id_FK` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
  `created_at_ts` int NOT NULL DEFAULT '0',
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
  `id` int NOT NULL AUTO_INCREMENT,
  `created_at_ts` int NOT NULL DEFAULT '0',
  `form_id` int,
  `name` varchar(255),
  `type` smallint DEFAULT NULL,
  `required` smallint NOT NULL DEFAULT '0',
  `weight` smallint DEFAULT NULL,
  `size` smallint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  CONSTRAINT `FK_form_field_form` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`)
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
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `is_default` smallint(6) NOT NULL DEFAULT '0',
  `is_published` smallint(6) NOT NULL DEFAULT '0',
  `published_at` date DEFAULT NULL,
  `unpublished_at` date DEFAULT NULL,
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

