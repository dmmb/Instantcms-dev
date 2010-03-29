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

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
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

    echo '<h2>�������� 1.5.3 &rarr; 1.6.0</h2>';

    echo '<div class="migrate">';

// ========================================================================== //
// ========================================================================== //

    $inDB->query("UPDATE cms_menu SET linkid='blogs' WHERE linktype='component' AND linkid='blog'");

    $inDB->query("UPDATE cms_components SET `link` = 'blogs' WHERE link = 'blog'");

    $menu_items  = $inCore->getMenuStruct();

    foreach($menu_items as $item){
        $item['link'] = $inCore->getMenuLink($item['linktype'], $item['linkid'], $item['menuid']);
        $inDB->query("UPDATE cms_menu SET link = '{$item['link']}' WHERE id = {$item['id']}");
    }

    echo '<p>������ ���� ���������</p>';

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_comment_targets')){

        $sql = "CREATE TABLE cms_comment_targets (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `target` VARCHAR( 20 ) NOT NULL ,
                    `component` VARCHAR( 32 ) NOT NULL ,
                    INDEX ( `target` , `component` )
                ) ENGINE = MYISAM";

        $inDB->query($sql);

        echo '<p>������� <strong>cms_comment_targets</strong> �������</p>';

        $inCore->registerCommentsTarget('article', 'content');
        $inCore->registerCommentsTarget('blog', 'blogs');
        $inCore->registerCommentsTarget('palbum', 'photos');
        $inCore->registerCommentsTarget('photo', 'photos');
        $inCore->registerCommentsTarget('catalog', 'catalog');
        $inCore->registerCommentsTarget('userphoto', 'users');

        echo '<p>������� <strong>cms_comment_targets</strong> ���������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_content', 'url')){
        $inDB->query("ALTER TABLE `cms_content` ADD `url` VARCHAR( 100 ) NOT NULL");
        echo '<p>���� <strong>url</strong> ��������� � ������� <strong>cms_content</strong></p>';
    }

    if (!$inDB->isFieldExists('cms_category', 'url')){
        $inDB->query("ALTER TABLE `cms_category` ADD `url` VARCHAR( 100 ) NOT NULL");
        echo '<p>���� <strong>url</strong> ��������� � ������� <strong>cms_category</strong></p>';
    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_comments', 'target_title')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `target_title` VARCHAR( 150 ) NOT NULL");
        echo '<p>���� <strong>target_title</strong> ��������� � ������� <strong>cms_comments</strong></p>';
    }

    if (!$inDB->isFieldExists('cms_comments', 'target_link')){

        $inDB->query("ALTER TABLE `cms_comments` ADD `target_link` VARCHAR( 200 ) NOT NULL");
        echo '<p>���� <strong>target_link</strong> ��������� � ������� <strong>cms_comments</strong></p>';

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

            echo '<p>������� <strong>cms_comments</strong> ���������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_rating_targets')){

        $sql = "CREATE TABLE IF NOT EXISTS `cms_rating_targets` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `target` varchar(32) NOT NULL,
                      `component` varchar(32) NOT NULL,
                      `is_user_affect` tinyint(4) NOT NULL,
                      `user_weight` smallint(6) NOT NULL,
                      `target_table` varchar(32) NOT NULL,
                      `target_title` VARCHAR( 70 ) NOT NULL, 
                      PRIMARY KEY (`id`),
                      KEY `target` (`target`)
                ) ENGINE=MyISAM";

        $inDB->query($sql);

        echo '<p>������� <strong>cms_rating_targets</strong> �������</p>';

        $inCore->registerRatingsTarget('content', 'content', '������', true, 5, 'cms_content');
        $inCore->registerRatingsTarget('photo', 'photos', '���� � �������', true, 5, 'cms_photo_files');
        $inCore->registerRatingsTarget('blogpost', 'blogs', '���� � �����', true, 5, 'cms_blog_posts');
        $inCore->registerRatingsTarget('comment', 'comments', '�����������', true, 2, 'cms_comments');

        echo '<p>������� <strong>cms_rating_targets</strong> ���������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isTableExists('cms_ratings_total')){

        $sql = "CREATE TABLE `cms_ratings_total` (
                    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                    `target` VARCHAR( 32 ) NOT NULL ,
                    `item_id` MEDIUMINT NOT NULL ,
                    `total_rating` INT NOT NULL ,
                    `total_votes` INT NOT NULL ,
                    INDEX ( `target` , `item_id` )
                ) ENGINE = MYISAM";

        $inDB->query($sql);
        echo '<p>������� <strong>cms_ratings_total</strong> �������</p>';

        //== ������� ������ ������������ ==========================
        $cv_sql = "SELECT comment_id, vote, user_id
                FROM cms_comments_votes
                LIMIT 500";

        $result = $inDB->query($cv_sql);

        if ($inDB->num_rows($result)){
            while($�v = $inDB->fetch_assoc($result)){
                $insert_sql  = "INSERT INTO cms_ratings (item_id, points, ip, target, user_id, pubdate)
                                VALUES ('{$�v['comment_id']}', '{$�v['vote']}', '127.0.0.1', 'comment', '{$�v['user_id']}', NOW())";
                $inDB->query($insert_sql);
            }
        }
        unset($result);
        echo '<p>������ ������������ ���������� � ����� �������</p>';

        //== �������� cms_comment_votes ============================
        $inDB->query("DROP TABLE cms_comments_votes");
        echo '<p>������� <strong>cms_comments_votes</strong> �������</p>';

        //== ��������� �������� ====================================
        $sql = "SELECT target, item_id, SUM(points) as total_rating, COUNT(id) as total_votes
                FROM cms_ratings
                GROUP BY target, item_id";

        $result = $inDB->query($sql);

        if ($inDB->num_rows($result)){
            while($item = $inDB->fetch_assoc($result)){
                $insert_sql  = "INSERT INTO cms_ratings_total (target, item_id, total_rating, total_votes)
                                VALUES ('{$item['target']}', '{$item['item_id']}', '{$item['total_rating']}', '{$item['total_votes']}')";
                $inDB->query($insert_sql);
            }
        }

        unset($result);
        echo '<p>������� �������� �����������</p>';

    }

