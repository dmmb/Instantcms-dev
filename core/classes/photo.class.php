<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						         written by Igor V. Bessmeltsef, 2011                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

class cmsPhoto {

    private static $instance;

	public $upload_dir    = '';			// директория загрузки
	public $small_size_w  = 96;	    	// ширина миниатюры
	public $small_size_h  = '';			// высота миниатюры
	public $medium_size_w = 480;		// ширина среднего изображения
	public $medium_size_h = '';			// высота среднего изображения
	public $thumbsqr      = 1;			// квадратное изображение, да по умолчанию
	public $is_watermark  = 1;			// накладывать ватермарк, да по умолчанию
	public $is_saveorig   = 0;			// сохранять оригинал фото, нет по умолчанию
	public $dir_small     = 'small/';	// директория загрузки миниатюры
	public $dir_medium    = 'medium/';	// директория загрузки среднего изображения
	
    private $where        = '';
    private $group_by     = '';
    private $order_by     = '';
    private $limit        = '100';

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

    private function resetConditions(){

        $this->where        = '';
        $this->group_by     = '';
        $this->order_by     = '';
        $this->limit        = '';

    }
// ============================================================================ //
// ============================================================================ //
    public function where($condition){
        $this->where .= ' AND ('.$condition.')' . "\n";
    }

    public function whereAlbumIs($album_id){
        $this->where("f.album_id = '$album_id'");
        return;
    }

    public function groupBy($field){
        $this->group_by = 'GROUP BY '.$field;
    }

    public function orderBy($field, $direction='ASC'){
        $this->order_by = 'ORDER BY '.$field.' '.$direction;
    }

    public function limit($howmany) {
        $this->limitIs(0, $howmany);
    }

    public function limitIs($from, $howmany='') {
        $this->limit = (int)$from;
        if ($howmany){
            $this->limit .= ', '.$howmany;
        }
    }

    public function limitPage($page, $perpage) {
        $this->limitIs(($page-1)*$perpage, $perpage);
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

			$realfile 				= $this->inDB->escape_string($_FILES['Filedata']['name']);
		
			$path_parts             = pathinfo($realfile);
			$ext                    = strtolower($path_parts['extension']);
			
			// убираем расширение файла вместе с точкой
			$realfile = substr($realfile, 0, strrpos($realfile, '.'));
		
			if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'png' && $ext != 'bmp') { return false; }

			$filename 				= md5(time().$realfile).'.jpg';					
		
			$uploadfile				= $this->upload_dir . $realfile;
			$uploadphoto 			= $this->upload_dir . $filename;
			$uploadthumb['small'] 	= $this->upload_dir . $this->dir_small . $filename;
			$uploadthumb['medium']	= $this->upload_dir . $this->dir_medium . $filename;
			
			$uploadphoto 			= $this->upload_dir . $filename;
		
			$source					= $_FILES['Filedata']['tmp_name'];
			$errorCode				= $_FILES['Filedata']['error'];
		
