<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
	
function search_photos($query, $look){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$searchModel = cms_model_search::initModel();

		$sql = "SELECT f.*, a.title as cat, a.id as cat_id
				FROM cms_photo_files f
				INNER JOIN cms_photo_albums a ON a.id = f.album_id
				WHERE MATCH(f.title, f.description) AGAINST ('$query' IN BOOLEAN MODE) AND f.published = 1";

		$result = $inDB->query($sql);
		
		if ($inDB->num_rows($result)){

			while($item = $inDB->fetch_assoc($result)){

				$result_array = array();

				$result_array['link']        = "/photos/photo".$item['id'].".html";
				$result_array['place']       = $item['cat'];
				$result_array['placelink']   = '/photos/'.$item['cat_id'];
				$result_array['description'] = $searchModel->getProposalWithSearchWord($item['description']);
				$result_array['title']       = $item['title'];
				$result_array['pubdate']     = $item['pubdate'];
				$result_array['session_id']  = session_id();

				$searchModel->addResult($result_array);
			}
		}
		
		return;
}


?>