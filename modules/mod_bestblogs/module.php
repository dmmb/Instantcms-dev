<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_bestblogs($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();

		if (!function_exists('cmsKarmaFormat')){ //if not included earlier
			include($_SERVER['DOCUMENT_ROOT'].'/core/lib_karma.php');
		}		
		
		$cfg = $inCore->loadModuleConfig($module_id);

		if ($cfg['menuid']>0) {
			$menuid = $cfg['menuid'];
		} else {
			$menuid = $inCore->menuId();
		}
		
		if (!isset($cfg['shownum'])){
			echo '<p>������� ��������� ������ � ������ ����������.</p>';
			return;
		}

        $inCore->loadModel('blog');
        $model = new cms_model_blog();

		$sql = "SELECT p.*, b.title as blog, b.id as blog_id, b.seolink as bloglink, IF(DATE_FORMAT(p.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'),	DATE_FORMAT(p.pubdate, '<strong>�������</strong> � %H:%i'),
						DATE_FORMAT(p.pubdate, '%d-%m-%Y'))  as fpubdate, IFNULL(SUM(r.points), 0) as points
				FROM cms_blogs b, cms_blog_posts p
				LEFT JOIN cms_ratings r ON r.item_id=p.id AND r.target='blogpost'
				WHERE p.blog_id = b.id AND b.allow_who = 'all'
				GROUP BY p.id
				ORDER BY points DESC";
		
		$sql .= "\n" . "LIMIT ".$cfg['shownum'];
	
		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){	
			$posts = array();
			while($con = $inDB->fetch_assoc($result)){
				$next = sizeof($posts);
				
				$text = strip_tags($con['title']);							
				if (strlen($text)>70) { $text = substr($text, 0, 70). '...'; }				

				$posts[$next]['title'] = $text;
                $posts[$next]['href'] = $model->getPostURL($menuid, $con['bloglink'], $con['seolink']);
				
				$posts[$next]['blog'] = $con['blog'];
                $posts[$next]['bloghref'] = $model->getBlogURL($menuid, $con['bloglink']);

				$posts[$next]['karma'] = cmsKarmaFormat($con['points']);
				$posts[$next]['date'] = $con['fpubdate'];								
			
			}
			
			$smarty = $inCore->initSmarty('modules', 'mod_bestblogs.tpl');			
			$smarty->assign('posts', $posts);
			$smarty->display('mod_bestblogs.tpl');

		} else { echo '<p>��� ������� � ������ ��� �����������.</p>'; }
				
		return true;
}
?>