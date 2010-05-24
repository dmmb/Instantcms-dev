<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_lastreg($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;

		$cfg = $inCore->loadModuleConfig($module_id);

		$sql = "SELECT u.*, p.imageurl, DATE_FORMAT(regdate, '%d-%m-%Y (%H:%i)') as fdate 
				FROM cms_users u, cms_user_profiles p
				WHERE u.is_deleted = 0 AND u.is_locked=0 AND p.user_id = u.id
				ORDER BY u.regdate DESC
				LIMIT ".$cfg['newscount']."
				";		
		
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){	
			if ($cfg['view_type']=='table'){
				include_once($_SERVER['DOCUMENT_ROOT'].'/components/users/includes/usercore.php');
			
				echo '<table cellspacing="5" border="0">';
				while($usr = $inDB->fetch_assoc($result)){
					echo '<tr>';
						echo '<td width="20" class="new_user_avatar">'.usrImageNOdb($usr['id'], 'small', $usr['imageurl'], $usr['is_deleted']).'</td>';
						echo '<td width="">';
							echo '<a href="'.cmsUser::getProfileURL($usr['login']).'" class="new_user_link">'.$usr['nickname'].'</a>';
						echo '</td>';				
					echo '</tr>';
				}
				echo '</table>';
			}
			if ($cfg['view_type']=='list'){
				$total = $inDB->num_rows($result);
				$now = 0;
				while($usr = $inDB->fetch_assoc($result)){				
					echo '<a href="'.cmsUser::getProfileURL($usr['login']).'">'.$usr['nickname'].'</a>';
					if ($now < $total-1) { echo ', '; }
					$now ++;
				}
				echo '<p><strong>'.$_LANG['LASTREG_TOTAL'].':</strong> '.dbRowsCount('cms_users', 'is_deleted=0 AND is_locked=0').'</p>';
			}
		} else { echo '<p>'.$_LANG['LASTREG_NOT_DATA'].'</p>'; }
		
				
		return true;
	
	}
?>