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

	cpAddPathway('Поиск', '?view=components&do=config&id='.$_REQUEST['id']);
	
	echo '<h3>Поиск</h3>';
	
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
	
	$toolmenu = array();

	$toolmenu[0]['icon'] = 'save.gif';
	$toolmenu[0]['title'] = 'Сохранить';
	$toolmenu[0]['link'] = 'javascript:document.optform.submit();';

	$toolmenu[1]['icon'] = 'cancel.gif';
	$toolmenu[1]['title'] = 'Отмена';
	$toolmenu[1]['link'] = '?view=components';

	cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('search');

	if($opt=='save'){
		$cfg = array();
		$cfg['perpage'] = $_REQUEST['perpage'];
		$cfg['comp'] = $_REQUEST['comp'];
		$inCore->saveComponentConfig('search', $cfg);
		$msg = 'Настройки сохранены.';
	}
	
	if ($opt=='dropcache'){
		$sql = "DELETE FROM cms_search";
		dbQuery($sql);
	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
?>
<form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" name="optform" method="post" target="_self">
        <table border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="215"><b>Результатов на странице: </b></td>
            <td width="289"><input name="perpage" type="text" id="perpage" value="<?php echo @$cfg['perpage'];?>" size="6" /></td>
          </tr>
          <tr>
            <td valign="top"><strong>Поиск по компонентам:</strong> </td>
            <td valign="top">
			<?php
				//get list of components and look for search processor in component folder
				$sql = "SELECT title, link FROM cms_components";
				$rs = dbQuery($sql) ;
				if (mysql_num_rows($rs)){
					echo '<table border="0" cellpadding="2" cellspacing="0">';
					while ($component = mysql_fetch_assoc($rs)){
						echo '<tr>';
						$spfile = $_SERVER['DOCUMENT_ROOT'].'/components/'.$component['link'].'/psearch.php';
						if (file_exists($spfile)){
							$checked = '';
							if (isset($cfg['comp'])){
								if (in_array($component['link'], $cfg['comp'])){
									$checked = 'checked="checked"';	
								}
							}
							echo '<td><input name="comp[]" type="checkbox" value="'.$component['link'].'" '.$checked.'/></td><td>'.$component['title'].'</td>';
						}
						echo '</tr>';
					}
					echo '</table>';
				}						
			?>			</td>
          </tr>
          <tr>
            <td valign="top"><strong>Записей в поисковом кеше:</strong> </td>
            <td valign="top">
			<?php 
				$records = dbRowsCount('cms_search', '1=1');
				echo $records . ' шт.';
				if ($records) {
					echo ' | <a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=dropcache">Очистить</a>';
				}
			?></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="save" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
        </p>
</form>