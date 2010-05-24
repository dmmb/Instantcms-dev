<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
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
                LIMIT 100";

        $result = $inDB->query($sql);

        if ($inDB->num_rows($result)){

            $count = 0;

            echo '<table cellspacing="2" cellpadding="4" border="0">';
            while($con = $inDB->fetch_assoc($result)){

                if ($count >= $cfg['shownum']) { break; }

                if ($con['rating'] >= $cfg['minrate']){

                    $link = $con['target_link'] . '#c'.$con['id'];
                    $text = strip_tags($inCore->parseSmiles($con['content'], true));

                    if (strlen($text)>50) { $text = substr($text, 0, 50). '...'; }

                    if (!$text) { $text = '...'; }

                    $user_url = $con['user_id'] ? cmsUser::getProfileURL($con['author_login']) : $link;
                    $author   = $con['user_id'] ? $con['author'] : $con['guestname'];

                    echo '<tr>';
                        echo '<td valign="top">';
                            echo '<div><a class="mod_com_userlink" href="'.$user_url.'">'.$author.'</a> &rarr; ';
                        echo '<a class="mod_com_link" href="'.$link.'">'.$text.'</a> ('.$inCore->dateFormat($con['fpubdate']).')</td>';
                    echo '</tr>';

                    $count++;

                }

            }
            echo '</table>';

            if ($cfg['showrss']){
                echo '<table align="right" style="margin-top:5px"><tr>';
                    echo '<td width="16"><img src="/images/markers/rssfeed.png" /></td>';
                    echo '<td><a href="/rss/comments/all/feed.rss" style="text-decoration:underline;color:#333">'.$_LANG['COMMENTS_RSS'].'</a></td>';
                echo '</tr></table>';
            }
        } else { echo '<p>'.$_LANG['COMMENTS_NOT_COMM'].'</p>'; }
				
		return true;
}
?>