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

// ========================================================================================== //
// ========================================================================================== //

    function remakeCats(){

        $inDB       = cmsDatabase::getInstance();
        $inCore     = cmsCore::getInstance();

        $sql = "CREATE TABLE IF NOT EXISTS `cms_uc_cats2` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `parent_id` int(11) NOT NULL,
                  `title` varchar(200) NOT NULL,
                  `description` text NOT NULL,
                  `published` int(11) NOT NULL DEFAULT '1',
                  `fieldsstruct` text,
                  `view_type` varchar(20) NOT NULL DEFAULT 'list',
                  `fields_show` int(11) NOT NULL DEFAULT '10',
                  `showmore` int(11) NOT NULL DEFAULT '1',
                  `perpage` int(11) NOT NULL DEFAULT '20',
                  `showtags` int(11) NOT NULL DEFAULT '1',
                  `showsort` int(11) NOT NULL DEFAULT '1',
                  `is_ratings` int(11) NOT NULL,
                  `orderby` varchar(12) NOT NULL DEFAULT 'pubdate',
                  `orderto` varchar(10) DEFAULT 'desc',
                  `showabc` int(11) NOT NULL DEFAULT '1',
                  `shownew` int(11) NOT NULL,
                  `newint` varchar(100) NOT NULL,
                  `filters` int(11) NOT NULL,
                  `is_shop` int(11) NOT NULL,
                  `NSLeft` int(11) NOT NULL,
                  `NSRight` int(11) NOT NULL,
                  `NSLevel` int(11) NOT NULL,
                  `NSDiffer` int(11) NOT NULL,
                  `NSIgnore` int(11) NOT NULL,
                  `ordering` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=cp1251";

        $inDB->query($sql);

        $ns = $inCore->nestedSetsInit('cms_uc_cats2');

        function addnodes($parentid=0, $ns_parentid=1, &$ns){
            $inDB       = cmsDatabase::getInstance();
            $inCore     = cmsCore::getInstance();
            $res = $inDB->query("SELECT * FROM cms_uc_cats WHERE parent_id=$parentid ORDER BY title");
            if ($inDB->num_rows($res)){
                while ($row = $inDB->fetch_assoc($res)){
                    $nspid = $ns->AddNode($ns_parentid);
                    $row['title'] = $inDB->escape_string($row['title']);
                    $row['description'] = $inDB->escape_string($row['description']);
                    $sql = "UPDATE cms_uc_cats2
                            SET id='{$row['id']}',                                
                                title='{$row['title']}',
                                description='{$row['description']}',
                                published='{$row['published']}',
                                fieldsstruct='{$row['fieldsstruct']}',
                                view_type='{$row['view_type']}',
                                fields_show='{$row['fields_show']}',
                                showmore='{$row['showmore']}',
                                perpage='{$row['perpage']}',
                                showtags='{$row['showtags']}',
                                showsort='{$row['showsort']}',
                                is_ratings='{$row['is_ratings']}',
                                orderby='{$row['orderby']}',
                                orderto='{$row['orderto']}',
                                showabc='{$row['showabc']}',
                                shownew='{$row['shownew']}',
                                newint='{$row['newint']}',
                                filters='{$row['filters']}',
                                is_shop='{$row['is_shop']}'
                            WHERE id={$nspid}";
                    $inDB->query($sql);

                    $res1 = $inDB->query("SELECT count(id) FROM cms_uc_cats WHERE parent_id={$row['id']}");
                    list($count) = $inDB->fetch_row($res1);
                    if ($count){
                        addnodes($row['id'], $row['id'], $ns);
                    }
                }
            }
        }

        $ns->ClearNodes();
		$IDParent = $ns->AddRootNode('');
		$inDB->query("UPDATE cms_uc_cats2 SET id=1000, title='-- �������� ������� --' WHERE id={$IDParent}");
		addnodes(0, 1000, $ns);

		$inDB->query("DROP TABLE `cms_uc_cats`");
		$inDB->query("ALTER TABLE `cms_uc_cats2` RENAME `cms_uc_cats`");
		return true;

	}

    //��������� ������� �� ������

    if (!$inDB->isFieldExists('cms_uc_cats', 'NSLeft')){

        remakeCats();

        echo '<p>��������� �������� ���������� �� nested sets...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    //��� �������� (����������� ���������� ������� �������)

    if (!$inDB->isFieldExists('cms_uc_cats', 'is_public')){

        $sql = "ALTER TABLE `cms_uc_cats` ADD `is_public` INT NOT NULL DEFAULT '0'";
        $result = $inDB->query($sql);

        echo '<p>������� <strong>cms_uc_cats</strong> ���������...</p>';

    }

    if (!$inDB->isFieldExists('cms_uc_items', 'user_id')){

        $sql = "ALTER TABLE `cms_uc_items` ADD `user_id` INT NOT NULL DEFAULT '1'";
        $result = $inDB->query($sql);

        $sql = "ALTER TABLE `cms_uc_items` ADD `on_moderate` INT NOT NULL DEFAULT '0'";
        $result = $inDB->query($sql);

        echo '<p>������� <strong>cms_uc_items</strong> ���������...</p>';

    }

    if (!$inDB->isTableExists('cms_uc_cats_access')){

        $sql = "CREATE TABLE cms_uc_cats_access (
                    `cat_id` INT NOT NULL ,
                    `group_id` INT NOT NULL ,
                    INDEX ( `cat_id` , `group_id` )
                ) ENGINE = MYISAM";
        $result = $inDB->query($sql);

        echo '<p>������� <strong>cms_uc_cats_access</strong> �������...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    //��� ����� ���������� (������ ���� ���������� � ������ �������)

    if (!$inDB->isFieldExists('cms_board_cats', 'obtypes')){

        $sql = "ALTER TABLE `cms_board_cats` ADD `obtypes` TEXT NOT NULL";
        $result = $inDB->query($sql);

        echo '<p>������� <strong>cms_board_cats</strong> ���������...</p>';

    }
    
// ========================================================================================== //
// ========================================================================================== //

    //��� ����������� �����������

    if (!$inDB->isFieldExists('cms_components', 'published')){

        $sql = "ALTER TABLE `cms_components` ADD `published` INT NOT NULL DEFAULT '1'";
        $result = $inDB->query($sql);

        $sql = "ALTER TABLE `cms_components` ADD `version` VARCHAR( 6 ) NOT NULL DEFAULT '1.5'";
        $result = $inDB->query($sql);

        $sql = "ALTER TABLE `cms_components` ADD `system` INT NOT NULL DEFAULT '1'";
        $result = $inDB->query($sql);

        echo '<p>������� <strong>cms_components</strong> ���������...</p>';

    }

// ========================================================================================== //
// ========================================================================================== //

    //��� fulltext-������

    $sql = "ALTER TABLE `cms_forum_threads` ADD FULLTEXT (`title`)";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_forum_posts` ADD FULLTEXT (`content`)";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_content` ADD FULLTEXT (`title` ,`description` ,`content`)";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_blog_posts` ADD FULLTEXT (`content`)";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_board_items` ADD FULLTEXT (`content`)";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_uc_items` ADD FULLTEXT (`title` ,`fieldsdata`)";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_photo_files` ADD FULLTEXT (`title` ,`description`)";
    $result = $inDB->query($sql);

    $sql = "ALTER TABLE `cms_price_items` ADD FULLTEXT (`title`)";
    $result = $inDB->query($sql);

    echo '<p>Fulltext-������� �������...</p>';

// ========================================================================================== //
// ========================================================================================== //

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�������� ���������. ����������� ������� ����� /migrate/ ������ ��� ����������!</div>';
	echo '<a href="/">������� �� ����</a>';
    
?>