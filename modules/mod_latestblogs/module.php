<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
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
                        IF(DATE_FORMAT(p.pubdate, '%d-%m-%Y')=DATE_FORMAT(NOW(), '%d-%m-%Y'),
						DATE_FORMAT(p.pubdate, '<strong>{$_LANG['TODAY']}</strong> {$_LANG['IN']} %H:%i'), DATE_FORMAT(p.pubdate, '%d-%m-%Y'))  as fpubdate,
						b.user_id as uid,
                        IFNULL(SUM(r.points), 0) as rating,
                        b.owner as owner,
                        b.ownertype as ownertype,
                        u.nickname as author
				FROM cms_users u, cms_blogs b, cms_blog_posts p
                LEFT JOIN cms_ratings r ON r.item_id=p.id AND r.target='blogpost'
				WHERE p.blog_id = b.id AND b.allow_who = 'all' AND p.published = 1
                GROUP BY p.id
				ORDER BY p.pubdate DESC
                LIMIT 50";
		
		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){	
			echo '<table cellspacing="2" cellpadding="4" border="0">';

            $count = 1;

			while($con = $inDB->fetch_assoc($result)){

                if ($count > $cfg['shownum']) { break; }

                if ($con['rating'] >= $cfg['minrate']){

                    if ($con['owner']=='user' && $con['ownertype']=='single' && $cfg['namemode']=='user'){
                        $con['blog'] = $con['author'];
                    }

                    if ($con['owner']=='club'){
                        $con['blog'] = dbGetField('cms_clubs', 'id='.$con['uid'], 'title');
                    }

                    $link = $model->getPostURL(null, $con['bloglink'], $con['seolink']);
                    $text = strip_tags($con['title']);

                    if (strlen($text)>70) { $text = substr($text, 0, 70). '...'; }
                    echo '<tr>';
                        echo '<td valign="top">';
                        echo '<a class="mod_blog_userlink" href="'.$model->getBlogURL(null, $con['bloglink']).'">'.$con['blog'].'</a> &rarr; ';
                        echo '<a class="mod_blog_link" href="'.$link.'">'.$text.'</a> ('.$con['fpubdate'].')</td>';
                    echo '</tr>';

                    $count++;

                }

			}

			echo '</table>';

			if ($cfg['showrss']){
				echo '<table align="right" style="margin-top:5px"><tr>';
					echo '<td width="16"><img src="/images/markers/rssfeed.png" /></td>';
					echo '<td><a href="/rss/blogs/all/feed.rss" style="text-decoration:underline;color:#333">'.$_LANG['LATESTBLOGS_RSS'].'</a></td>';
				echo '</tr></table>';
			}				
		} else { echo '<p>'.$_LANG['LATESTBLOGS_NOT_POSTS'].'</p>'; }
		
		return true;
}
?>