<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

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