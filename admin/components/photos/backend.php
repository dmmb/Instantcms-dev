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

	define('DEFAULT_PHOTO_TITLE', 'Фото');

	$inCore->loadLib('tags');
    $inCore->includeGraphics();

    $cfg = $inCore->loadComponentConfig('photos');
    if(!isset($cfg['watermark'])) { $cfg['watermark'] = 0; }

    $inDB = cmsDatabase::getInstance();

    $inCore->loadModel('photos');
    $model = new cms_model_photos();

    $opt = $inCore->request('opt', 'str', 'list_albums');

	cpAddPathway('Фотогалерея', '?view=components&do=config&id='.$_REQUEST['id']);
	echo '<h3>Фотогалерея</h3>';

//=================================================================================================//
//=================================================================================================//

	$toolmenu = array();

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['link']        = $_REQUEST['link'];
		$cfg['saveorig']    = $_REQUEST['saveorig'];
		$cfg['maxcols']     = $_REQUEST['maxcols'];
		$cfg['orderby']     = $_REQUEST['orderby'];
		$cfg['orderto']     = $_REQUEST['orderto'];
		$cfg['showlat']     = $_REQUEST['showlat'];
		$cfg['watermark']   = $_REQUEST['watermark'];
		$cfg['tumb_view']   = $_REQUEST['tumb_view'];
		$cfg['tumb_from']   = $_REQUEST['tumb_from'];
		$cfg['tumb_club']   = $_REQUEST['tumb_club'];
		$cfg['is_today']   	= $_REQUEST['is_today'];

		$inCore->saveComponentConfig('photos', $cfg);
        
		$msg = 'Настройки сохранены.';
		$opt = 'config';				
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt=='list_photos' || $opt=='list_albums'){
	
		$toolmenu[0]['icon'] = 'newfolder.gif';
		$toolmenu[0]['title'] = 'Новый альбом';
		$toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_album';
	
		$toolmenu[1]['icon'] = 'newphoto.gif';
		$toolmenu[1]['title'] = 'Новая фотография';
		$toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_photo';

		$toolmenu[2]['icon'] = 'newphotomulti.gif';
		$toolmenu[2]['title'] = 'Массовая загрузка фото';
		$toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_photo_multi';
	
		$toolmenu[3]['icon'] = 'folders.gif';
		$toolmenu[3]['title'] = 'Фотоальбомы';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_albums';
	
		$toolmenu[4]['icon'] = 'listphoto.gif';
		$toolmenu[4]['title'] = 'Все фотографии';
		$toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_photos';

		$toolmenu[5]['icon'] = 'config.gif';
		$toolmenu[5]['title'] = 'Настройки';
		$toolmenu[5]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';

	}

//=================================================================================================//
//=================================================================================================//

	if($opt=='list_photos'){

		$toolmenu[11]['icon'] = 'edit.gif';
		$toolmenu[11]['title'] = 'Редактировать выбранные';
		$toolmenu[11]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=edit_photo&multiple=1');";

		$toolmenu[12]['icon'] = 'delete.gif';
		$toolmenu[12]['title'] = 'Удалить выбранные';
		$toolmenu[12]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=delete_photo&multiple=1');";

		$toolmenu[13]['icon'] = 'show.gif';
		$toolmenu[13]['title'] = 'Публиковать выбранные';
		$toolmenu[13]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=show_photo&multiple=1');";

		$toolmenu[14]['icon'] = 'hide.gif';
		$toolmenu[14]['title'] = 'Скрыть выбранные';
		$toolmenu[14]['link'] = "javascript:checkSel('?view=components&do=config&id=".$_REQUEST['id']."&opt=hide_photo&multiple=1');";

	}

