<?php 
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function icon($icon, $link, $title, $onClick=''){
	
	if ($onClick==''){
		$html = '<a class="icon" title="'.$title.'" href="'.$link.'"><img border="0" src="/images/icons/'.$icon.'.png" alt="'.$title.'"></a>';
	} else {
		$html = '<a class="icon" title="'.$title.'" href="'.$link.'" onClick="'.$onClick.'"><img border="0" src="/images/icons/'.$icon.'.png" alt="'.$title.'"></a>';	
	}
	return $html;	

}

function inArray($array, $item){

	$found = false;
	foreach($array as $key=>$value){
		if ($value == $item) { 
			$found = true;
		}			
	}
	return $found;

}

function pagelinks($table, $perpage, $current, $first_url=''){

    $inDB       = cmsDatabase::getInstance();

	$result     = $inDB->query("SELECT id FROM $table") ;
	$records    = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);
		if($pages>1){
			echo '<div style="margin-top:10px; margin-bottom: 15px; font-size:10px">';
			echo '<span style="margin:5px; padding:4px"><strong>Страницы: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					if ($first_url == '') { $link = $_SERVER['PHP_SELF'].'?page='.$p; }
					else { $link = $_SERVER['PHP_SELF'].$first_url.'&page='.$p; }
					echo ' <a href="'.$link.'" style="margin:5px; padding:5px">'.$p.'</a> ';		
				} else {
					echo '<span style="margin:5px; padding:5px; border:solid 1px silver; background-color: white">'.$p.'</span>';
				}
			}
			echo '</div>';
		}
	}
}


?>