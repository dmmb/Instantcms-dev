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

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function checkNestedSet($table){
	$differ = $table['differ'];
	$table	= $table['name'];
	$errors = array();
	
	//step 1
		$sql = "SELECT id FROM $table WHERE NSLeft >= NSRight AND NSDiffer = '$differ'";
		$res = dbQuery($sql);
		if (!mysql_error()) { $errors[] = (mysql_num_rows($res)>0); } else { $errors[] = true; }	
	//step 2 and 3
		$sql = "SELECT COUNT(id) as rows, MIN(NSLeft) as min_left, MAX(NSRight) as max_right FROM $table WHERE NSDiffer = '$differ'";
		$res = dbQuery($sql);
		if (!mysql_error()) { 
			$data = mysql_fetch_assoc($res);
			$errors[] = ($data['min_left'] != 1);
			$errors[] = ($data['max_right'] != 2*$data['rows']);
		} else { $errors[] = true; }	
	//step 4
		$sql = "SELECT id, NSRight, NSLeft
				FROM $table 
				WHERE MOD((NSRight-NSLeft), 2) = 0 AND NSDiffer = '$differ'";
		$res = dbQuery($sql);
		if (!mysql_error()) { $errors[] = (mysql_num_rows($res)>0); } else { $errors[] = true; }	
	//step 5
		$sql = "SELECT id
				FROM $table
				WHERE MOD((NSLeft-NSLevel+2), 2) = 0 AND NSDiffer = '$differ'";
		$res = dbQuery($sql);
		if (!mysql_error()) { $errors[] = (mysql_num_rows($res)>0); } else { $errors[] = true; }							 
	//step 6
		$sql = "SELECT 	t1.id, 
						COUNT(t1.id) AS rep, 
						MAX(t3.NSRight) AS max_right 
				FROM $table AS t1, $table AS t2, $table AS t3 
				WHERE t1.NSLeft <> t2.NSLeft AND t1.NSLeft <> t2.NSRight AND t1.NSRight <> t2.NSLeft AND t1.NSRight <> t2.NSRight 
						AND t1.NSDiffer = '$differ' AND t2.NSDiffer = '$differ' AND t3.NSDiffer = '$differ'
				GROUP BY t1.id 
				HAVING max_right <> SQRT(4 * rep + 1) + 1";
		$res = dbQuery($sql);
		if (!mysql_error()) { $errors[] = (mysql_num_rows($res)>0); } else { $errors[] = true; }		
		return (in_array(true, $errors));					 
}

// ===================================================================================================================================== //

function repairNestedSet($table){
	$inDB   = cmsDatabase::getInstance();
	$differ = $table['differ'];
	$table	= $table['name'];
	
	$root_id = $inDB->get_field($table, "NSDiffer = '$differ' AND (title LIKE '%Корнев%')", 'id');
	
	$sql = "SELECT id
			FROM $table
			WHERE NSDiffer = '$differ' AND (NOT title LIKE '%Корнев%')
			ORDER BY NSLeft";
	$res = dbQuery($sql);
	
	if (!mysql_error()){
		$items_count = mysql_num_rows($res);
		$max_right	 = ($items_count+1) * 2;
		//fix root node
		$sql = "UPDATE $table
				SET NSLeft = 1, 
					NSRight = $max_right,
					parent_id = 0,
					NSLevel = 0,
					ordering = 1
				WHERE id = $root_id";
		dbQuery($sql);
		//fix child nodes
		$pos = 0;
		$ord = 1;
		while ($item = mysql_fetch_assoc($res)){			
			$level = 1;
			$left = $pos + 2;
			$right = $pos + 3;
			$sql = "UPDATE $table 
					SET NSLeft=$left,
						NSRight=$right,
						parent_id = $root_id,
						NSLevel = $level,
						ordering = $ord
					WHERE id=".$item['id'];			
			dbQuery($sql);
			$pos+=2; $ord++;
		}
	}	
}

// ===================================================================================================================================== //

function applet_repairnested(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }

	$tables = array();
	
	$tables[0]['name']	 	= 'cms_category';
	$tables[0]['title']		= 'Дерево разделов для статей';
	$tables[0]['differ']	= '';
	
	$tables[1]['name']	 	= 'cms_photo_albums';
	$tables[1]['title']		= 'Дерево фотоальбомов';
	$tables[1]['differ']	= '';	

	$tables[3]['name']	 	= 'cms_board_cats';
	$tables[3]['title']		= 'Дерево разделов доски объявлений';
	$tables[3]['differ']	= '';	

	$tables[4]['name']	 	= 'cms_uc_cats';
	$tables[4]['title']		= 'Дерево разделов каталога';
	$tables[4]['differ']	= '';

	$tables[5]['name']	    = 'cms_menu';
	$tables[5]['title']	    = 'Дерево меню';
	$tables[5]['differ']	= '';

	$tables[6]['name']	    = 'cms_forums';
	$tables[6]['title']	    = 'Дерево форумов';
	$tables[6]['differ']	= '';

	if($inCore->isComponentInstalled('maps')){
		$tables[7]['name']	    = 'cms_map_cats';
		$tables[7]['title']	    = 'Дерево категорий InstantMaps';
		$tables[7]['differ']	= '';
	}

	if($inCore->isComponentInstalled('video')){
		$tables[8]['name']	    = 'cms_video_category';
		$tables[8]['title']	    = 'Дерево категорий InstantVideo';
		$tables[8]['differ']	= '';
	}

	if($inCore->isComponentInstalled('shop')){
		$tables[9]['name']	    = 'cms_shop_cats';
		$tables[9]['title']	    = 'Дерево категорий InstantShop';
		$tables[9]['differ']	= '';
	}

	if (isset($_POST['go_repair']) && isset($_POST['tables'])){
		if (is_array($_POST['tables'])){
			foreach ($_POST['tables'] as $key=>$table_id){
				repairNestedSet($tables[$table_id]);
			}
		}
	}

	$GLOBALS['cp_page_title'] = 'Проверка деревьев';
 	
	cpAddPathway('Настройки сайта', 'index.php?view=config');	
	cpAddPathway('Проверка деревьев', 'index.php?view=repairnested');	
	
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/admin/js/repair.js"></script>';

	$errors_found = false;	
	
	echo '<h3>Проверка целостности деревьев БД</h3>';
	
	echo '<div style="margin:20px; margin-top:0px;">';
	echo '<form method="post" action="" id="repairform">';
		echo '<input type="hidden" name="go_repair" value="1">';
		echo '<table cellpadding="2">';
			foreach($tables as $id=>$table){
				$errors = checkNestedSet($table);
				echo '<tr>';
					echo '<td width="15">'.($errors ? '<input type="checkbox" name="tables[]" value="'.$id.'" checked="checked"/>' : '').'</td>';
					echo '<td><div>';
						echo '<span>'.$table['title'].'</span> &mdash; ' . ($errors ? '<span style="color:red">найдены ошибки!</span>' : '<span style="color:green">ошибок не найдено</span>');		
					echo '</div></td>';
				echo '</tr>';
				if ($errors) { $errors_found = true; }
			}
		echo '</table>';
	echo '</div>';	
	
	if ($errors_found){
		echo '<div style="margin-bottom:20px">';
			echo '<input type="button"  onclick="repairTrees()" value="Исправить выбранные">';
		echo '</div>';
	}
	
}

