<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_photos{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentTarget($target, $target_id) {

        $result = array();

        switch($target){

            case 'palbum': $album           = $this->inDB->get_fields('cms_photo_albums', "id='{$target_id}'", 'title');
                           if (!$album) { return false; }
                           $result['link']  = '/photos/'.$target_id;
                           $result['title'] = $album['title'];
                           break;

            case 'photo':  $photo           = $this->inDB->get_fields('cms_photo_files', "id='{$target_id}'", 'title');
                           if (!$photo) { return false; }
                           $result['link']  = '/photos/photo'.$target_id.'.html';
                           $result['title'] = $photo['title'];
                           break;

        }

        return ($result ? $result : false);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deletePhoto($id, $file=''){
        
        $inCore = cmsCore::getInstance();

        cmsCore::callEvent('DELETE_PHOTO', $id);

        if (!$file){
            $file = $this->inDB->get_field('cms_photo_files', "id='{$id}'", 'file');
        }
        if (!$file){ return false; }

        @chmod($_SERVER['DOCUMENT_ROOT'].'/images/photos/'.$file, 0777);
        @chmod($_SERVER['DOCUMENT_ROOT'].'/images/photos/small/'.$file, 0777);
        @chmod($_SERVER['DOCUMENT_ROOT'].'/images/photos/medium/'.$file, 0777);

        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/'.$file);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/small/'.$file);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/medium/'.$file);

		$inCore->deleteComments('photo', $id);
		$inCore->deleteRatings('photo', $id);

        cmsActions::removeObjectLog('add_photo', $id);

        $sql = "DELETE FROM cms_photo_files WHERE id = '$id'";
        $this->inDB->query($sql) ;

        cmsClearTags('photo', $id);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deletePhotos($id_list){

        cmsCore::callEvent('DELETE_PHOTOS', $id_list);

        foreach($id_list as $key=>$id){
            $this->deletePhoto($id);
        }

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updatePhoto($id, $photo){
        $inCore     = cmsCore::getInstance();
        $inUser     = cmsUser::getInstance();

        $photo      = cmsCore::callEvent('UPDATE_PHOTO', $photo);

        $sql = "UPDATE cms_photo_files
                SET album_id = '{$photo['album_id']}',
                    title='{$photo['title']}',
                    file='{$photo['filename']}',
                    description='{$photo['description']}',
                    published='{$photo['published']}',
                    showdate='{$photo['showdate']}'
                WHERE id = '$id'
                LIMIT 1";
        $this->inDB->query($sql);

        cmsInsertTags($photo['tags'], 'photo', $id);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function addPhoto($photo){

        $inCore     = cmsCore::getInstance();
        $inUser     = cmsUser::getInstance();

        $photo      = cmsCore::callEvent('ADD_PHOTO', $photo);

        $user_id    = $inUser->id;

        $sql = "INSERT INTO cms_photo_files (album_id, title, description, pubdate, file, published, showdate, comments, user_id)
                VALUES ('{$photo['album_id']}', '{$photo['title']}', '{$photo['description']}', NOW(),
                        '{$photo['filename']}', '{$photo['published']}', '{$photo['showdate']}', 1, '{$user_id}')";
        
        $this->inDB->query($sql);

        $photo_id = $this->inDB->get_last_id('cms_photo_files');
        
        cmsInsertTags($photo['tags'], 'photo', $photo_id);

        cmsUser::checkAwards($inUser->id);

        $album_title = $this->inDB->get_field('cms_photo_albums', "id='{$photo['album_id']}'", 'title');
		
		if ($photo['published']) {
			cmsActions::log('add_photo', array(
				  'object' => $photo['title'],
				  'object_url' => '/photos/photo'.$photo_id.'.html',
				  'object_id' => $photo_id,
				  'target' => $album_title,
				  'target_url' => '/photos/'.$photo['album_id'],
				  'description' => '<a href="/photos/photo'.$photo_id.'.html" class="act_photo">
										<img border="0" src="/images/photos/small/'.$photo['filename'].'" />
									  </a>'
			));
		}

        return $photo_id;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function randPhoto($album_id, $is_sub=false){

        $catsql = 'AND f.album_id = '.$album_id;

		if ($is_sub && $album_id != '0') {
            $rootcat = $this->inDB->get_fields('cms_photo_albums', 'id='.$album_id, 'NSLeft, NSRight, NSDiffer');
			if ($rootcat['NSDiffer']){
            $catsql = " AND a.NSLeft >= {$rootcat['NSLeft']} AND a.NSRight <= {$rootcat['NSRight']} AND a.NSDiffer = '{$rootcat['NSDiffer']}'";
			} else {
			$catsql = " AND a.NSLeft >= {$rootcat['NSLeft']} AND a.NSRight <= {$rootcat['NSRight']} AND a.NSDiffer = ''";
			}
        }

		$sql = "SELECT f.file
				FROM cms_photo_files f, cms_photo_albums a
				WHERE f.published = 1 AND f.album_id = a.id ".$catsql."
				ORDER BY RAND()
				LIMIT 1";
					
        $result = $this->inDB->query($sql);

		if ($this->inDB->num_rows($result)){
			$photo_url = $this->inDB->fetch_assoc($result);
		} else { return false; }

        return $photo_url['file'];
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function deleteAlbum($id){
        
        $inCore = cmsCore::getInstance();

        cmsCore::callEvent('DELETE_ALBUM', $id);

        $album  = $this->inDB->get_fields('cms_photo_albums', "id = '$id'", 'NSDiffer, NSLeft, NSRight');

        if (!$album) { return false; }

        $sql    = "SELECT f.id as id, f.file as file
                   FROM cms_photo_files f, cms_photo_albums a 
                   WHERE f.album_id = a.id AND a.NSDiffer = '{$album['NSDiffer']}'
                         AND a.NSLeft >= {$album['NSLeft']} AND a.NSRight <= {$album['NSRight']}";
        $result = $this->inDB->query($sql);

        if ($this->inDB->num_rows($result)){
            while($photo = $this->inDB->fetch_assoc($result)){
                $this->deletePhoto($photo['id'], $photo['file']);
            }
        }
		
		$inCore->deleteComments('palbum', $id);

        dbDeleteNS('cms_photo_albums', $id);
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function updateAlbum($id, $album){
        $inCore = cmsCore::getInstance();

        $album = cmsCore::callEvent('UPDATE_ALBUM', $album);

        $ns = $inCore->nestedSetsInit('cms_photo_albums');
        $ns->MoveNode($id, $album['parent_id']);

        $sql = "UPDATE cms_photo_albums
                SET title='{$album['title']}',
                    description='{$album['description']}',
                    published='{$album['published']}',
                    showdate='{$album['showdate']}',
					iconurl='{$album['iconurl']}',
                    orderby='{$album['orderby']}',
                    orderto='{$album['orderto']}',
                    public='{$album['public']}',
                    perpage='{$album['perpage']}',
                    cssprefix='{$album['cssprefix']}',
                    thumb1='{$album['thumb1']}',
                    thumb2='{$album['thumb2']}',
                    thumbsqr='{$album['thumbsqr']}',
                    showtype='{$album['showtype']}',
                    nav='{$album['nav']}',
                    uplimit='{$album['uplimit']}',
                    maxcols='{$album['maxcols']}',
                    orderform='{$album['orderform']}',
                    showtags='{$album['showtags']}',
                    bbcode='{$album['bbcode']}',
                    is_comments='{$album['is_comments']}'
                WHERE id = '$id'";
        
        $this->inDB->query($sql);
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getAlbumThumbsData($album_id){
        return $this->inDB->get_fields('cms_photo_albums', "id = '{$album_id}'", 'thumb1, thumb2, thumbsqr');
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function addAlbum($album){
        $inCore = cmsCore::getInstance();

        $album  = cmsCore::callEvent('ADD_ALBUM', $album);

        $ns = $inCore->nestedSetsInit('cms_photo_albums');
		$id = $ns->AddNode($album['parent_id']);

		if ($id){
			$sql = "UPDATE cms_photo_albums
					SET title='{$album['title']}',
						description='{$album['description']}',
						published='{$album['published']}',
						showdate='{$album['showdate']}',
						iconurl='',
						pubdate=NOW(),
						orderby='{$album['orderby']}',
						orderto='{$album['orderto']}',
						public='{$album['public']}',
						perpage='{$album['perpage']}',
						cssprefix='{$album['cssprefix']}',
						thumb1='{$album['thumb1']}',
						thumb2='{$album['thumb2']}',
						thumbsqr='{$album['thumbsqr']}',
						showtype='{$album['showtype']}',
						nav='{$album['nav']}',
						uplimit='{$album['uplimit']}',
						maxcols='{$album['maxcols']}',
						orderform='{$album['orderform']}',
						showtags='{$album['showtags']}',
						bbcode='{$album['bbcode']}',
                        is_comments='{$album['is_comments']}'
					WHERE id = '$id'";
			$this->inDB->query($sql);
		}

        return $id;
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getSubAlbums($parent_id, $left_key, $right_key, $albums_orderby, $albums_orderto) {

        $subcats    = array();

        $differ     = $this->inDB->get_field('cms_photo_albums', "id='{$parent_id}'", 'NSDiffer');

        if (!$differ) { $differ = ''; }

        $sql = "SELECT a.*, IFNULL(COUNT(f.id), 0) as content_count
                FROM cms_photo_albums a
                LEFT JOIN cms_photo_files f ON f.album_id = a.id AND f.published = 1
                WHERE (a.parent_id=$parent_id) AND a.published = 1 AND a.NSDiffer = '{$differ}'
                GROUP BY a.id
                ORDER BY $albums_orderby $albums_orderto";

        $result = $this->inDB->query($sql);

        if (!$this->inDB->num_rows($result)) { return false; }

        while($subcat = $this->inDB->fetch_assoc($result)){
            $subcats[] = $subcat;
        }

        $subcats = cmsCore::callEvent('GET_SUBALBUMS', $subcats);

        return $subcats;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getAlbum($id) {

		$album = $this->inDB->get_fields('cms_photo_albums', "id = '$id'", '*');

		if (!$album) { cmsCore::error404(); }

		return $album;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getPhoto($id) {

		$photo = $this->inDB->get_fields('cms_photo_files', "id = '$id'", '*');

		if (!$photo) { cmsCore::error404(); }

		return $photo;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function loadedByUser24h($user_id, $album_id) {

		$loaded = $this->inDB->rows_count('cms_photo_files', "user_id = '$user_id' AND album_id = '$album_id' AND pubdate >= DATE_SUB(NOW(), INTERVAL 1 DAY)");

		return $loaded;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}