//=================================================================================================//
//=================================================================================================//

	if ($opt=='list_photos' || $opt=='list_albums'){
	} else {
	
		$toolmenu[20]['icon'] = 'save.gif';
		$toolmenu[20]['title'] = 'Сохранить';
		$toolmenu[20]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[21]['icon'] = 'cancel.gif';
		$toolmenu[21]['title'] = 'Отмена';
		$toolmenu[21]['link'] = '?view=components&do=config&id='.$_REQUEST['id'];
	
	}

	cpToolMenu($toolmenu);

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'show_photo'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_photo_files', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbShowList('cms_photo_files', $_REQUEST['item']);				
			header('location:'.$_SERVER['HTTP_REFERER']);				
		}			
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'hide_photo'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_photo_files', $_REQUEST['item_id']);  }
			echo '1'; exit;
		} else {
			dbHideList('cms_photo_files', $_REQUEST['item']);				
			header('location:'.$_SERVER['HTTP_REFERER']);			
		}			
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'submit_photo'){

			$photo['album_id']      = $inCore->request('album_id', 'int', 1);
            $photo['title']         = $inCore->request('title', 'str', DEFAULT_PHOTO_TITLE);
			$photo['description']   = $inCore->request('description', 'html');
            $photo['description']   = $inDB->escape_string($photo['description']);
			$photo['published']     = $inCore->request('published', 'int', 1);
			$photo['showdate']      = $inCore->request('showdate', 'int', 1);
			$photo['tags']          = $inCore->request('tags', 'str');

			$uploaddir              = PATH.'/images/photos/';
			$realfile               = $_FILES['picture']['name'];
			$filename               = md5($realfile . time()).'.jpg';
			$uploadfile             = $uploaddir . $realfile;
			$uploadphoto            = $uploaddir . $filename;
			$uploadthumb            = $uploaddir . 'small/' . $filename;
			$uploadthumb2           = $uploaddir . 'medium/' . $filename;

            $photo['filename']      = $filename;

            $album                  = $model->getAlbumThumbsData($photo['album_id']);
										
			if (@move_uploaded_file($_FILES['picture']['tmp_name'], $uploadphoto)) {								
                @img_resize($uploadphoto, $uploadthumb, $album['thumb1'], $album['thumb1'], $album['thumbsqr']);
                @img_resize($uploadphoto, $uploadthumb2, $album['thumb2'], $album['thumb2'], false, $cfg['watermark']);
                if ($cfg['watermark']) { @img_add_watermark($uploadphoto);	}
				if (@!$cfg['saveorig']){ @unlink($uploadphoto); }
                
                $model->addPhoto($photo);

			} else { $msg = 'Ошибка загрузки фотографии!'; }
			
            $inCore->redirect('?view=components&do=config&opt=list_photos&id='.$_REQUEST['id']);
	}	  

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'submit_photo_multi'){	
			echo '<h3>Загрузка файлов завершена</h3>';

            $photo['album_id']     = $inCore->request('album_id', 'int');			
			$photo['description']  = $inCore->request('description', 'html');
            $photo['description']  = $inDB->escape_string($photo['description']);
			$photo['published']    = $inCore->request('published', 'int');
			$photo['showdate']     = $inCore->request('showdate', 'int');
            $photo['tags']         = $inCore->request('tags', 'str');
			
			$uploaddir             = PATH.'/images/photos/';

            $album                 = $model->getAlbumThumbsData($photo['album_id']);
			$titlemode             = $inCore->request('titlemode', 'str');

			$loaded_files = array();
			//////////
            $list_files = array();

            foreach($_FILES['upfile'] as $key=>$value) {
                foreach($value as $k=>$v) { $list_files['upfile'.$k][$key] = $v; }
            }

            foreach ($list_files as $key=>$data_array) {
					$error = $data_array['error'];
					if ($error == UPLOAD_ERR_OK) {
						
						$realfile = $data_array['name'];
						$tmp_name = $data_array['tmp_name'];
			
						$lid = dbGetFields('cms_photo_files', 'id>0', 'id', 'id DESC');
						$lastid = $lid['id']+1;	
						$filename = md5($realfile . '-' . $inUser->id . '-' . time()).'.jpg';
			
						$uploadfile = $uploaddir . $realfile;
						$uploadphoto = $uploaddir . $filename;
						$uploadthumb = $uploaddir . 'small/' . $filename;
						$uploadthumb2 = $uploaddir . 'medium/' . $filename;

                        $photo['filename'] = $filename;
						
                        if (move_uploaded_file($tmp_name, $uploadphoto)){
                            $loaded_files[] = $realfile;

                            @img_resize($uploadphoto, $uploadthumb, $album['thumb1'], $album['thumb1'], $album['thumbsqr']);
                            @img_resize($uploadphoto, $uploadthumb2, $album['thumb2'], $album['thumb2'], false, $cfg['watermark']);
                            if ($cfg['watermark']) { @img_add_watermark($uploadphoto);	}
                            
                            if (@!$inCore->inRequest('saveorig')){ @unlink($uploadphoto); }

                            if($titlemode == 'number'){
                                $photo['title'] = 'Фото #'.sizeof($loaded_files);
                            } else {
                                $photo['title'] = $realfile;
                            }

                            $model->addPhoto($photo);
                        }
					}
				}
					
				echo '<div style="padding:20px">';	
                    if (sizeof($loaded_files)){
                        echo '<div><strong>Загруженные файлы:</strong></div>';
                        echo '<ul>';
                            foreach($loaded_files as $k=>$val){
                                echo '<li>'.$val.'</li>';
                            }
                        echo '</ul>';
                    } else {
                        echo '<div style="color:red">Ни один файл не был загружен. Может файлы слишком большие?</div>';
                        echo '<div style="color:red">Имена файлов не должны содержать пробелов и русских букв.</div>';
                    }
                    echo '<div><a href="/admin/index.php?view=components&do=config&opt=list_photos&id='.$_REQUEST['id'].'">Продолжить</a> &rarr;</div>';
				echo '</div>';
	}	  

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'update_photo'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
			
            $photo['album_id']     = $inCore->request('album_id', 'int');
            $photo['title']        = $inCore->request('title', 'str');
			$photo['description']  = $inCore->request('description', 'html');
            $photo['description']  = $inDB->escape_string($photo['description']);
			$photo['published']    = $inCore->request('published', 'int');
			$photo['showdate']     = $inCore->request('showdate', 'int');
            $photo['tags']         = $inCore->request('tags', 'str');
            $photo['filename']     = $inDB->get_field('cms_photo_files', "id={$id}", 'file');

            $model->updatePhoto($id, $photo);
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_photos');
		} else {
			$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_photo');
		}
	}

