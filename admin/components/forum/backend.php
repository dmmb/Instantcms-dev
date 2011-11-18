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
    function uploadCategoryIcon($file='') {

        $inCore = cmsCore::getInstance();

		// Загружаем класс загрузки фото
		$inCore->loadClass('upload_photo');
		$inUploadPhoto = cmsUploadPhoto::getInstance();
		// Выставляем конфигурационные параметры
		$inUploadPhoto->upload_dir    = PATH.'/upload/forum/';
		$inUploadPhoto->dir_medium    = 'cat_icons/';
		$inUploadPhoto->medium_size_w = 32;
		$inUploadPhoto->medium_size_h = 32;
		$inUploadPhoto->only_medium   = true;
		$inUploadPhoto->is_watermark  = false;
		// Процесс загрузки фото
		$files = $inUploadPhoto->uploadPhoto($file);

		$icon = $files['filename'] ? $files['filename'] : $file;

        return $icon;

    }

    define('IS_BILLING', $inCore->isComponentInstalled('billing'));
    if (IS_BILLING) { $inCore->loadClass('billing'); }

    $inDB   = cmsDatabase::getInstance();
	$inUser = cmsUser::getInstance();

	$opt = $inCore->request('opt', 'str', 'config');
	$id  = $inCore->request('id', 'int', 0);

    $cfg = $inCore->loadComponentConfig('forum');
    if (!isset($cfg['is_rss'])) { $cfg['is_rss'] = 1; }

	$inCore->loadModel('forum');
	$model = new cms_model_forum();

	cpAddPathway('Форум', '?view=components&do=config&id='.$id);
	echo '<h3>Форум</h3>';

	$toolmenu = array();

	if ($opt=='list_forums' || $opt=='list_cats' || $opt=='config' || $opt=='saveconfig'){
	
		$toolmenu[0]['icon'] = 'newfolder.gif';
		$toolmenu[0]['title'] = 'Новая категория';
		$toolmenu[0]['link'] = '?view=components&do=config&id='.$id.'&opt=add_cat';
	
		$toolmenu[2]['icon'] = 'newforum.gif';
		$toolmenu[2]['title'] = 'Новый форум';
		$toolmenu[2]['link'] = '?view=components&do=config&id='.$id.'&opt=add_forum';
	
		$toolmenu[1]['icon'] = 'folders.gif';
		$toolmenu[1]['title'] = 'Категории форумов';
		$toolmenu[1]['link'] = '?view=components&do=config&id='.$id.'&opt=list_cats';
	
		$toolmenu[3]['icon'] = 'listforums.gif';
		$toolmenu[3]['title'] = 'Все форумы';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.$id.'&opt=list_forums';

		$toolmenu[4]['icon'] = 'ranks.gif';
		$toolmenu[4]['title'] = 'Звания на форуме';
		$toolmenu[4]['link'] = '?view=components&do=config&id='.$id.'&opt=list_ranks';

	}
	if($opt=='list_forums'){

		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = 'Редактировать выбранные';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$id."&opt=edit_forum&multiple=1');";

		$toolmenu[12]['icon'] = 'show.gif';
		$toolmenu[12]['title'] = 'Публиковать выбранные';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$id."&opt=show_forum&multiple=1');";

		$toolmenu[13]['icon'] = 'hide.gif';
		$toolmenu[13]['title'] = 'Скрыть выбранные';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$id."&opt=hide_forum&multiple=1');";

	}
	
	if ($opt=='list_forums' || $opt=='list_cats' || $opt=='config'){
	} else {
	
		$toolmenu[20]['icon'] = 'save.gif';
		$toolmenu[20]['title'] = 'Сохранить';
		$toolmenu[20]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[21]['icon'] = 'cancel.gif';
		$toolmenu[21]['title'] = 'Отмена';
		$toolmenu[21]['link'] = '?view=components&do=config&id='.$id;
	
	}

	cpToolMenu($toolmenu);

	if($opt=='saveconfig'){	
        
		$cfg['karma']       = $inCore->request('karma', 'int', 1);
		$cfg['is_rss']      = $inCore->request('is_rss', 'int', 1);
		$cfg['pp_thread']   = $inCore->request('pp_thread', 'int', 15);
		$cfg['pp_forum']    = $inCore->request('pp_forum', 'int', 15);

		$cfg['showimg']     = $inCore->request('showimg', 'int', 1);

		$cfg['img_on']      = $inCore->request('img_on', 'int', 1);
		$cfg['img_max']     = $inCore->request('img_max', 'int', 1);
		
		$cfg['fast_on']     = $inCore->request('fast_on', 'int', 1);
		$cfg['fast_bb']     = $inCore->request('fast_bb', 'int', 1);
		
		$cfg['fa_on']       = $inCore->request('fa_on', 'int');
		$cfg['fa_max']      = $inCore->request('fa_max', 'int');
		$cfg['fa_ext']      = $inCore->request('fa_ext', 'str');
		$cfg['fa_ext']      = str_replace('htm', '', $cfg['fa_ext']);
		$cfg['fa_ext']      = str_replace('php', '', $cfg['fa_ext']);
		$cfg['fa_size']     = $inCore->request('fa_size', 'int');
		$cfg['edit_minutes'] = $inCore->request('edit_minutes', 'int');

		$is_access = $inCore->request('is_access', 'int', '');
		if (!$is_access){
			$access_list = $inCore->request('allow_group', 'array_int');
			$cfg['group_access'] = $access_list ? $inCore->arrayToYaml($access_list) : '';
		} else { $cfg['group_access'] = ''; }

		$inCore->saveComponentConfig('forum', $cfg);

		$msg = 'Настройки сохранены.';
		$opt = 'config';

	}

	if($opt=='saveranks'){	
		
		$cfg['ranks']   = $_REQUEST['rank'];
		$cfg['modrank'] = $inCore->request('modrank', 'int');
		
		$inCore->saveComponentConfig('forum', $cfg);

		$msg = 'Настройки сохранены.';
		$opt = 'list_ranks';		
		
	}
	
	if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }

	if ($opt == 'show_forum'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_forums', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_forums', $_REQUEST['item']);				
			$inCore->redirectBack();
		}			
	}

	if ($opt == 'hide_forum'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_forums', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_forums', $_REQUEST['item']);				
			$inCore->redirectBack();
		}			
	}

	if ($opt == 'submit_forum'){	
			$category_id    = $inCore->request('category_id', 'int');
			$title          = $inCore->request('title', 'str');
			$published      = $inCore->request('published', 'int');
			$auth_group     = $inCore->request('auth_group', 'int');
			$parent_id      = $inCore->request('parent_id', 'int');
			$description    = $inCore->request('description', 'str');
			$topic_cost     = $inCore->request('topic_cost', 'int', 0);

			$ns = $inCore->nestedSetsInit('cms_forums');
			$myid = $ns->AddNode($parent_id);

			$is_access = $inCore->request('is_access', 'int', '');
			if (!$is_access){
				$access_list = $inCore->request('access_list', 'array_int');
				$group_access = $access_list ? $inCore->arrayToYaml($access_list) : '';
			} else {
				$group_access = '';
			}

			$icon = uploadCategoryIcon();

			$sql = "UPDATE cms_forums 
					SET category_id=$category_id, 
						title='$title', 
						description='$description', 
						access_list='$group_access', 
						published='$published',
						icon='$icon',
                        topic_cost='$topic_cost'
					WHERE id = '$myid'";

			dbQuery($sql);
			$inCore->redirect('?view=components&do=config&opt=list_forums&id='.$id);
	}	  
	
	if ($opt == 'update_forum'){
		if(isset($_REQUEST['item_id'])) { 
			$item_id = (int)$_REQUEST['item_id'];
			
			$category_id    = $inCore->request('category_id', 'int');
			$title          = $inCore->request('title', 'str');
			$published      = $inCore->request('published', 'int');
			$parent_id      = $inCore->request('parent_id', 'int');
			$description    = $inCore->request('description', 'str');
			$topic_cost     = $inCore->request('topic_cost', 'int', 0);

			$is_access = $inCore->request('is_access', 'int', '');
			if (!$is_access){
				$access_list = $inCore->request('access_list', 'array_int');
				$group_access = $access_list ? $inCore->arrayToYaml($access_list) : '';
				$inDB->query("UPDATE cms_forum_threads SET is_hidden = 1 WHERE forum_id = '$item_id'");
			} else {
				$group_access = '';
				$inDB->query("UPDATE cms_forum_threads SET is_hidden = 0 WHERE forum_id = '$item_id'");
			}

			$ns = $inCore->nestedSetsInit('cms_forums');
			$old = $inDB->get_fields('cms_forums', "id='$item_id'", '*');

			$icon = uploadCategoryIcon($old['icon']);

			if($parent_id != $old['parent_id']){
				$ns->MoveNode($item_id, $parent_id);			
			}

			$sql = "UPDATE cms_forums
					SET category_id=$category_id,
						title='$title',
						description='$description',
						access_list='$group_access', 
						published=$published,
						icon='$icon',
                        topic_cost='$topic_cost'
					WHERE id = '$item_id'
					LIMIT 1";

			dbQuery($sql);		

		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			$inCore->redirect('?view=components&do=config&id='.$id.'&opt=list_forums');
		} else {
			$inCore->redirect('?view=components&do=config&id='.$id.'&opt=edit_forum');
		}
	}

	if($opt == 'delete_forum'){
		include(PATH.'/components/forum/includes/forumcore.php');
		
		if(isset($_REQUEST['item_id'])) { 
			$item_id = (int)$_REQUEST['item_id'];		
			//DELETE POSTS WITH ALL DATA
			$sql = "SELECT p.*, f.icon as f_icon
					FROM cms_forum_posts p, cms_forum_threads t, cms_forums f
					WHERE p.thread_id = t.id AND t.forum_id = f.id AND f.id = '$item_id'";
			$rs = dbQuery($sql);
			if (mysql_num_rows($rs)){
				while ($post = mysql_fetch_assoc($rs)){
					uploadDeletePost(0, $post['id']);
					$inCore->deleteUploadImages($post['id'], 'forum');
				}
			}
			$f_icon = $inDB->get_field('cms_forums', "id = '$item_id'", 'icon');
			//DELETE THREADS
			dbQuery("DELETE FROM cms_forum_threads WHERE forum_id = '$item_id'");
			//DELETE FORUM
			dbDeleteNS('cms_forums', $item_id);
			if(file_exists(PATH.'/upload/forum/cat_icons/'.$f_icon)){
				@chmod(PATH.'/upload/forum/cat_icons/'.$f_icon, 0777);
				@unlink(PATH.'/upload/forum/cat_icons/'.$f_icon);
			}
		}
		$inCore->redirect('?view=components&do=config&id='.$id.'&opt=list_forums');
	}


	if ($opt == 'config') {

        require('../includes/jwtabs.php');
		$GLOBALS['cp_page_head'][] = jwHeader();

		?>
        <form action="index.php?view=components&amp;do=config&amp;id=<?php echo $id;?>" method="post" name="addform" target="_self" id="form1" style="margin-top:10px">
            <?php ob_start(); ?>
           {tab=Просмотр}
           <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Просмотр форума </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>Тем на странице: </strong></td>
                    <td valign="top"><input name="pp_forum" type="text" id="pp_forum" value="<?php echo @$cfg['pp_forum'];?>" size="5" /> шт.</td>
                </tr>                
                <tr>
                    <td valign="top"><strong>Иконка RSS: </strong></td>
                    <td valign="top">
                        <input name="is_rss" type="radio" value="1" <?php if (@$cfg['is_rss']) { echo 'checked="checked"'; } ?> /> Вкл
                        <input name="is_rss" type="radio" value="0" <?php if (@!$cfg['is_rss']) { echo 'checked="checked"'; } ?>/> Выкл
                    </td>
                </tr>
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Просмотр темы  </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>Сообщений на странице: </strong></td>
                    <td valign="top"><input name="pp_thread" type="text" id="pp_thread" value="<?php echo @$cfg['pp_thread'];?>" size="5" /> шт.</td>
                </tr>
                <tr>
                    <td valign="top"><strong>Показывать уменьшенные прикрепленные изображения: </strong></td>
                    <td valign="top">
                        <input name="showimg" type="radio" value="1" <?php if (@$cfg['showimg']) { echo 'checked="checked"'; } ?> /> Вкл
                        <input name="showimg" type="radio" value="0" <?php if (@!$cfg['showimg']) { echo 'checked="checked"'; } ?>/> Выкл
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Форма для быстрого ответа: </strong></td>
                    <td valign="top">
                        <input name="fast_on" type="radio" value="1" <?php if (@$cfg['fast_on'] || !isset($cfg['fast_on'])) { echo 'checked="checked"'; } ?> /> Вкл
                        <input name="fast_on" type="radio" value="0" <?php if (@!$cfg['fast_on']) { echo 'checked="checked"'; } ?>/> Выкл
                    </td>
                </tr>
                <tr>
                    <td valign="top"><p><strong>ББ-код в быстром ответе</strong><strong>: </strong></p></td>
                    <td valign="top">
                        <input name="fast_bb" type="radio" value="1" <?php if (@$cfg['fast_bb'] || !isset($cfg['fast_bb'])) { echo 'checked="checked"'; } ?> /> Вкл
                        <input name="fast_bb" type="radio" value="0" <?php if (@!$cfg['fast_bb']) { echo 'checked="checked"'; } ?>/> Выкл
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Запрещать редактирование/удаление через:</strong><br />
                        <span class="hinttext">Спустя указанное время после добавления поста его редактирование/удаление станет невозможным для пользователя</span>
                    </td>
                    <td valign="top">
                        <select name="edit_minutes" style="width:200px">
                            <option value="0" <?php if(!$cfg['edit_minutes']) { echo 'selected'; } ?>>не запрещать</option>
                            <option value="-1" <?php if(@$cfg['edit_minutes']==-1) { echo 'selected'; } ?>>запрещать сразу</option>
                            <option value="1" <?php if(@$cfg['edit_minutes']==1) { echo 'selected'; } ?>>1 минуту</option>
                            <option value="5" <?php if(@$cfg['edit_minutes']==5) { echo 'selected'; } ?>>5 минут</option>
                            <option value="10" <?php if(@$cfg['edit_minutes']==10) { echo 'selected'; } ?>>10 минут</option>
                            <option value="15" <?php if(@$cfg['edit_minutes']==15) { echo 'selected'; } ?>>15 минут</option>
                            <option value="30" <?php if(@$cfg['edit_minutes']==30) { echo 'selected'; } ?>>30 минут</option>
                            <option value="60" <?php if(@$cfg['edit_minutes']==60) { echo 'selected'; } ?>>1 час</option>                        
                        </select>
                    </td>
                </tr>
           </table>
           {tab=Изображения}
           <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Вставка изображений в сообщения: </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>Вставка изображений: </strong></td>
                    <td valign="top">
                        <input name="img_on" type="radio" value="1" <?php if (@$cfg['img_on']) { echo 'checked="checked"'; } ?> /> Вкл
                        <input name="img_on" type="radio" value="0" <?php if (@!$cfg['img_on']) { echo 'checked="checked"'; } ?>/> Выкл
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Максимум файлов:</strong><br />
                        <span class="hinttext">Сколько изображений можно вставить в одно сообщение</span>
                    </td>
                    <td valign="top"><input name="img_max" type="text" id="img_max" value="<?php echo @$cfg['img_max'];?>" size="5" /> шт.</td>
                </tr>
           </table>
           {tab=Вложения}
           <table width="609" border="0" cellpadding="10" cellspacing="0" class="proptable">
                <tr>
                    <td colspan="2" valign="top" bgcolor="#EBEBEB"><h4>Прикрепление файлов (аттачи) </h4></td>
                </tr>
                <tr>
                    <td valign="top"><strong>Прикрепление файлов: </strong></td>
                    <td valign="top">
                        <input name="fa_on" type="radio" value="1" <?php if (@$cfg['fa_on']) { echo 'checked="checked"'; } ?> /> Вкл
                        <input name="fa_on" type="radio" value="0" <?php if (@!$cfg['fa_on']) { echo 'checked="checked"'; } ?>/> Выкл
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Доступно для групп:</strong><br/>
                        <span class="hinttext">Какой из групп должен принадлежать пользователь, чтобы иметь возможность прикреплять файлы</span>
                    </td>
                    <td valign="top">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="checklist" style="margin-top:5px">
                          <tr>
                              <td width="20">
                                  <?php
									$groups = cmsUser::getGroups();

                                    $style  = 'disabled="disabled"';
                                    $public = 'checked="checked"';

									if ($cfg['group_access']){
										$public = '';
										$style  = '';
										
										$access_list = $inCore->yamlToArray($cfg['group_access']);

									}

                                  ?>
                                  <input name="is_access" type="checkbox" id="is_access" onclick="checkGroupList()" value="1" <?php echo $public?> />
                              </td>
                              <td><label for="is_access"><strong>Все группы</strong></label></td>
                          </tr>
                      </table>
                      <div style="padding:5px">
                          <span class="hinttext">
                              Если отмечено, все группы пользователей смогут прикреплять файлы.
                          </span>
                      </div>
  
                      <div style="margin-top:10px;padding:5px;padding-right:0px;" id="grp">
                          <div>
                              <strong>Могут прикреплять только группы:</strong><br />
                              <span class="hinttext">
                                  Можно выбрать несколько, удерживая CTRL.
                              </span>
                          </div>
                          <div>
                              <?php
                                  echo '<select style="width: 245px" name="allow_group[]" id="showin" size="6" multiple="multiple" '.$style.'>';
  
									if ($groups){
										foreach($groups as $group){
											if($group['alias'] != 'guest' && !$group['is_admin']){
												echo '<option value="'.$group['id'].'"';
												if ($access_list){
													if (inArray($access_list, $group['id'])){
														echo 'selected';
													}
												}
		
												echo '>';
												echo $group['title'].'</option>';
											}
										}
	
									}
                                  
                                  echo '</select>';
                              ?>
                          </div>
                      </div>
<script type="text/javascript">
function checkGroupList(){
	if($('input#is_access').attr('checked')){
		$('select#showin').attr('disabled', 'disabled');
	} else {
		$('select#showin').attr('disabled', '');
	}

}
</script>
                   </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Максимум файлов:</strong><br />
                        <span class="hinttext">Сколько файлов можно прикрепить к одному сообщению</span>
                    </td>
                    <td valign="top">
                        <input name="fa_max" type="text" id="fa_max" value="<?php echo @$cfg['fa_max'];?>" size="5" /> шт.
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <strong>Разрешенные расширения: </strong><br />
                        <span class="hinttext">Список допустимых расширений, через пробел</span>
                    </td>
                    <td valign="top">
                        <textarea name="fa_ext" cols="35" rows="3" id="fa_ext"><?php echo @$cfg['fa_ext'];?></textarea>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><strong>Максимальный размер файла: </strong></td>
                    <td valign="top">
                        <input name="fa_size" type="text" id="fa_size" value="<?php echo @$cfg['fa_size'];?>" size="10" /> Кб
                    </td>
                </tr>
            </table>
            {/tabs}
            <?php echo jwTabs(ob_get_clean()); ?>
            <p>
                <input name="opt" type="hidden" id="do" value="saveconfig" />
                <input name="save" type="submit" id="save" value="Сохранить" />
            </p>
        </form>
		<?php
	}
	
	if ($opt == 'list_ranks') {
		cpAddPathway('Звания', $_SERVER['REQUEST_URI']);
		echo '<h3>Звания на форуме</h3>';
		
		if(!isset($cfg['ranks'])) { $cfg['ranks'] = array(); }
		if(!isset($cfg['modrank'])) { $cfg['modrank'] = 1; }
		?>
            <form action="index.php?view=components&amp;do=config&amp;id=<?php echo $id;?>" method="post" name="addform" target="_self" id="form1">
                <table width="500" border="0" cellpadding="10" cellspacing="0" class="proptable" style="margin-bottom:2px">
                    <tr>
                        <td align="center" valign="middle"><strong>Показывать звания для модераторов: </strong></td>
                        <td width="120" align="center" valign="middle">
                            <input name="modrank" type="radio" value="1" <?php if (@$cfg['modrank']) { echo 'checked="checked"'; } ?> /> Да
                            <input name="modrank" type="radio" value="0" <?php if (@!$cfg['modrank']) { echo 'checked="checked"'; } ?>/> Нет
                        </td>
                    </tr>
                </table>
                <table width="500" border="0" cellpadding="10" cellspacing="0" class="proptable">
                    <tr>
                        <td align="center" valign="middle" bgcolor="#EBEBEB"><strong>Звание</strong></td>
                        <td width="120" align="center" valign="middle" bgcolor="#EBEBEB"><strong>Необходимое число сообщений </strong></td>
                    </tr>
                    <?php for($r = 1; $r <= 10; $r++){ ?>
                    <tr>
                        <td align="center" valign="top"><input type="text" name="rank[<?php echo $r?>][title]" style="width:250px;" value="<?php echo htmlspecialchars($cfg['ranks'][$r]['title']) ?>"></td>
                        <td align="center" valign="top"><input name="rank[<?php echo $r?>][msg]" type="text" id="" value="<?php echo htmlspecialchars($cfg['ranks'][$r]['msg']) ?>" size="10" /></td>
                    </tr>
                    <?php } ?>
                </table>
                <p>
                    <input name="opt" type="hidden" id="do" value="saveranks" />
                    <input name="save" type="submit" id="save" value="Сохранить" />
                    <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components&amp;do=config&amp;id=<?php echo $id;?>';"/>
                </p>
            </form>
		<?php
	}
	
	
	if ($opt == 'show_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$item_id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_forum_cats SET published = 1 WHERE id = $item_id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

	if ($opt == 'hide_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$item_id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_forum_cats SET published = 0 WHERE id = $item_id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}
	
	if ($opt == 'submit_cat'){	
		if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("Укажите заголовок категории!"); }
		$published  = (int)$_REQUEST['published'];
		$auth_group = $_REQUEST['auth_group'];
		$ordering   = (int)$_REQUEST['ordering'];		

		$seolink = $model->getCatSeoLink($title);

		$sql = "INSERT INTO cms_forum_cats (title, published, auth_group, ordering, seolink)
				VALUES ('$title', '$published', '$auth_group', '$ordering', '$seolink')";
		dbQuery($sql) ;
		$inCore->redirect('?view=components&do=config&id='.$id.'&opt=list_cats');
	}	  
	
	if($opt == 'delete_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$item_id = $_REQUEST['item_id'];
			dbQuery("DELETE FROM cms_forums WHERE category_id = $item_id");			
			dbQuery("DELETE FROM cms_forum_cats WHERE id = $item_id");			
		}
		$inCore->redirect('?view=components&do=config&id='.$id.'&opt=list_cats');
	}
	
	if ($opt == 'update_cat'){
		if(isset($_REQUEST['item_id'])) { 
			$item_id = (int)$_REQUEST['item_id'];
			
			if (!empty($_REQUEST['title'])) { $title = $_REQUEST['title']; } else { error("Укажите заголовок категории!"); }
			$published  = (int)$_REQUEST['published'];
			$auth_group = $_REQUEST['auth_group'];
			$ordering   = (int)$_REQUEST['ordering'];		

			$seolink = $model->getCatSeoLink($title, $item_id);

			$sql = "UPDATE cms_forum_cats
					SET title='$title', 
						published='$published',
						auth_group='$auth_group',
						ordering='$ordering',
						seolink='$seolink'
					WHERE id = '$item_id'
					LIMIT 1";
			dbQuery($sql) ;
			$inCore->redirect('?view=components&do=config&id='.$id.'&opt=list_cats');
		}
	}
	
	if ($opt == 'list_cats'){
		cpAddPathway('Категории форумов', '?view=components&do=config&id='.$id.'&opt=list_cats');
		echo '<h3>Категории форумов</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';
		
		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$id.'&opt=edit_cat&item_id=%id%';

		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_cat'; //Чтобы вместо 'do=hide&id=1' было 'opt=hide_albun&item_id=1'
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$id.'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Вместе с категорией будут удалены все ее форумы. Удалить категорию?';
		$actions[1]['link']  = '?view=components&do=config&id='.$id.'&opt=delete_cat&item_id=%id%';
				
		//Print table
		cpListTable('cms_forum_cats', $fields, $actions);		
	}

	if ($opt == 'list_forums'){
		echo '<h3>Форумы</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['filter'] = 15;
		$fields[1]['link'] = '?view=components&do=config&id='.$id.'&opt=edit_forum&item_id=%id%';

		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_forum';
	
		$fields[4]['title'] = 'Категория';		$fields[4]['field'] = 'category_id';	$fields[4]['width'] = '150';
		$fields[4]['prc'] = 'cpForumCatById';  $fields[4]['filter'] = 1;  $fields[4]['filterlist'] = cpGetList('cms_forum_cats');
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$id.'&opt=edit_forum&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить форум?';
		$actions[1]['link']  = '?view=components&do=config&id='.$id.'&opt=delete_forum&item_id=%id%';
				
		//Print table
		cpListTable('cms_forums', $fields, $actions, 'parent_id>0', 'NSLeft');		
	}
	
	if ($opt == 'add_cat' || $opt == 'edit_cat'){
		if ($opt=='add_cat'){
			 echo '<h3>Добавить категорию</h3>';
		} else {
					 if(isset($_REQUEST['item_id'])){
						 $item_id = (int)$_REQUEST['item_id'];
						 $sql = "SELECT * FROM cms_forum_cats WHERE id = $item_id LIMIT 1";
						 $result = dbQuery($sql) ;
						 if (mysql_num_rows($result)){
							$mod = mysql_fetch_assoc($result);
						 }
					 }
					
					 echo '<h3>Редактировать категорию</h3>';
			   }
		?>
        <form id="addform" name="addform" method="post" action="index.php?view=components&amp;do=config&amp;id=<?php echo $id;?>">
            <table width="600" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="211" valign="top">Заголовок категории: </td>
                    <td width="195" valign="top"><input name="title" type="text" id="title" size="30" value="<?php echo htmlspecialchars($mod['title']);?>"/></td>
                    <td width="168" valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top">Публиковать категорию?</td>
                    <td valign="top">
                        <input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> Да
                        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> Нет
                    </td>
                    <td valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top">Показывать группе: </td>
                    <td valign="top">
                        <select name="auth_group" id="auth_group">
                            <option value="0" <?php if (@$mod['auth_group']=='0') { echo "selected"; }?>>Всем группам</option>
                            <?php
                                if (isset($mod['auth_group'])) {
                                    echo $inCore->getListItems('cms_user_groups', $mod['auth_group']);
                                } else {
                                    echo $inCore->getListItems('cms_user_groups');
                                }
                            ?>
                        </select>
                    </td>
                    <td valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td valign="top">Порядковый номер: </td>
                    <td valign="top"><input name="ordering" type="text" id="ordering" value="<?php echo @$mod['ordering'];?>" size="5" /></td>
                    <td valign="top">&nbsp;</td>
                </tr>
            </table>
            <p>
                <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_cat') { echo 'value="submit_cat"'; } else { echo 'value="update_cat"'; } ?> />
                <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_cat') { echo 'value="Создать категорию"'; } else { echo 'value="Сохранить категорию"'; } ?> />
                <input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
                <?php
                    if ($opt=='edit_cat'){
                        echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
                    }
                ?>
            </p>
        </form>
		<?php
	}

	if ($opt == 'add_forum' || $opt == 'edit_forum'){	
			
		if ($opt=='add_forum'){
			 echo '<h3>Добавить форум</h3>';
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
						$item_id = array_shift($_SESSION['editlist']);
						if (sizeof($_SESSION['editlist'])==0) { unset($_SESSION['editlist']); } else 
						{ $ostatok = '(На очереди: '.sizeof($_SESSION['editlist']).')'; }
					 } else { $item_id = $_REQUEST['item_id']; }
		
		
					 $sql = "SELECT * FROM cms_forums WHERE id = '$item_id' LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$id.'&opt=edit_forum&item_id='.$item_id);		
						
			}
		?>
        <form action="index.php?view=components&do=config&id=<?php echo $id;?>" method="post" name="addform" id="addform" enctype="multipart/form-data">
            <table width="514" border="0" cellspacing="10" class="proptable">
                <tr>
                    <td width="236"><strong>Название форума:</strong></td>
                    <td width="259"><input name="title" type="text" id="title" size="30" value="<?php echo htmlspecialchars($mod['title']);?>" style="width:254px"/></td>
                </tr>
                <tr>
                    <td valign="top"><strong>Описание форума:</strong></td>
                    <td><textarea name="description" cols="35" rows="2" id="description" style="width:250px"><?php echo @$mod['description']?></textarea></td>
                </tr>
                <tr>
                    <td><strong>Публиковать форум?</strong></td>
                    <td>
                        <input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> Да
                        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> Нет
                    </td>
                </tr>
                <tr>
                    <td><strong>Родительский форум:</strong></td>
                    <td>
                        <?php $rootid = $inDB->get_field('cms_forums', 'parent_id=0', 'id'); ?>
                        <select name="parent_id" id="parent_id" style="width:260px">
                                <option value="<?php echo $rootid?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- Корень форумов --</option>
                        <?php
                            if (isset($mod['parent_id'])){
                               echo $inCore->getListItemsNS('cms_forums', $mod['parent_id']);
                            } else {
                               echo $inCore->getListItemsNS('cms_forums');
                            }
                        ?>
                        </select>
                   </td>
                </tr>
                <tr>
                    <td><strong>Категория:</strong></td>
                    <td>
                        <select name="category_id" id="category_id" style="width:260px">
                        <?php
                            if (isset($mod['category_id'])) { 
                                echo $inCore->getListItems('cms_forum_cats', $mod['category_id'], 'ordering');
                            } else {
                                if (isset($_REQUEST['addto'])){
                                    echo $inCore->getListItems('cms_forum_cats', $_REQUEST['addto'], 'ordering');
                                } else { 
                                   echo $inCore->getListItems('cms_forum_cats', 0, 'ordering');
                                }
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><strong>Показывать группе:</strong><br />
                      <span class="hinttext">
                          Можно выбрать несколько, удерживая CTRL.
                      </span>
                    </td>
                    <td>
					<?php
                    $groups = cmsUser::getGroups();
                    
                    $style  = 'disabled="disabled"';
                    $public = 'checked="checked"';
                    
                    if ($mod['access_list']){
                        $public = '';
                        $style  = '';
                        
                        $access_list = $inCore->yamlToArray($mod['access_list']);
                    
                    }
                    
                    echo '<select style="width: 260px" name="access_list[]" id="showin" size="6" multiple="multiple" '.$style.'>';
                    
                    if ($groups){
                        foreach($groups as $group){
                            if(!$group['is_admin']){
                                echo '<option value="'.$group['id'].'"';
                                if ($access_list){
                                    if (inArray($access_list, $group['id'])){
                                        echo 'selected';
                                    }
                                }
                    
                                echo '>';
                                echo $group['title'].'</option>';
                            }
                        }
                    
                    }
                    
                    echo '</select>';
                    ?>
                    
                    <label><input name="is_access" type="checkbox" id="is_access" onclick="checkAccesList()" value="1" <?php echo $public?> /> <strong>Всем группам</strong></label>
                    </td>
                </tr>
                <tr>
                    <td><strong>Иконка форума:</strong><br/>
                        <span class="hinttext">файл размером 32px и менее вставляется оригиналом</span></td>
                    <td valign="middle"> <?php if (@$mod['icon']) { ?><img src="/upload/forum/cat_icons/<?php echo @$mod['icon'];?>" border="0" /><?php } ?> 
                        <input name="Filedata" type="file" style="width:215px; margin:0 0 0 5px; vertical-align:top" />
                    </td>
                </tr>
                <tr>
                    <td width="236">
                        <strong>Стоимость создания темы:</strong><br/>
                        <span class="hinttext">0 &mdash; бесплатно</span>
                    </td>
                    <td width="259">
                        <?php if (IS_BILLING) { ?>
                            <input name="topic_cost" type="text" id="title" value="<?php echo @$mod['topic_cost'];?>" style="width:60px"/> баллов
                        <?php } else { ?>
                            требуется &laquo;<a href="http://www.instantcms.ru/billing/about.html">Биллинг пользователей</a>&raquo;
                        <?php } ?>
                    </td>
                </tr>
        </table>
        <p>
            <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_forum') { echo 'value="Создать форум"'; } else { echo 'value="Сохранить форум"'; } ?> />
            <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
            <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_forum') { echo 'value="submit_forum"'; } else { echo 'value="update_forum"'; } ?> />
            <?php
            if ($opt=='edit_forum'){
                echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
            }
            ?>
        </p>
        </form>
<script type="text/javascript">
function checkAccesList(){
	if(document.addform.is_access.checked){
		$('select#showin').attr('disabled', 'disabled');
	} else {
		$('select#showin').attr('disabled', '');
	}

}
</script>
	 <?php	
	}
?>