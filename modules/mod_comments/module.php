<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_comments($module_id){
    
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
        
		$cfg = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
		if (!isset($cfg['minrate'])) { $cfg['minrate'] = 0;}
		if (!isset($cfg['showguest'])) { $cfg['showguest'] = 0;}

		$targeting = sizeof($cfg['targets']);

        if (!$targeting){ echo '<p>'.$_LANG['COMMENTS_NOT_SHOWTYPE'].'</p>'; return true; }

        $sql            = '';
        $target_where   = '';
        $t              = 1;

        foreach($cfg['targets'] as $key=>$value){
            if ($t === 1) { $target_where .= " AND ("; }
            $target_where .= 'c.target="'.$value.'"';
            if ($t <= sizeof($cfg['targets'])-1) { $target_where .= " OR "; } else { $target_where .= ")"; }
            $t++;
        }

        $guest_sql = $cfg['showguest'] ? "OR c.guestname<>''" : "";

		$sql = "SELECT c.id as id,
                       c.target as target,
                       c.target_id as target_id,
                       c.target_link as target_link, 
					   c.target_title,
                       c.content as content,
                       c.guestname,
                       c.pubdate as fpubdate,
                       IFNULL(c.user_id, 0) as user_id,
                       IFNULL(u.nickname, '') as author,
                       IFNULL(u.login, '') as author_login,
                       IFNULL(v.total_rating, 0) as rating
				FROM cms_users u, cms_comments c
                LEFT JOIN cms_ratings_total v ON v.item_id=c.id AND v.target='comment'
				WHERE (c.user_id=u.id {$guest_sql}) AND c.published=1 {$target_where}
                GROUP BY c.id
                ORDER BY c.pubdate DESC
                LIMIT 70";

        $result = $inDB->query($sql);
		$is_com = false;
        if ($inDB->num_rows($result)){
			$is_com = true;
            $count = 0;
			$comments = array();
            while($con = $inDB->fetch_assoc($result)){

                if ($count >= $cfg['shownum']) { break; }

                if ($con['rating'] >= $cfg['minrate']){

                    $con['link'] = $con['target_link'] . '#c'.$con['id'];
                    $con['text'] = strip_tags($con['content']);
                    if (strlen($con['text'])>60) { $con['text'] = substr($con['text'], 0, 60). '...'; }
                    if (!$con['text']) { $con['text'] = '...'; }

                    $con['user_url'] = $con['user_id'] ? cmsUser::getProfileURL($con['author_login']) : $con['link'];
                    $con['author']   = $con['user_id'] ? $con['author'] : $con['guestname'];
                    $con['fpubdate'] = $inCore->dateFormat($con['fpubdate']);

					$comments[] = $con;
                    $count++;
                }

            }

            }
		
		$smarty = $inCore->initSmarty('modules', 'mod_comments.tpl');			
		$smarty->assign('comments', $comments);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_com', $is_com);
		$smarty->display('mod_comments.tpl');	
				
		return true;
}
?>