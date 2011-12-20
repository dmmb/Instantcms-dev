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
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    header('Content-Type: text/html; charset=utf-8');

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
                $ext        = mb_strtolower($path_parts['extension']);
				
				if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp' || $ext == 'png'){

					// оригинальный файл
					$filename       = md5($realfile.time()).'-orig.'.$ext;
					$uploadfile     = $uploaddir . $filename;
					// сконверченый файл
                    $filename_jpg   = md5($realfile.time()).'.jpg';
					$uploadphoto    = $uploaddir . $filename_jpg;
					// url файла
					$fileurl = '/upload/'.$place.'/'.$filename_jpg;

					if ($inCore->moveUploadedFile($_FILES['attach_img']['tmp_name'], $uploadfile, $_FILES['attach_img']['error'])) {

						$inCore->includeGraphics();
						$sql = "INSERT INTO cms_upload_images (post_id, session_id, fileurl, target)
								VALUES ('0', '".session_id()."', '{$fileurl}', '$place')";
						$inDB->query($sql);

                        @img_resize($uploadfile, $uploadphoto, $cfg['img_w'], $cfg['img_h']);

						if ($cfg['watermark']) { @img_add_watermark($uploadphoto); }

                        @unlink($uploadfile);

						echo "{";
						echo	"error: '',\n";
						echo	"msg: '".$filename_jpg."'\n";
						echo "}";
					} else { 
						echo "{";
						echo	"error: 'Файл не загружен! Проверьте его тип, размер и права на запись в папку /upload/$place.',\n";
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