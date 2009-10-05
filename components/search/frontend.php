<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function pageBar($current, $perpage, $sql, $query, $look, $limitStr){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$html = '';
	
	$sql = str_replace($limitStr, '', $sql);
	
	$result = $inDB->query($sql) ;
	$records = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);
		
		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					
					$link = '/index.php?view=search&query='.$query.'&look='.$look.'&menuid='.$inCore->menuId().'&page='.$p;
					
					$html .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

function pageBarTags($records, $current, $perpage, $query=''){
	$html = '';
	
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					
					$link = '/search/tag/'.urlencode($query).'/page'.$p.'.html';
					
					$html .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

function search(){

    $inCore     = cmsCore::getInstance();
    $inPage     = cmsPage::getInstance();
    $inDB       = cmsDatabase::getInstance();
    
	$inCore->loadLib('tags');

	$menuid     = $inCore->menuId();
	$cfg        = $inCore->loadComponentConfig('search');

    $query  = $inCore->request('query', 'str', '');
    $look   = $inCore->request('look', 'str', 'allwords');
    $mode   = $inCore->request('mode', 'str', 'text');

	//PREPARE QUERY
	$query = trim($query);
	$query = str_replace('\'', '', $query);
	$query = str_replace('"', '', $query);

	if ($menuid==0){
		if ($mode=='text'){
			$inPage->addPathway('Поиск', $_SERVER['REQUEST_URI']);	
		} else {
			$inPage->addPathway('Поиск по тегу', $_SERVER['REQUEST_URI']);	
		}
	}
	
	//GET PAGING OPTIONS
	$perpage    = $cfg['perpage'];
    $page       = $inCore->request('page', 'int', 1);
	
	unset($_SESSION['searchquery']);

	$inPage->setTitle('Поиск');

	echo '<div class="con_heading">Поиск</div>';
	
	if ($mode == 'text'){
        
		//PRINT SEARCH FORM
		echo '<form action="/index.php" method="GET" style="clear:both">';
			echo '<strong>Найти на сайте:</strong> ';
			echo '<input type="hidden"
						 name="view"
						 value="search" class="search_input"/>';
			echo '<input type="text" 
						 name="query"
						 size="60"
						 value="'.$query.'" 
						 /> ';
			echo '<select name="look" style="width:200px">
					<option value="allwords" '.($look=='allwords'||$look==''?'selected="selected"':'').'>Все слова</option>
                    <option value="anyword" '.($look=='anyword'?'selected="selected"':'').'>Любое слово</option>
					<option value="phrase" '.($look=='phrase'?'selected="selected"':'').'>Фраза целиком</option>
				  </select> ';
			echo '<input type="hidden" name="menuid" value="'.$menuid.'"/>';
			echo '<input type="submit" value="Найти"/>';
			
		echo '</form>';
		
		//DROP PREVIOUS RESULTS
		$inDB->query("DELETE FROM cms_search WHERE session_id = '".session_id()."'");
		
		if (strlen($query)>3){

            //Prepare query for full-text
            $against = '';

            $inCore->includeFile('includes/stemmer/stemmer.php');
            $stemmer = new Lingua_Stem_Ru();

            $words = split(' ', $query);

            //SEARCH IN THREADS TITLES
            if ($look == 'anyword' || $look == 'allwords'){
                if ($look == 'anyword') { $clue = ''; } else { $clue = '+'; }
                foreach($words as $w){
                    if(strlen($w)>1){
                        $against .= $clue.$stemmer->stem_word($w).'* ';
                    }
                }
            }

            if ($look == 'phrase'){
                $against .= '\"'.$query.'\"';
            }

			//RUN SEARCH PROCESSORS
			//get list of components and look for search processor in component folder
			$sql = "SELECT link FROM cms_components";
			$rs = $inDB->query($sql) ;
			if ($inDB->num_rows($rs)){
				while ($component = $inDB->fetch_assoc($rs)){
					$spfile = $_SERVER['DOCUMENT_ROOT'].'/components/'.$component['link'].'/psearch.php';
					if (file_exists($spfile)){
						if (in_array($component['link'], $cfg['comp'])){
							include $spfile;
							eval('search_'.$component['link'].'("'.$against.'", "'.$look.'", "'.$mode.'");');
						}
					}
				}
			}
					
			//OUTPUT SEARCH RESULTS	
			$sql = "SELECT DISTINCT *
					FROM cms_search
					WHERE session_id = '".session_id()."'
					ORDER BY place ASC
					";
					
			$total_rs   = $inDB->query($sql);
			$total      = $inDB->num_rows($total_rs);
					
			$sql       .= "LIMIT ".(($page-1)*$perpage).", $perpage";
					
			$rs         = $inDB->query($sql) or die(mysql_error().'<pre>'.$sql);
			$found      = $inDB->num_rows($rs);

            $num        = ($page-1)*$perpage;

			if ($found){
				echo '<p style="margin-bottom:20px"><strong>Найдено материалов:</strong> '.$total.'</p>';

				while ($item = $inDB->fetch_assoc($rs)){

                    $num++;

                    echo '<div style="margin-bottom:15px">';
                        echo '<div style="font-size:16px;font-weight:bold;" >
                                <span style="width:40px;display:block;float:left">'.$num.'.</span>
                                <a href="'.$item['link'].'" target="_blank">'.$item['title'].'</a>
                              </div>';
                        echo '<div style="margin-top:4px;margin-left:40px">
                                <a style="color:gray" href="'.$item['placelink'].'">'.$item['place'].'</a>
                                &mdash <span style="color:green">http://'.$_SERVER['HTTP_HOST'].$item['link'].'</span>
                              </div>';
                    echo '</div>';
                    
				}

                echo '<div style="margin-top:30px">';
                    echo pageBar($page, $perpage, $sql, $query, $look, "LIMIT ".(($page-1)*$perpage).", $perpage");
                echo '</div>';
				
				$_SESSION['searchquery'] = $query;
			} else {
				echo '<p>По запросу "'.$query.'" ничего не найдено. <a href="http://www.yandex.ru/yandsearch?text='.urlencode($query).'" target="_blank">Поискать в Яндексе?</a></p>';
			}
							
		} else {
			if (strlen($query)>0){
				echo '<p><strong>Ошибка:</strong> <span style="color:red">слишком короткий запрос!</span></p>';
			}
		}
	}
	
	if ($mode == 'tag'){
	
		if (!strlen($query)){
			echo '<p>Пустой поисковый запрос</p>';
		} else {
		
			if (isset($cfg['perpage'])) { $perpage = $cfg['perpage']; } else { $perpage = 20; }				
			if (isset($_REQUEST['page'])) { $page = abs((int)$_REQUEST['page']); } else { $page = 1; }		
		
			echo '<p style="padding:5px;padding-bottom:0px"><strong>Поиск по тегу:</strong> &laquo;'.$query.'&raquo;</p>';
		
			$sql = "SELECT * 
					FROM cms_tags
					WHERE tag = '$query' 
					ORDER BY tag DESC";
			$rs = $inDB->query($sql) ;
			
			$found = $inDB->num_rows($rs);
						
			if ($found){

					$sql = "SELECT * 
							FROM cms_tags 
							WHERE tag = '$query' 
							ORDER BY tag DESC
							LIMIT ".(($page-1)*$perpage).", $perpage";
					$rs = $inDB->query($sql) ;
						
					echo '<p style="padding:5px;padding-top:0px"><strong>Найдено материалов:</strong> '.$found;
					if ($perpage < $found){
						echo '<br/><strong>Показаны:</strong> '.((($page-1)*$perpage)+1).' &mdash; '.((($page-1)*$perpage)+$perpage);
					}
					echo '</p>';
					echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
						echo '<tr>';
							echo '<td class="search_head"><strong>Найдено:</strong></td>';
						echo '</tr>';
					$row = 1;
					while ($item = $inDB->fetch_assoc($rs)){
						if ($itemlink = cmsTagItemLink($item['target'], $item['item_id'])){
							if($row % 2 == 0) { $class = 'search_row2'; } else { $class = 'search_row1'; }
							echo '<tr>';
								echo '<td class="'.$class.'">'."\n".
										'<div class="tagsearch_item">';
										echo '<table><tr>';
											echo '<td><img src="/components/search/tagicons/'.$item['target'].'.gif"/></td>';
											echo '<td>'.$itemlink.'</td>';
										echo '</tr></table>';
										echo '</div>'."\n".
										'<div class="tagsearch_bar">'.cmsTagBar($item['target'], $item['item_id'], $query).'</div>'."\n".
									  '</td>'."\n";
							echo '</tr>';
							$row++;
						}
					}
					echo '</table>';
					
					echo pageBarTags($found, $page, $perpage, $query);
                    
				} else {
					echo '<p>По тегу "'.$query.'" ничего не найдено. <a href="http://www.yandex.ru/yandsearch?text='.urlencode($query).'" target="_blank">Поискать в Яндексе?</a></p>';
				}		
		
		}
	}
	
	return true;
}
?>