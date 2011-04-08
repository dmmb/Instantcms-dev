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
	
	if (!$n) { echo '<p>����� "/images/menuicons" �����!</p>'; }
	
	echo '<div align="right" style="clear:both">[<a href="javascript:selectIcon(\'\')">��� ������</a>] [<a href="javascript:hideIcons()">�������</a>]</div>';
	
	return;
}

function applet_menu(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/menu', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = '����';
 	cpAddPathway('���� �����', 'index.php?view=menu');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'new.gif';
		$toolmenu[0]['title'] = '�������� ����� ����';
		$toolmenu[0]['link'] = '?view=menu&do=add';

		$toolmenu[1]['icon'] = 'newmenu.gif';
		$toolmenu[1]['title'] = '�������� ����';
		$toolmenu[1]['link'] = '?view=menu&do=addmenu';

		$toolmenu[2]['icon'] = 'edit.gif';
		$toolmenu[2]['title'] = '������������� ���������';
		$toolmenu[2]['link'] = "javascript:checkSel('?view=menu&do=edit&multiple=1');";

		$toolmenu[5]['icon'] = 'delete.gif';
		$toolmenu[5]['title'] = '������� ���������';
		$toolmenu[5]['link'] = "javascript:checkSel('?view=menu&do=delete&multiple=1');";

		$toolmenu[3]['icon'] = 'show.gif';
		$toolmenu[3]['title'] = '����������� ���������';
		$toolmenu[3]['link'] = "javascript:checkSel('?view=menu&do=show&multiple=1');";

		$toolmenu[4]['icon'] = 'hide.gif';
		$toolmenu[4]['title'] = '������ ���������';
		$toolmenu[4]['link'] = "javascript:checkSel('?view=menu&do=hide&multiple=1');";

		$toolmenu[8]['icon'] = 'help.gif';
		$toolmenu[8]['title'] = '������';
		$toolmenu[8]['link'] = "?view=help&topic=menu";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';

		$fields[1]['title'] = '��������';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';		$fields[1]['link'] = '?view=menu&do=edit&id=%id%';
		
		$fields[2]['title'] = '�����';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '40';
		
		$fields[3]['title'] = '�������';	$fields[3]['field'] = 'ordering';	$fields[3]['width'] = '100';	
				
		$fields[4]['title'] = '������';		$fields[4]['field'] = 'id';		$fields[4]['width'] = '240';
		$fields[4]['prc'] = 'cpMenutypeById';

		$fields[5]['title'] = '����';		$fields[5]['field'] = 'menu';		$fields[5]['width'] = '70';	$fields[5]['filter'] = 10;
		$fields[5]['filterlist'] = cpGetList('menu');

		$fields[6]['title'] = '������';		$fields[6]['field'] = 'template';	$fields[6]['width'] = '70';
		$fields[6]['prc'] = 'cpTemplateById';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = '�������������';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=menu&do=edit&id=%id%';

		$actions[1]['title'] = '�������';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = '������� ����� ����?';
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
						'����', 
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
		$toolmenu[0]['title'] = '���������';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = '������';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);

		$GLOBALS['cp_page_title'] = '�������� ����';
		cpAddPathway('�������� ����', 'index.php?view=menu&do=addmenu');
		
		echo '<h3>�������� ����</h3>';

		?>
        <form id="addform" name="addform" action="index.php?view=menu&do=submitmenu" method="post">
            <table class="proptable" width="650" cellspacing="10" cellpadding="10">
                <tr>
                    <td width="300" valign="top">
                        <strong>�������� ������ ����:</strong>
                    </td>
                    <td valign="top">
                        <input name="title" type="text" id="title2" style="width:200px" value=""/>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>���� ��� ������: </strong><br/>
                        <span class="hinttext">��� �������� ������� ������ ���� ��������� ����� �� �������� � ���� &quot;����&quot;, ��� �������� </span>
                    </td>
                    <td valign="top">
                        <select name="menu" id="menu" style="width:200px">
                                <option value="mainmenu" <?php if (@$mod['menu']=='mainmenu' || !isset($mod['menu'])) { echo 'selected'; }?>>������� ����</option>
                                <?php for($m=1;$m<=15;$m++){ ?>
                                    <option value="menu<?php echo $m; ?>" <?php if (@$mod['menu']=='menu'.$m) { echo 'selected'; }?>>�������������� ���� <?php echo $m; ?></option>
                                <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>������� ������:</strong><br />
                        <span class="hinttext">������� ������ �������������� � �������</span>
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
                    <td valign="top"><strong>����������� ����?</strong></td>
                    <td valign="top">
                        <input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> ��
                        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> ���
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>CSS �������:</strong></td>
                    <td valign="top">
                        <input name="css_prefix" type="text" id="css_prefix" value="<?php echo @$mod['css_prefix'];?>" style="width:200px" />
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>������:</strong><br />
                        <span class="hinttext">����� ������ ������������� ���������� ��� ����</span>
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
                            <td><label for="is_public"><strong>����� ������</strong></label></td>
                        </tr>
                    </table>
                    <div style="padding:5px">
                        <span class="hinttext">
                            ���� ��������, ���������� ������ ���� ����� ����� ���� �������������. ������� �������, ����� ������� ������� ����������� ������ �������������.
                        </span>
                    </div>

                    <div style="margin-top:10px;padding:5px;padding-right:0px;" id="grp">
                        <div>
                            <strong>���������� �������:</strong><br />
                            <span class="hinttext">
                                ����� ������� ���������, ��������� CTRL.
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
                            �������� ����� ����, �� �������� ����� ������. �� ����� �������� � ������� "������" ������ � ����������. ����� �������� ����
                            �� ������ �������������� � ��������� ������ ������, ��� �� ������� ������� � ����� �������� ����� ������� ��� ����������.
                        </div>
                    </td>
                </tr>
            </table>
            <div style="margin-top:5px">
                <input name="save" type="submit" id="save" value="������� ����"/>
                <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=menu';"/>
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
		$toolmenu[0]['title'] = '���������';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = '������';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);
   
		if ($do=='add'){
			 echo '<h3>�������� ����� ����</h3>';
 	 		 cpAddPathway('�������� ����� ����', 'index.php?view=menu&do=add');
		} else {
					 if(isset($_REQUEST['multiple'])){				 
						if (isset($_REQUEST['item'])){					
							$_SESSION['editlist'] = $_REQUEST['item'];
						} else {
							echo '<p class="error">��� ��������� ��������!</p>';
							return;
						}				 
					 }
						
					 $ostatok = '';
					
					 if (isset($_SESSION['editlist'])){
						$id = array_shift($_SESSION['editlist']);
						if (sizeof($_SESSION['editlist'])==0) { unset($_SESSION['editlist']); } else 
						{ $ostatok = '(�� �������: '.sizeof($_SESSION['editlist']).')'; }
					 } else { $id = (int)$_REQUEST['id']; }
	
					 $sql = "SELECT * FROM cms_menu WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
					
					 echo '<h3>������������� ����� ���� '.$ostatok.'</h3>';
					 
					 cpAddPathway($mod['title'], 'index.php?view=menu&do=edit&id='.$mod['id']);
			}   
	?>
    <form id="addform" name="addform" method="post" action="index.php">
        <input type="hidden" name="view" value="menu" />

        <table class="proptable" width="100%" cellpadding="15" cellspacing="2">
            <tr>

                <!-- ������� ������ -->
                <td valign="top">

                    <div><strong>��������� ������ ����</strong> <span class="hinttext">&mdash; ������������ �� �����</span></div>
                    <div><input name="title" type="text" id="title" style="width:100%" value="<?php echo @$mod['title'];?>" /></div>

                    <div>
                        <select name="menu" id="menu" style="width:100%">
                            <option value="mainmenu" <?php if (@$mod['menu']=='mainmenu' || !isset($mod['menu'])) { echo 'selected'; }?>>������� ����</option>
                            <?php for($m=1;$m<=15;$m++){ ?>
                                <option value="menu<?php echo $m; ?>" <?php if (@$mod['menu']=='menu'.$m) { echo 'selected'; }?>>�������������� ���� <?php echo $m; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div><strong>������������ �����</strong></div>
                    <div>
                        <?php
                            $rootid = dbGetField('cms_menu', 'parent_id=0', 'id');
                        ?>
                        <select name="parent_id" size="10" id="parent_id" style="width:100%">
                            <option value="<?php echo $rootid?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- ������ ���� --</option>
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

                    <div><strong>�������� ������ ����</strong></div>
                    <div>
                        <select name="mode" id="linktype" style="width:100%" onchange="showMenuTarget()">
                            <option value="link" <?php if (@$mod['linktype']=='link' || !isset($mod['mode'])) { echo 'selected'; }?>>������� ������</option>
                            <option value="content" <?php if (@$mod['linktype']=='content') { echo 'selected'; }?>>������� ������</option>
                            <option value="category" <?php if (@$mod['linktype']=='category') { echo 'selected'; }?>>������� ������ (������ ������)</option>
                            <option value="component" <?php if (@$mod['linktype']=='component') { echo 'selected'; }?>>������� ���������</option>
                            <option value="blog" <?php if (@$mod['linktype']=='blog') { echo 'selected'; }?>>������� ����</option>
                            <option value="uccat" <?php if (@$mod['linktype']=='uccat') { echo 'selected'; }?>>������� ��������� ��������</option>
                            <option value="pricecat" <?php if (@$mod['linktype']=='pricecat') { echo 'selected'; }?>>������� ��������� �����-�����</option>
                            <option value="photoalbum" <?php if (@$mod['linktype']=='photoalbum') { echo 'selected'; }?>>������� ������ �����������</option>
                        </select>                        
                    </div>

                    <div id="t_link" class="menu_target" style="display:<?php if ($mod['linktype']=='link'||$mod['linktype']=='ext'||!$mod['linktype']) { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>����� ������</strong> <span class="hinttext">&mdash; ��� ������� ������ �� ��������� ������� <b>http://</b></span>
                        </div>
                        <div>
                            <input name="link" type="text" id="link" size="50" style="width:100%" <?php if (@$mod['linktype']=='link'||@$mod['linktype']=='ext') { echo  'value="'.$mod['link'].'"'; } ?>/>
                        </div>
                    </div>

                    <div id="t_content" class="menu_target" style="display:<?php if ($mod['linktype']=='content') { echo  'block'; } else { echo 'none'; } ?>">
                        <div>
                            <strong>�������� ������</strong>
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
                            <strong>�������� ������</strong>
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
                            <strong>�������� ���������</strong>
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
                            <strong>�������� ����</strong>
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
                            <strong>�������� ��������� ��������</strong>
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
                            <strong>�������� ��������� �����-�����</strong>
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
                            <strong>�������� ����������</strong>
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

                <!-- ������� ������ -->
                <td width="300" valign="top" style="background:#ECECEC;">

                    <?php ob_start(); ?>

                    {tab=����������}

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist">
                            <tr>
                                <td width="20"><input type="checkbox" name="published" id="published" value="1" <?php if ($mod['published'] || $do=='add') { echo 'checked="checked"'; } ?>/></td>
                                <td><label for="published"><strong>����������� ����� ����</strong></label></td>
                            </tr>
                        </table>

                        <div style="margin-top:15px">
                            <strong>��������� ����� ����</strong>
                        </div>
                        <div>
                            <select name="target" id="target" style="width:100%">
                                <option value="_self" <?php if (@$mod['target']=='_self') { echo 'selected'; }?>>� ���� �� ���� (self)</option>
                                <option value="_parent">� ������������ ���� (parent)</option>
                                <option value="_blank" <?php if (@$mod['target']=='_blank') { echo 'selected'; }?>>� ����� ���� (blank)</option>
                                <option value="_top" <?php if (@$mod['target']=='_top') { echo 'selected'; }?>>������ ���� ���� (top)</option>
                            </select>
                        </div>

                        <div style="margin-top:15px">
                            <strong>������ �����</strong><br/>
                            <span class="hinttext">��������, ���� ����� ����� �������� ������ ���� �������������� ������ ������� �����</span>
                        </div>
                        <div>
                            <select name="template" id="template" style="width:100%">
                                <option value="0" <?php if (@$mod['template']==0 || !$mod['template']) { echo 'selected'; } ?>>��-���������</option>
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
                            <strong>������</strong><br/>
                            <span class="hinttext">�������� ����� � ����� "/images/menuicons"</span>
                        </div>
                        <div>
                            <input name="iconurl" type="text" id="iconurl" size="30" value="<?php echo @$mod['iconurl'];?>" style="width:100%"/>
                            <div>
                                <a id="iconlink" style="display:block;" href="javascript:showIcons()"> ������� ������</a>
                                <div id="icondiv" style="display:none; padding:6px;border:solid 1px gray;background:#FFF">
                                    <div><?php iconList(); ?></div>
                                </div>
                            </div>
                        </div>

                    {tab=������}
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
                            <td><label for="is_public"><strong>����� ������</strong></label></td>
                        </tr>
                    </table>
                    <div style="padding:5px">
                        <span class="hinttext">
                            ���� ��������, ���������� ������ ���� ����� ����� ���� �������������. ������� �������, ����� ������� ������� ����������� ������ �������������.
                        </span>
                    </div>

                    <div style="margin-top:10px;padding:5px;padding-right:0px;" id="grp">
                        <div>
                            <strong>���������� �������:</strong><br />
                            <span class="hinttext">
                                ����� ������� ���������, ��������� CTRL.
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
            <input name="add_mod" type="button" onclick="submitItem()" id="add_mod" <?php if ($do=='add') { echo 'value="������� �����"'; } else { echo 'value="��������� �����"'; } ?> />
            <input name="back" type="button" id="back" value="������" onclick="window.location.href='index.php?view=menu';"/>
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