<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function mod_comments($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        
		$cfg = $inCore->loadModuleConfig($module_id);

		if ($cfg['menuid']>0) {
			$menuid = $cfg['menuid'];
		} else {
			$menuid = $inCore->menuId();
		}

		if (!isset($cfg['showrss'])) { $cfg['showrss'] = 1;}
		if (!isset($cfg['minrate'])) { $cfg['minrate'] = 0;}

		$sql = "SELECT c.id as id,
                       c.target as target,
                       c.target_id as target_id,
                       c.content as content, 
                       IF(DATE_FORMAT(c.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'), DATE_FORMAT(c.pubdate, '<strong>Сегодня</strong> в %H:%i'),
                       DATE_FORMAT(c.pubdate, '%d-%m-%Y'))  as fpubdate,
                       u.id as user_id,
                       u.nickname as author,
                       u.login as author_login,
                       IFNULL(SUM(v.vote), 0) as rating
				FROM cms_users u, cms_comments c
                LEFT JOIN cms_comments_votes v ON v.comment_id=c.id
				WHERE c.user_id=u.id AND c.published=1
                GROUP BY c.id, v.comment_id";
				
		$targeting = sizeof($cfg['targets']);
		
		if ($targeting){
			$t=1;
			foreach($cfg['targets'] as $key=>$value){
				if ($t === 1) { $sql .= " AND ("; }
				$sql .= 'c.target="'.$value.'"';
				if ($t <= sizeof($cfg['targets'])-1) { $sql .= " OR "; } else { $sql .= ")"; }
				$t++;
			}
		
			$sql .= "\n" . "ORDER BY c.pubdate DESC";
			$sql .= "\n" . "LIMIT 100";
		
			$result = $inDB->query($sql);
            
			if ($inDB->num_rows($result)){
            
                $count = 0;

				echo '<table cellspacing="2" cellpadding="4" border="0">';
				while($con = $inDB->fetch_assoc($result)){

                    if ($count > $cfg['shownum']) { break; }

                    if ($con['rating'] >= $cfg['minrate']){

                        $link = $inCore->getCommentLink($con['target'], $con['target_id'], false, true);
                        $text = strip_tags($inCore->parseSmiles($con['content'], true));

                        if (strlen($text)>50) { $text = substr($text, 0, 50). '...'; }

                        if (!$text) { $text = '...'; }

                        echo '<tr>';
                            echo '<td valign="top">';
                                echo '<div><a class="mod_com_userlink" href="'.cmsUser::getProfileURL($con['author_login']).'">'.$con['author'].'</a> &rarr; ';
                            echo '<a class="mod_com_link" href="'.$link.'">'.$text.'</a> ('.$con['fpubdate'].')</td>';
                        echo '</tr>';

                        $count++;

                    }

				}
				echo '</table>';

                if ($cfg['showrss']){
					echo '<table align="right" style="margin-top:5px"><tr>';
						echo '<td width="16"><img src="/images/markers/rssfeed.png" /></td>';
						echo '<td><a href="/rss/comments/all/feed.rss" style="text-decoration:underline;color:#333">Лента комментариев</a></td>';
					echo '</tr></table>';
				}
			} else { echo '<p>Нет комментариев для отображения.</p>'; }

		} else {
			echo '<p>Не выбран тип комментариев для показа.</p>';
		}
				
		return true;
}
?>