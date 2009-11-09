<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

	session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

	if ($inCore->request('file', 'str')) { $shortfile = $inCore->request('file', 'str'); } else { $shortfile = date('d-m-Y').'.sql'; }
    $opt = $inCore->request('opt', 'str', 'export');

	$dir    = PATH.'/backups';
	$file   = $dir.'/'.$shortfile;

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных

    $inConf = cmsConfig::getInstance();

    $inDB = cmsDatabase::getInstance();

	if ($opt=='export'){
		include($_SERVER['DOCUMENT_ROOT'].'/includes/dbexport.inc.php');
		if (is_writable($dir)){	
			$dumper = new MySQLDump($inConf->db_base,$file,false,false);
			$dumper->doDump();
			if(!$inDB->errno()){
				$fileurl = '/backups/'.$shortfile;	
				echo '<span style="color:green">Экспорт базы данных завершен.</span> <a href="/backups/'.$shortfile.'" target="_blank">Скачать файл</a> | <a href="#" onclick="deleteDump(\''.$shortfile.'\')">Удалить файл</a>';				
				echo '<div class="hinttext">Чтобы скачать файл, щелкните правой кнопкой мыши по ссылке и выберите "Сохранить объект как..."</div>';
			} else {
				echo '<span style="color:red">Ошибка экспорта базы</span>';
			}			
		} else {
			echo '<span style="color:red">Папка "/backups" не доступна для записи!</span>';	
		}	
	}
	
	if ($opt=='import'){
		$uploaddump = $dir.'/import.sql';	
		if (@move_uploaded_file($_FILES['dumpfile']['tmp_name'], $uploaddump)) {
			include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');
			$errors = '';
			if(dbRunSQL($uploaddump, $inConf->db_prefix)){
				@unlink($uploaddump);
				echo '<span style="color:green">Импорт базы данных завершен.</span>';	
			} else {
				echo '<span style="color:red">'.$errors.'</span>';		
			}
		} else {
			echo '<span style="color:red">Ошибка импорта базы</span>';		
		}
	}
	
	if ($opt=='delete'){
		if(@unlink($file)){
		 	echo '<span style="color:green">Файл удален.</span>';
		} else {
		 	echo '<span style="color:red">Ошибка удаления файла.</span>';
		}
	}
	
?>