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

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������

    $opt = $inCore->request('opt', 'str', '');
    $data = $inCore->request('data', 'str', '');

    if (!$opt) { return; }

    $inDB = cmsDatabase::getInstance();

	if ($opt=='checklogin'){

		$sql    = "SELECT id, login FROM cms_users WHERE (login LIKE '$data') AND (is_deleted = 0) LIMIT 1";
		$result = $inDB->query($sql);

		if($inDB->num_rows($result)==0){
			echo '<span style="color:green">�� ������ ������������ ���� �����</span>';		
		} else {
			echo '<span style="color:red">��������� ����� �����!</span>';				
		}

	}

    return;

?>