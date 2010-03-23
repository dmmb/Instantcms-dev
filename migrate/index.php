<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
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

// ========================================================================================== //
// ========================================================================================== //

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

    echo '<h2>Миграция 1.5.3 &rarr; 1.6.0</h2>';

    echo '<div class="migrate">';

// ========================================================================================== //
// ========================================================================================== //

    $inDB->query("UPDATE cms_menu SET linkid='blogs' WHERE linktype='component' AND linkid='blog'");

    $menu_items  = $inCore->getMenuStruct();

    foreach($menu_items as $item){
        $item['link'] = $inCore->getMenuLink($item['linktype'], $item['linkid'], $item['menuid']);
        $inDB->query("UPDATE cms_menu SET link = '{$item['link']}' WHERE id = {$item['id']}");
    }

    echo '<p>Пункты меню обновлены</p>';

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isTableExists('cms_comment_targets')){

        $sql = "CREATE TABLE cms_comment_targets (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `target` VARCHAR( 20 ) NOT NULL ,
                    `component` VARCHAR( 32 ) NOT NULL ,
                    INDEX ( `target` , `component` )
                ) ENGINE = MYISAM";

        $inDB->query($sql);

        echo '<p>Таблица <strong>cms_comment_targets</strong> создана</p>';

        $inCore->registerCommentsTarget('article', 'content');
        $inCore->registerCommentsTarget('blog', 'blogs');
        $inCore->registerCommentsTarget('palbum', 'photos');
        $inCore->registerCommentsTarget('photo', 'photos');
        $inCore->registerCommentsTarget('catalog', 'catalog');

        echo '<p>Таблица <strong>cms_comment_targets</strong> заполнена</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_comments', 'target_title')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `target_title` VARCHAR( 150 ) NOT NULL");
        echo '<p>Поле <strong>target_title</strong> добавлено в таблицу <strong>cms_comments</strong></p>';
    }

    if (!$inDB->isFieldExists('cms_comments', 'target_link')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `target_link` VARCHAR( 200 ) NOT NULL");
        echo '<p>Поле <strong>target_link</strong> добавлено в таблицу <strong>cms_comments</strong></p>';
    }

// ========================================================================================== //
// ========================================================================================== //

    $sql = "UPDATE  cms_comments com, 
                    cms_content targets 
            SET     com.target_title = targets.title, com.target_link = CONCAT('/content/', targets.seolink, '.html')
            WHERE   com.target='article' AND com.target_id = targets.id";

    $inDB->query($sql);
    
    $sql = "UPDATE  cms_comments com,
                    cms_photo_albums targets
            SET     com.target_title = targets.title, com.target_link = CONCAT('/photos/', targets.id)
            WHERE   com.target='palbum' AND com.target_id = targets.id";

    $inDB->query($sql);

    $sql = "UPDATE  cms_comments com,
                    cms_photo_files targets
            SET     com.target_title = targets.title, com.target_link = CONCAT('/photos/photo', targets.id, '.html')
            WHERE   com.target='photo' AND com.target_id = targets.id";

    $inDB->query($sql);

    $sql = "UPDATE  cms_comments com,
                    cms_blog_posts targets,
                    cms_blogs blogs
            SET     com.target_title = targets.title, com.target_link = CONCAT('/blogs/', blogs.seolink, '/', targets.seolink, '.html')
            WHERE   com.target='blog' AND com.target_id = targets.id AND targets.blog_id = blogs.id";

    $inDB->query($sql);

    $sql = "UPDATE  cms_comments com,
                    cms_uc_items targets
            SET     com.target_title = targets.title, com.target_link = CONCAT('/catalog/item', targets.id, '.html')
            WHERE   com.target='catalog' AND com.target_id = targets.id";

    $inDB->query($sql);

    echo '<p>Таблица <strong>cms_comments</strong> обновлена</p>';

// ========================================================================================== //
// ========================================================================================== //


// ========================================================================================== //
// ========================================================================================== //


// ========================================================================================== //
// ========================================================================================== //


// ========================================================================================== //
// ========================================================================================== //

    echo '</div>';

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Обязательно удалите папку /migrate/ прежде чем продолжить!</div>';
    echo '<div style="margin:15px 0px 15px 0px;">Если вы вносили собственные изменения в код: 
            <a href="http://www.instantcms.ru/wiki/doku.php/%D0%B0%D0%BF%D0%B3%D1%80%D0%B5%D0%B9%D0%B4_%D0%BF%D0%B5%D1%80%D0%B5%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0_%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D0%B8_%D0%B4%D0%B8%D1%81%D1%82%D1%80%D0%B8%D0%B1%D1%83%D1%82%D0%B8%D0%B2%D0%B0#%D0%BF%D0%B5%D1%80%D0%B5%D0%BD%D0%BE%D1%81_%D0%B2%D0%B0%D1%88%D0%B8%D1%85_%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B9_%D0%BC%D0%B5%D0%B6%D0%B4%D1%83_%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%8F%D0%BC%D0%B8">
                Инструкция по переносу изменений между версиями
            </a>
          </div>';
	echo '<a href="/">Перейти на сайт</a>';
    
?>