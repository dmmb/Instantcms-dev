<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

    session_start();

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

    require(PATH."/core/cms.php");
    include(PATH."/includes/config.inc.php");

    $inCore     = cmsCore::getInstance();

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

// ========================================================================== //
// ========================================================================== //

    echo '<style type="text/css">
            body { font-family:Arial; font-size:12px; }
            .migrate p { 
                           line-height:16px;
                           padding-left:20px;
                           margin:2px;
                           margin-left:20px;                           
                           background:url(/admin/images/actions/on.gif) no-repeat;
                       }
            .important {
                           margin:20px;
                           margin-left:0px;
                           border:solid 1px silver;
                           padding:15px;
                           padding-left:65px;
                           background:url(important.png) no-repeat 15px 15px;
                       }
          </style>';

    echo '<h2>Миграция 1.6.2 &rarr; 1.7</h2>';

    echo '<div class="migrate">';

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_modules', 'is_strict_bind')){
        $inDB->query("ALTER TABLE `cms_modules` ADD `is_strict_bind` TINYINT NOT NULL DEFAULT '0'");
    }

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
                VALUES ('sidebar', 'Статистика пользователей', 'Статистика пользователей', '1', 'mod_user_stats',
                        '1', '1', '0', '0', '', '1', '', '-1', '0', '1', 'HOUR', 'module.tpl', '0')";

        $inDB->query($sql);

        echo '<p>Модуль &laquo;<strong>Статистика пользователей</strong>&raquo; установлен</p>';

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
                VALUES ('sidebar', 'Пригласить друга', 'Пригласить друга', '1', 'mod_invite',
                        '1', '1', '0', '0', '', '1', '', '-1', '0', '1', 'HOUR', 'module.tpl', '0')";

        $inDB->query($sql);

        echo '<p>Модуль &laquo;<strong>Пригласить друга</strong>&raquo; установлен</p>';

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
                VALUES ('maintop', 'Лента активности', 'Лента активности', '1', 'mod_actions',
                        '1', '1', '0', '0', '', '1', '', '-1', '0', '1', 'HOUR', 'module.tpl', '0')";

        $inDB->query($sql);

        echo '<p>Модуль &laquo;<strong>Лента активности</strong>&raquo; установлен</p>';

    }

