<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_bestblogs($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        global $_LANG;
		if (!function_exists('cmsKarmaFormat')){ //if not included earlier
			include($_SERVER['DOCUMENT_ROOT'].'/core/lib_karma.php');
		}		
		
		$cfg = $inCore->loadModuleConfig($module_id);

        if (!isset($cfg['namemode'])) { $cfg['namemode'] = 'blog';}
        if (!isset($cfg['shownum'])) { $cfg['shownum'] = 10; }
        if (!isset($cfg['menuid'])) { $cfg['menuid'] = 0; }
		
		if (!isset($cfg['shownum'])){
			echo '<p>'.$_LANG['BESTBLOGS_CONFIG_TEXT'].'</p>';
			return;
		}

        $inCore->loadModel('blogs');
        $model = new cms_model_blogs();

		$sql = "SELECT  p.*,
                        b.title as blog,
                        b.id as blog_id,
                        b.seolink as bloglink,
                        b.owner as owner,
                        b.user_id as uid, 
                        p.pubdate as fpubdate,
                        IFNULL(r.total_rating, 0) as points,
                        b.owner as owner,
                        b.ownertype as ownertype,
                        u.nickname as author
				FROM cms_blogs b
				LEFT JOIN cms_blog_posts p ON p.blog_id = b.id AND p.published = 1				
				LEFT JOIN cms_ratings_total r ON r.item_id=p.id AND r.target='blogpost'
				LEFT JOIN cms_users u ON u.id=b.user_id
				WHERE b.allow_who = 'all'
				ORDER BY points DESC";
		
		$sql .= "\n" . "LIMIT ".$cfg['shownum'];
	
		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){	
			$posts = array();
			while($con = $inDB->fetch_assoc($result)){
				$next = sizeof($posts);

                if ($con['owner']=='club'){
                    $con['blog'] = dbGetField('cms_clubs', 'id='.$con['uid'], 'title');
                }

				$text = strip_tags($con['title']);							
				if (strlen($text)>70) { $text = substr($text, 0, 70). '...'; }				

				$posts[$next]['title'] = $text;
                $posts[$next]['href'] = $model->getPostURL(null, $con['bloglink'], $con['seolink']);

                if ($con['owner']=='user' && $con['ownertype']=='single' && $cfg['namemode']=='user'){
                    $con['blog'] = $con['author'];
                }

				$posts[$next]['blog'] = $con['blog'];
                $posts[$next]['bloghref'] = $model->getBlogURL(null, $con['bloglink']);

				$posts[$next]['karma'] = cmsKarmaFormat($con['points']);
				$posts[$next]['date'] = $inCore->dateFormat($con['fpubdate']);								
			
			}
			
			$smarty = $inCore->initSmarty('modules', 'mod_bestblogs.tpl');			
			$smarty->assign('posts', $posts);
			$smarty->display('mod_bestblogs.tpl');

		} else { echo '<p>'.$_LANG['BESTBLOGS_NOT_POSTS'].'</p>'; }
				
		return true;
}
?>