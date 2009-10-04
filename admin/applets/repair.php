<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

function applet_repair(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }

	include_once($_SERVER['DOCUMENT_ROOT'].'/admin/dbstructure.php');					//correct database structure

	if (isset($_POST['runsql'])){	
		include_once($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');					//correct database structure
		$sql = $_POST['sql'];
		$file = $_SERVER['DOCUMENT_ROOT'].'/backups/repair.sql';		
		file_put_contents($file, $sql);			
		dbRunSQL($file);
	}

/* -------------- Check tables fields ---------------------------------------------------------------- */

	$errors = false;
	$repair_sql = '';

	$tables_sql = "SHOW TABLES";	
	$tables_result = dbQuery($tables_sql);	
	
	ob_start();
	
	while($table = mysql_fetch_row($tables_result)){
		$current_table = $table[0];
		
		$fields_sql = "SHOW COLUMNS FROM {$table[0]}";	
		$fields_result = dbQuery($fields_sql);
		
		$f = 0;
		while($field = mysql_fetch_assoc($fields_result)){					
			foreach($field as $key=>$value){
				$mytables[$current_table][$f][$key] = $value;
			}
			$f++;			
		}

		$diff = array_diff_key($tables[$current_table], $mytables[$current_table]);			
		
		if ($diff){
			$errors = true;		
			echo '<div style="margin:3px; padding:20px; border:solid 1px silver">';
			echo '<div>Таблица <strong>"'.$current_table.'"</strong> отличается по структуре!</div>';			
			
			echo '<div>Не хватает полей:</div>';
			echo '<ul>';
			foreach($diff as $item){
				$null = ($item['Null'] == 'NO') ? 'NOT NULL' : 'NULL'; 
				$default = ($item['Default']) ? 'DEFAULT \''.$item['Default'].'\'' : ''; 
				$repair_sql .= "ALTER TABLE `".$current_table."` ADD `".$item['Field']."` ".$item['Type']." ".$default." ".$null."; \n\n";				
				echo '<li style="margin-bottom:4px">'.$item['Field'].' <div style="font-size:10px;color:#666">'.$item['Type'].' DEFAULT `'.$item['Default'].'` '.$null.'</div></li>';
			}	
			echo '</ul>';					
			echo '</div>';
		}
	}	
	
	$text = ob_get_clean();
	
/* -------------- Check missed tables ---------------------------------------------------------------- */	
	
	ob_start();
	
	$missed = '';
	$diff = array_diff_key($tables, $mytables);
	
	if ($diff){
		echo '<div style="margin:3px; padding:20px; border:solid 1px silver">';
		foreach($diff as $key=>$data){
			$errors = true;
			echo '<li>Таблица <strong>"'.$key.'"</strong> не существует!</li>';			
			$repair_sql .= "CREATE TABLE `$key` (" . "\n";			
				$f = 0; $is_id = false;
				foreach($data as $item){
					$f++;
					if ($item['Field']=='id') { 
						$is_id = true; 
						$auto_inc = 'auto_increment';
					} else { 
						$auto_inc = '';
					}
					
					$null = ($item['Null'] == 'NO') ? 'NOT NULL' : 'NULL'; 
					$default = ($item['Default']) ? 'DEFAULT \''.$item['Default'].'\'' : ''; 
					
					$repair_sql .= "`".$item['Field']."` ".$item['Type']." ".$default." ".$null." ".$auto_inc;
					if ($f < sizeof($data) || $is_id) { $repair_sql .= ", "; }
					$repair_sql .= "\n";
				}			
			if ($is_id){
				$repair_sql .= "PRIMARY KEY  (`id`)" . "\n";
			}
			$repair_sql .= ") ENGINE=MyISAM DEFAULT CHARSET=cp1251;" . "\n";					
		}
		echo '</div>';
	}
	
	$missed = ob_get_clean();
	
/* -------------- Show resume ---------------------------------------------------------------- */	

	echo '<h3>Проверка целостности БД</h3>';
	
	cpAddPathway('Проверка БД', 'index.php?view=repair');

	if (!$errors) { 
		echo '<h3 style="color:green">Структура БД совпадает с эталоном.</h3>'; 
		echo '<div style="margin-top:3px;margin-bottom:30px">
				Все таблицы имеют нужные поля. Изменения не требуются.
			  </div>';
	} else {
		if ($text){
			echo '<h3 style="color:red">Структура БД нарушена!</h3>';
			echo $text;
		}
		if ($missed){
			echo '<h3 style="color:red">Отсутствуют таблицы!</h3>';
			echo $missed;	
		}
		if ($repair_sql){
			echo '<h3>Следующие SQL-запросы восстановят структуру БД:</h3>';
			echo '<form action="" method="post" style="margin-bottom:20px">';
				echo '<input type="hidden" name="runsql" value="1"/>';
				echo '<textarea style="width:90%;height:250px" name="sql">'.trim($repair_sql).'</textarea>';
				echo '<div style="margin-top:5px;margin-bottom:3px">';
					echo '<input type="submit" style="padding:5px;" name="go" value="Выполнить запросы в БД"/> ';
					echo '<span><strong>Внимание!</strong> Настоятельно рекомендуем сделать <a href="index.php?view=backup">бекап базы</a> перед нажатием на эту кнопку!</span>';
				echo '</div>';
			echo '</form>';
		}
	}

}
?>