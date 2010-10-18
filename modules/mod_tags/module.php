<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
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

		if ($targeting){
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
			
			if ($inDB->num_rows($result)){
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
	
				echo '<div>';
				foreach($tags as $key=>$value){
					
					$tag = $tags[$key]['title'];
					$num = $tags[$key]['num'];
					
					if ($num>$cfg['minfreq']){
						$prc = ceil(($num / $summary) * 100);
	
						for ($s=0; $s<10; $s++){
							if ($prc >= ($s*10)) { $fontsize = $size[$s]; }
						}
									
						echo '<a class="tag" href="/search/tag/'.urlencode($tag).'" style="padding:2px; font-size: '.$fontsize.'px">'.ucfirst($tag).'</a>'."\n";
					}
				}
				echo '</div>';
				
			} else { echo '<p>Нет тегов для отображения</p>'; }
			
		} else {
			echo '<p>Не выбраны источники тегов для показа.</p>';
		}
				
		return true;
	
}
?>