<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function albumCreateRoot($user_id, $differ=''){
    $inDB = cmsDatabase::getInstance();
    $inCore = cmsCore::getInstance();
	$ns = $inCore->nestedSetsInit('cms_photo_albums');
	$album_id = $ns->AddRootNode($differ);
	
	$sql = "UPDATE cms_photo_albums
			SET user_id = $user_id, 
				title = '-Корневой альбом клуба-',
				orderform=0
			WHERE id = $album_id";
	$inDB->query($sql);
	
	return $album_id;
}

function albumCreate($differ='', $parent_id, $title, $description, $user_id){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$ns = $inCore->nestedSetsInit('cms_photo_albums');
	$album_id = $ns->AddNode($parent_id, -1, $differ);
	
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
				perpage=16,
				cssprefix='',
				thumb1=96,
				thumb2=600,
				thumbsqr=1,
				showtype='lightbox',
				nav=1,
				uplimit=100,
				maxcols=4,
				orderform=1,
				showtags=1,
				bbcode=1,
				user_id=$user_id
			WHERE id = $album_id";
	$inDB->query($sql);
	return ($inDB->errno()) ? false: true;
}

function albumEdit($album_id, $parent_id, $title, $description, $differ=''){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$old_parent = dbGetField('cms_photo_albums', 'id='.$album_id, 'parent_id');
	
	if ($old_parent!=$parent_id){
		$ns = $inCore->nestedSetsInit('cms_photo_albums');
		$ns->MoveNode($album_id, $parent_id, -1, $differ);
	}

	$sql = "UPDATE cms_photo_albums
			SET title='$title',
				description='$description'
			WHERE id = $album_id";
	$inDB->query($sql);
	return ($inDB->errno()) ? false: true;
}

function albumDelete($album_id, $differ=''){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$inCore->loadLib('tags');
	$sql = "SELECT id, file FROM cms_photo_files WHERE album_id = $album_id";
	$result = $inDB->query($sql) ;
	//DELETE ALL PHOTOS IN ALBUM
	if ($inDB->num_rows($result)){
		while($photo = $inDB->fetch_assoc($result)){
			cmsClearTags('photo', $photo['id']);	
			$inDB->query("DELETE FROM cms_photo_files WHERE id = ".$photo['id']) ;
            $inCore->deleteComments('photo', $photo['id']);
            $inCore->deleteRatings('photo', $photo['id']);
			@unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/'.$photo['file']);	
			@unlink($_SERVER['DOCUMENT_ROOT'].'/images/photos/thumb/'.$photo['file'].'.jpg');	
		}			
	}
	//DELETE ALBUM
	$ns = $inCore->nestedSetsInit('cms_photo_albums');
    $inCore->deleteComments('palbum', $album_id);
	return $ns->DeleteNode($album_id, $differ);
}

function cmsPhotoList($album){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	ob_start();		
		//SHOW ALBUM CONTENT
		$sql = "SELECT * FROM cms_photo_albums WHERE id = {$album['id']} LIMIT 1";				
		$result = $inDB->query($sql) ;
				
		if ($inDB->num_rows($result)==1){
			$album_row = $inDB->fetch_assoc($result);
																
			//SQL BUILD			
			$sql = "SELECT f.*, IFNULL(r.total_rating, 0) as rating
					FROM cms_photo_files f
					LEFT JOIN cms_ratings_total r ON r.item_id=f.id AND r.target='photo' 
					WHERE f.album_id = {$album['id']} AND f.published = 1
					";
			
			//ORDERING
			if (isset($album['orderby'])) { 
				$orderby = $album['orderby']; 
			} else {
				$orderby = 'pubdate'; 
			}
			
			if (isset($album['orderto'])) { 
				$orderto = $album['orderto']; 
			} else {
				$orderto = 'desc';
			}

			$sql .=  " ORDER BY ".$orderby." ".$orderto." \n";
			
			if ($album['max']){
				$sql .= "LIMIT ".$album['max'];
			}
			
			$result = $inDB->query($sql) ;
			
			$col = 1; $maxcols = $album['maxcols'];
			
			if ($inDB->num_rows($result)){
			
				if ($album['header']){
					echo '<div class="con_photos_header">'.$album['header'].'</div>';
				}
			
				//VIEW AS GALLERY (SIMPLE)			
					echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="con_photos">';
					while($con = $inDB->fetch_assoc($result)){
						$photolink = '/photos/photo'.$con['id'].'.html';
						$photolink2 = '/photos/photo'.$con['id'].'.html';
						if ($col==1) { echo '<tr>'; } echo '<td align="center" valign="middle">';
						echo '<div class="photo_thumb">';
						echo '<table width="100%" height="100" cellspacing="0" cellpadding="0">
							  <tr>
							  <td valign="middle" align="center">';
								echo '<a href="'.$photolink.'" title="'.$con['title'].'">';
									echo '<img class="photo_thumb_img" src="/images/photos/small/'.$con['file'].'" alt="'.$con['title'].'" border="0" />';
								echo '</a>';
						echo '</td></tr>';
						if ($album['titles']){
							echo '<tr><td align="center"><a href="'.$photolink2.'" title="'.$con['title'].'">'.$con['title'].'</a></td></tr>';
						}
						echo '</table>';
						echo '</div>';
						
						echo '</td>'; if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; }
					}
					if ($col>1) { echo '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }
					echo '</table>';
				
			} else { 
					 if($id != $root['id']) { echo '<p>Нет фотографий в этом альбоме.</p>'; }
					}
					
		}//END - ALBUM CONTENT
		
		return ob_get_clean();	
}

?>