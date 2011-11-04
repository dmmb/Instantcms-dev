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

function bannerCTRbyID($id){
	$b = dbGetFields('cms_banners', 'id='.$id, 'clicks, hits');
	if ($b['hits']>0) { 
		$ctr = round((($b['clicks']/$b['hits']) * 100), 2);
	} else {
		$ctr = 0;	
	}
	return $ctr . '%';
}

function bannerHitsbyID($id){
	$b = dbGetFields('cms_banners', 'id='.$id, 'maxhits, hits');
	if (!$b['maxhits']) { return $b['hits']; } else { return $b['hits'] . '/' . $b['maxhits']; }
}

	cpAddPathway('Баннеры', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>Баннеры</h3>';
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
	$msg = '';
	$inDB = cmsDatabase::getInstance();
	$toolmenu = array();

	if($opt=='list' || $opt=='show_banner' || $opt=='hide_banner'){

		$toolmenu[0]['icon'] = 'new.gif';
		$toolmenu[0]['title'] = 'Новый баннер';
		$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add';
	
		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = 'Редактировать выбранные';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = 'Публиковать выбранные';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_banner&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = 'Скрыть выбранные';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_banner&multiple=1');";

	}
	
	if ($opt=='list' || $opt=='config' || $opt=='show_banner' || $opt=='hide_banner'){
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
			
		$inCore->saveComponentConfig('banners', $cfg);
		
		$msg = 'Настройки сохранены.';
		$opt = 'config';
	}

	if ($opt == 'show_banner'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_banners', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_banners', $_REQUEST['item']);				
			$opt = 'list';				
		}			
	}

	if ($opt == 'hide_banner'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_banners', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_banners', $_REQUEST['item']);				
			$opt = 'list';				
		}			
	}

	if ($opt == 'submit'){	

			$title = $inCore->request('title', 'str', 'Банер без названия');
			$link  = $inCore->request('link', 'str');
			
			$typeimg = $inCore->request('typeimg', 'str');
			$maxhits = $inCore->request('maxhits', 'int');

			$maxuser = 0;
			
			$published = $inCore->request('published', 'int', 0);
			
			$position = $inCore->request('position', 'str');
			
			$uploaddir = PATH.'/images/banners/';		
			
			if (!is_dir($uploaddir)) { @mkdir($uploaddir); }

			$realfile = $_FILES['picture']['name'];
			$path_parts = pathinfo($realfile);
			$ext = strtolower($path_parts['extension']);

			if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'swf') { die('тип файла неверный'); }

			$realfile = substr($realfile, 0, strrpos($realfile, '.'));
			$realfile = preg_replace ('/[^a-zA-Z0-9]/i', '', $realfile);
			$filename = $inDB->escape_string($realfile . '.' . $ext);

			$uploadfile = $uploaddir . $filename;
												
			if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {			
					$sql = "INSERT INTO cms_banners (position, typeimg, fileurl, hits, clicks, maxhits, maxuser, user_id, pubdate, title, link, published)
							VALUES ('$position', '$typeimg', '$filename', 0, 0, '$maxhits', '$maxuser', 1, NOW(), '$title', '$link', '$published')";	
					dbQuery($sql);
			} else { $msg .= 'Ошибка загрузки баннера или файл не загружен!<br>'; }
			if ($msg) {$opt = 'add';} else {
			header('location:?view=components&do=config&opt=list&id='.$_REQUEST['id']);	}
	}	  
	
	if ($opt == 'update'){
		if(isset($_REQUEST['item_id'])) {

			$id = $inCore->request('item_id', 'int', 0);
			
			$title = $inCore->request('title', 'str', 'Банер без названия');
			$link  = $inCore->request('link', 'str');
			
			$typeimg = $inCore->request('typeimg', 'str');
			$maxhits = $inCore->request('maxhits', 'int');
			$maxuser = 0;
			
			$published = $inCore->request('published', 'int', 0);
			
			$position = $inCore->request('position', 'str');
			
			if ($_FILES['picture']['size']){

                $uploaddir = PATH.'/images/banners/';

				$realfile = $_FILES['picture']['name'];
				$path_parts = pathinfo($realfile);
				$ext = strtolower($path_parts['extension']);
	
				if ($ext != 'jpg' && $ext != 'jpeg' && $ext != 'gif' && $ext != 'swf') { die('тип файла неверный'); }
	
				$realfile = substr($realfile, 0, strrpos($realfile, '.'));
				$realfile = preg_replace ('/[^a-zA-Z0-9]/i', '', $realfile);
				$filename = $inDB->escape_string($realfile . '.' . $ext);
	
				$uploadfile = $uploaddir . $filename;

                if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
                        $sql = "UPDATE cms_banners SET fileurl = '$filename' WHERE id = '$id'";
                        dbQuery($sql) ;
                } else { $msg .= 'Ошибка загрузки баннера!'; }
            }
					
			$sql = "UPDATE cms_banners
					SET position = '$position',
						title = '$title',
						published = '$published',
						maxhits = '$maxhits',
						maxuser = '$maxuser',
						typeimg = '$typeimg',
						link = '$link'
					WHERE id = '$id'";
			
			dbQuery($sql);		
		}
		if ($msg) {$opt = 'edit';} else {
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
		} else {
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit');
		}
	}
	}

	if($opt == 'delete'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];		
			$sql = "SELECT * FROM cms_banners WHERE id = '$id' LIMIT 1";
			$result = dbQuery($sql) ;			
			if (mysql_num_rows($result)){			
				$f = mysql_fetch_assoc($result);

				$path_parts = pathinfo($f['fileurl']);
				$ext = strtolower($path_parts['extension']);
				if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'swf') {
					unlink(PATH.'/images/banners/'.$f['fileurl']);
				}
				$sql = "DELETE FROM cms_banners WHERE id = '$id'";
				dbQuery($sql) ;			
				header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
			}
		}
	}

	if ($opt == 'list'){
		cpAddPathway('Баннеры', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
		echo '<h3>Баннеры</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Дата';		$fields[1]['field'] = 'pubdate';		$fields[1]['width'] = '100';		$fields[1]['filter'] = 15;
		$fields[1]['fdate'] = '%d/%m/%Y';

		$fields[2]['title'] = 'Название';	$fields[2]['field'] = 'title';		$fields[2]['width'] = '';
		$fields[2]['filter'] = 15;
		$fields[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

		$fields[3]['title'] = 'Позиция';	$fields[3]['field'] = 'position';		$fields[3]['width'] = '100';
		$fields[3]['filter'] = 15;

		$fields[4]['title'] = 'Показ';		$fields[4]['field'] = 'published';	$fields[4]['width'] = '100';
		$fields[4]['do'] = 'opt';  $fields[4]['do_suffix'] = '_banner';

		$fields[5]['title'] = 'Хиты';	$fields[5]['field'] = 'id';		$fields[5]['width'] = '90';
		$fields[5]['prc'] = 'bannerHitsbyID';

		$fields[6]['title'] = 'Клики';	$fields[6]['field'] = 'clicks';		$fields[6]['width'] = '90';

		$fields[7]['title'] = 'CTR';	$fields[7]['field'] = 'id';		$fields[7]['width'] = '90';
		$fields[7]['prc'] = 'bannerCTRbyID';

		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить баннер со всей статистикой?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete&item_id=%id%';
				
		//Print table
		cpListTable('cms_banners', $fields, $actions, '', 'pubdate DESC');		
	}
	
	if ($opt == 'add' || $opt == 'edit'){	
			
		if ($opt=='add'){
			 echo '<h3>Добавить баннер</h3>';
			 cpAddPathway('Добавить баннер', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add');
			 if (@$msg) { echo '<p class="error">'.$msg.'</p>'; }
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
		
		
					 $sql = "SELECT * FROM cms_banners WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway('Баннеры', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit&item_id='.$id);		
						
			}
		?>
		<?php if ($opt=='edit') { ?>
			<table width="625" border="0" cellspacing="5" class="proptable">
				  <tr>
					<td align="center">
						<?php echo $inCore->getBannerById($id); ?>
					</td>
				 </tr>
			</table>
		<?php } ?>
		
		<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
				<table width="625" border="0" cellspacing="5" class="proptable">
				  <tr>
					<td width="298"><strong>Название баннера: </strong><br />
									<span class="hinttext">Отображается на сайте</span>					</td>
					<td width="308"><input name="title" type="text" id="title" size="45" value="<?php echo @$mod['title'];?>"/></td>
				  </tr>
				  <tr>
				    <td><strong>Ссылка баннера: </strong><br />
						<span class="hinttext">Не забывайте "http://" для внешних ссылок!</span>					</td>
				    <td><input name="link" type="text" id="link" size="45" value="<?php echo @$mod['link'];?>"/></td>
			      </tr>
				  <tr>
				    <td><strong>Позиция для показа: </strong></td>
				    <td><select name="position" id="position">
							<?php for($m=1;$m<=30;$m++){ ?>
                                <option value="banner<?php echo $m; ?>" <?php if(@$mod['position']=='banner'.$m) { echo 'selected'; } ?>>banner<?php echo $m; ?></option>
                            <?php } ?>
                    </select></td>
			      </tr>
				  <tr>
				    <td><strong>Тип баннера: </strong></td>
				    <td><select name="typeimg" id="typeimg">
                      <option value="image" <?php if(@$mod['typeimg']=='image') { echo 'selected'; } ?>>Изображение (gif, jpg)</option>
                      <option value="swf" <?php if(@$mod['typeimg']=='swf') { echo 'selected'; } ?>>Flash (swf) (468x60)</option>
                    </select></td>
			      </tr>
				  <tr>
					<td><strong>Файл баннера: </strong><br />
						<span class="hinttext">Только GIF, JPG, JPEG, SWF</span>					</td>
					<td><?php if (@$mod['file']) { echo '<a href="/images/photos/'.$mod['file'].'" title="Посмотреть фото">'.$mod['file'].'</a>'; } else { ?>
						<input name="picture" type="file" id="picture" size="30" />
					  <?php } ?></td>
				  </tr>
				  <tr>
				    <td><strong>Максимум показов: </strong><br />
						<span class="hinttext">Установите "0" для бесконечного количества</span>					</td>
				    <td><input name="maxhits" type="text" id="maxhits" size="5" value="<?php echo @$mod['maxhits'];?>"/>  раз.</td>
			      </tr>
				  <tr>
					<td><strong>Публиковать баннер?</strong><br />
						<span class="hinttext">Отключите для приостановки показов</span>					</td>
					<td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
					  Да
					  <label>
				  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
						Нет</label></td>
				  </tr>
				</table>
				<p><strong>Примечание:</strong> для вывода баннера на сайте укажите в нужной статье или модуле выражение вида: {БАННЕР=Имя_позиции}</p>
				<p>
				  <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add') { echo 'value="Загрузить баннер"'; } else { echo 'value="Сохранить баннер"'; } ?> />
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