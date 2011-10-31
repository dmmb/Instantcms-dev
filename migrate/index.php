<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
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

    require(PATH."/core/cms.php");
    include(PATH."/includes/config.inc.php");

    $inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');
	$inCore->loadClass('cron');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $version_prev = '1.8';
    $version_next = '1.8.1';
	
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
	}
	echo '<p>Задание CRON deleteOldResults для очистки поискового кеша создано.</p>';
	$is_was_migrate = true;
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