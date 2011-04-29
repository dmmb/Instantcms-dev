DROP TABLE IF EXISTS `#__actions`;
CREATE TABLE `#__actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `component` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_tracked` tinyint(4) NOT NULL,
  `is_visible` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`is_visible`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__actions` (`id`, `component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
(2, 'comments', 'add_comment', '���������� �����������', '��������� %s| �� �������� %s', 1, 1),
(7, 'photos', 'add_photo', '���������� ����', '��������� ���� %s| � ������ %s', 1, 1),
(8, 'content', 'add_article', '���������� ������', '��������� ������ %s| � ������ %s', 1, 1),
(9, 'blogs', 'add_blog', '�������� ����� �������������', '������� ���� %s|', 1, 1),
(10, 'blogs', 'add_post', '���������� ������ � ����', '����� ���� %s| � ����� %s', 1, 1),
(11, 'users', 'set_status', '��������� ������� ������������', '', 1, 1),
(12, 'board', 'add_board', '���������� ����������', '��������� ���������� %s| � ������� %s', 1, 1),
(13, 'catalog', 'add_catalog', '���������� ������ � �������', '��������� ������ %s| � ������� �������� %s', 1, 1),
(14, 'clubs', 'add_club', '�������� �����', '������� ���� %s|', 1, 1),
(15, 'clubs', 'add_club_user', '���������� � ����', '�������� � ���� %s|', 1, 1),
(16, 'faq', 'add_quest', '������', '������ %s| � ��������� %s', 1, 1),
(17, 'forum', 'add_fpost', '���������� ����� � ������', '��������� %s| � ���� %s', 1, 1),
(18, 'forum', 'add_thread', '���������� ���� �� ������', '������� ���� %s| �� ������ %s', 1, 1),
(19, 'users', 'add_avatar', '�������� ��� ����� ������� �������������', '�������� ������|', 1, 1),
(20, 'users', 'add_friend', '���������� �����', '� %s ����� ��������|', 1, 1),
(21, 'users', 'add_award', '��������� ������� �������������', '�������� ������� %s|', 1, 1),
(22, 'users', 'add_file', '�������� ����� �������������', '��������� ���� %s|', 1, 1),
(23, 'users', 'add_wall', '���������� ������ �� �����', '��������� ������ �� ����� ������������ %s|', 1, 1),
(24, 'clubs', 'add_wall_club', '���������� ������ �� ����� �����', '��������� ������ �� ����� ����� %s|', 1, 1),
(25, 'clubs', 'add_post_club', '���������� ������ � ���� �����', '����� ���� %s| � ����� %s', 1, 1),
(26, 'users', 'add_user_photo', '���������� ���� � ������ ������', '��������� ���� %s| � ������ %s', 1, 1),
(27, 'users', 'add_user_photo_multi', '���������� ����� ����� � ������ ������', '��������� %s ����| � ������ %s', 1, 1),
(28, 'registration', 'add_user', '����������� ������ ������������', '��������������. ������������!|', 1, 1),
(29, 'users', 'add_wall_my', '���������� ������ �� ���� �����', '����� �� ����� �����|	', 1, 1);

