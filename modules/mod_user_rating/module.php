<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_user_rating($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

        if (!isset($cfg['count'])) { $cfg['count'] = 20; }
		if (!isset($cfg['view_type'])) { $cfg['view_type'] = 'rating'; }	

		if ($cfg['view_type']!='rating' && $cfg['view_type']!='karma') {
			$cfg['view_type'] = 'rating';
		}
		
		if ($cfg['view_type'] == 'rating') { $target = 'Рейтинг'; } else { $target = 'Карма'; }

		$sql = "SELECT u.*, p.karma as karma, p.user_id, u.rating as rating, p.karma as karma
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_deleted = 0 AND u.is_locked = 0 AND p.user_id = u.id
				ORDER BY ".$cfg['view_type']." DESC
				LIMIT ".$cfg['count'];		
		$result = $inDB->query($sql);
		
		if (@$inDB->num_rows($result)){	
				include_once($_SERVER['DOCUMENT_ROOT'].'/components/users/includes/usercore.php');
			
				echo '<table cellspacing="5" border="0" class="mod_user_rating">';
				while($usr = $inDB->fetch_assoc($result)){
					echo '<tr>';
						echo '<td width="20" class="avatar">'.usrImage($usr['id']).'</td>';
						echo '<td width="">';
						echo '<div>';
							echo '<a href="'.cmsUser::getProfileURL($usr['login']).'" class="nickname">'.$usr['nickname'].'</a>';
							if ($cfg['view_type'] == 'rating'){
								echo '<div class="rating">'.$usr[$cfg['view_type']].'</div>';
							} elseif ($usr[$cfg['view_type']]>0) {
								echo '<div class="karma"><span style="color:green">+'.$usr[$cfg['view_type']].'</span></div>';
							} elseif ($usr[$cfg['view_type']]==0) {
								echo '<div class="karma"><span style="color:gray">'.$usr[$cfg['view_type']].'</span></div>';
							} else {
								echo '<div class="karma"><span style="color:red">'.$usr[$cfg['view_type']].'</span></div>';							
							}
						echo '</div>';				
					echo '</tr>';
				}
				echo '</table>';
		} else { echo '<p>Нет данных для отображения.</p>'; }
				
		return true;
	
	}
?>