<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

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

        $t_list = array();

		foreach($cfg['targets'] as $type){
			$t_list[] = "'$type'";
        }

		$t_list = rtrim(implode(',', $t_list), ',');

		$target_where = "AND c.target IN ({$t_list})";

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
				FROM cms_comments c
				INNER JOIN cms_users u ON u.id = c.user_id {$guest_sql}
                LEFT JOIN cms_ratings_total v ON v.item_id=c.id AND v.target='comment'
				WHERE c.published=1 {$target_where}
                GROUP BY c.id
                ORDER BY c.id DESC
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
                    
                    $con['text'] =  preg_replace('/\[hide\](.*?)\[\/hide\]/i', '', $con['text']);
                    $con['text'] =  preg_replace('/\[hide\](.*?)$/i', '', $con['text']);

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