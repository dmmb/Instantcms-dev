<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
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

// ========================================================================================== //
// ========================================================================================== //

    $_CFG['db_prefix'] = 'cms';
    $inConf->saveToFile($_CFG);

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_category', 'seolink')){

        $sql = "ALTER TABLE `cms_category` ADD `seolink` VARCHAR( 200 )";
        $result = $inDB->query($sql);

        $sql = "ALTER TABLE `cms_category` ADD UNIQUE (`seolink`)";
        $result = $inDB->query($sql);

        $sql = "ALTER TABLE `cms_content` ADD UNIQUE (`seolink`)";
        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_category</strong> обновлена...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    $sql = "SELECT * FROM cms_category";

    $result = $inDB->query($sql);

    if ($inDB->num_rows($result)){

        $inCore->loadModel('content');
        $model = new cms_model_content();

        while($category = $inDB->fetch_assoc($result)){

            $seolink = $model->getCategorySeoLink($category);

            $inDB->query("UPDATE cms_category SET seolink='{$seolink}' WHERE id={$category['id']}");

        }

    }

    echo '<p>Генерация SEO-ЧПУ для разделов статей завершена...</p>';

// ========================================================================================== //
// ========================================================================================== //

    $sql = "SELECT * FROM cms_content";

    $result = $inDB->query($sql);

    if ($inDB->num_rows($result)){

        $inCore->loadModel('content');
        $model = new cms_model_content();

        while($article = $inDB->fetch_assoc($result)){

            $seolink = $model->getSeoLink($article);

            $inDB->query("UPDATE cms_content SET seolink='{$seolink}' WHERE id={$article['id']}");

        }

    }

    echo '<p>Генерация SEO-ЧПУ для статей завершена...</p>';

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_blogs', 'seolink')){

        $sql = "ALTER TABLE `cms_blogs` ADD `seolink` VARCHAR( 255 )";
        $result = $inDB->query($sql);

    }

    if (!$inDB->isFieldExists('cms_blog_posts', 'seolink')){

        $sql = "ALTER TABLE `cms_blog_posts` ADD `seolink` VARCHAR( 255 )";
        $result = $inDB->query($sql);

    }

// ========================================================================================== //
// ========================================================================================== //

    $sql = "SELECT * FROM cms_blogs";

    $result = $inDB->query($sql);

    if ($inDB->num_rows($result)){

        $inCore->loadModel('blog');
        $model = new cms_model_blog();

        while($blog = $inDB->fetch_assoc($result)){

            $seolink = $model->getBlogSeoLink($blog);

            $inDB->query("UPDATE cms_blogs SET seolink='{$seolink}' WHERE id={$blog['id']}");

        }

    }

    echo '<p>Генерация SEO-ЧПУ для блогов завершена...</p>';

// ========================================================================================== //
// ========================================================================================== //

    $sql = "ALTER TABLE `cms_blogs` ADD INDEX (`seolink`) ";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_blog_posts` ADD INDEX (`seolink`)";
    $result = $inDB->query($sql);

    echo '<p>Таблица <strong>cms_blogs</strong> обновлена...</p>';
    echo '<p>Таблица <strong>cms_blog_posts</strong> обновлена...</p>';

// ========================================================================================== //
// ========================================================================================== //

    $sql = "UPDATE `cms_ratings` SET pubdate=NOW()";
    $result = $inDB->query($sql);

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_photo_albums', 'is_comments')){

        $sql = "ALTER TABLE `cms_photo_albums` ADD `is_comments` INT NOT NULL DEFAULT '0'";
        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_photo_albums</strong> обновлена...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    if (!$inDB->isFieldExists('cms_users', 'status')){

        $sql = "ALTER TABLE `cms_users`
                ADD `status` VARCHAR( 255 ) ,
                ADD `status_date` DATETIME";

        $result = $inDB->query($sql);

        echo '<p>Таблица <strong>cms_users</strong> обновлена...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Обязательно удалите папку /migrate/ прежде чем продолжить!</div>';
	echo '<a href="/">Перейти на сайт</a>';
    
?>