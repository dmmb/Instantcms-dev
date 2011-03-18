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

	public $upload_dir    = '';			// ���������� ��������
	public $small_size_w  = 96;	    	// ������ ���������
	public $small_size_h  = '';			// ������ ���������
	public $medium_size_w = 480;		// ������ �������� �����������
	public $medium_size_h = '';			// ������ �������� �����������
	public $thumbsqr      = true;		// ���������� �����������, �� �� ���������
	public $is_watermark  = true;		// ����������� ���������, �� �� ���������
	public $is_saveorig   = 0;			// ��������� �������� ����, ��� �� ���������
	public $dir_small     = 'small/';	// ���������� �������� ���������
	public $dir_medium    = 'medium/';	// ���������� �������� �������� �����������
	public $is_circle     = false;	    // ��������� ������, ��� �� ���������
	public $only_medium   = false;		// ��������� ������ ������� �����������, ��� �� ���������

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
     * ��������� ���� ����
     * @return array $file (filename, realfile)
     */
    public function uploadPhoto($old_file=''){
		
		// ���� ������� �������� �� ���������, ���������� ����
		if (!$this->upload_dir) { return false; }

		$inCore = cmsCore::getInstance();

		if ($_FILES['Filedata']['name']){

			$inCore->includeGraphics();

			$realfile 	= $this->inDB->escape_string($_FILES['Filedata']['name']);
		
			$path_parts = pathinfo($realfile);
			$ext        = strtolower($path_parts['extension']);
			
			// ������� ���������� ����� ������ � ������
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
				
				// ������� ���������� ���� ���� ����������
				$this->deletePhotoFile($old_file);
				
				if (!$this->small_size_h) { $this->small_size_h = $this->small_size_w; }
				if (!$this->medium_size_h) { $this->medium_size_h = $this->medium_size_w; }

				// ����������� ��������� � ������� �����������
				if(!$this->only_medium){
					@img_resize($uploadphoto, $uploadthumb['small'], $this->small_size_w, $this->small_size_h, $this->thumbsqr);
				}
				@img_resize($uploadphoto, $uploadthumb['medium'], $this->medium_size_w, $this->medium_size_h, false, false);

				// ��������� ������
				if($this->is_circle) { @img_circle($uploadthumb['small'],7); }

				// ����������� ���������
				if($this->is_watermark) { @img_add_watermark($uploadthumb['medium']); }

				// ��������� ��������
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
     * ������� ���� ���� � ����� ��������
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
