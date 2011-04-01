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

INSERT INTO `#__actions_log` (`id`, `action_id`, `pubdate`, `user_id`, `object`, `object_url`, `object_id`, `target`, `target_url`, `target_id`, `description`, `is_friends_only`, `is_users_only`) VALUES
(2, 11, '2010-11-13 23:09:34', 2, '', '', 0, '', '', 0, 'I love InstantCMS', 0, 0),
(3, 2, '2010-11-13 23:11:00', 2, '�����������', '/stati/marketing/yelastichnost-sprosa.html#c13', 13, '������������ ������', '/stati/marketing/yelastichnost-sprosa.html', 0, '������� ������ �� ����� �������� ������!', 0, 0),
(4, 27, '2010-11-13 23:13:37', 1, '2', '', 0, '��� ��������', '/users/admin/photos/private5.html', 5, ' <a href="/users/1/photo6.html" class="act_photo">\r\n											<img border="0" src="/images/users/photos/small/b22c5c0f95c1fb9398578fd5e396c7dd.jpg" />\r\n									</a>  <a href="/users/1/photo7.html" class="act_photo">\r\n											<img border="0" src="/images/users/photos/small/efe8d13779cd84cfeb319d9f0875a511.jpg" />\r\n									</a> ', 0, 0),
(5, 20, '2010-11-13 23:18:39', 3, '�������������', 'http://icms/users/admin', 11, '', '', 0, '', 0, 0),
(6, 2, '2010-11-13 23:19:19', 3, '�����������', '/stati/marketing/yelastichnost-sprosa.html#c14', 14, '������������ ������', '/stati/marketing/yelastichnost-sprosa.html', 0, '��������� �������� � ����, �������.', 0, 0),
(7, 15, '2010-11-13 23:21:43', 3, '���� ��������� InstantCMS', '/clubs/14', 14, '', '', 0, '', 0, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__blogs` (`id`, `user_id`, `title`, `pubdate`, `allow_who`, `view_type`, `showcats`, `ownertype`, `premod`, `forall`, `owner`, `seolink`, `rating`) VALUES
(1, 1, '������ ����� ������', '2008-06-03 13:26:55', 'all', 'list', 1, 'single', 0, 1, 'user', 'primer-bloga-admina', 2),
(24, 14, '����', '2010-10-20 00:02:41', 'all', 'list', 1, 'multi', 0, 0, 'club', 'instantcms-lovers', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__blog_posts` (`id`, `user_id`, `cat_id`, `blog_id`, `pubdate`, `title`, `feel`, `music`, `content`, `content_html`, `allow_who`, `edit_times`, `edit_date`, `published`, `seolink`, `comments`) VALUES
(5, 1, 5, 1, '2009-09-07 11:50:16', '������ ������ � �����', '', '', '[b]����� InstantCMS - ��� ������ � �������� ���������� ��� ����� ����������:[/b]\r\n\r\n- �������� BB-���� (������� �����, ������, ������, ��������);\r\n- ������� ����������� "�� ����";\r\n- �������������� ����� ������;\r\n- ����������� � �������;\r\n- ����������� ������� ������������ ������;\r\n- ������ � ����;', '<b>����� InstantCMS - ��� ������ � �������� ���������� ��� ����� ����������:</b><br />\r\n<br />\r\n- �������� BB-���� (������� �����, ������, ������, ��������);<br />\r\n- ������� ����������� "�� ����";<br />\r\n- �������������� ����� ������;<br />\r\n- ����������� � �������;<br />\r\n- ����������� ������� ������������ ������;<br />\r\n- ������ � ����;', '0', 3, '2009-09-07 11:50:16', 1, 'primer-zapisi-v-bloge', 1);

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
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  KEY `obtype` (`obtype`),
  KEY `city` (`city`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__board_items` (`id`, `category_id`, `user_id`, `obtype`, `title`, `content`, `city`, `pubdate`, `pubdays`, `published`, `file`, `hits`) VALUES
(4, 10, 1, '���������', '��������� ���� ������', '���� �������� ����� � ���� ���.', '������', '2009-10-26 14:11:18', 10, 1, '550de8a5de9b5301133a815de31be00d.jpg', 7),
(5, 9, 1, '�������', '������� ���-2107 �� Nissan Skyline GTR', '���������� ����� � ��� �������.', '������', '2009-10-26 14:14:24', 10, 1, '931f90c50adcea1ff18177bc22d4ceac.jpg', 34),
(6, 8, 2, '����', '���� 2-� ��������� ��������', '����� �� 2-3 �������', '������', '2010-10-28 15:57:22', 10, 1, '80204e6bad519060bca9d456949158dc.jpg', 2);

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
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  UNIQUE KEY `seolink` (`seolink`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__category` (`id`, `parent_id`, `title`, `description`, `published`, `showdate`, `showcomm`, `orderby`, `orderto`, `modgrp_id`, `NSLeft`, `NSRight`, `NSLevel`, `NSDiffer`, `NSIgnore`, `ordering`, `maxcols`, `showtags`, `showrss`, `showdesc`, `is_public`, `photoalbum`, `seolink`, `url`, `tpl`) VALUES
(1, 0, '--�������� ������--', '�������� ������ �����', 1, 1, 1, 'pubdate', 'asc', 0, 1, 14, 0, '', 0, 1, 1, 1, 1, 0, 0, '', '--kornevoi-razdel--', '', 'com_content_view.tpl'),
(2, 1, '�������', '', 1, 1, 1, 'pubdate', 'ASC', 0, 12, 13, 1, '', 0, 2, 1, 1, 0, 0, 0, 'a:7:{s:2:"id";i:0;s:6:"titles";i:0;s:6:"header";s:0:"";s:7:"orderby";s:5:"title";s:7:"orderto";s:4:"desc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'novosti', '', 'com_content_view.tpl'),
(6, 1, '������', '<p>������ ������ ������������� ������� <a href="http://referats.yandex.ru/">������.��������</a></p>', 1, 1, 1, 'pubdate', 'ASC', 0, 2, 11, 1, '', 0, 1, 1, 1, 1, 1, 1, 'a:7:{s:2:"id";i:0;s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:4:"hits";s:7:"orderto";s:3:"asc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati', '', 'com_content_view.tpl'),
(13, 6, '���������', '<p>������ �� ����������</p>', 1, 1, 1, 'pubdate', 'DESC', 0, 7, 8, 2, '', 0, 3, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";i:0;s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati/marketing', '', 'com_content_view.tpl'),
(12, 6, '��������', '������ �� ��������', 1, 1, 1, 'pubdate', 'DESC', 0, 3, 4, 2, '', 0, 1, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";s:1:"0";s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";s:1:"2";s:3:"max";s:1:"8";}', 'stati/geologija', '', 'com_content_view.tpl'),
(11, 6, '����������', '<p>������ �� ����������</p>', 1, 1, 1, 'pubdate', 'DESC', 0, 9, 10, 2, '', 0, 4, 1, 1, 1, 1, 1, 'a:7:{s:2:"id";i:0;s:6:"titles";i:0;s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";i:2;s:3:"max";i:8;}', 'stati/astronomija', '', 'com_content_view.tpl'),
(14, 6, '����������', '������ �� ����������', 1, 1, 1, 'pubdate', 'DESC', 0, 5, 6, 2, '', 0, 2, 1, 1, 1, 1, 0, 'a:7:{s:2:"id";s:1:"0";s:6:"titles";s:1:"0";s:6:"header";s:18:"���������� �� ����";s:7:"orderby";s:7:"pubdate";s:7:"orderto";s:4:"desc";s:7:"maxcols";s:1:"2";s:3:"max";s:1:"8";}', 'stati/psihologija', '', 'com_content_view.tpl');

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
  PRIMARY KEY (`id`),
  KEY `pubdate` (`pubdate`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__clubs` (`id`, `admin_id`, `title`, `description`, `imageurl`, `pubdate`, `clubtype`, `published`, `maxsize`, `enabled_blogs`, `enabled_photos`, `rating`, `photo_premod`, `blog_premod`, `blog_min_karma`, `photo_min_karma`, `album_min_karma`, `join_min_karma`, `join_karma_limit`) VALUES
(14, 1, '���� ��������� InstantCMS', '', '', '2010-10-12 14:41:45', 'public', 1, 0, 1, 1, 0, 0, 0, 0, 0, 25, 0, 0);

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
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__comments` (`id`, `parent_id`, `pid`, `user_id`, `target`, `target_id`, `guestname`, `content`, `pubdate`, `published`, `is_new`, `target_title`, `target_link`, `ip`) VALUES
(8, 0, 0, 1, 'article', 32, '', '������� ������� �������� ����������, �������� �� �� ����� ������������ ������� � ��������� ��������� ������, ���������� ������, ��� ��� ���������� ���������� ��������� �� �������������', '2010-10-13 23:45:56', 1, 1, '������ ������� �����?!!!', '/content/stati/geologija/pochemu-nerezko-plato.html', ''),
(13, 0, 0, 2, 'article', 34, '', '������� ������ �� ����� �������� ������!', '2010-11-13 23:11:00', 1, 1, '������������ ������', '/stati/marketing/yelastichnost-sprosa.html', '127.0.0.1'),
(14, 13, 0, 3, 'article', 34, '', '��������� �������� � ����, �������.', '2010-11-13 23:19:19', 1, 1, '������������ ������', '/stati/marketing/yelastichnost-sprosa.html', '127.0.0.1');

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
(2, '�����������', 'registration', '---\nreg_type: open\ninv_count: 3\ninv_karma: 1\ninv_period: WEEK\nis_on: 1\nact: 0\nsend: false\noffmsg: >\n  ����������� �������������� ��\n  ����������� ��������.\nfirst_auth_redirect: profile\nauth_redirect: profile\nname_mode: nickname\nbadnickname: |\n  �������������\n  �����\n  qwert\n  qwerty\n  123\n  admin\n  ���� ������\nask_icq: 1\nask_birthdate: 1\nsend_greetmsg: 1\ngreetmsg: |\n  <h2>������!</h2>\n  <p><span style="font-size: medium;">�� ����� <span style="color: rgb(51, 153, 102);">����</span> ��� �� �����������������!</span></p>\n', 0, 'InstantCMS team', 1, '1.5', 1),
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
  KEY `category_id` (`category_id`),
  KEY `user_id` (`user_id`),
  UNIQUE KEY `seolink` (`seolink`),
  FULLTEXT KEY `title` (`title`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__content` (`id`, `category_id`, `user_id`, `pubdate`, `enddate`, `is_end`, `title`, `description`, `content`, `published`, `hits`, `meta_desc`, `meta_keys`, `showtitle`, `showdate`, `showlatest`, `showpath`, `ordering`, `comments`, `is_arhive`, `seolink`, `canrate`, `pagetitle`, `url`, `tpl`) VALUES
(20, 2, 1, '2009-03-01 15:56:00', '2009-05-22', 0, '��� ���� ������!', '<p>�� ���� �������������� ��� �� ����� �����!</p>', '<p>��� ���� ������ � �������� ������� ��������������.</p>\r\n<p>� ��� ������� ����� �� �������. �� ���� ����� �� ����������� ���������� ���������� ����������.</p>\r\n<p>�� ����� ���� ��� �� ������� ��� �������.</p>\r\n<p>���� �������� ���������� �������� � ����� ������� ������������ ����������.</p>', 1, 397, '', '', 1, 1, 1, 1, 1, 1, 0, 'novosti/nash-sait-otkryt', 1, '', '', 'com_content_read.tpl'),
(26, 2, 1, '2009-03-01 15:56:00', '2009-05-22', 0, '�������� ������� �����', '<p>��� �������� �������. ������������ ����� �� ��������������.</p>', '<p>������� ����� ������������� ��������� ����������, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. ������, �������� ���������� ������������� ����, ����������� ����������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ����� ������������ ��������� ����, � ������������ � ����������� � ��������� �������������. ����� �������� ������, �� ���� ���� ������������� �������� ���� ��� ������� ��������. ���������� ����� �������� ����, ��� � ����� ������ �������� � ������� ���������� ������ ��� ��������� ������������ ����. ��� ��������� �������� ����� ��������.</p>', 1, 279, '', '', 1, 1, 1, 1, 2, 1, 0, 'novosti/testovaja-novost-saita', 1, '', '', 'com_content_read.tpl'),
(30, 11, 1, '0000-00-00 00:00:00', '2009-06-17', 0, '�������������� ������� ��������', '<p><strong>��� ��������� ��������� ����� ������ ��������</strong>, ��� ���� ����������� ��� ������ ����� &ndash; � ����� �������� ������ ����� ������������ � ���������, ��� �� ��� ������ ������� �����������. ��� ���� �������� ����, ����������� ����p�� ���� p������������ ���������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����. �p������������ ������ ����������� ����������� ������� ���� (��������� ��������� �� ���������, ����, �����).</p>', '<p>����� ���� ���� � ����������, ������� �����-��������� �������� �������� ���������, ���� ��������� � ��������� ������� ����� ������� ����������. ���� �p������� �� ���p���, �� �������, ��� ����������� ��� ����� ������� �������������� ��������, ���� ���� �������� �� �������� ������ ����� ������ ������, ������� � ������ ���������� �������������.<br />\r\n<br />\r\n������� �� ��������� ���, ����� ���� ������ �������� � ������ ������� ���� ����� � ������ (��� ����� ����� � �������� ������� ������), ����� ��������� ����������, ���� ��������� � ��������� ������� ����� ������� ����������. � ������� �� �������� � ������� �������, ��������� ���� ���������. ������ ����������� �� ��������� ���, ����� ���� ������ �������� � ������ ������� ���� ����� � ������ (��� ����� ����� � �������� ������� ������), ������������. ������ ����������� ����������� ����������� �����, ��� �� �����, ��� ����� ������� � ������ ����� 82-� ������� ������. ��������� �������, ������ ���������� ������ ������ ������, �������� ������� p������������ ���������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����.<br />\r\n<br />\r\n���p���� ������ � ��p������ ������������ ��������, � ������� �������������� ����������� ������ ��������� ������� ��������� �������: M��.= 2,5lg D�� + 2,5lg ����� + 4. ���������� ��������������� �����������. ��� ����� �������� ��������� �������: V = 29.8 * sqrt(2/r &ndash; 1/a) ��/���, ��� ������ ����� ��������� ������������ �������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����. ������ ������. ��������� ���� ����������.</p>', 1, 68, '', '', 1, 1, 1, 1, 1, 1, 0, 'stati/astronomija/pervonachalnyi-nulevoi-meridian', 1, '�������������� ������� ��������', '', 'com_content_read.tpl'),
(31, 12, 1, '2009-05-20 16:24:00', '2009-05-20', 0, '���������� ��������: �������� �������', '<p>������� ���������������, � ������ ������������ ��������, ��������� ��������������, � �� ����� ��� �������� ���������� ���������� � ������� ��������. ����� ���������� ���������� ���������� �����, � ������������ � ����������� � ��������� �������������. ��������� ���������� ��������� �����������, ������� �� ������� � �������-���������� ��������� ������������ ������� ��������. ������� ����� �������������, ����������� ������ ����������������� ������ ���������� �����, ���������� � ���� ������������, ��� � ����� ��������������� � ������������ ������������� ��������� � ��� �����.</p>', '<p>���������� ����� ��������� ���������� ����, ��������� ��������������� ��������� ����� �� �����������. ����������� �������� ����������. ����������� ����������� ���������� ������������� ����, ��� ��������������� � ������������� ����������� ����� � ������� ����. ����� ����������� ������������� ������������-�������������� ������, ��� � ����� ������ �������� � ������� ���������� ������ ��� ��������� ������������ ����. ������ ��������� ��������������� ������-�����, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����.</p>\r\n<p>��������� ����������� ��������� ������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ������������� �������� �������, ��������� ���������� �������� �������� �������� ��������������� � ��������������� �������������. ����, ��� ��, ��� � � ������ ��������, �������������. ������� �������� ���������������� ������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ���� ����������� ���������� ������-����������� ��������, ��������� ���������� �������� �������� �������� ��������������� � ��������������� �������������. � �������������� ����� ��� ���������� ������������ ������������ ��������� ����, ������� �� ������� � �������-���������� ��������� ������������ ������� ��������.</p>', 1, 66, '���������� ��������: �������� �������', '���������������, ���������, �����, ������������, �������, ����������, ������, ��������, ���������������, ���������������, �������������, ��������, ����������, ������, ����������, �����������, ���������, ��������, ���������� ������������, ������� �����, ��������� �������, ��������������� ���������, ������������ ���������������, �������� ���������������, ��������������� �������������, �������� ��������, ��������� ����������, ���������� ��������, �������� ��������, ���������� ������������ ���������������, ��������������� ��������� �������, ������������ ��������������� ���������, �������� �������� ���������������, �������� �������� ��������, ��������� ���������� ��������, ���������� �������� ��������', 1, 1, 1, 1, 2, 1, 0, 'stati/geologija/ostancovyi-lakkolit-osnovnye-momenty', 1, '', '', 'com_content_read.tpl'),
(32, 12, 1, '2009-05-20 16:24:00', '2009-06-17', 0, '������ ������� �����?!!!', '<p>��������������� ����� �������� ����� �����������, ���, ������, �� ���������� ������������ ��������������� ��������� ������� �����. ������������� �������������� ������������ ������� ������, ��� � ����� ��������������� � ������������ ������������� ��������� � ��� �����. ����������� �����������, ������� � ��������� ����� ��������� ���� ������ ����, ��������� �������� ����, � �� ����� ��� �������� ���������� ���������� � ������� ��������. ������� ������� ��������, ����� ���� �������������� ������ ������������� ��������� ��������� ���������-������������� ������������� ��������� �������.</p>', '<p>���������� ��������� ����������� ��, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. �������� �������������� ��������� ����, ������� ��������� �����, ��� � ����� ������ �������� � ������� ���������� ������ ��� ��������� ������������ ����. ������������� ������ �������� ������ ��������, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. �������� ������ ����������� ������-��������� ����� ������, � �� ����� ��� �������� ���������� ���������� � ������� ��������. ��������������� ����� ���������� ��������� ���������� ����, � ������������ � ����������� � ��������� �������������. ���������� �����, �� ���� ������� ��� �������� ��������� ���������, ����������� ������ ����������, ������� � ����� �������, ��������� � ��.<br />\r\n<br />\r\n�� ��������� ������� ���������� ����������������� ��������� ������� ��������, ��� ����������� �� ����������-������������� �����������, ������������������ ��������� � ��������-���������������� �������� �����. ���������� �����, �� ���� ������� ��� �������� ��������� ���������, ��������� ���������� ����������������� �����, ��� ����������� �� ������ ���������� ������������ ���������-�������������� ������� ����������� ��������������� �����, �� � ������������ ����� ������� �������� ���������. ������� ������� �������� ����������, �������� �� �� ����� ������������ ������� � ��������� ��������� ������, ���������� ������, ��� ��� ���������� ���������� ��������� �� ������������� � ���� � ������ ���������� ������������ ����������� ������������. ������� ���������� ������ �������� ����������, � � �� �� ����� ��������������� ���������� ����������� ��� ������� ���� �������� ������.</p>', 1, 21, '', '', 1, 1, 1, 1, 1, 1, 0, 'stati/geologija/pochemu-nerezko-plato', 1, '������ ������� �����?!!!', '', 'com_content_read.tpl'),
(33, 13, 1, '2009-05-20 15:50:00', '2010-10-28', 0, '���������� �������� �����: ����������� � �����������', '<p>������������� ��������� ������������� �������������� conversion rate, �� �������� � ���������. ��������� ������ �������� PR, �� �������� � ���������. VIP-�����������, ���������� ���������� ��������� ��������, ��������� �������� PR-������, �� �������� � ���������. ��� �������� ����� ������, �������������� ���������� ������������ �������������� �������������� �����, �� �������� � ���������. ����, ����, ��� ��������� ���� ��������� ������� �� �����.</p>', '<p>����� ��������, ��� ����������� ������������� ����������� ������ ��������, ��������� �� ������������ ����������. ����-�����, ���������� �����������, ��������� �������������� ���-���, ����������� �������. � ��� �� ������ ���������� ����������������� �������� ������������ ��������� �����, �������� �� ���� �������� ������. ����-�����, ����������, ��� ��� ��������� ��� ������. ����� ��������� �������� �������� � �� ����, ��� ���� ����� �������� ����� �������.</p>\r\n<p>� ���� �� �������������� ������������� �������� ����������� ��������������� ������������� ������, �������� �� �������� �����������. ������ �������� ��� ����������� ������� ������������ ����������� ���������� � ������, ������� ��������� ��� ����� ������������. ��������� � ��������,&nbsp;�������������, ������������� ���������� ����������, �������� �� ���� �������� ������. ��������� ������, ����������� ��������, ����������� ������������� ������� ������, ��������� ���� ���������� ��������. �������� ���������� �������� ��������������� �����, ����������� �������.</p>', 1, 10, '���������� �������� �����: ����������� � �����������', '��������, ������, ���������������, ����������, ��������, �������, ��������, �����, �����������, ������, �������� ������, ����������� �������', 1, 1, 1, 1, 2, 1, 0, 'stati/marketing/kulturnyi-mediinyi-kanal-metodologija-i-osobenosti', 1, '', '', 'com_content_read.tpl'),
(34, 13, 1, '2009-05-20 11:33:00', '2009-07-22', 0, '������������ ������', '<p>���� � ���, ��� ����������� �������������� ����������������� ��������� �����, ��������� �� ������������ ����������. ������� ��������� �������������. ��� �� �����, ������ ����� ����� ������������ ������������ ������ ����������� �����, �������� ���� �����.</p>', '<p>��������� ��������� �������������� ������������,&nbsp;�&nbsp;������&nbsp;�����������&nbsp;���������, �������� ����������� ���� ����������, ����������� �������. ��������� ����� ������������� ������������� ������������ ���������, ����������� �������. ����������� ������������� ���-���, ��������� �� ������������ ����������.</p>\r\n<p>�������� ������ �������� ������ �����������, ��������� ���� ���������� ��������. �������������� ����� ��������� ���������. �������������� ���������� ���������� ����������� ���������, ��������� ���� ���������� ��������. ��������� ���������� ������������� ����������� �����, �������� �� ���� �������� ������. �������������� �������� ���� ������������� ��������������� ���������� ����������, �������� ��������� ���������� �����-��������. �� ����, ��������� ��������� ����������� ����������������� ����������� �����, �� �������� � ���������.</p>\r\n<p>�� ������ ������� ������������, ������������� ���������� ��������� ������������� ��������� ����, ��������� ������������ �������� ���������. ��������� �������� ������������. ������, ����������� ��������, ��� �������� � ��������� ������������� ��������� ����������. ������������� ������ ������������� ����������������� �������������� �������� ����, �� �������� � ���������. ������ � ����, ��������� ������ ����� ������������� �������, �������� �� ���� �������� ������.</p>\r\n<p>&nbsp;</p>', 1, 9, '������ �������� �� ��� ���� ������������ ������?', '��������, ����������, ������, ��������, ��������, ��������������, ���������, ��������, �����, ��������, �������������, �������������, �������, ���������, ��������������, �����������, ���������, �����������, �������������� ��������, �������� ������, ����������� �����, ���������� ��������, ����������� �������', 1, 1, 1, 1, 1, 1, 0, 'stati/marketing/yelastichnost-sprosa', 1, '', '', 'com_content_read.tpl'),
(29, 11, 1, '2009-05-20 13:41:00', '2009-07-21', 0, '����������� ����������� ������: �������� � ������', '<p>������� �������� ����������� ��������������, ��� ���� ��������� ��������� � 3 * 10 � 18-� ������� ��� ������, � ������ ��������� ����������� ������� ������� �����. ����������, ����� ����������� �������, ����� ��������� �������� ��������� &ndash; ��� ������ ���������, ��� �������. ���� ���� ����, ������ ����������� ��������� �������� ������ ����� ������ � �� �� �������, � ����� ��������� �������. P������������ ��������� ���������� �������� ������� ����, ����� �������, ������� ������ ������ ����� ����������� �� �������� ����� 1666��. ������� �����-���������, �������� ����� ����������� ������������� ������, ����������� �������� ����, ��� ��� ��������� � 1994 ���� � ������� �������p��-���� 9. �������, � ��� ������������� ����� ���� ����� ������, � ��� ��������������� ������� ������������.</p>', '<p>��������� �����, ��� ������� ���������� �� ��������� �������, ���������. �������� ������� ����� ��������. ����������� ����p�� ��������. ��������� ���� �������� ������������ �����, ���� ��� ���� ����� �� �����p��������� ���������, ���������� � ������� 1.2-���p����� ���������. �������� ��������� ������ ���������, �� ��� �� ����� ���� �������� ������������ �������. ������ ������ ������������� ����� � ������, ������ ������ ������������ ������ �����, ������ ����������� ��������� �������� ������ ����� ������ � �� �� �������, � ����� ��������� �������.</p>\r\n<p>{pagebreak}</p>\r\n<p>������ ������������� ������������� ������, ����� �������, ������� ������ ������ ����� ����������� �� �������� ����� 1666��. ����������� ��������, ��� �� ��� �� �������� ��������������, ��������� ����������� �������� - ��� ��������� �������� ���������� ������� ����� ���������. ��������� ���� �������� ����� ������� ����, ���� ��� ���� ����� �� �����p��������� ���������, ���������� � ������� 1.2-���p����� ���������. �����������, ��� �� ��� �� �������� ��������������, �������� ��������� &ndash; ��� ������ ���������, ��� �������. ������� �����, � ������ �����������, ���������. �������� �����, �������p�� � ����p������ ��p� ������, �������� ����������� ����������� �������, �� ���� � �������� ������� ������� ����������� �������������� NASA.</p>', 1, 99, '����������� ����������� ������: �������� � ������', '�����������, ������, ���������, ��������, ��������, ��������, ��������������, ���������, ���p�����, �����, ������������, ���������, �����p���������, ���������, �������, ����������, �����, �������� ��������������, ����������� ��������, ���p����� ���������, ��������� ����������, �����p��������� ���������, �����p��������� ��������� ����������', 1, 1, 1, 1, 2, 1, 0, 'stati/astronomija/kosmicheskii-vozmushayushii-faktor-gipoteza-i-teori', 1, '', '', 'com_content_read.tpl'),
(35, 14, 1, '2009-05-20 16:24:00', '2009-05-20', 0, '������������ ��������: ����������� � �����������', '', '<p>������� ������������ ������, ������������� ��������� � ����������� ������� � ����� ������ �����������.</p>\r\n<p>������� ��������. �������� ������������ ����� ������, ��� �� ����� ��� ������ ���������������� ������������ �����������, ���� ��� ��������� ������� ����� �������. ��������, � ������ �����������, ������������ �����������, ����� ����������� �������������� ������� ��� �������� ������ ������ ��������. ������� �����������. ���������, �������, ���� ����������� ������, ��� �������� ����� ���������� ������ ��� �����, �����, ���, �������, �����. ����� ��������� ����������� ������������ ������ � ���� �������� ��������� ������������ � �����������, ��������� ���� ���������� ���������� �� �������� ����� �����.</p>\r\n<p>��������� ����������� ��������� ���� ��������� �� ���� ������������. ������� ��������� ������� ���, ��������� �������� ��������, ���� ���� ���� ��������� � ���������� �������� �����������. ����������, �c���� �� ����, ��� ������������ ��������������� �����������, ���, ��������, ������ ������� ��� ���������� ����������� ��������� ����������� ��������� ���������������. ���������, ��������, ������������. �������������, ��������, ����������� ��������������� ��������, � ��� � ����� �. ������ � ����� ������ &quot;��������� � ��������&quot;. �������, �������, ���������� ���� ���������� ������, ��� � ������������� ������ � ����������� ������.</p>', 1, 11, '������������ ��������: ����������� � �����������', '��������, ���������������, �����������, �������, �����, ��������, ������������', 1, 1, 1, 1, 1, 1, 0, 'stati/psihologija/yempiricheskii-kontrast-metodologija-i-osobennosti', 1, '', '', 'com_content_read.tpl'),
(36, 14, 1, '2009-05-20 18:29:00', '2009-09-16', 0, '��������� ����������� ������� �������������', '<p>�����������, ��� �� ��� �� �������� ��������������, ������������ ������, ����� ����������� �������������� ������� ��� �������� ������ ������ ��������. ����������� ����� ����� �� ����� ��� �����, ��� � ����� �������� �������� ������� ���, ��� �������� ����� ���������� ������ ��� �����, �����, ���, �������, �����.</p>', '<p><strong>�.�. ��������� ������� ��� ����, ��� ���������� ��������������� ����������� ��������������, ��� ���������� �� ������ ��� ��������������� ������ ���������, ������� �������������� �� ������ �������������. ������� �������� �������� ���������� ������������� ������ ��������� �� ���� ������������. �������, � ������������� ������, ����������� ���������� ������, ���� ������ ��� �������. ����������� ����� ����� �� ����� ��� �����, ��� � �������� �������� ������� ����������, ������������� ��������� � ����������� ������� � ����� ������ �����������. ��������, �� �����������, ��������.</strong><br />\r\n<br />\r\n������������ ���������������, ��������, �������� �������, ���, ��������, ������ ������� ��� ���������� ����������� ��������� ����������� ��������� ���������������. ����������� ������������� ��������������� ����������� �������������� ������, ��� ������ ��� ������������ ������� �. ������. ���������������� ������� �������� �����������, ��� ���������� �� ������ ��� ��������������� ������ ���������, ������� �������������� �� ������ �������������. ��������� ����������, ������������ �� ����������� ���������, ������ ������� ���������� ���, ���������������� ����������������� �������� �����������, ���������� �� ������������ ��������� ��������. ���������� ������� ������, ���� ���� ���� ��������� � ���������� �������� �����������. ������ �. �������� ���������, ��� �������� ������ ���� ��������, ���������� �� ������������ ��������� ��������.</p>\r\n<p>{�����=�������� ������}</p>', 1, 13, '��������� ����������� ������� �������������', '��������, �������������, ������, ��������, ����������, ��������, ���������, ������������, ��������������, �������, �����������, ���������������, ����������, ����������, ������, ���������, ������, ���������������, ���������, ������������ ���������, ��������� ��������, ������ �������������, ������� ��������������, ������ ���������, ��������� �������, ��������������� ������, ������������ ��������� ��������, ��������� ������� ��������������, ������ ��������� �������, ��������������� ������ ���������', 1, 1, 0, 1, 2, 0, 0, 'stati/psihologija/gruppovoi-autotrening-glazami-sovremennikov', 1, '�����������', '', 'com_content_read.tpl');

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

INSERT INTO `#__cron_jobs` (`id`, `job_name`, `job_interval`, `component`, `model_method`, `custom_file`, `is_enabled`, `is_new`, `comment`, `class_name`, `class_method`) VALUES
(1, 'photos_clear', 24, 'users', 'clearUploadedPhotos', '', 1, 1, '�������� �������������� ������ ����������', '', ''),
(2, 'optimize_tables', 24, '', '', '', 1, 1, '����������� ������ ��', 'db|cmsDatabase', 'optimizeTables'),
(3, 'drop_inactive_users', 48, 'users', 'deleteInactiveUsers', '', 1, 1, '�������� ���������� ������������� (��. ��������� ���������� "������� �������������")', '', ''),
(4, 'remove_old_log', 48, '', '', '', 1, 1, '������� ������ ������ ����� �������', 'actions|cmsActions', 'removeOldLog'),
(5, 'give_invites', 24, 'users', 'giveInvitesCron', '', 1, 1, '������ �������� �������������', '', ''),
(6, 'clear_invites', 24, 'users', 'clearInvites', '', 1, 1, '�������� �������������� ��������', '', '');

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
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__faq_quests` (`id`, `category_id`, `pubdate`, `published`, `quest`, `answer`, `user_id`, `answeruser_id`, `answerdate`, `hits`) VALUES
(3, 3, '2008-04-14 20:09:54', 1, '� ���� �� �������� ������. ����� � ������� � ���� ���� � ������ ����������, �� �������� �� ������������ �������. ����� ���� �������� ������, ������ ��������� ��������� � �������� ������������. ���� ��� �� ������� � �����.\r\n\r\n����������, ��� ��� ��� ��������?', '<p>�������, ���� �������� ����������� ����� �����. �, � ���������, ���� �� ��� ���-�� ����� ������. ������������, ��� �� ����� - ��� ������� �� ��� � �������. ��� ��� �������, ��� ��� ������.</p>', 2, 1, '2008-04-14 20:09:54', 32),
(4, 4, '2008-04-14 00:00:00', 1, '� ������ ���� ������. ����� � ���������. � ���������� �� �����. ������ ������� ������. �� ��������� ��������� ����. � �� ����� ���� ������ ������ � ���� ������ ������. ��� ��� ����?', '<p>������ ����� �� ������� ����� ���������� ���, ��� �� �����.</p>\r\n<p>�������� �������������, ����������� ����������� ������� � 1985 ����, ����������� ����� ����� ����� �� ���� ������, ������� ��� �������� &quot;�����&quot;. ����������� �������� ����� ������������ ��� ������. ������ �� ��� ���� ��������� ������ ������ �� ����.</p>\r\n<p>������� ��� � �������, ������ ���.</p>', 2, 1, '2008-04-14 00:00:00', 36),
(5, 5, '2008-04-14 00:00:00', 1, '������� ���������� ����� ��� ������ ���������� ������� � ���� �� ������ � ���� 1937 ����?', '<p>� ���������, �������������� �� ��� �������� ���������� ������� ����������� 94 ���� � �� �� ������ �������� �� ���� �������� ������. �� ���������� ��� �� ������, �� ������� �� ����� � ��������� �����, �� �� ��� ����������� �����. ������ ��������� � ����������.</p>', 2, 1, '2008-04-16 00:00:00', 36);

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
(24, 3, '�����������:', 2, 'list', 0, 'a:1:{s:5:"items";s:24:"������/�������/���������";}');

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
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forums` (`id`, `category_id`, `title`, `description`, `auth_group`, `ordering`, `published`, `parent_id`, `NSLeft`, `NSRight`, `NSDiffer`, `NSIgnore`, `NSLevel`) VALUES
(1000, 0, '-- ������ ������� --', '', 0, 1, 0, 0, 1, 8, '', 0, 1),
(1, 1, '����� �����', '', 0, 1, 1, 1000, 2, 7, '', 0, 2),
(1002, 1, '��������1', '', 0, 2, 1, 1, 5, 6, '', 0, 3),
(1004, 1, '��������2', '', 0, 1, 1, 1, 3, 4, '', 0, 3);

DROP TABLE IF EXISTS `#__forum_cats`;
CREATE TABLE `#__forum_cats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `published` int(11) NOT NULL DEFAULT '1',
  `auth_group` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_group` (`auth_group`)
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
  KEY `thread_id` (`thread_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forum_posts` (`id`, `thread_id`, `user_id`, `pubdate`, `editdate`, `edittimes`, `content`) VALUES
(29, 12, 1, '2009-04-04 18:54:53', '2010-10-07 18:07:14', 2, '������������� [b]��������� ������������� ��������������[/b], ��� � ����� ��������������� � ������������ ������������� ��������� � ��� �����. ����������� ����������. ������ �����������. ����������, ����������� ������ ����������������� ������ ���������� �����, \r\n\r\n��������������� ������� �����, ������� �� ������� � �������-���������� ��������� ������������ ������� ��������. ������� ���������� ������������� ������� - ��������� ��������� ��������, ��� ��� ���������� ���������� ��������� �� ������������� � ���� � ������ ���������� ������������ ����������� ������������.');

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__forum_threads` (`id`, `forum_id`, `user_id`, `title`, `description`, `icon`, `pubdate`, `hits`, `closed`, `pinned`, `is_hidden`, `rel_to`, `rel_id`) VALUES
(12, 1, 1, '������ ����', '', '', '2009-10-16 12:31:36', 71, 0, 0, 0, '', 0);

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
(1, 'root', '-- ������� �������� --', '-1', 'link', '-1', '_self', '', 1, 0, '0', '', '', 1, 34, 0, '', 0, 0),
(10, 'mainmenu', '�������', '/novosti', 'category', '2', '_self', '', 1, 1, '0', '', '01.gif', 2, 3, 1, '', 0, 1),
(13, 'mainmenu', 'Q&A', '/faq', 'component', 'faq', '_self', '', 6, 1, '0', '', '27.gif', 24, 25, 1, '', 0, 1),
(15, 'mainmenu', '����', '/users', 'component', 'users', '_self', '', 9, 1, '0', '', 'group.gif', 30, 31, 1, '', 0, 1),
(17, 'mainmenu', '�����', '/blogs', 'component', 'blogs', '_self', '', 4, 1, '0', '', 'blog.gif', 20, 21, 1, '', 0, 1),
(18, 'mainmenu', '�����', '/forum', 'component', 'forum', '_self', '', 10, 1, '0', '', '29.gif', 32, 33, 1, '', 0, 1),
(20, 'mainmenu', '����', '/photos', 'component', 'photos', '_self', '', 3, 1, '0', '', '20.gif', 14, 19, 1, '', 0, 1),
(21, 'mainmenu', '������', '/stati', 'category', '6', '_self', '', 2, 1, '0', '', '22.gif', 4, 13, 1, '', 0, 1),
(23, 'mainmenu', '�������', '/catalog', 'component', 'catalog', '_self', '', 7, 1, '0', '', 'objects048.gif', 26, 27, 1, '', 0, 1),
(44, 'mainmenu', '���������', '/stati/marketing', 'category', '13', '_self', '', 4, 1, '0', '', '', 11, 12, 2, '', 0, 21),
(37, 'mainmenu', '����������', '/board', 'component', 'board', '_self', '', 8, 1, '0', '', 'objects038.gif', 28, 29, 1, '', 0, 1),
(38, 'mainmenu', '�����', '/clubs', 'component', 'clubs', '_self', '', 5, 1, '0', '', '45.gif', 22, 23, 1, '', 0, 1),
(39, 'mainmenu', '����������', '/stati/astronomija', 'category', '11', '_self', '', 1, 1, '0', '', '', 5, 6, 2, '', 0, 21),
(40, 'mainmenu', '��������', '/stati/geologija', 'category', '12', '_self', '', 2, 1, '0', '', '', 7, 8, 2, '', 0, 21),
(41, 'mainmenu', '����������', '/stati/psihologija', 'category', '14', '_self', '', 3, 1, '0', '', '', 9, 10, 2, '', 0, 21),
(42, 'mainmenu', '����� ����', '/photos/latest.html', 'link', '/photos/latest.html', '_self', '', 5, 1, '0', '', '', 15, 16, 2, '', 0, 20),
(43, 'mainmenu', '������ ����', '/photos/top.html', 'link', '/photos/top.html', '_self', '', 6, 1, '0', '', '', 17, 18, 2, '', 0, 20);

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
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

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
(100, 0, 1, 1, 6, '', 0, 0, '-- �������� ������ --', '', 1, 1, '', '2008-05-30 12:03:07', 'title', 'asc', 0, 15, '', 96, 480, 1, 'list', 1, 0, 4, 1, 1, 1, 1, 0),
(1, 100, 6, 2, 3, '', 0, 1, '����� ������', '����� ������������������ ������������ ����� �������� ���� ���������� � ���� ������.', 1, 1, '', '2008-04-24 10:18:21', 'pubdate', 'asc', 2, 15, '', 96, 600, 1, 'lightbox', 1, 10, 5, 1, 1, 1, 1, 1),
(1034, 100, 8, 4, 5, '', 0, 1, '�������', '������ �������� �������', 1, 1, '', '2010-10-12 13:44:56', 'pubdate', 'desc', 2, 20, '', 96, 600, 1, 'lightbox', 1, 20, 4, 1, 1, 1, 1, 1),
(1035, 0, 1, 1, 2, 'club14', 0, 0, '-����: ���� ��������� InstantCMS-', '', 0, 1, '', '0000-00-00 00:00:00', 'title', 'asc', 0, 15, '', 96, 480, 1, 'list', 1, 0, 4, 0, 1, 1, 14, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__photo_files` (`id`, `album_id`, `title`, `description`, `pubdate`, `file`, `published`, `hits`, `showdate`, `comments`, `user_id`, `owner`) VALUES
(10, 1, '����� �� �����', '', '2009-08-31 18:26:43', 'd0633d5a84f03a27f1b7d0419947e968.jpg', 1, 25, 1, 1, 1, 'photos'),
(11, 1, '�������', '', '2009-08-31 18:27:33', '5e7a09ffcaa383df24d25d56c315f0d0.jpg', 1, 28, 1, 1, 1, 'photos'),
(15, 1034, '�������� ������', '', '2010-10-12 13:47:32', '312604de74e4de8aec59626ac024c7d3.jpg', 1, 6, 1, 1, 1, 'photos'),
(16, 1034, '������ �������', '', '2010-10-12 13:47:52', 'e223946b3d76cc37417d0304c9cb23a1.jpg', 1, 40, 1, 1, 1, 'photos'),
(17, 1034, '��� �� �����', '<p>�������� ������ ������ � ����� �� �����</p>', '2010-10-12 17:00:27', '38fde6623d0ad43c79c4d90a88a07009.jpg', 1, 25, 1, 1, 1, 'photos');

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
(8, 'p_ping', '���� ��������� ������', '������� ������ � ���� ��� ���������� ������ � ������ � �����', 'InstantCMS Team', '1.0', 'plugin', 1, '---\nYandex HOST: ping.blogs.yandex.ru\nYandex PATH: /RPC2\nGoogle HOST: blogsearch.google.com\nGoogle PATH: /ping/RPC2\n'),
(15, 'p_morecontent', '������� ������', '��������� � ����� ������ ������ ������ ������� ������.', 'Maximov & InstantCMS Team', '1.0', 'plugin', 0, '---\nlimit: 5\nunsort: 1\n'),
(14, 'p_hidetext', '������� �����', '�������� ���������� ���� [hide] �� ��������������������', 'InstantCMS Team', '1.0', 'plugin', 1, '---\n');

DROP TABLE IF EXISTS `#__polls`;
CREATE TABLE `#__polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `pubdate` date NOT NULL DEFAULT '0000-00-00',
  `answers` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__polls` (`id`, `title`, `pubdate`, `answers`) VALUES
(2, '����� � ��� �������?', '2008-05-23', 'a:3:{s:7:"�������";i:0;s:10:"����������";i:1;s:11:"�����������";i:0;}');

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
(26, '����', 'catalog', 5),
(27, '�������', 'catalog', 5),
(28, '������', 'catalog', 5),
(31, '����', 'catalog', 7),
(32, '�������', 'catalog', 7),
(241, '����', 'blogpost', 5),
(240, '������', 'blogpost', 5),
(273, '��������', 'content', 31),
(272, '������', 'content', 31),
(271, '������', 'content', 31),
(279, '��������', 'content', 32),
(278, '������', 'content', 32),
(277, '������', 'content', 32),
(282, '���������', 'content', 33),
(281, '������', 'content', 33),
(280, '������', 'content', 33),
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
  KEY `category_id` (`category_id`),
  FULLTEXT KEY `title` (`title`,`fieldsdata`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__uc_items` (`id`, `category_id`, `title`, `pubdate`, `published`, `imageurl`, `fieldsdata`, `hits`, `is_comments`, `tags`, `rating`, `meta_desc`, `meta_keys`, `price`, `canmany`, `user_id`, `on_moderate`) VALUES
(1, 1, '���������', '2008-06-03 13:38:55', 1, 'b00117f6bca1efaaef37b44da87c1100.jpg', 'a:4:{i:0;s:7:"3130070";i:1;s:7:"�������";i:2;s:7:"191 ��.";i:3;s:64:"����� ��� ���������: 12 ������� ���������� � ��������� ���������";}', 37, 1, '�������, ���������', 0, '', '', 14.6, 1, 0, 0),
(2, 1, '����� ��� ���������', '2008-06-03 13:40:37', 1, 'b21ddffd1e9fe4716f5d1496c4e74400.jpg', 'a:5:{i:0;s:10:"3170050PK2";i:1;s:34:"�������, �������, �����, ���������";i:2;s:6:"84 ��.";i:3;s:65:"8 �������� ����������, 8 ������ � ��������, � ����������� �������";i:4;s:19:"11.00, 12.55, 13.02";}', 41, 1, '�����, ������', 0, '', '', 24, 1, 0, 0),
(5, 2, 'Toyota Estima', '2008-06-03 13:47:00', 1, '96bd390df9222bdc684ceec8afc94ec3.jpg', 'a:4:{i:0;s:7:"�������";i:1;s:9:"2,4 �����";i:2;s:4:"2000";i:3;s:1056:"<p>������ ��������� Toyota Estima, ����������� �� ����� � 2000 ����, �������� ����� ���������. �������������� ��������� ������ ���������� �� ��������� ������������ �������� ��������. � ������������ Estima ������ ��� �������� ����������: ����� 4-����������� ������ ��������� ������� 2.4 ����� � �������� ����������������� DOHC � 3-�������� 6-����������� V-�������� ��������� � ��� �� �������� �����������������. Estima ���������� ������ �������� � �������� ��������� ��������.</p>\r\n<p>�������������� ���������� ������� ���������� ����������� � ����������� ������� ������ ������ � �������� �������� �����. ������ � ������ ���������� ����� ������ �� ��������� � ���������� ����������, �� ���� �������� ���� ����������� �� 40 ��.</p>\r\n<p>��� ����� Estima �������� ���������� &laquo;easy closer&raquo;, ������ ����� ����������, ������� ����� �������. � �������� ������������ ����� ������������ �������� ��������������� �������� � ��������. ������ ��������� Estima � ������ ������ ����������� ���������� ������ ������� � ������ �� ������ &laquo;2-3-3&raquo;.</p>";}', 8, 1, '����, �������, ������', 0, '', '', 0, 1, 0, 0),
(6, 2, 'Mitsubishi Eterna!', '2008-06-03 10:54:00', 1, '7afbfacf9a4c4a9d64e0da2b31b880e5.jpg', 'a:4:{i:0;s:5:"�����";i:1;s:9:"1.8 �����";i:2;s:4:"1992";i:3;s:0:"";}', 11, 1, '', 0, '��������', '�������� �����', 0, 1, 0, 0),
(7, 2, 'Subaru Domingo', '2008-06-03 13:51:24', 1, 'db0297daef1de808feed34a75b5ea49b.jpg', 'a:4:{i:0;s:7:"�������";i:1;s:9:"1.2 �����";i:2;s:4:"1991";i:3;s:0:"";}', 32, 1, '����, �������', 0, '', '', 0, 1, 0, 0);

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
(1, 2, 'admin', '�������������', '2ca41752ccf4dbdb76d8fe88c488fd44', 'admin@cms.ru', '100200300', '2007-11-23 12:41:57', '2010-11-14 15:24:30', '1980-10-23', 0, 0, 32, 0, '127.0.0.1', '����� ������� ������ �� ���� ��� ���������� � ���� ���������� ����', '2010-10-21 02:06:53', 0, '2010-11-09 23:25:59'),
(2, 1, 'vasya', '�������', '2ca41752ccf4dbdb76d8fe88c488fd44', 'vasya@cms.ru', '100200300', '2008-07-16 16:31:48', '2010-11-14 14:32:47', '1980-01-01', 0, 0, 5, 0, '127.0.0.1', 'I love InstantCMS', '2010-11-13 23:09:34', 0, '2010-11-02 13:50:04'),
(3, 1, 'fedor', '�����', '2ca41752ccf4dbdb76d8fe88c488fd44', 'fedor@cms.com', '100334564', '2010-10-20 17:33:42', '2010-11-13 23:22:26', '1979-10-20', 0, 0, 0, 0, '127.0.0.1', 'We are all made of stars (c) Moby', '2010-10-28 15:44:45', NULL, NULL);

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

INSERT INTO `#__user_albums` (`id`, `user_id`, `title`, `pubdate`, `allow_who`) VALUES
(2, 3, '��� ����������', '2010-10-22 20:28:51', 'all'),
(5, 1, '��� ��������', '2010-11-13 23:13:37', 'all');

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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_awards` (`id`, `user_id`, `pubdate`, `title`, `description`, `imageurl`, `from_id`, `award_id`) VALUES
(1, 2, '2010-10-27 21:46:44', '������ �� �������', '� ������������� �� �������������', 'aw.gif', 1, 0);

DROP TABLE IF EXISTS `#__user_clubs`;
CREATE TABLE `#__user_clubs` (
  `user_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'guest',
  `pubdate` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_clubs` (`user_id`, `club_id`, `role`, `pubdate`) VALUES
(3, 14, 'member', '0000-00-00 00:00:00');

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_friends` (`id`, `to_id`, `from_id`, `logdate`, `is_accepted`) VALUES
(1, 2, 1, '2010-10-08 17:53:22', 1),
(2, 3, 2, '2010-10-21 01:22:27', 1),
(11, 3, 1, '2010-11-13 23:18:19', 1);

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
  PRIMARY KEY (`id`),
  KEY `to_id` (`to_id`),
  KEY `from_id` (`from_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

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

INSERT INTO `#__user_photos` (`id`, `user_id`, `album_id`, `pubdate`, `title`, `description`, `allow_who`, `hits`, `imageurl`) VALUES
(6, 1, 5, '2010-11-13', '������ ������', '�������� ��� � ������� ������', 'all', 4, 'b22c5c0f95c1fb9398578fd5e396c7dd.jpg'),
(7, 1, 5, '2010-11-13', '������ � �������', '��� �� ���� �������', 'all', 3, 'efe8d13779cd84cfeb319d9f0875a511.jpg');

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
  KEY `city` (`city`),
  KEY `formsdata` (`formsdata`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 ROW_FORMAT=DYNAMIC;

INSERT INTO `#__user_profiles` (`id`, `user_id`, `city`, `description`, `showmail`, `showbirth`, `showicq`, `karma`, `imageurl`, `allow_who`, `signature`, `gender`, `formsdata`, `email_newmsg`, `cm_subscribe`, `stats`) VALUES
(1, 1, '������', '�.��������, �.������, �.�����', 1, 1, 1, 0, '', 'all', '', 'm', '---\n22: �.��������, �.������, �.�����\n24: ������\n', 1, 'none', '---\ncount: \n  comments: 1\n  forum: 1\n  photos: 2\n  board: 2\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(2, 2, '������', '��������, &#8217;������&#8217;, ������ "�������" ������', 0, 0, 1, 0, '165e5d6b2786dc6d0a538146de38b480.jpg', 'all', '', 'm', '---\n22: |\n  &#8217;������&#8217;, ������ &quot;�������&quot; ������\n24: ������\n', 1, '0', '---\ncount: \n  comments: 1\n  forum: 0\n  photos: 0\n  board: 1\n  files_public: 0\n  files_private: 0\nrating: 0\n'),
(3, 3, '', '', 0, 0, 1, 0, 'a946f7701b178eedbbdae7a57ba7e0be.jpg', 'all', '', '', '', 1, '', '---\ncount: \n  comments: 0\n  forum: 0\n  photos: 0\n  board: 0\n  files_public: 0\n  files_private: 0\nrating: 0\n');

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
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `#__user_wall` (`id`, `user_id`, `author_id`, `pubdate`, `content`, `usertype`) VALUES
(6, 1, 1, '2010-10-22 20:52:56', '������� ��� ��������� � ��� �������', 'user'),
(7, 3, 2, '2010-10-28 16:12:16', '��� ��� ���� ����� ������!', 'user'),
(9, 2, 1, '2010-11-09 17:24:05', '����� ������� ������ �� ����', 'user');
