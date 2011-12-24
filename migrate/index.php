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

    session_start();
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    header('Content-Type: text/html; charset=utf-8');
    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    include(PATH.'/core/cms.php');
    include(PATH.'/includes/config.inc.php');

    $inCore = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');
    $inCore->loadClass('db');
    $inCore->loadClass('user');
	$inCore->loadClass('cron');

    $inConf = cmsConfig::getInstance();
    $inDB   = cmsDatabase::getInstance();

	global $_CFG;

    $version_prev = '1.9';
    $version_next = '1.9.1';

	$info_migrate_file = PATH.'/migrate/info.php';
	$array_m = array();
	$M_INFO  = array();

	if (file_exists($info_migrate_file)){

		include($info_migrate_file);

	}

// ========================================================================== //
// ========================================================================== //
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>InstantCMS - Миграция</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<style type="text/css">
	body { font-family:Arial; font-size:14px; }

	a { color: #0099CC; }
	a:hover { color: #375E93; }
	h2 { color: #375E93; }

	#wrapper { padding:10px 30px; }
	#wrapper p{ line-height: 20px; }

	.migrate p { 
				   line-height:16px;
				   padding-left:20px;
				   margin:2px;
				   margin-left:20px;                           
				   background:url(/admin/images/actions/on.gif) no-repeat;
			   }
	.important {
				   margin:20px;
				   margin-left:0px;
				   border:solid 1px silver;
				   padding:15px;
				   padding-left:65px;
				   background:url(important.png) no-repeat 15px 15px;
			   }
	 .nextlink {
				   margin-top:15px;
				   font-size:18px;
	 }
  </style>
<div id="wrapper" class="migrate">
<?php
    echo "<h2>Миграция InstantCMS {$version_prev} &rarr; {$version_next}</h2>";

	$folders = array();
	$folders[] = '/includes';
	$folders[] = '/migrate';

	foreach($folders as $key=>$folder){	
		$right = true;
		if(!@is_writable(PATH.$folder)){
			if (!@chmod(PATH.$folder, 0777)){
					echo '<div style="color:red">Директория "'.$folder.'" недоступна для записи. Миграция приостановлена.</div>';
			}
		}
	}

	if(!@is_writable(PATH.'/includes') || !@is_writable(PATH.'/migrate')){ exit; }

	$filepath = PATH.'/includes/config.inc.php';

	if (file_exists($filepath)){
		if (!@is_writable($filepath)){ echo '<div style="color:red">Файл <strong>'.$filepath.'</strong> недоступен для записи!</div>'; exit; }
	}

	if(!$inCore->inRequest('go')){
	    echo '<h3>Внимание! Будет произведена конвертация базы в UTF-8. Убедитесь, что резервная копия базы создана и сайт выключен для посетителей.</h3>';
		echo '<h3><a href="/migrate/index.php?go=1">продолжить</a></h3>';
		exit;
	}
// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert']){

		$result = $inDB->query('SHOW TABLES');
		while($table = $inDB->fetch_assoc($result)){
			$inDB->query('ALTER TABLE '.$table['Tables_in_'.$inConf->db_base].' CONVERT TO CHARACTER SET utf8 COLLATE utf8_bin');
		}
		$inDB->query('ALTER DATABASE `'.$inConf->db_base.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin');

		echo '<p>База данных сконвертирована в UTF-8, сравнение строк utf8_bin.</p>';
		$M_INFO['is_convert'] = 1;

	}

// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert_cat_photoalbum']){

		$sql = "SELECT id, photoalbum FROM cms_category";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			while($item = $inDB->fetch_assoc($result)){

				$item['photoalbum'] = iconv('utf-8', 'cp1251', $item['photoalbum']);
				$cfg    = unserialize($item['photoalbum']);
				foreach($cfg as $param=>$value){
					$photoalbum[iconv('cp1251', 'utf-8', $param)] = iconv('cp1251', 'utf-8', $value);
				}
				$photoalbum = serialize($photoalbum);
				$photoalbum = $inDB->escape_string($photoalbum);
				$inDB->query("UPDATE cms_category SET photoalbum='{$photoalbum}' WHERE id='{$item['id']}'");
				unset($cfg);
			}
	
		}

		echo '<p>Конвертация настроек категорий выполнена.</p>';
		$M_INFO['is_convert_cat_photoalbum'] = 1;

	}

// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert_pools']){

		$sql = "SELECT id, answers FROM cms_polls";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			while($item = $inDB->fetch_assoc($result)){

				$item['answers'] = iconv('utf-8', 'cp1251', $item['answers']);
				$cfg    = unserialize($item['answers']);
				foreach($cfg as $param=>$value){
					$answers[iconv('cp1251', 'utf-8', $param)] = iconv('cp1251', 'utf-8', $value);
				}
				$answers = serialize($answers);
				$answers = $inDB->escape_string($answers);
				$inDB->query("UPDATE cms_polls SET answers='{$answers}' WHERE id='{$item['id']}'");
				unset($cfg);
			}
	
		}

		echo '<p>Конвертация опросов выполнена.</p>';
		$M_INFO['is_convert_pools'] = 1;

	}

// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert__forum_pools']){

		$sql = "SELECT id, answers, options FROM cms_forum_polls";

		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			while($item = $inDB->fetch_assoc($result)){

				$item['answers'] = unserialize(iconv('utf-8', 'cp1251', $item['answers']));
				foreach($item['answers'] as $param=>$value){
					$answers[iconv('cp1251', 'utf-8', $param)] = iconv('cp1251', 'utf-8', $value);
				}
				$answers = serialize($answers);
				$answers = $inDB->escape_string($answers);

				$item['options'] = unserialize(iconv('utf-8', 'cp1251', $item['options']));
				foreach($item['options'] as $param=>$value){
					$options[iconv('cp1251', 'utf-8', $param)] = iconv('cp1251', 'utf-8', $value);
				}
				$options = serialize($options);
				$options = $inDB->escape_string($options);

				$inDB->query("UPDATE cms_forum_polls SET answers='{$answers}', options='{$options}' WHERE id='{$item['id']}'");

			}
	
		}

		echo '<p>Конвертация опросов на форуме выполнена.</p>';
		$M_INFO['is_convert__forum_pools'] = 1;

	}

// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert_form']){

		$sql = "SELECT id, config FROM cms_form_fields";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			while($item = $inDB->fetch_assoc($result)){
	
				$item['config'] = iconv('utf-8', 'cp1251', $item['config']);
				$cfg    = unserialize($item['config']);
				foreach($cfg as $param=>$value){
					$cfg[$param] = iconv('cp1251', 'utf-8', $value);
				}
				$cfg = serialize($cfg);
				$cfg = $inDB->escape_string($cfg);
				$inDB->query("UPDATE cms_form_fields SET config='{$cfg}' WHERE id='{$item['id']}'");
				unset($cfg);
			}
	
		}
	
		echo '<p>Настройки значений форм сконверчены.</p>';
		$M_INFO['is_convert_form'] = 1;
	}
// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert_uc_items']){

		$sql = "SELECT id, fieldsdata FROM cms_uc_items";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			while($item = $inDB->fetch_assoc($result)){
	
				$item['fieldsdata'] = iconv('utf-8', 'cp1251', $item['fieldsdata']);
				$cfg    = unserialize($item['fieldsdata']);
				foreach($cfg as $param=>$value){
					$cfg[$param] = iconv('cp1251', 'utf-8', $value);
				}
				$cfg = serialize($cfg);
				$cfg = $inDB->escape_string($cfg);
				$inDB->query("UPDATE cms_uc_items SET fieldsdata='{$cfg}' WHERE id='{$item['id']}'");
				unset($cfg);
			}
	
		}
	
		echo '<p>Поля записей каталога сконверчены.</p>';
		$M_INFO['is_convert_uc_items'] = 1;

	}

// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert_uc_cats']){

		$sql = "SELECT id, fieldsstruct FROM cms_uc_cats";
	
		$result = $inDB->query($sql);
	
		if ($inDB->num_rows($result)){
	
			while($item = $inDB->fetch_assoc($result)){
	
				$item['fieldsstruct'] = iconv('utf-8', 'cp1251', $item['fieldsstruct']);
				$cfg    = unserialize($item['fieldsstruct']);
				foreach($cfg as $param=>$value){
					$cfg[$param] = iconv('cp1251', 'utf-8', $value);
				}
				$cfg = serialize($cfg);
				$cfg = $inDB->escape_string($cfg);
				$inDB->query("UPDATE cms_uc_cats SET fieldsstruct='{$cfg}' WHERE id='{$item['id']}'");
				unset($cfg);
			}
	
		}
	
		echo '<p>Поля категорий каталога сконверчены.</p>';
		$M_INFO['is_convert_uc_cats'] = 1;

	}

// ========================================================================== //
// ========================================================================== //

	if(!$M_INFO['is_convert_config']){

		$new_cfg = myIconv('cp1251', 'utf-8', $_CFG);

		$inConf->saveToFile($new_cfg);

		echo '<p>Конвертация файла конфигурации "/includes/config.inc.php" выполнена.</p>';
		$M_INFO['is_convert_config'] = 1;

	}

// ========================================================================== //
// ========================================================================== //

	echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Удалите папку /migrate/ прежде чем продолжить!</div>';
    echo '<div class="nextlink"><a href="/">Перейти на сайт</a></div>';
    echo '</div></body></html>';

// ========================================================================== //

	$cfg_file = fopen($info_migrate_file, 'w+');

	fputs($cfg_file, "<?php \n");
	fputs($cfg_file, '$M_INFO = array();'."\n");

	foreach($M_INFO as $key=>$value){
		$s = '$M_INFO' . "['$key'] \t= $value;\n";
		fwrite($cfg_file, $s);
	}

	fwrite($cfg_file, "?>");
	fclose($cfg_file);

	function myIconv($from, $to, $var){
		if (is_array($var)){
			$new = array();
			foreach ($var as $key => $val){
				$new[self::myIconv($from, $to, $key)] = self::myIconv($from, $to, str_replace('\n', "\n", $val));
			}
			$var = $new;
		} else if (is_string($var)){
			$var = stripslashes(iconv($from, $to, str_replace('\n', "\n", $var)));
		}
		return $var;
	}

?>