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
                  `description` varchar(500) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `action_id` (`action_id`,`user_id`),
                  KEY `object_id` (`object_id`),
                  KEY `target_id` (`target_id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";

        $inDB->query($sql);
        echo '<p>Таблица <strong>cms_actions_log</strong> создана</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_user_invites')){

        $sql = "CREATE TABLE `cms_user_invites` (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `code` VARCHAR( 32 ) NOT NULL ,
                    `owner_id` INT NOT NULL ,
                    `createdate` DATETIME NOT NULL ,
                    `is_used` SMALLINT NOT NULL DEFAULT '0',
                    INDEX ( `code` , `owner_id` , `is_used` )
                ) ENGINE = MYISAM";

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
    }

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

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_modules', 'is_strict_bind')){
        $inDB->query("ALTER TABLE `cms_modules` ADD `is_strict_bind` TINYINT NOT NULL DEFAULT '0'");
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

    $sql = "SELECT id, message
            FROM cms_user_msg";

    $result = $inDB->query($sql);

    if($inDB->num_rows($result)){

        while($msg = $inDB->fetch_assoc($result)){
		
			$html = $inCore->parseSmiles($msg['message'], true);
			$html = mysql_escape_string($html);
			
            $inDB->query("UPDATE cms_user_msg SET message = '{$html}' WHERE id = '{$msg['id']}'");

        }

        echo '<p>Записи личных сообщений оптимизированы.</p>';

    }

// ========================================================================== //
// ========================================================================== //

    $sql = "SELECT id, content
            FROM cms_comments";

    $result = $inDB->query($sql);

    if($inDB->num_rows($result)){

        while($msg = $inDB->fetch_assoc($result)){
		
			$html = $inCore->parseSmiles($msg['content'], true);
			$html = mysql_escape_string($html);
			
            $inDB->query("UPDATE cms_comments SET content = '{$html}' WHERE id = '{$msg['id']}'");

        }

        echo '<p>Комментарии оптимизированы.</p>';

    }
		
// ========================================================================== //
// ========================================================================== //

    $sql = "SELECT id, content
            FROM cms_user_wall";

    $result = $inDB->query($sql);

    if($inDB->num_rows($result)){

        while($msg = $inDB->fetch_assoc($result)){
		
			$html = $inCore->parseSmiles($msg['content'], true);
			$html = mysql_escape_string($html);
			
            $inDB->query("UPDATE cms_user_wall SET content = '{$html}' WHERE id = '{$msg['id']}'");

        }

        echo '<p>Записи на стенах пользователей оптимизированы.</p>';

    }

// ========================================================================== //
// ========================================================================== //

    $mod_friends_installed = $inDB->get_field('cms_modules', "content='mod_user_friend'", 'id');

    if (!$mod_friends_installed){

        $sql = "INSERT INTO `cms_modules` VALUES ('', 'left', 'Друзья онлайн', 'Друзья онлайн', 1, 'mod_user_friend', 5, 1, 0, 0,
    '---
    limit: 5
    view_type: table', 1, '', -1, 0, 1, 'HOUR','module_simple.tpl', 0)";

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

//    if (!$inDB->isFieldExists('', '')){
//
//        $inDB->query("");
//        echo '<p></p>';
//
//    }

// ========================================================================== //
// ========================================================================== //

    echo '</div>';

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Обязательно удалите папку /migrate/ прежде чем продолжить!</div>';
	echo '<a href="/">Перейти на сайт</a>';
    
?>