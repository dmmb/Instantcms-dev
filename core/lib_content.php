<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS') && !defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function cmsAutoCreateThread($article, $content_cfg){

    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    
    if (!$content_cfg['af_on']){ return false; }
    if (!$content_cfg['af_forum_id']){ return false; }
    if ($article['category_id'] == $content_cfg['af_hidecat_id']) { return false; }

    $link   = '[URL=http://'.$_SERVER['HTTP_HOST'].'/content/0/read'.$article['id'].'.html]'.$article['title'].'[/URL]';

    $topic  = $article['title'];
    $post   = 'В этой теме форума обсуждаем статью &quot;'.$link.'&quot;';

    $sql = "INSERT INTO cms_forum_threads (forum_id, user_id, title, description, icon, pubdate, hits, rel_to, rel_id)
            VALUES ('{$content_cfg['af_forum_id']}', '".$inUser->id."', '$topic', '', '', NOW(), 0, 'content', ".$article['id'].")";
    $inDB->query($sql);

    $threadlastid = $inDB->get_last_id('cms_forum_threads');

    $sql = "INSERT INTO cms_forum_posts (thread_id, user_id, pubdate, editdate, edittimes, content)
            VALUES ('$threadlastid', '".$inUser->id."', NOW(), NOW(), 0, '$post')";
    $inDB->query($sql);

    return true;
}

?>