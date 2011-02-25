<?php

    if (!$inDB->isTableExists('cms_ns_transactions')){

        $sql = "CREATE TABLE cms_ns_transactions (
                  IDTransaction INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
                  TableName TINYTEXT NULL,
                  Differ TINYTEXT NULL,
                  InTransaction BIT NULL,
                  TStamp TIMESTAMP NULL,
                  PRIMARY KEY(IDTransaction)
                )";

        $inDB->query($sql);
        echo '<p>������� <strong>cms_ns_transactions</strong> �������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_actions')){

        $sql = "CREATE TABLE `cms_actions` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `component` varchar(20) NOT NULL,
                  `name` varchar(20) NOT NULL,
                  `title` varchar(100) NOT NULL,
                  `message` varchar(255) NOT NULL,
                  `is_tracked` tinyint(4) NOT NULL,
                  `is_visible` tinyint(4) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `name` (`name`,`is_visible`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";

        $inDB->query($sql);
        echo '<p>������� <strong>cms_actions</strong> �������</p>';

        $sql = "INSERT INTO `cms_actions` (`id`, `component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
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
                (29, 'users', 'add_wall_my', '���������� ������ �� ���� �����', '����� �� ����� �����|	', 1, 1)";

        $inDB->query($sql);

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_actions_log')){

        $sql = "CREATE TABLE `cms_actions_log` (
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
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;";

        $inDB->query($sql);

        echo '<p>������� <strong>cms_actions_log</strong> �������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_cron_jobs')){

        $sql = "CREATE TABLE `cms_cron_jobs` (
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
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;";

        $inDB->query($sql);

        $sql = "INSERT INTO `cms_cron_jobs` (`id`, `job_name`, `job_interval`, `component`, `model_method`, `custom_file`, `is_enabled`, `is_new`, `comment`, `class_name`, `class_method`) VALUES
                (1, 'photos_clear', 24, 'users', 'clearUploadedPhotos', '', 1, 1, '�������� �������������� ������ ����������', '', ''),
                (2, 'optimize_tables', 24, '', '', '', 1, 1, '����������� ������ ��', 'db|cmsDatabase', 'optimizeTables'),
                (3, 'drop_inactive_users', 48, 'users', 'deleteInactiveUsers', '', 1, 1, '�������� ���������� ������������� (��. ��������� ���������� \"������� �������������\")', '', ''),
                (4, 'remove_old_log', 48, '', '', '', 1, 1, '������� ������ ������ ����� �������', 'actions|cmsActions', 'removeOldLog'),
                (5, 'give_invites', 24, 'users', 'giveInvitesCron', '', 1, 1, '������ �������� �������������', '', ''),
                (6, 'clear_invites', 24, 'users', 'clearInvites', '', 1, 1, '�������� �������������� ��������', '', '')";

        $inDB->query($sql);

        echo '<p>������� <strong>cms_cron_jobs</strong> �������</p>';

    }
