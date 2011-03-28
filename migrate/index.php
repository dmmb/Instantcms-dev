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

    require(PATH."/core/cms.php");
    include(PATH."/includes/config.inc.php");

    $inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
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

    echo "<h2>�������� InstantCMS {$version_prev} &rarr; {$version_next}</h2>";

// ========================================================================== //
// ========================================================================== //	
    if (!$inDB->isFieldExists('cms_modules', 'access_list')){
        $inDB->query("ALTER TABLE `cms_modules` ADD `access_list` TINYTEXT NOT NULL AFTER `css_prefix`");
        echo '<p>���� <strong>access_list</strong> ��������� � ������� <strong>cms_modules</strong></p>';
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
	
		echo '<p>������������ ����� � ������� ��������.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_modules', 'allow_group')){
        $inDB->query("ALTER TABLE `cms_modules` DROP `allow_group`");
        echo '<p>���� <strong>allow_group</strong> ������� �� ������� <strong>cms_modules</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //	
    if (!$inDB->isFieldExists('cms_menu', 'access_list')){
        $inDB->query("ALTER TABLE `cms_menu` ADD `access_list` TINYTEXT NOT NULL AFTER `template`");
        echo '<p>���� <strong>access_list</strong> ��������� � ������� <strong>cms_menu</strong></p>';
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
	
		echo '<p>������������ ����� � ������� ���� ��������.</p>';
		$is_was_migrate = true;
	}
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_menu', 'allow_group')){
        $inDB->query("ALTER TABLE `cms_menu` DROP `allow_group`");
        echo '<p>���� <strong>allow_group</strong> ������� �� ������� <strong>cms_menu</strong></p>';
		$is_was_migrate = true;
    }
// ========================================================================== //
// ========================================================================== //
    if (!$inDB->isFieldExists('cms_clubs', 'create_karma')){
        $inDB->query("ALTER TABLE `cms_clubs` ADD `create_karma` INT( 11 ) NOT NULL AFTER `join_karma_limit`");
        echo '<p>���� <strong>create_karma</strong> ��������� � ������� <strong>cms_clubs</strong></p>';
		$is_was_migrate = true;
    }	
// ========================================================================== //
// ========================================================================== //
    if ($inDB->isFieldExists('cms_comments', 'content_bbcode')){
        $inDB->query("ALTER TABLE `cms_comments` ADD `content_bbcode` TEXT NOT NULL AFTER `content`");
        echo '<p>���� <strong>content_bbcode</strong> ��������� � ������� <strong>cms_comments</strong></p>';
		$is_was_migrate = true;
    }	
// ========================================================================== //
// ========================================================================== //

    $inDB->query("ALTER TABLE `cms_search` CHANGE `link` `link` VARCHAR( 200 ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL");

// ========================================================================== //
// ========================================================================== //

    if ($inDB->isFieldExists('cms_user_msg', 'to_del')){
        $inDB->query("ALTER TABLE `cms_user_msg` ADD `to_del` TINYINT NOT NULL DEFAULT '0'");
        $inDB->query("ALTER TABLE `cms_user_msg` ADD `from_del` TINYINT NOT NULL DEFAULT '0'");
        echo '<p>���� <strong>to_del</strong>, <strong>from_del</strong> ��������� � ������� <strong>cms_user_msg</strong></p>';
        $is_was_migrate = true;
    }	

// ========================================================================== //
// ========================================================================== //
	if ($is_was_migrate) {
	    echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�������� ���������. ������� ����� /migrate/ ������ ��� ����������!</div>';
	} else {
		echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�� ��� ������ ��������.</div>';
	}
    echo '<div class="nextlink"><a href="/">������� �� ����</a></div>';
    echo '</div>';

    
?>