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

function mod_arhive($module_id){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$cfg    = $inCore->loadModuleConfig($module_id);

		global $_LANG;

		$rootcat = $inDB->get_fields('cms_category', "id='{$cfg['cat_id']}'", 'NSLeft, NSRight');
		if(!$rootcat) { return false; }
		$catsql = "AND (cat.NSLeft >= {$rootcat['NSLeft']} AND cat.NSRight <= {$rootcat['NSRight']})";

		$today = date("Y-m-d H:i:s");

		$sql = "SELECT DATE_FORMAT( con.pubdate, '%M, %Y' ) fdate, DATE_FORMAT( con.pubdate, '%Y' ) year, DATE_FORMAT( con.pubdate, '%m' ) month, COUNT( con.id ) num
				FROM cms_content con
				INNER JOIN cms_category cat ON cat.id = con.category_id {$catsql}"."\n";
				
		if($cfg['cat_id']>0){
			$sql .= "WHERE con.published = 1  AND con.pubdate <= '$today'";
			if ($cfg['source']!='both'){
				if ($cfg['source']=='arhive'){
					$sql .= " AND con.is_arhive = 1". "\n";
				} else {
					$sql .= " AND con.is_arhive = 0". "\n";
				}				
			} else {
				$sql .= "\n";
			}
		}
		
		$sql .= "GROUP BY DATE_FORMAT(con.pubdate, '%M, %Y')"."\n";
		$sql .= "ORDER BY con.pubdate DESC";
		
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)>0){		
			while ($item = $inDB->fetch_assoc($result)){
				$item['fdate'] = $inCore->getRusDate($item['fdate']);
				echo '<div class="arhive_month"><a href="/arhive/'.$item['year'].'/'.$item['month'].'">'.$item['fdate'].'</a> ('.$item['num'].')</div>';
			
			}		
		} else { echo '<p>'.$_LANG['ARHIVE_NOT_MATERIAL'].'</p>'; }
		return true;
}
?>