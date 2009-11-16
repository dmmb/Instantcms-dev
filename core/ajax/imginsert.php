<?php

    if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { die(); }

    session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных

    $inDB  = cmsDatabase::getInstance();

	$place = $inCore->request('place', 'str');
	
	if (!$place) { 
			echo "{";
			echo		"error: 'Файл не загружен!',\n";
			echo		"msg: ''\n";
			echo "}";
			die();
	}
	
	if(isset($_FILES['attach_img'])) {

		//LOAD CURRENT CONFIG
        $cfg = $inCore->loadComponentConfig($place);

		if (!isset($cfg['img_max'])) { $cfg['img_max'] = 10; } 
		if (!isset($cfg['img_on'])) { $cfg['img_on'] = 1; } 
		
		if ($cfg['img_on']){
		
			$sql    = "SELECT * FROM cms_upload_images WHERE session_id = '".session_id()."'";
			$rs     = $inDB->query($sql);
			$loaded = $inDB->num_rows($rs);
			
			if ($loaded < $cfg['img_max']){			
				$uploaddir  = PATH.'/upload/'.$place.'/';
				$realfile   = $_FILES['attach_img']['name'];
			
				$path_parts = pathinfo($realfile);
				$ext        = $path_parts['extension'];
				
				if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png'){
		
					$filename       = md5($realfile.time()).'.'.$ext;
					
					$uploadfile     = $uploaddir . $realfile;
					$uploadphoto    = $uploaddir . $filename;
			
					if (@move_uploaded_file($_FILES['attach_img']['tmp_name'], $uploadphoto)) {		
					
						$sql = "INSERT INTO cms_upload_images (post_id, session_id, fileurl, target)
								VALUES ('0', '".session_id()."', '/upload/".$place."/$filename', '$place')";
						$inDB->query($sql);

					    $filepath	= PATH."/upload/".$place."/".$filename;
						$filedir 	= PATH."/upload/".$place;
	                    @chmod(dirname($filedir), 0755);
									
						echo "{";
						echo	"error: '',\n";
						echo	"msg: '".$filename."'\n";
						echo "}";
					} else { 
						echo "{";
						echo	"error: 'Файл не загружен! Проверьте его тип и размер.',\n";
						echo	"msg: ''\n";
						echo "}";
					} 
					
				} else { 
						echo "{";
						echo	"error: 'Неверный тип файла! Допустимые типы: jpg, jpeg, gif, png, bmp.',\n";
						echo	"msg: ''\n";
						echo "}";
				} //filetype
			}//max limit
			else {
					echo "{";
					echo		"error: 'Достигнут предел количества изображений!',\n";
					echo		"msg: ''\n";
					echo "}";	
				}

		} //img is on
		else {
			echo "{";
			echo		"error: 'Загрузка файлов запрещена!',\n";
			echo		"msg: ''\n";
			echo "}";	
		}
	} else { 	
			echo "{";
			echo		"error: 'Файл не загружен!',\n";
			echo		"msg: ''\n";
			echo "}";
	 }
	
	return;
?>