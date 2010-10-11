<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_latestblogs($module_id){	
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $cfg = $inCore->loadModuleConfig($module_id);
        global $_LANG;
        $inCore->loadModel('blogs');
        $model = new cms_model_blogs();

		if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
		if (!isset($cfg['minrate'])) { $cfg['minrate'] = 0;}
        if (!isset($cfg['namemode'])) { $cfg['namemode'] = 'blog';}

		if (!isset($cfg['shownum'])){
			echo '<p>'.$_LANG['LATESTBLOGS_CONFIG_TEXT'].'</p>';
			return;
		}

		$sql = "SELECT  p.title as title,
                        p.id as id,
                        p.seolink as seolink, 
                        b.title as blog,
                        b.owner as owner,
                        b.id as blog_id,
                        b.seolink as bloglink,
                        p.pubdate as fpubdate,
						b.user_id as uid,
                        IFNULL(r.total_rating, 0) as rating,
                        b.owner as owner,
                        b.ownertype as ownertype,
                        u.id as author_id,
                        u.nickname as author,
                        up.imageurl as author_image,
                        u.is_deleted as author_deleted,
                        IFNULL(COUNT(cm.id), 0) as comments
				FROM cms_users u, cms_user_profiles up, cms_blogs b, cms_blog_posts p
                LEFT JOIN cms_ratings_total r ON r.item_id=p.id AND r.target='blogpost'
                LEFT JOIN cms_comments cm ON cm.target='blog' AND cm.target_id=p.id
				WHERE p.blog_id = b.id AND
                      b.allow_who = 'all' AND
                      p.published = 1 AND
                      p.user_id = u.id AND
                      p.user_id = up.user_id
                GROUP BY p.id
				ORDER BY p.pubdate DESC
                LIMIT 50";
		
		$result = $inDB->query($sql);
		$is_blog = false;
		if ($inDB->num_rows($result)){	

			$is_blog = true;
            $count = 1;
			$posts = array();

			while($con = $inDB->fetch_assoc($result)){

                include_once($_SERVER['DOCUMENT_ROOT'].'/components/users/includes/usercore.php');

                if ($count > $cfg['shownum']) { break; }

                if ($con['rating'] >= $cfg['minrate']){

                    if ($con['owner']=='user' && $con['ownertype']=='single' && $cfg['namemode']=='user'){
                        $con['blog'] = $con['author'];
                    }

                    if ($con['owner']=='club'){
                        $con['blog'] = dbGetField('cms_clubs', 'id='.$con['uid'], 'title');
                    }

                    $con['href'] 	 = $model->getPostURL(null, $con['bloglink'], $con['seolink']);
                    $con['title'] 	 = strip_tags($con['title']);
					if (strlen($con['title'])>70) { $con['title'] = substr($con['title'], 0, 70). '...'; }
					$con['fpubdate'] = $inCore->dateFormat($con['fpubdate']);
					$con['bloghref'] = $model->getBlogURL(null, $con['bloglink']);

                    $con['image'] = usrImageNOdb($con['author_id'], 'small', $con['author_image'], $con['author_deleted']);

                    $count++;

                }
				$posts[] = $con;
			}

			}				
		
		$smarty = $inCore->initSmarty('modules', 'mod_latestblogs.tpl');			
		$smarty->assign('posts', $posts);
		$smarty->assign('cfg', $cfg);
		$smarty->assign('is_blog', $is_blog);
		$smarty->display('mod_latestblogs.tpl');	
		
		return true;
}
?>