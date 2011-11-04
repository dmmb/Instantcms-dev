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


	cpAddPathway('Прайслист', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>Прайслист</h3>';
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'config'; }
	
	$toolmenu = array();

	$toolmenu[0]['icon'] = 'newfolder.gif';
	$toolmenu[0]['title'] = 'Новая категория';
	$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';

	$toolmenu[2]['icon'] = 'newstuff.gif';
	$toolmenu[2]['title'] = 'Новый товар';
	$toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item';

	$toolmenu[1]['icon'] = 'folders.gif';
	$toolmenu[1]['title'] = 'Категории прайса';
	$toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';

	$toolmenu[3]['icon'] = 'liststuff.gif';
	$toolmenu[3]['title'] = 'Все товары';
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

	cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('price');

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['email'] = $_REQUEST['email'];
		$cfg['delivery'] = $_REQUEST['delivery'];
			
		$inCore->saveComponentConfig('price', $cfg);
	}

	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

	if ($opt == 'show_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['id'])){ dbShow('cms_price_items', $id);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_price_items', $_REQUEST['item']);				
			header('location:'.$_SERVER['HTTP_REFERER']);					
		}			
	}

	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['id'])){ dbHide('cms_price_items', $id);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_price_items', $_REQUEST['item']);				
			header('location:'.$_SERVER['HTTP_REFERER']);					
		}			
	}

	if ($opt == 'submit_item'){	
		$category_id = $_REQUEST['category_id'];
		if (!empty($_REQUEST['title'])) { $title = htmlspecialchars($_REQUEST['title'], ENT_QUOTES, 'cp1251'); } else { error("Укажите название категории!"); }
		if (!empty($_REQUEST['price'])) {		
			$price = $_REQUEST['price'];					
		}
		$published = $_REQUEST['published'];
		$canmany = $_REQUEST['canmany'];
		
		$price = str_replace(',', '.', $price);
			
		$sql = "INSERT INTO cms_price_items (category_id, title, price, published, canmany)
				VALUES ($category_id, '$title', '$price', $published, $canmany)";	

		dbQuery($sql) ;	
		header('location:?view=components&do=config&opt=list_items&id='.$_REQUEST['id']);		
	}	  
	
	if ($opt == 'update_item'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			$category_id = $_REQUEST['category_id'];
			$title = $_REQUEST['title'];
			$price = $_REQUEST['price'];		
			$published = $_REQUEST['published'];
			$canmany = $_REQUEST['canmany'];
			
			$price = str_replace(',', '.', $price);
			
			$sql = "UPDATE cms_price_items
					SET category_id = $category_id,
						title='$title', 
						price='$price',
						published=$published,
						canmany=$canmany
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		} else {
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item');
		}
	}

	if($opt == 'delete_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbDelete('cms_price_items', $_REQUEST['item_id']);  }
		} else {
			dbDeleteList('cms_price_items', $_REQUEST['item']);				
		}
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
	}

	if ($opt == 'config') {
		?>
		<form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
        <table width="600" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="218"><b>E-mail продавца : </b></td>
            <td width="338"><input name="email" type="text" id="title2" size="30" value="<?php echo @$cfg['email'];?>"/></td>
          </tr>
        </table>
        <table width="100%" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td><p><b>Информация о доставке: </b></p>
            <?php
                
                $inCore->insertEditor('delivery', $cfg['delivery'], '260', '100%');

			?></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="saveconfig" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
        </p>
      </form>		
		<?php
	}
	
	if ($opt == 'show_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_price_cats SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		}
	}

	if ($opt == 'hide_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_price_cats SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		}
	}
	
	if ($opt == 'submit_cat'){	
		if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("Укажите название категории!"); }
		$description = $_REQUEST['description'];
		$published = $_REQUEST['published'];
		
		$sql = "INSERT INTO cms_price_cats (title, description, published)
				VALUES ('$title', '$description', '$published')";
		dbQuery($sql) ;		
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}	  
	
	if($opt == 'delete_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			//DELETE ITEMS
			$sql = "DELETE FROM cms_price_items WHERE category_id = $id";
			dbQuery($sql) ;			
			//DELETE CATEGORY
			$sql = "DELETE FROM cms_price_cats WHERE id = $id LIMIT 1";
			dbQuery($sql) ;			
		}
		header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
	}
	
	if ($opt == 'update_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("Укажите название категории!"); }	
			$description = $_REQUEST['description'];
			$published = $_REQUEST['published'];
			
			$sql = "UPDATE cms_price_cats
					SET title='$title', 
						description='$description',
						published=$published
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
							
			header('location:?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		
		}
	}
	
	
	if ($opt == 'list_cats'){
		cpAddPathway('Категории прайса', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
		echo '<h3>Категории прайса</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';
		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_cat';

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить категорию из прайслиста?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';
				
		//Print table
		cpListTable('cms_price_cats', $fields, $actions);		
	}

	if ($opt == 'list_items'){
		cpAddPathway('Товары', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		echo '<h3>Товары</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
		$fields[1]['filter'] = 15;
		
		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_item';

		$fields[3]['title'] = 'Цена';		$fields[3]['field'] = 'price';		$fields[3]['width'] = '90';
		$fields[3]['filter'] = 6;

		$fields[4]['title'] = 'Категория';	$fields[4]['field'] = 'category_id';$fields[4]['width'] = '300';
		$fields[4]['prc'] = 'cpPriceCatById';  $fields[4]['filter'] = 1;  $fields[4]['filterlist'] = cpGetList('cms_price_cats');
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить позицию из прайса?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_item&item_id=%id%';
				
		//Print table
		cpListTable('cms_price_items', $fields, $actions);		
	}
	
	if ($opt == 'add_item' || $opt == 'edit_item'){
		if ($opt=='add_item'){
		 echo '<h3>Добавить товар</h3>';
		 cpAddPathway('Добавить товар', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item');
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
		
		
					 $sql = "SELECT * FROM cms_price_items WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway('Товары', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id='.$id);
			}

		?>
		 <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
        <table width="650" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="177">Название товара: </td>
            <td width="311"><textarea name="title" id="title" rows="1" style="height:16px;width:320px;"><?php echo @$mod['title'];?></textarea></td>
          </tr>
          <tr>
            <td>Цена (<font color="#999999">руб.коп</font>): </td>
            <td><input name="price" type="text" size="30" style="height:16px;width:120px;" value="<?php echo @$mod['price'];?>"/></td>
          </tr>
          <tr>
            <td>Категория:</td>
            <td>
                <select name="category_id" id="category_id">
                    <?php
                        if (isset($mod['category_id'])) {
                            echo $inCore->getListItems('cms_price_cats', $mod['category_id']);
                        } else {
                            if (isset($_REQUEST['addto'])){
                                echo $inCore->getListItems('cms_price_cats', $_REQUEST['addto']);
                            } else {
                                echo $inCore->getListItems('cms_price_cats');
                            }
                        }
                    ?>
                </select>
            </td>
          </tr>
          <tr>
            <td>Выбор количества: </td>
            <td><select name="canmany" id="canmany">
              <option value="1" <?php if(@$mod['canmany']) { echo 'selected'; } ?>>Разрешить</option>
              <option value="0" <?php if(@!$mod['canmany']) { echo 'selected'; } ?>>Запретить</option>
            </select>
            </td>
          </tr>
          <tr>
            <td>Публиковать товар?</td>
            <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
              Да
              <label>
          <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
                Нет</label></td>
          </tr>
        </table>
        <p>
          <label>
          <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_item') { echo 'value="Добавить товар"'; } else { echo 'value="Сохранить изменения"'; } ?> />
          </label>
          <label>
          <input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
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
					 $sql = "SELECT * FROM cms_price_cats WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
				 }
				
				 echo '<h3>Категория: '.$mod['title'].'</h3>';
	 	 		 cpAddPathway('Категории прайса', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$_REQUEST['item_id']);	 
			}
			?>
		<form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>">
			<table width="600" border="0" cellspacing="5" class="proptable">
			  <tr>
				<td width="200">Название категории: </td>
				<td width="213"><input name="title" type="text" id="title" size="30" value="<?php echo htmlspecialchars($mod['title']);?>"/></td>
				<td width="173">&nbsp;</td>
			  </tr>
			  <tr>
				<td>Публиковать категорию?</td>
				<td><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
				  Да
				  <label>
			  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
					Нет</label></td>
				<td>&nbsp;</td>
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
			  <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
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