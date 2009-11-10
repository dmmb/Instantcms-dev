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

function applet_editor(){

    $inCore = cmsCore::getInstance();

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['lang'])) { $lang = $_REQUEST['lang']; } else { $lang = 'html'; }
	if (isset($_REQUEST['file'])) { $file = $_REQUEST['file']; } else { $file = ''; }

	$fileshort = $file;
	$file = $_SERVER['DOCUMENT_ROOT'].$file;

	$GLOBALS['cp_page_title'] = 'Редактор файлов';
 	cpAddPathway('Настройки сайта', 'index.php?view=config');	
 	cpAddPathway('Редактор файлов', 'index.php?view=csseditor');
	
	echo '<h3>Редактор файлов</h3>';
	
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:content.toggleEditor();document.editform.submit();';
		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);
			
		if (file_exists($file)){					
			$GLOBALS['cp_page_head'][] = '<script src="/admin/includes/codepress/codepress.js" type="text/javascript"></script>';
			$GLOBALS['cp_page_head'][] = '<script type="text/javascript">CodePress.language = \''.$lang.'\';</script>';
			
			$filesource = file_get_contents($file);			
			echo '<div class="css_filename">Файл &rarr; <a href="'.$fileshort.'">'.$file.'</a>:</div>';			
			if (!is_writable($file)){
				echo '<div class="css_filename" style="background-color:#FFCECE;border:solid 1px red;font-size:14px">Файл не доступен для записи! Перед сохранением выставьте нужные права на этот файл!</div>';			
			}			
			echo '<form action="" method="POST" name="editform">';		
				echo '<textarea id="content" name="content" style="width:100%" rows="30" class="codepress '.$lang.'">'.$filesource.'</textarea>';	
				echo '<input type="hidden" name="do" value="save" />';	
				echo '<input type="hidden" name="back" value="'.$_SERVER['HTTP_REFERER'].'" />';	
			echo '</form>';					
		} else {
			echo '<div class="css_filename">Файл "'.$fileshort.'" не найден!</div>';	
		}
		
    }
	
	if ($do == 'save'){
		if (isset($_POST['content'])){
			$filesource = $_POST['content'];			
			$filesource = str_replace('\"', '"', $filesource);
			if ($filesource){
			echo $filesource;
				file_put_contents($file, $filesource);
			}
		}	
		header('location:'.$_POST['back']);
	}
}

?>