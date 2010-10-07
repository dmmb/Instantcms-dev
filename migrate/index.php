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

        while($post = $inDB->fetch_assoc($result)){

            $html = $inCore->parseSmiles($post['content'], true);
            $html = mysql_escape_string($html);

            $inDB->query("UPDATE cms_blog_posts SET content_html = '{$html}' WHERE id = '{$post['id']}'");

        }

        echo '<p>Записи блогов оптимизированы</p>';

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