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

function error($msg){
//
}

	cpAddPathway('Награждение пользователей', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>Награждение пользователей</h3>';
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
	
	$toolmenu = array();

	if($opt=='list'){

		$toolmenu[0]['icon'] = 'newaward.gif';
		$toolmenu[0]['title'] = 'Новая награда';
		$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add';
	
		$toolmenu[3]['icon'] = 'listawards.gif';
		$toolmenu[3]['title'] = 'Все награды';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list';

		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = 'Редактировать выбранные';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = 'Включить выбранные';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_award&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = 'Отключить выбранные';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_award&multiple=1');";

	}
	
	if ($opt=='list' || $opt=='config'){

	} else {
	
		$toolmenu[20]['icon'] = 'save.gif';
		$toolmenu[20]['title'] = 'Сохранить';
		$toolmenu[20]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[21]['icon'] = 'cancel.gif';
		$toolmenu[21]['title'] = 'Отмена';
		$toolmenu[21]['link'] = '?view=components&do=config&id='.$_REQUEST['id'];
	
	}

	cpToolMenu($toolmenu);

	if($opt=='saveconfig'){	
		$cfg = array();
		$inCore->saveComponentConfig('autoawards', $cfg);
		$msg = 'Настройки сохранены.';
		$opt = 'config';
	}

	if ($opt == 'show_award'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_user_autoawards', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_user_autoawards', $_REQUEST['item']);				
			header('location:'.$_SERVER['HTTP_REFERER']);			
		}			
	}

	if ($opt == 'hide_award'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_user_autoawards', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_user_autoawards', $_REQUEST['item']);				
			header('location:'.$_SERVER['HTTP_REFERER']);			
		}			
	}

	if ($opt == 'submit'){	
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { $title = 'Награда'; }
												
			$description = $_REQUEST['description'];
			$published = $_REQUEST['published'];
			
			$imageurl = $_REQUEST['imageurl'];
			
			$p_comment = (int)$_REQUEST['p_comment'];
			$p_forum = (int)$_REQUEST['p_forum'];
			$p_content = (int)$_REQUEST['p_content'];
			$p_blog = (int)$_REQUEST['p_blog'];
			$p_karma = (int)$_REQUEST['p_karma'];
			$p_photo = (int)$_REQUEST['p_photo'];
			$p_privphoto = (int)$_REQUEST['p_privphoto'];	
			
			$sql = "INSERT INTO cms_user_autoawards (title, description, imageurl, p_comment, p_blog, p_forum, p_photo, p_privphoto, p_content, p_karma, published)
					VALUES ('$title', '$description', '$imageurl', $p_comment, $p_blog, $p_forum, $p_photo, $p_privphoto, $p_content, $p_karma, $published)";
			dbQuery($sql) ;						
			header('location:?view=components&do=config&opt=list&id='.$_REQUEST['id']);		
	}	  
	
	if ($opt == 'update'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { $title = 'Награда'; }
												
			$description = $_REQUEST['description'];
			$published = $_REQUEST['published'];
			
			$imageurl = $_REQUEST['imageurl'];
			
			$p_comment = (int)$_REQUEST['p_comment'];
			$p_forum = (int)$_REQUEST['p_forum'];
			$p_content = (int)$_REQUEST['p_content'];
			$p_blog = (int)$_REQUEST['p_blog'];
			$p_karma = (int)$_REQUEST['p_karma'];
			$p_photo = (int)$_REQUEST['p_photo'];
			$p_privphoto = (int)$_REQUEST['p_privphoto'];					
			
			$sql = "UPDATE cms_user_autoawards
					SET title='$title', 
						description='$description', 
						imageurl='$imageurl', 
						p_comment=$p_comment, 
						p_blog=$p_blog, 
						p_forum=$p_forum, 
						p_photo=$p_photo, 
						p_privphoto=$p_privphoto, 
						p_content=$p_content, 
						p_karma=$p_karma, 
						published=$published
					WHERE id = $id";
			
			dbQuery($sql);		
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
		} else {
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit');
		}
	}

	if($opt == 'delete'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];		
			$sql = "DELETE FROM cms_user_autoawards WHERE id = $id";
			dbQuery($sql);
			$sql = "DELETE FROM cms_user_awards WHERE award_id = $id";
			dbQuery($sql);
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
		}
	}

	if ($opt == 'list'){
		echo '<h3>Награды</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[2]['title'] = 'Название';	$fields[2]['field'] = 'title';		$fields[2]['width'] = '250';
		$fields[2]['filter'] = 15;
		$fields[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

		$fields[3]['title'] = 'Описание';	$fields[3]['field'] = 'description';		$fields[3]['width'] = '';
		$fields[3]['filter'] = 15;

		$fields[4]['title'] = 'Выдача';		$fields[4]['field'] = 'published';	$fields[4]['width'] = '100';
		$fields[4]['do'] = 'opt';  $fields[4]['do_suffix'] = '_award';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить награду?\nЕе лишатся все пользователи, у которых она есть.';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete&item_id=%id%';
				
		//Print table
		cpListTable('cms_user_autoawards', $fields, $actions);		
	}
	
	if ($opt == 'add' || $opt == 'edit'){	
			
		include(PATH.'/components/users/includes/usercore.php');
			
		if ($opt=='add'){
			 cpAddPathway('Добавить награду', $_SERVER['REQUEST_URI']);
			 echo '<h3>Добавить награду</h3>';
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
		
		
					 $sql = "SELECT * FROM cms_user_autoawards WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$id);		
						
			}
			
		if (!isset($mod['p_comment'])){ $mod['p_comment'] = 0; }
		if (!isset($mod['p_content'])){ $mod['p_content'] = 0; }
		if (!isset($mod['p_blog'])){ $mod['p_blog'] = 0; }
		if (!isset($mod['p_karma'])){ $mod['p_karma'] = 0; }
		if (!isset($mod['p_forum'])){ $mod['p_forum'] = 0; }
		if (!isset($mod['p_photo'])){ $mod['p_photo'] = 0; }
		if (!isset($mod['p_privphoto'])){ $mod['p_privphoto'] = 0; }										
		?>
		<style type="text/css">
			#p_input{
				border:solid 1px silver;
				text-align:center;
				margin-left:4px;
				margin-right:6px;
			}
			#p_input:hover{
				border:solid 1px gray;
				background-color:#EBEBEB;
				text-align:center;
				margin-left:4px;
				margin-right:6px;		
			}
		</style>
		<form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
				<table width="625" border="0" cellspacing="5" class="proptable">
				  <tr>
					<td width="298" valign="top"><strong>Название награды: </strong><br /></td>
					<td width="308" valign="top"><input name="title" type="text" id="title" size="45" value="<?php echo @$mod['title'];?>"/></td>
				  </tr>
				  <tr>
                    <td valign="top"><strong>Описание награды: </strong><br /></td>
				    <td valign="top"><input name="description" type="text" id="description" size="45" value="<?php echo @$mod['description'];?>"/></td>
			      </tr>
				  <tr>
					<td valign="top"><strong>Выдавать награду?</strong><br />
						<span class="hinttext">Отключите для приостановки выдачи награды </span>					</td>
					<td valign="top"><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
					  Да
					  <label>
				  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
						Нет</label></td>
				  </tr>
				  <tr>
				    <td valign="top"><strong>Изображение награды:<br />
				    </strong><span class="hinttext">Картинки из папки /images/users/awards </span><strong><br />
			        </strong></td>
				    <td valign="top"><?php echo usrAwardsList($mod['imageurl']);?></td>
			      </tr>
				  <tr>
				    <td valign="top"><p><strong>Условия получения награды:</strong> <br>
				      <span class="hinttext">Награда</span> будет выдаваться пользователям, набравшим указанное количество сообщений и баллов </p>			        </td>
				    <td valign="top"><table width="100%" border="0" cellspacing="2" cellpadding="0">
                      <tr>
                        <td width="20"><img src="/admin/components/autoawards/images/p_comment.gif" width="16" height="16" /></td>
                        <td width="14%"><label>
                          <input name="p_comment" type="text" id="p_input" size="5" value="<?php echo @$mod['p_comment'];?>">
                        </label></td>
                        <td width="86%">комментариев</td>
                      </tr>
                      <tr>
                        <td><img src="/admin/components/autoawards/images/p_forum.gif" width="16" height="16" /></td>
                        <td><input name="p_forum" type="text" id="p_input" size="5" value="<?php echo @$mod['p_forum'];?>" /></td>
                        <td>сообщений на форуме </td>
                      </tr>
                      <tr>
                        <td><img src="/admin/components/autoawards/images/p_content.gif" width="16" height="16" /></td>
                        <td><input name="p_content" type="text" id="p_input" size="5" value="<?php echo @$mod['p_content'];?>"></td>
                        <td>опубликованных статей </td>
                      </tr>
                      <tr>
                        <td><img src="/admin/components/autoawards/images/p_blog.gif" width="16" height="16" /></td>
                        <td><input name="p_blog" type="text" id="p_input" size="5" value="<?php echo @$mod['p_blog'];?>"></td>
                        <td>одобренных записей в блоге </td>
                      </tr>
                      <tr>
                        <td><img src="/admin/components/autoawards/images/p_karma.gif" width="16" height="16" /></td>
                        <td><input name="p_karma" type="text" id="p_input" size="5" value="<?php echo @$mod['p_karma'];?>"></td>
                        <td>баллов личной кармы </td>
                      </tr>
                      <tr>
                        <td><img src="/admin/components/autoawards/images/p_photo.gif" width="16" height="16" /></td>
                        <td><input name="p_photo" type="text" id="p_input" size="5" value="<?php echo @$mod['p_photo'];?>" /></td>
                        <td>фотографий в общих альбомах </td>
                      </tr>
                      <tr>
                        <td><img src="/admin/components/autoawards/images/p_privphoto.gif" width="16" height="16" /></td>
                        <td><input name="p_privphoto" type="text" id="p_input" size="5" value="<?php echo @$mod['p_privphoto'];?>" /></td>
                        <td>фотографий в личном альбоме </td>
                      </tr>
                    </table></td>
			      </tr>
				</table>
				<p>
				  <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add') { echo 'value="Создать награду"'; } else { echo 'value="Сохранить изменения"'; } ?> />
				  <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
				  <input name="opt" type="hidden" id="opt" <?php if ($opt=='add') { echo 'value="submit"'; } else { echo 'value="update"'; } ?> />
				  <?php
					if ($opt=='edit'){
					 echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
					}
				  ?>
				</p>
</form>
	 <?php	
	}
	
		
?>