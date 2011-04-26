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

function applet_frules(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/filters', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = 'Правила фильтрации';
 	cpAddPathway('Фильтры', 'index.php?view=filters');	
 	cpAddPathway('Правила фильтрации', 'index.php?view=frules');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'new.gif';
		$toolmenu[0]['title'] = 'Добавить правило';
		$toolmenu[0]['link'] = '?view=frules&do=add';

		$toolmenu[1]['icon'] = 'edit.gif';
		$toolmenu[1]['title'] = 'Редактировать выбранные';
		$toolmenu[1]['link'] = "javascript:checkSel('?view=frules&do=edit&multiple=1');";

		$toolmenu[2]['icon'] = 'show.gif';
		$toolmenu[2]['title'] = 'Публиковать выбранные';
		$toolmenu[2]['link'] = "javascript:checkSel('?view=frules&do=show&multiple=1');";

		$toolmenu[3]['icon'] = 'hide.gif';
		$toolmenu[3]['title'] = 'Скрыть выбранные';
		$toolmenu[3]['link'] = "javascript:checkSel('?view=frules&do=hide&multiple=1');";

		$toolmenu[4]['icon'] = 'delete.gif';
		$toolmenu[4]['title'] = 'Удалить выбранные';
		$toolmenu[4]['link'] = "javascript:checkSel('?view=frules&do=delete&multiple=1');";

		$toolmenu[6]['icon'] = 'help.gif';
		$toolmenu[6]['title'] = 'Помощь';
		$toolmenu[6]['link'] = "?view=help&topic=menu";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';		$fields[1]['link'] = '?view=frules&do=edit&id=%id%';
		$fields[1]['filter'] = 15;
		
		$fields[2]['title'] = 'Найти';		$fields[2]['field'] = 'find';	$fields[2]['width'] = '220';	

		$fields[3]['title'] = 'Заменить на';$fields[3]['field'] = 'replace';	$fields[3]['width'] = '220';	

		$fields[4]['title'] = 'В работе';	$fields[4]['field'] = 'published';	$fields[4]['width'] = '100';	
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=frules&do=edit&id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить правило фильтрации?';
		$actions[1]['link']  = '?view=frules&do=delete&id=%id%';
				
		//Print table
		cpListTable('cms_filter_rules', $fields, $actions);		
	}
	
	if ($do == 'show'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbShow('cms_filter_rules', $id);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_filter_rules', $_REQUEST['item']);
			$inCore->redirectBack();		
		}		
	}

	if ($do == 'hide'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbHide('cms_filter_rules', $id);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_filter_rules', $_REQUEST['item']);				
			$inCore->redirectBack();
		}
		
	}
	
	if ($do == 'delete'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbDelete('cms_filter_rules', $id);  }
		} else {
			dbDeleteList('cms_filter_rules', $_REQUEST['item']);				
		}
		header('location:?view=frules');
	}
	
	if ($do == 'update'){
		if(isset($_REQUEST['id'])) { 
			$id = (int)$_REQUEST['id'];
			
			$title = $_REQUEST['title'];
			$find = $_REQUEST['find'];
			$replace = $_REQUEST['replace'];
			
			$sql = "UPDATE cms_filter_rules 
					SET `title`='$title', 
						`find`='$find',
						`replace`='$replace'
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
							
			if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
				header('location:?view=frules');		
			} else {
				header('location:?view=frules&do=edit');		
			}
		}	
	}
	
	if ($do == 'submit'){

		$title = $_REQUEST['title'];
		$find = $_REQUEST['find'];
		$replace = $_REQUEST['replace'];
		
		$sql = "INSERT INTO cms_filter_rules VALUES ('$title', '$find', '$replace', 1)";
			
		dbQuery($sql) ;
		
		header('location:?view=frules');		
	}	  

   if ($do == 'add' || $do == 'edit'){
 
 		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:window.location.href=\'index.php?view=frules\'';

		cpToolMenu($toolmenu);
   
		if ($do=='add'){
			 echo '<h3>Добавить правило</h3>';
 	 		 cpAddPathway('Добавить правило', 'index.php?view=frules&do=add');
		} else {
					 if(isset($_REQUEST['multiple'])){				 
						if (isset($_REQUEST['item'])){					
							$_SESSION['editlist'] = $_REQUEST['item'];
						} else {
							echo '<p class="error">Нет выбранных объектов!</p>';
							return;
						}				 
					 }
						
					 $ostatok = '';
					
					 if (isset($_SESSION['editlist'])){
						$id = array_shift($_SESSION['editlist']);
						if (sizeof($_SESSION['editlist'])==0) { unset($_SESSION['editlist']); } else 
						{ $ostatok = '(На очереди: '.sizeof($_SESSION['editlist']).')'; }
					 } else { $id = (int)$_REQUEST['id']; }
	
					 $sql = "SELECT * FROM cms_filter_rules WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
					
					 echo '<h3>Редактировать правило '.$ostatok.'</h3>';
					 
					 cpAddPathway($mod['title'], 'index.php?view=frules&do=edit&id='.$mod['id']);
			}   
	?>
<form id="addform" name="addform" method="post" action="index.php">
<input type="hidden" name="view" value="frules" />
  <table width="650" border="0" cellpadding="0" cellspacing="10" class="proptable">
    <tr>
      <td valign="top"><strong>Название правила:</strong></td>
      <td width="220" valign="top"><input name="title" type="text" id="name" style="width:220px" value="<?php echo @$mod['title'];?>" />      </td>
    </tr>
    <tr>
      <td valign="top"><strong>Тэг для поиска:</strong><br />
          <span class="hinttext">Указанный тэг будет заменяться фильтром на текст, введенный ниже. <br/> В статьях и модулях тег нужно будет вводить в фигурных скобках.</span></td>
      <td align="center" valign="top"><b>{</b>
          <input name="find" type="text" id="find" size="27" style="text-align:center" value="<?php echo @$mod['find'];?>"/>
          <b>}</b> </td>
    </tr>
  </table>
  <table width="100%" border="0">
    <tr>
      <?php
			if(!isset($mod['user']) || @$mod['user']==1){
				echo '<td width="52%" valign="top">';
				echo '<h3>Заменять тэг на:</h3>';

                    $inCore->insertEditor('replace', $mod['replace'], '400', '605');

				echo '</td>';
			}
			?>
    </tr>
  </table>
  <p>
    <label>
      <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add') { echo 'value="Создать правило"'; } else { echo 'value="Сохранить правило"'; } ?> />
    </label>
    <label><span style="margin-top:15px">
      <input name="back" type="button" id="back" value="Отмена" onclick="window.history.back();"/>
      </span></label>
    <input name="do" type="hidden" id="do" <?php if ($do=='add') { echo 'value="submit"'; } else { echo 'value="update"'; } ?> />
    <?php
		  	if ($do=='edit'){
			 echo '<input name="id" type="hidden" value="'.$mod['id'].'" />';
			}
		  ?>
  </p>
</form>
<?php
   }
}

?>
