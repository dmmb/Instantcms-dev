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
        echo '<p>Таблица <strong>cms_ns_transactions</strong> создана</p>';

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
        echo '<p>Таблица <strong>cms_actions</strong> создана</p>';

        $sql = "INSERT INTO `cms_actions` (`id`, `component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
                (2, 'comments', 'add_comment', 'Добавление комментария', 'добавляет %s| на странице %s', 1, 1),
                (7, 'photos', 'add_photo', 'Добавление фото', 'добавляет фото %s| в альбом %s', 1, 1),
                (8, 'content', 'add_article', 'Добавление статьи', 'добавляет статью %s| в раздел %s', 1, 1),
                (9, 'blogs', 'add_blog', 'Создание блога пользователем', 'создает блог %s|', 1, 1),
                (10, 'blogs', 'add_post', 'Добавление записи в блог', 'пишет пост %s| в блоге %s', 1, 1),
                (11, 'users', 'set_status', 'Изменение статуса пользователя', '', 1, 1),
                (12, 'board', 'add_board', 'Добавление объявления', 'добавляет объявление %s| в рубрику %s', 1, 1),
                (13, 'catalog', 'add_catalog', 'Добавление записи в каталог', 'добавляет запись %s| в рубрику каталога %s', 1, 1),
                (14, 'clubs', 'add_club', 'Создание клуба', 'создает клуб %s|', 1, 1),
                (15, 'clubs', 'add_club_user', 'Вступление в клуб', 'вступает в клуб %s|', 1, 1),
                (16, 'faq', 'add_quest', 'Вопрос', 'задает %s| в категории %s', 1, 1),
                (17, 'forum', 'add_fpost', 'Добавление поста в форуме', 'добавляет %s| в теме %s', 1, 1),
                (18, 'forum', 'add_thread', 'Добавление темы на форуме', 'создает тему %s| на форуме %s', 1, 1),
                (19, 'users', 'add_avatar', 'Загрузка или смена аватара пользователем', 'изменяет аватар|', 1, 1),
                (20, 'users', 'add_friend', 'Добавление друга', 'и %s стали друзьями|', 1, 1),
                (21, 'users', 'add_award', 'Получение награды пользователем', 'получает награду %s|', 1, 1),
                (22, 'users', 'add_file', 'Загрузка файла пользователем', 'загружает файл %s|', 1, 1),
                (23, 'users', 'add_wall', 'Добавление записи на стену', 'добавляет запись на стене пользователя %s|', 1, 1),
                (24, 'clubs', 'add_wall_club', 'Добавление записи на стену клуба', 'добавляет запись на стене клуба %s|', 1, 1),
                (25, 'clubs', 'add_post_club', 'Добавление записи в блог клуба', 'пишет пост %s| в клубе %s', 1, 1),
                (26, 'users', 'add_user_photo', 'Добавление фото в личный альбом', 'добавляет фото %s| в альбом %s', 1, 1),
                (27, 'users', 'add_user_photo_multi', 'Добавление много фоток в личный альбом', 'добавляет %s фото| в альбом %s', 1, 1),
                (28, 'registration', 'add_user', 'Регистрация нового пользователя', 'регистрируется. Приветствуем!|', 1, 1),
                (29, 'users', 'add_wall_my', 'Добавление записи на свою стену', 'пишет на своей стене|	', 1, 1)";

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

        echo '<p>Таблица <strong>cms_actions_log</strong> создана</p>';

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
                (1, 'photos_clear', 24, 'users', 'clearUploadedPhotos', '', 1, 1, 'Удаление неиспользуемых личных фотографий', '', ''),
                (2, 'optimize_tables', 24, '', '', '', 1, 1, 'Оптимизация таблиц БД', 'db|cmsDatabase', 'optimizeTables'),
                (3, 'drop_inactive_users', 48, 'users', 'deleteInactiveUsers', '', 1, 1, 'Удаление неактивных пользователей (см. настройки компонента \"Профили пользователей\")', '', ''),
                (4, 'remove_old_log', 48, '', '', '', 1, 1, 'Удаляет старые записи ленты событий', 'actions|cmsActions', 'removeOldLog'),
                (5, 'give_invites', 24, 'users', 'giveInvitesCron', '', 1, 1, 'Выдача инвайтов пользователям', '', ''),
                (6, 'clear_invites', 24, 'users', 'clearInvites', '', 1, 1, 'Удаление использованных инвайтов', '', '')";

        $inDB->query($sql);

        echo '<p>Таблица <strong>cms_cron_jobs</strong> создана</p>';

    }