// ========================================================================== //
// ========================================================================== //

    if (!$inDB->isFieldExists('cms_blogs', 'rating')){

        $inDB->query("ALTER TABLE `cms_blogs` ADD `rating` INT NOT NULL");
        echo '<p>���� <strong>rating</strong> ��������� � ������� <strong>cms_blogs</strong></p>';

        //== ��������� ������� ������ ====================================
        $sql = "SELECT b.id as id,
                       IFNULL(SUM(r.total_rating), 0) AS rating
                FROM cms_blogs b
                LEFT JOIN cms_blog_posts p ON p.blog_id = b.id
                LEFT JOIN cms_ratings_total r ON r.item_id = p.id AND r.target = 'blogpost'
                GROUP BY b.id";

        $result = $inDB->query($sql);

        if ($inDB->num_rows($result)){
            while($blog = $inDB->fetch_assoc($result)){
                $insert_sql  = "UPDATE cms_blogs SET rating='{$blog['rating']}' WHERE id = '{$blog['id']}'";
                $inDB->query($insert_sql);
            }
        }

        unset($result);
        echo '<p>������� ������ �����������</p>';

    }


// ========================================================================== //
// ========================================================================== //

    echo '</div>';

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�������� ���������. ����������� ������� ����� /migrate/ ������ ��� ����������!</div>';
    echo '<div style="margin:15px 0px 15px 0px;">���� �� ������� ����������� ��������� � ���: 
            <a href="http://www.instantcms.ru/wiki/doku.php/%D0%B0%D0%BF%D0%B3%D1%80%D0%B5%D0%B9%D0%B4_%D0%BF%D0%B5%D1%80%D0%B5%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0_%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D0%B8_%D0%B4%D0%B8%D1%81%D1%82%D1%80%D0%B8%D0%B1%D1%83%D1%82%D0%B8%D0%B2%D0%B0#%D0%BF%D0%B5%D1%80%D0%B5%D0%BD%D0%BE%D1%81_%D0%B2%D0%B0%D1%88%D0%B8%D1%85_%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B9_%D0%BC%D0%B5%D0%B6%D0%B4%D1%83_%D0%B2%D0%B5%D1%80%D1%81%D0%B8%D1%8F%D0%BC%D0%B8">
                ���������� �� �������� ��������� ����� ��������
            </a>
          </div>';
	echo '<a href="/">������� �� ����</a>';
    
?>