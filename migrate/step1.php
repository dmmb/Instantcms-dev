<?php

    if (!$inDB->isFieldExists('cms_modules', 'is_strict_bind')){
        $inDB->query("ALTER TABLE `cms_modules` ADD `is_strict_bind` TINYINT NOT NULL DEFAULT '0'");
    }

    $inDB->query("ALTER TABLE `cms_user_photos` CHANGE `pubdate` `pubdate` DATETIME NOT NULL");

    if (!$inDB->isFieldExists('cms_modules', 'version')){
        $inDB->query("ALTER TABLE `cms_modules` ADD `version` VARCHAR(6) NOT NULL DEFAULT '1.0'");
    }

// ========================================================================== //
// ========================================================================== //

    $mod_stats_installed = $inDB->get_field('cms_modules', "content='mod_user_stats'", 'id');

    if (!$mod_stats_installed){

        $sql = "INSERT INTO cms_modules (`position`, `name`, `title`, `is_external`,
                                          `content`, `ordering`, `showtitle`, `published`,
                                          `user`, `config`, `original`, `css_prefix`,
                                          `allow_group`, `cache`, `cachetime`, `cacheint`,
                                           `template`, `is_strict_bind`)
                VALUES ('sidebar', '���������� �������������', '���������� �������������', '1', 'mod_user_stats',
                        '1', '1', '0', '0', '', '1', '', '-1', '0', '1', 'HOUR', 'module.tpl', '0')";

        $inDB->query($sql);

        echo '<p>������ &laquo;<strong>���������� �������������</strong>&raquo; ����������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    $mod_invite_installed = $inDB->get_field('cms_modules', "content='mod_invite'", 'id');

    if (!$mod_invite_installed){

        $sql = "INSERT INTO cms_modules (`position`, `name`, `title`, `is_external`,
                                          `content`, `ordering`, `showtitle`, `published`,
                                          `user`, `config`, `original`, `css_prefix`,
                                          `allow_group`, `cache`, `cachetime`, `cacheint`,
                                           `template`, `is_strict_bind`)
                VALUES ('sidebar', '���������� �����', '���������� �����', '1', 'mod_invite',
                        '1', '1', '0', '0', '', '1', '', '-1', '0', '1', 'HOUR', 'module.tpl', '0')";

        $inDB->query($sql);

        echo '<p>������ &laquo;<strong>���������� �����</strong>&raquo; ����������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    $mod_actions_installed = $inDB->get_field('cms_modules', "content='mod_actions'", 'id');

    if (!$mod_actions_installed){

        $sql = "INSERT INTO cms_modules (`position`, `name`, `title`, `is_external`,
                                          `content`, `ordering`, `showtitle`, `published`,
                                          `user`, `config`, `original`, `css_prefix`,
                                          `allow_group`, `cache`, `cachetime`, `cacheint`,
                                           `template`, `is_strict_bind`)
                VALUES ('maintop', '����� ����������', '����� ����������', '1', 'mod_actions',
                        '1', '1', '0', '0', '', '1', '', '-1', '0', '1', 'HOUR', 'module.tpl', '0')";

        $inDB->query($sql);

        echo '<p>������ &laquo;<strong>����� ����������</strong>&raquo; ����������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    $mod_friends_installed = $inDB->get_field('cms_modules', "content='mod_user_friend'", 'id');

    if (!$mod_friends_installed){

        $sql = "INSERT INTO `cms_modules` VALUES ('', 'left', '������ ������', '������ ������', 1, 'mod_user_friend', 5, 1, 0, 0, '---\nlimit: 5\nview_type: table', 1, '', -1, 0, 1, 'HOUR','module_simple.tpl', 0, '1.0')";

        $result = $inDB->query($sql);

        echo '<p>������ &laquo;<strong>������ ������</strong>&raquo; ����������</p>';

    }
