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

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function applet_repair(){

    $inCore = cmsCore::getInstance();
    $inConf = cmsConfig::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }

	include_once($_SERVER['DOCUMENT_ROOT'].'/admin/dbstructure.php');					//correct database structure

	if (isset($_POST['runsql'])){	
		include_once($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');					//correct database structure
		$sql = $_POST['sql'];
		$file = $_SERVER['DOCUMENT_ROOT'].'/backups/repair.sql';		
		file_put_contents($file, $sql);			
		dbRunSQL($file, $inConf->db_prefix);
	}

/* -------------- Check tables fields ---------------------------------------------------------------- */

	$errors = false;
	$repair_sql = '';

	$tables_sql = "SHOW TABLES";	
	$tables_result = dbQuery($tables_sql);	

	ob_start();
	
	while($table = mysql_fetch_row($tables_result)){

		$current_table = $table[0];
        $current_table = str_replace($inConf->db_prefix, '#', $current_table);
		
		$fields_sql = "SHOW COLUMNS FROM {$table[0]}";	
		$fields_result = dbQuery($fields_sql);
		
		$f = 0;
        
		while($field = mysql_fetch_assoc($fields_result)){
			foreach($field as $key=>$value){
				$mytables[$current_table][$f][$key] = $value;

                // Строка для генерации dbstructure.php:
                // echo '$tables[\''.$current_table.'\'][\''.$f.'\'][\''.$key.'\'] = \''.$value.'\';<br/>';
                // (не забыть убрать ob_start и ob_get_clean)
			}
			$f++;
		}

		$diff = @array_diff_key($tables[$current_table], $mytables[$current_table]);

        //echo '<pre>'; print_r($diff); echo '</pre>';
		
		if ($diff){
			$errors = true;
			echo '<div style="margin:3px; padding:20px; border:solid 1px silver">';
			echo '<div>Таблица <strong>"'.str_replace('#', $inConf->db_prefix, $current_table).'"</strong> отличается по структуре!</div>';
			echo '</div>';
		}
	}

	$text = ob_get_clean();

/* -------------- Check missed tables ---------------------------------------------------------------- */	
	
	ob_start();
	
	$missed = '';

	$diff   = @array_diff_key($tables, $mytables);
	
	if ($diff){
		echo '<div style="margin:3px; padding:20px; border:solid 1px silver">';
		foreach($diff as $key=>$data){
			$errors = true;
            $key    = str_replace('#', $inConf->db_prefix, $key);
			echo '<li style="list-style:none">Таблица <strong>"'.$key.'"</strong> не существует!</li>';
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
					$default = ($item['Default']) ? "DEFAULT '{$item['Default']}'" : '';
					
					$repair_sql .= "`".$item['Field']."` ".$item['Type']." ".$default." ".$null." ".$auto_inc;
					if ($f < sizeof($data) || $is_id) { $repair_sql .= ", "; }
					$repair_sql .= "\n";
				}			
			if ($is_id){
				$repair_sql .= "PRIMARY KEY  (`id`)" . "\n";
			}
			$repair_sql .= ") ENGINE=MyISAM DEFAULT CHARSET=cp1251;" . "\n\n";
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
			echo '<h3>Следующие SQL-запросы добавят отсутствующие таблицы БД:</h3>';
			echo '<form action="" method="post" style="margin-bottom:20px">';
				echo '<input type="hidden" name="runsql" value="1"/>';
				echo '<textarea style="width:90%;height:250px" name="sql">'.trim($repair_sql).'</textarea>';
			echo '</form>';
            echo '<div style="margin-bottom:12px;font-size:14px;color:gray;">';
                echo '<div>Скопируйте эти запросы и выполните в своей базе данных через <strong>phpMyAdmin</strong> (вкладка SQL).</div>';
                echo '<span>Настоятельно рекомендуем предварительно сделать <a href="index.php?view=backup">резервную копию базы</a>.</span>';
            echo '</div>';

		}
	}

}
?>