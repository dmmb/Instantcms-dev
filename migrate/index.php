<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

    session_start();

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    include(PATH.'/core/cms.php');
    include(PATH.'/includes/config.inc.php');

    $inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');
	$inCore->loadClass('cron');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $version_prev = '1.8';
    $version_next = '1.9';
	
	$is_was_migrate = false;

// ========================================================================== //
// ========================================================================== //

    echo '<style type="text/css">
            body { font-family:Arial; font-size:14px; }

            a { color: #0099CC; }
            a:hover { color: #375E93; }
            h2 { color: #375E93; }

            #wrapper { padding:10px 30px; }
            #wrapper p{ line-height: 20px; }

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
             .nextlink {
                           margin-top:15px;
                           font-size:18px;
             }
          </style>';

    echo '<div id="wrapper">';

    echo "<h2>Миграция InstantCMS {$version_prev} &rarr; {$version_next}</h2>";

// ========================================================================== //
// ========================================================================== //
	if(!$inDB->get_field('cms_components', "link='actions'", 'id')){
		$sql = "INSERT INTO `cms_components` (`title`, `link`, `config`, `internal`, `author`, `published`, `version`, `system`) VALUES
		('Лента активности', 'actions', '---\r\nshow_target: 1\r\nperpage: 10\r\nperpage_tab: 15\r\nis_all: 1\r\nact_type: \r\n  add_quest: 16\r\n  add_club_user: 15\r\n  vote_movie: 31\r\n  add_movie: 30\r\n  add_friend: 20\r\n  add_post: 10\r\n  add_post_club: 25\r\n  add_catalog: 13\r\n  add_wall_my: 29\r\n  add_wall: 23\r\n  add_wall_club: 24\r\n  add_comment: 2\r\n  add_user_photo_multi: 27\r\n  add_board: 12\r\n  add_fpost: 17\r\n  add_article: 8\r\n  add_thread: 18\r\n  add_photo: 7\r\n  add_user_photo: 26\r\n  add_avatar: 19\r\n  add_file: 22\r\n  set_status: 11\r\n  add_award: 21\r\n  add_user: 28\r\n  add_blog: 9\r\n  add_club: 14\r\n', 0, 'InstantCMS Team', 1, '1.9', 1);";
		$inDB->query($sql);
	
		echo '<p>Компонент <strong>Лента активности</strong> установлен.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //	
	$inDB->query("UPDATE `cms_forums` SET `title` = '-- Корневой форум --' WHERE `parent_id` = 0 LIMIT 1");
	$inDB->query("UPDATE `cms_menu` SET `title` = '-- Корневая страница --' WHERE `parent_id` = 0 LIMIT 1");
	$inDB->query("ALTER TABLE `cms_board_cats` CHANGE `icon` `icon` VARCHAR( 200 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NULL DEFAULT 'folder_grey.png'");
// ========================================================================== //
// ========================================================================== //	
	if (!$inDB->isFieldExists('cms_users', 'openid')){

		$inDB->query("ALTER TABLE `cms_users` ADD `openid` VARCHAR( 250 ) NULL, ADD INDEX ( `openid` )");
        echo '<p>Поле <strong>openid</strong> добавлено в таблицу <strong>cms_users</strong></p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //	
	if (!$inDB->isFieldExists('cms_board_items', 'ip')){

		$inDB->query("ALTER TABLE `cms_board_items` ADD `ip` INT( 10 ) UNSIGNED NOT NULL AFTER `vipdate` , ADD INDEX ( `ip` )");
        echo '<p>Поле <strong>ip</strong> добавлено в таблицу <strong>cms_board_items</strong></p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //	
	if (!$inDB->isFieldExists('cms_board_cats', 'form_id')){

		$inDB->query("ALTER TABLE `cms_board_cats` ADD `form_id` INT( 11 ) NOT NULL AFTER `obtypes`");
        echo '<p>Поле <strong>form_id</strong> добавлено в таблицу <strong>cms_board_cats</strong></p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //	
	if (!$inDB->isFieldExists('cms_board_items', 'formsdata')){

		$inDB->query("ALTER TABLE `cms_board_items` ADD `formsdata` TEXT CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL AFTER `content`");
        echo '<p>Поле <strong>formsdata</strong> добавлено в таблицу <strong>cms_board_items</strong></p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
		$sql = "SELECT * FROM cms_board_items";

		$result = $inDB->query($sql);

		if ($inDB->num_rows($result)){

			while($item = $inDB->fetch_assoc($result)){

				$title = str_ireplace($item['obtype'], '', $item['title']);
				$title = $inDB->escape_string(trim($title));

				$inDB->query("UPDATE cms_board_items SET title='{$title}' WHERE id='{$item['id']}'");

			}

		}

		echo '<p>Типы объявлений удалены из поля заголовков.</p>';
		$is_was_migrate = true;

// ========================================================================== //
// ========================================================================== //	
	$inDB->query("DROP TABLE IF EXISTS `cms_search`");

	$sql = "CREATE TABLE `cms_search` (
			  `id` int(11) NOT NULL auto_increment,
			  `session_id` varchar(100) NOT NULL,
			  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
			  `pubdate` datetime default NULL,
			  `title` varchar(250) NOT NULL,
			  `description` varchar(500) NOT NULL,
			  `link` varchar(200) NOT NULL,
			  `place` varchar(100) NOT NULL,
			  `placelink` varchar(200) NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `session_id` (`session_id`),
			  KEY `date` (`date`)
			) ENGINE=MyISAM DEFAULT CHARSET=cp1251;";

	$inDB->query($sql);

	echo '<p>Таблица <strong>cms_search</strong> пересоздана.</p>';
	$is_was_migrate = true;

// ========================================================================== //
// ========================================================================== //
	if(!$inDB->get_field('cms_cron_jobs', "job_name='deleteOldResults'", 'id')){
		cmsCron::registerJob('deleteOldResults', array(
										'interval' => 24,
										'component' => 'search',
										'model_method' => 'deleteOldResults',
										'comment' => 'Удаляет записи в кеше поиска старее 1 дня.',
										'custom_file' => '',
										'enabled' => 1,
										'class_name' => '',
										'class_method' => ''
								  ));
		echo '<p>Задание CRON deleteOldResults для очистки поискового кеша создано.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
	if(!$inDB->get_field('cms_cron_jobs', "job_name='deleteOldNotification'", 'id')){
		cmsCron::registerJob('deleteOldNotification', array(
										'interval' => 48,
										'component' => 'users',
										'model_method' => 'deleteOldNotification',
										'comment' => 'Удаляет сообщения службы обновлений и рассылки старее 1 месяца',
										'custom_file' => '',
										'enabled' => 1,
										'class_name' => '',
										'class_method' => ''
								  ));
		echo '<p>Задание CRON deleteOldNotification для очистки сообщений службы обновлений и рассылки создано.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
	addFultextIndex('cms_blog_posts');
	addFultextIndex('cms_board_items');
	addFultextIndex('cms_faq_quests', 'quest');
	addFultextIndex('cms_faq_quests', 'answer');
	echo '<p>Индексы fulltext для таблиц cms_blog_posts, cms_faq_quests и cms_board_items созданы.</p>';
	$is_was_migrate = true;
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_comments', 'is_hidden')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `is_hidden` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `ip`");
        echo '<p>Поле <strong>is_hidden</strong> добавлено в таблицу <strong>cms_comments</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_forum_cats', 'seolink')){
        $inDB->query("ALTER TABLE `cms_forum_cats`  ADD `seolink` VARCHAR(200) NOT NULL AFTER `ordering`,  ADD INDEX (`seolink`) ");
        echo '<p>Поле <strong>seolink</strong> добавлено в таблицу <strong>cms_forum_cats</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================================== //
// ========================================================================================== //
    if ($inDB->isFieldExists('cms_forum_cats', 'seolink')){
		$sql = "SELECT * FROM cms_forum_cats";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			$inCore->loadModel('forum');
			$model = new cms_model_forum();
	
			while($cat = $inDB->fetch_assoc($result)){
	
				$seolink = $model->getCatSeoLink($cat['title']);
	
				$inDB->query("UPDATE cms_forum_cats SET seolink='{$seolink}' WHERE id='{$cat['id']}'");
	
			}
	
		}
	
		echo '<p>Генерация SEO-ЧПУ для категорий форума завершена...</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_forums', 'icon')){
        $inDB->query("ALTER TABLE `cms_forums` ADD `icon` VARCHAR( 200 ) NOT NULL AFTER `NSLevel`");
        echo '<p>Поле <strong>icon</strong> добавлено в таблицу <strong>cms_forums</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_forums', 'access_list')){
        $inDB->query("ALTER TABLE `cms_forums` ADD `access_list` TINYTEXT NOT NULL AFTER `description`");
        echo '<p>Поле <strong>access_list</strong> добавлено в таблицу <strong>cms_forums</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_forums', 'auth_group') && $inDB->isFieldExists('cms_forums', 'access_list')){
		$sql    = "SELECT id, auth_group
				   FROM cms_forums";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
			while($mod = $inDB->fetch_assoc($result)){
				if($mod['auth_group'] && $mod['auth_group'] != -1) {
	
					$access_list[]  = $mod['auth_group'];
					$access_list_ya = $inCore->arrayToYaml($access_list);
					$inDB->query("UPDATE cms_forums SET `access_list` = '{$access_list_ya}' WHERE id = '{$mod['id']}'");
					unset ($access_list);
	
				}
			}
		}
	
		echo '<p>Мультидоступ групп к форумам выполнен.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    $forum_cfg = $inCore->loadComponentConfig('forum');
	if($forum_cfg['fa_allow'] && $forum_cfg['fa_allow']!=-1){

		$access_list[] = $forum_cfg['fa_allow'];
		$forum_cfg['group_access'] = $access_list ? $inCore->arrayToYaml($access_list) : '';
		$inCore->saveComponentConfig('forum', $forum_cfg);

	} elseif(!$forum_cfg['group_access']) { $forum_cfg['group_access'] = ''; $inCore->saveComponentConfig('forum', $forum_cfg); }

	echo '<p>Мультидоступ групп к прикреплению вложений на форуме выполнен.</p>';
	$is_was_migrate = true;

// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_forums', 'auth_group')){
        $inDB->query("ALTER TABLE `cms_forums` DROP `auth_group`");
        echo '<p>Поле <strong>auth_group</strong> удалено из таблицы <strong>cms_forums</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
	if ($is_was_migrate) {
	    echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Удалите папку /migrate/ прежде чем продолжить!</div>';
	} else {
		echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Вы уже прошли миграцию.</div>';
	}
    echo '<div class="nextlink"><a href="/">Перейти на сайт</a></div>';
    echo '</div>';

// ========================================================================== //
    function addFultextIndex($table, $pole = 'title'){

		$inDB = cmsDatabase::getInstance();

		$result = $inDB->query("SHOW INDEX FROM `$table`");
		if ($inDB->num_rows($result)){
			$is = false;
			while($index = $inDB->fetch_assoc($result)){
				if($index['Key_name'] == $pole.'_full') { $is = true; };
			}
			if(!$is) { $inDB->query("ALTER TABLE `$table` ADD FULLTEXT `{$pole}_full` (`{$pole}`)"); }
		} else {
			$inDB->query("ALTER TABLE `$table` ADD FULLTEXT `{$pole}_full` (`{$pole}`)");
		}

        return true;

    }
// ========================================================================== //
?>