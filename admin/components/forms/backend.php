<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
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

	require('../includes/jwtabs.php');

	$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/forms.js"></script>';									
	$GLOBALS['cp_page_head'][] = jwHeader();

	cpAddPathway('Конструктор форм', '?view=components&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }

	$toolmenu = array();
	$toolmenu[0]['icon'] = 'newform.gif';
	$toolmenu[0]['title'] = 'Новая форма';
	$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add';

	$toolmenu[1]['icon'] = 'listforms.gif';
	$toolmenu[1]['title'] = 'Формы';
	$toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list';

	if($opt!='list'){
		$toolmenu[3]['icon'] = 'cancel.gif';
		$toolmenu[3]['title'] = 'Отмена';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'];
	}

	cpToolMenu($toolmenu);

    $inPage = cmsPage::getInstance();
	$inDB = cmsDatabase::getInstance();

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('forms');

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['email'] = $_REQUEST['email'];
		$cfg['delivery'] = $_REQUEST['delivery'];
			
		$inCore->saveComponentConfig('forms', $cfg);
        
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');	
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function autoOrder($form_id){
		$inDB = cmsDatabase::getInstance();
		$sql = "SELECT * FROM cms_form_fields WHERE form_id = $form_id ORDER BY ordering";
		$rs = dbQuery($sql);
		if (mysql_num_rows($rs)){
			$ord = 1;
			while ($item = mysql_fetch_assoc($rs)){
				dbQuery("UPDATE cms_form_fields SET ordering = ".$ord." WHERE id=".$item['id']);
				$ord += 1;
			}				
		}
		return true;
	}

	if($opt == 'up_field'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$form_id = $_REQUEST['form_id'];
			$ord = $inDB->get_field('cms_form_fields', 'id = '.$id, 'ordering');		
			$sql = "SELECT * FROM cms_form_fields WHERE form_id = $form_id AND ordering < $ord ORDER BY ordering DESC LIMIT 1";
			$rs = dbQuery($sql);			
			if (mysql_num_rows($rs)){
				$prev = mysql_fetch_assoc($rs);
				$sql = "UPDATE cms_form_fields SET ordering = $ord WHERE id = ".$prev['id'];
				dbQuery($sql);
				$sql = "UPDATE cms_form_fields SET ordering = ".$prev['ordering']." WHERE id = ".$id;
				dbQuery($sql);
				autoOrder($form_id);
			}
		}
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$form_id);	
	}

	if($opt == 'down_field'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$form_id = $_REQUEST['form_id'];
			$ord = $inDB->get_field('cms_form_fields', 'id = '.$id, 'ordering');		
			$sql = "SELECT * FROM cms_form_fields WHERE form_id = $form_id AND ordering > $ord ORDER BY ordering ASC LIMIT 1";
			$rs = dbQuery($sql);			
			if (mysql_num_rows($rs)){
				$next = mysql_fetch_assoc($rs);
				$sql = "UPDATE cms_form_fields SET ordering = $ord WHERE id = ".$next['id'];
				dbQuery($sql);
				$sql = "UPDATE cms_form_fields SET ordering = ".$next['ordering']." WHERE id = ".$id;
				dbQuery($sql);
				autoOrder($form_id);
			}
		}
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$form_id);	
	}
	
	if($opt == 'del_field'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$form_id = $_REQUEST['form_id'];
			//DELETE FIELD
			$sql = "DELETE FROM cms_form_fields WHERE id = $id";
			dbQuery($sql) ;			
		}
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$form_id);
	}
	
	if ($opt == 'add_field'){
			
		$kind = $_REQUEST['kind'];
		$title = $_REQUEST['f_title'];
		$order = $_REQUEST['f_order'];
		$form_id = $_REQUEST['form_id'];
		$mustbe = $_REQUEST['mustbe'];
		
		$cfg = array();
		
		switch($kind){		
			case 'text': $cfg['max'] = $_REQUEST['f_text_max'];
						 $cfg['size'] = $_REQUEST['f_text_size'];
						 $cfg['default'] = $_REQUEST['f_text_default'];
						 break;
			case 'textarea': $cfg['max'] = $_REQUEST['f_ta_max'];
							 $cfg['size'] = $_REQUEST['f_ta_size'];
							 $cfg['rows'] = $_REQUEST['f_ta_rows'];
							 $cfg['default'] = $_REQUEST['f_ta_default'];
							 break;
			case 'checkbox': $cfg['checked'] = $_REQUEST['f_checked'];
							 break;		
			case 'radiogroup': $cfg['items'] = $_REQUEST['f_rg_list'];
							   break;		
			case 'list': $cfg['items'] = $_REQUEST['f_list_list'];
						 break;		
			case 'menu': $cfg['items'] = $_REQUEST['f_menu_list'];
						 break;				
		}
		
		$config = serialize($cfg);
		
		$sql = "INSERT INTO cms_form_fields (form_id, title, ordering, kind, mustbe, config)
				VALUES ($form_id, '$title', $order, '$kind', $mustbe, '$config')";
		dbQuery($sql) ;
		
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$form_id);	
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($opt == 'submit'){
			
		$title = $inCore->request('title', 'str', 'Форма без названия');
		$description = $_REQUEST['description'];

		$sendto = $_REQUEST['sendto']; 
		$email = $_REQUEST['email'];
		$user_id = $_REQUEST['user_id'];
		
		$sql = "INSERT INTO cms_forms (title, description, email, sendto, user_id)
				VALUES ('$title', '$description', '$email', '$sendto', '$user_id')";
		dbQuery($sql);
		
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');	
	}	  
	
	if($opt == 'delete'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			//DELETE FIELDS
			$sql = "DELETE FROM cms_form_fields WHERE form_id = $id";
			dbQuery($sql) ;			
			//DELETE FORM
			$sql = "DELETE FROM cms_forms WHERE id = $id LIMIT 1";
			dbQuery($sql) ;			
		}
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');	
	}
	
	if ($opt == 'update'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("Укажите название формы!"); }	
			$description = $_REQUEST['description'];

			$sendto = $_REQUEST['sendto']; 
			$email = $_REQUEST['email'];
			$user_id = $_REQUEST['user_id'];
			
			$sql = "UPDATE cms_forms
					SET title='$title', 
						description='$description',
						email='$email',
						sendto='$sendto',
						user_id = '$user_id'
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
							
			header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');	
		}
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($opt == 'list'){
		cpAddPathway('Формы', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
		echo '<h3>Формы</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = ''; $fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

		$fields[2]['title'] = 'E-Mail';		$fields[2]['field'] = 'email';		$fields[2]['width'] = '150'; 	

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить форму?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete&item_id=%id%';
				
		//Print table
		cpListTable('cms_forms', $fields, $actions);		
	}

	if($opt=='add' || $opt=='edit'){
					
		if ($opt=='add'){

			 cpAddPathway('Добавить форму', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add');
			 echo '<h3>Добавить форму</h3>';
			 
		} else {
			 if(isset($_REQUEST['item_id'])){
				 $id = $_REQUEST['item_id'];
				 $sql = "SELECT * FROM cms_forms WHERE id = $id LIMIT 1";
				 $result = dbQuery($sql) ;
				 if (mysql_num_rows($result)){
					$mod = mysql_fetch_assoc($result);
				 }
			 }
			
			 echo '<h3>Форма: '.$mod['title'].'</h3>';
 			 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$id);

		}

	?>
      <?php if($opt=='edit'){ob_start();} ?><?php if($opt=='edit') { echo '{tab=Свойства формы}'; } ?>
      <form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>">
        <table width="605" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="200"><strong>Название формы: </strong></td>
            <td width=""><input name="title" type="text" id="title" size="30" value="<?php echo htmlspecialchars($mod['title']);?>" style="width:220px;"/></td>
          </tr>
          <tr>
            <td><strong>Куда отправлять форму: </strong></td>
            <td>
			 <select name="sendto" id="sendto" style="width:220px;" onChange="toggleSendTo()">
              <option value="mail" <?php if(@$mod['sendto']=='mail' || !isset($mod['sendto'])) { echo 'selected'; } ?>>На адрес e-mail</option>
              <option value="user" <?php if(@$mod['sendto']=='user') { echo 'selected'; } ?>>Личным сообщением на сайте</option>
            </select>			</td>
          </tr>
        </table>
		<div id="sendto_mail" <?php if(@$mod['sendto']=='mail' || !isset($mod['sendto'])) { echo 'style="display:block"'; } else { echo 'style="display:none"'; }?>>
		<table width="605" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="16"><img src="/admin/components/forms/email.gif" width="16" height="16"></td>
            <td width="178"><strong>Адрес e-mail: </strong></td>
            <td><input name="email" type="text" id="email" size="30" value="<?php echo @$mod['email'];?>" style="width:220px;"/></td>
          </tr>
		</table>
		</div>
		<div id="sendto_user" <?php if(@$mod['sendto']=='user') { echo 'style="display:block"'; } else { echo 'style="display:none"'; }?>>
		<table width="605" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="16"><img src="/admin/components/forms/user.gif" width="16" height="16"></td>
            <td width="178"><strong>Получатель: </strong></td>
            <td>
                <select name="user_id" id="user_id" style="width:220px">
                  <?php
                        if (isset($mod['user_id'])) {
                            echo $inCore->getListItems('cms_users', $mod['user_id'], 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0', 'id', 'nickname');
                        } else {
                            echo $inCore->getListItems('cms_users', 0, 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0', 'id', 'nickname');
                        }
                  ?>
                </select>
            </td>
          </tr>
		</table>
		</div>
        <table width="100%" border="0">
          <tr>
            <?php
				echo '<td width="52%" valign="top">';
				echo '<p><strong>Пояснения к форме:</strong></p>';

                $inCore->insertEditor('description', $mod['description'], '280', '100%');
				
				echo '</td>';
			?>
          </tr>
        </table>	
        <?php if ($opt=='add') { echo '<p><b>Примечание: </b>После создания формы вернитесь в режим ее редактирования, чтобы добавить поля. </p>'; } 
		else {echo '<p><b>Примечание: </b> Чтобы вставить форму в материал (статью/новость), укажите в нужном<br/> месте статьи выражение {ФОРМА=Название формы}, либо воспользуйтесь панелью вставки,<br/> расположенной над окном редактора материала.';} ?>
        <p>
          <label>
          <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add') { echo 'value="Создать форму"'; } else { echo 'value="Сохранить изменения"'; } ?> />
          </label>
          <label>
          <input name="reset" type="reset" id="reset" value="Сброс" />
          </label>
          <input name="opt" type="hidden" id="do" <?php if ($opt=='add') { echo 'value="submit"'; } else { echo 'value="update"'; } ?> />
          <?php
		  	if ($opt=='edit'){
			 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
			}
		  ?>
        </p>
      </form>
      <p>
        <?php	
	
	if ($opt=='edit'){
		$last_order = 1 + $inDB->get_field('cms_form_fields', "form_id='{$mod['id']}' ORDER BY ordering DESC", 'ordering');
?>	
	{tab=Поля формы}
	<table width="761" cellpadding="8" cellspacing="5">
	<tr>
		<td width="300" valign="top" class="proptable">
			<p style="border-bottom:solid 1px black"><b>Добавить поле</b></p>
			<form id="fieldform" name="fieldform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>">
			 <input type="hidden" name="opt" value="add_field"/>
			  <input name="form_id" type="hidden" id="form_id" value="<?php echo @$id?>"/>
			  <table width="100%" border="0" cellspacing="2" cellpadding="0">
                <tr>
                  <td width="100">Тип поля: </td>
                  <td>
					  <select name="kind" id="kind" onchange="show()">
						  <option value="text" selected="selected">Текстовое</option>
						  <option value="textarea">Многострочное</option>
						  <option value="checkbox">Опция да/нет</option>
						  <option value="radiogroup">Группа опций</option>
						  <option value="list">Выпадающий список</option>
						  <option value="menu">Видимый список</option>
					  </select>				  </td>
                </tr>
                <tr>
                  <td>Заголовок:</td>
                  <td><input name="f_title" type="text" id="f_title" size="25" /></td>
                </tr>
                <tr>
                  <td>Порядок:</td>
                  <td><input name="f_order" type="text" id="f_order" value="<?php echo $last_order?>" size="6" /></td>
                </tr>
                <tr>
                  <td>Заполнение:</td>
                  <td><select name="mustbe" id="mustbe" onchange="show()">
                    <option value="1">Обязательно</option>
                    <option value="0">Не обязательно</option>
                                    </select></td>
                </tr>
              </table>
			    		
			<div id="kind_text">
			  <table width="100%" border="0" cellspacing="2" cellpadding="0">

                <tr>
                  <td width="100">Макс. длина :</td>
                  <td><input name="f_text_max" type="text" id="f_text_max" value="200" size="6" /> 
                    символов </td>
                </tr>
                <tr>
                  <td>Размер:</td>
                  <td><input name="f_text_size" type="text" id="f_text_size" value="30" size="6" /> 
                    символов </td>
                </tr>
                <tr>
                  <td>Текст: </td>
                  <td><input name="f_text_default" type="text" id="f_text_default" size="25" /></td>
                </tr>
              </table>
			</div>
			<div id="kind_textarea" style="display:none">
			<table width="100%" border="0" cellspacing="2" cellpadding="0">

                <tr>
                  <td width="100">Макс. длина :</td>
                  <td><input name="f_ta_max" type="text" id="f_ta_max" value="200" size="6" /> 
                    символов </td>
                </tr>
                <tr>
                  <td>Размер:</td>
                  <td><input name="f_ta_size" type="text" id="f_ta_size" value="30" size="6" /> 
                    символов </td>
                </tr>
                <tr>
			  <tr>
					  <td>Строк:</td>
					  <td><input name="f_ta_rows" type="text" id="f_ta_rows" value="5" size="6" /></td>
			  </tr>
                  <td>Текст: </td>
                  <td><input name="f_ta_default" type="text" id="f_ta_default" size="25" /></td>
                </tr>
              </table>
			</div>			
			<div id="kind_checkbox" style="display:none">
			  <div id="div" >
                <table width="100%" border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td width="100">Отметка:</td>
                    <td><select name="f_checked" id="f_checked">
                      <option value="1">Отмечена</option>
                      <option value="0">Не отмечена</option>
                    </select>
                    </td>
                  </tr>
                </table>
		      </div>
			</div>			
			<div id="kind_radiogroup" style="display:none">
			  <table width="100%" border="0" cellspacing="2" cellpadding="0">
                <tr>
                  <td width="100">Элементы:<br />
                    <small>через "<b>/</b>"</small> </td>
                  <td><textarea name="f_rg_list" cols="20" rows="5" id="f_rg_list"></textarea></td>
                </tr>
              </table>
			</div>
			<div id="kind_list" style="display:none">
			  <table width="100%" border="0" cellspacing="2" cellpadding="0">
                <tr>
                  <td width="100">Элементы:<br />
                      <small>через "<b>/</b>"</small> </td>
                  <td><textarea name="f_list_list" cols="20" rows="5" id="f_list_list"></textarea></td>
                </tr>
              </table>
			</div>
			<div id="kind_menu" style="display:none">
			  <table width="100%" border="0" cellspacing="2" cellpadding="0">
                <tr>
                  <td width="100">Элементы:<br />
                      <small>через "<b>/</b>"</small> </td>
                  <td><textarea name="f_menu_list" cols="20" rows="5" id="f_menu_list"></textarea></td>
                </tr>
              </table>
			</div>	
						
			<p>
			  <input type="submit" name="Submit" value="Добавить поле" />
			</p>
			</form>			
			
	  </td>
		<td width="440" valign="top" class="proptable"><p style="border-bottom:solid 1px black"><b>Предварительный просмотр </b></p>
          <?php echo $inPage->buildForm($id, true); ?></td>
	</tr>
	</table>
	{/tabs}
	<?php
	 echo jwTabs(ob_get_clean());
	
	?>
	<?php
	}
	
	}
	


?>