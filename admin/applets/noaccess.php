<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function applet_noaccess(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = '������ ��������';
 	cpAddPathway('������ ��������', 'index.php?view=noaccess');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
	if ($do == 'list'){

		echo '<h3>������ ��������</h3>';
		echo '<p>������� ������������� ��������� ��� ������ � ���� ������.</p>';
		echo '<p><a href="javascript:void(0)" onclick="window.history.go(-1)">��������� �����</a></p>';
		
    }
}

?>