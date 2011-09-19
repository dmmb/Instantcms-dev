<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
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

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $version_prev = '1.7';
    $version_next = '1.8';
	
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
    if (!$inDB->isFieldExists('cms_modules', 'access_list')){
        $inDB->query("ALTER TABLE `cms_modules` ADD `access_list` TINYTEXT NOT NULL AFTER `css_prefix`");
        echo '<p>Поле <strong>access_list</strong> добавлено в таблицу <strong>cms_modules</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_modules', 'allow_group')){
		$sql    = "SELECT id, allow_group
				   FROM cms_modules";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
			while($mod = $inDB->fetch_assoc($result)){
				if($mod['allow_group'] != -1) {
	
					$access_list[]  = $mod['allow_group'];
					$access_list_ya = $inCore->arrayToYaml($access_list);
					$inDB->query("UPDATE cms_modules SET `access_list` = '{$access_list_ya}' WHERE id = '{$mod['id']}'");
					unset ($access_list);
	
				}
			}
		}
	
		echo '<p>Мультидоступ групп к модулям выполнен.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_modules', 'allow_group')){
        $inDB->query("ALTER TABLE `cms_modules` DROP `allow_group`");
        echo '<p>Поле <strong>allow_group</strong> удалено из таблицы <strong>cms_modules</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //	
    if (!$inDB->isFieldExists('cms_menu', 'access_list')){
        $inDB->query("ALTER TABLE `cms_menu` ADD `access_list` TINYTEXT NOT NULL AFTER `template`");
        echo '<p>Поле <strong>access_list</strong> добавлено в таблицу <strong>cms_menu</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_menu', 'allow_group')){
		$sql    = "SELECT id, allow_group
				   FROM cms_menu";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
			while($mod = $inDB->fetch_assoc($result)){
				if($mod['allow_group'] != -1) {
	
					$access_list[]  = $mod['allow_group'];
					$access_list_ya = $inCore->arrayToYaml($access_list);
					$inDB->query("UPDATE cms_menu SET `access_list` = '{$access_list_ya}' WHERE id = '{$mod['id']}'");
					unset ($access_list);
	
				}
			}
		}
	
		echo '<p>Мультидоступ групп к пунктам меню выполнен.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_menu', 'allow_group')){
        $inDB->query("ALTER TABLE `cms_menu` DROP `allow_group`");
        echo '<p>Поле <strong>allow_group</strong> удалено из таблицы <strong>cms_menu</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_clubs', 'create_karma')){
        $inDB->query("ALTER TABLE `cms_clubs` ADD `create_karma` INT( 11 ) NOT NULL AFTER `join_karma_limit`");
        echo '<p>Поле <strong>create_karma</strong> добавлено в таблицу <strong>cms_clubs</strong></p>';
		$is_was_migrate = true;
    }
    if (!$inDB->isFieldExists('cms_clubs', 'is_vip')){
        $inDB->query("ALTER TABLE `cms_clubs` ADD `is_vip` TINYINT NOT NULL DEFAULT '0'");
        echo '<p>Поле <strong>is_vip</strong> добавлено в таблицу <strong>cms_clubs</strong></p>';
		$is_was_migrate = true;
    }
    if (!$inDB->isFieldExists('cms_clubs', 'join_cost')){
        $inDB->query("ALTER TABLE `cms_clubs` ADD `join_cost` FLOAT NOT NULL");
        echo '<p>Поле <strong>join_cost</strong> добавлено в таблицу <strong>cms_clubs</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_comments', 'content_bbcode')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `content_bbcode` TEXT NOT NULL AFTER `content`");
        echo '<p>Поле <strong>content_bbcode</strong> добавлено в таблицу <strong>cms_comments</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //

    $inDB->query("ALTER TABLE `cms_search` CHANGE `link` `link` VARCHAR( 200 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL");

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_user_msg', 'to_del')){
        $inDB->query("ALTER TABLE `cms_user_msg` ADD `to_del` TINYINT NOT NULL DEFAULT '0'");
        $inDB->query("ALTER TABLE `cms_user_msg` ADD `from_del` TINYINT NOT NULL DEFAULT '0'");
        echo '<p>Поля <strong>to_del</strong>, <strong>from_del</strong> добавлены в таблицу <strong>cms_user_msg</strong></p>';
        $is_was_migrate = true;
    }	

    if (!$inDB->isFieldExists('cms_board_items', 'is_vip')){
        $inDB->query("ALTER TABLE `cms_board_items` ADD `is_vip` TINYINT NOT NULL DEFAULT '0'");
        $inDB->query("ALTER TABLE `cms_board_items` ADD `vipdate` DATETIME NOT NULL");
        echo '<p>Поля <strong>is_vip</strong>, <strong>vipdate</strong> добавлены в таблицу <strong>cms_board_items</strong></p>';
        $is_was_migrate = true;
    }

    if (!$inDB->isFieldExists('cms_category', 'cost')){
        $inDB->query("ALTER TABLE `cms_category` ADD `cost` VARCHAR( 5 ) NOT NULL");
        $is_was_migrate = true;
    }

    if (!$inDB->isFieldExists('cms_uc_cats', 'cost')){
        $inDB->query("ALTER TABLE `cms_uc_cats` ADD `cost` VARCHAR( 5 ) NOT NULL");
        $is_was_migrate = true;
    }

    if (!$inDB->isFieldExists('cms_forums', 'topic_cost')){
        $inDB->query("ALTER TABLE `cms_forums` ADD `topic_cost` FLOAT NOT NULL DEFAULT '0'");
        $is_was_migrate = true;
    }

    if (!$inDB->isFieldExists('cms_users', 'is_logged_once')){
        $inDB->query("ALTER TABLE `cms_users` ADD `is_logged_once` TINYINT NOT NULL DEFAULT '1' AFTER `is_deleted`");
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

    
?>