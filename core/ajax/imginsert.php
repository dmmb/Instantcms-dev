<?php
	header('Content-Type: text/html; charset=windows-1251'); 
    session_start();

	define("VALID_CMS", 1);
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

	include(PATH.'/core/cms.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');			//юзер

    $inUser = cmsUser::getInstance();
    $inDB  = cmsDatabase::getInstance();

	$place = $inCore->request('place', 'str');
	
	// если место не определено, выводим ошибку и выходим
	if (!$place) { 
			echo "{";
			echo		"error: 'Файл не загружен!',\n";
			echo		"msg: ''\n";
			echo "}";
			die();
	}
	
	// если в имени компонента запрещенные сиволы, выходим
	if (!preg_match('/^([a-zA-Z0-9\_]+)$/i', $place)) { die(); }
	
	// если не авторизованы, выводим ошибку и выходим
	$inUser->update();
    if (!$inUser->id) {
			echo "{";
			echo		"error: 'Загрузка файлов только для зарегистрированных!',\n";
			echo		"msg: ''\n";
			echo "}";
			die();
	}
	
	if(isset($_FILES['attach_img'])) {

		//LOAD CURRENT CONFIG
        $cfg = $inCore->loadComponentConfig($place);

		if (!isset($cfg['img_max'])) { $cfg['img_max'] = 50; }
		if (!isset($cfg['img_on'])) { $cfg['img_on'] = 1; } 
		if (!isset($cfg['watermark'])) { $cfg['watermark'] = 1; } 
		if (!isset($cfg['img_w'])) { $cfg['img_w'] = 600; }
		if (!isset($cfg['img_h'])) { $cfg['img_h'] = 600; }
		
		if ($cfg['img_on']){
		
			$sql    = "SELECT * FROM cms_upload_images WHERE session_id = '".session_id()."'";
			$rs     = $inDB->query($sql);
			$loaded = $inDB->num_rows($rs);
			
			if ($loaded < $cfg['img_max']){			
				$uploaddir  = PATH.'/upload/'.$place.'/';
				$realfile   = $_FILES['attach_img']['name'];
			
				$path_parts = pathinfo($realfile);
                $ext        = strtolower($path_parts['extension']);
				
				if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png'){
		
					$filename       = md5($realfile.time()).'.'.$ext;
                    $filename_jpg   = md5($realfile.time()).'.'.$ext.'.jpg';
					
					$uploadfile     = $uploaddir . $realfile;
					$uploadphoto    = $uploaddir . $filename;
			
					if (@move_uploaded_file($_FILES['attach_img']['tmp_name'], $uploadphoto)) {		
						$inCore->includeGraphics();
						$sql = "INSERT INTO cms_upload_images (post_id, session_id, fileurl, target)
								VALUES ('0', '".session_id()."', '/upload/".$place."/$filename_jpg', '$place')";
						$inDB->query($sql);

					    $filepath       = PATH."/upload/".$place."/".$filename;
					    $filepath_jpg	= PATH."/upload/".$place."/".$filename_jpg;
						$filedir        = PATH."/upload/".$place;

                        @img_resize($filepath, $filepath_jpg, $cfg['img_w'], $cfg['img_h']);

						if ($cfg['watermark']) { @img_add_watermark($filepath_jpg); }
	                    @chmod(dirname($filedir), 0755);

                        @unlink($filepath);

						echo "{";
						echo	"error: '',\n";
						echo	"msg: '".$filename.".jpg'\n";
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