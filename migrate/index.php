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

    echo '<h2>Миграция 1.6.2 &rarr; 1.6.3</h2>';

    echo '<div class="migrate">';

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
				$html = $msg_to.' '.$msg_after;
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

    $sql = "INSERT INTO `cms_modules` VALUES ('', 'left', 'Друзья онлайн', 'Друзья онлайн', 1, 'mod_user_friend', 5, 1, 0, 0, 
'---
limit: 5
view_type: table', 1, '', -1, 0, 1, 'HOUR','module_simple.tpl')";

    $result = $inDB->query($sql);

    echo '<p>Модуль "Друзья онлайн" установлен</p>';

// ========================================================================== //
// ========================================================================== //

    $sql = "DELETE FROM cms_uc_ratings WHERE points = 0";

    $result = $inDB->query($sql);

    echo '<p>Голоса с нулевым рейтингом универсального каталога удалены.</p>';
	
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