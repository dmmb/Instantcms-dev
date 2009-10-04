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

    //COMPLETED
	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">�������� ���������. ����������� ������� ����� /migrate/ ������ ��� ����������!</div>';
	echo '<a href="/">������� �� ����</a>';
    
?>