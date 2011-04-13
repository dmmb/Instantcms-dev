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

function createMenuItem($menu, $id, $title){
    $inCore = cmsCore::getInstance();

	$rootid = dbGetField('cms_menu', 'parent_id=0', 'id');

	$ns = $inCore->nestedSetsInit('cms_menu');
	$myid = $ns->AddNode($rootid);
	
    $link = $inCore->getMenuLink('category', $id, $myid);

	$sql = "UPDATE cms_menu 
			SET menu='$menu', 
				title='$title', 
				link='$link',
				linktype='category',
				linkid='$id', 
				target='_self', 
				published='1', 
				template='0', 
				access_list='', 
				iconurl=''
			WHERE id = '$myid'";

	dbQuery($sql) or die(mysql_error().$sql);
	return true;
}

function applet_cats(){

    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();

    $GLOBALS['cp_page_title'] = 'Разделы сайта';
    cpAddPathway('Разделы сайта', 'index.php?view=tree');

    $inCore->loadModel('content');
    $model = new cms_model_content();

    if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
    if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
    if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }
    
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'new.gif';
		$toolmenu[0]['title'] = 'Добавить раздел';
		$toolmenu[0]['link'] = '?view=cats&do=add';

		$toolmenu[1]['icon'] = 'edit.gif';
		$toolmenu[1]['title'] = 'Редактировать выбранные';
		$toolmenu[1]['link'] = "javascript:checkSel('?view=cats&do=edit&multiple=1');";

		$toolmenu[2]['icon'] = 'show.gif';
		$toolmenu[2]['title'] = 'Публиковать выбранные';
		$toolmenu[2]['link'] = "javascript:checkSel('?view=cats&do=show&multiple=1');";

		$toolmenu[3]['icon'] = 'hide.gif';
		$toolmenu[3]['title'] = 'Скрыть выбранные';
		$toolmenu[3]['link'] = "javascript:checkSel('?view=cats&do=hide&multiple=1');";

		$toolmenu[4]['icon'] = 'delete.gif';
		$toolmenu[4]['title'] = 'Удалить выбранные';
		$toolmenu[4]['link'] = "javascript:checkSel('?view=cats&do=delete&multiple=1');";

		$toolmenu[5]['icon'] = 'reorder.gif';
		$toolmenu[5]['title'] = 'Сохранить порядок элементов';
		$toolmenu[5]['link'] = "javascript:checkSel('?view=cats&do=saveorder');";

		$toolmenu[6]['icon'] = 'config.gif';
		$toolmenu[6]['title'] = 'Настроить каталог статей';
		$toolmenu[6]['link'] = "?view=components&do=config&link=content";

		$toolmenu[7]['icon'] = 'help.gif';
		$toolmenu[7]['title'] = 'Помощь';
		$toolmenu[7]['link'] = "?view=help&topic=cats";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'ID';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';		$fields[1]['link'] = '?view=cats&do=edit&id=%id%';
		$fields[1]['filter'] = 15;

		$fields[2]['title'] = 'Родитель';	$fields[2]['field'] = 'parent_id';		$fields[2]['width'] = '300';	
		$fields[2]['prc'] = 'cpCatById'; 	$fields[2]['filter'] = 1;		$fields[2]['filterlist'] = cpGetList('cms_category');

		$fields[3]['title'] = 'Показ';		$fields[3]['field'] = 'published';	$fields[3]['width'] = '100';	
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Добавить статью';
		$actions[0]['icon']  = 'add.gif';
		$actions[0]['link']  = '?view=content&do=add&to=%id%';

		$actions[1]['title'] = 'Редактировать';
		$actions[1]['icon']  = 'edit.gif';
		$actions[1]['link']  = '?view=cats&do=edit&id=%id%';

		$actions[2]['title'] = 'Удалить';
		$actions[2]['icon']  = 'delete.gif';
		$actions[2]['confirm'] = 'Удалить раздел? Статьи не будут удалены.';
		$actions[2]['link']  = '?view=cats&do=delete&id=%id%';
				
		//Print table
		cpListTable('cms_category', $fields, $actions, 'parent_id>0', 'NSLeft');		
	}
	
	function reorder(){
		$sql = "SELECT * FROM cms_category ORDER BY NSLeft";
		$rs = dbQuery($sql) ;
		
		if (mysql_num_rows($rs)){
			$level = array();
			while ($item = mysql_fetch_assoc($rs)){
				if (isset($level[$item['NSLevel']])){
					$level[$item['NSLevel']] += 1;
				} else {
					$level[] = 1;
				}
				dbQuery("UPDATE cms_category SET ordering = ".$level[$item['NSLevel']]." WHERE id=".$item['id']) ;
			}				
		}
	}
	
	if ($do == 'saveorder'){
		if(isset($_REQUEST['ordering'])) { 
			$ord = $_REQUEST['ordering'];
			$ids = $_REQUEST['ids'];
			
			foreach ($ord as $id=>$ordering){			
				dbQuery("UPDATE cms_category SET ordering = $ordering WHERE id = ".$ids[$id]) ;						
			}
			header('location:?view=tree');

		}
	}

	if ($do == 'show'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbShow('cms_category', $id);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_category', $_REQUEST['item']);				
			$inCore->redirectBack();
		}
		
	}

	if ($do == 'hide'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbHide('cms_category', $id);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_category', $_REQUEST['item']);		
			$inCore->redirectBack();		
		}
		
	}
	
	if ($do == 'delete'){
        $is_with_content = $inCore->inRequest('content');

        $model->deleteCategory($id, $is_with_content);
		reorder();
		header('location:?view=tree');
	}
	
	if ($do == 'update'){
		if(isset($_REQUEST['id'])) { 

			$category['id']				= (int)$_REQUEST['id'];
			$category['title']			= $inCore->request('title', 'str');
			$category['url']			= $inCore->request('url', 'str');
			$category['parent_id']		= $inCore->request('parent_id', 'int');
			$category['description'] 	= $inCore->request('description', 'html');
            $category['description']      = $inDB->escape_string($category['description']);
			$category['published'] 		= $inCore->request('published', 'int', 0);
			$category['showdate'] 		= $inCore->request('showdate', 'int', 0);
			$category['showcomm'] 		= $inCore->request('showcomm', 'int', 0);
			$category['orderby'] 		= $inCore->request('orderby', 'str');
			$category['orderto']		= $inCore->request('orderto', 'str');
			$category['modgrp_id'] 		= $inCore->request('modgrp_id', 'int', 0);
			$category['maxcols'] 		= $inCore->request('maxcols', 'int', 0);
			$category['showtags'] 		= $inCore->request('showtags', 'int', 0);
			$category['showrss'] 		= $inCore->request('showrss', 'int', 0);
			$category['showdesc'] 		= $inCore->request('showdesc', 'int', 0);
			$category['is_public'] 		= $inCore->request('is_public', 'int', 0);
			$category['tpl'] 			= $inCore->request('tpl', 'str', 'com_content_view.tpl');

            $category['cost']           = $inCore->request('cost', 'str', '');
            if (!is_numeric($category['cost'])) { $category['cost'] = ''; }

			$album = array();
			$album['id']       = $inCore->request('album_id', 'int', 0);
			$album['titles']   = $inCore->request('album_titles', 'int', 0);
			$album['header']   = $inCore->request('album_header', 'str', '');
			$album['orderby']  = $inCore->request('album_orderby', 'str', '');
			$album['orderto']  = $inCore->request('album_orderto', 'str', '');
			$album['maxcols']  = $inCore->request('album_maxcols', 'int', 0);
			$album['max']	   = $inCore->request('album_max', 'int', 0);
			
			$photoalbum = serialize($album);

			$ns = $inCore->nestedSetsInit('cms_category');
			$ns->MoveNode($category['id'], $category['parent_id']);

            $old = $inDB->get_fields('cms_category', "id={$category['id']}", '*');

            if ($category['url']) { $category['url'] = cmsCore::strToURL($category['url']); }
            $seolink    = $model->getCategorySeoLink($category);

            $sql = "UPDATE cms_category
                    SET parent_id={$category['parent_id']},
                        title='{$category['title']}',
                        description='{$category['description']}',
                        published={$category['published']},
                        showdate={$category['showdate']},
                        showcomm={$category['showcomm']},
                        orderby='{$category['orderby']}',
                        orderto='{$category['orderto']}',
                        modgrp_id='{$category['modgrp_id']}',
                        maxcols='{$category['maxcols']}',
                        showtags={$category['showtags']},
                        showrss={$category['showrss']},
                        showdesc={$category['showdesc']},
                        is_public={$category['is_public']},
                        photoalbum='$photoalbum',
                        cost='{$category['cost']}',
                        seolink='$seolink',
                        url='{$category['url']}',
                        tpl='{$category['tpl']}'
                     WHERE id = {$category['id']}
                     LIMIT 1";
            dbQuery($sql) ;
            reorder();
            
            //обновляем УРЛы всех вложенных разделов
            if ($seolink != $old['seolink']){
                $sql = "SELECT id, title, url, seolink
                        FROM cms_category
                        WHERE NSLeft > {$old['NSLeft']} AND NSRight < {$old['NSRight']}";
                $res = $inDB->query($sql);
                if ($inDB->num_rows($res)){
                    while($subcat = $inDB->fetch_assoc($res)){
                        if ($subcat['url']) { $subcat['url'] = cmsCore::strToURL($subcat['url']); }
                        $sub_seolink = $model->getCategorySeoLink($subcat);
                        $inDB->query("UPDATE cms_category SET seolink='{$sub_seolink}' WHERE id={$subcat['id']}");
                    }
                }
            }

            //обновляем ссылки меню
            $inDB = cmsDatabase::getInstance();
            $menuid = $inDB->get_field('cms_menu', "linktype='category' AND linkid={$category['id']}", 'id');
            if ($menuid){
                $menulink = $inCore->getMenuLink('category', $category['id'], $menuid);
                $inDB->query("UPDATE cms_menu SET link='{$menulink}' WHERE id={$menuid}");
            }
			
			if (!$inCore->request('is_access', 'int', 0)){
				$showfor = $_REQUEST['showfor'];				
                $model->setArticleAccess($category['id'], $showfor, 'category');
			} else {
                $model->clearArticleAccess($category['id'], 'category');
            }
			
			if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
				header('location:?view=tree&cat_id='.$category['id']);
			} else {
				header('location:?view=cats&do=edit');		
			}
		}
	}
	
	if ($do == 'submit'){

        $category['title']          = $inCore->request('title', 'str');
        $category['url']            = $inCore->request('url', 'str');
        $category['parent_id']      = $inCore->request('parent_id', 'int');
        $category['description']    = $inCore->request('description', 'html');
        $category['description']    = $inDB->escape_string($category['description']);
        $category['published']      = $inCore->request('published', 'int', 0);
        $category['showdate']       = $inCore->request('showdate', 'int', 0);
        $category['showcomm']       = $inCore->request('showcomm', 'int', 0);
        $category['orderby']        = $inCore->request('orderby', 'str');
        $category['orderto']        = $inCore->request('orderto', 'str');
        $category['modgrp_id']      = $inCore->request('modgrp_id', 'int', 0);
        $category['maxcols']        = $inCore->request('maxcols', 'int', 0);
        $category['showtags']       = $inCore->request('showtags', 'int', 0);
        $category['showrss']        = $inCore->request('showrss', 'int', 0);
        $category['showdesc']       = $inCore->request('showdesc', 'int', 0);
        $category['is_public']      = $inCore->request('is_public', 'int', 0);        
        $category['tpl']            = $inCore->request('tpl', 'str', 'com_content_view.tpl');

        $category['cost']           = $inCore->request('cost', 'str', 0);
        if (!is_numeric($category['cost'])) { $category['cost'] = ''; }

        $album = array();
        $album['id']       = $inCore->request('album_id', 'int', 0);
        $album['titles']   = $inCore->request('album_titles', 'int', 0);
        $album['header']   = $inCore->request('album_header', 'str', '');
        $album['orderby']  = $inCore->request('album_orderby', 'str', '');
        $album['orderto']  = $inCore->request('album_orderto', 'str', '');
        $album['maxcols']  = $inCore->request('album_maxcols', 'int', 0);
        $album['max']	  = $inCore->request('album_max', 'int', 0);
        
        $photoalbum = serialize($album);
		
        $ns = $inCore->nestedSetsInit('cms_category');
        $category['id'] = $ns->AddNode($category['parent_id']);

        if (!$category['title']) { $category['title'] = 'Раздел #'.$category['id']; }

        if ($category['url']) { $category['url'] = cmsCore::strToURL($category['url']); }
        $seolink    = $model->getCategorySeoLink($category);
		
        if ($category['id']){

			$sql = "UPDATE cms_category
					SET parent_id={$category['parent_id']},
						title='{$category['title']}',
						description='{$category['description']}',
						published={$category['published']},
						showdate={$category['showdate']},
						showcomm={$category['showcomm']},
						orderby='{$category['orderby']}',
						orderto='{$category['orderto']}',
						modgrp_id='{$category['modgrp_id']}',
						maxcols='{$category['maxcols']}',
						showtags={$category['showtags']},
						showrss={$category['showrss']},
						showdesc={$category['showdesc']},
						is_public={$category['is_public']},
						photoalbum='$photoalbum',
                        cost='{$category['cost']}',
                        seolink='$seolink',
                        url='{$category['url']}',
                        tpl='{$category['tpl']}'
                    WHERE id = {$category['id']}
                    LIMIT 1";

			dbQuery($sql);

			if (!$inCore->request('is_access', 'int', 0)){
				$showfor = $_REQUEST['showfor'];				
                $model->setArticleAccess($category['id'], $showfor, 'category');
			} else {
                $model->clearArticleAccess($category['id'], 'category');
            }
        }
        
        reorder();

        $inmenu = $inCore->request('createmenu', 'str', '');

        if ($inmenu){
            createMenuItem($inmenu, $category['id'], $category['title']);
        }
	
        header('location:?view=tree');

    }

   if ($do == 'add' || $do == 'edit'){
 
	 	require('../includes/jwtabs.php');
		$GLOBALS['cp_page_head'][] = jwHeader();
		$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/content.js"></script>';
 
 		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);
   
		if ($do=='add'){
			 echo '<h3>Добавить раздел</h3>';
 	 		 cpAddPathway('Добавить раздел', 'index.php?view=cats&do=add');
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
	
					 $sql = "SELECT * FROM cms_category WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
						if(@$mod['photoalbum']){
							$mod['photoalbum'] = unserialize($mod['photoalbum']);
						}
					 }
					
					 echo '<h3>Редактировать раздел '.$ostatok.'</h3>';
 					 cpAddPathway($mod['title'], 'index.php?view=cats&do=edit&id='.$mod['id']);
			}   
	?>

    <form id="addform" name="addform" method="post" action="index.php">
        <input type="hidden" name="view" value="cats" />
        <table class="proptable" width="100%" cellpadding="15" cellspacing="2">
            <tr>

                <!-- главная ячейка -->
                <td valign="top">
                    <table border="0" cellpadding="0" cellspacing="5" width="100%">
                      <tbody>
                        <tr>
                          <td>
                            <strong>Название раздела</strong>
                          </td>
                          <td width="190" style="padding-left:6px">
                            <strong>Шаблон раздела</strong>
                          </td>
                        </tr>
                        <tr>
                          <td>
                        <input name="title" type="text" id="title" style="width:100%" value="<?php echo @$mod['title'];?>" />
                          </td>
                          <td style="padding-left:6px">
                            <input name="tpl" type="text" style="width:98%" value="<?php echo @$mod['tpl'];?>">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div><strong>Родительский раздел</strong></div>
                    <div>
                        <div class="parent_notice" style="color:red;margin:4px 0px;display:none">Раздел будет вложен сам в себя. Выберите другого родителя.</div>
                        <select name="parent_id" size="12" id="parent_id" style="width:100%" onchange="if($(this).val()=='<?php echo $mod['id']; ?>'){ $('.parent_notice').show(); } else { $('.parent_notice').hide(); }">
                            <?php $rootid = dbGetField('cms_category', 'parent_id=0', 'id'); ?>
                            <option value="<?php echo $rootid; ?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- Корневой раздел --</option>
                            <?php
                                if (isset($mod['parent_id'])){
                                    echo $inCore->getListItemsNS('cms_category', $mod['parent_id']);
                                } else {
                                    echo $inCore->getListItemsNS('cms_category');
                                }
                            ?>
                        </select>
                    </div>

                    <div><strong>Описание раздела</strong></div>
                    <div>
                        <?php $inCore->insertEditor('description', $mod['description'], '250', '100%'); ?>
                    </div>

                </td>

                <!-- боковая -->
                <td valign="top" width="300" style="background:#ECECEC;">

                    <?php ob_start(); ?>

                    {tab=Публикация}

                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                        <tr>
                            <td width="20"><input type="checkbox" name="published" id="published" value="1" <?php if ($mod['published'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                            <td><label for="published"><strong>Публиковать раздел</strong></label></td>
                        </tr>
                    </table>

                    <div style="margin-top:15px">
                        <strong>URL раздела</strong><br/>
                        <div style="color:gray">Если не указан, генерируется из заголовка</div>
                    </div>
                    <div>
                        <input type="text" name="url" value="<?php echo $mod['url']; ?>" style="width:99%"/>
                    </div>

                    <div style="margin-top:20px"><strong>Сортировка статей</strong></div>
                    <div>
                        <select name="orderby" id="orderby" style="width:100%">
                            <option value="pubdate" <?php if(@$mod['orderby']=='pubdate') { echo 'selected'; } ?>>По дате</option>
                            <option value="title" <?php if(@$mod['orderby']=='title') { echo 'selected'; } ?>>По заголовку</option>
                            <option value="ordering" <?php if(@$mod['orderby']=='ordering') { echo 'selected'; } ?>>По порядку</option>
                            <option value="hits" <?php if(@$mod['orderby']=='hits') { echo 'selected'; } ?>>По просмотрам</option>
                        </select>
                        <select name="orderto" id="orderto" style="width:100%">
                            <option value="ASC" <?php if(@$mod['orderto']=='ASC') { echo 'selected'; } ?>>По возрастанию</option>
                            <option value="DESC" <?php if(@$mod['orderto']=='DESC') { echo 'selected'; } ?>>По убыванию</option>
                        </select>
                    </div>

                    <div style="margin-top:20px"><strong>Число колонок для вывода статей</strong></div>
                    <div>
                        <?php if (!isset($mod['maxcols'])) { $mod['maxcols'] = 1; } ?>
                        <input name="maxcols" type="text" id="maxcols" style="width:99%" value="<?php echo @$mod['maxcols'];?>" />
                    </div>

                    <div style="margin-top:20px"><strong>Параметры публикации</strong></div>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                        <tr>
                            <td width="20"><input type="checkbox" name="showdesc" id="showdesc" value="1" <?php if ($mod['showdesc'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                            <td><label for="showdesc">Показывать анонсы статей</label></td>
                        </tr>
                        <tr>
                            <td width="20"><input type="checkbox" name="showdate" id="showdate" value="1" <?php if ($mod['showdate'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                            <td><label for="showdate">Показывать даты статей</label></td>
                        </tr>
                        <tr>
                            <td width="20"><input type="checkbox" name="showcomm" id="showcomm" value="1" <?php if ($mod['showcomm'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                            <td><label for="showcomm">Показывать число комментариев</label></td>
                        </tr>
                        <tr>
                            <td width="20"><input type="checkbox" name="showtags" id="showtags" value="1" <?php if ($mod['showtags'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                            <td><label for="showtags">Показывать теги статей</label></td>
                        </tr>
                        <tr>
                            <td width="20"><input type="checkbox" name="showrss" id="showrss" value="1" <?php if ($mod['showrss'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                            <td><label for="showrss">Показывать иконку RSS</label></td>
                        </tr>
                    </table>

                    <?php if ($do=='add'){ ?>
                        <div style="margin-top:25px">
                            <strong>Создать ссылку в меню</strong>
                        </div>
                        <div>
                            <select name="createmenu" id="createmenu" style="width:99%">
                                <option value="0" selected="selected">-- не создавать --</option>
                                <option value="mainmenu">Главное меню</option>
                                <option value="menu1">Дополнительное меню 1</option>
                                <option value="menu2">Дополнительное меню 2</option>
                                <option value="menu3">Дополнительное меню 3</option>
                                <option value="menu4">Дополнительное меню 4</option>
                                <option value="menu5">Дополнительное меню 5</option>
                            </select>
                        </div>
                    <?php } ?>

                    {tab=Редакторы}

                        <div style="margin-top:10px">
                            <strong>Принимать статьи от пользователей</strong><br/>
                            <span class="hinttext">Если включено, то раздел отображается в списке доступных для публикации разделов, когда пользователь добавляет статью с сайта.</span>
                        </div>
                        <div>
                            <select name="is_public" id="is_public" style="width:100%">
                                <option value="0" <?php if(!$mod['is_public']) { echo 'selected'; } ?>>Нет</option>
                                <option value="1" <?php if($mod['is_public']) { echo 'selected'; } ?>>Да</option>
                            </select>
                        </div>
                        <?php if (IS_BILLING){ ?>
                            <div style="margin-top:15px">
                                <strong>Стоимость добавления статьи</strong><br/>
                                <div style="color:gray">Если не указана здесь, то используется цена по-умолчанию, указанная в настройках биллинга</div>
                            </div>
                            <div>
                                <input type="text" name="cost" value="<?php echo $mod['cost']; ?>" style="width:50px"/> баллов
                            </div>
                        <?php } ?>
                        <div style="margin-top:20px">
                            <strong>Редакторы раздела</strong><br/>
                            <span class="hinttext">Пользователи выбранной группы смогут заходить в админку, но будут видеть только этот раздел и его подразделы</span>
                        </div>
                        <div>
                            <select name="modgrp_id" id="modgrp_id" style="width:100%">
                                <option value="0" <?php if (!isset($mod['modgrp_id']) || @$mod['modgrp_id']==0) { echo 'selected'; }?>>Только администраторы</option>
                                <?php
                                    if (@$mod['modgrp_id']) {
                                        echo $inCore->getListItems('cms_user_groups', $mod['modgrp_id']);
                                    }	else {
                                        echo $inCore->getListItems('cms_user_groups');
                                    }
                                ?>
                            </select>
                        </div>

                    {tab=Фото}

                        <div style="margin-top:10px">
                            <strong>Привязать фотоальбом</strong><br/>
                            <span class="hinttext">При просмотре раздела справа будут выводиться фотографии из выбранного альбома</span>
                        </div>
                        <div>
                            <select name="album_id" id="album_id" style="width:100%">
                                <option value="0" <?php if (!isset($mod['photoalbum']['id']) || @$mod['photoalbum']['id']==-1) { echo 'selected'; }?>>-- не привязывать --</option>
                                <?php  //FIND ROOT
                                    $rootid = dbGetField('cms_photo_albums', 'parent_id=0', 'id');
                                    if (isset($mod['photoalbum']['id'])){
                                        echo $inCore->getListItemsNS('cms_photo_albums', $mod['photoalbum']['id']);
                                    } else {
                                        echo $inCore->getListItemsNS('cms_photo_albums');
                                    }
                                ?>
                            </select>
                        </div>

                        <div style="margin-top:20px">
                            <strong>Заголовок</strong><br/>
                            <span class="hinttext">Выводится над фотографиями</span>
                        </div>
                        <div>
                            <input name="album_header" type="text" id="album_header" style="width:99%" value="<?php echo @$mod['photoalbum']['header'];?>" />
                        </div>

                        <div style="margin-top:20px">
                            <strong>Показывать названия фото</strong>
                        </div>
                        <div>
                            <select name="album_titles" id="album_titles" style="width:100%">
                                <?php if(!isset($mod['photoalbum']['titles'])) { $mod['photoalbum']['titles'] = 0; } ?>
                                <option value="0" <?php if(!$mod['photoalbum']['titles']) { echo 'selected'; } ?>>Нет</option>
                                <option value="1" <?php if($mod['photoalbum']['titles']) { echo 'selected'; } ?>>Да</option>
                            </select>
                        </div>

                        <div style="margin-top:20px">
                            <strong>Сортировка фото</strong>
                        </div>
                        <div>
                            <select name="album_orderby" id="album_orderby" style="width:100%">
                                <option value="title" <?php if(@$mod['photoalbum']['orderby']=='title') { echo 'selected'; } ?>>По алфавиту</option>
                                <option value="pubdate" <?php if(@$mod['photoalbum']['orderby']=='pubdate') { echo 'selected'; } ?>>По дате</option>
                                <option value="rating" <?php if(@$mod['photoalbum']['orderby']=='rating') { echo 'selected'; } ?>>По рейтингу</option>
                                <option value="hits" <?php if(@$mod['photoalbum']['orderby']=='hits') { echo 'selected'; } ?>>По просмотрам</option>
                            </select>
                            <select name="album_orderto" id="album_orderto" style="width:100%">
                                <option value="desc" <?php if(@$mod['photoalbum']['orderto']=='desc') { echo 'selected'; } ?>>по убыванию</option>
                                <option value="asc" <?php if(@$mod['photoalbum']['orderto']=='asc') { echo 'selected'; } ?>>по возрастанию</option>
                            </select>
                        </div>

                        <div style="margin-top:20px">
                            <strong>Число колонок для вывода</strong>
                        </div>
                        <div>
                            <?php if(!isset($mod['photoalbum']['maxcols'])) { $mod['photoalbum']['maxcols'] = 2; } ?>
                            <input name="album_maxcols" type="text" id="album_maxcols" style="width:99%" value="<?php echo @$mod['photoalbum']['maxcols'];?>"/>
                        </div>

                        <div style="margin-top:20px">
                            <strong>Число фотографий</strong>
                        </div>
                        <div>
                            <?php if(!isset($mod['photoalbum']['max'])) { $mod['photoalbum']['max'] = 8; } ?>
                            <input name="album_max" type="text" id="album_max" style="width:99%" value="<?php echo @$mod['photoalbum']['max'];?>"/>
                        </div>
                      {tab=Доступ}
  
                      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist" style="margin-top:5px">
                          <tr>
                              <td width="20">
                                  <?php
                                      $sql    = "SELECT * FROM cms_user_groups";
                                      $result = dbQuery($sql) ;
  
                                      $style  = 'disabled="disabled"';
                                      $public = 'checked="checked"';
  
                                      if ($do == 'edit'){
  
                                          $sql2 = "SELECT * FROM cms_content_access WHERE content_id = ".$mod['id']." AND content_type = 'category'";
                                          $result2 = dbQuery($sql2);
                                          $ord = array();
  
                                          if (mysql_num_rows($result2)){
                                              $public = '';
                                              $style = '';
                                              while ($r = mysql_fetch_assoc($result2)){
                                                  $ord[] = $r['group_id'];
                                              }
                                          }
                                      }
                                  ?>
                                  <input name="is_access" type="checkbox" id="is_public" onclick="checkGroupList()" value="1" <?php echo $public?> />
                              </td>
                              <td><label for="is_public"><strong>Общий доступ</strong></label></td>
                          </tr>
                      </table>
                      <div style="padding:5px">
                          <span class="hinttext">
                              Если отмечено, категория будет видна всем посетителям. Снимите галочку, чтобы вручную выбрать разрешенные группы пользователей.
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
                                  echo '<select style="width: 99%" name="showfor[]" id="showin" size="6" multiple="multiple" '.$style.'>';
  
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
            <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add') { echo 'value="Создать раздел"'; } else { echo 'value="Сохранить раздел"'; } ?> />
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
