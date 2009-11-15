DROP TABLE IF EXISTS `cms_banlist`;
CREATE TABLE `cms_banlist` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`ip` varchar(15) NOT NULL,
`bandate` datetime NOT NULL,
`int_num` int(11) NOT NULL,
`int_period` varchar(20) NOT NULL,
`status` int(11) DEFAULT '1' NOT NULL,
`autodelete` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_banner_hits`;
CREATE TABLE `cms_banner_hits` (
`id` int(11) NOT NULL auto_increment,
`banner_id` int(11),
`ip` varchar(16),
`pubdate` datetime,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_banners`;
CREATE TABLE `cms_banners` (
`id` int(11) NOT NULL auto_increment,
`position` varchar(100) DEFAULT 'banner_top' NOT NULL,
`typeimg` varchar(10) DEFAULT 'image' NOT NULL,
`fileurl` varchar(250),
`hits` int(11) NOT NULL,
`clicks` int(11) NOT NULL,
`maxhits` int(11) NOT NULL,
`maxuser` int(11) NOT NULL,
`user_id` int(11) DEFAULT '1' NOT NULL,
`pubdate` datetime,
`title` varchar(250),
`link` varchar(250),
`published` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5;

DROP TABLE IF EXISTS `cms_blog_authors`;
CREATE TABLE `cms_blog_authors` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`blog_id` int(11) NOT NULL,
`description` varchar(100) NOT NULL,
`startdate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33;

DROP TABLE IF EXISTS `cms_blog_cats`;
CREATE TABLE `cms_blog_cats` (
`id` int(11) NOT NULL auto_increment,
`blog_id` int(11) NOT NULL,
`title` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5;

DROP TABLE IF EXISTS `cms_blog_files`;
CREATE TABLE `cms_blog_files` (
`id` int(11) NOT NULL auto_increment,
`post_id` int(11) NOT NULL,
`filename` varchar(200) NOT NULL,
`filesize` int(11) NOT NULL,
`hits` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_blog_posts`;
CREATE TABLE `cms_blog_posts` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`cat_id` int(11) NOT NULL,
`blog_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
`title` varchar(250) NOT NULL,
`feel` varchar(100) NOT NULL,
`music` varchar(100) NOT NULL,
`content` text NOT NULL,
`allow_who` varchar(20) NOT NULL,
`edit_times` int(11) NOT NULL,
`edit_date` datetime NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`seolink` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `seolink` (`seolink`),
  KEY `seolink_2` (`seolink`),
  KEY `seolink_3` (`seolink`)
) ENGINE=MyISAM AUTO_INCREMENT=38;

DROP TABLE IF EXISTS `cms_blogs`;
CREATE TABLE `cms_blogs` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`title` varchar(250) NOT NULL,
`pubdate` datetime NOT NULL,
`allow_who` varchar(15) NOT NULL,
`view_type` varchar(15) DEFAULT 'list' NOT NULL,
`showcats` int(11) DEFAULT '1' NOT NULL,
`ownertype` varchar(15) DEFAULT 'single' NOT NULL,
`premod` int(11) NOT NULL,
`forall` int(11) DEFAULT '1' NOT NULL,
`owner` varchar(10) DEFAULT 'user' NOT NULL,
`seolink` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `seolink` (`seolink`),
  KEY `seolink_2` (`seolink`),
  KEY `seolink_3` (`seolink`),
  KEY `seolink_4` (`seolink`)
) ENGINE=MyISAM AUTO_INCREMENT=22;

DROP TABLE IF EXISTS `cms_board_cats`;
CREATE TABLE `cms_board_cats` (
`id` int(11) NOT NULL auto_increment,
`parent_id` int(11) NOT NULL,
`ordering` int(11) DEFAULT '1' NOT NULL,
`NSLeft` int(11) NOT NULL,
`NSRight` int(11) NOT NULL,
`NSDiffer` varchar(15) NOT NULL,
`NSIgnore` int(11) NOT NULL,
`NSLevel` int(11) NOT NULL,
`title` varchar(100) NOT NULL,
`description` varchar(100) NOT NULL,
`published` int(11) NOT NULL,
`orderform` int(11) DEFAULT '1',
`showdate` int(11) DEFAULT '1' NOT NULL,
`pubdate` datetime NOT NULL,
`orderby` varchar(20) DEFAULT 'title' NOT NULL,
`orderto` varchar(4) DEFAULT 'asc' NOT NULL,
`public` int(11) NOT NULL,
`perpage` int(11) DEFAULT '15' NOT NULL,
`maxcols` int(11) DEFAULT '1' NOT NULL,
`thumb1` int(11) DEFAULT '64' NOT NULL,
`thumb2` int(11) DEFAULT '400' NOT NULL,
`thumbsqr` int(11) NOT NULL,
`uplimit` int(11) DEFAULT '10' NOT NULL,
`is_photos` int(11) DEFAULT '1' NOT NULL,
`icon` varchar(25) DEFAULT 'folder_grey.png',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11;

DROP TABLE IF EXISTS `cms_board_items`;
CREATE TABLE `cms_board_items` (
`id` int(11) NOT NULL auto_increment,
`category_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`obtype` varchar(50) NOT NULL,
`title` varchar(250) NOT NULL,
`content` text NOT NULL,
`city` varchar(100) NOT NULL,
`pubdate` datetime,
`pubdays` int(11) NOT NULL,
`published` int(11) NOT NULL,
`file` varchar(250) NOT NULL,
`hits` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4;

DROP TABLE IF EXISTS `cms_cache`;
CREATE TABLE `cms_cache` (
`id` int(11) NOT NULL auto_increment,
`target` varchar(10) NOT NULL,
`target_id` varchar(255) NOT NULL,
`cachedate` datetime NOT NULL,
`cachefile` varchar(80) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=124;

DROP TABLE IF EXISTS `cms_category`;
CREATE TABLE `cms_category` (
`id` int(11) NOT NULL auto_increment,
`parent_id` int(11),
`title` varchar(200) NOT NULL,
`description` text NOT NULL,
`published` int(11) NOT NULL,
`showdate` int(11) DEFAULT '1' NOT NULL,
`showcomm` int(11) DEFAULT '1' NOT NULL,
`orderby` varchar(30) DEFAULT 'date' NOT NULL,
`orderto` varchar(4) DEFAULT 'asc' NOT NULL,
`modgrp_id` int(11) NOT NULL,
`NSLeft` int(11) NOT NULL,
`NSRight` int(11) NOT NULL,
`NSLevel` int(11) NOT NULL,
`NSDiffer` varchar(11) NOT NULL,
`NSIgnore` int(11) NOT NULL,
`ordering` int(11) NOT NULL,
`maxcols` int(11) DEFAULT '1' NOT NULL,
`showtags` int(11) DEFAULT '1' NOT NULL,
`showrss` int(11) DEFAULT '1' NOT NULL,
`showdesc` int(11) NOT NULL,
`is_public` int(11) NOT NULL,
`photoalbum` text NOT NULL,
`seolink` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `seolink` (`seolink`),
  UNIQUE KEY `seolink_2` (`seolink`),
  UNIQUE KEY `seolink_3` (`seolink`)
) ENGINE=MyISAM AUTO_INCREMENT=18;

DROP TABLE IF EXISTS `cms_clubs`;
CREATE TABLE `cms_clubs` (
`id` int(11) NOT NULL auto_increment,
`admin_id` int(11) DEFAULT '1' NOT NULL,
`title` text NOT NULL,
`description` longtext NOT NULL,
`imageurl` varchar(100) NOT NULL,
`pubdate` datetime NOT NULL,
`clubtype` varchar(15) DEFAULT 'public' NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`maxsize` int(11) NOT NULL,
`enabled_blogs` int(11) DEFAULT '1',
`enabled_photos` int(11) DEFAULT '1',
`rating` int(11) NOT NULL,
`photo_premod` int(11) NOT NULL,
`blog_premod` int(11) NOT NULL,
`blog_min_karma` int(11) NOT NULL,
`photo_min_karma` int(11) NOT NULL,
`album_min_karma` int(11) DEFAULT '25' NOT NULL,
`join_min_karma` int(11) NOT NULL,
`join_karma_limit` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14;

DROP TABLE IF EXISTS `cms_codecheck`;
CREATE TABLE `cms_codecheck` (
`id` int(11) NOT NULL auto_increment,
`place` varchar(200) NOT NULL,
`code` varchar(40) NOT NULL,
`session_id` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_comments`;
CREATE TABLE `cms_comments` (
`id` int(11) NOT NULL auto_increment,
`parent_id` int(11) NOT NULL,
`pid` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`target` varchar(20) NOT NULL,
`target_id` int(11) NOT NULL,
`guestname` varchar(200) NOT NULL,
`content` text NOT NULL,
`pubdate` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`is_new` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14;

DROP TABLE IF EXISTS `cms_comments_votes`;
CREATE TABLE `cms_comments_votes` (
`comment_id` int(11) NOT NULL,
`comment_type` varchar(3) NOT NULL,
`vote` smallint(6) NOT NULL,
`user_id` int(11) NOT NULL,
  KEY `comment_id` (`comment_id`, `comment_type`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `cms_components`;
CREATE TABLE `cms_components` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(200) NOT NULL,
`link` varchar(200) NOT NULL,
`config` text NOT NULL,
`internal` int(11) NOT NULL,
`author` varchar(200) DEFAULT 'InstantCMS team' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24;

DROP TABLE IF EXISTS `cms_content`;
CREATE TABLE `cms_content` (
`id` int(11) NOT NULL auto_increment,
`category_id` int(11) NOT NULL,
`user_id` int(11) DEFAULT '1' NOT NULL,
`pubdate` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
`enddate` date NOT NULL,
`is_end` int(11) NOT NULL,
`title` varchar(200) NOT NULL,
`description` text NOT NULL,
`content` longtext,
`published` int(11) DEFAULT '1' NOT NULL,
`hits` int(11) NOT NULL,
`meta_desc` text NOT NULL,
`meta_keys` text NOT NULL,
`showtitle` int(11) DEFAULT '1' NOT NULL,
`showdate` int(11) DEFAULT '1' NOT NULL,
`showlatest` int(11) DEFAULT '1' NOT NULL,
`showpath` int(11) DEFAULT '1' NOT NULL,
`ordering` int(1) NOT NULL,
`comments` int(11) DEFAULT '1' NOT NULL,
`is_arhive` int(11) NOT NULL,
`seolink` varchar(200) NOT NULL,
`canrate` int(11) DEFAULT '1' NOT NULL,
`pagetitle` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `seolink` (`seolink`),
  UNIQUE KEY `seolink_2` (`seolink`)
) ENGINE=MyISAM AUTO_INCREMENT=47;

DROP TABLE IF EXISTS `cms_content_access`;
CREATE TABLE `cms_content_access` (
`id` int(11) NOT NULL auto_increment,
`content_id` int(11) NOT NULL,
`content_type` varchar(100) NOT NULL,
`group_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_downloads`;
CREATE TABLE `cms_downloads` (
`id` int(11) NOT NULL auto_increment,
`fileurl` varchar(250) NOT NULL,
`hits` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_event_hooks`;
CREATE TABLE `cms_event_hooks` (
`id` int(11) NOT NULL auto_increment,
`event` varchar(50) NOT NULL,
`plugin_id` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `event` (`event`, `plugin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8;

DROP TABLE IF EXISTS `cms_faq_cats`;
CREATE TABLE `cms_faq_cats` (
`id` int(11) NOT NULL auto_increment,
`parent_id` int(11) NOT NULL,
`title` varchar(250) NOT NULL,
`description` text NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6;

DROP TABLE IF EXISTS `cms_faq_quests`;
CREATE TABLE `cms_faq_quests` (
`id` int(11) NOT NULL auto_increment,
`category_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
`published` int(11) NOT NULL,
`quest` text NOT NULL,
`answer` text NOT NULL,
`user_id` int(11) NOT NULL,
`answeruser_id` int(11) NOT NULL,
`answerdate` datetime NOT NULL,
`hits` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7;

DROP TABLE IF EXISTS `cms_filter_rules`;
CREATE TABLE `cms_filter_rules` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(200) NOT NULL,
`find` varchar(200) NOT NULL,
`replace` text NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_filters`;
CREATE TABLE `cms_filters` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(100) NOT NULL,
`description` text NOT NULL,
`link` varchar(100) NOT NULL,
`published` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8;

DROP TABLE IF EXISTS `cms_form_fields`;
CREATE TABLE `cms_form_fields` (
`id` int(11) NOT NULL auto_increment,
`form_id` int(11) NOT NULL,
`title` varchar(200) NOT NULL,
`ordering` int(11) NOT NULL,
`kind` varchar(30) NOT NULL,
`mustbe` int(11) NOT NULL,
`config` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25;

DROP TABLE IF EXISTS `cms_forms`;
CREATE TABLE `cms_forms` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(200) NOT NULL,
`description` text NOT NULL,
`email` varchar(200) NOT NULL,
`sendto` varchar(4) DEFAULT 'mail' NOT NULL,
`user_id` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4;

DROP TABLE IF EXISTS `cms_forum_cats`;
CREATE TABLE `cms_forum_cats` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(250) NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`auth_group` int(11) NOT NULL,
`ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2;

DROP TABLE IF EXISTS `cms_forum_files`;
CREATE TABLE `cms_forum_files` (
`id` int(11) NOT NULL auto_increment,
`post_id` int(11) NOT NULL,
`filename` varchar(200) NOT NULL,
`filesize` int(11) NOT NULL,
`hits` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_forum_images`;
CREATE TABLE `cms_forum_images` (
`id` int(11) NOT NULL auto_increment,
`post_id` int(11) NOT NULL,
`session_id` varchar(50) NOT NULL,
`fileurl` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_forum_polls`;
CREATE TABLE `cms_forum_polls` (
`id` int(11) NOT NULL auto_increment,
`thread_id` int(11) NOT NULL,
`title` varchar(200) NOT NULL,
`description` text NOT NULL,
`answers` text NOT NULL,
`options` varchar(250) NOT NULL,
`enddate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_forum_posts`;
CREATE TABLE `cms_forum_posts` (
`id` int(11) NOT NULL auto_increment,
`thread_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
`editdate` datetime NOT NULL,
`edittimes` int(11) NOT NULL,
`content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36;

DROP TABLE IF EXISTS `cms_forum_threads`;
CREATE TABLE `cms_forum_threads` (
`id` int(11) NOT NULL auto_increment,
`forum_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`title` varchar(250) NOT NULL,
`description` varchar(250) NOT NULL,
`icon` varchar(100) NOT NULL,
`pubdate` datetime NOT NULL,
`hits` int(11) NOT NULL,
`closed` int(11) NOT NULL,
`pinned` int(11) NOT NULL,
`rel_to` varchar(15) NOT NULL,
`rel_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16;

DROP TABLE IF EXISTS `cms_forum_votes`;
CREATE TABLE `cms_forum_votes` (
`id` int(11) NOT NULL auto_increment,
`poll_id` int(11) NOT NULL,
`answer_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_forums`;
CREATE TABLE `cms_forums` (
`id` int(11) NOT NULL auto_increment,
`category_id` int(11) NOT NULL,
`title` varchar(250) NOT NULL,
`description` varchar(250) NOT NULL,
`auth_group` int(11) DEFAULT '-1' NOT NULL,
`ordering` int(11) NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`parent_id` int(11) NOT NULL,
`NSLeft` int(11) NOT NULL,
`NSRight` int(11) NOT NULL,
`NSDiffer` varchar(15) NOT NULL,
`NSIgnore` int(11) NOT NULL,
`NSLevel` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1004;

DROP TABLE IF EXISTS `cms_menu`;
CREATE TABLE `cms_menu` (
`id` int(11) NOT NULL auto_increment,
`menu` varchar(200) NOT NULL,
`title` varchar(200) NOT NULL,
`link` varchar(200) NOT NULL,
`linktype` varchar(12) DEFAULT 'link' NOT NULL,
`linkid` varchar(25) DEFAULT '-1',
`target` varchar(8) DEFAULT '_self' NOT NULL,
`component` varchar(100) NOT NULL,
`ordering` int(11) DEFAULT '1' NOT NULL,
`published` int(11) NOT NULL,
`template` varchar(30) NOT NULL,
`allow_group` int(11) DEFAULT '-1' NOT NULL,
`iconurl` varchar(100) NOT NULL,
`NSLeft` int(11) NOT NULL,
`NSRight` int(11) NOT NULL,
`NSLevel` int(11) NOT NULL,
`NSDiffer` varchar(40),
`NSIgnore` int(11) NOT NULL,
`parent_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43;

DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE `cms_modules` (
`id` int(11) NOT NULL auto_increment,
`position` varchar(10) NOT NULL,
`name` varchar(200) NOT NULL,
`title` varchar(200) NOT NULL,
`is_external` int(11) NOT NULL,
`content` text NOT NULL,
`ordering` int(11) NOT NULL,
`showtitle` int(11) DEFAULT '1' NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`user` int(11) DEFAULT '1' NOT NULL,
`config` text NOT NULL,
`original` int(11) NOT NULL,
`css_prefix` varchar(30) NOT NULL,
`allow_group` int(11) DEFAULT '-1' NOT NULL,
`cache` int(11) NOT NULL,
`cachetime` int(11) DEFAULT '1' NOT NULL,
`cacheint` varchar(15) DEFAULT 'HOUR' NOT NULL,
`template` varchar(35) DEFAULT 'module.tpl' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=77;

DROP TABLE IF EXISTS `cms_modules_bind`;
CREATE TABLE `cms_modules_bind` (
`id` int(11) NOT NULL auto_increment,
`module_id` int(11) NOT NULL,
`menu_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_online`;
CREATE TABLE `cms_online` (
`id` int(11) NOT NULL auto_increment,
`ip` varchar(15) NOT NULL,
`sess_id` varchar(100) NOT NULL,
`lastdate` datetime NOT NULL,
`user_id` int(11) NOT NULL,
`agent` varchar(250) NOT NULL,
`viewurl` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8;

DROP TABLE IF EXISTS `cms_photo_albums`;
CREATE TABLE `cms_photo_albums` (
`id` int(11) NOT NULL auto_increment,
`parent_id` int(11) NOT NULL,
`ordering` int(11) DEFAULT '1' NOT NULL,
`NSLeft` int(11) NOT NULL,
`NSRight` int(11) NOT NULL,
`NSDiffer` varchar(15) NOT NULL,
`NSIgnore` int(11) NOT NULL,
`NSLevel` int(11) NOT NULL,
`title` varchar(100) NOT NULL,
`description` varchar(100) NOT NULL,
`published` int(11) NOT NULL,
`showdate` int(11) DEFAULT '1' NOT NULL,
`iconurl` varchar(100) NOT NULL,
`pubdate` datetime NOT NULL,
`orderby` varchar(20) DEFAULT 'title' NOT NULL,
`orderto` varchar(4) DEFAULT 'asc' NOT NULL,
`public` int(11) NOT NULL,
`perpage` int(11) DEFAULT '15' NOT NULL,
`cssprefix` varchar(20) NOT NULL,
`thumb1` int(11) DEFAULT '96' NOT NULL,
`thumb2` int(11) DEFAULT '480' NOT NULL,
`thumbsqr` int(11) DEFAULT '1' NOT NULL,
`showtype` varchar(10) DEFAULT 'list' NOT NULL,
`nav` int(11) DEFAULT '1' NOT NULL,
`uplimit` int(11) NOT NULL,
`maxcols` int(11) DEFAULT '4' NOT NULL,
`orderform` int(11) DEFAULT '1' NOT NULL,
`showtags` int(11) DEFAULT '1' NOT NULL,
`bbcode` int(11) DEFAULT '1' NOT NULL,
`user_id` int(11) DEFAULT '1' NOT NULL,
`is_comments` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1034;

DROP TABLE IF EXISTS `cms_photo_files`;
CREATE TABLE `cms_photo_files` (
`id` int(11) NOT NULL auto_increment,
`album_id` int(11) NOT NULL,
`title` varchar(200) NOT NULL,
`description` text NOT NULL,
`pubdate` datetime DEFAULT '0000-00-00 00:00:00',
`file` varchar(200) NOT NULL,
`published` int(11) NOT NULL,
`hits` int(11) NOT NULL,
`showdate` int(11) DEFAULT '1' NOT NULL,
`comments` int(11) DEFAULT '1' NOT NULL,
`user_id` int(11) DEFAULT '1' NOT NULL,
`owner` varchar(10) DEFAULT 'photos',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12;

DROP TABLE IF EXISTS `cms_plugins`;
CREATE TABLE `cms_plugins` (
`id` int(11) NOT NULL auto_increment,
`plugin` varchar(30) NOT NULL,
`title` varchar(255) NOT NULL,
`description` text NOT NULL,
`author` varchar(255) NOT NULL,
`version` varchar(15) NOT NULL,
`plugin_type` varchar(10) NOT NULL,
`published` int(11) NOT NULL,
`config` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7;

DROP TABLE IF EXISTS `cms_polls`;
CREATE TABLE `cms_polls` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(200) NOT NULL,
`pubdate` date DEFAULT '0000-00-00' NOT NULL,
`answers` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_polls_log`;
CREATE TABLE `cms_polls_log` (
`id` int(11) NOT NULL auto_increment,
`poll_id` int(11) NOT NULL,
`answer_id` int(11) NOT NULL,
`user_id` int(11) NOT NULL,
`ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_price_cats`;
CREATE TABLE `cms_price_cats` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(200) NOT NULL,
`description` text NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10;

DROP TABLE IF EXISTS `cms_price_items`;
CREATE TABLE `cms_price_items` (
`id` int(11) NOT NULL auto_increment,
`category_id` int(11) NOT NULL,
`title` varchar(230) NOT NULL,
`price` float NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`canmany` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=892;

DROP TABLE IF EXISTS `cms_ratings`;
CREATE TABLE `cms_ratings` (
`id` int(11) NOT NULL auto_increment,
`item_id` int(11) NOT NULL,
`points` int(11) NOT NULL,
`ip` varchar(20) NOT NULL,
`target` varchar(20) NOT NULL,
`user_id` int(11) DEFAULT '1' NOT NULL,
`pubdate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_search`;
CREATE TABLE `cms_search` (
`id` int(11) NOT NULL,
`session_id` varchar(100) NOT NULL,
`title` varchar(250) NOT NULL,
`link` varchar(100) NOT NULL,
`place` varchar(100) NOT NULL,
`placelink` varchar(100) NOT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `cms_stats`;
CREATE TABLE `cms_stats` (
`id` int(11) NOT NULL auto_increment,
`ip` varchar(20) NOT NULL,
`logdate` datetime NOT NULL,
`page` varchar(200) NOT NULL,
`agent` varchar(60) DEFAULT 'unknown',
`refer` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_subscribe`;
CREATE TABLE `cms_subscribe` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`target` varchar(20) NOT NULL,
`target_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_tags`;
CREATE TABLE `cms_tags` (
`id` int(11) NOT NULL auto_increment,
`tag` varchar(250) NOT NULL,
`target` varchar(25) NOT NULL,
`item_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_uc_cart`;
CREATE TABLE `cms_uc_cart` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`session_id` varchar(50) NOT NULL,
`item_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
`itemscount` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_uc_cats`;
CREATE TABLE `cms_uc_cats` (
`id` int(11) NOT NULL auto_increment,
`parent_id` int(11) NOT NULL,
`title` varchar(200) NOT NULL,
`description` text NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`fieldsstruct` text,
`view_type` varchar(20) DEFAULT 'list' NOT NULL,
`fields_show` int(11) DEFAULT '10' NOT NULL,
`showmore` int(11) DEFAULT '1' NOT NULL,
`perpage` int(11) DEFAULT '20' NOT NULL,
`showtags` int(11) DEFAULT '1' NOT NULL,
`showsort` int(11) DEFAULT '1' NOT NULL,
`is_ratings` int(11) NOT NULL,
`orderby` varchar(12) DEFAULT 'pubdate' NOT NULL,
`orderto` varchar(10) DEFAULT 'desc',
`showabc` int(11) DEFAULT '1' NOT NULL,
`shownew` int(11) NOT NULL,
`newint` varchar(100) NOT NULL,
`filters` int(11) NOT NULL,
`is_shop` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3;

DROP TABLE IF EXISTS `cms_uc_discount`;
CREATE TABLE `cms_uc_discount` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(150) NOT NULL,
`cat_id` int(11) NOT NULL,
`sign` tinyint(4) NOT NULL,
`value` float NOT NULL,
`unit` varchar(10) NOT NULL,
`if_limit` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4;

DROP TABLE IF EXISTS `cms_uc_items`;
CREATE TABLE `cms_uc_items` (
`id` int(11) NOT NULL auto_increment,
`category_id` int(11) NOT NULL,
`title` varchar(200) NOT NULL,
`pubdate` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
`imageurl` varchar(200) NOT NULL,
`fieldsdata` text NOT NULL,
`hits` int(11) NOT NULL,
`is_comments` int(11) NOT NULL,
`tags` varchar(200) NOT NULL,
`rating` float NOT NULL,
`meta_desc` varchar(250) NOT NULL,
`meta_keys` varchar(250) NOT NULL,
`price` float NOT NULL,
`canmany` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8;

DROP TABLE IF EXISTS `cms_uc_ratings`;
CREATE TABLE `cms_uc_ratings` (
`id` int(11) NOT NULL auto_increment,
`item_id` int(11) NOT NULL,
`points` int(11) NOT NULL,
`ip` varchar(16) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_upload_images`;
CREATE TABLE `cms_upload_images` (
`id` int(11) NOT NULL auto_increment,
`post_id` int(11) NOT NULL,
`session_id` varchar(50) NOT NULL,
`fileurl` varchar(250) NOT NULL,
`target` varchar(25) DEFAULT 'forum' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2;

DROP TABLE IF EXISTS `cms_user_autoawards`;
CREATE TABLE `cms_user_autoawards` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(200) NOT NULL,
`description` varchar(200) NOT NULL,
`imageurl` varchar(200) NOT NULL,
`p_comment` int(11) NOT NULL,
`p_blog` int(11) NOT NULL,
`p_forum` int(11) NOT NULL,
`p_photo` int(11) NOT NULL,
`p_privphoto` int(11),
`p_content` int(11) NOT NULL,
`p_karma` int(11) NOT NULL,
`published` int(11) DEFAULT '1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3;

DROP TABLE IF EXISTS `cms_user_awards`;
CREATE TABLE `cms_user_awards` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
`title` varchar(250) NOT NULL,
`description` text NOT NULL,
`imageurl` varchar(100) NOT NULL,
`from_id` int(11) NOT NULL,
`award_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2;

DROP TABLE IF EXISTS `cms_user_clubs`;
CREATE TABLE `cms_user_clubs` (
`user_id` int(11) NOT NULL,
`club_id` int(11) NOT NULL,
`role` varchar(20) DEFAULT 'guest' NOT NULL,
`pubdate` datetime NOT NULL
) ENGINE=MyISAM;

DROP TABLE IF EXISTS `cms_user_files`;
CREATE TABLE `cms_user_files` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`filename` varchar(250) NOT NULL,
`pubdate` datetime NOT NULL,
`allow_who` varchar(50) NOT NULL,
`filesize` int(11) NOT NULL,
`hits` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_user_friends`;
CREATE TABLE `cms_user_friends` (
`id` int(11) NOT NULL auto_increment,
`to_id` int(11) NOT NULL,
`from_id` int(11) NOT NULL,
`logdate` datetime NOT NULL,
`is_accepted` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2;

DROP TABLE IF EXISTS `cms_user_groups`;
CREATE TABLE `cms_user_groups` (
`id` int(11) NOT NULL auto_increment,
`title` varchar(200) NOT NULL,
`alias` varchar(100) NOT NULL,
`is_admin` int(11) NOT NULL,
`access` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10;

DROP TABLE IF EXISTS `cms_user_karma`;
CREATE TABLE `cms_user_karma` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`sender_id` int(11) NOT NULL,
`points` smallint(6) NOT NULL,
`senddate` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_user_msg`;
CREATE TABLE `cms_user_msg` (
`id` int(11) NOT NULL auto_increment,
`to_id` int(11) NOT NULL,
`from_id` int(11) NOT NULL,
`senddate` datetime NOT NULL,
`is_new` int(11) DEFAULT '1' NOT NULL,
`message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_user_photos`;
CREATE TABLE `cms_user_photos` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`pubdate` date NOT NULL,
`title` varchar(250) NOT NULL,
`description` text NOT NULL,
`allow_who` varchar(15) DEFAULT 'all' NOT NULL,
`hits` int(11) NOT NULL,
`imageurl` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_user_profiles`;
CREATE TABLE `cms_user_profiles` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`city` varchar(250) NOT NULL,
`description` text NOT NULL,
`showmail` int(11) NOT NULL,
`showbirth` int(11) DEFAULT '1' NOT NULL,
`showicq` int(11) DEFAULT '1' NOT NULL,
`karma` int(11) NOT NULL,
`imageurl` varchar(250) NOT NULL,
`allow_who` varchar(35) DEFAULT 'all' NOT NULL,
`signature` varchar(240) NOT NULL,
`gender` varchar(1) NOT NULL,
`formsdata` text NOT NULL,
`email_newmsg` int(11) DEFAULT '1' NOT NULL,
`cm_subscribe` varchar(4) NOT NULL,
`stats` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26;

DROP TABLE IF EXISTS `cms_user_wall`;
CREATE TABLE `cms_user_wall` (
`id` int(11) NOT NULL auto_increment,
`user_id` int(11) NOT NULL,
`author_id` int(11) NOT NULL,
`pubdate` datetime NOT NULL,
`content` text NOT NULL,
`usertype` varchar(8) DEFAULT 'user' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11;

DROP TABLE IF EXISTS `cms_users`;
CREATE TABLE `cms_users` (
`id` int(11) NOT NULL auto_increment,
`group_id` int(11) DEFAULT '1' NOT NULL,
`login` varchar(100) NOT NULL,
`nickname` varchar(100) NOT NULL,
`password` varchar(100) NOT NULL,
`email` varchar(100) NOT NULL,
`icq` varchar(15) NOT NULL,
`regdate` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
`logdate` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
`birthdate` date DEFAULT '0000-00-00' NOT NULL,
`is_locked` int(11) NOT NULL,
`is_deleted` int(11) NOT NULL,
`rating` int(11) NOT NULL,
`points` int(11) NOT NULL,
`last_ip` varchar(15) NOT NULL,
`status` varchar(255) NOT NULL,
`status_date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26;

DROP TABLE IF EXISTS `cms_users_activate`;
CREATE TABLE `cms_users_activate` (
`id` int(11) NOT NULL auto_increment,
`pubdate` datetime NOT NULL,
`user_id` int(11) NOT NULL,
`code` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `cms_user_profile_visitor`;
CREATE TABLE `cms_user_profile_visitor` (  
`user_id` int(11) NOT NULL,  
`visitor_id` int(11) NOT NULL,  
`visitdate` datetime NOT NULL default '0000-00-00') 
ENGINE=MyISAM AUTO_INCREMENT=1;

INSERT INTO `cms_blog_authors` (`id`, `user_id`, `blog_id`, `description`, `startdate`) VALUES ('21','1','1','','2009-05-13 12:07:38'),
 ('26','21','14','','2009-05-26 10:33:59'),
 ('25','1','14','','2009-05-26 10:33:59'),
 ('32','25','17','','2009-08-19 12:00:33'),
 ('31','21','17','','2009-08-19 12:00:33');

INSERT INTO `cms_blog_cats` (`id`, `blog_id`, `title`) VALUES ('2','14','test');

INSERT INTO `cms_blog_posts` (`id`, `user_id`, `cat_id`, `blog_id`, `pubdate`, `title`, `feel`, `music`, `content`, `allow_who`, `edit_times`, `edit_date`, `published`, `seolink`) VALUES ('5','1','0','1','2009-08-29 14:47:00','Пример записи в блоге','','','[b]Блоги InstantCMS - это мощный и надежный инструмент для ваших публикаций:[/b]\r\n\r\n- Редактор BB-кода (скрытый текст, ссылки, цитаты, картинки);\r\n- Вставка изображений "на лету";\r\n- Неограниченное число рубрик;\r\n- Комментарии к записям;\r\n- Возможность ведения коллективных блогов;\r\n- Смайлы и теги;','0','2','2009-08-29 14:47:00','1','primer-zapisi-v-bloge');

INSERT INTO `cms_blogs` (`id`, `user_id`, `title`, `pubdate`, `allow_who`, `view_type`, `showcats`, `ownertype`, `premod`, `forall`, `owner`, `seolink`) VALUES ('1','1','Пример блога','2008-06-03 13:26:55','all','list','1','single','0','1','user','primer-bloga');

INSERT INTO `cms_board_cats` (`id`, `parent_id`, `ordering`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `title`, `description`, `published`, `orderform`, `showdate`, `pubdate`, `orderby`, `orderto`, `public`, `perpage`, `maxcols`, `thumb1`, `thumb2`, `thumbsqr`, `uplimit`, `is_photos`, `icon`) VALUES ('1','0','1','1','8','','0','0','-- Корневая рубрика --','','1','1','1','2008-09-22 13:39:32','title','asc','0','15','1','64','400','0','10','1','folder_grey.png'),
 ('10','1','15','4','5','','0','1','Грызуны','','1','1','1','2008-09-22 14:30:29','pubdate','desc','-1','20','1','64','400','0','10','1','folder_grey.png'),
 ('9','1','17','6','7','','0','1','Собаки','','1','1','1','2008-09-22 14:30:23','pubdate','desc','-1','20','2','64','400','0','10','1','folder_grey.png'),
 ('8','1','14','2','3','','0','1','Кошки','','1','1','1','2008-09-22 14:30:00','pubdate','desc','-1','20','1','64','400','0','10','1','folder_grey.png');

INSERT INTO `cms_category` (`id`, `parent_id`, `title`, `description`, `published`, `showdate`, `showcomm`, `orderby`, `orderto`, `modgrp_id`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `maxcols`, `showtags`, `showrss`, `showdesc`, `is_public`, `photoalbum`, `seolink`) VALUES ('1','0','--Корневой раздел--','Корневой раздел сайта','1','1','1','pubdate','asc','0','1','6','0','','0','1','1','1','1','0','0','','--kornevoi-razdel--'),
 ('2','1','Новости','','1','1','1','pubdate','ASC','0','2','3','1','','0','1','1','1','0','0','0','','novosti'),
 ('6','1','Статьи','<p>Тексты статей предоставлены службой <a href="http://referats.yandex.ru/">Яндекс.Рефераты</a></p>','1','1','1','pubdate','ASC','0','4','5','1','','0','2','1','1','1','1','1','a:7:{s:2:"id";i:0;s:6:"titles";s:1:"0";s:6:"header";s:18:"Фотографии на тему";s:7:"orderby";s:4:"hits";s:7:"orderto";s:3:"asc";s:7:"maxcols";i:2;s:3:"max";i:8;}','stati');

INSERT INTO `cms_components` (`id`, `title`, `link`, `config`, `internal`, `author`) VALUES ('1','Каталог статей','content','---\nshowtitle: 0\nreaddesc: 0\nrating: 1\nperpage: 1\npt_show: 0\npt_disp: 0\npt_hide: 0\nautokeys: 1\naf_on: 0\naf_delete: 1\naf_showlink: 1\naf_forum_id: 1\naf_hidecat_id: 2\n','0','InstantCMS team'),
 ('2','Регистрация','registration','---\nis_on: 1\nact: 0\nsend: \noffmsg: >\n  Регистрация приостановлена по\n  техническим причинам.\nhimsg: \nfirst_auth_redirect: profile\nauth_redirect: profile\nname_mode: nickname\nask_icq: 1\nask_birthdate: 1\n','0','InstantCMS team'),
 ('3','Фотогалерея','photos','---\nlink: 1\nsaveorig: 1\nmaxcols: 2\nshowlat: 1\nwatermark: 1\n','0','InstantCMS team'),
 ('4','Прайс-лист','price','---\n','0','InstantCMS team'),
 ('5','Поиск','search','---\nperpage: 10\ncomp: \n  - content\n  - photos\n  - price\n  - catalog\n  - forum\n  - blog\n','0','InstantCMS team'),
 ('6','Конструктор форм','forms','---\n','0','InstantCMS team'),
 ('7','Комментарии пользователей','comments','---\nemail: comments@cms.ru\ncanguests: 1\nregcap: 0\npublish: 1\nsmiles: 1\nbbcode: 1\nselfdel: 0\nsubscribe: 1\nanchors: 0\nrecode: 0\nmin_karma: 0\nmin_karma_show: 0\nmin_karma_add: 0\n','1','InstantCMS team'),
 ('8','Архив материалов','arhive','---\n','0','InstantCMS team'),
 ('9','Универсальный каталог','catalog','---\nemail: shop@site.ru\ndelivery: |\n  Сведения о доставке.\n  Этот текст можно изменить в настройках компонента &quot;Универсальный каталог&quot;.\nnotice: 0\n','0','InstantCMS team'),
 ('10','Профили пользователей','users','---\nshowgroup: 1\nsw_stats: \nsw_comm: 1\nsw_forum: 1\nsw_photo: 1\nsw_wall: 1\nsw_friends: 1\nsw_blogs: 1\nsw_clubs: 1\nsw_feed: 1\nsw_content: 1\nsw_awards: 1\nsw_board: 1\nsw_msg: 1\nkarmatime: 3\nkarmaint: HOUR\nphotosize: 20\nwatermark: 0\nsmallw: 64\nmedw: 200\nsw_files: 1\nfilessize: 100\nprivforms: \n  - 3\n','0','InstantCMS team'),
 ('12','Форум','forum','---\nis_on: 1\nkarma: \npp_thread: 15\npp_forum: 15\nshowimg: 1\nimg_on: 1\nimg_max: 5\nfast_on: 1\nfast_bb: 1\nfa_on: 1\nfa_allow: -1\nfa_max: 25\nfa_ext: txt doc zip rar arj png gif jpg jpeg bmp\nfa_size: 1024\n','0','InstantCMS team'),
 ('13','Статистика сайта','statistics','---\n','1','InstantCMS team'),
 ('15','Блоги','blog','---\nperpage: 10\nmin_karma_private: 0\nmin_karma_public: 0\nmin_karma: 1\nrss_all: 1\nrss_one: 1\n','0','InstantCMS team'),
 ('16','Вопросы и ответы','faq','---\n','0','InstantCMS team'),
 ('17','Баннеры','banners','---\n','1','InstantCMS team'),
 ('19','RSS генератор','rssfeed','---\naddsite: 1\nmaxitems: 50\nicon_on: 1\nicon_url: http://cmssite/images/rss.png\nicon_title: InstantCMS\nicon_link: http://cmssite/\n','1','InstantCMS team'),
 ('21','Награждение пользователей','autoawards','---\n','1','InstantCMS team'),
 ('22','Доска объявлений','board','---\nmaxcols: 1\nobtypes: |\n  Продам\n  Куплю\n  Обменяю\n  Подарю\nshowlat: \npublic: 2\nphotos: 1\nsrok: 1\npubdays: 10\nwatermark: 0\n','0','InstantCMS team'),
 ('23','Клубы пользователей','clubs','---\nenabled_blogs: 1\nenabled_photos: 1\nthumb1: 48\nthumb2: 200\nthumbsqr: 1\ncancreate: 1\nperpage: 10\ncreate_min_karma: 0\ncreate_min_rating: 0\n','0','InstantCMS team');

INSERT INTO `cms_event_hooks` (`id`, `event`, `plugin_id`) VALUES ('6','GET_ARTICLE','5'),
 ('3','INSERT_WYSIWYG','3'),
 ('7','USER_PROFILE','6');

INSERT INTO `cms_filters` (`id`, `title`, `description`, `link`, `published`) VALUES ('1','Автозамена / Фильтр ссылок','Фильтр выполняет замену выражений по созданным вами правилам, а так же служит для создания ссылок в категории прайслиста, фотоальбомы и фотографии.<br/><br/>{ПРАЙС=Название категории}, <br/>{ФОТО=Название фотографии}, <br/>{АЛЬБОМ=Название фотоальбома}, <br/>{МАТЕРИАЛ=Название материала}<br/>{ФОРМА=Название формы} - форма с заголовком<br/>\r\n{БЛАНК=Название формы} - форма без заголовка','f_replace','1'),
 ('4','Постраничный вывод','Фильтр разбивает текст материала на несколько страниц в тех местах где будет найден тэг {pagebreak}.','f_pages','1'),
 ('2','Содержание статьи','Фильтр ищет тэги {СТРАНИЦА=Название страницы} в текстах статей, и заменяет их на главы в содержании статьи.','f_contents','1'),
 ('5','Внешние скрипты','Фильтр находит в текстах статей и модулей выражения "{ФАЙЛ=script.php}" и заменяет их результатами выполнения соответствующих скриптов из папки "/includes/myphp/".','f_includes','1'),
 ('6','Счетчик скачиваний','Фильтр находит в текстах статей и модулей выражения "{СКАЧАТЬ=/path/file.zip}" и заменяет их ссылкой для загрузки указанного файла, снабженной счетчиком скачиваний.','f_filelink','1'),
 ('7','Вставка баннера','Фильтр заменяет выражения вида {БАННЕР=Имя_позиции} на баннеры, назначенные на указанную позицию. Работает в статьях и модулях.','f_banners','1');

INSERT INTO `cms_form_fields` (`id`, `form_id`, `title`, `ordering`, `kind`, `mustbe`, `config`) VALUES ('8','1','Ваше имя:','1','text','1','a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:7:"default";s:0:"";}'),
 ('19','1','Подписаться на новости?','5','checkbox','0','a:1:{s:7:"checked";s:1:"0";}'),
 ('10','1','Текст сообщения:','2','textarea','1','a:4:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:4:"rows";s:1:"5";s:7:"default";s:0:"";}'),
 ('11','1','Откуда вы о нас узнали?','4','list','0','a:1:{s:5:"items";s:47:"Из прессы/От знакомых/По телевизору/От знакомых";}'),
 ('15','1','Компания:','3','text','0','a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:7:"default";s:0:"";}'),
 ('22','3','Любимая музыка:','1','text','0','a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"50";s:7:"default";s:0:"";}'),
 ('24','3','Образование:','3','list','0','a:1:{s:5:"items";s:24:"Высшее/Среднее/Начальное";}');

INSERT INTO `cms_forms` (`id`, `title`, `description`, `email`, `sendto`, `user_id`) VALUES ('1','Написать письмо','<p>Используйте эту форму для обратной связи!</p>','forms@cms.ru','user','1'),
 ('3','Анкета пользователя','','','mail','1');

INSERT INTO `cms_forum_posts` (`id`, `thread_id`, `user_id`, `pubdate`, `editdate`, `edittimes`, `content`) VALUES ('29','12','1','2009-04-04 18:54:53','2009-06-19 11:46:32','1','Геосинклиналь [b]обогащает магматический монтмориллонит[/b], что в общем свидетельствует о преобладании тектонических опусканий в это время. Углефикация характерна. Порода существенна. Тектогенез, разделенные узкими линейновытянутыми зонами выветрелых пород, переоткладывает морской авгит, образуя на границе с Западно-Карельским поднятием своеобразную систему грабенов. Ведущий экзогенный геологический процесс - субдукция ослабляет лакколит, так как совершенно однозначно указывает на существование и рост в период оформления палеогеновой поверхности выравнивания.');

INSERT INTO `cms_forum_threads` (`id`, `forum_id`, `user_id`, `title`, `description`, `icon`, `pubdate`, `hits`, `closed`, `pinned`, `rel_to`, `rel_id`) VALUES ('12','1','1','Пример темы','','','2009-07-03 13:20:51','46','0','0','','0');

INSERT INTO `cms_forums` (`id`, `category_id`, `title`, `description`, `auth_group`, `ordering`, `published`, `parent_id`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`) VALUES ('1000','0','-- Корень форумов --','','0','1','0','0','1','6','','0','1');

INSERT INTO `cms_menu` (`id`, `menu`, `title`, `link`, `linktype`, `linkid`, `target`, `component`, `ordering`, `published`, `template`, `allow_group`, `iconurl`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `parent_id`) VALUES ('1','root','-- Главная страница --','','link','-1','_self','','1','0','0','-1','','1','2','0','','0','0');

INSERT INTO `cms_modules` (`id`, `position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `allow_group`, `cache`, `cachetime`, `cacheint`, `template`) VALUES ('1','left','Меню','Меню','1','mod_menu','6','1','0','0','---\nmenu: mainmenu\njtree: 1\n','1','','-1','0','1','HOUR','module.tpl'),
 ('17','top','Главная страница','Добро пожаловать!','0','<table width="100%" cellspacing="5" cellpadding="0" border="0">\r\n    <tbody>\r\n        <tr>\r\n            <td width="216" valign="top"><a target="_blank" href="http://www.instantcms.ru"><img height="100" width="206" border="0" alt="" src="/images/content/instantcms.jpg" /></a></td>\r\n            <td valign="top">\r\n            <p>Теперь, когда InstantCMS установлена и готова к работе, вы можете приступить к наполнению вашего сайта каким-либо содержимым. Для наглядности изучения InstantCMS, база данных уже содержит демонстрационные данные.</p>\r\n            <div>Для перехода в панель управления нажмите <a href="/admin/">здесь</a>.\r\n            <div>Используйте логин и пароль, указанные при установке.</div>\r\n            <div>&nbsp;</div>\r\n            <div>Чтобы изменить этот текст, <a href="/admin/index.php?view=modules&amp;do=edit&amp;id=17">отредактируйте модуль &quot;Главная страница&quot;</a>.</div>\r\n            </div>\r\n            </td>\r\n        </tr>\r\n    </tbody>\r\n</table>','0','1','0','1','---\n','1','','-1','0','1','HOUR','module.tpl'),
 ('44','right','Облако тегов','Облако тегов','1','mod_tags','18','0','0','0','---\ncat_id: \nsortby: tag\nmenuid: \nminfreq: 0\nminlen: 3\ntargets: \n  content: content\n  photo: photo\n  blogpost: blog\n  catalog: catalog\n  userphoto: userphoto\n','1','','-1','0','1','HOUR','module.tpl'),
 ('46','bottom','Новинки каталога','Новинки каталога','1','mod_uc_latest','30','1','0','0','---\nnewscount: 6\ncat_id: 0\nmenuid: 23\nshowf: 2\nshowtype: list\nfulllink: 1\n','1','','-1','0','1','HOUR','module.tpl'),
 ('26','right','Корзина покупателя','Корзина','1','mod_cart','19','1','0','0','---\nshowtype: list\nshowqty: 1\nmenuid: 23\nsource: catalog\n','1','','-1','0','1','HOUR','module.tpl'),
 ('25','right','Голосования','Голосования','1','mod_polls','7','1','0','0','---\nshownum: 0\npoll_id: 2\n','1','','-1','0','1','HOUR','module.tpl'),
 ('27','right','Поиск','Поиск','1','mod_search','1','0','0','0','---\n','1','','-1','0','1','HOUR','module.tpl'),
 ('19','top','Последние материалы','Последние материалы','1','mod_latest','2','1','0','0','---\nnewscount: 2\nshowdesc: 0\nshowdate: 1\nshowcom: 1\nshowrss: 1\ncat_id: 6\nmenuid: 10\nsubs: 1\n','1','','-1','0','1','HOUR','module.tpl'),
 ('20','left','Авторизация','Авторизация','1','mod_auth','20','1','0','0','---\nautolog: 1\npassrem: 1\nshowtype: menu\n','1','','-1','0','1','MINUTE','module.tpl'),
 ('22','right','Последние регистрации','Новые пользователи','1','mod_lastreg','2','1','0','0','---\nnewscount: 5\nmenuid: 15\nview_type: table\n','1','','-1','0','1','HOUR','module.tpl'),
 ('23','left','Случайное изображение','Случайная картинка','1','mod_random_image','21','1','0','0','---\nshowtitle: 1\nalbum_id: 0\nmenuid: 20\n','1','','-1','0','1','HOUR','module.tpl'),
 ('24','right','Часы','Часы','1','mod_clock','17','1','0','0','---\n','1','','-1','0','1','HOUR','module.tpl'),
 ('37','right','Категории прайса','Категории прайса','1','mod_pricecat','14','1','0','0','---\nshowdesc: 0\nicon: /images/markers/pricelist.png\nmenuid: 22\n','1','','-1','0','1','HOUR','module.tpl'),
 ('36','right','Разделы сайта','Статьи','1','mod_category','13','1','0','0','---\nshowdesc: 0\ncategory_id: 6\nicon: /images/markers/folder.png\nmenuid: 21\n','1','','-1','0','1','HOUR','module.tpl'),
 ('39','right','Выбор шаблона','Выбор шаблона','1','mod_template','12','1','0','0','---\n','1','','-1','0','1','HOUR','module.tpl'),
 ('47','bottom','Популярное в каталоге','Популярное в каталоге','1','mod_uc_popular','23','1','0','0','---\nnum: 10\ncat_id: 0\nmenuid: 23\nshowf: 2\nshowtype: thumb\nfulllink: 1\nsort: rating\n','1','','-1','0','1','HOUR','module.tpl'),
 ('49','left','Кто онлайн?','Кто онлайн?','1','mod_whoonline','24','1','0','0','---\nnewscount: \nmenuid: 13\nview_type: \n','1','','-1','0','1','HOUR','module.tpl'),
 ('50','top','Темы на форуме','Новости форума','1','mod_forum','31','1','0','0','---\nshownum: 2\nshowtype: web2\nshowforum: 0\nshowlink: 0\nshowtext: 0\nmenuid: 18\n','1','','-1','0','1','HOUR','module.tpl'),
 ('51','left','Случайное фото','Случайное фото','1','mod_user_image','25','1','0','0','---\nshowtitle: 1\nmenuid: 15\n','1','','-1','0','1','HOUR','module.tpl'),
 ('52','right','Внешний файл','Внешний файл','0','<p>{ФАЙЛ=test.php}</p>','11','1','0','1','---\n','0','','-1','0','1','HOUR','module.tpl'),
 ('56','right','Архив статей','Архив новостей','1','mod_arhive','27','1','0','0','---\ncat_id: -1\nsource: both\nmenuid: 14\n','1','','-1','0','1','HOUR','module.tpl'),
 ('54','right','Случайное в каталоге','Случайное в каталоге','1','mod_uc_random','26','1','0','0','---\ncat_id: 1\ncount: 2\nshowtitle: 1\nshowcat: 0\nmenuid: 23\n','1','','-1','0','1','HOUR','module.tpl'),
 ('60','top','Лента RSS ','Лента новостей','1','mod_rss','9','1','0','0','---\nshowdesc: 0\nshowicon: 1\nitemslimit: 6\nrssurl: http://portal.novator.ru/ngnews.rss\ncols: 2\n','1','','-1','0','1','HOUR','module.tpl'),
 ('61','top','Последние комментарии','Последние комментарии','1','mod_comments','3','1','0','0','---\nshownum: 10\nshowrss: 1\nmenuid: 0\nminrate: 0\ntargets: \n  article: article\n  photo: photo\n  palbum: palbum\n  blog: blog\n  catalog: catalog\n  userphoto: userphoto\n','1','','-1','0','1','MINUTE','module.tpl'),
 ('62','top','Новинки фотогалереи','Новинки фотогалереи','1','mod_latestphoto','32','1','0','0','---\nshownum: 6\nmaxcols: 3\nshowalbum: 0\nshowdate: 0\nshowcom: 1\nalbum_id: 1\nmenuid: 20\nshowtype: full\nshowmore: 1\nshowclubs: 1\n','1','','-1','0','1','HOUR','module.tpl'),
 ('63','right','Популярные фотографии','Популярные фотографии','1','mod_bestphoto','10','1','0','0','---\nshownum: 2\nmaxcols: 2\nshowalbum: 0\nshowdate: 1\nshowcom: 1\nalbum_id: 0\nsort: rating\nmenuid: 20\nshowtype: full\nshowmore: 1\n','1','','-1','0','1','HOUR','module.tpl'),
 ('64','top','Новое в блогах','Новое в блогах','1','mod_latestblogs','1','1','0','0','---\nshownum: 4\nshowrss: 1\nmenuid: 17\nminrate: 0\n','1','','-1','0','1','HOUR','module.tpl'),
 ('65','top','Популярное в блогах','Популярное в блогах','1','mod_bestblogs','32','1','0','0','---\nshownum: 10\nmenuid: 17\n','1','','-1','0','1','HOUR','module.tpl'),
 ('66','left','Меню пользователя','Меню пользователя','1','mod_usermenu','5','0','0','0','---\navatar: 1\nshowtype: text\n','1','','-1','0','1','HOUR','module.tpl'),
 ('67','bottom','Последние вопросы FAQ','Последние вопросы FAQ','1','mod_latest_faq','33','1','0','0','---\nnewscount: 2\ncat_id: -1\nmenuid: 13\nsubs: 0\n','1','','-1','0','1','HOUR','module.tpl'),
 ('68','top','Горизонтальное меню','Горизонтальное меню','1','mod_hmenu','2','0','0','0','---\n','1','','-1','0','1','HOUR','module.tpl'),
 ('69','top','Популярные статьи','Популярные статьи','1','mod_bestcontent','3','1','0','0','---\nshownum: 4\nmenuid: 21\nshowlink: 1\nshowdesc: 1\n','1','','-1','0','1','HOUR','module.tpl'),
 ('70','right','Поиск пользователей','Поиск пользователей','1','mod_usersearch','4','1','0','0','---\ncat_id: \nsource: \nmenuid: 15\n','1','','-1','0','1','HOUR','module.tpl'),
 ('71','top','Новые объявления','Новые объявления','1','mod_latestboard','1','1','0','0','---\nshownum: 10\nshowcity: 1\nshowrss: 1\ncat_id: -1\nmenuid: 37\nsubs: 0\n','1','','-1','0','1','HOUR','module.tpl'),
 ('72','top','Рейтинг пользователей','Рейтинг пользователей','1','mod_user_rating','1','1','0','0','---\ncount: 20\nmenuid: 15\nview_type: rating\n','1','','-1','0','1','HOUR','module.tpl'),
 ('73','top','Новые клубы','Новые клубы','1','mod_latestclubs','3','1','0','0','---\ncount: 2\nmenuid: 38\n','1','','-1','0','1','HOUR','module.tpl'),
 ('74','top','Популярные клубы','Популярные клубы','1','mod_bestclubs','4','1','0','0','---\ncount: 2\nmenuid: 38\n','1','','-1','0','1','HOUR','module.tpl'),
 ('75','left','Доска почета','Доска почета','1','mod_respect','1','1','0','0','---\nview: all\nshow_awards: 0\norder: desc\nlimit: 5\n','1','','-1','0','1','HOUR','module.tpl'),
 ('76','right','Файлы пользователей','Файлы пользователей','1','mod_userfiles','1','1','0','0','---\nmenuid: 0\nsw_stats: 1\nsw_latest: 1\nsw_popular: 1\nnum_latest: 5\nnum_popular: 5\n','1','','-1','0','1','HOUR','module.tpl');

INSERT INTO `cms_photo_albums` (`id`, `parent_id`, `ordering`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `title`, `description`, `published`, `showdate`, `iconurl`, `pubdate`, `orderby`, `orderto`, `public`, `perpage`, `cssprefix`, `thumb1`, `thumb2`, `thumbsqr`, `showtype`, `nav`, `uplimit`, `maxcols`, `orderform`, `showtags`, `bbcode`, `user_id`, `is_comments`) VALUES ('100','0','1','1','2','','0','0','-- Корневой альбом --','','1','1','','2008-05-30 12:03:07','title','asc','0','15','','96','480','1','list','1','0','4','1','1','1','1','0');

INSERT INTO `cms_plugins` (`id`, `plugin`, `title`, `description`, `author`, `version`, `plugin_type`, `published`, `config`) VALUES ('6','p_usertab','Demo Profile Plugin','Пример плагина - Добавляет вкладку "Статьи" в профили всех пользователей','InstantCMS Team','1.0','plugin','0','---\nКоличество статей: 10\n'),
 ('3','p_fckeditor','FCKEditor','Визуальный редактор','F. C. Knabben','2.63','wysiwyg','1','---\n'),
 ('5','p_demo','Demo Plugin','Пример плагина - Добавляет текст в конец каждой статьи на сайте','InstantCMS Team','1.0','plugin','0','---\ntext: Added By Plugin From Parameter\ncolor: blue\ncounter: 1\n');

INSERT INTO `cms_user_autoawards` (`id`, `title`, `description`, `imageurl`, `p_comment`, `p_blog`, `p_forum`, `p_photo`, `p_privphoto`, `p_content`, `p_karma`, `published`) VALUES ('1','Медаль','Почетный член форума','aw.gif','0','0','100','0','0','0','0','1'),
 ('2','Грамота','Почетный комментатор сайта','aw4.gif','100','5','50','0','0','0','0','1');

INSERT INTO `cms_user_groups` (`id`, `title`, `alias`, `is_admin`, `access`) VALUES ('1','Пользователи','registered','0','comments/add, comments/delete, content/add, board/autoadd'),
 ('2','Администраторы','admin','1','admin/content, admin/com_rssfeed, admin/com_arhive, admin/com_banners, admin/com_blog, admin/com_faq, admin/com_board, admin/com_content, admin/com_clubs, admin/com_comments, admin/com_forms, admin/com_autoawards, admin/com_search, admin/com_price, admin/com_users, admin/com_registration, admin/com_statistics, admin/com_catalog, admin/com_forum, admin/com_photos'),
 ('8','Гости','guest','0','comments/add'),
 ('7','Редакторы','editors','0','comments/add, comments/delete, content/add, content/autoadd, content/delete'),
 ('9','Модераторы','moderators','0','comments/add, comments/delete, comments/moderate, forum/moderate, content/add');

INSERT INTO `cms_user_profiles` (`id`, `user_id`, `city`, `description`, `showmail`, `showbirth`, `showicq`, `karma`, `imageurl`, `allow_who`, `signature`, `gender`, `formsdata`, `email_newmsg`, `cm_subscribe`, `stats`) VALUES ('1','1','Москва','it, web, php','1','1','1','1','4a8b60d375f8abbebe53aa0bd881e8ef.jpg','all','','m','---\n22: Minimal techno\n24: Высшее\n','1','none','---\ncount: \n  comments: 2\n  forum: 1\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
 ('21','21','Москва','живопись, &#8217;музыка&#8217;, всякая "всячина" однако','0','0','1','-2','067f873e2a156fcf736c26c3b6df6349.jpg','all','','m','---\n22: |\n  &#8217;музыка&#8217;, всякая &quot;всячина&quot; однако\n24: Высшее\n','1','0','---\ncount: \n  comments: 2\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n');

INSERT INTO `cms_users` (`id`, `group_id`, `login`, `nickname`, `password`, `email`, `icq`, `regdate`, `logdate`, `birthdate`, `is_locked`, `is_deleted`, `rating`, `points`, `last_ip`, `status`, `status_date`) VALUES ('1','2','admin','Администратор','2ca41752ccf4dbdb76d8fe88c488fd44','admin@cms.ru','100200300','2007-11-23 12:41:57','2009-09-01 12:59:21','1980-01-01','0','0','11','0','127.0.0.1','status the best','2009-08-29 14:08:32'),
 ('21','2','vasya','Василий','2ca41752ccf4dbdb76d8fe88c488fd44','r2@novator.ru','100200300','2008-07-16 16:31:48','2009-08-29 14:40:40','1980-01-01','0','1','11','0','127.0.0.1','Hello, World!','2009-08-20 17:17:37');
