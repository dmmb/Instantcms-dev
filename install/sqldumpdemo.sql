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
  KEY `seolink` (`seolink`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__blogs` (`id`, `user_id`, `title`, `pubdate`, `allow_who`, `view_type`, `showcats`, `ownertype`, `premod`, `forall`, `owner`, `seolink`, `rating`) VALUES
(1, 1, '������ �����', '2008-06-03 13:26:55', 'all', 'list', 1, 'single', 0, 1, 'user', 'primer-bloga', 1),
(21, 13, '����', '2009-08-29 14:04:46', 'all', 'list', 1, 'multi', 0, 0, 'club', 'phpclub', 0);

DROP TABLE IF EXISTS `#__blog_authors`;
CREATE TABLE `#__blog_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `startdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `#__blog_cats`;
CREATE TABLE `#__blog_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

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
  `allow_who` varchar(20) NOT NULL,
  `edit_times` int(11) NOT NULL,
  `edit_date` datetime NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `seolink` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `seolink` (`seolink`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__blog_posts` (`id`, `user_id`, `cat_id`, `blog_id`, `pubdate`, `title`, `feel`, `music`, `content`, `allow_who`, `edit_times`, `edit_date`, `published`, `seolink`) VALUES
(5, 1, 5, 1, '2009-09-07 11:50:16', '������ ������ � �����', '', '', '[b]����� InstantCMS - ��� ������ � �������� ���������� ��� ����� ����������:[/b]\r\n\r\n- �������� BB-���� (������� �����, ������, ������, ��������);\r\n- ������� ����������� "�� ����";\r\n- �������������� ����� ������;\r\n- ����������� � �������;\r\n- ����������� ������� ������������ ������;\r\n- ������ � ����;', '0', 3, '2009-09-07 11:50:16', 1, 'primer-zapisi-v-bloge'),
(38, 1, 5, 1, '2009-09-07 11:50:50', '������� �����: ����������� � �����������', '', '', '��������� ����������. ��������� ������������ ����� ��������� � �������-��������� ������� ����, ��� ��� ���������� ���������� ��������� �� ������������� � ���� � ������ ���������� ������������ ����������� ������������. �������������������� ����������. ��������� ���� ��������� � ���������, ��� �� ����������� �������� ��������������� ��������� ����������. �������� ������ ���������� � ���� ����, � ���� ����� ���������� � �������� ������� �� ���������� ����� 250 �. ��������� ���� ����� � ���� �����, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������.', '0', 1, '2009-09-07 11:50:50', 1, 'melovoi-trias-metodologija-i-osobennosti'),
(39, 1, 5, 1, '2009-09-07 11:50:36', '�������������� ���������: ����������� � �����������', '', '', '���������� ��������� �����, �� �������� � ����������� ���������� �����. ��� �� �����, ����� ��������� � �� ��������������, ��� ������������� ������������� �������� �������. ��� ��������� �������� ������ �������� ����������� ��������, ��������� ���������� �������� �������� �������� ��������������� � ��������������� �������������. ���� ������� �� �������� �������� ��� ���������, ����������� �������� ��������� ���������� ������, ��� ������������ �������� �������� ������������ ��������. ����������� ����, ������, �� 100 ���. ���, ������������. ���� ������� �� �������� �������� ��� ���������, ������� ��������� ������������ ������� ������, ��� �� ����������� �������� ��������������� ��������� ����������.\r\n\r\n������� �������� ������� ����� �������� �������������� ������, ��� ���� ������������ ��, ��� �������� ������ ������������� �� �������. ����� ��������. ������ ��������� �������� ����������� �����, ��� ��������� ���������� ��������������� ������������� �������. ��� ������������ ����������� ����������� ����������� � ��������� ���� ��������������� �������� ����������� �������� �������� ��������� ���������, ��� ��� ���������� ���������� ��������� �� ������������� � ���� � ������ ���������� ������������ ����������� ������������. ������ ������ ��������, ��� ��������� �������� ���������, � ������������ � ����������� � ��������� �������������.', '0', 2, '2009-09-07 11:50:36', 1, 'ultraosnovnoi-kimberlit-metodologija-i-osobennosti');

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
(1, 0, 1, 1, 8, '', 0, 0, '-- �������� ������� --', '', 1, 1, 1, '2008-09-22 13:39:32', 'title', 'asc', 0, 15, 1, 64, 400, 0, 10, 1, 'folder_grey.png', ''),
(10, 1, 21, 6, 7, '', 0, 1, '������', '', 1, 1, 1, '2008-09-22 14:30:29', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', '���������\r\n���������'),
(9, 1, 20, 4, 5, '', 0, 1, '����������', '', 1, 1, 1, '2008-09-22 14:30:23', 'pubdate', 'desc', -1, 20, 2, 64, 400, 0, 10, 1, 'folder_grey.png', '�����\r\n������\r\n�������'),
(8, 1, 19, 2, 3, '', 0, 1, '������������', '', 1, 1, 1, '2008-09-22 14:30:00', 'pubdate', 'desc', -1, 20, 1, 64, 400, 0, 10, 1, 'folder_grey.png', '������\r\n�����\r\n�������\r\n����\r\n�����');

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
  PRIMARY KEY (`id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__board_items` (`id`, `category_id`, `user_id`, `obtype`, `title`, `content`, `city`, `pubdate`, `pubdays`, `published`, `file`, `hits`) VALUES
(4, 10, 1, '���������', '��������� ���� ������', '���� �������� ����� � ���� ���.', '������', '2009-10-26 14:11:18', 10, 1, '550de8a5de9b5301133a815de31be00d.jpg', 0),
(5, 9, 1, '�������', '������� ���-2107 �� Nissan Skyline GTR', '���������� ����� � ��� �������.', '������', '2009-10-26 14:14:24', 10, 1, '931f90c50adcea1ff18177bc22d4ceac.jpg', 2);

DROP TABLE IF EXISTS `#__cache`;
CREATE TABLE `#__cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(10) NOT NULL,
  `target_id` varchar(255) NOT NULL,
  `cachedate` datetime NOT NULL,
  `cachefile` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `seolink` (`seolink`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__category` (`id`, `parent_id`, `title`, `description`, `published`, `showdate`, `showcomm`, `orderby`, `orderto`, `modgrp_id`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `maxcols`, `showtags`, `showrss`, `showdesc`, `is_public`, `photoalbum`, `seolink`, `url`) VALUES
(1, 0, '--�������� ������--', '�������� ������ �����', 1, 1, 1, 'pubdate', 'asc', 0, 1, 14, 0, '', 0, 1, 1, 1, 1, 0, 0, '', '--kornevoi-razdel--', ''),
(2, 1, '�������', '', 1, 1, 1, 'pubdate', 'ASC', 0, 2, 3, 1, '', 0, 1, 1, 1, 0, 0, 0, '', 'novosti', ''),
(6, 1, '������', '<p>������ ������ ������������� ������� <a href="http://referats.yandex.ru/">������.��������</a></p>', 1, 1, 1, 'pubdate', 'ASC', 0, 4, 13, 1, '', 0, 2, 1, 1, 1, 1, 1, 'a:7:{s:2:"id";i:0;s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:4:"hits";s:7:"orderto";s:3:"asc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati', ''),
(13, 6, '���������', '<p>������ �� ����������</p>', 1, 1, 1, 'pubdate', 'DESC', 0, 11, 12, 2, '', 0, 4, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";i:0;s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati/marketing', ''),
(12, 6, '��������', '������ �� ��������', 1, 1, 1, 'pubdate', 'DESC', 0, 7, 8, 2, '', 0, 2, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";s:1:"0";s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";s:1:"2";s:3:"max";s:1:"8";}', 'stati/geologija', ''),
(11, 6, '����������', '������ �� ����������', 1, 1, 1, 'pubdate', 'DESC', 0, 5, 6, 2, '', 0, 1, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";s:1:"0";s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";s:1:"2";s:3:"max";s:1:"8";}', 'stati/astronomija', ''),
(14, 6, '����������', '������ �� ����������', 1, 1, 1, 'pubdate', 'DESC', 0, 9, 10, 2, '', 0, 3, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";s:1:"0";s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";s:1:"2";s:3:"max";s:1:"8";}', 'stati/psihologija', '');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__clubs` (`id`, `admin_id`, `title`, `description`, `imageurl`, `pubdate`, `clubtype`, `published`, `maxsize`, `enabled_blogs`, `enabled_photos`, `rating`, `photo_premod`, `blog_premod`, `blog_min_karma`, `photo_min_karma`, `album_min_karma`, `join_min_karma`, `join_karma_limit`) VALUES
(13, 21, 'PHPClub', '', '', '2009-08-29 14:04:46', 'public', 1, 0, 1, 1, 0, 0, 0, 0, 0, 25, 0, 0);

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
  `pubdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` int(11) NOT NULL DEFAULT '1',
  `is_new` int(11) NOT NULL DEFAULT '1',
  `target_title` varchar(150) NOT NULL,
  `target_link` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__comments` (`id`, `parent_id`, `pid`, `user_id`, `target`, `target_id`, `guestname`, `content`, `pubdate`, `published`, `is_new`, `target_title`, `target_link`) VALUES
(1, 0, 0, 1, 'article', 30, '', '�������� ������! ������� ������� ������!', '2009-05-20 17:42:14', 1, 1, '�������������� ������� ��������', '/content/stati/astronomija/pervonachalnyi-nulevoi-meridian.html'),
(2, 1, 0, 21, 'article', 30, '', '�������� � ����, �������!', '2009-05-20 17:42:40', 1, 1, '�������������� ������� ��������', '/content/stati/astronomija/pervonachalnyi-nulevoi-meridian.html');

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
(1, '������� ������', 'content', '---\nshowtitle: 0\nreaddesc: 0\nrating: 1\nperpage: 15\npt_show: 0\npt_disp: 0\npt_hide: 0\nautokeys: 1\nimg_small_w: 100\nimg_big_w: 200\nimg_sqr: 1\naf_on: 0\naf_delete: 1\naf_showlink: 1\naf_forum_id: 1\naf_hidecat_id: 2\n', 0, 'InstantCMS team', 1, '1.5', 1),
(2, '�����������', 'registration', '---\nis_on: 1\nact: 0\nsend: \noffmsg: >\n  ����������� �������������� ��\n  ����������� ��������.\nhimsg: \nfirst_auth_redirect: profile\nauth_redirect: profile\nname_mode: nickname\nask_icq: 1\nask_birthdate: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(3, '�����������', 'photos', '---\nlink: 1\nsaveorig: 1\nmaxcols: 2\nshowlat: 1\nwatermark: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(4, '�����-����', 'price', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(5, '�����', 'search', '---\nperpage: 10\ncomp: \n  - content\n  - photos\n  - price\n  - catalog\n  - forum\n  - blog\n', 0, 'InstantCMS team', 1, '1.5', 1),
(6, '����������� ����', 'forms', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(7, '����������� �������������', 'comments', '---\nemail: comments@cms.ru\ncanguests: 1\nregcap: 0\npublish: 1\nsmiles: 1\nbbcode: 1\nselfdel: 0\nsubscribe: 1\nanchors: 0\nrecode: 0\nmin_karma: 0\nmin_karma_show: 0\nmin_karma_add: 0\n', 1, 'InstantCMS team', 1, '1.5', 1),
(8, '����� ����������', 'arhive', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(9, '������������� �������', 'catalog', '---\nemail: shop@site.ru\ndelivery: |\n  �������� � ��������.\n  ���� ����� ����� �������� � ���������� ���������� &quot;������������� �������&quot;.\nnotice: 1\npremod: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(10, '������� �������������', 'users', '---\nshowgroup: 1\nsw_stats: \nsw_comm: 1\nsw_search: 1\nsw_forum: 1\nsw_photo: 1\nsw_wall: 1\nsw_friends: 1\nsw_blogs: 1\nsw_clubs: 1\nsw_feed: 1\nsw_content: 1\nsw_awards: 1\nsw_board: 1\nsw_msg: 1\nkarmatime: 3\nkarmaint: HOUR\nphotosize: 20\nwatermark: 0\nsmallw: 64\nmedw: 200\nmedh: 500\nsw_files: 1\nfilessize: 100\nprivforms: \n  - 3\n', 0, 'InstantCMS team', 1, '1.5', 1),
(12, '�����', 'forum', '---\nis_on: 1\nkarma: \npp_thread: 15\npp_forum: 15\nshowimg: 1\nimg_on: 1\nimg_max: 5\nfast_on: 1\nfast_bb: 1\nfa_on: 1\nfa_allow: -1\nfa_max: 25\nfa_ext: txt doc zip rar arj png gif jpg jpeg bmp\nfa_size: 1024\n', 0, 'InstantCMS team', 1, '1.5', 1),
(13, '���������� �����', 'statistics', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(15, '�����', 'blogs', '---\nperpage: 10\nmin_karma_private: 0\nmin_karma_public: 0\nmin_karma: 1\nrss_all: 1\nrss_one: 1\n', 0, 'InstantCMS team', 1, '1.5', 1),
(16, '������� � ������', 'faq', '---\n', 0, 'InstantCMS team', 1, '1.5', 1),
(17, '�������', 'banners', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(19, 'RSS ���������', 'rssfeed', '---\naddsite: 1\nmaxitems: 50\nicon_on: 1\nicon_url: http://cmssite/images/rss.png\nicon_title: InstantCMS\nicon_link: http://cmssite/\n', 1, 'InstantCMS team', 1, '1.5', 1),
(21, '����������� �������������', 'autoawards', '---\n', 1, 'InstantCMS team', 1, '1.5', 1),
(22, '����� ����������', 'board', '---\nmaxcols: 1\nobtypes: |\n  ������\n  �����\n  �������\n  ������\nshowlat: \npublic: 2\nphotos: 1\nsrok: 1\npubdays: 10\nwatermark: 0\n', 0, 'InstantCMS team', 1, '1.5', 1),
(23, '����� �������������', 'clubs', '---\nenabled_blogs: 1\nenabled_photos: 1\nthumb1: 48\nthumb2: 200\nthumbsqr: 1\ncancreate: 1\nperpage: 10\ncreate_min_karma: 0\ncreate_min_rating: 0\n', 0, 'InstantCMS team', 1, '1.5', 1);

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `seolink` (`seolink`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__content` (`id`, `category_id`, `user_id`, `pubdate`, `enddate`, `is_end`, `title`, `description`, `content`, `published`, `hits`, `meta_desc`, `meta_keys`, `showtitle`, `showdate`, `showlatest`, `showpath`, `ordering`, `comments`, `is_arhive`, `seolink`, `canrate`, `pagetitle`, `url`) VALUES
(20, 2, 1, '2009-03-01 15:56:00', '2009-05-22', 0, '��� ���� ������!', '<p>�� ���� �������������� ��� �� ����� �����!</p>', '<p>��� ���� ������ � �������� ������� ��������������.</p>\r\n<p>� ��� ������� ����� �� �������. �� ���� ����� �� ����������� ���������� ���������� ����������.</p>\r\n<p>�� ����� ���� ��� �� ������� ��� �������.</p>\r\n<p>���� �������� ���������� �������� � ����� ������� ������������ ����������.</p>', 1, 385, '', '', 1, 1, 1, 1, 1, 1, 0, 'novosti/nash-sait-otkryt', 1, '', ''),
(26, 2, 1, '2009-03-01 15:56:00', '2009-05-22', 0, '�������� ������� �����', '<p>��� �������� �������. ������������ ����� �� ��������������.</p>', '<p>������� ����� ������������� ��������� ����������, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. ������, �������� ���������� ������������� ����, ����������� ����������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ����� ������������ ��������� ����, � ������������ � ����������� � ��������� �������������. ����� �������� ������, �� ���� ���� ������������� �������� ���� ��� ������� ��������. ���������� ����� �������� ����, ��� � ����� ������ �������� � ������� ���������� ������ ��� ��������� ������������ ����. ��� ��������� �������� ����� ��������.</p>', 1, 276, '', '', 1, 1, 1, 1, 3, 1, 0, 'novosti/testovaja-novost-saita', 1, '', ''),
(30, 11, 1, '0000-00-00 00:00:00', '2009-06-17', 0, '�������������� ������� ��������', '<p><strong>��� ��������� ��������� ����� ������ ��������</strong>, ��� ���� ����������� ��� ������ ����� &ndash; � ����� �������� ������ ����� ������������ � ���������, ��� �� ��� ������ ������� �����������. ��� ���� �������� ����, ����������� ����p�� ���� p������������ ���������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����. �p������������ ������ ����������� ����������� ������� ���� (��������� ��������� �� ���������, ����, �����).</p>', '<p>����� ���� ���� � ����������, ������� �����-��������� �������� �������� ���������, ���� ��������� � ��������� ������� ����� ������� ����������. ���� �p������� �� ���p���, �� �������, ��� ����������� ��� ����� ������� �������������� ��������, ���� ���� �������� �� �������� ������ ����� ������ ������, ������� � ������ ���������� �������������.<br />\r\n<br />\r\n������� �� ��������� ���, ����� ���� ������ �������� � ������ ������� ���� ����� � ������ (��� ����� ����� � �������� ������� ������), ����� ��������� ����������, ���� ��������� � ��������� ������� ����� ������� ����������. � ������� �� �������� � ������� �������, ��������� ���� ���������. ������ ����������� �� ��������� ���, ����� ���� ������ �������� � ������ ������� ���� ����� � ������ (��� ����� ����� � �������� ������� ������), ������������. ������ ����������� ����������� ����������� �����, ��� �� �����, ��� ����� ������� � ������ ����� 82-� ������� ������. ��������� �������, ������ ���������� ������ ������ ������, �������� ������� p������������ ���������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����.<br />\r\n<br />\r\n���p���� ������ � ��p������ ������������ ��������, � ������� �������������� ����������� ������ ��������� ������� ��������� �������: M��.= 2,5lg D�� + 2,5lg ����� + 4. ���������� ��������������� �����������. ��� ����� �������� ��������� �������: V = 29.8 * sqrt(2/r &ndash; 1/a) ��/���, ��� ������ ����� ��������� ������������ �������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����. ������ ������. ��������� ���� ����������.</p>', 1, 65, '', '', 1, 1, 1, 1, 1, 1, 0, 'stati/astronomija/pervonachalnyi-nulevoi-meridian', 1, '�������������� ������� ��������', ''),
(31, 12, 1, '2009-05-20 16:24:00', '2009-05-20', 0, '���������� ��������: �������� �������', '<p>������� ���������������, � ������ ������������ ��������, ��������� ��������������, � �� ����� ��� �������� ���������� ���������� � ������� ��������. ����� ���������� ���������� ���������� �����, � ������������ � ����������� � ��������� �������������. ��������� ���������� ��������� �����������, ������� �� ������� � �������-���������� ��������� ������������ ������� ��������. ������� ����� �������������, ����������� ������ ����������������� ������ ���������� �����, ���������� � ���� ������������, ��� � ����� ��������������� � ������������ ������������� ��������� � ��� �����.</p>', '<p>���������� ����� ��������� ���������� ����, ��������� ��������������� ��������� ����� �� �����������. ����������� �������� ����������. ����������� ����������� ���������� ������������� ����, ��� ��������������� � ������������� ����������� ����� � ������� ����. ����� ����������� ������������� ������������-�������������� ������, ��� � ����� ������ �������� � ������� ���������� ������ ��� ��������� ������������ ����. ������ ��������� ��������������� ������-�����, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����.</p>\r\n<p>��������� ����������� ��������� ������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ������������� �������� �������, ��������� ���������� �������� �������� �������� ��������������� � ��������������� �������������. ����, ��� ��, ��� � � ������ ��������, �������������. ������� �������� ���������������� ������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ���� ����������� ���������� ������-����������� ��������, ��������� ���������� �������� �������� �������� ��������������� � ��������������� �������������. � �������������� ����� ��� ���������� ������������ ������������ ��������� ����, ������� �� ������� � �������-���������� ��������� ������������ ������� ��������.</p>', 1, 56, '���������� ��������: �������� �������', '���������������, ���������, �����, ������������, �������, ����������, ������, ��������, ���������������, ���������������, �������������, ��������, ����������, ������, ����������, �����������, ���������, ��������, ���������� ������������, ������� �����, ��������� �������, ��������������� ���������, ������������ ���������������, �������� ���������������, ��������������� �������������, �������� ��������, ��������� ����������, ���������� ��������, �������� ��������, ���������� ������������ ���������������, ��������������� ��������� �������, ������������ ��������������� ���������, �������� �������� ���������������, �������� �������� ��������, ��������� ���������� ��������, ���������� �������� ��������', 1, 1, 1, 1, 1, 1, 0, 'stati/geologija/ostancovyi-lakkolit-osnovnye-momenty', 1, '', ''),
(32, 12, 1, '2009-05-20 16:24:00', '2009-06-17', 0, '������ ������� �����?!!!', '<p>��������������� ����� �������� ����� �����������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ������������� �������������� ������������ ������� ������, ��� � ����� ��������������� � ������������ ������������� ��������� � ��� �����. ����������� �����������, ������� � ��������� ����� ��������� ���� ������ ����, ��������� �������� ����, � �� ����� ��� �������� ���������� ���������� � ������� ��������. ������� ������� ��������, ����� ���� �������������� ������ ������������� ��������� ��������� ���������-������������� ������������� ��������� �������.</p>', '<p>���������� ��������� ����������� ��, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. �������� �������������� ��������� ����, ������� ��������� �����, ��� � ����� ������ �������� � ������� ���������� ������ ��� ��������� ������������ ����. ������������� ������ �������� ������ ��������, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. �������� ������ ����������� ������-��������� ����� ������, � �� ����� ��� �������� ���������� ���������� � ������� ��������. ��������������� ����� ���������� ��������� ���������� ����, � ������������ � ����������� � ��������� �������������. ���������� �����, �� ���� ������� ��� �������� ��������� ���������, ����������� ������ ����������, ������� � ����� �������, ��������� � ��.<br />\r\n<br />\r\n�� ��������� ������� ���������� ����������������� ��������� ������� ��������, ��� ����������� �� ����������-������������� �����������, ������������������ ��������� � ��������-���������������� �������� �����. ���������� �����, �� ���� ������� ��� �������� ��������� ���������, ��������� ���������� ����������������� �����, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. ������� ������� �������� ����������, �������� �� �� ����� ������������ ������� � ��������� ��������� ������, ���������� ������, ��� ��� ���������� ���������� ��������� �� ������������� � ���� � ������ ���������� ������������ ����������� ������������. ������� ���������� ������ �������� ����������, � � �� �� ����� ��������������� ���������� ����������� ��� ������� ���� �������� ������.</p>', 1, 9, '', '', 1, 1, 1, 1, 1, 1, 0, 'stati/geologija/pochemu-nerezko-plato', 1, '������ ������� �����?!!!', ''),
(33, 13, 1, '2009-05-20 16:24:00', '2009-05-20', 0, '���������� �������� �����: ����������� � �����������', '<p>������������� ��������� ������������� �������������� conversion rate, �� �������� � ���������. ��������� ������ �������� PR, �� �������� � ���������. VIP-�����������, ���������� ���������� ��������� ��������, ��������� �������� PR-������, �� �������� � ���������. ��� �������� ����� ������, �������������� ���������� ������������ �������������� �������������� �����, �� �������� � ���������. ����, ����, ��� ��������� ���� ��������� ������� �� �����.</p>', '<p>����� ��������, ��� ����������� ������������� ����������� ������ ��������, ��������� �� ������������ ����������. ����-�����, ���������� �����������, ��������� �������������� ���-���, ����������� �������. � ��� �� ������ ���������� ����������������� �������� ������������ ��������� �����, �������� �� ���� �������� ������. ����-�����, ����������, ��� ��� ��������� ��� ������. ����� ��������� �������� �������� � �� ����, ��� ���� ����� �������� ����� �������.</p>\r\n<p>� ���� �� �������������� ������������� �������� ����������� ��������������� ������������� ������, �������� �� �������� �����������. ������ �������� ��� ����������� ������� ������������ ����������� ���������� � ������, ������� ��������� ��� ����� ������������. ��������� � ��������,&nbsp;�������������, ������������� ���������� ����������, �������� �� ���� �������� ������. ��������� ������, ����������� ��������, ����������� ������������� ������� ������, ��������� ���� ���������� ��������. �������� ���������� �������� ��������������� �����, ����������� �������.</p>', 1, 8, '���������� �������� �����: ����������� � �����������', '��������, ������, ���������������, ����������, ��������, �������, ��������, �����, �����������, ������, �������� ������, ����������� �������', 1, 1, 1, 1, 1, 1, 0, 'stati/marketing/kulturnyi-mediinyi-kanal-metodologija-i-osobennosti', 1, '', ''),
(34, 13, 1, '2009-05-20 11:33:00', '2009-07-22', 0, '������������ ������', '<p>���� � ���, ��� ����������� �������������� ����������������� ��������� �����, ��������� �� ������������ ����������. ������� ��������� �������������. ��� �� �����, ������ ����� ����� ������������ ������������ ������ ����������� �����, �������� ���� �����.</p>', '<p>��������� ��������� �������������� ������������,&nbsp;�&nbsp;������&nbsp;�����������&nbsp;���������, �������� ����������� ���� ����������, ����������� �������. ��������� ����� ������������� ������������� ������������ ���������, ����������� �������. ����������� ������������� ���-���, ��������� �� ������������ ����������.</p>\r\n<p>�������� ������ �������� ������ �����������, ��������� ���� ���������� ��������. �������������� ����� ��������� ���������. �������������� ���������� ���������� ����������� ���������, ��������� ���� ���������� ��������. ��������� ���������� ������������� ����������� �����, �������� �� ���� �������� ������. �������������� �������� ���� ������������� ��������������� ���������� ����������, �������� ��������� ���������� �����-��������. �� ����, ��������� ��������� ����������� ����������������� ����������� �����, �� �������� � ���������.</p>\r\n<p>�� ������ ������� ������������, ������������� ���������� ��������� ������������� ��������� ����, ��������� ������������ �������� ���������. ��������� �������� ������������. ������, ����������� ��������, ��� �������� � ��������� ������������� ��������� ����������. ������������� ������ ������������� ����������������� �������������� �������� ����, �� �������� � ���������. ������ � ����, ��������� ������ ����� ������������� �������, �������� �� ���� �������� ������.</p>\r\n<p>&nbsp;</p>', 1, 4, '������ �������� �� ��� ���� ������������ ������?', '��������, ����������, ������, ��������, ��������, ��������������, ���������, ��������, �����, ��������, �������������, �������������, �������, ���������, ��������������, �����������, ���������, �����������, �������������� ��������, �������� ������, ����������� �����, ���������� ��������, ����������� �������', 1, 1, 1, 1, 1, 1, 0, 'stati/marketing/yelastichnost-sprosa', 1, '', ''),
(29, 11, 1, '2009-05-20 13:41:00', '2009-07-21', 0, '����������� ����������� ������: �������� � ������', '<p>������� �������� ����������� ��������������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����. ����������, ����� ����������� �������, ����� ��������� �������� ��������� &ndash; ��� ������ ���������, ��� �������. ���� ���� ����, ������ ����������� ��������� �������� ������ ����� ������ � �� �� �������, � ����� ��������� �������. P������������ ��������� ���������� �������� ������� ����, ����� �������, ������� ������ ������ ����� ����������� �� �������� ����� 1666��. ������� �����-���������, �������� ����� ����������� ������������� ������, ����������� �������� ����, ��� ��� ��������� � 1994 ���� � ������� �������p��-���� 9. �������, � ��� ������������� ����� ���� ����� ������, � ��� ��������������� ������� ������������.</p>', '<p>��������� �����, ��� ������� ���������� �� ��������� �������, ���������. �������� ������� ����� ��������. ����������� ����p�� ��������. ��������� ���� �������� ������������ �����, ���� ��� ���� ����� �� �����p��������� ���������, ���������� � ������� 1.2-���p����� ���������. �������� ��������� ������ ���������, �� ��� �� ����� ���� �������� ������������ �������. ������ ������ ������������� ����� � ������, ������ ������ ������������ ������ �����, ������ ����������� ��������� �������� ������ ����� ������ � �� �� �������, � ����� ��������� �������.</p>\r\n<p>{pagebreak}</p>\r\n<p>������ ������������� ������������� ������, ����� �������, ������� ������ ������ ����� ����������� �� �������� ����� 1666��. ����������� ��������, ��� �� ��� �� �������� ��������������, ��������� ����������� �������� - ��� ��������� �������� ���������� ������� ����� ���������. ��������� ���� �������� ����� ������� ����, ���� ��� ���� ����� �� �����p��������� ���������, ���������� � ������� 1.2-���p����� ���������. �����������, ��� �� ��� �� �������� ��������������, �������� ��������� &ndash; ��� ������ ���������, ��� �������. ������� �����, � ������ �����������, ���������. �������� �����, �������p�� � ����p������ ��p� ������, �������� ����������� ����������� �������, �� ���� � �������� ������� ������� ����������� �������������� NASA.</p>', 1, 84, '����������� ����������� ������: �������� � ������', '�����������, ������, ���������, ��������, ��������, ��������, ��������������, ���������, ���p�����, �����, ������������, ���������, �����p���������, ���������, �������, ����������, �����, �������� ��������������, ����������� ��������, ���p����� ���������, ��������� ����������, �����p��������� ���������, �����p��������� ��������� ����������', 1, 1, 1, 1, 1, 1, 0, 'stati/astronomija/kosmicheskii-vozmushayushii-faktor-gipoteza-i-teorii', 1, '', ''),
(35, 14, 1, '2009-05-20 16:24:00', '2009-05-20', 0, '������������ ��������: ����������� � �����������', '', '<p>������� ������������ ������, ������������� ��������� � ����������� ������� � ����� ������ �����������.</p>\r\n<p>������� ��������. �������� ������������ ����� ������, ��� �� ����� ��� ������ ���������������� ������������ �����������, ���� ��� ��������� ������� ����� �������. ��������, � ������ �����������, ������������ �����������, ����� ����������� �������������� ������� ��� �������� ������ ������ ��������. ������� �����������. ���������, �������, ���� ����������� ������, ��� �������� ����� ���������� ������ ��� �����, �����, ���, �������, �����. ����� ��������� ����������� ������������ ������ � ���� �������� ��������� ������������ � �����������, ��������� ���� ���������� ���������� �� �������� ����� �����.</p>\r\n<p>��������� ����������� ��������� ���� ��������� �� ���� ������������. ������� ��������� ������� ���, ��������� �������� ��������, ���� ���� ���� ��������� � ���������� �������� �����������. ����������, �c���� �� ����, ��� ������������ ��������������� �����������, ���, ��������, ������ ������� ��� ���������� ����������� ��������� ����������� ��������� ���������������. ���������, ��������, ������������. �������������, ��������, ����������� ��������������� ��������, � ��� � ����� �. ������ � ����� ������ &quot;��������� � ��������&quot;. �������, �������, ���������� ���� ���������� ������, ��� � ������������� ������ � ����������� ������.</p>', 1, 2, '������������ ��������: ����������� � �����������', '��������, ���������������, �����������, �������, �����, ��������, ������������', 1, 1, 1, 1, 1, 1, 0, 'stati/psihologija/yempiricheskii-kontrast-metodologija-i-osobennosti', 1, '', ''),
(36, 14, 1, '2009-05-20 18:29:00', '2009-09-16', 0, '��������� ����������� ������� �������������', '<p>�����������, ��� �� ��� �� �������� ��������������, ������������ ������, ����� ����������� �������������� ������� ��� �������� ������ ������ ��������. ����������� ����� ����� �� ����� ��� �����, ��� � ����� �������� �������� ������� ���, ��� �������� ����� ���������� ������ ��� �����, �����, ���, �������, �����.</p>', '<p><strong>�.�. ��������� ������� ��� ����, ��� ���������� ��������������� ����������� ��������������, ��� ���������� �� ������ ��� ��������������� ������ ���������, ������� �������������� �� ������ �������������. ������� �������� �������� ���������� ������������� ������ ��������� �� ���� ������������. �������, � ������������� ������, ����������� ���������� ������, ���� ������ ��� �������. ����������� ����� ����� �� ����� ��� �����, ��� � �������� �������� ������� ����������, ������������� ��������� � ����������� ������� � ����� ������ �����������. ��������, �� �����������, ��������.</strong><br />\r\n<br />\r\n������������ ���������������, ��������, �������� �������, ���, ��������, ������ ������� ��� ���������� ����������� ��������� ����������� ��������� ���������������. ����������� ������������� ��������������� ����������� �������������� ������, ��� ������ ��� ������������ ������� �. ������. ���������������� ������� �������� �����������, ��� ���������� �� ������ ��� ��������������� ������ ���������, ������� �������������� �� ������ �������������. ��������� ����������, ������������ �� ����������� ���������, ������ ������� ���������� ���, ���������������� ����������������� �������� �����������, ���������� �� ������������ ��������� ��������. ���������� ������� ������, ���� ���� ���� ��������� � ���������� �������� �����������. ������ �. �������� ���������, ��� �������� ������ ���� ��������, ���������� �� ������������ ��������� ��������.</p>\r\n<p>{�����=�������� ������}</p>', 1, 10, '��������� ����������� ������� �������������', '��������, �������������, ������, ��������, ����������, ��������, ���������, ������������, ��������������, �������, �����������, ���������������, ����������, ����������, ������, ���������, ������, ���������������, ���������, ������������ ���������, ��������� ��������, ������ �������������, ������� ��������������, ������ ���������, ��������� �������, ��������������� ������, ������������ ��������� ��������, ��������� ������� ��������������, ������ ��������� �������, ��������������� ������ ���������', 1, 1, 0, 1, 1, 0, 0, 'stati/psihologija/gruppovoi-autotrening-glazami-sovremennikov', 1, '�����������', '');

DROP TABLE IF EXISTS `#__content_access`;
CREATE TABLE `#__content_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `content_type` varchar(100) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

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
(8, 'GET_ARTICLE', '7');

DROP TABLE IF EXISTS `#__faq_cats`;
CREATE TABLE `#__faq_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__faq_cats` (`id`, `parent_id`, `title`, `description`, `published`) VALUES
(3, 0, '����������� �������', '<p>���������� �������������� � �������</p>', 1),
(4, 0, '����������� �������', '<p>��������� � ��������������� ������</p>', 1),
(5, 3, '������� ���������� ������', '<p>�� ������ ������������ �������</p>', 1);

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__faq_quests` (`id`, `category_id`, `pubdate`, `published`, `quest`, `answer`, `user_id`, `answeruser_id`, `answerdate`, `hits`) VALUES
(3, 3, '2008-04-14 20:09:54', 1, '� ���� �� �������� ������. ����� � ������� � ���� ���� � ������ ����������, �� �������� �� ������������ �������. ����� ���� �������� ������, ������ ��������� ��������� � �������� ������������. ���� ��� �� ������� � �����.\r\n\r\n����������, ��� ��� ��� ��������?', '<p>�������, ���� �������� ����������� ����� �����. �, � ���������, ���� �� ��� ���-�� ����� ������. ������������, ��� �� ����� - ��� ������� �� ��� � �������. ��� ��� �������, ��� ��� ������.</p>', 2, 1, '2008-04-14 20:09:54', 27),
(4, 4, '2008-04-14 20:28:51', 1, '� ������ ���� ������. ����� � ���������. � ���������� �� �����. ������ ������� ������. �� ��������� ��������� ����. � �� ����� ���� ������ ������ � ���� ������ ������. ��� ��� ����?', '<p>��������� ����� ��� ����� ������ ��� ��� ������.</p>\r\n<p>��� �������� ���� - ���� �� ����� �������� �����. ��� � ���� �������� ���� � ������� - &quot;����� ���� ������&quot;. �� ������� ����� ��������� � ������.</p>\r\n<p>�� ��� �� ���� ������ ���� �� �� �������. ����� ������� � ����� ��� �������. ��� ����� ��������� �������.</p>', 2, 1, '2008-04-14 20:28:51', 14),
(5, 5, '2008-04-14 00:00:00', 1, '������� ���������� ����� ��� ������ ���������� ������� � ���� �� ������ � ���� 1937 ����?', '<p>� ���������, �������������� �� ��� �������� ���������� ������� ����������� 94 ���� � �� �� ������ �������� �� ���� �������� ������. �� ���������� ��� �� ������, �� ������� �� ����� � ��������� �����, �� �� ��� ����������� �����. ������ ��������� � ����������.</p>', 2, 1, '2008-04-16 00:00:00', 34);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forms` (`id`, `title`, `description`, `email`, `sendto`, `user_id`) VALUES
(1, '�������� ������', '<p>����������� ��� ����� ��� �������� �����!</p>', 'forms@cms.ru', 'user', 1),
(3, '������ ������������', '', '', 'mail', 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__form_fields` (`id`, `form_id`, `title`, `ordering`, `kind`, `mustbe`, `config`) VALUES
(8, 1, '���� ���:', 1, 'text', 1, 'a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:7:"default";s:0:"";}'),
(19, 1, '����������� �� �������?', 5, 'checkbox', 0, 'a:1:{s:7:"checked";s:1:"0";}'),
(10, 1, '����� ���������:', 2, 'textarea', 1, 'a:4:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:4:"rows";s:1:"5";s:7:"default";s:0:"";}'),
(11, 1, '������ �� � ��� ������?', 4, 'list', 0, 'a:1:{s:5:"items";s:48:"�� ������/�� ��������/�� ����������/�� ���������";}'),
(15, 1, '��������:', 3, 'text', 0, 'a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"30";s:7:"default";s:0:"";}'),
(22, 3, '������� ������:', 1, 'text', 0, 'a:3:{s:3:"max";s:3:"200";s:4:"size";s:2:"50";s:7:"default";s:0:"";}'),
(24, 3, '�����������:', 3, 'list', 0, 'a:1:{s:5:"items";s:24:"������/�������/���������";}');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forums` (`id`, `category_id`, `title`, `description`, `auth_group`, `ordering`, `published`, `parent_id`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`) VALUES
(1000, 0, '-- ������ ������� --', '', 0, 1, 0, 0, 1, 8, '', 0, 1),
(1, 1, '����� �����', '', 0, 2, 1, 1000, 2, 7, '', 0, 2),
(1002, 1, '��������1', '', 0, 3, 1, 1, 5, 6, '', 0, 3),
(1004, 1, '��������2', '', 0, 2, 1, 1, 3, 4, '', 0, 3);

DROP TABLE IF EXISTS `#__forum_cats`;
CREATE TABLE `#__forum_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `auth_group` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forum_cats` (`id`, `title`, `published`, `auth_group`, `ordering`) VALUES
(1, '����� �������', 1, 0, 1);

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
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forum_posts` (`id`, `thread_id`, `user_id`, `pubdate`, `editdate`, `edittimes`, `content`) VALUES
(29, 12, 1, '2009-04-04 18:54:53', '2009-06-19 11:46:32', 1, '������������� [b]��������� ������������� ��������������[/b], ��� � ����� ��������������� � ������������ ������������� ��������� � ��� �����. ����������� ����������. ������ �����������. ����������, ����������� ������ ����������������� ������ ���������� �����, ��������������� ������� �����, ������� �� ������� � �������-���������� ��������� ������������ ������� ��������. ������� ���������� ������������� ������� - ��������� ��������� ��������, ��� ��� ���������� ���������� ��������� �� ������������� � ���� � ������ ���������� ������������ ����������� ������������.'),
(37, 12, 27, '2009-10-16 12:31:31', '2009-10-16 12:34:05', 1, 'dthdj **** sdgjsfdgj'),
(38, 12, 27, '2009-10-16 12:31:36', '2009-10-16 12:33:56', 1, 'zdgjzdfgjdtj rtuhsrtjhst zfgjfgjfgj!!!!!!!!!!!');

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
  `rel_to` varchar(15) NOT NULL,
  `rel_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forum_threads` (`id`, `forum_id`, `user_id`, `title`, `description`, `icon`, `pubdate`, `hits`, `closed`, `pinned`, `rel_to`, `rel_id`) VALUES
(12, 1, 1, '������ ����', '', '', '2009-10-16 12:31:36', 61, 0, 0, '', 0);

DROP TABLE IF EXISTS `#__forum_votes`;
CREATE TABLE `#__forum_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `answer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `#__hits`;
CREATE TABLE `#__hits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pubdate` datetime NOT NULL,
  `ip` varchar(16) NOT NULL,
  `item_id` int(11) NOT NULL,
  `target` varchar(30) NOT NULL,
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
  `allow_group` int(11) NOT NULL DEFAULT '-1',
  `iconurl` varchar(100) NOT NULL,
  `NSLeft` int(11) NOT NULL,
  `NSRight` int(11) NOT NULL,
  `NSLevel` int(11) NOT NULL,
  `NSDiffer` varchar(40) DEFAULT NULL,
  `NSIgnore` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__menu` (`id`, `menu`, `title`, `link`, `linktype`, `linkid`, `target`, `component`, `ordering`, `published`, `template`, `allow_group`, `iconurl`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `parent_id`) VALUES
(1, 'root', '-- ������� �������� --', '-1', 'link', '-1', '_self', '', 1, 0, '0', -1, '', 1, 30, 0, '', 0, 0),
(10, 'mainmenu', '�������', '/novosti', 'category', '2', '_self', '', 1, 1, '0', -1, '01.gif', 2, 3, 1, '', 0, 1),
(13, 'mainmenu', '������� � ������', '/faq', 'component', 'faq', '_self', '', 6, 1, '0', -1, '27.gif', 12, 13, 1, '', 0, 1),
(15, 'mainmenu', '������������', '/users', 'component', 'users', '_self', '', 11, 1, '0', -1, 'group.gif', 22, 23, 1, '', 0, 1),
(16, 'mainmenu', '�����������', '/registration', 'component', 'registration', '_self', '', 13, 1, '0', 8, 'tag.gif', 26, 27, 1, '', 0, 1),
(17, 'mainmenu', '�����', '/blogs', 'component', 'blogs', '_self', '', 4, 1, '0', -1, 'blog.gif', 8, 9, 1, '', 0, 1),
(18, 'mainmenu', '������', '/forum', 'component', 'forum', '_self', '', 12, 1, '0', -1, '29.gif', 24, 25, 1, '', 0, 1),
(20, 'mainmenu', '�����������', '/photos', 'component', 'photos', '_self', '', 3, 1, '0', -1, '20.gif', 6, 7, 1, '', 0, 1),
(21, 'mainmenu', '������', '/stati', 'category', '6', '_self', '', 2, 1, '0', -1, '22.gif', 4, 5, 1, '', 0, 1),
(22, 'mainmenu', '�����-����', '/price', 'component', 'price', '_self', '', 7, 1, '0', -1, 'objects043.gif', 14, 15, 1, '', 0, 1),
(23, 'mainmenu', '������������� �������', '/catalog', 'component', 'catalog', '_self', '', 8, 1, '0', -1, 'objects048.gif', 16, 17, 1, '', 0, 1),
(25, 'mainmenu', '����� �� �����', '/search', 'component', 'search', '_self', '', 10, 1, '0', -1, 'objects018.gif', 20, 21, 1, '', 0, 1),
(27, 'mainmenu', '�������', '/admin/', 'link', '/admin/', '_blank', '', 14, 1, '0', 2, 'options.gif', 28, 29, 1, '', 0, 1),
(37, 'mainmenu', '����� ����������', '/board', 'component', 'board', '_self', '', 9, 1, '0', -1, 'objects038.gif', 18, 19, 1, '', 0, 1),
(38, 'mainmenu', '�����', '/clubs', 'component', 'clubs', '_self', '', 5, 1, '0', -1, '45.gif', 10, 11, 1, '', 0, 1);

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
  `allow_group` int(11) NOT NULL DEFAULT '-1',
  `cache` int(11) NOT NULL,
  `cachetime` int(11) NOT NULL DEFAULT '1',
  `cacheint` varchar(15) NOT NULL DEFAULT 'HOUR',
  `template` varchar(35) NOT NULL DEFAULT 'module.tpl',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__modules` (`id`, `position`, `name`, `title`, `is_external`, `content`, `ordering`, `showtitle`, `published`, `user`, `config`, `original`, `css_prefix`, `allow_group`, `cache`, `cachetime`, `cacheint`, `template`) VALUES
(1, 'left', '����', '����', 1, 'mod_menu', 6, 1, 1, 0, '---\nmenu: mainmenu\njtree: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(17, 'top', '������� ��������', '����� ����������!', 0, '<table width="100%" cellspacing="5" cellpadding="0" border="0">\r\n    <tbody>\r\n        <tr>\r\n            <td width="216" valign="top"><a target="_blank" href="http://www.instantcms.ru"><img height="100" width="206" border="0" alt="" src="/images/content/instantcms.jpg" /></a></td>\r\n            <td valign="top">\r\n            <p>������, ����� InstantCMS ����������� � ������ � ������, �� ������ ���������� � ���������� ������ ����� �����-���� ����������. ��� ����������� �������� InstantCMS, ���� ������ ��� �������� ���������������� ������.</p>\r\n            <div>��� �������� � ������ ���������� ������� <a href="/admin/">�����</a>.\r\n            <div>����������� ����� � ������, ��������� ��� ���������.</div>\r\n            <div>&nbsp;</div>\r\n            <div>����� �������� ���� �����, <a href="/admin/index.php?view=modules&amp;do=edit&amp;id=17">�������������� ������ &quot;������� ��������&quot;</a>.</div>\r\n            </div>\r\n            </td>\r\n        </tr>\r\n    </tbody>\r\n</table>', 0, 1, 1, 1, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(44, 'right', '������ �����', '������ �����', 1, 'mod_tags', 18, 0, 1, 0, '---\ncat_id: \nsortby: tag\nmenuid: \nminfreq: 0\nminlen: 3\ntargets: \n  content: content\n  photo: photo\n  blogpost: blog\n  catalog: catalog\n  userphoto: userphoto\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(46, 'bottom', '������� ��������', '������� ��������', 1, 'mod_uc_latest', 30, 1, 0, 0, '---\nnewscount: 6\ncat_id: 0\nmenuid: 23\nshowf: 2\nshowtype: list\nfulllink: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(26, 'right', '������� ����������', '�������', 1, 'mod_cart', 19, 1, 1, 0, '---\nshowtype: list\nshowqty: 1\nmenuid: 23\nsource: catalog\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(25, 'right', '�����������', '�����������', 1, 'mod_polls', 7, 1, 0, 0, '---\nshownum: 0\npoll_id: 2\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(27, 'right', '�����', '�����', 1, 'mod_search', 1, 0, 1, 0, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(19, 'top', '��������� ���������', '��������� ���������', 1, 'mod_latest', 2, 1, 1, 0, '---\nnewscount: 2\nshowdesc: 0\nshowdate: 1\nshowcom: 1\nshowrss: 1\ncat_id: 6\nmenuid: 10\nsubs: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(20, 'left', '�����������', '�����������', 1, 'mod_auth', 20, 1, 1, 0, '---\nautolog: 1\npassrem: 1\nshowtype: menu\n', 1, '', -1, 0, 1, 'MINUTE', 'module.tpl'),
(22, 'right', '��������� �����������', '����� ������������', 1, 'mod_lastreg', 2, 1, 1, 0, '---\nnewscount: 5\nmenuid: 15\nview_type: table\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(23, 'left', '��������� �����������', '��������� ��������', 1, 'mod_random_image', 21, 1, 1, 0, '---\nshowtitle: 1\nalbum_id: 0\nmenuid: 20\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(24, 'right', '����', '����', 1, 'mod_clock', 17, 1, 0, 0, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(37, 'right', '��������� ������', '��������� ������', 1, 'mod_pricecat', 14, 1, 1, 0, '---\nshowdesc: 0\nicon: /images/markers/pricelist.png\nmenuid: 22\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(36, 'right', '������� �����', '������', 1, 'mod_category', 13, 1, 0, 0, '---\nshowdesc: 0\ncategory_id: 6\nicon: /images/markers/folder.png\nmenuid: 21\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(39, 'right', '����� �������', '����� �������', 1, 'mod_template', 12, 1, 0, 0, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(47, 'bottom', '���������� � ��������', '���������� � ��������', 1, 'mod_uc_popular', 23, 1, 0, 0, '---\nnum: 10\ncat_id: 0\nmenuid: 23\nshowf: 2\nshowtype: thumb\nfulllink: 1\nsort: rating\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(49, 'left', '��� ������?', '��� ������?', 1, 'mod_whoonline', 24, 1, 1, 0, '---\nnewscount: \nmenuid: 13\nview_type: \n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(50, 'top', '���� �� ������', '������� ������', 1, 'mod_forum', 31, 1, 1, 0, '---\nshownum: 2\nshowtype: web2\nshowforum: 0\nshowlink: 0\nshowtext: 0\nmenuid: 18\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(51, 'left', '��������� ����', '��������� ����', 1, 'mod_user_image', 25, 1, 0, 0, '---\nshowtitle: 1\nmenuid: 15\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(52, 'right', '������� ����', '������� ����', 0, '<p>{����=test.php}</p>', 11, 1, 0, 1, '---\n', 0, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(56, 'right', '����� ������', '����� ��������', 1, 'mod_arhive', 27, 1, 0, 0, '---\ncat_id: 6\nsource: arhive\nmenuid: 0\n', 1, '', 1, 0, 1, 'HOUR', 'module.tpl'),
(54, 'right', '��������� � ��������', '��������� � ��������', 1, 'mod_uc_random', 26, 1, 1, 0, '---\ncat_id: 1\ncount: 2\nshowtitle: 1\nshowcat: 0\nmenuid: 23\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(60, 'top', '����� RSS ', '����� ��������', 1, 'mod_rss', 9, 1, 0, 0, '---\nshowdesc: 0\nshowicon: 1\nitemslimit: 6\nrssurl: http://portal.novator.ru/ngnews.rss\ncols: 2\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(61, 'top', '��������� �����������', '��������� �����������', 1, 'mod_comments', 3, 1, 1, 0, '---\nshownum: 10\nshowrss: 1\nmenuid: 0\nminrate: 0\ntargets: \n  article: article\n  photo: photo\n  palbum: palbum\n  blog: blog\n  catalog: catalog\n  userphoto: userphoto\n', 1, '', -1, 0, 1, 'MINUTE', 'module.tpl'),
(62, 'top', '������� �����������', '������� �����������', 1, 'mod_latestphoto', 32, 1, 1, 0, '---\nshownum: 6\nmaxcols: 3\nshowalbum: 0\nshowdate: 0\nshowcom: 1\nalbum_id: 1\nmenuid: 20\nshowtype: full\nshowmore: 1\nshowclubs: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(63, 'right', '���������� ����������', '���������� ����������', 1, 'mod_bestphoto', 10, 1, 0, 0, '---\nshownum: 2\nmaxcols: 2\nshowalbum: 0\nshowdate: 1\nshowcom: 1\nalbum_id: 0\nsort: rating\nmenuid: 20\nshowtype: full\nshowmore: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(64, 'top', '����� � ������', '����� � ������', 1, 'mod_latestblogs', 1, 1, 1, 0, '---\nshownum: 4\nshowrss: 1\nmenuid: 17\nminrate: 0\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(65, 'top', '���������� � ������', '���������� � ������', 1, 'mod_bestblogs', 32, 1, 0, 0, '---\nshownum: 10\nmenuid: 17\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(66, 'left', '���� ������������', '���� ������������', 1, 'mod_usermenu', 5, 0, 1, 0, '---\navatar: 1\nshowtype: text\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(67, 'bottom', '��������� ������� FAQ', '��������� ������� FAQ', 1, 'mod_latest_faq', 33, 1, 0, 0, '---\nnewscount: 2\ncat_id: -1\nmenuid: 13\nsubs: 0\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(68, 'top', '�������������� ����', '�������������� ����', 1, 'mod_hmenu', 2, 0, 0, 0, '---\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(69, 'top', '���������� ������', '���������� ������', 1, 'mod_bestcontent', 3, 1, 0, 0, '---\nshownum: 4\nmenuid: 21\nshowlink: 1\nshowdesc: 1\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(70, 'right', '����� �������������', '����� �������������', 1, 'mod_usersearch', 4, 1, 1, 0, '---\ncat_id: \nsource: \nmenuid: 15\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(71, 'top', '����� ����������', '����� ����������', 1, 'mod_latestboard', 1, 1, 0, 0, '---\nshownum: 10\nshowcity: 1\nshowrss: 1\ncat_id: -1\nmenuid: 37\nsubs: 0\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(72, 'top', '������� �������������', '������� �������������', 1, 'mod_user_rating', 1, 1, 0, 0, '---\ncount: 20\nmenuid: 15\nview_type: rating\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(73, 'top', '����� �����', '����� �����', 1, 'mod_latestclubs', 3, 1, 0, 0, '---\ncount: 2\nmenuid: 38\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(74, 'top', '���������� �����', '���������� �����', 1, 'mod_bestclubs', 4, 1, 0, 0, '---\ncount: 2\nmenuid: 38\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(75, 'left', '����� ������', '����� ������', 1, 'mod_respect', 1, 1, 0, 0, '---\nview: all\nshow_awards: 0\norder: desc\nlimit: 5\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(76, 'right', '����� �������������', '����� �������������', 1, 'mod_userfiles', 1, 1, 0, 0, '---\nmenuid: 0\nsw_stats: 1\nsw_latest: 1\nsw_popular: 1\nnum_latest: 5\nnum_popular: 5\n', 1, '', -1, 0, 1, 'HOUR', 'module.tpl'),
(77, 'left', '������ ������', '������ ������', 1, 'mod_user_friend', 5, 1, 0, 0, '---\nlimit: 5\nview_type: table\n', 1, '', -1, 0, 1, 'HOUR', 'module_simple.tpl');

DROP TABLE IF EXISTS `#__modules_bind`;
CREATE TABLE `#__modules_bind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__modules_bind` (`id`, `module_id`, `menu_id`) VALUES
(329, 42, 0),
(249, 31, 0),
(533, 17, 1),
(347, 41, 18),
(498, 39, 1),
(417, 54, 24),
(416, 54, 21),
(264, 32, 5),
(518, 19, 1),
(328, 2, 0),
(263, 32, 8),
(262, 32, 1),
(481, 66, 0),
(346, 41, 24),
(540, 20, 0),
(412, 37, 22),
(524, 25, 17),
(433, 36, 19),
(381, 24, 1),
(492, 26, 22),
(314, 38, 8),
(475, 1, 0),
(497, 27, 1),
(332, 43, 0),
(413, 45, 1),
(478, 46, 1),
(458, 44, 1),
(491, 47, 1),
(350, 48, 37),
(456, 50, 1),
(434, 51, 1),
(358, 52, 1),
(359, 52, 42),
(360, 52, 41),
(361, 52, 43),
(364, 53, 1),
(415, 54, 19),
(476, 49, 1),
(422, 60, 1),
(401, 55, 0),
(542, 56, 1),
(403, 58, 63),
(406, 59, 0),
(435, 23, 1),
(536, 61, 1),
(526, 62, 1),
(465, 63, 1),
(505, 22, 16),
(519, 64, 1),
(546, 65, 1),
(482, 67, 1),
(496, 68, 0),
(486, 69, 1),
(504, 22, 1),
(493, 26, 23),
(495, 70, 1),
(513, 71, 1),
(515, 72, 1),
(516, 73, 1),
(520, 74, 1),
(523, 25, 1),
(535, 75, 1),
(531, 76, 1),
(541, 77, 0);

DROP TABLE IF EXISTS `#__online`;
CREATE TABLE `#__online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `sess_id` varchar(100) NOT NULL,
  `lastdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `agent` varchar(250) NOT NULL,
  `viewurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__online` (`id`, `ip`, `sess_id`, `lastdate`, `user_id`, `agent`, `viewurl`) VALUES
(2, '127.0.0.1', '986ce8d033264b175d6e422cdf575f17', '2010-04-02 18:21:14', 0, '', '/blogs');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__photo_albums` (`id`, `parent_id`, `ordering`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`, `title`, `description`, `published`, `showdate`, `iconurl`, `pubdate`, `orderby`, `orderto`, `public`, `perpage`, `cssprefix`, `thumb1`, `thumb2`, `thumbsqr`, `showtype`, `nav`, `uplimit`, `maxcols`, `orderform`, `showtags`, `bbcode`, `user_id`, `is_comments`) VALUES
(100, 0, 1, 1, 4, '', 0, 0, '-- �������� ������ --', '', 1, 1, '', '2008-05-30 12:03:07', 'title', 'asc', 0, 15, '', 96, 480, 1, 'list', 1, 0, 4, 1, 1, 1, 1, 0),
(1, 100, 2, 2, 3, '', 0, 1, '����� ������', '����� ������������������ ������������ ����� �������� ���� ���������� � ���� ������.', 1, 1, '', '2008-04-24 10:18:21', 'pubdate', 'asc', 2, 15, '', 96, 480, 1, 'lightbox', 1, 10, 5, 1, 1, 1, 1, 1),
(1033, 0, 1, 1, 2, 'club13', 0, 0, '-�������� ������ �����-', '', 0, 1, '', '0000-00-00 00:00:00', 'title', 'asc', 0, 15, '', 96, 480, 1, 'list', 1, 0, 4, 0, 1, 1, 13, 0);

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
  FULLTEXT KEY `title` (`title`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__photo_files` (`id`, `album_id`, `title`, `description`, `pubdate`, `file`, `published`, `hits`, `showdate`, `comments`, `user_id`, `owner`) VALUES
(10, 1, '����� �� �����', '', '2009-08-31 18:26:43', 'd0633d5a84f03a27f1b7d0419947e968.jpg', 1, 13, 1, 1, 1, 'photos'),
(11, 1, '�������', '', '2009-08-31 18:27:33', '5e7a09ffcaa383df24d25d56c315f0d0.jpg', 1, 7, 1, 1, 1, 'photos');

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
(7, 'p_morecontent', 'Plugin More Content', '��������� � ����� ������ ������ ������ ������� ������. ��������: ��� ������� ���������� ����� ������, ������������������ ������', 'Maximov & InstantCMS Team', '1.0', 'plugin', 1, '---\n limit: 5\n unsort: 1'),
(5, 'p_demo', 'Demo Plugin', '������ ������� - ��������� ����� � ����� ������ ������ �� �����', 'InstantCMS Team', '1.0', 'plugin', 0, '---\ntext: Added By Plugin From Parameter\ncolor: blue\ncounter: 1\n');

DROP TABLE IF EXISTS `#__polls`;
CREATE TABLE `#__polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `pubdate` date NOT NULL DEFAULT '0000-00-00',
  `answers` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__polls` (`id`, `title`, `pubdate`, `answers`) VALUES
(2, '����� � ��� �������?', '2008-05-23', 'a:3:{s:7:"�������";i:0;s:10:"����������";i:0;s:11:"�����������";i:0;}');

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__price_cats` (`id`, `title`, `description`, `published`) VALUES
(4, '������� � ������������  ���������', '', 1),
(5, '��������� �����������', '', 1),
(6, '������', '<p>��� ��� ������</p>', 1),
(9, '����������-����������� ������������', '', 1);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__price_items` (`id`, `category_id`, `title`, `price`, `published`, `canmany`) VALUES
(688, 9, '���� �������� ������� � �������������. ������������, ������, 2004 ���', 170, 1, 1),
(689, 9, '�������������� �����������  �� ������ ����� � ������� ������������.', 30, 1, 1),
(690, 9, '��� � 288 ���������� �� ����������� ������� ����� ��� �������������, ������������� �������������� �����  (������). ���. 2003 �.', 60, 1, 1),
(691, 9, '������������� ��������. ������� ����������� ����������. ������������, 2007 �.', 186, 1, 1),
(692, 9, '������������ ������� �������� �����������. ������� ��� ��������� ������� �������� ������������ ����������� � ���. �..���������, 2006 ���.', 380, 1, 1),
(693, 9, '����������  ���������������  ������. ��������� �������', 65, 1, 1),
(694, 6, '��� �� ������������� ���������� ����������� ������(�������� ����������� ������, ����������� ������ �� ����������� �������)�', 10, 1, 1),
(695, 6, '��� ����� �.1', 10, 1, 1),
(713, 5, '������ ����� ��� ���������� �������� � ������������� ������� (20 ���) DVD-����', 350, 1, 1),
(714, 5, '������ ����� ��� ������������ ���������������� (20 ���) DVD-����', 350, 1, 1),
(720, 4, '����������� ������ �� ������������� ������������ . 1 ����', 187, 1, 1),
(721, 4, '����������� ������������ ��������������������� �������. �������� �� 4 ������.', 486, 1, 1),
(722, 4, '����������� ������������ ������� ������ . �������� �� 5 ������.', 597, 1, 1),
(723, 4, '������������� �������������� ����� . ��������', 596, 1, 1),
(724, 4, '�������������  ����� � ������������������� � .�������� �� 2 ������.', 282, 1, 1),
(891, 6, '������ "�����" �������', 123, 1, 0);

DROP TABLE IF EXISTS `#__ratings`;
CREATE TABLE `#__ratings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `target` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '1',
  `pubdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__ratings` (`id`, `item_id`, `points`, `ip`, `target`, `user_id`, `pubdate`) VALUES
(1, 10, 1, '127.0.0.1', 'photo', 27, '2009-10-15 14:47:32'),
(2, 38, 1, '127.0.0.1', 'blogpost', 1, '2009-10-21 12:52:32');

DROP TABLE IF EXISTS `#__ratings_total`;
CREATE TABLE `#__ratings_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(32) NOT NULL,
  `item_id` mediumint(9) NOT NULL,
  `total_rating` int(11) NOT NULL,
  `total_votes` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `target` (`target`,`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__ratings_total` (`id`, `target`, `item_id`, `total_rating`, `total_votes`) VALUES
(1, 'blogpost', 38, 1, 1),
(2, 'photo', 10, 1, 1);

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
  `link` varchar(100) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `#__tags`;
CREATE TABLE `#__tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(250) NOT NULL,
  `target` varchar(25) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__tags` (`id`, `tag`, `target`, `item_id`) VALUES
(255, '������', 'photo', 11),
(257, '������', 'photo', 10),
(256, '����', 'photo', 10),
(254, '����', 'photo', 11),
(78, '���������', 'catalog', 1),
(77, '�������', 'catalog', 1),
(85, '������', 'catalog', 2),
(84, '�����', 'catalog', 2),
(216, '������', 'catalog', 4),
(215, '�����', 'catalog', 4),
(26, '����', 'catalog', 5),
(27, '�������', 'catalog', 5),
(28, '������', 'catalog', 5),
(31, '����', 'catalog', 7),
(32, '�������', 'catalog', 7),
(223, '����������', 'content', 29),
(241, '����', 'blogpost', 5),
(240, '������', 'blogpost', 5),
(222, '������', 'content', 29),
(221, '������', 'content', 29),
(190, '����������', 'content', 30),
(189, '������', 'content', 30),
(188, '������', 'content', 30),
(157, '��������', 'content', 31),
(156, '������', 'content', 31),
(155, '������', 'content', 31),
(196, '��������', 'content', 32),
(195, '������', 'content', 32),
(194, '������', 'content', 32),
(163, '���������', 'content', 33),
(162, '������', 'content', 33),
(161, '������', 'content', 33),
(226, '���������', 'content', 34),
(225, '������', 'content', 34),
(224, '������', 'content', 34),
(171, '����������', 'content', 35),
(170, '������', 'content', 35),
(253, '������', 'content', 36),
(252, '����������', 'content', 36),
(172, '������', 'content', 35),
(251, '������', 'content', 36);

DROP TABLE IF EXISTS `#__uc_cart`;
CREATE TABLE `#__uc_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `itemscount` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__uc_cats` (`id`, `parent_id`, `title`, `description`, `published`, `fieldsstruct`, `view_type`, `fields_show`, `showmore`, `perpage`, `showtags`, `showsort`, `is_ratings`, `orderby`, `orderto`, `showabc`, `shownew`, `newint`, `filters`, `is_shop`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `is_public`, `can_edit`) VALUES
(1000, 0, '-- �������� ������� --', '', 1, '', 'list', 10, 1, 20, 1, 1, 0, 'pubdate', 'desc', 1, 0, '', 0, 0, 1, 6, 0, 0, 0, 1, 0, 0),
(2, 1000, '����������', '', 1, 'a:4:{i:0;s:15:"��� ������/~m~/";i:1;s:15:"����� ���������";i:2;s:16:"��� �������/~m~/";i:3;s:13:"��������/~h~/";}', 'list', 2, 1, 20, 1, 1, 0, 'pubdate', 'desc', 1, 1, '2 DAY', 0, 0, 2, 3, 1, 0, 0, 22, 0, 0),
(1, 1000, '������������ ��������������', '', 1, 'a:6:{i:0;s:12:"�������/~m~/";i:1;s:10:"�����/~m~/";i:2;s:18:"����������� ������";i:3;s:13:"��������/~h~/";i:4;s:11:"������/~m~/";i:5;s:12:"�������/~l~/";}', 'shop', 4, 0, 11, 0, 0, 0, 'hits', 'desc', 0, 0, '123 HOUR', 0, 0, 4, 5, 1, 0, 0, 23, 1, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__uc_discount` (`id`, `title`, `cat_id`, `sign`, `value`, `unit`, `if_limit`) VALUES
(2, '�������� �������', 0, 2, 200, '���.', 0),
(3, '������ �� ����.������', 1, -1, 15, '%', 0);

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
  FULLTEXT KEY `title` (`title`,`fieldsdata`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__uc_items` (`id`, `category_id`, `title`, `pubdate`, `published`, `imageurl`, `fieldsdata`, `hits`, `is_comments`, `tags`, `rating`, `meta_desc`, `meta_keys`, `price`, `canmany`, `user_id`, `on_moderate`) VALUES
(1, 1, '���������', '2008-06-03 13:38:55', 1, 'b00117f6bca1efaaef37b44da87c1100.jpg', 'a:4:{i:0;s:7:"3130070";i:1;s:7:"�������";i:2;s:7:"191 ��.";i:3;s:64:"����� ��� ���������: 12 ������� ���������� � ��������� ���������";}', 29, 1, '�������, ���������', 0, '', '', 14.6, 1, 0, 0),
(2, 1, '����� ��� ���������', '2008-06-03 13:40:37', 1, 'b21ddffd1e9fe4716f5d1496c4e74400.jpg', 'a:5:{i:0;s:10:"3170050PK2";i:1;s:34:"�������, �������, �����, ���������";i:2;s:6:"84 ��.";i:3;s:65:"8 �������� ����������, 8 ������ � ��������, � ����������� �������";i:4;s:19:"11.00, 12.55, 13.02";}', 39, 1, '�����, ������', 0, '', '', 24, 1, 0, 0),
(4, 1, '����� ��� ������', '2008-06-03 13:55:00', 1, '30c14e99b4853a07e8718a176a5cbb3c.jpg', 'a:6:{i:0;s:10:"600010xPK2";i:1;s:39:"�������, �������, �����, ������, ������";i:2;s:7:"280 ��.";i:3;s:43:"<p>����� ��� ������ � ��������� �������</p>";i:4;s:0:"";i:5;s:15:"/images/rss.png";}', 113, 1, '�����, ������', 0, '', '', 18.25, 1, 0, 0),
(5, 2, 'Toyota Estima', '2008-06-03 13:47:00', 1, '96bd390df9222bdc684ceec8afc94ec3.jpg', 'a:4:{i:0;s:7:"�������";i:1;s:9:"2,4 �����";i:2;s:4:"2000";i:3;s:1056:"<p>������ ��������� Toyota Estima, ����������� �� ����� � 2000 ����, �������� ����� ���������. �������������� ��������� ������ ���������� �� ��������� ������������ �������� ��������. � ������������ Estima ������ ��� �������� ����������: ����� 4-����������� ������ ��������� ������� 2.4 ����� � �������� ����������������� DOHC � 3-�������� 6-����������� V-�������� ��������� � ��� �� �������� �����������������. Estima ���������� ������ �������� � �������� ��������� ��������.</p>\r\n<p>�������������� ���������� ������� ���������� ����������� � ����������� ������� ������ ������ � �������� �������� �����. ������ � ������ ���������� ����� ������ �� ��������� � ���������� ����������, �� ���� �������� ���� ����������� �� 40 ��.</p>\r\n<p>��� ����� Estima �������� ���������� &laquo;easy closer&raquo;, ������ ����� ����������, ������� ����� �������. � �������� ������������ ����� ������������ �������� ��������������� �������� � ��������. ������ ��������� Estima � ������ ������ ����������� ���������� ������ ������� � ������ �� ������ &laquo;2-3-3&raquo;.</p>";}', 8, 1, '����, �������, ������', 0, '', '', 0, 1, 0, 0),
(6, 2, 'Mitsubishi Eterna!', '2008-06-03 10:54:00', 1, '7afbfacf9a4c4a9d64e0da2b31b880e5.jpg', 'a:4:{i:0;s:5:"�����";i:1;s:9:"1.8 �����";i:2;s:4:"1992";i:3;s:0:"";}', 11, 1, '', 0, '��������', '�������� �����', 0, 1, 0, 0),
(7, 2, 'Subaru Domingo', '2008-06-03 13:51:24', 1, 'db0297daef1de808feed34a75b5ea49b.jpg', 'a:4:{i:0;s:7:"�������";i:1;s:9:"1.2 �����";i:2;s:4:"1991";i:3;s:0:"";}', 31, 1, '����, �������', 0, '', '', 0, 1, 0, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `#__upload_images`;
CREATE TABLE `#__upload_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `fileurl` varchar(250) NOT NULL,
  `target` varchar(25) NOT NULL DEFAULT 'forum',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__users` (`id`, `group_id`, `login`, `nickname`, `password`, `email`, `icq`, `regdate`, `logdate`, `birthdate`, `is_locked`, `is_deleted`, `rating`, `points`, `last_ip`, `status`, `status_date`) VALUES
(1, 2, 'admin', '�������������', '2ca41752ccf4dbdb76d8fe88c488fd44', 'admin@cms.ru', '100200300', '2007-11-23 12:41:57', '2009-11-16 12:06:31', '1980-01-01', 0, 0, 10, 0, '127.0.0.1', 'I love InstantCMS', '2009-08-29 14:08:32'),
(21, 1, 'vasya', '�������', '2ca41752ccf4dbdb76d8fe88c488fd44', 'vasya@cms.ru', '100200300', '2008-07-16 16:31:48', '2009-11-13 15:30:12', '1980-01-01', 0, 0, 0, 0, '127.0.0.1', '', '2009-11-13 14:13:51');

DROP TABLE IF EXISTS `#__users_activate`;
CREATE TABLE `#__users_activate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pubdate` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_autoawards` (`id`, `title`, `description`, `imageurl`, `p_comment`, `p_blog`, `p_forum`, `p_photo`, `p_privphoto`, `p_content`, `p_karma`, `published`) VALUES
(1, '������', '�������� ���� ������', 'aw.gif', 0, 0, 100, 0, 0, 0, 0, 1),
(2, '�������', '�������� ����������� �����', 'aw4.gif', 100, 5, 50, 0, 0, 0, 0, 1);

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
  PRIMARY KEY (`id`)
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
  PRIMARY KEY (`id`)
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
(1, '������������', 'registered', 0, 'comments/add, comments/delete, content/add, board/autoadd'),
(2, '��������������', 'admin', 1, 'admin/content, admin/com_rssfeed, admin/com_arhive, admin/com_banners, admin/com_blog, admin/com_faq, admin/com_board, admin/com_content, admin/com_clubs, admin/com_comments, admin/com_forms, admin/com_photos'),
(8, '�����', 'guest', 0, 'comments/add'),
(7, '���������', 'editors', 0, 'comments/add, comments/delete, content/add, content/autoadd, content/delete'),
(9, '����������', 'moderators', 0, 'comments/add, comments/delete, comments/moderate, forum/moderate, content/add');

DROP TABLE IF EXISTS `#__user_karma`;
CREATE TABLE `#__user_karma` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `points` smallint(6) NOT NULL,
  `senddate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_karma` (`id`, `user_id`, `sender_id`, `points`, `senddate`) VALUES
(1, 1, 27, 1, '2009-11-10 09:49:35');

DROP TABLE IF EXISTS `#__user_msg`;
CREATE TABLE `#__user_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to_id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `senddate` datetime NOT NULL,
  `is_new` int(11) NOT NULL DEFAULT '1',
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `#__user_photos`;
CREATE TABLE `#__user_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `pubdate` date NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `allow_who` varchar(15) NOT NULL DEFAULT 'all',
  `hits` int(11) NOT NULL,
  `imageurl` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_photos` (`id`, `user_id`, `pubdate`, `title`, `description`, `allow_who`, `hits`, `imageurl`) VALUES
(1, 27, '2009-10-15', 'Florida', '', 'all', 2, 'cdf10c6b3c70a828c5f224861702a1ed.jpg');

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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_profiles` (`id`, `user_id`, `city`, `description`, `showmail`, `showbirth`, `showicq`, `karma`, `imageurl`, `allow_who`, `signature`, `gender`, `formsdata`, `email_newmsg`, `cm_subscribe`, `stats`) VALUES
(1, 1, '������', '�.��������, �.������, �.�����, ���� ����, � ������ ������. � ������, ��� �������.', 1, 1, 1, 1, 'nopic.jpg', 'all', '', 'm', '---\n22: �.��������, �.������, �.�����\n24: ������\n', 1, 'none', '---\ncount: \n  comments: 1\n  forum: 1\n  photos: 0\n  board: 2\n  files_public: 0\n  files_private: 0\nrating: 10\n'),
(21, 21, '������', '��������, &#8217;������&#8217;, ������ "�������" ������', 0, 0, 1, 0, 'nopic.jpg', 'all', '', 'm', '---\n22: |\n  &#8217;������&#8217;, ������ &quot;�������&quot; ������\n24: ������\n', 1, '0', '---\ncount: \n  comments: 1\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n');

DROP TABLE IF EXISTS `#__user_wall`;
CREATE TABLE `#__user_wall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `pubdate` datetime NOT NULL,
  `content` text NOT NULL,
  `usertype` varchar(8) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