// ========================================================================== //
// ========================================================================== //

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

        $inCore->loadClass('actions');

        cmsActions::registerAction(
                                    'comments',
                                    array(
                                            'name'=>'add_comment',
                                            'title'=>'Добавление комментария',
                                            'message'=>'добавляет %s на странице %s'
                                         )
                                  );

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
        
        $sql = "INSERT INTO `cms_actions` (`id`, `component`, `name`, `title`, `message`, `is_tracked`, `is_visible`) VALUES
                (2, 'comments', 'add_comment', 'Добавление комментария', 'добавляет %s| на странице %s', 1, 1),
                (7, 'photos', 'add_photo', 'Добавление фото', 'добавляет фото %s| в альбом %s', 1, 1),
                (8, 'content', 'add_article', 'Добавление статьи с сайта', 'добавляет статью %s| в раздел %s', 1, 1),
                (9, 'blogs', 'add_blog', 'Создание блога пользователем', 'создает блог %s|', 1, 1),
                (10, 'blogs', 'add_post', 'Добавление поста в блог', 'пишет пост %s| в блоге %s', 1, 1),
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
                (24, 'clubs', 'add_wall_club', 'Добавление записи на стену', 'добавляет запись на стене клуба %s|', 1, 1),
                (25, 'clubs', 'add_post_club', 'Добавление поста в блог клуба', 'пишет пост %s| в клубе %s', 1, 1),
                (26, 'users', 'add_user_photo', 'Добавление фото в личный альбом', 'добавляет фото %s| в альбом %s', 1, 1),
                (27, 'users', 'add_user_photo_multi', 'Добавление много фоток в личный альбом', 'добавляет %s фото| в альбом %s', 1, 1),
                (28, 'registration', 'add_user', 'Регистрация нового пользователя', 'зарегистрировался. Приветствуем!|', 1, 1),
                (29, 'users', 'add_wall_my', 'Добавление записи на свою стену', 'пишет на своей стене|	', 1, 1)";
        $inDB->query($sql);

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_cron_jobs')){

        $sql = "CREATE TABLE `cms_cron_jobs` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `job_name` varchar(50) NOT NULL,
                  `job_interval` smallint(6) NOT NULL DEFAULT '1',
                  `job_run_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
        echo '<p>Таблица <strong>cms_cron_jobs</strong> создана</p>';

        $inCore->loadClass('cron');

        cmsCron::registerJob('photos_clear', array(
                                                        'interval' => 24,
                                                        'component' => 'users',
                                                        'model_method' => 'clearUploadedPhotos',
                                                        'comment' => 'Удаление неиспользуемых личных фотографий'
                                                  ));

        cmsCron::registerJob('give_invites', array(
                                                        'interval' => 24,
                                                        'component' => 'users',
                                                        'model_method' => 'giveInvitesCron',
                                                        'comment' => 'Выдача инвайтов пользователям'
                                                  ));

        cmsCron::registerJob('clear_invites', array(
                                                        'interval' => 24,
                                                        'component' => 'users',
                                                        'model_method' => 'clearInvites',
                                                        'comment' => 'Удаление использованных инвайтов'
                                                  ));

    }

// ========================================================================== //
// ========================================================================== //

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

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_user_photos', 'album_id')){
        $inDB->query("ALTER TABLE `cms_user_photos` ADD `album_id` INT NOT NULL AFTER `user_id`, ADD INDEX (album_id)");
    }

    if (!$inDB->isTableExists('cms_user_albums')){

        $sql = "CREATE TABLE `cms_user_albums` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `title` varchar(100) NOT NULL,
                  `pubdate` datetime NOT NULL,
                  `allow_who` varchar(10) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `allow_who` (`allow_who`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";

        $inDB->query($sql);
        echo '<p>Таблица <strong>cms_user_albums</strong> создана</p>';

        $sql = "SELECT user_id as id
            FROM cms_user_photos
            GROUP BY user_id";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            $inCore->loadModel('users');
            $model = new cms_model_users();

            while($user = $inDB->fetch_assoc($result)){

                $album_id = $model->addPhotoAlbum(array('user_id'=>$user['id'], 'title'=>'Мой фотоальбом'));

                $inDB->query("UPDATE cms_user_photos SET album_id = '{$album_id}' WHERE user_id = '{$user['id']}'");

            }

            echo '<p>Личные фотоальбомы созданы</p>';

        }

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_blog_posts', 'content_html')){

        $inDB->query("ALTER TABLE `cms_blog_posts` ADD `content_html` TEXT NOT NULL AFTER `content`");

        $sql = "SELECT id, content
                FROM cms_blog_posts";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            $inCore->loadModel('blogs');
            $model = new cms_model_blogs();

            while($post = $inDB->fetch_assoc($result)){
                // Парсим по отдельности части текста, если есть тег [cut
                if (strstr($post['content'], '[cut')){
                    $msg_to 	= $model->getPostShort($post['content']);
                    $msg_to 	= $inCore->parseSmiles($msg_to, true);
                    $msg_after 	= $model->getPostShort($post['content'], false, true);
                    $msg_after 	= $inCore->parseSmiles($msg_after, true);
				$cut        = $model->getPostCut($post['content']);
				$html		= $msg_to . $cut . $msg_after;
                } else {
                $html = $inCore->parseSmiles($post['content'], true);
                }

                $html = $inDB->escape_string($html);

                $inDB->query("UPDATE cms_blog_posts SET content_html = '{$html}' WHERE id = '{$post['id']}'");

            }

            echo '<p>Записи блогов оптимизированы</p>';

        }

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_modules_bind', 'position')){

        $inDB->query("ALTER TABLE `cms_modules_bind` ADD `position` VARCHAR( 20 ) NOT NULL, ADD INDEX ( position )");

        $sql = "SELECT id, position
                FROM cms_modules";

        $result = $inDB->query($sql);

        if($inDB->num_rows($result)){

            while($mod = $inDB->fetch_assoc($result)){

                $inDB->query("UPDATE cms_modules_bind SET position = '{$mod['position']}' WHERE module_id = '{$mod['id']}'");

            }

        }

    }

// ========================================================================== //
// ========================================================================== //

//    $sql = "SELECT id, message
//            FROM cms_user_msg";
//
//    $result = $inDB->query($sql);
//
//    if($inDB->num_rows($result)){
//
//        while($msg = $inDB->fetch_assoc($result)){
//
//			$html = $inCore->parseSmiles($msg['message'], true);
//			$html = mysql_escape_string($html);
//
//            $inDB->query("UPDATE cms_user_msg SET message = '{$html}' WHERE id = '{$msg['id']}'");
//
//        }
//
//        echo '<p>Записи личных сообщений оптимизированы.</p>';
//
//    }

// ========================================================================== //
// ========================================================================== //

//    $sql = "SELECT id, content
//            FROM cms_comments";
//
//    $result = $inDB->query($sql);
//
//    if($inDB->num_rows($result)){
//
//        while($msg = $inDB->fetch_assoc($result)){
//
//			$html = $inCore->parseSmiles($msg['content'], true);
//			$html = mysql_escape_string($html);
//
//            $inDB->query("UPDATE cms_comments SET content = '{$html}' WHERE id = '{$msg['id']}'");
//
//        }
//
//        echo '<p>Комментарии оптимизированы.</p>';
//
//    }
		
// ========================================================================== //
// ========================================================================== //

//    $sql = "SELECT id, content
//            FROM cms_user_wall";
//
//    $result = $inDB->query($sql);
//
//    if($inDB->num_rows($result)){
//
//        while($msg = $inDB->fetch_assoc($result)){
//
//			$html = $inCore->parseSmiles($msg['content'], true);
//			$html = mysql_escape_string($html);
//
//            $inDB->query("UPDATE cms_user_wall SET content = '{$html}' WHERE id = '{$msg['id']}'");
//
//        }
//
//        echo '<p>Записи на стенах пользователей оптимизированы.</p>';
//
//    }

// ========================================================================== //
// ========================================================================== //

    $mod_friends_installed = $inDB->get_field('cms_modules', "content='mod_user_friend'", 'id');

    if (!$mod_friends_installed){

        $sql = "INSERT INTO `cms_modules` VALUES ('', 'left', 'Друзья онлайн', 'Друзья онлайн', 1, 'mod_user_friend', 5, 1, 0, 0, '---\nlimit: 5\nview_type: table', 1, '', -1, 0, 1, 'HOUR','module_simple.tpl', 0)";

        $result = $inDB->query($sql);

        echo '<p>Модуль &laquo;<strong>Друзья онлайн</strong>&raquo; установлен</p>';

    }

// ========================================================================== //
// ========================================================================== //

    $sql = "DELETE FROM cms_uc_ratings WHERE points = 0";

    $result = $inDB->query($sql);

    echo '<p>Голоса с нулевым рейтингом универсального каталога удалены.</p>';

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_forum_threads', 'is_hidden')){
        $inDB->query("ALTER TABLE `cms_forum_threads` ADD `is_hidden` INT( 11 ) NOT NULL DEFAULT '0' AFTER `pinned`");
    }
	
	$sql = "SELECT t.id
			FROM cms_forum_threads t, cms_forums f
			WHERE t.forum_id = f.id AND f.auth_group<>0";
	$result = $inDB->query($sql);
	
    if($inDB->num_rows($result)){

        while($msg = $inDB->fetch_assoc($result)){
		
            $inDB->query("UPDATE cms_forum_threads SET is_hidden = 1 WHERE id = '{$msg['id']}'");

        }

        echo '<p>Права доступа тем к закрытым разделам форума установлены.</p>';

    }

// ========================================================================== //
// ========================================================================== //

    echo '</div>';

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Обязательно удалите папку /migrate/ прежде чем продолжить!</div>';
	echo '<a href="/">Перейти на сайт</a>';
    
?>