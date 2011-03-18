<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7.1   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, fuze@instantcms.ru                      //
//                                                                                           //
// 						         written by Igor V. Bessmeltsef, 2011                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

class cmsUploadPhoto {

    private static $instance;

	public $upload_dir    = '';			// директория загрузки
	public $small_size_w  = 96;	    	// ширина миниатюры
	public $small_size_h  = '';			// высота миниатюры
	public $medium_size_w = 480;		// ширина среднего изображения
	public $medium_size_h = '';			// высота среднего изображения
	public $thumbsqr      = true;		// квадратное изображение, да по умолчанию
	public $is_watermark  = true;		// накладывать ватермарк, да по умолчанию
	public $is_saveorig   = 0;			// сохранять оригинал фото, нет по умолчанию
	public $dir_small     = 'small/';	// директория загрузки миниатюры
	public $dir_medium    = 'medium/';	// директория загрузки среднего изображения
	public $is_circle     = false;	    // скруглять уголки, нет по умолчанию
	public $only_medium   = false;		// загружать только среднее изображение, нет по умолчанию

// ============================================================================ //
// ============================================================================ //

	private function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

// ============================================================================ //
// ============================================================================ //

    /**
     * Загружает фото файл
     * @return array $file (filename, realfile)
     */
    public function uploadPhoto($old_file=''){
		
		// если каталог загрузки не определен, возвращаем ложь
		if (!$this->upload_dir) { return false; }

		$inCore = cmsCore::getInstance();

		if ($_FILES['Filedata']['name']){

			$inCore->includeGraphics();

			$realfile 	= $this->inDB->escape_string($_FILES['Filedata']['name']);
		
			$path_parts = pathinfo($realfile);
			$ext        = strtolower($path_parts['extension']);
			
			// убираем расширение файла вместе с точкой
			$realfile = substr($realfile, 0, strrpos($realfile, '.'));
		
			if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png' && $ext != 'bmp') { return false; }

			$filename 			   = md5(time().$realfile).'.jpg';					
		
			$uploadfile			   = $this->upload_dir . $realfile;
			$uploadphoto 		   = $this->upload_dir . $filename;
			$uploadthumb['small']  = $this->upload_dir . $this->dir_small . $filename;
			$uploadthumb['medium'] = $this->upload_dir . $this->dir_medium . $filename;
			
			$uploadphoto 		   = $this->upload_dir . $filename;
		
			$source				   = $_FILES['Filedata']['tmp_name'];
			$errorCode			   = $_FILES['Filedata']['error'];
		
			if ($inCore->moveUploadedFile($source, $uploadphoto, $errorCode)) {
				
				// удаляем предыдущий файл если необходимо
				$this->deletePhotoFile($old_file);
				
				if (!$this->small_size_h) { $this->small_size_h = $this->small_size_w; }
				if (!$this->medium_size_h) { $this->medium_size_h = $this->medium_size_w; }

				// Гененрируем маленькое и среднее изображения
				if(!$this->only_medium){
					@img_resize($uploadphoto, $uploadthumb['small'], $this->small_size_w, $this->small_size_h, $this->thumbsqr);
				}
				@img_resize($uploadphoto, $uploadthumb['medium'], $this->medium_size_w, $this->medium_size_h, false, false);

				// Скруглять уголки
				if($this->is_circle) { @img_circle($uploadthumb['small'],7); }

				// Накладывать ватермарк
				if($this->is_watermark) { @img_add_watermark($uploadthumb['medium']); }

				// сохранять оригинал
				if(!$this->is_saveorig) { @unlink($uploadphoto); } elseif($this->is_watermark) { @img_add_watermark($uploadphoto); }

				$file['filename'] = $filename;

				$realfile_1251    = iconv('utf-8', 'cp1251', $realfile);
				$file['realfile'] = $realfile_1251 ? $realfile_1251 : $realfile;
		
		
			} else {
		
				return false;
				
			}


		} else {
		
			return false;
			
		}

        return $file;

    }

// ============================================================================ //
// ============================================================================ //
    /**
     * Удаляет файл фото с папок загрузки
     * @return bool
     */
	public function deletePhotoFile($file=''){
		
		if (!($file && $this->upload_dir)) { return false; }
		
		@chmod($this->upload_dir . $file, 0777);
		@unlink($this->upload_dir . $file);
		@chmod($this->upload_dir . $this->dir_small . $file, 0777);
		@unlink($this->upload_dir . $this->dir_small . $file);
		@chmod($this->upload_dir . $this->dir_medium . $file, 0777);
		@unlink($this->upload_dir . $this->dir_medium . $file);

        return true;

    }
// ============================================================================ //
// ============================================================================ //
    
}
?>
