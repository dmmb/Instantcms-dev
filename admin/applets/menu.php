<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
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
	
	echo '<div align="right" style="clear:both">[<a href="javascript:selectIcon(\'\')">Без иконки</a>] [<a href="javascript:hideIcons()">Закрыть</a>]</div>';
	
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

		$toolmenu[5]['icon'] = 'delete.gif';
		$toolmenu[5]['title'] = 'Удалить выбранные';
		$toolmenu[5]['link'] = "javascript:checkSel('?view=menu&do=delete&multiple=1');";

		$toolmenu[3]['icon'] = 'show.gif';
		$toolmenu[3]['title'] = 'Публиковать выбранные';
		$toolmenu[3]['link'] = "javascript:checkSel('?view=menu&do=show&multiple=1');";

		$toolmenu[4]['icon'] = 'hide.gif';
		$toolmenu[4]['title'] = 'Скрыть выбранные';
		$toolmenu[4]['link'] = "javascript:checkSel('?view=menu&do=hide&multiple=1');";

		$toolmenu[8]['icon'] = 'help.gif';
		$toolmenu[8]['title'] = 'Помощь';
		$toolmenu[8]['link'] = "?view=help&topic=menu";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';		$fields[1]['link'] = '?view=menu&do=edit&id=%id%';
		
		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '40';
		
		$fields[3]['title'] = 'Порядок';	$fields[3]['field'] = 'ordering';	$fields[3]['width'] = '100';	
				
		$fields[4]['title'] = 'Ссылка';		$fields[4]['field'] = 'id';		$fields[4]['width'] = '240';
		$fields[4]['prc'] = 'cpMenutypeById';

		$fields[5]['title'] = 'Меню';		$fields[5]['field'] = 'menu';		$fields[5]['width'] = '70';	$fields[5]['filter'] = 10;
		$fields[5]['filterlist'] = cpGetList('menu');

		$fields[6]['title'] = 'Шаблон';		$fields[6]['field'] = 'template';	$fields[6]['width'] = '70';
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
	
	if ($do == 'move_up'){
		$id = (int)$_REQUEST['id'];
		$ns = $inCore->nestedSetsInit('cms_menu');
		$ns->MoveOrdering($id, -1);
		$inCore->redirectBack();
	}

	if ($do == 'move_down'){
		$id = (int)$_REQUEST['id'];	
		$ns = $inCore->nestedSetsInit('cms_menu');
		$ns->MoveOrdering($id, 1);
		$inCore->redirectBack();
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
		header('location:?view=menu');
	}
	
	if ($do == 'update'){
		if(isset($_REQUEST['id'])) { 
			$id = (int)$_REQUEST['id'];
			
			$title = $inCore->request('title', 'str', '');
			$menu = $_REQUEST['menu'];
			$link = $inCore->getMenuLink($_REQUEST['mode'], $_REQUEST[$_REQUEST['mode']], $id);
			$linktype = $_REQUEST['mode'];
			$linkid = $_REQUEST[$_REQUEST['mode']];
			$target = $_REQUEST['target'];		
			$published = $_REQUEST['published'];
			$template = $_REQUEST['template'];

			$is_public      = $inCore->request('is_public', 'int', '');
			if (!$is_public){
				$access_list = $inCore->request('allow_group', 'array_int');
				$access_list = $inCore->arrayToYaml($access_list);
			}

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
						published='$published',
						template='$template',
						access_list='$access_list',
						iconurl='$iconurl'
					WHERE id = '$id'
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

		$menu     = $_REQUEST['menu'];
		$title    = $inCore->request('title', 'str', '');
		$target   = $_REQUEST['target'];
		$link     = $inCore->getMenuLink($_REQUEST['mode'], $_REQUEST[$_REQUEST['mode']], $id);
        $linktype = $_REQUEST['mode'];
        $linkid   = $_REQUEST[$_REQUEST['mode']];
		$template = $_REQUEST['template'];
		$iconurl  = $_REQUEST['iconurl'];
		$is_public = $inCore->request('is_public', 'int', '');
		if (!$is_public){
			$access_list = $inCore->request('allow_group', 'array_int');
			$access_list = $inCore->arrayToYaml($access_list);
		}
		$parent_id = $_REQUEST['parent_id'];
			
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
					access_list='$access_list', 
					iconurl='$iconurl'
				WHERE id = '$myid'";
	
		dbQuery($sql) or die(mysql_error().$sql);
		header('location:?view=menu');		
	}	  

	if ($do == 'submitmenu'){

		$sql = "SELECT ordering as max_o FROM cms_modules ORDER BY ordering DESC LIMIT 1";
		$result = dbQuery($sql) ;
		$row = mysql_fetch_assoc($result);
		$maxorder = $row['max_o'] + 1;
	
		$menu = $_REQUEST['menu'];
		$title = $inCore->request('title', 'str', '');
		$position = $_REQUEST['position'];
		$published = $_REQUEST['published'];
		$css_prefix = $_REQUEST['css_prefix'];
		$is_public      = $inCore->request('is_public', 'int', '');
		if (!$is_public){
			$access_list = $inCore->request('allow_group', 'array_int');
			$access_list = $inCore->arrayToYaml($access_list);
		}
	
		$cfg['menu'] = $menu;		
		$cfg_str = $inCore->arrayToYaml($cfg);
	
		$sql = "INSERT INTO cms_modules (position, name, title, is_external, content, ordering, showtitle, published, user, config, css_prefix, access_list)
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
						'$css_prefix',
						'$access_list')";
	
		dbQuery($sql) ;
		
		$newid = dbLastId('cms_modules');
		
		header('location:?view=modules&do=edit&id='.$newid);		
	}	  

   if ($do == 'addmenu'){
		$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/menu.js"></script>';


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
        <form id="addform" name="addform" action="index.php?view=menu&do=submitmenu" method="post">
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
                                <?php for($m=1;$m<=15;$m++){ ?>
                                    <option value="menu<?php echo $m; ?>" <?php if (@$mod['menu']=='menu'.$m) { echo 'selected'; }?>>Дополнительное меню <?php echo $m; ?></option>
                                <?php } ?>
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
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist" style="margin-top:5px">
                        <tr>
                            <td width="20">
                                <?php
								
									$groups = cmsUser::getGroups();

                                    $style  = 'disabled="disabled"';
                                    $public = 'checked="checked"';

                                    if ($do == 'edit'){

                                        if ($mod['access_list']){
                                            $public = '';
                                            $style  = '';
											
											$access_list = $inCore->yamlToArray($mod['access_list']);

                                        }
                                    }
                                ?>
                                <input name="is_public" type="checkbox" id="is_public" onclick="checkAccesList()" value="1" <?php echo $public?> />
                            </td>
                            <td><label for="is_public"><strong>Общий доступ</strong></label></td>
                        </tr>
                    </table>
                    <div style="padding:5px">
                        <span class="hinttext">
                            Если отмечено, назначение пункта меню будет видно всем пользователям. Снимите галочку, чтобы вручную выбрать разрешенные группы пользователей.
                        </span>
                    </div>

                    <div style="margin-top:10px;padding:5px;padding-right:0px;" id="grp">
                        <div>
                            <strong>Показывать группам:</strong><br />
                            <span class="hinttext">
                                Можно выбрать несколько, удерживая CTRL.
                            </span>
                        </div>
                        <div>
                            <?php
                                echo '<select style="width: 99%" name="allow_group[]" id="allow_group" size="6" multiple="multiple" '.$style.'>';

                                if ($groups){
									foreach($groups as $group){
                                        echo '<option value="'.$group['id'].'"';
                                        if ($do=='edit'){
                                            if (inArray($access_list, $group['id'])){
                                                echo 'selected';
                                            }
                                        }

                                        echo '>';
                                        echo $group['title'].'</option>';
									}

                                }
                                
                                echo '</select>';
                            ?>
                        </div>
                    </div>
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

        require('../includes/jwtabs.php');
		$GLOBALS['cp_page_head'][] = jwHeader();

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

        <table class="proptable" width="100%" cellpadding="15" cellspacing="2">
            <tr>

                <!-- главная ячейка -->
                <td valign="top">

                    <div><strong>Заголовок пункта меню</strong> <span class="hinttext">&mdash; отображается на сайте</span></div>
                    <div><input name="title" type="text" id="title" style="width:100%" value="<?php echo @$mod['title'];?>" /></div>

                    <div>
                        <select name="menu" id="menu" style="width:100%">
                            <option value="mainmenu" <?php if (@$mod['menu']=='mainmenu' || !isset($mod['menu'])) { echo 'selected'; }?>>Главное меню</option>
                            <?php for($m=1;$m<=15;$m++){ ?>
                                <option value="menu<?php echo $m; ?>" <?php if (@$mod['menu']=='menu'.$m) { echo 'selected'; }?>>Дополнительное меню <?php echo $m; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div><strong>Родительский пункт</strong></div>
                    <div>
                        <?php
                            $rootid = dbGetField('cms_menu', 'parent_id=0', 'id');
                        ?>
                        <select name="parent_id" size="10" id="parent_id" style="width:100%">
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
                    </div>

                    <div><strong>Действие пункта меню</strong></div>
                    <div>
                        <select name="mode" id="linktype" style="width:100%" onchange="showMenuTarget()">
                            <option value="link" <?php if (@$mod['linktype']=='link' || !isset($mod['mode'])) { echo 'selected'; }?>>Открыть ссылку</option>
                            <option value="content" <?php if (@$mod['linktype']=='content') { echo 'selected'; }?>>Открыть статью</option>
                            <option value="category" <?php if (@$mod['linktype']=='category') { echo 'selected'; }?>>Открыть раздел (список статей)</option>
                            <option value="component" <?php if (@$mod['linktype']=='component') { echo 'selected'; }?>>Открыть компонент</option>
                            <option value="blog" <?php if (@$mod['linktype']=='blog') { echo 'selected'; }?>>Открыть блог</option>
                            <option value="uccat" <?php if (@$mod['linktype']=='uccat') { echo 'selected'; }?>>Открыть категорию каталога</option>
                            <option value="pricecat" <?php if (@$mod['linktype']=='pricecat') { echo 'selected'; }?>>Открыть категорию прайс-листа</option>
                            <option value="photoalbum" <?php if (@$mod['linktype']=='photoalbum') { echo 'selected'; }?>>Открыть альбом фотогалереи</option>
                        </select>                        
                    </div>

                    <div id="t_link" class="menu_target" style="display:<?php if ($mod['linktype']=='link'||$mod['linktype']=='ext'||!$mod['linktype']) { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Адрес ссылки</strong> <span class="hinttext">&mdash; для внешних ссылок не забывайте префикс <b>http://</b></span>
                        </div>
                        <div>
                            <input name="link" type="text" id="link" size="50" style="width:100%" <?php if (@$mod['linktype']=='link'||@$mod['linktype']=='ext') { echo  'value="'.$mod['link'].'"'; } ?>/>
                        </div>
                    </div>

                    <div id="t_content" class="menu_target" style="display:<?php if ($mod['linktype']=='content') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Выберите статью</strong>
                        </div>
                        <div>
                            <select name="content" id="content" style="width:100%">
                                <?php
                                    if (@$mod['linktype']=='content') {
                                        echo $inCore->getListItems('cms_content', $mod['linkid']);
                                    } else {
                                        echo $inCore->getListItems('cms_content');
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="t_category" class="menu_target" style="display:<?php if ($mod['linktype']=='category') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Выберите раздел</strong>
                        </div>
                        <div>
                            <select name="category" id="category" style="width:100%">
                                    <?php
                                    if (@$mod['linktype']=='category') {
                                        echo $inCore->getListItemsNS('cms_category', $mod['linkid']);
                                    } else {
                                        echo $inCore->getListItemsNS('cms_category');
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="t_component" class="menu_target" style="display:<?php if ($mod['linktype']=='component') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Выберите компонент</strong>
                        </div>
                        <div>
                           <select name="component" id="component" style="width:100%">
                                <?php
                                    if (@$mod['linktype']=='component') {
                                        echo $inCore->getListItems('cms_components', $mod['linkid'], 'title', 'asc', 'internal=0', 'link');
                                    } else {
                                        echo $inCore->getListItems('cms_components', 0, 'title', 'asc', 'internal=0', 'link');
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="t_blog" class="menu_target" style="display:<?php if ($mod['linktype']=='blog') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Выберите блог</strong>
                        </div>
                        <div>
                           <select name="blog" id="blog" style="width:100%">
                                <?php
                                    if (@$mod['linktype']=='blog') {
                                        echo $inCore->getListItems('cms_blogs', $mod['linkid'], 'title', 'asc', "owner='user'");
                                    } else {
                                        echo $inCore->getListItems('cms_blogs', 0, 'title', 'asc', "owner='user'");
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="t_uccat" class="menu_target" style="display:<?php if ($mod['linktype']=='uccat') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Выберите категорию каталога</strong>
                        </div>
                        <div>
                           <select name="uccat" id="uccat" style="width:100%">
                                <?php
                                    if (@$mod['linktype']=='uccat') {
                                        echo $inCore->getListItems('cms_uc_cats', $mod['linkid']);
                                    } else {
                                        echo $inCore->getListItems('cms_uc_cats');
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div id="t_pricecat" class="menu_target" style="display:<?php if ($mod['linktype']=='pricecat') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Выберите категорию прайс-листа</strong>
                        </div>
                        <div>
                           <select name="pricecat" id="pricecat" style="width:100%">
                                <?php
                                    if (@$mod['linktype']=='pricecat') {
                                        echo $inCore->getListItems('cms_price_cats', $mod['linkid']);
                                    } else {
                                        echo $inCore->getListItems('cms_price_cats');
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div id="t_photoalbum" class="menu_target" style="display:<?php if ($mod['linktype']=='photoalbum') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>Выберите фотоальбом</strong>
                        </div>
                        <div>
                           <select name="photoalbum" id="photoalbum" style="width:100%">
                                <?php
                                    if (@$mod['linktype']=='photoalbum') {
                                        echo $inCore->getListItems('cms_photo_albums', $mod['linkid'], 'id', 'ASC', 'NSDiffer = ""');
                                    } else {
                                        echo $inCore->getListItems('cms_photo_albums', 0, 'id', 'ASC', 'NSDiffer = ""');
                                    }
                                ?>
                            </select>
                        </div>
                    </div>

                </td>

                <!-- боковая ячейка -->
                <td width="300" valign="top" style="background:#ECECEC;">

                    <?php ob_start(); ?>

                    {tab=Публикация}

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <tr>
                                <td width="20"><input type="checkbox" name="published" id="published" value="1" <?php if ($mod['published'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                                <td><label for="published"><strong>Публиковать пункт меню</strong></label></td>
                            </tr>
                        </table>

                        <div style="margin-top:15px">
                            <strong>Открывать пункт меню</strong>
                        </div>
                        <div>
                            <select name="target" id="target" style="width:100%">
                                <option value="_self" <?php if (@$mod['target']=='_self') { echo 'selected'; }?>>В этом же окне (self)</option>
                                <option value="_parent">В родительском окне (parent)</option>
                                <option value="_blank" <?php if (@$mod['target']=='_blank') { echo 'selected'; }?>>В новом окне (blank)</option>
                                <option value="_top" <?php if (@$mod['target']=='_top') { echo 'selected'; }?>>Поверх всех окон (top)</option>
                            </select>
                        </div>

                        <div style="margin-top:15px">
                            <strong>Шаблон сайта</strong><br/>
                            <span class="hinttext">Выберите, если нужно чтобы открытие пункта меню сопровождалось сменой дизайна сайта</span>
                        </div>
                        <div>
                            <select name="template" id="template" style="width:100%">
                                <option value="0" <?php if (@$mod['template']==0 || !$mod['template']) { echo 'selected'; } ?>>По-умолчанию</option>
                                <?php
                                    if (isset($mod['template'])){
                                        $inCore->templatesList($mod['template']);
                                    } else {
                                        $inCore->templatesList(-1);
                                    }
                                ?>
                            </select>
                        </div>

                        <div style="margin-top:15px">
                            <strong>Иконка</strong><br/>
                            <span class="hinttext">Название файла в папке "/images/menuicons"</span>
                        </div>
                        <div>
                            <input name="iconurl" type="text" id="iconurl" size="30" value="<?php echo @$mod['iconurl'];?>" style="width:100%"/>
                            <div>
                                <a id="iconlink" style="display:block;" href="javascript:showIcons()"> Выбрать иконку</a>
                                <div id="icondiv" style="display:none; padding:6px;border:solid 1px gray;background:#FFF">
                                    <div><?php iconList(); ?></div>
                                </div>
                            </div>
                        </div>

                    {tab=Доступ}
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist" style="margin-top:5px">
                        <tr>
                            <td width="20">
                                <?php
								
									$groups = cmsUser::getGroups();

                                    $style  = 'disabled="disabled"';
                                    $public = 'checked="checked"';

                                    if ($do == 'edit'){

                                        if ($mod['access_list']){
                                            $public = '';
                                            $style  = '';
											
											$access_list = $inCore->yamlToArray($mod['access_list']);

                                        }
                                    }
                                ?>
                                <input name="is_public" type="checkbox" id="is_public" onclick="checkAccesList()" value="1" <?php echo $public?> />
                            </td>
                            <td><label for="is_public"><strong>Общий доступ</strong></label></td>
                        </tr>
                    </table>
                    <div style="padding:5px">
                        <span class="hinttext">
                            Если отмечено, назначение пункта меню будет видно всем пользователям. Снимите галочку, чтобы вручную выбрать разрешенные группы пользователей.
                        </span>
                    </div>

                    <div style="margin-top:10px;padding:5px;padding-right:0px;" id="grp">
                        <div>
                            <strong>Показывать группам:</strong><br />
                            <span class="hinttext">
                                Можно выбрать несколько, удерживая CTRL.
                            </span>
                        </div>
                        <div>
                            <?php
                                echo '<select style="width: 99%" name="allow_group[]" id="allow_group" size="6" multiple="multiple" '.$style.'>';

                                if ($groups){
									foreach($groups as $group){
                                        echo '<option value="'.$group['id'].'"';
                                        if ($do=='edit' && $mod['access_list']){
                                            if (inArray($access_list, $group['id'])){
                                                echo 'selected';
                                            }
                                        }

                                        echo '>';
                                        echo $group['title'].'</option>';
									}

                                }
                                
                                echo '</select>';
                            ?>
                        </div>
                    </div>

                    {/tabs}

                    <?php echo jwTabs(ob_get_clean()); ?>

                </td>

            </tr>
        </table>

        <p>
            <input name="add_mod" type="button" onclick="submitItem()" id="add_mod" <?php if ($do=='add') { echo 'value="Создать пункт"'; } else { echo 'value="Сохранить пункт"'; } ?> />
            <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=menu';"/>
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