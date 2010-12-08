<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function mod_tags($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
		$cfg = $inCore->loadModuleConfig($module_id);

		$targeting = sizeof($cfg['targets']);
		
		if(!isset($cfg['minfreq'])) { $cfg['minfreq']=0; }	
		if(!isset($cfg['minlen'])) { $cfg['minlen'] = 3; }
        if(!isset($cfg['maxtags'])) { $cfg['maxtags'] = 20; }

		$is_targeting = false;

		if ($targeting){
		
			$is_targeting = true;
			
			$sql = "SELECT t.*, COUNT(t.tag) as num
					FROM cms_tags t
					WHERE ";	
	
			$t=1;
			foreach($cfg['targets'] as $key=>$value){
				if ($t === 1) { $sql .= " ("; }
				if ($value=='blog')
				{
				$sql .= 't.target="blogpost"';	
				}
				else
				{
				$sql .= 't.target="'.$value.'"';	
				}
				
				if ($t <= sizeof($cfg['targets'])-1) { $sql .= " OR "; } else { $sql .= ")"; }
				$t++;
			}

			$sql .= "\n" . "GROUP BY t.tag";
			if ($cfg['sortby'] == 'tag') { $sql .= "\n"." ORDER BY tag ASC"; } else { $sql .= "\n"." ORDER BY num DESC"; }

            $sql .= " LIMIT ".$cfg['maxtags'];

			$result = $inDB->query($sql);
			
			//$maxsize = 55;
			//$minsize = 10;
					
			$size = array();
			for ($s=0; $s<10; $s++) { $size[] = 10 + ($s*4); }
			
			$is_tags = false;
			
			if ($inDB->num_rows($result)){
				$is_tags = true;
				
				$tags = array();
				$summary = 0;
				while($tag = $inDB->fetch_assoc($result)){
					if (strlen($tag['tag'])>=$cfg['minlen']){
						$next = sizeof($tags);
						$tags[$next]['title'] = $tag['tag'];
						$tags[$next]['num'] = $tag['num'];
						$summary += $tag['num'];
					}
				}
	
				$tags_sel = array();
				
				foreach($tags as $key=>$value){
					
					$tag = $tags[$key]['title'];
					$num = $tags[$key]['num'];
					
					if ($num>$cfg['minfreq']){
						$prc = ceil(($num / $summary) * 100);
	
						for ($s=0; $s<10; $s++){
							if ($prc >= ($s*10)) { $fontsize = $size[$s]; }
						}
									
						$next = sizeof($tags_sel);
						$tags_sel[$next]['title'] = $tag;
						$tags_sel[$next]['num'] = $num;
						$tags_sel[$next]['fontsize'] = $fontsize;

					}
				}
				
			
		}
		} 
		
		$smarty = $inCore->initSmarty('modules', 'mod_tags.tpl');			
		$smarty->assign('tags', $tags_sel);
		$smarty->assign('is_tags', $is_tags);
		$smarty->assign('is_targeting', $is_targeting);
		$smarty->display('mod_tags.tpl');
				
		return true;
	
}
?>