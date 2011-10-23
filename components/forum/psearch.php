<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
	
function search_forum($query, $look){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$searchModel = cms_model_search::initModel();

        global $_LANG;
		$inCore->loadLanguage('components/forum');

		// »щем в названи€х тем
		$sql = "SELECT t.*, f.title as forum, f.id as forum_id, f.access_list
				FROM cms_forum_threads t
				INNER JOIN cms_forums f ON f.id = t.forum_id
				WHERE MATCH(t.title) AGAINST ('$query' IN BOOLEAN MODE) AND t.is_hidden=0 LIMIT 50";

		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){
			while($item = $inDB->fetch_assoc($result)){

				if(!$inCore->checkContentAccess($item['access_list'])) { continue; }

				$result_array = array();

				$result_array['link']        = "/forum/thread".$item['id'].".html";
				$result_array['place']       = $item['forum'];
				$result_array['placelink']   = "/forum/".$item['forum_id'];
				$result_array['title']       = $item['title'];
				$result_array['pubdate']     = $item['pubdate'];
				$result_array['description'] = $searchModel->getProposalWithSearchWord($inCore->parseSmiles($item['description'], true));
				$result_array['session_id']  = session_id();

				$searchModel->addResult($result_array);				
			}
		}
		
		// »щем в тексте постов
		$sql = "SELECT p.*, t.title as thread, t.id as thread_id
				FROM cms_forum_posts p
				INNER JOIN cms_forum_threads t ON t.id = p.thread_id AND t.is_hidden=0
				WHERE MATCH(p.content) AGAINST ('$query' IN BOOLEAN MODE) LIMIT 50";

		$result = $inDB->query($sql); 
		
		if ($inDB->num_rows($result)){
			while($item = $inDB->fetch_assoc($result)){

				$result_array = array();

				$result_array['link']        = "/forum/thread".$item['thread_id'].".html";
				$result_array['place']       = $_LANG['FORUM_POST'];
				$result_array['placelink']   = $result_array['link'];
				$result_array['description'] = $searchModel->getProposalWithSearchWord($inCore->parseSmiles($item['content'], true));
				$result_array['title']       = $item['thread'];
				$result_array['pubdate']     = $item['pubdate'];
				$result_array['session_id']  = session_id();

				$searchModel->addResult($result_array);				
			}
		}
		
		return;
}

?>