			if ($inCore->moveUploadedFile($source, $uploadphoto, $errorCode)) {
				
				// удаляем предыдущий файл если необходимо
				$this->deletePhotoFile($old_file);
				
				if (!$this->small_size_h) { $this->small_size_h = $this->small_size_w; }
				if (!$this->medium_size_h) { $this->medium_size_h = $this->medium_size_w; }

				@img_resize($uploadphoto, $uploadthumb['small'], $this->small_size_w, $this->small_size_h, $this->thumbsqr);
				@img_resize($uploadphoto, $uploadthumb['medium'], $this->medium_size_w, $this->medium_size_h, false, false);

				if($this->is_watermark) { @img_add_watermark($uploadthumb['medium']); }
		
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
     * Добавляет фото
     * @param array $photo
     * @param str $differ
     * @return bool
     */
	public function addPhoto($photo, $differ = ''){

        $inCore = cmsCore::getInstance();
        $inUser = cmsUser::getInstance();

        $sql = "INSERT INTO cms_photo_files (album_id, title, description, pubdate, file, published, showdate, comments, user_id, owner)
                VALUES ('{$photo['album_id']}', '{$photo['title']}', '{$photo['description']}', NOW(),
                        '{$photo['filename']}', '{$photo['published']}', '{$photo['showdate']}', 1, '{$inUser->id}', '{$differ}')";
        
        $this->inDB->query($sql);

        $photo_id = $this->inDB->get_last_id('cms_photo_files');

		if ($photo['tags']){
			$inCore->loadLib('tags');
			cmsInsertTags($photo['tags'], 'photo', $photo_id);
		}

        cmsUser::checkAwards($inUser->id);

        return $photo_id;

    }
// ============================================================================ //
// ============================================================================ //
    /**
     * Обновляет данные фото
     * @param array $photo
     * @return bool
     */
	public function updatePhoto($photo){

        $sql = "UPDATE cms_photo_files
                SET title='{$photo['title']}',
                    file='{$photo['filename']}',
                    description='{$photo['description']}',
                    published='{$photo['published']}',
                    showdate='{$photo['showdate']}'
                WHERE id = '{$photo['id']}'
                LIMIT 1";

        $this->inDB->query($sql);

		if ($photo['tags']){
			$inCore->loadLib('tags');
			cmsInsertTags($photo['tags'], 'photo', $photo['id']);
		}
		
        return true;
    }
// ============================================================================ //
// ============================================================================ //
    /**
     * Получает фото
     * @param int $photo_id
     * @param bool $is_full
     * @return array $photo
     */
    public function getPhoto($photo_id, $is_full = true){
		
		if ($is_full) {
			
			$f_sql      = "INNER JOIN cms_users u ON u.id = f.user_id \n LEFT JOIN cms_ratings_total r ON r.item_id = f.id AND r.target = 'photo'";
			$f_sql_from = ', u.nickname, u.login, IFNULL(r.total_rating, 0) as rating';
			
		} else {
			
			$f_sql      = '';
			$f_sql_from = '';
		}
		
		
		$sql = "SELECT f.id, f.album_id, f.title, f.description, f.pubdate, f.file, f.hits, f.user_id, a.user_id as auser_id, a.title cat_title
				{$f_sql_from}
				FROM cms_photo_files f
				INNER JOIN cms_photo_albums a ON a.id = f.album_id
				{$f_sql}
				WHERE f.id = '$photo_id' LIMIT 1";

		$result = $this->inDB->query($sql);

		$photo = $this->inDB->fetch_assoc($result);
		
		$photo['pubdate'] = cmsCore::dateformat($photo['pubdate']);

		// Обновляем количество просмотров фотографии
		$photo ? $this->inDB->query("UPDATE cms_photo_files SET hits = hits + 1 WHERE id = '$photo_id' LIMIT 1") : '';

		return $photo ? $photo : false;

    }
// ============================================================================ //
// ============================================================================ //
    /**
     * Удаляет фото
     * @param int $photo_id
     * @param str $file
     * @return bool
     */
	public function deletePhoto($photo_id, $file=''){
        
        $inCore = cmsCore::getInstance();

        if (!$file){
            $file = $this->inDB->get_field('cms_photo_files', "id='{$photo_id}'", 'file');
        }
        if (!$file){ return false; }
		
		$this->deletePhotoFile($file);
        
		$inCore->deleteComments('photo', $photo_id);
		$inCore->deleteRatings('photo', $photo_id);
		$inCore->loadLib('tags');
		cmsClearTags('photo', $photo_id);

        $sql = "DELETE FROM cms_photo_files WHERE id = '$photo_id' LIMIT 1";

        $this->inDB->query($sql);
		
		return true;

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
    /**
     * Создает корневой фотоальбом
     * @param int $user_id
     * @param str $differ
     * @param str $title
     * @return int $album_id
     */
	public function createRootAlbum($user_id, $differ='', $title=''){

		$inCore = cmsCore::getInstance();

		$ns = $inCore->nestedSetsInit('cms_photo_albums');
		$album_id = $ns->AddRootNode($differ);

		$sql = "UPDATE cms_photo_albums
				SET user_id = '$user_id', 
					title = '$title',
					pubdate=NOW(),
					orderform=0
				WHERE id = '$album_id'";

		$this->inDB->query($sql);
		
		return $album_id;
	}
// ============================================================================ //
// ============================================================================ //
    /**
     * Создает фотоальбом
     * @param int $user_id
     * @param int $parent_id
     * @param str $differ
     * @param str $title
     * @param str $description
     * @return bool
     */	
	public function createAlbum($differ='', $parent_id, $title, $description, $user_id){

		$inCore = cmsCore::getInstance();

		$ns = $inCore->nestedSetsInit('cms_photo_albums');
		$album_id = $ns->AddNode($parent_id, -1, $differ);
		
		if(!$album_id) { return false; }
		
		$sql = "UPDATE cms_photo_albums
				SET title='$title',
					description='$description',
					published=1,
					showdate=1,
					iconurl='',
					pubdate=NOW(),
					orderby='pubdate',
					orderto='desc',
					public=1,
					perpage=20,
					cssprefix='',
					thumb1=96,
					thumb2=600,
					thumbsqr=1,
					showtype='thumb',
					nav=1,
					uplimit=100,
					maxcols=5,
					orderform=0,
					showtags=1,
					bbcode=0,
					user_id = '$user_id'
				WHERE id = '$album_id'";

		$this->inDB->query($sql);

		return true;

	}
// ============================================================================ //
// ============================================================================ //
    /**
     * Возвращает id корневого альбома для заданного $differ
     * @param str $differ
     * @return int $album_id
     */	
    public function getRootAlbumId($differ){
		
		return $this->inDB->get_field('cms_photo_albums', "parent_id=0 AND NSDiffer = '$differ'", 'id');

    }
// ============================================================================ //
// ============================================================================ //
    /**
     * Возвращает массив заданного альбома
     * @param int $album_id
     * @return array $album
     */	
    public function getAlbum($album_id){
		
		return $this->inDB->get_fields('cms_photo_albums', "id = '$album_id'", '*');

    }
// ============================================================================ //
// ============================================================================ //
    /**
     * Редактирует альбом
     * @param int $album_id
     * @param int $parent_id
     * @param str $differ
     * @param str $title
     * @param str $description
     * @return bool
     */	
	public function editAlbum($album_id, $title, $description, $parent_id,  $differ=''){
		
		if(!$album_id) { return false; }

		$inCore = cmsCore::getInstance();

		$old_parent = $this->inDB->get_field('cms_photo_albums', "id = '$album_id'", 'parent_id');
		
		if ($parent_id && $old_parent != $parent_id){

			$ns = $inCore->nestedSetsInit('cms_photo_albums');
			$ns->MoveNode($album_id, $parent_id, -1, $differ);

		}
	
		$sql = "UPDATE cms_photo_albums
				SET title='$title',
					description='$description'
				WHERE id = '$album_id'";

		$this->inDB->query($sql);

		return true;

	}
// ============================================================================ //
// ============================================================================ //
    /**
     * Удаляет альбом
     * @param int $album_id
     * @param str $differ
     * @return bool
     */	
    public function deleteAlbum($album_id, $differ = '') {

		if(!$album_id) { return false; }

		$inCore = cmsCore::getInstance();

		//устанавливаем нужный альбом
		$this->whereAlbumIs($album_id);

        $photos = $this->getPhotos(true);

        if ($photos){
            foreach($photos as $photo){
                $this->deletePhoto($photo['id'], $photo['file']);
            }
        }
		
		$inCore->deleteComments('palbum', $album_id);

		$ns = $inCore->nestedSetsInit('cms_photo_albums');

		return $ns->DeleteNode($album_id, $differ);

    }
// ============================================================================ //
// ============================================================================ //
    /**
     * Возвращает массив фотографий по заданным условиям
     * @param bool $show_all
     * @return array $photos
     */	
	public function getPhotos($show_all = false){

		$inCore = cmsCore::getInstance();

        //подготовим условия
        $pub_where = ($show_all ? '1=1' : 'f.published = 1');
        $left_join = strstr($this->where, 'rt.') ? "LEFT JOIN cms_ratings_total rt ON rt.item_id = f.id AND rt.target='photo'" : '';

        $sql = "SELECT f.id, f.album_id, f.title, f.description, f.pubdate, f.file, f.hits

                FROM cms_photo_files f
				{$left_join}
                WHERE {$pub_where}
                      {$this->where}

                {$this->group_by}                      

                {$this->order_by}\n";

        if ($this->limit){
            $sql .= "LIMIT {$this->limit}";
        }

		$result = $this->inDB->query($sql);

		if(!$this->inDB->num_rows($result)){ return false; }

		$photos = array();

		while ($photo = $this->inDB->fetch_assoc($result)){

			$photo['pubdate'] = $inCore->dateFormat($photo['pubdate']);
			$photos[] = $photo;

		}

		$this->resetConditions();

		return $photos;

	}
/* ========================================================================== */
/* ========================================================================== */

    public function getPhotosCount($show_all = false){

        //подготовим условия
        $pub_where = ($show_all ? '1=1' : 'f.published = 1');

        $sql = "SELECT 1

                FROM cms_photo_files f

                WHERE {$pub_where}
                      {$this->where}

                {$this->group_by}\n";

		$result = $this->inDB->query($sql);

		return $this->inDB->num_rows($result);

    }
// ============================================================================ //
// ============================================================================ //
    
}
?>
