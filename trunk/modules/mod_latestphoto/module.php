<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_latestphoto($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		if ($cfg['menuid']>0) {
			$menuid = $cfg['menuid'];
		} else {
			$menuid = $inCore->menuId();
		}

        $catsql = '';

		if ($cfg['album_id'] != '0') {
            $rootcat = $inDB->get_fields('cms_photo_albums', 'id='.$cfg['album_id'], 'NSLeft, NSRight');
            $catsql = " AND a.NSLeft >= {$rootcat['NSLeft']} AND a.NSRight <= {$rootcat['NSRight']}";
        }

		if (!isset($cfg['showtype'])) { $cfg['showtype'] = 'full'; }
		if (!isset($cfg['showmore'])) { $cfg['showmore'] = 1; }
        if (!isset($cfg['showclubs'])) { $cfg['showclubs'] = 1; }

        if (!$cfg['showclubs']) { $catsql .= " AND a.NSDiffer=''"; }
		
		$col = 1; $maxcols = $cfg['maxcols'];

		$sql = "SELECT f.*, IF(DATE_FORMAT(f.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(f.pubdate, '<strong>Сегодня</strong>, %H:%i'), DATE_FORMAT(f.pubdate, '%d-%m-%Y'))  as fpubdate, a.id as album_id, a.title as album
				FROM cms_photo_files f, cms_photo_albums a
				WHERE f.published = 1 AND f.album_id = a.id ".$catsql."
				ORDER BY pubdate DESC
				LIMIT ".$cfg['shownum'];		
 	
		$result = $inDB->query($sql);
			
		if ($inDB->num_rows($result)){	
			$photos = array();			
			echo '<table cellspacing="2" border="0" width="100%">';
			while($con = $inDB->fetch_assoc($result)){
				if ($col==1) { echo '<tr>'; } echo '<td align="center" valign="middle" class="mod_lp_photo" width="'.round(100/$maxcols, 0).'%">';
				echo '<table width="100%" height="100" cellspacing="0" cellpadding="0">';
				if ($cfg['showtype']=='full'){
					echo '<tr><td align="center"><div class="mod_lp_titlelink"><a href="/photos/'.$menuid.'/photo'.$con['id'].'.html" title="'.$con['title'].'">'.$con['title'].'</a></div></td></tr>';
				}
				echo '<tr>
					  <td valign="middle" align="center">';
						echo '<a href="/photos/'.$menuid.'/photo'.$con['id'].'.html" title="'.$con['title'].'">';
							echo '<img class="photo_thumb_img" src="/images/photos/small/'.$con['file'].'" alt="'.$con['title'].'" border="0" />';
						echo '</a>';
				echo '</td></tr>';
				if ($cfg['showtype']=='full'){
					echo '<tr>';
					echo '<td align="center">';
						if($cfg['showalbum']){
							echo '<div class="mod_lp_albumlink"><a href="/photos/'.$menuid.'/'.$con['album_id'].'" title="'.$con['album'].'">'.$con['album'].'</a></div>';
						}
						if($cfg['showcom'] || $cfg['showdate']){
							echo '<div class="mod_lp_details">';
							echo '<table cellpadding="2" cellspacing="2" align="center" border="0"><tr>';
								if ($cfg['showdate']){
									echo '<td><img src="/images/icons/date.gif" border="0"/></td>';
									echo '<td>'.$con['fpubdate'].'</td>';
								}
								if ($cfg['showcom']){
									echo '<td><img src="/images/icons/comments.gif" border="0"/></td>';
									echo '<td><a href="/photos/'.$menuid.'/photo'.$con['id'].'.html#c">'.$inCore->getCommentsCount('photo', $con['id']).'</td>';
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
				echo '<div style="text-align:right"><a style="text-decoration:underline" href="/photos/'.$menuid.'/latest.html">Все новые фото</a> &rarr;</div>';
			}
		} else { echo '<p>Нет материалов для отображения.</p>'; }
		
				
		return true;
	
}
?>