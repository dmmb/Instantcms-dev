<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $shortfile = $inCore->request('file', 'str', date('d-m-Y').'.sql');
    $opt = $inCore->request('opt', 'str', 'export');

	$dir    = PATH.'/backups';
	$file   = $dir.'/'.$shortfile;

    $inCore->loadClass('config');       //������������
    $inCore->loadClass('db');           //���� ������
    $inCore->loadClass('user');         //������������

    $inUser = cmsUser::getInstance();

    if (!$inUser->update()) { $inCore->halt(); }
    if (!$inUser->is_admin) { $inCore->halt(); }

    $inConf = cmsConfig::getInstance();

    $inDB = cmsDatabase::getInstance();

	if ($opt=='export'){
		include(PATH.'/includes/dbexport.inc.php');
		if (is_writable($dir)){	
			$dumper = new MySQLDump($inConf->db_base,$file,false,false);
			$dumper->doDump();
			if(!$inDB->errno()){
				$fileurl = '/backups/'.$shortfile;	
				echo '<span style="color:green">������� ���� ������ ��������.</span> <a href="#" onclick="deleteDump(\''.$shortfile.'\')">������� ����</a>';				
				echo '<div class="hinttext">���� ����� ��������� � ����� <strong>backups</strong> �� FTP.</div>';
			} else {
				echo '<span style="color:red">������ �������� ����</span>';
			}
		} else {
			echo '<span style="color:red">����� "/backups" �� �������� ��� ������!</span>';	
		}	
	}
	
	if ($opt=='import'){
		$uploaddump = $dir.'/import.sql';
		if (@move_uploaded_file($_FILES['dumpfile']['tmp_name'], $uploaddump)) {
			include(PATH.'/includes/dbimport.inc.php');
			$errors = '������ ������� ����';
			if(dbRunSQL($uploaddump, $inConf->db_prefix)){
				@unlink($uploaddump);
				echo '<span style="color:green">������ ���� ������ ��������.</span>';
			} else {
				echo '<span style="color:red">'.$errors.'</span>';
			}
		} else {
			echo '<span style="color:red">������ ������� ����</span>';
		}
	}
	
	if ($opt=='delete'){
		if(@unlink($dir.'/'.$shortfile)){ 
		 	echo '<span style="color:green">���� ������.</span>';
		} else {
		 	echo '<span style="color:red">������ �������� �����.</span>';
		}
	}
	
?>