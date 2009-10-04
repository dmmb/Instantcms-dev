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

function iconList(){
	if ($handle = opendir($_SERVER['DOCUMENT_ROOT'].'/images/menuicons')) {
		$n = 0;
		while (false !== ($file = readdir($handle))) {
			if ($file != '.' && $file != '..' && strstr($file, '.gif')){
				$tag = str_replace('.gif', '', $file);
				$dir = '/images/menuicons/';
				echo '<a style="width:20px;height:20px;display:block; float:left; padding:2px" href="javascript:selectIcon(\''.$file.'\')"><img alt="'.$file.'"src="'.$dir.$file.'" border="0" /></a>';
 				$n++;
			}
		}	
		closedir($handle);
	}	
	
	if (!$n) { echo '<p>Папка "/images/menuicons" пуста!</p>'; }
	
	echo '<p align="right">[<a href="javascript:selectIcon(\'\')">Без иконки</a>] [<a href="javascript:hideIcons()">Закрыть</a>]</p>';
	
	return;
}

function applet_menu(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/menu', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = 'Меню';
 	cpAddPathway('Меню сайта', 'index.php?view=menu');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'new.gif';
		$toolmenu[0]['title'] = 'Добавить пункт меню';
		$toolmenu[0]['link'] = '?view=menu&do=add';

		$toolmenu[1]['icon'] = 'newmenu.gif';
		$toolmenu[1]['title'] = 'Добавить меню';
		$toolmenu[1]['link'] = '?view=menu&do=addmenu';

		$toolmenu[2]['icon'] = 'edit.gif';
		$toolmenu[2]['title'] = 'Редактировать выбранные';
		$toolmenu[2]['link'] = "javascript:checkSel('?view=menu&do=edit&multiple=1');";

		$toolmenu[3]['icon'] = 'show.gif';
		$toolmenu[3]['title'] = 'Публиковать выбранные';
		$toolmenu[3]['link'] = "javascript:checkSel('?view=menu&do=show&multiple=1');";

		$toolmenu[4]['icon'] = 'hide.gif';
		$toolmenu[4]['title'] = 'Скрыть выбранные';
		$toolmenu[4]['link'] = "javascript:checkSel('?view=menu&do=hide&multiple=1');";

		$toolmenu[5]['icon'] = 'delete.gif';
		$toolmenu[5]['title'] = 'Удалить выбранные';
		$toolmenu[5]['link'] = "javascript:checkSel('?view=menu&do=delete&multiple=1');";

		$toolmenu[6]['icon'] = 'reorder.gif';
		$toolmenu[6]['title'] = 'Сохранить порядок элементов';
		$toolmenu[6]['link'] = "javascript:checkSel('?view=menu&do=saveorder');";

		$toolmenu[7]['icon'] = 'autoorder.gif';
		$toolmenu[7]['title'] = 'Упорядочить элементы';
		$toolmenu[7]['link'] = "?view=menu&do=autoorder";

		$toolmenu[8]['icon'] = 'help.gif';
		$toolmenu[8]['title'] = 'Помощь';
		$toolmenu[8]['link'] = "?view=help&topic=menu";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';		$fields[1]['link'] = '?view=menu&do=edit&id=%id%';
		
		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';	
		
		$fields[3]['title'] = 'Порядок';	$fields[3]['field'] = 'ordering';	$fields[3]['width'] = '100';	
				
		$fields[4]['title'] = 'Ссылка';		$fields[4]['field'] = 'id';		$fields[4]['width'] = '300';	
		$fields[4]['prc'] = 'cpMenutypeById';

		$fields[5]['title'] = 'Меню';		$fields[5]['field'] = 'menu';		$fields[5]['width'] = '100';	$fields[5]['filter'] = 10;
		$fields[5]['filterlist'] = cpGetList('menu');

		$fields[6]['title'] = 'Шаблон';		$fields[6]['field'] = 'template';	$fields[6]['width'] = '100';	
		$fields[6]['prc'] = 'cpTemplateById';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=menu&do=edit&id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить пункт меню?';
		$actions[1]['link']  = '?view=menu&do=delete&id=%id%';
				
		//Print table
		cpListTable('cms_menu', $fields, $actions, 'parent_id>0', 'menu, NSLeft');		
	}
	
	function reorder(){
		$sql = "SELECT * FROM cms_menu ORDER BY NSLeft";
		$rs = dbQuery($sql) ;
		
		if (mysql_num_rows($rs)){
			$level = array();
			while ($item = mysql_fetch_assoc($rs)){
				if (isset($level[$item['NSLevel']])){
					$level[$item['NSLevel']] += 1;
				} else {
					$level[] = 1;
				}
				dbQuery("UPDATE cms_menu SET ordering = ".$level[$item['NSLevel']]." WHERE id=".$item['id']) ;
			}				
		}
	}

	if ($do == 'autoorder'){
		reorder();
		header('location:index.php?view=menu');		
	}
	
	if ($do == 'move_up'){
		$id = (int)$_REQUEST['id'];
		$ns = $inCore->nestedSetsInit('cms_menu');
		$ns->MoveOrdering($id, -1);
		reorder();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	if ($do == 'move_down'){
		$id = (int)$_REQUEST['id'];	
		$ns = $inCore->nestedSetsInit('cms_menu');
		$ns->MoveOrdering($id, 1);
		reorder();
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	if ($do == 'saveorder'){
		if(isset($_REQUEST['ordering'])) { 
			$ord = $_REQUEST['ordering'];
			$ids = $_REQUEST['ids'];
			
			foreach ($ord as $id=>$ordering){			
				dbQuery("UPDATE cms_menu SET ordering = $ordering WHERE id = ".$ids[$id]) ;						
			}
			header('location:?view=menu');

		}
	}

	if ($do == 'show'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbShow('cms_menu', $id);  }
			echo '1'; exit;		
		} else {
			dbShowList('cms_menu', $_REQUEST['item']);			
			$inCore->redirectBack();	
		}
	}

	if ($do == 'hide'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbHide('cms_menu', $id);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_menu', $_REQUEST['item']);
			$inCore->redirectBack();		
		}		
	}
	
	if ($do == 'delete'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbDeleteNS('cms_menu', $id);  }
		} else {
			dbDeleteListNS('cms_menu', $_REQUEST['item']);				
		}
		reorder();
		header('location:?view=menu');
	}
	
	if ($do == 'update'){
		if(isset($_REQUEST['id'])) { 
			$id = (int)$_REQUEST['id'];
			
			$title = $_REQUEST['title'];
			$menu = $_REQUEST['menu'];
			$link = $inCore->menuGetLink($_REQUEST['mode'], $_REQUEST[$_REQUEST['mode']]);
			$linktype = $_REQUEST['mode'];
			$linkid = $_REQUEST[$_REQUEST['mode']];
			$target = $_REQUEST['target'];		
			$published = $_REQUEST['published'];
			$template = $_REQUEST['template'];
			$allow_group = $_REQUEST['allow_group'];
			$iconurl = $_REQUEST['iconurl'];

			$parent_id = $_REQUEST['parent_id'];
			
			$oldparent = $_REQUEST['oldparent'];
			
			$ns = $inCore->nestedSetsInit('cms_menu');

			if ($oldparent!=$parent_id){
				$ns->MoveNode($id, $parent_id);			
			}
			
			$sql = "UPDATE cms_menu
					SET title='$title', 
						menu='$menu',
						link='$link',
						linktype='$linktype',
						linkid='$linkid',
						target='$target',
						published=$published,
						template='$template',
						allow_group='$allow_group',
						iconurl='$iconurl'
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;					
			if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
				header('location:?view=menu');		
			} else {
				header('location:?view=menu&do=edit');		
			}
		}	
	}
	
	if ($do == 'submit'){

		$sql = "SELECT ordering as max_o FROM cms_menu ORDER BY ordering DESC LIMIT 1";
		$result = dbQuery($sql) ;
		$row = mysql_fetch_assoc($result);
		$maxorder = $row['max_o'] + 1;
	
		$menu = $_REQUEST['menu'];
		$title = $_REQUEST['title'];
		$target = $_REQUEST['target'];
		$linktype = $_REQUEST['mode'];
		$template = $_REQUEST['template'];
		$iconurl = $_REQUEST['iconurl'];
		$allow_group = $_REQUEST['allow_group'];
		$parent_id = $_REQUEST['parent_id'];
		
		$link = $inCore->menuGetLink($linktype, $_REQUEST[$linktype]);
		
		if ($linktype != 'link') {
			$linkid = $_REQUEST[$linktype];
		} else {
			$linkid = 0;
		}
		
		$published = $_REQUEST['published'];

		$ns = $inCore->nestedSetsInit('cms_menu');
		$myid = $ns->AddNode($parent_id);
	
		$sql = "UPDATE cms_menu 
				SET menu='$menu', 
					title='$title', 
					link='$link', 
					linktype='$linktype', 
					linkid='$linkid', 
					target='$target', 
					published='$published', 
					template='$template', 
					allow_group='$allow_group', 
					iconurl='$iconurl'
				WHERE id = $myid";
	
		dbQuery($sql) or die(mysql_error().$sql);
		reorder();
		header('location:?view=menu');		
	}	  

	if ($do == 'submitmenu'){

		$sql = "SELECT ordering as max_o FROM cms_modules ORDER BY ordering DESC LIMIT 1";
		$result = dbQuery($sql) ;
		$row = mysql_fetch_assoc($result);
		$maxorder = $row['max_o'] + 1;
	
		$menu = $_REQUEST['menu'];
		$title = $_REQUEST['title'];
		$position = $_REQUEST['target'];
		$published = $_REQUEST['published'];
		$css_prefix = $_REQUEST['css_prefix'];
		$allow_group = $_REQUEST['allow_group'];
	
		$cfg['menu'] = $menu;		
		$cfg_str = serialize($cfg);
	
		$sql = "INSERT INTO cms_modules (position, name, title, is_external, content, ordering, showtitle, published, user, config, css_prefix)
				VALUES ('".$position."', 
						'Меню', 
						'".$title."', 
						1, 
						'mod_menu', 
						$maxorder, 
						1, 
						$published,
						0,
						'$cfg_str',
						'$css_prefix')";
	
		dbQuery($sql) ;
		
		$newid = dbLastId('cms_modules');
		
		header('location:?view=modules&do=edit&id='.$newid);		
	}	  

   if ($do == 'addmenu'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);

		$GLOBALS['cp_page_title'] = 'Добавить меню';
		cpAddPathway('Добавить меню', 'index.php?view=menu&do=addmenu');
		
		echo '<h3>Добавить меню</h3>';
 		
		?>
        <form action="index.php?view=menu&do=submitmenu" method="post">
            <table class="proptable" width="650" cellspacing="10" cellpadding="10">
                <tr>
                    <td width="300" valign="top">
                        <strong>Название модуля меню:</strong>
                    </td>
                    <td valign="top">
                        <input name="title" type="text" id="title2" style="width:200px" value=""/>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Меню для показа: </strong><br/>
                        <span class="hinttext">При создании пунктов нового меню выбирайте такое же значение в поле &quot;меню&quot;, для привязки </span>
                    </td>
                    <td valign="top">
                        <select name="menu" id="menu" style="width:200px">
                                <option value="mainmenu" <?php if (@$mod['menu']=='mainmenu' || !isset($mod['menu'])) { echo 'selected'; }?>>Главное меню</option>
                                <option value="menu1" <?php if (@$mod['menu']=='menu1') { echo 'selected'; }?>>Дополнительно меню 1</option>
                                <option value="menu2" <?php if (@$mod['menu']=='menu2') { echo 'selected'; }?>>Дополнительно меню 2</option>
                                <option value="menu3" <?php if (@$mod['menu']=='menu3') { echo 'selected'; }?>>Дополнительно меню 3</option>
                                <option value="menu4" <?php if (@$mod['menu']=='menu4') { echo 'selected'; }?>>Дополнительно меню 4</option>
                                <option value="menu5" <?php if (@$mod['menu']=='menu5') { echo 'selected'; }?>>Дополнительно меню 5</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Позиция показа:</strong><br />
                        <span class="hinttext">Позиция должна присутствовать в шаблоне</span>
                    </td>
                    <td valign="top">
                        <?php
                            include PATH.'/includes/config.inc.php';
                            $pos = cpModulePositions($_CFG['template']);
                        ?>
                        <select name="position" id="position" style="width:200px">
                            <?php
                                if ($pos){
                                    foreach($pos as $key=>$position){
                                        if (@$mod['position']==$position){
                                            echo '<option value="'.$position.'" selected>'.$position.'</option>';
                                        } else {
                                            echo '<option value="'.$position.'">'.$position.'</option>';
                                        }
                                    }
                                }
                            ?>
                        </select>
                        <input name="is_external" type="hidden" id="is_external" value="0" />
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Публиковать меню?</strong></td>
                    <td valign="top">
                        <input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> Да
                        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> Нет
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>CSS префикс:</strong></td>
                    <td valign="top">
                        <input name="css_prefix" type="text" id="css_prefix" value="<?php echo @$mod['css_prefix'];?>" style="width:200px" />
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Доступ:</strong><br />
                        <span class="hinttext">Какой группе пользователей показывать это меню</span>
                    </td>
                    <td valign="top">
                        <select name="allow_group" id="allow_group" style="width:200px">
                            <option value="-1" <?php if (@$mod['allow_group']==-1 || !isset($mod['allow_group'])) { echo 'selected="selected"'; } ?>>-- Все группы --</option>
                            <?php
                                if (isset($mod['allow_group'])) {
                                    echo $inCore->getListItems('cms_user_groups', $mod['allow_group']);
                                } else {
                                    echo $inCore->getListItems('cms_user_groups');
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" valign="top">
                        <div style="padding:10px;margin:4px;background-color:#EBEBEB;border:solid 1px gray">
                            Создавая новое меню, вы создаете новый модуль. Он будет доступен в разделе "Модули" вместе с остальными. После создания меню
                            вы будете перенаправлены в настройки нового модуля, где вы сможете выбрать в каких разделах сайта следует его показывать.
                        </div>
                    </td>
                </tr>
            </table>
            <div style="margin-top:5px">
                <input name="save" type="submit" id="save" value="Создать меню"/>
                <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=menu';"/>
            </div>
        </form>
		<?php
		
   }


   if ($do == 'add' || $do == 'edit'){
		$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/menu.js"></script>';
	 	$GLOBALS['cp_page_head'][] = '';
	
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);
   
		if ($do=='add'){
			 echo '<h3>Добавить пункт меню</h3>';
 	 		 cpAddPathway('Добавить пункт меню', 'index.php?view=menu&do=add');
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
	
					 $sql = "SELECT * FROM cms_menu WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
					
					 echo '<h3>Редактировать пункт меню '.$ostatok.'</h3>';
					 
					 cpAddPathway($mod['title'], 'index.php?view=menu&do=edit&id='.$mod['id']);
			}   
	?>
        <form id="addform" name="addform" method="post" action="index.php">
            <input type="hidden" name="view" value="menu" />
            <table width="650" border="0" cellpadding="0" cellspacing="10" class="proptable">
                <tr>
                    <td width="350" valign="top">
                        <strong>Заголовок пунта меню:</strong><br />
                        <span class="hinttext">Отображается на сайте</span>
                    </td>
                    <td valign="top">
                        <input name="title" type="text" id="title" size="30" style="width:280px" value="<?php echo @$mod['title'];?>"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Меню:</strong></td>
                    <td valign="top">
                        <select name="menu" id="menu" style="width:280px">
                            <option value="mainmenu" <?php if (@$mod['menu']=='mainmenu' || !isset($mod['menu'])) { echo 'selected'; }?>>Главное меню</option>
                            <option value="menu1" <?php if (@$mod['menu']=='menu1') { echo 'selected'; }?>>Дополнительно меню 1</option>
                            <option value="menu2" <?php if (@$mod['menu']=='menu2') { echo 'selected'; }?>>Дополнительно меню 2</option>
                            <option value="menu3" <?php if (@$mod['menu']=='menu3') { echo 'selected'; }?>>Дополнительно меню 3</option>
                            <option value="menu4" <?php if (@$mod['menu']=='menu4') { echo 'selected'; }?>>Дополнительно меню 4</option>
                            <option value="menu5" <?php if (@$mod['menu']=='menu5') { echo 'selected'; }?>>Дополнительно меню 5</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Родительский пункт: </strong></td>
                    <td valign="top">
                        <?php
                            $rootid = dbGetField('cms_menu', 'parent_id=0', 'id');
                        ?>
                        <select name="parent_id" size="8" id="parent_id" style="width:280px">
                            <option value="<?php echo $rootid?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- Корень меню --</option>
                            <?php
                                if (isset($mod['parent_id'])){
                                    echo $inCore->getListItemsNS('cms_menu', $mod['parent_id']);
                                } else {
                                    echo $inCore->getListItemsNS('cms_menu');
                                }
                            ?>
                        </select>
                        <input type="hidden" name="oldparent" value="<?php echo @$mod['parent_id'];?>" />
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Открывать: </strong></td>
                    <td valign="top">
                        <select name="target" id="target" style="width:280px">
                                <option value="_self" <?php if (@$mod['target']=='_self') { echo 'selected'; }?>>В этом же окне (self)</option>
                                <option value="_parent">В родительском окне (parent)</option>
                                <option value="_blank" <?php if (@$mod['target']=='_blank') { echo 'selected'; }?>>В новом окне (blank)</option>
                                <option value="_top" <?php if (@$mod['target']=='_top') { echo 'selected'; }?>>Поверх всех окон (top)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Шаблон:</strong><br />
                        <span class="hinttext">Выберите, если открытие пункта меню должно сопровождаться сменой шаблона</span>
                    </td>
                    <td valign="top">
                        <select name="template" id="template" style="width:280px">
                                <option value="0" <?php if (@$mod['template']==0 || !$mod['template']) { echo 'selected'; } ?>>По умолчанию</option>
                                <?php if (isset($mod['template'])){
                                    $inCore->templatesList($mod['template']);
                                } else {
                                    $inCore->templatesList(-1);
                                }
                                ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Иконка:</strong><br />
                        <span class="hinttext">Название gif-файла в папке &quot;/images/menuicons&quot;</span>
                    </td>
                    <td valign="top">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <input name="iconurl" type="text" id="iconurl" size="30" value="<?php echo @$mod['iconurl'];?>" style="width:280px"/>
                                    <a id="iconlink" style="display:block; float:left" href="javascript:showIcons()"> Выбрать иконку</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div id="icondiv" style="display:none; width:270px; padding:6px;border:solid 1px gray">
                                        <div><?php iconList(); ?></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Публиковать пункт?</strong></td>
                    <td valign="top">
                        <input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> Да
                        <input name="published" type="radio" value="0" <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> Нет
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Доступ:</strong><br />
                        <span class="hinttext">Какой группе пользователей показывать и разрешать открывать этот пункт меню </span>
                    </td>
                    <td valign="top">
                        <select name="allow_group" id="allow_group" style="width:280px">
                                <option value="-1" <?php if (@$mod['allow_group']==-1 || !isset($mod['allow_group'])) { echo 'selected="selected"'; } ?>>-- Все группы --</option>
                                <?php
                                    if (isset($mod['allow_group'])) {
                                        echo $inCore->getListItems('cms_user_groups', $mod['allow_group']);
                                    } else {
                                        echo $inCore->getListItems('cms_user_groups');
                                    }
                                ?>
                        </select>
                    </td>
                </tr>
            </table>

            <h3>Действие пункта меню</h3>

            <table width="650" border="0" cellpadding="0" cellspacing="10" class="proptable" style="margin-top:4px">
                <tr>
                    <td width="275" valign="top">
                        <strong>Ссылка:</strong><br />
                        <span class="hinttext">Для внешних ссылок не забывайте &quot;http://&quot; </span>
                    </td>
                    <td valign="top">
                        <table width="100%" border="0" cellspacing="8" bgcolor="#F2F2F2" style="<?php echo (@$mod['linktype']=='link'||@$mod['linktype']=='ext')?'border: solid 1px gray':''?>" id="t_link">
                            <tr>
                                <td width="24"><input onclick="highlight('t_link')" name="mode" type="radio" class="mode" value="link" <?php if (@$mod['linktype']=='link'||@$mod['linktype']=='ext') { echo  'checked'; } ?>/></td>
                                <td width="429"><input name="link" type="text" id="link" size="50" style="width:300px" <?php if (@$mod['linktype']=='link'||@$mod['linktype']=='ext') { echo  'value="'.$mod['link'].'"'; } ?>/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Материал:</strong><br />
                        <span class="hinttext">Пункт меню будет открывать статью </span>
                    </td>
                    <td valign="top">
                        <table width="100%" border="0" cellspacing="8" bgcolor="#F2F2F2" style="<?php echo (@$mod['linktype']=='content')?'border: solid 1px gray':''?>" id="t_content">
                            <tr>
                                <td width="24"><input onclick="highlight('t_content')" name="mode" type="radio" class="mode" value="content" <?php if (@$mod['linktype']=='content') { echo  'checked'; } ?>/></td>
                                <td width="429">
                                    <select name="content" id="content" style="width:300px">
                                        <?php
                                            if (@$mod['linktype']=='content') {
                                                echo $inCore->getListItems('cms_content', $mod['linkid']);
                                            } else {
                                                echo $inCore->getListItems('cms_content');
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                    </table></td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Раздел:</strong><br />
                        <span class="hinttext">Пункт меню будет показывать содержимое раздела </span>
                    </td>
                    <td valign="top">
                        <table width="100%" border="0" cellspacing="8" bgcolor="#F2F2F2" style="<?php echo (@$mod['linktype']=='category')?'border: solid 1px gray':''?>" id="t_category">
                            <tr>
                                <td width="24"><input onclick="highlight('t_category')" name="mode" type="radio" class="mode" value="category" <?php if (@$mod['linktype']=='category') { echo  'checked'; } ?>/></td>
                                <td width="429">
                                    <select name="category" id="category" style="width:300px">
                                        <?php
                                        if (@$mod['linktype']=='category') {
                                            echo $inCore->getListItemsNS('cms_category', $mod['linkid']);
                                        } else {
                                            echo $inCore->getListItemsNS('cms_category');
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php if ($inCore->isComponentInstalled('price')) { ?>
                    <tr>
                        <td valign="top">
                            <strong>Категория прайса:</strong><br />
                            <span class="hinttext">Пункт меню будет показывать товары из прайслиста </span>
                        </td>
                        <td valign="top">
                            <table width="100%" border="0" cellspacing="8" bgcolor="#F2F2F2" style="<?php echo (@$mod['linktype']=='pricecat')?'border: solid 1px gray':''?>" id="t_pricecat">
                                <tr>
                                    <td width="24"><input onclick="highlight('t_pricecat')" name="mode" type="radio" class="mode" value="pricecat" <?php if (@$mod['linktype']=='pricecat') { echo  'checked'; } ?>/></td>
                                    <td width="429">
                                        <select name="pricecat" id="pricecat" style="width:300px">
                                            <?php
                                            if (@$mod['linktype']=='pricecat') {
                                                echo $inCore->getListItems('cms_price_cats', $mod['linkid']);
                                            } else {
                                                echo $inCore->getListItems('cms_price_cats');
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <?php } ?>
                <?php  if ($inCore->isComponentInstalled('catalog')) { ?>
                    <tr>
                        <td valign="top"><strong>Рубрика каталога: </strong><br />
                        <span class="hinttext">Пункт меню будет показывать записи из универсального каталога </span></td>
                        <td valign="top"><table width="100%" border="0" cellspacing="8" bgcolor="#F2F2F2" style="<?php echo (@$mod['linktype']=='uccat')?'border: solid 1px gray':''?>" id="t_uccat">
                                <tr>
                                    <td width="24"><input onclick="highlight('t_uccat')" name="mode" type="radio" class="mode" value="uccat" <?php if (@$mod['linktype']=='uccat') { echo  'checked'; } ?>/></td>
                                    <td width="429">
                                        <select name="uccat" id="uccat" style="width:300px">
                                            <?php
                                                if (@$mod['linktype']=='uccat') {
                                                    echo $inCore->getListItems('cms_uc_cats', $mod['linkid']);
                                                } else {
                                                    echo $inCore->getListItems('cms_uc_cats');
                                                }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                        </table></td>
                    </tr>
                <?php } ?>
                <?php  if ($inCore->isComponentInstalled('blog')) { ?>
                    <tr>
                        <td valign="top">
                            <strong>Блог: </strong><br />
                            <span class="hinttext">Пункт меню будет показывать записи из выбранного блога </span>
                        </td>
                        <td valign="top">
                            <table width="100%" border="0" cellspacing="8" bgcolor="#F2F2F2" style="<?php echo (@$mod['linktype']=='blog')?'border: solid 1px gray':''?>" id="t_blog">
                                <tr>
                                    <td width="24"><input onclick="highlight('t_blog')" name="mode" type="radio" class="mode" value="blog" <?php if (@$mod['linktype']=='blog') { echo  'checked'; } ?>/></td>
                                    <td width="429">
                                        <select name="blog" id="blog" style="width:300px">
                                            <?php
                                                if (@$mod['linktype']=='blog') {
                                                    echo $inCore->getListItems('cms_blogs', $mod['linkid'], 'title', 'asc', "owner='user'");
                                                } else {
                                                    echo $inCore->getListItems('cms_blogs', 0, 'title', 'asc', "owner='user'");
                                                }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <?php  } ?>
                <tr>
                    <td valign="top">
                        <strong>Компонент:</strong><br />
                        <span class="hinttext">Пункт меню будет запускать компонент сайта</span>
                    </td>
                    <td valign="top">
                        <table width="100%" border="0" cellspacing="8" bgcolor="#F2F2F2" style="<?php echo (@$mod['linktype']=='component')?'border: solid 1px gray':''?>" id="t_component">
                            <tr>
                                <td width="24"><input onclick="highlight('t_component')" name="mode" type="radio" class="mode" value="component" <?php if (@$mod['linktype']=='component') { echo  'checked'; } ?>/></td>
                                <td width="429">
                                    <select name="component" id="component" style="width:300px">
                                        <?php
                                            if (@$mod['linktype']=='component') {
                                                echo $inCore->getListItems('cms_components', $mod['linkid'], 'title', 'asc', 'internal=0', 'link');
                                            } else {
                                                echo $inCore->getListItems('cms_components', 0, 'title', 'asc', 'internal=0', 'link');
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div>
                <span style="margin-top:15px">
                    <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add') { echo 'value="Создать пункт"'; } else { echo 'value="Сохранить пункт"'; } ?> />
                    <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=menu';"/>
                </span>
                <input name="do" type="hidden" id="do" <?php if ($do=='add') { echo 'value="submit"'; } else { echo 'value="update"'; } ?> />
                <?php
                    if ($do=='edit'){
                        echo '<input name="id" type="hidden" value="'.$mod['id'].'" />';
                    }
                ?>
            </div>
        </form>
<?php
   }
}

?>