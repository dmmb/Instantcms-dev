<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_bestphoto($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
    	global $_LANG;
		$cfg = $inCore->loadModuleConfig($module_id);
		
		if ($cfg['album_id'] != '0') { $catsql = ' AND album_id = '.$cfg['album_id']; }
		else { $catsql = ''; }
		
		if (!isset($cfg['showtype'])) { $cfg['showtype'] = 'full'; }
		if (!isset($cfg['showmore'])) { $cfg['showmore'] = 1; }
		
		$col = 1; $maxcols = $cfg['maxcols'];

		$sql = "SELECT f.*, f.id as fid,
					   IF(DATE_FORMAT(f.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(f.pubdate, '<strong>{$_LANG['TODAY']}</strong>'), DATE_FORMAT(f.pubdate, '%d-%m-%Y'))  as fpubdate,
					   a.id as album_id, a.title as album, 
					   IFNULL(r.total_rating, 0) as rating
				FROM cms_photo_files f
				LEFT JOIN cms_ratings_total r ON r.item_id=f.id AND r.target = 'photo'
				LEFT JOIN cms_photo_albums a ON f.album_id = a.id
				WHERE f.published = 1 ".$catsql."
				GROUP BY f.id
				ORDER BY ".$cfg['sort']." DESC 
				LIMIT ".$cfg['shownum'];		
 	
		$result = $inDB->query($sql) ;
			
		if ($inDB->num_rows($result)){	
			echo '<table cellspacing="2" border="0" width="100%">';
			while($con = $inDB->fetch_assoc($result)){
				$votes = $con[$cfg['sort']];
				if ($col==1) { echo '<tr>'; } echo '<td align="center" valign="middle" class="mod_lp_photo" width="'.round(100/$maxcols, 0).'%">';
				echo '<table width="100%" height="100" cellspacing="0" cellpadding="0">';
				if ($cfg['showtype']=='full'){
					  echo '<tr><td align="center"><div class="mod_lp_titlelink"><a href="/photos/photo'.$con['id'].'.html" title="'.$con['title'].' ('.$con['rating'].')">'.$con['title'].'</a></div></td></tr>';
			  	}
				  echo '<tr>';
				  echo '<td valign="middle" align="center">';
				echo '<a href="/photos/photo'.$con['id'].'.html" title="'.$con['title'].'">';
					echo '<img class="photo_thumb_img" src="/images/photos/small/'.$con['file'].'" alt="'.$con['title'].' ('.$con['rating'].')" border="0" />';
				echo '</a>';
				echo '</td></tr>';
				if ($cfg['showtype']=='full'){
					echo '<tr>';
					echo '<td align="center">';
						if($cfg['showalbum']){
							echo '<div class="mod_lp_albumlink"><a href="/photos/'.$con['album_id'].'" title="'.$con['album'].'">'.$con['album'].'</a></div>';
						}
						if($cfg['showcom'] || $cfg['showdate']){
							echo '<div class="mod_lp_details">';
							echo '<table cellpadding="2" cellspacing="2" align="center" border="0"><tr>';
								if ($cfg['showdate']){
									include_once($_SERVER['DOCUMENT_ROOT'].'/core/lib_karma.php');
									if ($cfg['sort'] == 'rating'){
										echo '<td style="font-weight:bold">'.cmsKarmaFormat($con['rating']).'</td>';
									} else {
										echo '<td>'.$votes.'</td>';
									}
								}
								if ($cfg['showcom']){
									echo '<td><img src="/images/icons/comments.gif" border="0"/></td>';
									echo '<td><a href="/photos/photo'.$con['id'].'.html#c">'.$inCore->getCommentsCount('photo', $con['id']).'</td>';
								}
							echo '</tr></table>';
							echo '</div>';
						}
					echo '</td>';
					echo '</tr>';
				}
				echo '</table>';
				//echo '</div>';
				echo '</td>'; if ($col==$maxcols) { echo '</tr>'; $col=1; } else { $col++; }
			}			
			if ($col>1) { echo '<td colspan="'.($maxcols-$col+1).'">&nbsp;</td></tr>'; }
			echo '</table>';
			if ($cfg['showmore']){
				echo '<div style="text-align:right"><a style="text-decoration:underline" href="/photos/top.html">'.$_LANG['BESTPHOTO_ALL_BEST_PHOTO'].'</a> &rarr;</div>';
			}
		} else { echo '<p>'.$_LANG['BESTCONTENT_NOT_MATERIALS'].'</p>'; }
		
				
		return true;
	
}
?>