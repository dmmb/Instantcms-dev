<?php

    if (!$inDB->isTableExists('cms_user_invites')){

        $sql = "CREATE TABLE `cms_user_invites` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `code` varchar(32) NOT NULL,
                  `owner_id` int(11) NOT NULL,
                  `createdate` datetime NOT NULL,
                  `is_used` tinyint(4) NOT NULL DEFAULT '0',
                  `is_sended` tinyint(4) NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `code` (`code`,`owner_id`,`is_used`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";

        $inDB->query($sql);
        echo '<p>Таблица <strong>cms_user_invites</strong> создана</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_users', 'invited_by')){
        $inDB->query("ALTER TABLE `cms_users` ADD `invited_by` INT NULL");
    }

    if (!$inDB->isFieldExists('cms_users', 'invdate')){
        $inDB->query("ALTER TABLE `cms_users` ADD `invdate` DATETIME NULL");
    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_comments', 'ip')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `ip` VARCHAR( 15 ) NOT NULL AFTER `target_link`");
    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_blog_posts', 'comments')){
        $inDB->query("ALTER TABLE `cms_blog_posts` ADD `comments` TINYINT( 4 ) NOT NULL DEFAULT '1' AFTER `seolink`");
    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_category', 'tpl')){
        $inDB->query("ALTER TABLE `cms_category` ADD `tpl` VARCHAR( 50 ) NOT NULL DEFAULT 'com_content_view.tpl' AFTER `url`");
    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_content', 'tpl')){
        $inDB->query("ALTER TABLE `cms_content` ADD `tpl` VARCHAR( 50 ) NOT NULL DEFAULT 'com_content_read.tpl' AFTER `url`");
    }
