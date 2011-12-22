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

    define('VALID_CMS', 1);
    
    define('PATH', $_SERVER['DOCUMENT_ROOT']);

    include(PATH.'/core/cms.php');
    include(PATH.'/includes/config.inc.php');

    $inCore     = cmsCore::getInstance();

    define('HOST', 'http://' . $inCore->getHost());

    $inCore->loadClass('config');       //конфигурация
    $inCore->loadClass('db');           //база данных
    $inCore->loadClass('user');
	$inCore->loadClass('cron');

    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();

    $version_prev = '1.9';
    $version_next = '1.9.1';
	
	$is_was_migrate = false;

// ========================================================================== //
// ========================================================================== //

    echo '<style type="text/css">
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
          </style>';

    echo '<div id="wrapper">';

    echo "<h2>Миграция InstantCMS {$version_prev} &rarr; {$version_next}</h2>";

// ========================================================================== //
// ========================================================================== //
// -------------Конвертация базы данных---------------------------------------//

	// проверяем кодировку БД
	$result = $inDB->query('SHOW VARIABLES LIKE "character_set_results"');
	$ch = $inDB->fetch_assoc($result);

	//if (mb_strtolower($ch['Value']) == 'cp1251'){

		$result = $inDB->query('SHOW TABLES');
		while($table = $inDB->fetch_assoc($result)){
			$inDB->query('ALTER TABLE '.$table['Tables_in_'.$inConf->db_base].' CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
			$inDB->query('ALTER TABLE '.$table['Tables_in_'.$inConf->db_base].' DEFAULT CHARACTER SET utf8');
		}
		$inDB->query('ALTER DATABASE `'.$inConf->db_base.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');

	//} else {
		echo '<p>база данных уже была сконвертирована в UTF-8.</p>';
	//}

// ========================================================================== //
// ========================================================================== //

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
			//$inDB->query("UPDATE cms_form_fields SET config='{$cfg}' WHERE id='{$item['id']}'");
			unset($cfg);
		}

	}

	echo '<p>Настройки значений форм сконверчены.</p>';
	$is_was_migrate = true;
// ========================================================================== //
// ========================================================================== //

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
			//$inDB->query("UPDATE cms_uc_items SET fieldsdata='{$cfg}' WHERE id='{$item['id']}'");
			unset($cfg);
		}

	}

	echo '<p>Поля записей каталога сконверчены.</p>';
	$is_was_migrate = true;

// ========================================================================== //
// ========================================================================== //

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
			//$inDB->query("UPDATE cms_uc_cats SET fieldsstruct='{$cfg}' WHERE id='{$item['id']}'");
			unset($cfg);
		}

	}

	echo '<p>Поля категорий каталога сконверчены.</p>';
	$is_was_migrate = true;

// ========================================================================== //
// ========================================================================== //
	if ($is_was_migrate) {
	    echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Миграция завершена. Удалите папку /migrate/ прежде чем продолжить!</div>';
	} else {
		echo '<div style="margin:15px 0px 15px 0px;font-weight:bold">Вы уже прошли миграцию.</div>';
	}
    echo '<div class="nextlink"><a href="/">Перейти на сайт</a></div>';
    echo '</div>';

// ========================================================================== //
?>