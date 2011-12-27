<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
	
function search_board($query, $look){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$searchModel = cms_model_search::initModel();

		$sql = "SELECT f.*, f.title as title, a.title as cat, a.id as cat_id
				FROM cms_board_items f
				INNER JOIN cms_board_cats a ON a.id = f.category_id
				WHERE MATCH(f.title, f.content) AGAINST ('$query' IN BOOLEAN MODE) AND f.published = 1 LIMIT 100";

		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){

			while($item = $inDB->fetch_assoc($result)){

				$result_array = array();

				$result_array['link']        = "/board/read".$item['id'].".html";
				$result_array['place']       = $item['cat'];
				$result_array['placelink']   = '/board/'.$item['cat_id'];
				$result_array['description'] = $searchModel->getProposalWithSearchWord($item['content']);
				$result_array['title']       = $item['title'];
				$result_array['pubdate']     = $item['pubdate'];
				$result_array['session_id']  = session_id();

				$searchModel->addResult($result_array);		
			}
		}
		
		return;
}


?>