<?php

return array(
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` char(100) NOT NULL,
  `key_value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB",
    "INSERT INTO `" . OSPARI_DB_PREFIX . "settings` (`id`, `key_name`, `key_value`) VALUES
(3, 'ospari_version', '" . OSPARI_VERSION . "'),
(7, 'img_width', '600'),
(8, 'img_height', '400'),
(9, 'perpage', '20'),
(10, 'theme', 'simply-pure');",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "drafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `slug` char(100) NOT NULL,
  `content` text NOT NULL,
  `code` text,
  `cover` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `draft_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` datetime NOT NULL,
  `edited_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "sessions` (
  `sid` char(33) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session` text NOT NULL,
  `update_date` datetime NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `sid` (`sid`)
) ENGINE=MyISAM",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` char(100) NOT NULL,
  `rkey` VARCHAR( 255 )NOT NULL,
  `name` char(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `bio` text,
  `last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` char(50) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `posts` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "drafts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `tag_id` int(11) NOT NULL,
    `draft_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB ",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "medias` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `large` varchar(200) NOT NULL,
      `thumb` varchar(200) NOT NULL,
      `ext` char(5) NOT NULL,
      `user_id` int(11) NOT NULL,
      `created_at` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "drafts_medias` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `draft_id` int(11) NOT NULL,
      `media_id` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "tags_drafts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `draft_id` int(11) NOT NULL,
        `tag_id` int(11) NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB",
    "ALTER TABLE `" . OSPARI_DB_PREFIX . "posts` ADD view_count int(11) NOT NULL AFTER state",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "post_meta` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `draft_id` int(11) NOT NULL,
        `key_name` char(100) NOT NULL,
        `key_value` text,
        PRIMARY KEY (`id`),
        UNIQUE KEY `key_name` (`key_name`)
      ) ENGINE=InnoDB",
    "ALTER TABLE `" . OSPARI_DB_PREFIX . "posts` ADD code text AFTER content",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "components` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `setting` text,
        `state` tinyint(1) NOT NULL DEFAULT '0',
        `user_id` int(11) NOT NULL,
        `draft_id` int(11) NOT NULL,
        `comment` text,
        `code` text,
        `keywords` varchar(255) NOT NULL,
        `type_id` int(11) NOT NULL,
        `order_nr` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB",
    "CREATE TABLE IF NOT EXISTS `" . OSPARI_DB_PREFIX . "component_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `short_name` char(20) NOT NULL,
  `position` int(11) NOT NULL,
  `label` char(30) NOT NULL,
  `tpl_name` char(50) NOT NULL,
  `res_tpl_name` varchar(100) NOT NULL,
  `javascript` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB",
    
    "INSERT INTO `" . OSPARI_DB_PREFIX . "component_types` (`id`, `name`, `short_name`, `position`, `label`, `tpl_name`, `res_tpl_name`, `javascript`) VALUES
(1, 'image', 'Photo', 3, 'Image', 'component-image-tpl', 'component-img-tpl-response', ''),
(2, 'youtube_video', 'Youtube', 6, 'Youtube embed code', 'component-all-types-tpl', 'component-all-types-tpl-response', ''),
(3, 'twitter_tweet', 'Tweet', 88, 'Tweet embed code', 'component-all-types-tpl', 'component-all-types-tpl-response', ''),
(4, 'vimeo_video', 'Vimeo', 97, 'Vimeo embed code', 'component-all-types-tpl', 'component-all-types-tpl-response', ''),
(5, 'html', 'Html', 99, 'HTML Code', 'component-all-types-tpl', 'component-all-types-tpl-response', ''),
(6, 'text', 'Text', 0, 'Comment', 'component-text-tpl', 'component-text-tpl-response', ''),
(7, 'facebook_post', 'Facebook', 80, 'Facebook embed code', 'component-all-types-tpl', 'component-all-types-tpl-response', '<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = \"//connect.facebook.net/en_US/all.js#xfbml=1\"; fjs.parentNode.insertBefore(js, fjs); }(document, ''script'', ''facebook-jssdk''));</script>'),
(8, 'google_plus_post', 'Google+', 98, 'Google+ embed code', 'component-all-types-tpl', 'component-all-types-tpl-response', ''),
(9, 'instagram', 'Instagram', 89, 'Instagram embed code', 'component-all-types-tpl', 'component-all-types-tpl-response', '');",
    
    "ALTER TABLE  `" . OSPARI_DB_PREFIX . "post_meta` DROP INDEX  `key_name`",
    
    "ALTER TABLE  `" . OSPARI_DB_PREFIX . "medias` ADD  `original` VARCHAR( 255 ) NOT NULL AFTER  `id`",
    "ALTER TABLE  `" . OSPARI_DB_PREFIX . "components` ADD  `media_id` INT UNSIGNED NOT NULL DEFAULT  '0' AFTER  `type_id`"
);
