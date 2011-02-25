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

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('user');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $version_prev = '1.7';
    $version_next = '1.7.1';
	
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
	if ($is_was_migrate) {
	    echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�������� ���������. ������� ����� /migrate/ ������ ��� ����������!</div>';
	} else {
		echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�� ��� ������ ��������.</div>';
	}
    echo '<div class="nextlink"><a href="/">������� �� ����</a></div>';
    echo '</div>';

    
?>