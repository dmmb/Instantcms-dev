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
	
function search_price($query, $look){
    
        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$searchModel = cms_model_search::initModel();

		$sql = "SELECT i.*, c.title as cat, c.id as cat_id
				FROM cms_price_items i
				INNER JOIN cms_price_cats c ON c.id = i.category_id
				WHERE MATCH(i.title) AGAINST ('$query' IN BOOLEAN MODE)";

		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){
			while($item = $inDB->fetch_assoc($result)){

				$result_array = array();

				$result_array['link']        = "/price/".$item['cat_id'];
				$result_array['place']       = $item['cat'];
				$result_array['placelink']   = $result_array['link'];
				$result_array['title']       = $item['title'];
				$result_array['session_id']  = session_id();

				$searchModel->addResult($result_array);
			
			}
		}
		
		return;
        
}


?>