//=================================================================================================//
//=================================================================================================//

	if($opt == 'delete_photo'){
        
		if (!isset($_REQUEST['item'])){
            $id = (int)$_REQUEST['item_id'];
			if ($id >= 0){
				$model->deletePhoto($id);
			}
		} else {
			$model->deletePhotos($_REQUEST['item']);
		}

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_photos');

	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'config') {

        cpAddPathway('Настройки', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config');

		if (@$msg) { echo '<p class="success">'.$msg.'</p>'; }
			
		if (!isset($cfg['showlat'])) { $cfg['showlat'] = 1; }		
		if (!isset($cfg['orderto'])) { $cfg['orderto'] = 'title'; }		
		if (!isset($cfg['orderby'])) { $cfg['orderby'] = 'ASC'; }		
		if (!isset($cfg['tumb_view'])) { $cfg['tumb_view'] = 1; }
		if (!isset($cfg['tumb_from'])) { $cfg['tumb_from'] = 1; }	
		
		?>
		<?php cpCheckWritable('/images/photos', 'folder'); ?>
		<?php cpCheckWritable('/images/photos/medium', 'folder'); ?>
		<?php cpCheckWritable('/images/photos/small', 'folder'); ?>				

			<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id']; ?>" method="post" enctype="multipart/form-data" name="optform">
			  <table width="" border="0" cellpadding="10" cellspacing="0" class="proptable">
				<tr>
				  <td width="300"><strong>Показывать ссылки на оригинал: </strong></td>
				  <td width="250">
				  	<input name="link" type="radio" value="1" <?php if (@$cfg['link']) { echo 'checked="checked"'; } ?>/> Да
				  <input name="link" type="radio" value="0" <?php if (@!$cfg['link']) { echo 'checked="checked"'; } ?>/> Нет				  </td>
				</tr>
				<tr>
				  <td><strong>Сохранять оригиналы при загрузке<br /> 
			      фотографий пользователями:</strong> </td>
				  <td>
					  <input name="saveorig" type="radio" value="1" <?php if (@$cfg['saveorig']) { echo 'checked="checked"'; } ?>/> Да
					  <input name="saveorig" type="radio" value="0" <?php if (@!$cfg['saveorig']) { echo 'checked="checked"'; } ?>/> Нет				  </td>
			    </tr>
				<tr>
				  <td><strong>Количество колонок для<br />вывода списка альбомов: </strong></td>
				  <td><input name="maxcols" type="text" id="maxcols" size="5" value="<?php echo @$cfg['maxcols'];?>"/> шт</td>
			    </tr>
				<tr>
				  <td valign="top"><strong>Сортировать список альбомов: </strong></td>
				  <td><select name="orderby" style="width:190px">
                    <option value="title" <?php if(@$cfg['orderby']=='title') { echo 'selected'; } ?>>По алфавиту</option>
                    <option value="pubdate" <?php if(@$cfg['orderby']=='pubdate') { echo 'selected'; } ?>>По дате</option>
                    <option value="content_count" <?php if(@$cfg['orderby']=='content_count') { echo 'selected'; } ?>>По количеству фото</option>
                  </select>
                    <select name="orderto" style="width:190px">
                      <option value="desc" <?php if(@$cfg['orderto']=='desc') { echo 'selected'; } ?>>по убыванию</option>
                      <option value="asc" <?php if(@$cfg['orderto']=='asc') { echo 'selected'; } ?>>по возрастанию</option>
                    </select></td>
			    </tr>
				<tr>
				  <td><strong>Показывать ссылки на последние и лучшие фото: </strong></td>
				  <td>
				  	<input name="showlat" type="radio" value="1" <?php if (@$cfg['showlat']) { echo 'checked="checked"'; } ?>/> Да
					<input name="showlat" type="radio" value="0" <?php if (@!$cfg['showlat']) { echo 'checked="checked"'; } ?>/> Нет			      </td>
			    </tr>
				<tr>
				  <td>
                      <strong>Наносить водяной знак:</strong><br />
                      <span class="hinttext">Если включено, то на все загружаемые фотографии будет наносится изображение из файла "<a href="/images/watermark.png" target="_blank">/images/watermark.png</a>"</span></td>
				  <td>
					<input name="watermark" type="radio" value="1" <?php if (@$cfg['watermark']) { echo 'checked="checked"'; } ?>/> Да
					<input name="watermark" type="radio" value="0" <?php if (@!$cfg['watermark']) { echo 'checked="checked"'; } ?>/> Нет				  </td>
			    </tr>
				<tr>
				  <td>
                      <strong>Показ мини-эскизов:</strong><br />
                      <span class="hinttext">Выводить фото вместо иконки фотольбома</span>
                  </td>
				  <td>
					<select name="tumb_view" style="width:190px">
                        <option value="1" <?php if(@$cfg['tumb_view']=='1') { echo 'selected'; } ?>>Не показывать</option>
                        <option value="2" <?php if(@$cfg['tumb_view']=='2') { echo 'selected'; } ?>>Случайный</option>
                        <option value="3" <?php if(@$cfg['tumb_view']=='3') { echo 'selected'; } ?>>По выбору</option>
                  	</select><br />
                    <label>
                        <input name="tumb_club" type="checkbox" value="1" <?php if (@$cfg['tumb_club']) { echo 'checked'; } ?>/>
                        Не применять к альбомам клубов
                    </label>
                    </td>
			    </tr>
				<tr>
				  <td>
                      <strong>Выбор случайных мини-эскизов:</strong><br />
                      <span class="hinttext">Из каких альбомов выбирать эскизы, если выбран их случайный показ</span>
                  </td>
				  <td>
					<select name="tumb_from" style="width:190px">
                        <option value="1" <?php if(@$cfg['tumb_from']=='1') { echo 'selected'; } ?>>Из текущего</option>
                        <option value="2" <?php if(@$cfg['tumb_from']=='2') { echo 'selected'; } ?>>Включая вложенные</option>
                  	</select>
                  </td>
			    </tr>
                <tr>
                    <td><strong>Показывать количество новых фото за день?</strong></td>
                        <td>
                            <input name="is_today" type="radio" value="1" <?php if (@$cfg['is_today']) { echo 'checked="checked"'; } ?> /> Да
                            <input name="is_today" type="radio" value="0"  <?php if (@!$cfg['is_today']) { echo 'checked="checked"'; } ?> /> Нет
                        </td>
                </tr>
			  </table>
			  <p>
				<input name="opt" type="hidden" value="saveconfig" />
				<input name="save" type="submit" id="save" value="Сохранить" />
			  </p>
		</form>
		<?php
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'show_album'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_photo_albums SET published = 1 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'hide_album'){
		if(isset($_REQUEST['item_id'])) { 
			$id = $_REQUEST['item_id'];
			$sql = "UPDATE cms_photo_albums SET published = 0 WHERE id = $id";
			dbQuery($sql) ;
			echo '1'; exit;
		}
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'submit_album'){

        $album['title']         = $inCore->request('title', 'str');
		$album['description']   = $inCore->request('description', 'html');
        $album['description']   = $inDB->escape_string($album['description']);
		$album['published']     = $inCore->request('published', 'int');
		$album['showdate']      = $inCore->request('showdate', 'int');
		$album['parent_id']     = $inCore->request('parent_id', 'int');
		$album['showtype']      = $inCore->request('showtype', 'str');
		$album['public']        = $inCore->request('public', 'int');
		$album['orderby']       = $inCore->request('orderby', 'str');
		$album['orderto']       = $inCore->request('orderto', 'str');
		$album['perpage']       = $inCore->request('perpage', 'int');
		$album['thumb1']        = $inCore->request('thumb1', 'int');
		$album['thumb2']        = $inCore->request('thumb2', 'int');
		$album['thumbsqr']      = $inCore->request('thumbsqr', 'int');
		$album['cssprefix']     = $inCore->request('cssprefix', 'str');
		$album['nav']           = $inCore->request('nav', 'int');
		$album['uplimit']       = $inCore->request('uplimit', 'int');
		$album['maxcols']       = $inCore->request('maxcols', 'int');
		$album['orderform']     = $inCore->request('orderform', 'int');
		$album['showtags']      = $inCore->request('showtags', 'int');
		$album['bbcode']        = $inCore->request('bbcode', 'int');
        $album['is_comments']   = $inCore->request('is_comments', 'int');
		
		$model->addAlbum($album);
				
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_albums');
	}	  

//=================================================================================================//
//=================================================================================================//

	if($opt == 'delete_album'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
            $model->deleteAlbum($id);
		}
		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_albums');
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'update_album'){
		if($inCore->inRequest('item_id')) {
			$id = $inCore->request('item_id', 'int');
			
            $album['title']         = $inCore->request('title', 'str');
            $album['description']   = $inCore->request('description', 'html');
            $album['description']   = $inDB->escape_string($album['description']);
            $album['published']     = $inCore->request('published', 'int');
            $album['showdate']      = $inCore->request('showdate', 'int');
            $album['parent_id']     = $inCore->request('parent_id', 'int');
            $album['is_comments']   = $inCore->request('is_comments', 'int');
            $album['showtype']      = $inCore->request('showtype', 'str');
            $album['public']        = $inCore->request('public', 'int');
            $album['orderby']       = $inCore->request('orderby', 'str');
            $album['orderto']       = $inCore->request('orderto', 'str');
            $album['perpage']       = $inCore->request('perpage', 'int');
            $album['thumb1']        = $inCore->request('thumb1', 'int');
            $album['thumb2']        = $inCore->request('thumb2', 'int');
            $album['thumbsqr']      = $inCore->request('thumbsqr', 'int');
            $album['cssprefix']     = $inCore->request('cssprefix', 'str');
            $album['nav']           = $inCore->request('nav', 'int');
            $album['uplimit']       = $inCore->request('uplimit', 'int');
            $album['maxcols']       = $inCore->request('maxcols', 'int');
            $album['orderform']     = $inCore->request('orderform', 'int');
            $album['showtags']      = $inCore->request('showtags', 'int');
            $album['bbcode']        = $inCore->request('bbcode', 'int');
			$album['iconurl']       = $inCore->request('iconurl', 'str');
								
            $model->updateAlbum($id, $album);
							
			$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_albums');
		}
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'list_albums'){
		
		echo '<h3>Фотоальбомы</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'Lt';			$fields[0]['field'] = 'NSLeft';			$fields[0]['width'] = '30';
		
		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';
		$fields[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_album&item_id=%id%';

		$fields[2]['title'] = 'Показ';		$fields[2]['field'] = 'published';	$fields[2]['width'] = '100';
		$fields[2]['do'] = 'opt'; $fields[2]['do_suffix'] = '_album'; //Чтобы вместо 'do=hide&id=1' было 'opt=hide_album&item_id=1'

		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_album&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Вместе с альбомом будут удалены все фотографии. Удалить фотоальбом?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_album&item_id=%id%';
				
		//Print table
		cpListTable('cms_photo_albums', $fields, $actions, 'parent_id>0 AND NSDiffer=""', 'NSLeft');		
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'list_photos'){
		cpAddPathway('Фотографии', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_photos');
		echo '<h3>Фотографии</h3>';
		
		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Дата';		$fields[1]['field'] = 'pubdate';		$fields[1]['width'] = '80';		$fields[1]['filter'] = 15;
		$fields[1]['fdate'] = '%d/%m/%Y';

		$fields[2]['title'] = 'Название';	$fields[2]['field'] = 'title';		$fields[2]['width'] = '';
		$fields[2]['filter'] = 15;
		$fields[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_photo&item_id=%id%';

		$fields[3]['title'] = 'Показ';		$fields[3]['field'] = 'published';	$fields[3]['width'] = '100';
		$fields[3]['do'] = 'opt'; $fields[3]['do_suffix'] = '_photo';

		$fields[4]['title'] = 'Просмотров';	$fields[4]['field'] = 'hits';		$fields[4]['width'] = '90';
		
		$fields[5]['title'] = 'Альбом';		$fields[5]['field'] = 'album_id';	$fields[5]['width'] = '250';
		$fields[5]['prc'] = 'cpPhotoAlbumById';  $fields[5]['filter'] = 1;  $fields[5]['filterlist'] = cpGetList('cms_photo_albums');
	
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_photo&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить фотографию?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_photo&item_id=%id%';
				
		//Print table
		cpListTable('cms_photo_files', $fields, $actions, '', 'id DESC');		
	}

//=================================================================================================//
//=================================================================================================//
	
	if ($opt == 'add_album' || $opt == 'edit_album'){
		if ($opt=='add_album'){
			 cpAddPathway('Фотоальбомы', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_albums');
			 cpAddPathway('Добавить фотоальбом', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_album');
			 echo '<h3>Добавить фотоальбом</h3>';
		} else {
					 if(isset($_REQUEST['item_id'])){
						 $id = $_REQUEST['item_id'];
						 $sql = "SELECT * FROM cms_photo_albums WHERE id = $id LIMIT 1";
						 $result = dbQuery($sql) ;
						 if (mysql_num_rows($result)){
							$mod = mysql_fetch_assoc($result);
							
						 }
					 }
					 cpAddPathway('Фотоальбомы', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_albums');
					 cpAddPathway('Редактировать фотоальбом', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_album');
					 echo '<h3>Редактировать фотоальбом</h3>';
			   }

               //DEFAULT VALUES
               if (!isset($mod['thumb1'])) { $mod['thumb1'] = 96; }
               if (!isset($mod['thumb2'])) { $mod['thumb2'] = 450; }
               if (!isset($mod['thumbsqr'])) { $mod['thumbsqr'] = 1; }
               if (!isset($mod['is_comments'])) { $mod['is_comments'] = 0; }
               if (!isset($mod['maxcols'])) { $mod['maxcols'] = 4; }
               if (!isset($mod['showtype'])) { $mod['showtype'] = 'lightbox'; }
               if (!isset($mod['perpage'])) { $mod['perpage'] = '20'; }
               if (!isset($mod['uplimit'])) { $mod['uplimit'] = 20; }
               if (!isset($mod['published'])) { $mod['published'] = 1; }
        
		?>
<script type="text/javascript">
function showMapMarker(){
    var file = $('select[name=iconurl]').val();
    $('img#marker_demo').attr('src', '/images/photos/small/'+file);
}
</script>
		
        <form id="addform" name="addform" method="post" action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>">
            <table width="610" border="0" cellspacing="5" class="proptable">
                <tr>
                    <td width="300">Название альбома:</td>
                    <td><input name="title" type="text" id="title" size="30" value="<?php echo htmlspecialchars($mod['title']); ?>"/></td>
                </tr>
                <tr>
                    <td valign="top">Родительский альбом:</td>
                    <td valign="top">
                        <?php if($opt=='add_album' || ($opt=='edit_album' && @$mod['NSDiffer']=='')){ ?>
                            <?php $rootid = $inDB->get_field('cms_photo_albums', "parent_id=0 AND NSDiffer=''", 'id'); ?>
                            <select name="parent_id" size="8" id="parent_id" style="width:285px">
                                <option value="<?php echo $rootid; ?>" <?php if (@$mod['parent_id']==$rootid || !isset($mod['parent_id'])) { echo 'selected'; }?>>-- Корневой альбом --</option>
                                <?php
                                    if (isset($mod['parent_id'])){
                                        echo $inCore->getListItemsNS('cms_photo_albums', $mod['parent_id']);
                                    } else {
                                        echo $inCore->getListItemsNS('cms_photo_albums');                                        
                                    }
                                ?>
                            </select>
                        <?php } else {
                                $club['id']     = substr($mod['NSDiffer'], 4);
                                $club['title']  = $inDB->get_field('cms_clubs', "id={$club['id']}", 'title');
                        ?>
                            <input type="hidden" name="parent_id" value="<?php echo $mod['parent_id']; ?>" />
                            Клуб <a href="index.php?view=components&do=config&id=23&opt=edit&item_id=<?php echo $club['id']; ?>"><?php echo $club['title'];?></a>
                        <?php
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Публиковать альбом?</td>
                        <td>
                            <input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> /> Да
                            <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> /> Нет
                        </td>
                </tr>
                <tr>
                    <td>Показывать даты и комментарии фото?</td>
                        <td>
                            <input name="showdate" type="radio" value="1" checked="checked" <?php if (@$mod['showdate']) { echo 'checked="checked"'; } ?> /> Да
                            <input name="showdate" type="radio" value="0"  <?php if (@!$mod['showdate']) { echo 'checked="checked"'; } ?> /> Нет
                        </td>
                </tr>
                <tr>
                    <td valign="top">Показывать теги фото:</td>
                    <td valign="top">
                        <input name="showtags" type="radio" value="1" checked="checked" <?php if (@$mod['showtags']) { echo 'checked="checked"'; } ?> /> Да
                        <input name="showtags" type="radio" value="0"  <?php if (@!$mod['showtags']) { echo 'checked="checked"'; } ?> /> Нет
                    </td>
                </tr>
                <tr>
                    <td valign="top">Показывать код для вставки на форум:</td>
                    <td valign="top">
                        <input name="bbcode" type="radio" value="1" checked="checked" <?php if (@$mod['bbcode']) { echo 'checked="checked"'; } ?> /> Да
                        <input name="bbcode" type="radio" value="0"  <?php if (@!$mod['bbcode']) { echo 'checked="checked"'; } ?> /> Нет
                    </td>
                </tr>
                <tr>
                    <td valign="top">Комментарии для альбома:</td>
                    <td valign="top">
                        <input name="is_comments" type="radio" value="1" checked="checked" <?php if (@$mod['is_comments']) { echo 'checked="checked"'; } ?> /> Да
                        <input name="is_comments" type="radio" value="0"  <?php if (@!$mod['is_comments']) { echo 'checked="checked"'; } ?> /> Нет
                    </td>
                </tr>
                <tr>
                    <td>Сортировать фото:</td>
                    <td>
                        <select name="orderby" id="orderby" style="width:285px">
                            <option value="title" <?php if(@$mod['orderby']=='title') { echo 'selected'; } ?>>По алфавиту</option>
                            <option value="pubdate" <?php if(@$mod['orderby']=='pubdate') { echo 'selected'; } ?>>По дате</option>
                            <option value="rating" <?php if(@$mod['orderby']=='rating') { echo 'selected'; } ?>>По рейтингу</option>
                            <option value="hits" <?php if(@$mod['orderby']=='hits') { echo 'selected'; } ?>>По просмотрам</option>
                        </select>
                        <select name="orderto" id="orderto" style="width:285px">
                            <option value="desc" <?php if(@$mod['orderto']=='desc') { echo 'selected'; } ?>>по убыванию</option>
                            <option value="asc" <?php if(@$mod['orderto']=='asc') { echo 'selected'; } ?>>по возрастанию</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Вывод фотографий:</td>
                    <td>
                        <select name="showtype" id="showtype" style="width:285px">
                            <option value="list" <?php if(@$mod['showtype']=='list') { echo 'selected'; } ?>>Таблица (список)</option>
                            <option value="thumb" <?php if(@$mod['showtype']=='thumb') { echo 'selected'; } ?>>Галерея</option>
                            <option value="lightbox" <?php if(@$mod['showtype']=='lightbox') { echo 'selected'; } ?>>Галерея (лайтбокс)</option>
                            <option value="simple" <?php if(@$mod['showtype']=='simple') { echo 'selected'; } ?>>Галерея (простая)</option>
                            <option value="fast" <?php if(@$mod['showtype']=='fast') { echo 'selected'; } ?>>Галерея (быстрая)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Число колонок для вывода:</td>
                    <td>
                        <input name="maxcols" type="text" id="maxcols" size="5" value="<?php echo @$mod['maxcols'];?>"/> шт.
                    </td>
                </tr>
                <tr>
                    <td>Добавление фото пользователями:</td>
                    <td>
                        <select name="public" id="select" style="width:285px">
                            <option value="0" <?php if(@$mod['public']=='0') { echo 'selected'; } ?>>Запрещено</option>
                            <option value="1" <?php if(@$mod['public']=='1') { echo 'selected'; } ?>>Разрешено с премодерацией</option>
                            <option value="2" <?php if(@$mod['public']=='2') { echo 'selected'; } ?>>Разрешено без модерации</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Макс. загрузок от одного пользователя в сутки:</td>
                    <td>
                        <input name="uplimit" type="text" id="uplimit" size="5" value="<?php echo @$mod['uplimit'];?>"/> шт.
                    </td>
                </tr>
                <tr>
                    <td>Форма сортировки:</td>
                    <td>
                        <input name="orderform" type="radio" value="1" checked="checked" <?php if (@$mod['orderform']) { echo 'checked="checked"'; } ?> /> Показать
                        <input name="orderform" type="radio" value="0"  <?php if (@!$mod['orderform']) { echo 'checked="checked"'; } ?> /> Скрыть
                    </td>
                </tr>
                <tr>
                    <td>Навигация в альбоме:</td>
                    <td>
                        <input name="nav" type="radio" value="1" <?php if (@$mod['nav']) { echo 'checked="checked"'; } ?> /> Включена
                        <input name="nav" type="radio" value="0"  <?php if (@!$mod['nav']) { echo 'checked="checked"'; } ?> /> Выключена
                    </td>
                </tr>
                <tr>
                    <td>CSS-префикс фотографий:</td>
                    <td><input name="cssprefix" type="text" id="cssprefix" size="10" value="<?php echo @$mod['cssprefix'];?>"/></td>
                </tr>
                <tr>
                    <td>Фотографий на странице:</td>
                    <td>
                        <input name="perpage" type="text" id="perpage" size="5" value="<?php echo @$mod['perpage'];?>"/> шт.</td>
                </tr>
                <tr>
                    <td>Ширина маленькой копии: </td>
                    <td>
                        <table border="0" cellspacing="0" cellpadding="1">
                            <tr>
                                <td width="100" valign="middle">
                                    <input name="thumb1" type="text" id="thumb1" size="3" value="<?php echo @$mod['thumb1'];?>"/> пикс.
                                </td>
                                <td width="100" align="center" valign="middle" style="background-color:#EBEBEB">Квадратные:</td>
                                <td width="115" align="center" valign="middle" style="background-color:#EBEBEB">
                                    <input name="thumbsqr" type="radio" value="1" checked="checked" <?php if (@$mod['thumbsqr']) { echo 'checked="checked"'; } ?> /> Да
                                    <input name="thumbsqr" type="radio" value="0"  <?php if (@!$mod['thumbsqr']) { echo 'checked="checked"'; } ?> />Нет
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>Ширина средней копии: </td>
                    <td>
                        <input name="thumb2" type="text" id="thumb2" size="3" value="<?php echo @$mod['thumb2'];?>"/> пикс.
                    </td>
                </tr>
				<?php
                    if ($opt=='edit_album' && $cfg['tumb_view'] == 3){ ?>
                <tr>
                    <td valign="top">Мини-эскиз:<br />
                    <?php if ($mod['iconurl']){ ?>
                    <img id="marker_demo" src="/images/photos/small/<?php echo $mod['iconurl']; ?>" border="0">
                    <?php  } else { ?>
                    <img id="marker_demo" src="/images/photos/no_image.png" border="0">
                    <?php  } ?>
                    </td>
                    <td valign="top">
                    <?php if ($inDB->rows_count('cms_photo_files', 'album_id = '.$id.'')) { ?>	
                            <select name="iconurl" id="iconurl" style="width:285px" onchange="showMapMarker()">
                                <?php
                                    if ($mod['iconurl']){
                                        echo $inCore->getListItems('cms_photo_files', $mod['iconurl'], 'id', 'ASC', 'album_id = '.$id.' AND published = 1', 'file');
                                    } else {
										echo '<option value="" selected>Выберите мини-эскиз</option>';
                                        echo $inCore->getListItems('cms_photo_files', 0, 'id', 'ASC', 'album_id = '.$id.' AND published = 1', 'file');                                        
                                    }
                                ?>
                            </select>
                       <?php  } else { ?>
                       		В альбоме нет еще фотографий, загрузите фотографии в альбом, после выберите мини-эскиз.
                       <?php  } ?>
                    </td>
                </tr>
            <?php
                }
            ?>
            </table>
            <table width="100%" border="0">
                <tr>
                    <div style="margin:5px 0px 5px 0px">Описание альбома:</div>
                    <textarea name="description" style="width:580px" rows="4"><?php echo @$mod['description']?></textarea>
                </tr>
            </table>

        <p>
            <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_album') { echo 'value="submit_album"'; } else { echo 'value="update_album"'; } ?> />
            <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_album') { echo 'value="Создать альбом"'; } else { echo 'value="Сохранить альбом"'; } ?> />
            <input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
            <?php
                if ($opt=='edit_album'){
                    echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
                }
            ?>
        </p>
    </form>
		<?php
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'add_photo' || $opt == 'edit_photo'){	
			
		if ($opt=='add_photo'){
			 echo '<h3>Добавить фотографию</h3>';
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
		
		
					 $sql = "SELECT f.*, a.NSDiffer as NSDiffer, a.title as album
                             FROM cms_photo_files f, cms_photo_albums a
                             WHERE f.id = $id AND f.album_id = a.id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>'.$mod['title'].' '.$ostatok.'</h3>';
					 cpAddPathway('Фотографии', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_photos');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_photo&item_id='.$id);		
						
			}
		?>
		<?php cpCheckWritable('/images/photos', 'folder'); ?>
		<?php cpCheckWritable('/images/photos/medium', 'folder'); ?>
		<?php cpCheckWritable('/images/photos/small', 'folder'); ?>				
        <form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
        <table width="600" border="0" cellspacing="5" class="proptable">
        <tr>
            <td width="177">Название фотографии: </td>
            <td width="311"><input name="title" type="text" id="title" size="30" value="<?php echo htmlspecialchars($mod['title']);?>"/></td>
        </tr>
        <tr>
            <td valign="top">Фотоальбом:</td>
            <td valign="top">
                <?php if($opt=='add_photo' || ($opt=='edit_photo' && @$mod['NSDiffer']=='')){ ?>
                    <select name="album_id" size="8" id="album_id" style="width:250px">
                        <?php $rootid = $inDB->get_field('cms_photo_albums', "parent_id=0 AND NSDiffer=''", 'id'); ?>
                        <option value="<?php echo $rootid; ?>" <?php if (@$mod['album_id']==$rootid || !isset($mod['album_id'])) { echo 'selected'; }?>>-- Корневой альбом --</option>
                        <?php
                            if (isset($mod['album_id'])){
                               echo $inCore->getListItemsNS('cms_photo_albums', $mod['album_id']);
                            } else {
                               echo $inCore->getListItemsNS('cms_photo_albums');
                            }
                        ?>
                    </select>
                <?php } else {
                    $club['id']     = substr($mod['NSDiffer'], 4);
                    $club['title']  = $inDB->get_field('cms_clubs', "id={$club['id']}", 'title');
                ?><input type="hidden" name="album_id" value="<?php echo $mod['album_id']; ?>" />
                    Клуб <a href="index.php?view=components&do=config&id=23&opt=edit&item_id=<?php echo $club['id']; ?>"><?php echo $club['title'];?></a> &rarr; <?php echo $mod['album']; ?>
                <?php
                  }
                ?>
            </td>
        </tr>
        <tr>
            <td>Файл фотографии: </td>
            <td><?php if (@$mod['file']) {
                echo '<div><img src="/images/photos/small/'.$mod['file'].'" border="1" /></div>';
                echo '<div><a href="/images/photos/medium/'.$mod['file'].'" title="Посмотреть фото">'.$mod['file'].'</a></div>';
            } else { ?>
                <input name="picture" type="file" id="picture" size="30" />
            <?php } ?></td>
        </tr>
        <tr>
            <td>Публиковать фотографию?</td>
            <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
                Да
                <label>
                    <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
            Нет</label></td>
        </tr>
        <tr>
            <td>Показывать дату? </td>
            <td><input name="showdate" type="radio" value="1" checked="checked" <?php if (@$mod['showdate']) { echo 'checked="checked"'; } ?> />
                Да
                <label>
                    <input name="showdate" type="radio" value="0"  <?php if (@!$mod['showdate']) { echo 'checked="checked"'; } ?> />
            Нет</label></td>
        </tr>
        <?php if ($do=='add_photo'){ ?>
        <tr>
        <td>Cохранить оригинал: </td>
        <td><input name="saveorig" type="radio" value="1" checked="checked" />Да<input name="saveorig" type="radio" value="0"  />Нет</label></td>
        </tr>
        <?php } ?>
        <tr>
            <td valign="top">Теги фотографии: <br />
            <span class="hinttext">Ключевые слова, через запятую</span></td>
            <td valign="top"><input name="tags" type="text" id="tags" size="45" value="<?php if (isset($mod['id'])) { echo cmsTagLine('photo', $mod['id'], false); } ?>" /></td>
        </tr>
        </table>
        <table width="100%" border="0">
            <tr>
                <?php
                if(!isset($mod['user']) || @$mod['user']==1){
                    echo '<td width="52%" valign="top">';
                    echo 'Описание фотографии:<br/>';

                    $inCore->insertEditor('description', $mod['description'], '260', '605');
                    
                    echo '</td>';
                }
                ?>
            </tr>
        </table>
        <p>
            <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_photo') { echo 'value="Загрузить фото"'; } else { echo 'value="Сохранить фото"'; } ?> />
            <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
            <input name="opt" type="hidden" id="opt" <?php if ($opt=='add_photo') { echo 'value="submit_photo"'; } else { echo 'value="update_photo"'; } ?> />
            <?php
            if ($opt=='edit_photo'){
                echo '<input name="item_id" type="hidden" value="'.$mod['id'].'" />';
            }
            ?>
        </p>
        </form>
	 <?php	
	}

//=================================================================================================//
//=================================================================================================//

	if ($opt == 'add_photo_multi'){	
			$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.js"></script>';
			$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/multifile/jquery.multifile.js"></script>';
			$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.blockUI.js"></script>';

			$GLOBALS['cp_page_head'][] = '<script type="text/javascript">
										  function startUpload(){
											$("#upload_btn").attr(\'disabled\', \'true\');
											$("#upload_btn").attr(\'value\', \'Идет загрузка...\');
											$("#cancel_btn").css(\'display\', \'none\');
											$("#loadergif").css(\'display\', \'block\');
											document.addform.submit();													
										  }
									   </script>';

	
			 echo '<h3>Массовая загрузка фото</h3>';

			 cpAddPathway('Массовая загрузка фото', $_SERVER['REQUEST_URI']);
						
		?>
         <form action="/admin/index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
         <table width="600" border="0" cellspacing="5" class="proptable">
         <tr>
             <td width="177">Названия фотографий: </td>
             <td width="311"><label>
                     <select name="titlemode" id="titlemode">
                         <option value="number">Фото + номер</option>
                         <option value="original">Оригинальные названия файлов</option>
                     </select>
             </label></td>
         </tr>
         <tr>
             <td valign="top">Фотоальбом:</td>
             <td valign="top"><select name="album_id" size="8" id="parent_id" style="width:250px">
                     <?php  //FIND MENU ROOT
                     $rootid = $inDB->get_field('cms_photo_albums', 'parent_id=0', 'id');
                     ?>
                     <option value="<?php echo $rootid?>" <?php if (@$mod['album_id']==$rootid || !isset($mod['album_id'])) { echo 'selected'; }?>>-- Корневой альбом --</option>
                     <?php if (isset($mod['album_id'])){
                         echo $inCore->getListItemsNS('cms_photo_albums', $mod['album_id']);
                     } else {
                         echo $inCore->getListItemsNS('cms_photo_albums');
                     }
                     ?>
             </select></td>
         </tr>
         <tr>
             <td>Публиковать фотографии?</td>
             <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
                 Да
                 <label>
                     <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
             Нет</label></td>
         </tr>
         <tr>
             <td>Показывать даты? </td>
             <td><input name="showdate" type="radio" value="1" checked="checked" <?php if (@$mod['showdate']) { echo 'checked="checked"'; } ?> />
                 Да
                 <label>
                     <input name="showdate" type="radio" value="0"  <?php if (@!$mod['showdate']) { echo 'checked="checked"'; } ?> />
             Нет</label></td>
         </tr>
         <tr>
         <td>Cохранить оригиналы: </td>
         <td><input name="saveorig" type="radio" value="1" checked="checked" />
             Да
             <input name="saveorig" type="radio" value="0">
             Нет
         </label></td>
         </tr>
         <tr>
             <td valign="top">Теги фотографий: <br />
             <span class="hinttext">Ключевые слова, через запятую</span></td>
             <td valign="top"><input name="tags" type="text" id="tags" size="45" /></td>
         </tr>

         <tr>
             <td valign="top">Файлы фотографий: <br />
             <span class="hinttext">Выбирайте все фото по очереди </span></td>
             <td valign="top">
                 <input type="file" class="multi" name="upfile[]" id="upfile" accept="jpeg,gif,png,jpg,bmp"/>
                 <div id="loadergif" style="display:none;float:left;margin:6px"><img src="/images/ajax-loader.gif" border="0"/></div>
             </td>
         </tr>
         </table>
         <p>
             <input name="upload_btn" type="button" id="upload_btn" value="Загрузить фото" onclick="startUpload()"/>
             <input name="back3" type="button" id="cancel_btn" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
             <input name="opt" type="hidden" id="opt" value="submit_photo_multi" />
         </p>
         </form>
	 <?php	
	}
	
//=================================================================================================//
//=================================================================================================//
		
?>