DROP TABLE IF EXISTS `#__actions_log`;
CREATE TABLE `#__actions_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `object` varchar(100) NOT NULL,
  `object_url` varchar(255) NOT NULL,
  `object_id` int(11) NOT NULL,
  `target` varchar(100) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `target_id` int(11) NOT NULL,
  `description` varchar(650) DEFAULT NULL,
  `is_friends_only` tinyint(4) NOT NULL DEFAULT '0',
  `is_users_only` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `action_id` (`action_id`,`user_id`),
  KEY `object_id` (`object_id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__banlist`;
CREATE TABLE `#__banlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `bandate` datetime NOT NULL,
  `int_num` int(11) NOT NULL,
  `int_period` varchar(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `autodelete` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__banners`;
CREATE TABLE `#__banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(100) NOT NULL DEFAULT 'banner_top',
  `typeimg` varchar(10) NOT NULL DEFAULT 'image',
  `fileurl` varchar(250) DEFAULT NULL,
  `hits` int(11) NOT NULL,
  `clicks` int(11) NOT NULL,
  `maxhits` int(11) NOT NULL,
  `maxuser` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `link` varchar(250) DEFAULT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__banners` (`id`, `position`, `typeimg`, `fileurl`, `hits`, `clicks`, `maxhits`, `maxuser`, `user_id`, `pubdate`, `title`, `link`, `published`) VALUES
(4, 'banner1', 'image', 'banner468x60v1.gif', 0, 0, 0, 0, 1, '2009-04-04 19:43:53', 'InstantCMS - ���������� ������� ���������� ������', 'http://www.instantcms.ru/', 1);

DROP TABLE IF EXISTS `#__banner_hits`;
CREATE TABLE `#__banner_hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_id` int(11) DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `pubdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__blogs`;
CREATE TABLE `#__blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `pubdate` datetime NOT NULL,
  `allow_who` varchar(15) NOT NULL,
  `view_type` varchar(15) NOT NULL DEFAULT 'list',
  `showcats` int(11) NOT NULL DEFAULT '1',
  `ownertype` varchar(15) NOT NULL DEFAULT 'single',
  `premod` int(11) NOT NULL,
  `forall` int(11) NOT NULL DEFAULT '1',
  `owner` varchar(10) NOT NULL DEFAULT 'user',
  `seolink` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seolink` (`seolink`),
  KEY `user_id` (`user_id`),
  KEY `pubdate` (`pubdate`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__blog_authors`;
CREATE TABLE `#__blog_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `startdate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `blog_id` (`blog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__blog_cats`;
CREATE TABLE `#__blog_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_id` (`blog_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__blog_files`;
CREATE TABLE `#__blog_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `filesize` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__blog_posts`;
CREATE TABLE `#__blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `title` varchar(250) NOT NULL,
  `feel` varchar(100) NOT NULL,
  `music` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `content_html` text NOT NULL,
  `allow_who` varchar(20) NOT NULL,
  `edit_times` int(11) NOT NULL,
  `edit_date` datetime NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `seolink` varchar(255) NOT NULL,
  `comments` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `seolink` (`seolink`),
  KEY `blog_id` (`blog_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__board_cats`;
CREATE TABLE `#__board_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '1',
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSDiffer` varchar(15) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `published` int(11) NOT NULL,
  `orderform` int(11) DEFAULT '1',
  `showdate` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL,
  `orderby` varchar(20) NOT NULL DEFAULT 'title',
  `orderto` varchar(4) NOT NULL DEFAULT 'asc',
  `public` int(11) NOT NULL,
  `perpage` int(11) NOT NULL DEFAULT '15',
  `maxcols` int(11) NOT NULL DEFAULT '1',
  `thumb1` int(11) NOT NULL DEFAULT '64',
  `thumb2` int(11) NOT NULL DEFAULT '400',
  `thumbsqr` int(11) NOT NULL,
  `uplimit` int(11) NOT NULL DEFAULT '10',
  `is_photos` int(11) NOT NULL DEFAULT '1',
  `icon` varchar(25) DEFAULT 'folder_grey.png',
  `obtypes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__board_cats` (`id`, `parent_id`, `ordering`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `title`, `description`, `published`, `orderform`, `showdate`, `pubdate`, `orderby`, `orderto`, `public`, `perpage`, `maxcols`, `thumb1`, `thumb2`, `thumbsqr`, `uplimit`, `is_photos`, `icon`, `obtypes`) VALUES
(1, 0, 1, 1, 8, '', 0, 0, '-- �������� ������� --', '', 1, 1, 1, '2008-09-22 13:39:32', 'pubdate', 'desc', 0, 15, 1, 64, 400, 0, 10, 1, 'folder_grey.png', ''),
(10, 1, 21, 2, 3, '', 0, 1, '������', '', 1, 1, 1, '2008-09-22 14:30:29', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', '���������\r\n���������'),
(9, 1, 23, 6, 7, '', 0, 1, '����������', '', 1, 1, 1, '2008-09-22 14:30:23', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', '�����\r\n������\r\n�������'),
(8, 1, 22, 4, 5, '', 0, 1, '������������', '', 1, 1, 1, '2008-09-22 14:30:00', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', '������\r\n�����\r\n�������\r\n����\r\n�����');

DROP TABLE IF EXISTS `#__board_items`;
CREATE TABLE `#__board_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `obtype` varchar(50) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `pubdate` datetime DEFAULT NULL,
  `pubdays` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `file` varchar(250) NOT NULL,
  `hits` int(11) NOT NULL,
  `is_vip` tinyint(4) NOT NULL DEFAULT '0',
  `vipdate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `obtype` (`obtype`),
  KEY `city` (`city`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__cache`;
CREATE TABLE `#__cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(10) NOT NULL,
  `target_id` varchar(255) NOT NULL,
  `cachedate` datetime NOT NULL,
  `cachefile` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__category`;
CREATE TABLE `#__category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL,
  `showdate` int(11) NOT NULL DEFAULT '1',
  `showcomm` int(11) NOT NULL DEFAULT '1',
  `orderby` varchar(30) NOT NULL DEFAULT 'date',
  `orderto` varchar(4) NOT NULL DEFAULT 'asc',
  `modgrp_id` int(11) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `NSDiffer` varchar(11) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `maxcols` int(11) NOT NULL DEFAULT '1',
  `showtags` int(11) NOT NULL DEFAULT '1',
  `showrss` int(11) NOT NULL DEFAULT '1',
  `showdesc` int(11) NOT NULL,
  `is_public` int(11) NOT NULL,
  `photoalbum` text NOT NULL,
  `seolink` varchar(200) NOT NULL,
  `url` varchar(100) NOT NULL,
  `tpl` varchar(50) NOT NULL DEFAULT 'com_content_view.tpl',
  `cost` varchar(5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `seolink` (`seolink`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__category` (`id`, `parent_id`, `title`, `description`, `published`, `showdate`, `showcomm`, `orderby`, `orderto`, `modgrp_id`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `maxcols`, `showtags`, `showrss`, `showdesc`, `is_public`, `photoalbum`, `seolink`, `url`, `tpl`, `cost`) VALUES
(1, 0, '--�������� ������--', '�������� ������ �����', 1, 1, 1, 'pubdate', 'asc', 0, 1, 2, 0, '', 0, 1, 1, 1, 1, 0, 0, '', '--kornevoi-razdel--', '', 'com_content_view.tpl', '');

DROP TABLE IF EXISTS `#__clubs`;
CREATE TABLE `#__clubs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '1',
  `title` text NOT NULL,
  `description` longtext NOT NULL,
  `imageurl` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `clubtype` varchar(15) NOT NULL DEFAULT 'public',
  `published` int(11) NOT NULL DEFAULT '1',
  `maxsize` int(11) NOT NULL,
  `enabled_blogs` int(11) DEFAULT '1',
  `enabled_photos` int(11) DEFAULT '1',
  `rating` int(11) NOT NULL,
  `photo_premod` int(11) NOT NULL,
  `blog_premod` int(11) NOT NULL,
  `blog_min_karma` int(11) NOT NULL,
  `photo_min_karma` int(11) NOT NULL,
  `album_min_karma` int(11) NOT NULL DEFAULT '25',
  `join_min_karma` int(11) NOT NULL,
  `join_karma_limit` int(11) NOT NULL,
  `create_karma` int(11) NOT NULL,
  `is_vip` tinyint(4) NOT NULL DEFAULT '0',
  `join_cost` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pubdate` (`pubdate`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__codecheck`;
CREATE TABLE `#__codecheck` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place` varchar(200) NOT NULL,
  `code` varchar(40) NOT NULL,
  `session_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__comments`;
CREATE TABLE `#__comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `target` varchar(20) NOT NULL,
  `target_id` int(11) NOT NULL,
  `guestname` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `content_bbcode` text NOT NULL,
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `is_new` int(11) NOT NULL DEFAULT '1',
  `target_title` varchar(150) NOT NULL,
  `target_link` varchar(200) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__comments_votes`;
CREATE TABLE `#__comments_votes` (
  `comment_id` int(11) NOT NULL,
  `comment_type` varchar(3) NOT NULL,
  `vote` smallint(6) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `comment_id` (`comment_id`,`comment_type`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__comment_targets`;
CREATE TABLE `#__comment_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(32) NOT NULL,
  `component` varchar(32) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target` (`target`,`component`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__comment_targets` (`id`, `target`, `component`, `title`) VALUES
(1, 'article', 'content', '������'),
(2, 'blog', 'blogs', '����� ������'),
(3, 'palbum', 'photos', '�����������'),
(4, 'photo', 'photos', '���������� �������'),
(5, 'userphoto', 'users', '���������� �������������'),
(6, 'catalog', 'catalog', '������ ��������'),
(7, 'boarditem', 'board', '����������'),
(8, 'faq', 'faq', '������� FAQ');

DROP TABLE IF EXISTS `#__components`;
CREATE TABLE `#__components` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `link` varchar(200) NOT NULL,
  `config` text NOT NULL,
  `internal` int(11) NOT NULL,
  `author` varchar(200) NOT NULL DEFAULT 'InstantCMS team',
  `published` int(11) NOT NULL DEFAULT '1',
  `version` varchar(6) NOT NULL DEFAULT '1.5',
  `system` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__components` (`id`, `title`, `link`, `config`, `internal`, `author`, `published`, `version`, `system`) VALUES
(1, '������� ������', 'content', '---\nreaddesc: 0\nrating: 1\nperpage: 15\npt_show: 1\npt_disp: 1\npt_hide: 1\nautokeys: 1\nimg_small_w: 100\nimg_big_w: 200\nimg_sqr: 1\nimg_users: 1\nwatermark: 1\nwatermark_only_big: 1\naf_on: 0\naf_delete: 1\naf_showlink: 1\naf_forum_id: 1\naf_hidecat_id: 2\n', 0, 'InstantCMS team', 1, '1.5', 1),
(2, '�����������', 'registration', '---\nreg_type: open\ninv_count: 3\ninv_karma: 1\ninv_period: WEEK\nis_on: 1\nact: 0\nsend: false\noffmsg: >\n  ����������� �������������� ��\n  ����������� ��������.\nfirst_auth_redirect: profile\nauth_redirect: profile\nname_mode: nickname\nbadnickname: |\n  �������������\n  �����\n  qwert\n  qwerty\n  123\n  admin\n  ���� ������\nask_icq: 1\nask_birthdate: 1\nsend_greetmsg: 0\ngreetmsg: |\n  <span style="font-size: medium;"><br />\n  </span>\n  <p>&nbsp;</p>\n', 0, 'InstantCMS team', 1, '1.5', 1),
(3, '�����������', 'photos', '---\nlink: 0\nsaveorig: 0\nmaxcols: 2\norderby: title\norderto: desc\nshowlat: 1\nwatermark: 1\ntumb_view: 2\ntumb_from: 1\ntumb_club: \nis_today: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(4, '�����-����', 'price', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(5, '�����', 'search', '---\nperpage: 10\ncomp: \n  - content\n  - photos\n  - price\n  - catalog\n  - forum\n  - blogs\n  - board\n', 0, 'InstantCMS team', 1, '1.5', 1),
(6, '����������� ����', 'forms', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(7, '����������� �������������', 'comments', '---\nemail: \ncanguests: 1\nregcap: 0\npublish: 1\nsmiles: 1\nbbcode: 1\nselfdel: 0\nsubscribe: 1\nanchors: 0\nrecode: 0\nmin_karma: 0\nmin_karma_show: 0\nmin_karma_add: 0\nperpage: 20\nj_code: 1\ncmm_ajax: 0\ncmm_ip: 1\n', 1, 'InstantCMS team', 1, '1.5', 1),
(8, '����� ����������', 'arhive', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(9, '������������� �������', 'catalog', '---\nemail: shop@site.ru\ndelivery: |\n  �������� � ��������.\n  ���� ����� ����� �������� � ���������� ���������� &quot;������������� �������&quot;.\nnotice: 1\npremod: 1\npremod_msg: 1\nis_comments: 1\nis_rss: 1\nwatermark: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(10, '������� �������������', 'users', '---\nshowgroup: 1\nsw_stats: \nsw_comm: 1\nsw_search: 1\nsw_forum: 1\nsw_photo: 1\nsw_wall: 1\nsw_friends: 1\nsw_blogs: 1\nsw_clubs: 1\nsw_feed: 1\nsw_content: 1\nsw_awards: 1\nsw_board: 1\nsw_msg: 1\nsw_guest: 1\nkarmatime: 1\nkarmaint: DAY\nphotosize: 0\nwatermark: 1\nsmallw: 64\nmedw: 200\nmedh: 500\nsw_files: 1\nfilessize: 100\nfilestype: jpeg,gif,png,jpg,bmp,zip,rar,tar\nprivforms: \n  - 3\nj_code: 1\ndeltime: 6\n', 0, 'InstantCMS team', 1, '1.5', 1),
(12, '�����', 'forum', '---\nis_on: 1\nkarma: 1\nis_rss: 1\npp_thread: 15\npp_forum: 15\nshowimg: 1\nimg_on: 1\nimg_max: 5\nfast_on: 1\nfast_bb: 1\nfa_on: 1\nfa_allow: -1\nfa_max: 25\nfa_ext: txt doc zip rar arj png gif jpg jpeg bmp\nfa_size: 1024\n', 0, 'InstantCMS team', 1, '1.5', 1),
(13, '���������� �����', 'statistics', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(15, '�����', 'blogs', '---\nperpage: 10\nperpage_blog: 15\nupdate_date: 0\nupdate_seo_link: 0\nmin_karma_private: 0\nmin_karma_public: 5\nmin_karma: 1\nwatermark: 1\nimg_on: 1\nrss_all: 1\nrss_one: 1\nj_code: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(16, '������� � ������', 'faq', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(17, '�������', 'banners', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(19, 'RSS ���������', 'rssfeed', '---\naddsite: 1\nmaxitems: 50\nicon_on: 1\nicon_url: http://icms/images/rss.png\nicon_title: InstantCMS\nicon_link: http://icms/images/rss.png\n', 1, 'InstantCMS team', 1, '1.5', 1),
(21, '����������� �������������', 'autoawards', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(22, '����� ����������', 'board', '---\nmaxcols: 3\nobtypes: |\n  ������\n  �����\n  �������\n  ������\nshowlat: \npublic: 2\nphotos: 1\nsrok: 1\npubdays: 10\nwatermark: 0\naftertime: \ncomments: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(23, '����� �������������', 'clubs', '---\nseo_club: title\nenabled_blogs: 1\nenabled_photos: 1\nthumb1: 48\nthumb2: 200\nthumbsqr: 1\ncancreate: 1\nperpage: 10\ncreate_min_karma: 0\ncreate_min_rating: 0\n', 0, 'InstantCMS team', 1, '1.5', 1);

DROP TABLE IF EXISTS `#__content`;
CREATE TABLE `#__content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enddate` date NOT NULL,
  `is_end` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `content` longtext,
  `published` int(11) NOT NULL DEFAULT '1',
  `hits` int(11) NOT NULL,
  `meta_desc` text NOT NULL,
  `meta_keys` text NOT NULL,
  `showtitle` int(11) NOT NULL DEFAULT '1',
  `showdate` int(11) NOT NULL DEFAULT '1',
  `showlatest` int(11) NOT NULL DEFAULT '1',
  `showpath` int(11) NOT NULL DEFAULT '1',
  `ordering` int(1) NOT NULL,
  `comments` int(11) NOT NULL DEFAULT '1',
  `is_arhive` int(11) NOT NULL,
  `seolink` varchar(200) NOT NULL,
  `canrate` int(11) NOT NULL DEFAULT '1',
  `pagetitle` varchar(255) NOT NULL,
  `url` varchar(100) NOT NULL,
  `tpl` varchar(50) NOT NULL DEFAULT 'com_content_read.tpl',
  PRIMARY KEY (`id`),
  UNIQUE KEY `seolink` (`seolink`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__content_access`;
CREATE TABLE `#__content_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content_type` varchar(100) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__cron_jobs`;
CREATE TABLE `#__cron_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(50) NOT NULL,
  `job_interval` smallint(6) NOT NULL DEFAULT '1',
  `job_run_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `component` varchar(20) NOT NULL,
  `model_method` varchar(100) NOT NULL,
  `custom_file` varchar(250) NOT NULL,
  `is_enabled` tinyint(4) NOT NULL DEFAULT '1',
  `is_new` smallint(6) NOT NULL DEFAULT '1',
  `comment` varchar(200) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `class_method` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `job_name` (`job_name`,`is_enabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__cron_jobs` (`id`, `job_name`, `job_interval`, `job_run_date`, `component`, `model_method`, `custom_file`, `is_enabled`, `is_new`, `comment`, `class_name`, `class_method`) VALUES
(1, 'photos_clear', 24, '2010-12-06 21:11:26', 'users', 'clearUploadedPhotos', '', 1, 1, '�������� �������������� ������ ����������', '', ''),
(2, 'optimize_tables', 24, '2010-12-06 21:11:26', '', '', '', 1, 1, '����������� ������ ��', 'db|cmsDatabase', 'optimizeTables'),
(3, 'drop_inactive_users', 48, '2010-12-06 21:11:26', 'users', 'deleteInactiveUsers', '', 1, 1, '�������� ���������� ������������� (��. ��������� ���������� "������� �������������")', '', ''),
(4, 'remove_old_log', 48, '2010-12-06 21:11:26', '', '', '', 1, 1, '������� ������ ������ ����� �������', 'actions|cmsActions', 'removeOldLog'),
(5, 'give_invites', 24, '2010-12-06 21:11:26', 'users', 'giveInvitesCron', '', 1, 1, '������ �������� �������������', '', ''),
(6, 'clear_invites', 24, '2010-12-06 21:11:26', 'users', 'clearInvites', '', 1, 1, '�������� �������������� ��������', '', '');

DROP TABLE IF EXISTS `#__downloads`;
CREATE TABLE `#__downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fileurl` varchar(250) NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__event_hooks`;
CREATE TABLE `#__event_hooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(50) NOT NULL,
  `plugin_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event` (`event`,`plugin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__event_hooks` (`id`, `event`, `plugin_id`) VALUES
(6, 'GET_ARTICLE', '5'),
(3, 'INSERT_WYSIWYG', '3'),
(7, 'USER_PROFILE', '6'),
(11, 'ADD_ARTICLE_DONE', '8'),
(10, 'ADD_POST_DONE', '8'),
(34, 'GET_FORUM_POSTS', '14'),
(33, 'GET_COMMENTS', '14'),
(32, 'GET_BEST_POSTS', '14'),
(31, 'GET_LATEST_POSTS', '14'),
(30, 'GET_POST', '14'),
(29, 'GET_POSTS', '14'),
(35, 'GET_WALL_POSTS', '14'),
(36, 'GET_ARTICLE', '15');

DROP TABLE IF EXISTS `#__faq_cats`;
CREATE TABLE `#__faq_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__faq_quests`;
CREATE TABLE `#__faq_quests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `quest` text NOT NULL,
  `answer` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `answeruser_id` int(11) NOT NULL,
  `answerdate` datetime NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__filters`;
CREATE TABLE `#__filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(100) NOT NULL,
  `published` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__filters` (`id`, `title`, `description`, `link`, `published`) VALUES
(1, '���������� / ������ ������', '������ ��������� ������ ��������� �� ��������� ���� ��������, � ��� �� ������ ��� �������� ������ � ��������� ����������, ����������� � ����������.<br/><br/>{�����=�������� ���������}, <br/>{����=�������� ����������}, <br/>{������=�������� �����������}, <br/>{��������=�������� ���������}<br/>{�����=�������� �����} - ����� � ����������<br/>\r\n{�����=�������� �����} - ����� ��� ���������', 'f_replace', 1),
(4, '������������ �����', '������ ��������� ����� ��������� �� ��������� ������� � ��� ������ ��� ����� ������ ��� {pagebreak}.', 'f_pages', 1),
(2, '���������� ������', '������ ���� ���� {��������=�������� ��������} � ������� ������, � �������� �� �� ����� � ���������� ������.', 'f_contents', 1),
(5, '������� �������', '������ ������� � ������� ������ � ������� ��������� "{����=script.php}" � �������� �� ������������ ���������� ��������������� �������� �� ����� "/includes/myphp/".', 'f_includes', 1),
(6, '������� ����������', '������ ������� � ������� ������ � ������� ��������� "{�������=/path/file.zip}" � �������� �� ������� ��� �������� ���������� �����, ���������� ��������� ����������.', 'f_filelink', 1),
(7, '������� �������', '������ �������� ��������� ���� {������=���_�������} �� �������, ����������� �� ��������� �������. �������� � ������� � �������.', 'f_banners', 1);

DROP TABLE IF EXISTS `#__filter_rules`;
CREATE TABLE `#__filter_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `find` varchar(200) NOT NULL,
  `replace` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forms`;
CREATE TABLE `#__forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `sendto` varchar(4) NOT NULL DEFAULT 'mail',
  `user_id` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__form_fields`;
CREATE TABLE `#__form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `ordering` int(11) NOT NULL,
  `kind` varchar(30) NOT NULL,
  `mustbe` int(11) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forums`;
CREATE TABLE `#__forums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `auth_group` int(11) NOT NULL DEFAULT '-1',
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `parent_id` int(11) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSDiffer` varchar(15) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `topic_cost` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forums` (`id`, `category_id`, `title`, `description`, `auth_group`, `ordering`, `published`, `parent_id`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `topic_cost`) VALUES
(1000, 0, '-- ������ ������� --', '', 0, 1, 0, 0, 1, 8, '', 0, 1, 0);

DROP TABLE IF EXISTS `#__forum_cats`;
CREATE TABLE `#__forum_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `auth_group` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_group` (`auth_group`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forum_files`;
CREATE TABLE `#__forum_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `filesize` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forum_images`;
CREATE TABLE `#__forum_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `fileurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forum_polls`;
CREATE TABLE `#__forum_polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `answers` text NOT NULL,
  `options` varchar(250) NOT NULL,
  `enddate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forum_posts`;
CREATE TABLE `#__forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `editdate` datetime NOT NULL,
  `edittimes` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forum_threads`;
CREATE TABLE `#__forum_threads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `forum_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `hits` int(11) NOT NULL,
  `closed` int(11) NOT NULL,
  `pinned` int(11) NOT NULL,
  `is_hidden` int(11) NOT NULL DEFAULT '0',
  `rel_to` varchar(15) NOT NULL,
  `rel_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `forum_id` (`forum_id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__forum_votes`;
CREATE TABLE `#__forum_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__menu`;
CREATE TABLE `#__menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `link` varchar(200) NOT NULL,
  `linktype` varchar(12) NOT NULL DEFAULT 'link',
  `linkid` varchar(25) DEFAULT '-1',
  `target` varchar(8) NOT NULL DEFAULT '_self',
  `component` varchar(100) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '1',
  `published` int(11) NOT NULL,
  `template` varchar(30) NOT NULL,
  `access_list` tinytext NOT NULL,
  `iconurl` varchar(100) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `NSDiffer` varchar(40) DEFAULT NULL,
  `NSIgnore` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__menu` (`id`, `menu`, `title`, `link`, `linktype`, `linkid`, `target`, `component`, `ordering`, `published`, `template`, `access_list`, `iconurl`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `parent_id`) VALUES
(1, 'root', '-- ������� �������� --', '-1', 'link', '-1', '_self', '', 1, 0, '0', '', '', 1, 2, 0, '', 0, 0);

DROP TABLE IF EXISTS `#__modules`;
CREATE TABLE `#__modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(10) NOT NULL,
  `name` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `is_external` int(11) NOT NULL,
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `showtitle` int(11) NOT NULL DEFAULT '1',
  `published` int(11) NOT NULL DEFAULT '1',
  `user` int(11) NOT NULL DEFAULT '1',
  `config` text NOT NULL,
  `original` int(11) NOT NULL,
  `css_prefix` varchar(30) NOT NULL,
  `access_list` tinytext NOT NULL,
  `cache` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL DEFAULT '1',
  `cacheint` varchar(15) NOT NULL DEFAULT 'HOUR',
  `template` varchar(35) NOT NULL DEFAULT 'module.tpl',
  `is_strict_bind` tinyint(4) NOT NULL DEFAULT '0',
  `version` varchar(6) NOT NULL DEFAULT '1.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__modules` (`id`, `position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `access_list`, `cache`, `cachetime`, `cacheint`, `template`, `is_strict_bind`, `version`) VALUES
(1, 'topmenu', '����', '����', 1, 'mod_menu', 6, 0, 1, 0, '---\nmenu: mainmenu\njtree: 1\n', 1, '', '', 0, 1, 'HOUR', 'module_simple.tpl', 0, '1.0'),
(17, 'top', '������� ��������', '����� ����������!', 0, '<table cellspacing="0" cellpadding="0" border="0" width="100%">\r\n    <tbody>\r\n        <tr>\r\n            <td width="100" valign="top"><a target="_blank" href="http://www.instantcms.ru"><img border="0" alt="" src="/images/content/community.png" /></a></td>\r\n            <td>\r\n            <p class="moduletitle">����� ����������!</p>\r\n            <p>�� ���� �������������� ��� � ����� ���������� ����. ����� ����������� ��� ������ �������� ��� ������� �����.</p>\r\n            <p>�� ������� ������� ����, ��������� ���������� � �������� � ��������.</p>\r\n            <div>\r\n            <div>����� �������� ���� �����, <a href="/admin/index.php?view=modules&amp;do=edit&amp;id=17">�������������� ������ &quot;������� ��������&quot;</a>.</div>\r\n            </div>\r\n            </td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 0, 0, 1, 1, '---\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(44, 'sidebar', '������ �����', '������ �����', 1, 'mod_tags', 18, 0, 0, 0, '---\ncat_id: \nsortby: tag\nmenuid: \nminfreq: 0\nminlen: 3\ntargets: \n  content: content\n  photo: photo\n  blogpost: blog\n  catalog: catalog\n  userphoto: userphoto\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(46, 'mainbottom', '������� ��������', '������� ��������', 1, 'mod_uc_latest', 30, 1, 0, 0, '---\nnewscount: 6\nshowtype: list\nshowf: 2\ncat_id: \nsubs: 1\nfulllink: 0\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(26, 'sidebar', '������� ����������', '�������', 1, 'mod_cart', 19, 1, 0, 0, '---\nshowtype: list\nshowqty: 1\nmenuid: 23\nsource: catalog\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(25, 'sidebar', '�����������', '�����������', 1, 'mod_polls', 2, 1, 1, 0, '---\nshownum: 0\npoll_id: 2\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(27, 'sidebar', '�����', '�����', 1, 'mod_search', 1, 0, 0, 0, '---\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(19, 'maintop', '��������� ���������', '����� ������', 1, 'mod_latest', 2, 1, 1, 0, '---\nnewscount: 4\nshowdesc: 0\nshowdate: 1\nshowcom: 1\nshowrss: 1\ncat_id: 6\nsubs: 1\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(20, 'topmenu', '�����������', '�����������', 1, 'mod_auth', 0, 1, 1, 0, '---\nautolog: 1\npassrem: 1\n', 1, '', '', 0, 1, 'MINUTE', 'module.tpl', 0, '1.0'),
(22, 'topmenu', '��������� �����������', '����� ������������', 1, 'mod_lastreg', 2, 1, 1, 0, '---\nnewscount: 5\nview_type: hr_table\nmaxcool: 2\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(23, 'sidebar', '��������� �����������', '��������� ��������', 1, 'mod_random_image', 21, 1, 0, 0, '---\nshowtitle: 1\nalbum_id: 0\nmenuid: 20\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(24, 'sidebar', '����', '����', 1, 'mod_clock', 17, 1, 0, 0, '---\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(37, 'sidebar', '��������� ������', '��������� ������', 1, 'mod_pricecat', 14, 1, 0, 0, '---\nshowdesc: 0\nicon: /images/markers/pricelist.png\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(36, 'sidebar', '������� �����', '������', 1, 'mod_category', 13, 1, 0, 0, '---\nshowdesc: 0\ncategory_id: 6\nicon: /images/markers/folder.png\nmenuid: 21\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(39, 'sidebar', '����� �������', '����� �������', 1, 'mod_template', 12, 1, 0, 0, '---\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(47, 'mainbottom', '���������� � ��������', '���������� � ��������', 1, 'mod_uc_popular', 23, 1, 0, 0, '---\nnum: 10\ncat_id: 0\nmenuid: 23\nshowf: 2\nshowtype: thumb\nfulllink: 1\nsort: rating\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(49, 'sidebar', '��� ������?', '��� ������?', 1, 'mod_whoonline', 24, 1, 1, 0, '---\nshow_today: 1\nadmin_editor: 1\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(50, 'topmenu', '���� �� ������', '������� ������', 1, 'mod_forum', 31, 1, 1, 0, '---\nshownum: 2\nshowtype: web2\nshowforum: 0\nshowlink: 0\nshowtext: 0\nmenuid: 18\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(51, 'sidebar', '��������� ����', '��������� ����', 1, 'mod_user_image', 25, 1, 0, 0, '---\nshowtitle: 1\nmenuid: 15\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(52, 'sidebar', '������� ����', '������� ����', 0, '<p>{����=test.php}</p>', 11, 1, 0, 1, '---\n', 0, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(56, 'sidebar', '����� ������', '����� ��������', 1, 'mod_arhive', 27, 1, 0, 0, '---\nsource: both\ncat_id: 6\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(54, 'sidebar', '��������� � ��������', '��������� � ��������', 1, 'mod_uc_random', 26, 1, 0, 0, '---\ncat_id: 1\ncount: 2\nshowtitle: 1\nshowcat: 0\nmenuid: 23\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(60, 'maintop', '����� RSS ', '����� ��������', 1, 'mod_rss', 9, 1, 0, 0, '---\nshowdesc: 0\nshowicon: 1\nitemslimit: 6\nrssurl: http://portal.novator.ru/ngnews.rss\ncols: 2\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(61, 'sidebar', '��������� �����������', '��������� �����������', 1, 'mod_comments', 4, 1, 1, 0, '---\nshownum: 10\nminrate: 0\nshowdesc: 1\nshowrss: 1\nshowtarg: 0\ntargets: \n  faq: faq\n  catalog: catalog\n  boarditem: boarditem\n  blog: blog\n  article: article\n  palbum: palbum\n  photo: photo\n  userphoto: userphoto\n', 1, '', '', 0, 1, 'MINUTE', 'module.tpl', 0, '1.0'),
(62, 'maintop', '������� �����������', '������� �����������', 1, 'mod_latestphoto', 32, 1, 1, 0, '---\nshownum: 6\nmaxcols: 2\nshowclubs: 1\nshowalbum: 0\nshowdate: 0\nshowcom: 0\nalbum_id: 100\nshowtype: short\nshowmore: 0\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(63, 'sidebar', '���������� ����������', '���������� ����������', 1, 'mod_bestphoto', 10, 1, 0, 0, '---\nshownum: 2\nmaxcols: 2\nshowalbum: 0\nshowdate: 1\nshowcom: 1\nalbum_id: 0\nsort: rating\nmenuid: 20\nshowtype: full\nshowmore: 1\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(64, 'maintop', '����� � ������', '����� � ������', 1, 'mod_latestblogs', 2, 1, 1, 0, '---\nnamemode: blog\nshownum: 10\nminrate: 0\nshowrss: 1\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(65, 'sidebar', '���������� � ������', '���������� � ������', 1, 'mod_bestblogs', 3, 1, 1, 0, '---\nnamemode: blog\nshownum: 10\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(66, 'header', '���� ������������', '���� ������������', 1, 'mod_usermenu', 1, 0, 1, 0, '---\navatar: 1\nshowtype: text\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(67, 'mainbottom', '��������� ������� FAQ', '��������� ������� FAQ', 1, 'mod_latest_faq', 33, 1, 0, 0, '---\nnewscount: 5\nmaxlen: 140\ncat_id: \n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(69, 'maintop', '���������� ������', '���������� ������', 1, 'mod_bestcontent', 3, 1, 0, 0, '---\nshownum: 4\nmenuid: 21\nshowlink: 1\nshowdesc: 1\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(70, 'sidebar', '����� �������������', '����� �������������', 1, 'mod_usersearch', 4, 1, 0, 0, '---\ncat_id: \nsource: \nmenuid: 15\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(71, 'maintop', '����� ����������', '����� ����������', 1, 'mod_latestboard', 1, 1, 1, 0, '---\nshownum: 10\nshowcity: 1\ncat_id: -1\nsubs: 1\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(72, 'maintop', '������� �������������', '������� �������������', 1, 'mod_user_rating', 1, 1, 0, 0, '---\ncount: 20\nmenuid: 15\nview_type: rating\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(73, 'maintop', '����� �����', '����� �����', 1, 'mod_latestclubs', 3, 1, 0, 0, '---\ncount: 2\nmenuid: 38\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(74, 'maintop', '���������� �����', '���������� �����', 1, 'mod_bestclubs', 4, 1, 0, 0, '---\ncount: 2\nmenuid: 38\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(75, 'sidebar', '����� ������', '����� ������', 1, 'mod_respect', 1, 1, 1, 0, '---\nview: all\nshow_awards: 1\norder: desc\nlimit: 5\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(76, 'sidebar', '����� �������������', '����� �������������', 1, 'mod_userfiles', 1, 1, 0, 0, '---\nmenuid: 0\nsw_stats: 1\nsw_latest: 1\nsw_popular: 1\nnum_latest: 5\nnum_popular: 5\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 0, '1.0'),
(87, 'maintop', '����� ����������', '����� ����������', 1, 'mod_actions', 1, 1, 1, 0, '---\nlimit: 15\nshow_target: 0\naction_types: \n  16: 16\n  15: 15\n  20: 20\n  13: 13\n  29: 29\n  24: 24\n  23: 23\n  2: 2\n  27: 27\n  12: 12\n  10: 10\n  25: 25\n  17: 17\n  8: 8\n  18: 18\n  7: 7\n  26: 26\n  19: 19\n  22: 22\n  11: 11\n  21: 21\n  28: 28\n  9: 9\n  14: 14\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.7'),
(82, 'sidebar', '�����������', '������������� �������', 0, '<p>� ������� ���������� &laquo;������������� �������&raquo;, � ������� �� ������ ����������, ����� ������������ �������� ����� ������. �� ���������� �� �����������.</p>\r\n<p>������ ������� �������� ����� ����������� ����� �������������, ������� ����� �������� � ������ ����������. ������������ ����� ����������� ������ �������� �� ��������������� ����� ������� ����.&nbsp;</p>\r\n<p>����� ������������ ����� ��������� ����������� ������ � �� ������� ��������, ��� ������� ��� ��������� � ����������.</p>', 14, 1, 1, 1, '', 1, '', '', 0, 24, 'HOUR', 'module.tpl', 1, '1.0'),
(83, 'sidebar', '���������� �������������', '���������� �������������', 1, 'mod_user_stats', 1, 1, 1, 0, '---\nshow_total: 1\nshow_online: 1\nshow_gender: 1\nshow_city: 1\nshow_bday: 1\n', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0'),
(84, 'sidebar', '������ ������', '������ ������', 1, 'mod_user_friend', 5, 1, 0, 0, '---\r\nlimit: 5\r\nview_type: table', 1, '', '', 0, 1, 'HOUR', 'module_simple.tpl', 0, '1.0'),
(85, 'sidebar', '���������� �����', '���������� �����', 1, 'mod_invite', 1, 1, 0, 0, '', 1, '', '', 0, 1, 'HOUR', 'module.tpl', 1, '1.0');

DROP TABLE IF EXISTS `#__modules_bind`;
CREATE TABLE `#__modules_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `position` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__modules_bind` (`id`, `module_id`, `menu_id`, `position`) VALUES
(329, 42, 0, ''),
(249, 31, 0, ''),
(568, 17, 1, 'top'),
(347, 41, 18, ''),
(498, 39, 1, 'sidebar'),
(417, 54, 24, 'sidebar'),
(416, 54, 21, 'sidebar'),
(264, 32, 5, ''),
(328, 2, 0, ''),
(263, 32, 8, ''),
(262, 32, 1, ''),
(596, 66, 0, 'header'),
(346, 41, 24, ''),
(588, 20, 1, 'sidebar'),
(412, 37, 22, 'sidebar'),
(433, 36, 19, 'sidebar'),
(381, 24, 1, 'sidebar'),
(492, 26, 22, 'sidebar'),
(314, 38, 8, ''),
(556, 1, 0, 'topmenu'),
(497, 27, 1, 'sidebar'),
(332, 43, 0, ''),
(413, 45, 1, ''),
(478, 46, 1, 'mainbottom'),
(458, 44, 1, 'sidebar'),
(491, 47, 1, 'mainbottom'),
(350, 48, 37, ''),
(595, 50, 1, 'maintop'),
(434, 51, 1, 'sidebar'),
(358, 52, 1, 'sidebar'),
(359, 52, 42, 'sidebar'),
(360, 52, 41, 'sidebar'),
(361, 52, 43, 'sidebar'),
(364, 53, 1, ''),
(415, 54, 19, 'sidebar'),
(422, 60, 1, 'maintop'),
(401, 55, 0, ''),
(609, 56, 0, 'sidebar'),
(403, 58, 63, ''),
(406, 59, 0, ''),
(435, 23, 1, 'sidebar'),
(572, 61, 1, 'sidebar'),
(465, 63, 1, 'sidebar'),
(587, 64, 1, 'maintop'),
(482, 67, 1, 'mainbottom'),
(496, 68, 0, 'maintop'),
(486, 69, 1, 'maintop'),
(617, 22, 1, 'sidebar'),
(493, 26, 23, 'sidebar'),
(604, 70, 15, 'sidebar'),
(591, 71, 1, 'mainbottom'),
(515, 72, 1, 'maintop'),
(516, 73, 1, 'maintop'),
(520, 74, 1, 'maintop'),
(575, 25, 1, 'sidebar'),
(531, 76, 1, 'sidebar'),
(541, 77, 1, ''),
(598, 19, 21, 'sidebar'),
(597, 19, 1, 'maintop'),
(582, 65, 17, 'sidebar'),
(592, 79, 1, 'maintop'),
(586, 62, 20, 'sidebar'),
(599, 82, 23, 'sidebar'),
(608, 49, 1, 'sidebar'),
(607, 83, 15, 'sidebar'),
(616, 75, 1, 'sidebar'),
(615, 87, 1, 'maintop');

DROP TABLE IF EXISTS `#__ns_transactions`;
CREATE TABLE `#__ns_transactions` (
  `IDTransaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TableName` tinytext,
  `Differ` tinytext,
  `InTransaction` bit(1) DEFAULT NULL,
  `TStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`IDTransaction`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__online`;
CREATE TABLE `#__online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `sess_id` varchar(100) NOT NULL,
  `lastdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `agent` varchar(250) NOT NULL,
  `viewurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `sess_id` (`sess_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__photo_albums`;
CREATE TABLE `#__photo_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '1',
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSDiffer` varchar(15) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `published` int(11) NOT NULL,
  `showdate` int(11) NOT NULL DEFAULT '1',
  `iconurl` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `orderby` varchar(20) NOT NULL DEFAULT 'title',
  `orderto` varchar(4) NOT NULL DEFAULT 'asc',
  `public` int(11) NOT NULL,
  `perpage` int(11) NOT NULL DEFAULT '15',
  `cssprefix` varchar(20) NOT NULL,
  `thumb1` int(11) NOT NULL DEFAULT '96',
  `thumb2` int(11) NOT NULL DEFAULT '480',
  `thumbsqr` int(11) NOT NULL DEFAULT '1',
  `showtype` varchar(10) NOT NULL DEFAULT 'list',
  `nav` int(11) NOT NULL DEFAULT '1',
  `uplimit` int(11) NOT NULL,
  `maxcols` int(11) NOT NULL DEFAULT '4',
  `orderform` int(11) NOT NULL DEFAULT '1',
  `showtags` int(11) NOT NULL DEFAULT '1',
  `bbcode` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL DEFAULT '1',
  `is_comments` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__photo_albums` (`id`, `parent_id`, `ordering`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `title`, `description`, `published`, `showdate`, `iconurl`, `pubdate`, `orderby`, `orderto`, `public`, `perpage`, `cssprefix`, `thumb1`, `thumb2`, `thumbsqr`, `showtype`, `nav`, `uplimit`, `maxcols`, `orderform`, `showtags`, `bbcode`, `user_id`, `is_comments`) VALUES
(100, 0, 1, 1, 2, '', 0, 0, '-- �������� ������ --', '', 1, 1, '', '2008-05-30 12:03:07', 'title', 'asc', 0, 15, '', 96, 480, 1, 'list', 1, 0, 4, 1, 1, 1, 1, 0);

DROP TABLE IF EXISTS `#__photo_files`;
CREATE TABLE `#__photo_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `pubdate` datetime DEFAULT '0000-00-00 00:00:00',
  `file` varchar(200) NOT NULL,
  `published` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `showdate` int(11) NOT NULL DEFAULT '1',
  `comments` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL DEFAULT '1',
  `owner` varchar(10) DEFAULT 'photos',
  PRIMARY KEY (`id`),
  KEY `album_id` (`album_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__plugins`;
CREATE TABLE `#__plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(30) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `version` varchar(15) NOT NULL,
  `plugin_type` varchar(10) NOT NULL,
  `published` int(11) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__plugins` (`id`, `plugin`, `title`, `description`, `author`, `version`, `plugin_type`, `published`, `config`) VALUES
(6, 'p_usertab', 'Demo Profile Plugin', '������ ������� - ��������� ������� "������" � ������� ���� �������������', 'InstantCMS Team', '1.0', 'plugin', 0, '---\n���������� ������: 10\n'),
(3, 'p_fckeditor', 'FCKEditor', '���������� ��������', 'F. C. Knabben', '2.63', 'wysiwyg', 1, '---\n'),
(5, 'p_demo', 'Demo Plugin', '������ ������� - ��������� ����� � ����� ������ ������ �� �����', 'InstantCMS Team', '1.0', 'plugin', 0, '---\ntext: Added By Plugin From Parameter\ncolor: blue\ncounter: 1\n'),
(8, 'p_ping', '���� ��������� ������', '������� ������ � ���� ��� ���������� ������ � ������ � �����', 'InstantCMS Team', '1.0', 'plugin', 0, '---\nYandex HOST: ping.blogs.yandex.ru\nYandex PATH: /RPC2\nGoogle HOST: blogsearch.google.com\nGoogle PATH: /ping/RPC2\n'),
(15, 'p_morecontent', '������� ������', '��������� � ����� ������ ������ ������ ������� ������.', 'Maximov & InstantCMS Team', '1.0', 'plugin', 0, '---\nlimit: 5\nunsort: 1\n'),
(14, 'p_hidetext', '������� �����', '�������� ���������� ���� [hide] �� ��������������������', 'InstantCMS Team', '1.0', 'plugin', 1, '---\n');

DROP TABLE IF EXISTS `#__polls`;
CREATE TABLE `#__polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `pubdate` date NOT NULL DEFAULT '0000-00-00',
  `answers` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__polls_log`;
CREATE TABLE `#__polls_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__price_cats`;
CREATE TABLE `#__price_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__price_items`;
CREATE TABLE `#__price_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(230) NOT NULL,
  `price` float NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `canmany` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__ratings`;
CREATE TABLE `#__ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `target` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__ratings_total`;
CREATE TABLE `#__ratings_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(32) NOT NULL,
  `item_id` mediumint(9) NOT NULL,
  `total_rating` int(11) NOT NULL,
  `total_votes` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `target` (`target`,`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__rating_targets`;
CREATE TABLE `#__rating_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(32) NOT NULL,
  `component` varchar(32) NOT NULL,
  `is_user_affect` tinyint(4) NOT NULL,
  `user_weight` smallint(6) NOT NULL,
  `target_table` varchar(32) NOT NULL,
  `target_title` varchar(70) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target` (`target`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__rating_targets` (`id`, `target`, `component`, `is_user_affect`, `user_weight`, `target_table`, `target_title`) VALUES
(1, 'content', 'content', 1, 5, 'cms_content', '������'),
(2, 'photo', 'photos', 1, 5, 'cms_photo_files', '���� � �������'),
(3, 'blogpost', 'blogs', 1, 5, 'cms_blog_posts', '���� � �����'),
(4, 'comment', 'comments', 1, 2, 'cms_comments', '�����������');

DROP TABLE IF EXISTS `#__search`;
CREATE TABLE `#__search` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `title` varchar(250) NOT NULL,
  `link` varchar(200) NOT NULL,
  `place` varchar(100) NOT NULL,
  `placelink` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__stats`;
CREATE TABLE `#__stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) NOT NULL,
  `logdate` datetime NOT NULL,
  `page` varchar(200) NOT NULL,
  `agent` varchar(60) DEFAULT 'unknown',
  `refer` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__subscribe`;
CREATE TABLE `#__subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `target` varchar(20) NOT NULL,
  `target_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__tags`;
CREATE TABLE `#__tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(250) NOT NULL,
  `target` varchar(25) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag` (`tag`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__uc_cart`;
CREATE TABLE `#__uc_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `itemscount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__uc_cats`;
CREATE TABLE `#__uc_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `fieldsstruct` text,
  `view_type` varchar(20) NOT NULL DEFAULT 'list',
  `fields_show` int(11) NOT NULL DEFAULT '10',
  `showmore` int(11) NOT NULL DEFAULT '1',
  `perpage` int(11) NOT NULL DEFAULT '20',
  `showtags` int(11) NOT NULL DEFAULT '1',
  `showsort` int(11) NOT NULL DEFAULT '1',
  `is_ratings` int(11) NOT NULL,
  `orderby` varchar(12) NOT NULL DEFAULT 'pubdate',
  `orderto` varchar(10) DEFAULT 'desc',
  `showabc` int(11) NOT NULL DEFAULT '1',
  `shownew` int(11) NOT NULL,
  `newint` varchar(100) NOT NULL,
  `filters` int(11) NOT NULL,
  `is_shop` int(11) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `NSDiffer` int(11) NOT NULL,
  `NSIgnore` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `is_public` int(11) NOT NULL,
  `can_edit` int(11) NOT NULL DEFAULT '0',
  `cost` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__uc_cats` (`id`, `parent_id`, `title`, `description`, `published`, `fieldsstruct`, `view_type`, `fields_show`, `showmore`, `perpage`, `showtags`, `showsort`, `is_ratings`, `orderby`, `orderto`, `showabc`, `shownew`, `newint`, `filters`, `is_shop`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `is_public`, `can_edit`, `cost`) VALUES
(1000, 0, '-- �������� ������� --', '', 1, '', 'list', 10, 1, 20, 1, 1, 0, 'pubdate', 'desc', 1, 0, '', 0, 0, 1, 2, 0, 0, 0, 1, 0, 0, '');

DROP TABLE IF EXISTS `#__uc_cats_access`;
CREATE TABLE `#__uc_cats_access` (
  `cat_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  KEY `cat_id` (`cat_id`,`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__uc_discount`;
CREATE TABLE `#__uc_discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `sign` tinyint(4) NOT NULL,
  `value` float NOT NULL,
  `unit` varchar(10) NOT NULL,
  `if_limit` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__uc_items`;
CREATE TABLE `#__uc_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `imageurl` varchar(200) NOT NULL,
  `fieldsdata` text NOT NULL,
  `hits` int(11) NOT NULL,
  `is_comments` int(11) NOT NULL,
  `tags` varchar(200) NOT NULL,
  `rating` float NOT NULL,
  `meta_desc` varchar(250) NOT NULL,
  `meta_keys` varchar(250) NOT NULL,
  `price` float NOT NULL,
  `canmany` int(11) NOT NULL DEFAULT '1',
  `user_id` int(11) NOT NULL,
  `on_moderate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `title` (`title`,`fieldsdata`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__uc_ratings`;
CREATE TABLE `#__uc_ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__uc_tags`;
CREATE TABLE `#__uc_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(200) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__upload_images`;
CREATE TABLE `#__upload_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `fileurl` varchar(250) NOT NULL,
  `target` varchar(25) NOT NULL DEFAULT 'forum',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__users`;
CREATE TABLE `#__users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL DEFAULT '1',
  `login` varchar(100) NOT NULL,
  `nickname` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `icq` varchar(15) NOT NULL,
  `regdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `logdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `birthdate` date NOT NULL DEFAULT '0000-00-00',
  `is_locked` int(11) NOT NULL,
  `is_deleted` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `last_ip` varchar(15) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_date` datetime NOT NULL,
  `invited_by` int(11) DEFAULT NULL,
  `invdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ROW_FORMAT=DYNAMIC;

INSERT INTO `#__users` (`id`, `group_id`, `login`, `nickname`, `password`, `email`, `icq`, `regdate`, `logdate`, `birthdate`, `is_locked`, `is_deleted`, `rating`, `points`, `last_ip`, `status`, `status_date`, `invited_by`, `invdate`) VALUES
(1, 2, 'admin', '�������������', '2ca41752ccf4dbdb76d8fe88c488fd44', 'admin@cms.ru', '100200300', '2007-11-23 12:41:57', '2010-12-06 21:16:56', '1980-10-23', 0, 0, 32, 0, '127.0.0.1', '����� ������� ������ �� ���� ��� ���������� � ���� ���������� ����', '2010-10-21 02:06:53', 0, '2010-11-09 23:25:59');

DROP TABLE IF EXISTS `#__users_activate`;
CREATE TABLE `#__users_activate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pubdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_albums`;
CREATE TABLE `#__user_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `pubdate` datetime NOT NULL,
  `allow_who` varchar(10) NOT NULL,
  `description` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `allow_who` (`allow_who`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_autoawards`;
CREATE TABLE `#__user_autoawards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `imageurl` varchar(200) NOT NULL,
  `p_comment` int(11) NOT NULL,
  `p_blog` int(11) NOT NULL,
  `p_forum` int(11) NOT NULL,
  `p_photo` int(11) NOT NULL,
  `p_privphoto` int(11) DEFAULT NULL,
  `p_content` int(11) NOT NULL,
  `p_karma` int(11) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_awards`;
CREATE TABLE `#__user_awards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `imageurl` varchar(100) NOT NULL,
  `from_id` int(11) NOT NULL,
  `award_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `#__user_clubs`;
CREATE TABLE `#__user_clubs` (
  `user_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'guest',
  `pubdate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_files`;
CREATE TABLE `#__user_files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `filename` varchar(250) NOT NULL,
  `pubdate` datetime NOT NULL,
  `allow_who` varchar(50) NOT NULL,
  `filesize` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_friends`;
CREATE TABLE `#__user_friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `logdate` datetime NOT NULL,
  `is_accepted` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `to_id` (`to_id`),
  KEY `from_id` (`from_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_groups`;
CREATE TABLE `#__user_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `is_admin` int(11) NOT NULL,
  `access` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_groups` (`id`, `title`, `alias`, `is_admin`, `access`) VALUES
(1, '������������', 'registered', 0, 'comments/add, comments/bbcode, comments/delete, content/add, board/autoadd'),
(2, '��������������', 'admin', 1, 'admin/content, admin/com_rssfeed, admin/com_arhive, admin/com_banners, admin/com_blog, admin/com_faq, admin/com_board, admin/com_content, admin/com_clubs, admin/com_comments, admin/com_forms, admin/com_photos'),
(8, '�����', 'guest', 0, 'comments/add'),
(7, '���������', 'editors', 0, 'comments/add, comments/delete, content/add, content/autoadd, content/delete'),
(9, '����������', 'moderators', 0, 'comments/add, comments/delete, comments/moderate, forum/moderate, content/add');

DROP TABLE IF EXISTS `#__user_invites`;
CREATE TABLE `#__user_invites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `createdate` datetime NOT NULL,
  `is_used` tinyint(4) NOT NULL DEFAULT '0',
  `is_sended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `code` (`code`,`owner_id`,`is_used`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_karma`;
CREATE TABLE `#__user_karma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `points` smallint(6) NOT NULL,
  `senddate` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_msg`;
CREATE TABLE `#__user_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `senddate` datetime NOT NULL,
  `is_new` int(11) NOT NULL DEFAULT '1',
  `message` text NOT NULL,
  `to_del` tinyint(4) NOT NULL DEFAULT '0',
  `from_del` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `to_id` (`to_id`),
  KEY `from_id` (`from_id`),
  KEY `to_del` (`to_del`),
  KEY `from_del` (`from_del`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_photos`;
CREATE TABLE `#__user_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `allow_who` varchar(15) NOT NULL DEFAULT 'all',
  `hits` int(11) NOT NULL,
  `imageurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `album_id` (`album_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `#__user_profiles`;
CREATE TABLE `#__user_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `city` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `showmail` int(11) NOT NULL,
  `showbirth` int(11) NOT NULL DEFAULT '1',
  `showicq` int(11) NOT NULL DEFAULT '1',
  `karma` int(11) NOT NULL,
  `imageurl` varchar(250) NOT NULL,
  `allow_who` varchar(35) NOT NULL DEFAULT 'all',
  `signature` varchar(240) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `formsdata` text NOT NULL,
  `email_newmsg` int(11) NOT NULL DEFAULT '1',
  `cm_subscribe` varchar(4) NOT NULL,
  `stats` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `city` (`city`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ROW_FORMAT=DYNAMIC;

INSERT INTO `#__user_profiles` (`id`, `user_id`, `city`, `description`, `showmail`, `showbirth`, `showicq`, `karma`, `imageurl`, `allow_who`, `signature`, `gender`, `formsdata`, `email_newmsg`, `cm_subscribe`, `stats`) VALUES
(1, 1, '������', '�.��������, �.������, �.�����', 1, 1, 1, 0, '', 'all', '', 'm', '---\n22: �.��������, �.������, �.�����\n24: ������\n', 1, 'none', '---\ncount: \n  comments: 1\n  forum: 1\n  photos: 2\n  board: 2\n  files_public: 0\n  files_private: 0\nrating: 0\n');

DROP TABLE IF EXISTS `#__user_wall`;
CREATE TABLE `#__user_wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `content` text NOT NULL,
  `usertype` varchar(8) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
