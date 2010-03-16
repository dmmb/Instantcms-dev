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

function applet_modules(){

    $inCore = cmsCore::getInstance();
    $inConf = cmsConfig::getInstance();
    $inDB   = cmsDatabase::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/modules', $adminAccess)) { cpAccessDenied(); }
	
	$GLOBALS['cp_page_title'] = 'Модули сайта';	
	cpAddPathway('Модули сайта', 'index.php?view=modules');	
	$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/modules.js"></script>';
	
	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort

	if ($do == 'config'){
	
		$mod = cpModuleById($id);
		
		if ($mod) {
			$file = 'modules/'.$mod.'/backend.php';
			if (file_exists($file)){
				include $file;
			}
		} else {
			header('location:index.php?view=modules&do=edit&id='.$id);
		}
	
	}
		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'new.gif';
		$toolmenu[0]['title'] = 'Добавить модуль';
		$toolmenu[0]['link'] = '?view=modules&do=add';

//		$toolmenu[1]['icon'] = 'install.gif';
//		$toolmenu[1]['title'] = 'Установить модуль';
//		$toolmenu[1]['link'] = '?view=install&do=module';

		$toolmenu[2]['icon'] = 'edit.gif';
		$toolmenu[2]['title'] = 'Редактировать выбранные';
		$toolmenu[2]['link'] = "javascript:checkSel('?view=modules&do=edit&multiple=1');";

		$toolmenu[3]['icon'] = 'show.gif';
		$toolmenu[3]['title'] = 'Публиковать выбранные';
		$toolmenu[3]['link'] = "javascript:checkSel('?view=modules&do=show&multiple=1');";

		$toolmenu[4]['icon'] = 'hide.gif';
		$toolmenu[4]['title'] = 'Скрыть выбранные';
		$toolmenu[4]['link'] = "javascript:checkSel('?view=modules&do=hide&multiple=1');";

		$toolmenu[5]['icon'] = 'delete.gif';
		$toolmenu[5]['title'] = 'Удалить выбранные';
		$toolmenu[5]['link'] = "javascript:checkSel('?view=modules&do=delete&multiple=1');";

		$toolmenu[6]['icon'] = 'reorder.gif';
		$toolmenu[6]['title'] = 'Сохранить порядок модулей';
		$toolmenu[6]['link'] = "javascript:checkSel('?view=modules&do=saveorder');";

		$toolmenu[7]['icon'] = 'autoorder.gif';
		$toolmenu[7]['title'] = 'Упорядочить модули';
		$toolmenu[7]['link'] = "?view=modules&do=autoorder";

		$toolmenu[8]['icon'] = 'help.gif';
		$toolmenu[8]['title'] = 'Помощь';
		$toolmenu[8]['link'] = "?view=help&topic=modules";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Заголовок';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';		$fields[1]['filter'] = 15;
		$fields[1]['link'] = '?view=modules&do=edit&id=%id%';

		$fields[2]['title'] = 'Название';	$fields[2]['field'] = 'name';		$fields[2]['width'] = '300';
		$fields[2]['filter'] = 15;
				
		$fields[3]['title'] = 'Показ';		$fields[3]['field'] = 'published';	$fields[3]['width'] = '100';	
		$fields[4]['title'] = 'Порядок';	$fields[4]['field'] = 'ordering';	$fields[4]['width'] = '100';	
		$fields[5]['title'] = 'Позиция';	$fields[5]['field'] = 'position';	$fields[5]['width'] = '100';	
		$fields[5]['filter'] = 10; 			$fields[5]['filterlist'] = cpGetList('positions');
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Настроить';
		$actions[0]['icon']  = 'config.gif';
		$actions[0]['link']  = '?view=modules&do=config&id=%id%';
		// Функция, которой передается ID объекта, и если она вернет TRUE то только тогда отобразится значок
		$actions[0]['condition'] = 'cpModuleHasConfig';
		
		$actions[1]['title'] = 'Редактировать';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=modules&do=edit&id=%id%';

		$actions[2]['title'] = 'Удалить';
		$actions[2]['icon']  = 'delete.gif';
		$actions[2]['confirm'] = 'Удалить модуль?';
		$actions[2]['link']  = '?view=modules&do=delete&id=%id%';
				
		//Print table
		cpListTable('cms_modules', $fields, $actions);

        ?>

        <div class="filter" id="com_filter">
            <table width="100%">
                <tbody>
                    <tr>
                        <td width="125">Фильтр по названию: </td>
                        <td width="">
                            <a href="javascript:" onclick="$('input#_filterText3').val('меню|Меню').trigger('keyup');">меню</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('стат|материалы').trigger('keyup');">статьи</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('коммент|Коммент').trigger('keyup');">комментарии</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('блог|Блог').trigger('keyup');">блоги</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('клуб|Клуб').trigger('keyup');">клубы</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('пользовател|авторизация').trigger('keyup');">пользователи</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('каталог|корзина').trigger('keyup');">каталог</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('фото|изображен').trigger('keyup');">фотографии</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('форум').trigger('keyup');">форум</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('файл').trigger('keyup');">файлы</a> |
                            <a href="javascript:" onclick="$('input#_filterText3').val('вопросы').trigger('keyup');">вопросы</a> |
                            <a href="javascript:" onclick="$('input._filterText').val('').trigger('keyup');" style="color:gray"> показать все</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <script type="text/javascript">
            $('.filter').eq(0).after('<div class="filter">'+$('#com_filter').html()+'</div>');
            $('#com_filter').remove();
        </script>

        <?php

	}

	if ($do == 'autoorder'){
		$sql = "SELECT * FROM cms_modules ORDER BY ordering";
		$rs = dbQuery($sql) ;
		
		if (mysql_num_rows($rs)){
			$ord = 1;
			while ($item = mysql_fetch_assoc($rs)){
				dbQuery("UPDATE cms_modules SET ordering = ".$ord." WHERE id=".$item['id']) ;
				$ord += 1;
			}				
		}
		header('location:index.php?view=modules&sort=ordering');		
	}
	
	if ($do == 'move_up'){
		if ($id >= 0){ dbMoveUp('cms_modules', $id, $co); }
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	if ($do == 'move_down'){
		if ($id >= 0){ dbMoveDown('cms_modules', $id, $co); }
		header('location:'.$_SERVER['HTTP_REFERER']);
	}

	if ($do == 'saveorder'){
		if(isset($_REQUEST['ordering'])) { 
			$ord = $_REQUEST['ordering'];
			$ids = $_REQUEST['ids'];
			
			foreach ($ord as $id=>$ordering){			
				dbQuery("UPDATE cms_modules SET ordering = $ordering WHERE id = ".$ids[$id]) ;						
			}
			header('location:?view=modules');

		}
	}

	if ($do == 'show'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbShow('cms_modules', $id);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_modules', $_REQUEST['item']);	
			$inCore->redirectBack();			
		}
		
	}

	if ($do == 'hide'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbHide('cms_modules', $id);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_modules', $_REQUEST['item']);	
			$inCore->redirectBack();			
		}		
	}
	
	if ($do == 'delete'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbDelete('cms_modules', $id);  }
		} else {
			dbDeleteList('cms_modules', $_REQUEST['item']);				
		}
		header('location:?view=modules');
	}
	
	if ($do == 'update'){

			$id             = $inCore->request('id', 'int', 0);
			
			$name           = $inCore->request('name', 'str', '');
			$title          = $inCore->request('title', 'str', '');
			$position       = $inCore->request('position', 'str', '');
			$showtitle      = $inCore->request('showtitle', 'int', 0);
			$content        = $inCore->request('content', 'html', '');
			$published      = $inCore->request('published', 'int', 0);
			$css_prefix     = $inCore->request('css_prefix', 'str', '');
			$allow_group    = $inCore->request('allow_group', 'int', 0);

            $template       = $inCore->request('template', 'str', '');

			$cache          = $inCore->request('cache', 'int', 0);
			$cachetime      = $inCore->request('cachetime', 'int', 0);
			$cacheint       = $inCore->request('cacheint', 'str', '');
			
			$sql = "UPDATE cms_modules 
					SET name='$name', 
						title='$title', 
						position='$position',
                        template='$template', 
						showtitle=$showtitle,";
						
					if ($content){	
						$sql .= "content='$content',";
					}
						
			$sql .=	"
						published=$published,
						css_prefix='$css_prefix',
						allow_group='$allow_group',
						cachetime = '$cachetime',
						cacheint = '$cacheint',
						cache = $cache
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;

			$sql = "DELETE FROM cms_modules_bind WHERE module_id = $id";
			dbQuery($sql) ;
			
			if ($inCore->request('show_all', 'int', 0)){
				$sql = "INSERT INTO cms_modules_bind (module_id, menu_id) 
						VALUES ($id, 0)";
				dbQuery($sql) ;	
			} else {		
				$showin = $_REQUEST['showin'];
				if (sizeof($showin)>0){
					foreach ($showin as $key=>$value){
						$sql = "INSERT INTO cms_modules_bind (module_id, menu_id) 
								VALUES ($id, $value)";
						dbQuery($sql) ;
					}
				}	
			}
					
			if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
				header('location:?view=modules');		
			} else {
				header('location:?view=modules&do=edit');		
			}
						
	}
	
	if ($do == 'submit'){

		$sql        = "SELECT ordering as max_o FROM cms_menu ORDER BY ordering DESC LIMIT 1";
		$result     = dbQuery($sql) ;
		$row        = mysql_fetch_assoc($result);
		$maxorder   = $row['max_o'] + 1;

        $name           = $inCore->request('name', 'str', '');
        $title          = $inCore->request('title', 'str', '');
        $position       = $inCore->request('position', 'str', '');
        $showtitle      = $inCore->request('showtitle', 'int', 0);
        $content        = $inCore->request('content', 'html', '');
        $published      = $inCore->request('published', 'int', 0);
        $css_prefix     = $inCore->request('css_prefix', 'str', '');
        $allow_group    = $inCore->request('allow_group', 'int', 0);

        $template       = $inCore->request('template', 'str', '');

        $cache          = $inCore->request('cache', 'int', 0);
        $cachetime      = $inCore->request('cachetime', 'int', 0);
        $cacheint       = $inCore->request('cacheint', 'str', '');

		$operate        = $inCore->request('operate', 'str', '');
		
		if ($operate == 'user'){ //USER MODULE
			$sql = "INSERT INTO cms_modules (id, position, name, title, is_external, content, ordering, showtitle, published, original, css_prefix, allow_group, cache, cachetime, cacheint, template)
					VALUES ('', '$position', '$name', '$title', 0, '$content', $maxorder, $showtitle, $published, 1, '$css_prefix', '$allow_group', 0, 24, 'HOUR', '$template')";
			dbQuery($sql) ;			
		}
		
		if ($operate == 'clone'){ //DUPLICATE MODULE
			$mod_id     = $inCore->request('clone_id', 'int', 0);
					
			$sql        = "SELECT * FROM cms_modules WHERE id = $mod_id LIMIT 1";
			$result     = dbQuery($sql) ;
			$original   = mysql_fetch_assoc($result);

			$sql = "INSERT INTO cms_modules (id, position, name, title, is_external, content, ordering, showtitle, published, user, config, css_prefix)
					VALUES ('', 
							'".$position."', 
							'".$original['name']."', 
							'".$title."', 
							".$original['is_external'].", 
							'".$original['content']."', 
							$maxorder, 
							".$showtitle.", 
							0,
							".$original['user'].",
							'".$original['config']."',
							'$css_prefix')";
			dbQuery($sql) ;					
						
			if ($inCore->request('del_orig', 'int', 0)){
				$sql = "DELETE FROM cms_modules WHERE id = $mod_id";
				dbQuery($sql) ;
			}
		}
		
		$sql     = "SELECT LAST_INSERT_ID() as lastid FROM cms_modules";
		$result  = dbQuery($sql) ;
		$row     = mysql_fetch_assoc($result);
		$lastid  = $row['lastid'];
		
		if (isset($_REQUEST['show_all'])){
			$sql = "INSERT INTO cms_modules_bind (id, module_id, menu_id) 
					VALUES ('', $lastid, 0)";
			dbQuery($sql) ;	
		} else {		
			$showin = $_REQUEST['showin'];
			if (sizeof($showin)>0){
				foreach ($showin as $key=>$value){
					$sql = "INSERT INTO cms_modules_bind (id, module_id, menu_id) 
							VALUES ('', $lastid, $value)";
					dbQuery($sql) ;
				}			
			}	
		}

		header('location:?view=modules');		
	}	  

   if ($do == 'add' || $do == 'edit'){

    	require('../includes/jwtabs.php');
		$GLOBALS['cp_page_head'][] = jwHeader();

		if ($do=='add'){
	 		 cpAddPathway('Добавить модуль', 'index.php?view=modules&do=add');
			 echo '<h3>Добавить модуль</h3>';
             $show_all = false;
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
	
					 $sql = "SELECT * FROM cms_modules WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
					 
					 $sql = "SELECT id FROM cms_modules_bind WHERE module_id = $id AND menu_id = 0 LIMIT 1";
					 $result = dbQuery($sql) ;			 
					 
					 if(mysql_num_rows($result)) { $show_all = true; } else { $show_all = false; }
					 					
					 echo '<h3>Редактировать модуль '.$ostatok.'</h3>';
 					 cpAddPathway($mod['name'], 'index.php?view=modules&do=edit&id='.$mod['id']);
			}   
			
 		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		if(@$mod['is_external']){
			$file = 'modules/'.$mod['content'].'/backend.php';
			if (file_exists($file)){
				$toolmenu[1]['icon'] = 'config.gif';
				$toolmenu[1]['title'] = 'Настроить модуль';
				$toolmenu[1]['link'] = '?view=modules&do=config&id='.$mod['id'];				
			}		
		}

		$toolmenu[2]['icon'] = 'cancel.gif';
		$toolmenu[2]['title'] = 'Отмена';
		$toolmenu[2]['link'] = 'javascript:history.go(-1);';
		
		cpToolMenu($toolmenu);

	?>
    <form id="addform" name="addform" method="post" action="index.php">
        <input type="hidden" name="view" value="modules" />

        <table class="proptable" width="100%" cellpadding="15" cellspacing="2">
            <tr>

                <!-- главная ячейка -->
                <td valign="top">

                    <div><strong>Заголовок модуля</strong> <span class="hinttext">&mdash; отображается на сайте</span></div>
                    <div>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td><input name="title" type="text" id="title" style="width:100%" value="<?php echo @$mod['title'];?>" /></td>
                                <td style="width:15px;padding-left:10px;padding-right:0px;">
                                    <input type="checkbox" title="Показывать заголовок" name="showtitle" <?php if ($mod['showtitle'] || $do=='add') { echo 'checked="checked"'; } ?> value="1">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:5px;">
                        <tr>
                            <td valign="top">
                                <div>
                                    <strong>Название модуля</strong> <span class="hinttext">&mdash; отображается в админке</span>
                                </div>
                                <div>
                                    <?php if (!isset($mod['user']) || @$mod['user']==1) { ?>
                                        <input name="name" type="text" id="name" style="width:99%" value="<?php echo @$mod['name'];?>" />
                                    <?php } else { ?>
                                        <input name="" type="text" id="name" style="width:99%" value="<?php echo @$mod['name'];?>" disabled="disabled" />
                                        <input name="name" type="hidden" value="<?php echo @$mod['name'];?>" />
                                    <?php } ?>
                                </div>
                            </td>
                            <td valign="top" width="160" style="padding-left:10px;">
                                <div>
                                    <strong>CSS префикс</strong>
                                </div>
                                <div>                                    
                                    <input name="css_prefix" type="text" id="css_prefix" value="<?php echo @$mod['css_prefix'];?>" style="width:154px" />
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-top:8px">
                        <strong>Позиция показа</strong> <span class="hinttext">&mdash; должна присутствовать в шаблоне</span>
                    </div>
                    <div>
                        <?php
                            include PATH.'/includes/config.inc.php';
                            $pos = cpModulePositions($_CFG['template']);
                        ?>
                        <select name="position" id="position" style="width:100%">
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
                    </div>

                    <div style="margin-top:15px">
                        <strong>Шаблон модуля</strong> <span class="hinttext">&mdash; Файлы из папки <b>modules/</b> вашего шаблона, названия которых начинаются на module</span>
                    </div>
                    <div>
                        <?php
                            $tpls = cmsPage::getModuleTemplates();
                        ?>
                        <select name="template" id="template" style="width:100%">
                            <?php
                                foreach($tpls as $tpl){
                                    $selected = ($mod['template']==$tpl || (!$mod['template'] && $tpl=='module.tpl' )) ? 'selected' : '';
                                    echo '<option value="'.$tpl.'" '.$selected.'>'.$tpl.'</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <?php if ($do=='add'){ ?>
                    <div style="margin-top:15px">
                        <strong>Тип модуля</strong>
                    </div>
                    <div>
                        <select name="operate" id="operate" onchange="checkDiv()" style="width:100%">
                            <option value="user" selected="selected">Пользовательский (новый)</option>
                            <option value="clone">Дубликат (копия)</option>
                        </select>
                    </div>
                    <?php } ?>

                    <?php if(!isset($mod['user']) || $mod['user']==1 || $do=='add'){ ?>
                        <div id="user_div">
                            <div style="margin-top:15px">
                                <strong>Содержимое модуля</strong>
                            </div>
                            <div><?php insertPanel(); ?></div>
                            <div>                             
                                <?php
                                        $inCore->insertEditor('content', $mod['content'], '250', '100%');
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                <div id="clone_div" style="display:none;">
                        <div style="margin-top:15px">
                            <strong>Скопировать модуль</strong>
                        </div>
                        <div>
                            <select name="clone_id" id="clone_id" style="width:100%">
                                <?php
                                    echo $inCore->getListItems('cms_modules');
                                ?>
                            </select>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist" style="margin-top:6px">
                                <tr>
                                    <td width="20"><input type="checkbox" name="del_orig" id="del_orig" value="1" /></td>
                                    <td><label for="del_orig">Удалить оригинал</label></td>
                                </tr>
                            </table>    
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
                            <td><label for="published"><strong>Публиковать модуль</strong></label></td>
                        </tr>
                        <tr>
                            <td width="20"><input name="show_all" id="show_all" type="checkbox" value="1"  onclick="checkGroupList()" <?php if ($show_all) { echo 'checked'; } ?> /></td>
                            <td><label for="show_all"><strong>Показывать на всех страницах сайта</strong></label></td>
                        </tr>
                    </table>

                    <?php
                        $sql = "SELECT * FROM cms_menu";
                        $result = dbQuery($sql) ;

                        if ($do=='edit'){
                            $sql2 = "SELECT * FROM cms_modules_bind WHERE module_id = ".$mod['id'];
                            $result2 = dbQuery($sql2);
                            $ord = array();
                            while ($r = mysql_fetch_assoc($result2)){
                                $ord[] = $r['menu_id'];
                            }
                        }
                        
                        echo '<div id="grp">';

                        echo '<div style="margin-top:13px">
                                <strong>Где показывать модуль?</strong>
                              </div>';

                        echo '<select style="width: 100%" id="showin" name="showin[]" size="9" multiple="multiple" '.(@$show_all ? 'disabled="disabled"' : '').'>';

                        if (mysql_num_rows($result)){
                            while ($item=mysql_fetch_assoc($result)){
                                echo '<option value="'.$item['id'].'"';
                                if ($do=='edit'){
                                    if (inArray($ord, $item['id'])){
                                        echo 'selected';
                                    }
                                }
                                echo '>';
                                echo $item['title'].'</option>';
                            }
                        }
                        echo '</select></div>';
                    ?>

                    {tab=Кеширование}

                        <div style="margin-top:4px">
                            <strong>Кешировать модуль?</strong>
                        </div>
                        <div>
                            <select name="cache" id="cache" style="width:100%">
                                <option value="0" <?php if (@!$mod['cache']) { echo 'selected'; } ?>>Нет</option>
                                <option value="1" <?php if (@$mod['cache']) { echo 'selected'; } ?>>Да</option>
                            </select>
                        </div>

                        <div style="margin-top:15px">
                            <strong>Период обновления кеша</strong>
                        </div>
                        <div>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:5px;">
                                <tr>
                                    <td valign="top"  width="100">
                                        <input name="cachetime" type="text" id="int_1" style="width:99%" value="<?php echo @(int)$mod['cachetime']?>"/>
                                    </td>
                                    <td valign="top" style="padding-left:5px">
                                        <select name="cacheint" id="int_2" style="width:100%">
                                            <option value="MINUTE"  <?php if(@strstr($mod['cacheint'], 'MINUTE')) { echo 'selected'; } ?>>минут</option>
                                            <option value="HOUR"  <?php if(@strstr($mod['cacheint'], 'HOUR')) { echo 'selected'; } ?>>часов</option>
                                            <option value="DAY" <?php if(@strstr($mod['cacheint'], 'DAY')) { echo 'selected'; } ?>>дней</option>
                                            <option value="MONTH" <?php if(@strstr($mod['cacheint'], 'MONTH')) { echo 'selected'; } ?>>месяцев</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div style="margin-top:15px">
                            <?php
                                if ($do=='edit'){
                                    if ($inCore->isCached('module', $mod['id'], $mod['cachetime'], $mod['cacheint'])){
                                        $t = 'module'.$mod['id'];
                                        $cfile = $_SERVER['DOCUMENT_ROOT'].'/cache/'.md5($t).'.html';
                                        if (file_exists($cfile)){
                                            $kb = round(filesize($cfile)/1024, 2);
                                            echo '<a href="applets/cache.php?do=delcache&target=module&id='.$mod['id'].'">Удалить кеш</a> ('.$kb.' Кб)';
                                        }
                                    } else {
                                        echo '<span style="color:gray">Сейчас нет кеша</span>';
                                    }
                                }
                            ?>
                        </div>

                    {tab=Доступ}

                        <div style="margin-top:4px">
                            <strong>Кому показывать этот модуль</strong>
                        </div>
                        <div>
                            <select name="allow_group" id="allow_group" style="width:100%">
                                <option value="-1" <?php if (@$mod['allow_group']==-1 || !isset($mod['allow_group'])) { echo 'selected="selected"'; } ?>>-- Все группы --</option>
                                <?php
                                    if (isset($mod['allow_group'])) {
                                        echo $inCore->getListItems('cms_user_groups', $mod['allow_group']);
                                    } else {
                                        echo $inCore->getListItems('cms_user_groups');
                                    }
                                ?>
                            </select>
                        </div>

                    {/tabs}

                    <?php echo jwTabs(ob_get_clean()); ?>
                    
                </td>

            </tr>
        </table>
        <p>
            <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add') { echo 'value="Создать модуль"'; } else { echo 'value="Сохранить модуль"'; } ?> />
            <input name="back" type="button" id="back" value="Отмена" onclick="window.history.back();"/>
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