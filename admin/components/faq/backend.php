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

	cpAddPathway('Вопросы и ответы', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>Вопросы и ответы</h3>';
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list_cats'; }
	
	$toolmenu = array();

	$toolmenu[0]['icon'] = 'newfolder.gif';
	$toolmenu[0]['title'] = 'Новая категория';
	$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';

	$toolmenu[2]['icon'] = 'newquest.gif';
	$toolmenu[2]['title'] = 'Новый вопрос';
	$toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item';

	$toolmenu[1]['icon'] = 'folders.gif';
	$toolmenu[1]['title'] = 'Категории вопросов';
	$toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';

	$toolmenu[3]['icon'] = 'listquest.gif';
	$toolmenu[3]['title'] = 'Все вопросы';
	$toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items';

	if($opt == 'list_items'){
		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = 'Редактировать выбранные';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit_item&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = 'Публиковать выбранные';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_item&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = 'Скрыть выбранные';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_item&multiple=1');";
		
		$toolmenu[14]['icon'] = 'delete.gif';
		$toolmenu[14]['title'] = 'Удалить выбранные';
		$toolmenu[14]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=delete_item&multiple=1');";
	}

	$toolmenu[15]['icon'] = 'cancel.gif';
	$toolmenu[15]['title'] = 'Отмена';
	$toolmenu[15]['link'] = '?view=components';

	cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('faq');

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['email'] = $_REQUEST['email'];
		$cfg['delivery'] = $_REQUEST['delivery'];
			
		$inCore->saveComponentConfig('faq', $cfg);
	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

	if ($opt == 'show_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_faq_quests', $_REQUEST['item_id']);  }
		} else {
			dbShowList('cms_faq_quests', $_REQUEST['item']);				
		}			
		echo '1'; exit;
	}

	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_faq_quests', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_faq_quests', $_REQUEST['item']);				
		}			
		echo '1'; exit;
	}

	if ($opt == 'submit_item'){	
		$category_id = $_REQUEST['category_id'];
		$published = $_REQUEST['published'];
		$quest = $_REQUEST['quest'];
		$answer = $_REQUEST['answer'];
		$answeruser_id = $_SESSION['user']['id'];
		$user_id = $_REQUEST['user_id'];;
						
		$pubdate = $_REQUEST['pubdate'];
		$answerdate = $_REQUEST['answerdate'];
			
		$date = explode('.', $pubdate);
		$pubdate = $date[2] . '-' . $date[1] . '-' . $date[0];
		$date = explode('.', $answerdate);
		$answerdate = $date[2] . '-' . $date[1] . '-' . $date[0];
						
		$sql = "INSERT INTO cms_faq_quests (category_id, pubdate, published, quest, answer, user_id, answeruser_id, answerdate)
				VALUES ('$category_id', '$pubdate', $published, '$quest', '$answer', $user_id, $answeruser_id, '$answerdate')";	

		dbQuery($sql) ;	
		header('location:?view=components&do=config&opt=list_items&id='.$_REQUEST['id']);
	}	  
	
	if ($opt == 'update_item'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			$category_id = $_REQUEST['category_id'];
			$published = $_REQUEST['published'];
			$quest = $_REQUEST['quest'];
			$answer = $_REQUEST['answer'];
			$answeruser_id = $_SESSION['user']['id'];

			$pubdate = $_REQUEST['pubdate'];
			$answerdate = $_REQUEST['answerdate'];
				
			$date = explode('.', $pubdate);
			$pubdate = $date[2] . '-' . $date[1] . '-' . $date[0];
			$date = explode('.', $answerdate);
			$answerdate = $date[2] . '-' . $date[1] . '-' . $date[0];
					
			$sql = "UPDATE cms_faq_quests
					SET category_id = $category_id,
						quest='$quest', 
						answer='$answer',
						published=$published,
						answeruser_id=$answeruser_id,
						pubdate='$pubdate',
						answerdate='$answerdate'
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql);
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		} else {
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item');
		}
	}

	if($opt == 'delete_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbDelete('cms_faq_quests', $_REQUEST['item_id']);  }
		} else {
			dbDeleteList('cms_faq_quests', $_REQUEST['item']);				
		}
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
	}

	if ($opt == 'config') {
		?>
<?php
	}
	
	if ($opt == 'show_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_faq_cats SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

	if ($opt == 'hide_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_faq_cats SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}
	
	if ($opt == 'submit_cat'){	
		$parent_id = $_REQUEST['parent_id'];		
		$title = $_REQUEST['title'];
		$published = $_REQUEST['published'];
		$description = $_REQUEST['description'];		
		
		$sql = "INSERT INTO cms_faq_cats (parent_id, title, published, description)
				VALUES ($parent_id, '$title', $published, '$description')";
		dbQuery($sql) ;		
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}	  
	
	if($opt == 'delete_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			//DELETE ITEMS
			$sql = "DELETE FROM cms_faq_quests WHERE category_id = $id";
			dbQuery($sql) ;			
			//DELETE CATEGORY
			$sql = "DELETE FROM cms_faq_cats WHERE id = $id LIMIT 1";
			dbQuery($sql) ;			
		}
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}
	
	if ($opt == 'update_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			$parent_id = $_REQUEST['parent_id'];		
			$title = $_REQUEST['title'];
			$published = $_REQUEST['published'];
			$description = $_REQUEST['description'];		
			
			$sql = "UPDATE cms_faq_cats
					SET title='$title',
						parent_id = $parent_id,		
						description='$description',
						published=$published
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
							
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		
		}
	}
	
	
	if ($opt == 'list_cats'){
		cpAddPathway('Категории вопросов', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		echo '<h3>Категории вопросов</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['filter'] = 20; 
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$fields[2]['title'] = 'Родитель';	$fields[2]['field'] = 'parent_id'; $fields[2]['width'] = '300';
		$fields[2]['prc'] = 'cpFaqCatById';  $fields[2]['filter'] = 1;  $fields[2]['filterlist'] = cpGetList('cms_faq_cats');

		$fields[3]['title'] = 'Показ';		$fields[3]['field'] = 'published';	$fields[3]['width'] = '100';
		$fields[3]['do'] = 'opt'; $fields[3]['do_suffix'] = '_cat';

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить категорию вопросов?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';
				
		//Print table
		cpListTable('cms_faq_cats', $fields, $actions);		
	}

	if ($opt == 'list_items'){
		cpAddPathway('Вопросы', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		echo '<h3>Вопросы</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Вопрос';		$fields[1]['field'] = 'quest';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
		$fields[1]['filter'] = 15;
		$fields[1]['maxlen'] = 80;
		
		$fields[2]['title'] = 'Категория';	$fields[2]['field'] = 'category_id';$fields[2]['width'] = '300';
		$fields[2]['prc'] = 'cpFaqCatById';  $fields[2]['filter'] = 1;  $fields[2]['filterlist'] = cpGetList('cms_faq_cats');

		$fields[3]['title'] = 'Показ';		$fields[3]['field'] = 'published';	$fields[3]['width'] = '100';
		$fields[3]['do'] = 'opt'; $fields[3]['do_suffix'] = '_item';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить вопрос?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_item&item_id=%id%';
				
		//Print table
		cpListTable('cms_faq_quests', $fields, $actions, '', 'pubdate DESC');		
	}
	
	if ($opt == 'add_item' || $opt == 'edit_item'){
		if ($opt=='add_item'){
		 echo '<h3>Добавить вопрос</h3>';
		 cpAddPathway('Добавить вопрос', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item');
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
					 } else { $id = $_REQUEST['item_id']; }
		
		
					 $sql = "SELECT *, DATE_FORMAT(pubdate, '%d.%m.%Y') as pubdate, DATE_FORMAT(answerdate, '%d.%m.%Y') as answerdate
					 		 FROM cms_faq_quests 
							 WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>Просмотр вопроса</h3>';
					 cpAddPathway('Вопросы', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id='.$id);
			}

		?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
        <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
          <tr>
            <td><strong>Категория вопроса:</strong></td>
            <td width="220"><select name="category_id" id="category_id" style="width:220px">
                <?php
                    if (isset($mod['category_id'])) {
                        echo $inCore->getListItems('cms_faq_cats', $mod['category_id']);
                    } else {
                        if (isset($_REQUEST['addto'])){
                            echo $inCore->getListItems('cms_faq_cats', $_REQUEST['addto']);
                        } else {
                            echo $inCore->getListItems('cms_faq_cats');
                        }
                    }
                ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Автор вопроса:</strong></td>
            <td><select name="user_id" id="user_id" style="width:220px">
              <?php
                  if (isset($mod['user_id'])) {
                        echo $inCore->getListItems('cms_users', $mod['user_id'], 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                  } else {
                        echo $inCore->getListItems('cms_users', $inUser->id, 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                  }
              ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Публиковать вопрос?</strong></td>
            <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
              Да
              <label>
        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
                Нет</label></td>
          </tr>
          <tr>
            <td valign="top"><strong>Дата подачи вопроса: </strong></td>
            <td valign="top"><input name="pubdate" type="text" style="width:190px" id="pubdate" <?php if(@!$mod['pubdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['pubdate'].'"'; } ?>/>
                <?php 
					//include javascript
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/jquery.js"></script>';
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/date_ru_win1251.js"></script>';
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/datepicker.js"></script>';		
					$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/datepicker/datepicker.css" rel="stylesheet" type="text/css" />';
					if (@!$mod['pubdate']){					
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(new Date().asString()).trigger(\'change\');});</script>';
					} else {
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(\''.$mod['pubdate'].'\').trigger(\'change\');});</script>';
					}
			  ?>
                <input type="hidden" name="oldpubdate" value="<?php echo @$mod['pubdate']?>"/>            </td>
          </tr>
          <tr>
            <td valign="top"><strong>Дата ответа: </strong></td>
            <td valign="top"><input name="answerdate" style="width:190px" type="text" id="answerdate" <?php if(@!$mod['answerdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['answerdate'].'"'; } ?>/>
                <?php 
					//include javascript
					if (@!$mod['answerdate']){					
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#answerdate\').datePicker({startDate:\'01/01/1996\'}).val(new Date().asString()).trigger(\'change\');});</script>';
					} else {
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#answerdate\').datePicker({startDate:\'01/01/1996\'}).val(\''.$mod['answerdate'].'\').trigger(\'change\');});</script>';
					}
			  ?>
                <input type="hidden" name="oldanswerdate" value="<?php echo @$mod['answerdate']?>"/>
            </td>
          </tr>
  </table>
        <table width="507" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="377">
			<div style="margin-bottom:10px"><strong>Текст вопроса:</strong></div>
			<div>
				<textarea name="quest" rows="6" id="quest" style="border:solid 1px gray;width:605px"><?php echo @$mod['quest'];?></textarea>
			</div>			</td>
          </tr>
          <tr>
            <td>
			<div style="margin-bottom:10px"><strong>Ответ на вопрос:</strong></div>
			<div>
			<?php
                $inCore->insertEditor('answer', $mod['answer'], '300', '605');
			?>
			</div>			</td>
          </tr>
        </table>
        <p>
          <label>
          <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_item') { echo 'value="Добавить вопрос"'; } else { echo 'value="Сохранить изменения"'; } ?> />
          </label>
          <label>
          <input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
          </label>
          <input name="opt" type="hidden" id="do" <?php if ($opt=='add_item') { echo 'value="submit_item"'; } else { echo 'value="update_item"'; } ?> />
          <?php
		  	if ($opt=='edit_item'){
				 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
			}
		  ?>
        </p>
</form>
		<?php
	}

	if ($opt == 'add_cat' || $opt == 'edit_cat'){		
		if ($opt=='add_cat'){
			 echo '<h3>Добавить категорию</h3>';
			 cpAddPathway('Добавить категорию', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat');	 
			} else {
				 if(isset($_REQUEST['item_id'])){
					 $id = $_REQUEST['item_id'];
					 $sql = "SELECT * FROM cms_faq_cats WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
				 }
				
				 echo '<h3>Категория: '.$mod['title'].'</h3>';
	 	 		 cpAddPathway('Категории вопросов', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$_REQUEST['item_id']);	 
			}
			?>
		<form id="addform" name="addform" method="post" action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>">
			<table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
			  <tr>
				<td><strong>Название категории: </strong></td>
				<td width="220"><input name="title" type="text" id="title" style="width:220px" value="<?php echo @$mod['title'];?>"/></td>
			  </tr>
			  <tr>
			    <td><strong>Родительская категория</strong>: </td>
			    <td><select name="parent_id" id="parent_id" style="width:220px">
					<option value="0" <?php if (!isset($mod['parent_id'])||@$mod['parent_id']==0){ echo 'selected'; } ?>>--</option>
				<?php if (isset($mod['parent_id'])) 
					  { 
							echo $inCore->getListItems('cms_faq_cats', $mod['id']);
					  }	else { 
								echo $inCore->getListItems('cms_faq_cats');
							 }
				?>
                </select></td>
		      </tr>
			  <tr>
				<td><strong>Публиковать категорию?</strong></td>
				<td><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
				  Да
				  <label>
			  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
					Нет</label></td>
			  </tr>
			</table>
			<table width="100%" border="0">
			  <tr>
				<?php
				if(!isset($mod['user']) || @$mod['user']==1){
					echo '<td width="52%" valign="top">';
					echo 'Описание категории:<br/>';

                    $inCore->insertEditor('description', $mod['description'], '260', '605');
					
					echo '</td>';
				}
				?>
			  </tr>
			</table>	
			<p>
			  <label>
			  <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add_cat') { echo 'value="Создать категорию"'; } else { echo 'value="Сохранить изменения"'; } ?> />
			  </label>
			  <label>
			  <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
			  </label>
			  <input name="opt" type="hidden" id="do" <?php if ($opt=='add_cat') { echo 'value="submit_cat"'; } else { echo 'value="update_cat"'; } ?> />
			  <?php
				if ($opt=='edit_cat'){
				 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
				}
			  ?>
			</p>
</form>
		 <?php	
	}
			
?>