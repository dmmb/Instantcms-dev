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

    $inDB   = cmsDatabase::getInstance();
	$inUser = cmsUser::getInstance();
	$inCore = cmsCore::getInstance();
    $inCore->loadModel('actions');
    $model = new cms_model_actions();

    $inCore->loadClass('actions');
    $inActions = cmsActions::getInstance();

    $opt = $inCore->request('opt', 'str', 'list');
	$id  = $inCore->request('id', 'int', 0);

	$act_components = cmsActions::getActionsComponents();
    $act_component  = $inCore->request('act_component', 'str', '');

    cpAddPathway('Лента активности', '?view=components&do=config&id='.$id);

	$messages = cmsCore::getSessionMessages();
	if ($messages) { ?>
	<div class="sess_messages">
		<?php foreach($messages as $message){
			     echo $message;
		      }?>
	</div>
	<?php }
//=================================================================================================//
//=================================================================================================//

	$toolmenu = array();
		
	if($opt != 'config'){		
?>
		<table width="100%" cellpadding="2" border="0" class="toolmenu" style="margin:0px">
		  <tbody>
			<tr>
			  <td width="45px">
				<a class="toolmenuitem" href="?view=components&do=config&id=<?php echo $id; ?>&opt=config" title="Настройки">
				  <img src="images/toolmenu/config.gif" border="0">
				</a>
			  </td>
			  <td>
              <form action="?view=components&do=config&id=<?php echo $id; ?>" method="post" id="filter_form">
				Показывать события от: 
                <select name="act_component" style="width:215px" onchange="$('#filter_form').submit()">
                    <option value="" <?php if(!$act_component){ ?>selected="selected"<?php } ?>>всех компонентов</option>
                    <?php foreach($act_components as $act_com) {
							if($act_com['link'] == $act_component){
								echo '<option value="'.$act_com['link'].'" selected="selected">'.$act_com['title'].'</option>';
							} else {
								echo '<option value="'.$act_com['link'].'">'.$act_com['title'].'</option>';
							}
					}
					?>
                </select>
              </form>
			  </td>
			</tr>
		  </tbody>
		</table>
		
<?php
	}

	if($opt == 'config'){
		$toolmenu[16]['icon'] = 'save.gif';
		$toolmenu[16]['title'] = 'Сохранить';
		$toolmenu[16]['link'] = 'javascript:document.optform.submit();';

		$toolmenu[17]['icon'] = 'cancel.gif';
		$toolmenu[17]['title'] = 'Отмена';
		$toolmenu[17]['link'] = '?view=components&do=config&id='.$id;	
		cpToolMenu($toolmenu);	
	}

//=================================================================================================//
//=================================================================================================//

    if ($opt == 'list'){

        $page       = $inCore->request('page', 'int', 1);
        $perpage    = 15;

        $inActions->showTargets(true);

		if ($act_component){
			$inActions->where("a.component = '$act_component'");
		}

		$total = $inActions->getCountActions();

        $inActions->limitPage($page, $perpage);

        $actions = $inActions->getActionsLog();

		$pagebar = cmsPage::getPagebar($total, $page, $perpage, '?view=components&do=config&id='.$id.'&opt=list&page=%page%');

		$tpl_file   = 'admin/actions.php';
		$tpl_dir    = file_exists(TEMPLATE_DIR.$tpl_file) ? TEMPLATE_DIR : DEFAULT_TEMPLATE_DIR;

		include($tpl_dir.$tpl_file);

    }

//=================================================================================================//
//=================================================================================================//
	if($opt=='saveconfig'){

		$cfg = array();

        $cfg['show_target'] = $inCore->request('show_target', 'int', 1);
        $cfg['perpage']     = $inCore->request('perpage', 'int', 10);
		$cfg['perpage_tab'] = $inCore->request('perpage_tab', 'int', 15);
		$cfg['is_all']      = $inCore->request('is_all', 'int', 0);
       	$cfg['act_type']    = $inCore->request('act_type', 'array_str', '');

        $inCore->saveComponentConfig('actions', $cfg);

		cmsCore::addSessionMessage('Настройки успешно сохранены', 'success');

		$inCore->redirect('?view=components&do=config&id='.$id.'&opt=config');

	}
//=================================================================================================//
//=================================================================================================//
	if ($opt=='config') {
	
		cpAddPathway('Настройки', '?view=components&do=config&id='.$id.'&opt=config');

        $sql        = "SELECT *
                       FROM cms_actions
                       ORDER BY title
                       LIMIT 100";

        $result = $inDB->query($sql);

		?>
	
	<form action="index.php?view=components&do=config&id=<?php echo $id;?>&opt=saveconfig" method="post" name="optform" target="_self" id="form1">
		<table width="680" border="0" cellpadding="10" cellspacing="0" class="proptable">
			<tr>
				<td>
					<strong>Показывать место назначения:</strong><br />
				</td>
				<td valign="top">
					<label><input name="show_target" type="radio" value="1"  <?php if ($model->config['show_target']) { echo 'checked="checked"'; } ?> /> Да </label>
					<label><input name="show_target" type="radio" value="0"  <?php if (!$model->config['show_target']) { echo 'checked="checked"'; } ?> /> Нет </label>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Количество событий на странице:</strong><br />
				</td>
				<td valign="top">
					<input name="perpage" size=5 value="<?php echo $model->config['perpage'];?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Количество событий на странице профиля во вкладке "лента":</strong><br />
				</td>
				<td valign="top">
					<input name="perpage_tab" size=5 value="<?php echo $model->config['perpage_tab'];?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Типы событий:</strong><br />
                    <div class="param-links">
                        <label for="is_all"><input type="checkbox" id="is_all" name="is_all" value="1" <?php if($model->config['is_all']) {?>checked="checked" <?php }?> /> <a href="javascript:" onclick="$('td input[type=checkbox]').attr('checked', 'checked');$('#is_all').attr('checked', 'checked')">Выделить все</a></label> |
                        <a href="javascript:" onclick="$('td input[type=checkbox]').attr('checked', '');$('#is_all').attr('checked', '')">Снять все</a>
                    </div>
				</td>
				<td valign="top">
					<?php
			
						$html = '<table cellpadding="0" cellspacing="0">' . "\n";
			
						if ($inDB->num_rows($result)){
							while($option = $inDB->fetch_assoc($result)){
								
								$html .= '<tr>' . "\n" .
											"\t" . '<td><input type="checkbox" id="act_type_'.$option['name'].'" name="act_type['.$option['name'].']" value="'.$option['id'].'" '.(@in_array($option['id'], $model->config['act_type']) ? 'checked="checked"' : '').' />' . "\n" .
											"\t" . '<td><label for="act_type_'.$option['name'].'">'.$option['title'].'</label></td>' . "\n" .
										 '</tr>';
							}
						}
			
						$html .= '</table>' . "\n";
						echo $html;
					
					?>
				</td>
			</tr>
		</table>
		<p>
			<input name="save" type="submit" id="save" value="Сохранить" />
			<input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
		</p>
	</form>	
	
	<?php } 	
	
	
?>