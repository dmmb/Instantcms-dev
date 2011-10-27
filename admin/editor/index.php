<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/
Error_Reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	session_start();

	define("VALID_CMS", 1);
	define("VALID_CMS_ADMIN", 1);
	define("VALID_CMS_EDITOR", 1);

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

	require("../../core/cms.php");
	require("../includes/cp.php");
	require("../includes/editor.php");

	require("../../includes/config.inc.php");
	require("../../includes/database.inc.php");
	require("../../includes/tools.inc.php");

    $inCore = cmsCore::getInstance();

    $inCore->loadClass('page');         //страница
    $inCore->loadClass('plugin');       //плагины
    $inCore->loadClass('user');       //пользователь
    $inCore->loadClass('actions');      //лента активности

    $inPage     = cmsPage::getInstance();
    $inConf     = cmsConfig::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();

	$inUser->update();
    if (!$inUser->id) { cmsCore::error404(); }

	$editor_category_id = editorCheckAuth(); //CHECK AUTHENTICATION

    $GLOBALS['ed_page_title']   = '';
	$GLOBALS['ed_menu']         = array();
	$GLOBALS['ed_page_head']    = array();
	$GLOBALS['ed_page_body']    = '';

	if (isset($_REQUEST['do'])){ $do = $_REQUEST['do']; } else { $do = 'view'; }
	if (isset($_REQUEST['id'])){ $id = intval($_REQUEST['id']); }

	ob_start();
/*********************************************************************************************/
if ($do=='view'){

	//MENU
	$GLOBALS['ed_menu'][0]['link'] = 'index.php';
	$GLOBALS['ed_menu'][0]['title'] = 'Начало';

	$GLOBALS['ed_menu'][1]['link'] = '?do=newcat';
	$GLOBALS['ed_menu'][1]['title'] = 'Новый подраздел';

	$GLOBALS['ed_menu'][2]['link'] = '?do=newdoc';
	$GLOBALS['ed_menu'][2]['title'] = 'Новая статья';

	$GLOBALS['ed_menu'][3]['link'] = '/';
	$GLOBALS['ed_menu'][3]['title'] = 'Перейти на сайт';

	if (!isset($id)){
		$cat = editorGetCat() or die('ACCESS DENIED');
		$GLOBALS['ed_page_title'] = $cat['title'];
		$id = $cat['id'];
		$root = true;
	} else {
		$sql = "SELECT * FROM cms_category WHERE id = '$id'	ORDER BY title ASC";
		$result = dbQuery($sql);
		if (mysql_num_rows($result)==0) { die('Category not found.'); }
		$cat = mysql_fetch_assoc($result);
		$root = false;
	}

	$perpage = 20;
	$page	 = $inCore->request('page',	'int', 1);

	echo '<div class="title">'.$cat['title'].'</div>';

	echo '<div class="desc">';
		if (!$root){
			echo '<a href="index.php" class="toollink">Назад</a>';
		}
	echo '</div>';

	echo '<div class="content">';

	$row = 0;

	//LIST OF SUBCATEGORIES
	if ($page==1){
		$sql = "SELECT cat.*
				FROM cms_category cat
				WHERE (cat.parent_id = $id AND cat.id > 0)
				";

		$result = dbQuery($sql) ;

		if (mysql_num_rows($result)){
			echo '<table class="categorylist" cellpadding="5" cellspacing="0" width="100%" border="0">';
			while($subcat = mysql_fetch_assoc($result)){
				$row++;
				$style = ($row % 2) ? 'background-color:#EBEBEB' : 'background-color:#FFFFFF';
				echo '<tr>';
					echo '<td style="'.$style.'" width="20" valign="top"><img src="/images/markers/folder.png" border="0" /></td>';
					echo '<td style="'.$style.'" width="">';
						echo '<a href="?do=view&id='.$subcat['id'].'">'.$subcat['title'].'</a>';
					echo '</td>';

					if ($subcat['published']){
						echo '<td style="'.$style.'" width="16" valign="top"><img src="../images/actions/on.gif" alt="Раздел опубликован"/></td>';
						echo '<td style="'.$style.'" width="60" align="center" valign="top"><a href="?do=hidecat&id='.$subcat['id'].'">Скрыть</a></td>';
					} else {
						echo '<td style="'.$style.'" width="16" valign="top"><img src="../images/actions/off.gif" alt="Раздел скрыт"/></td>';
						echo '<td style="'.$style.'" width="60" align="center" valign="top"><a href="?do=showcat&id='.$subcat['id'].'">Показать</a></td>';
					}

				echo '</tr>';
			}
			echo '</table>';
		}
	}

	//CURRENT CATEGORY CONTENT
	$sql = "SELECT *,
                   DATE_FORMAT(pubdate, '%d-%m-%Y') as fpubdate,
                   DATE_FORMAT(pubdate, '%H:%i') as fpubtime
			FROM cms_content
			WHERE category_id = $id AND is_arhive = 0
			ORDER BY ".$cat['orderby']." ".$cat['orderto']."
			LIMIT ".(($page-1)*$perpage).", $perpage";

	$result = dbQuery($sql) or die(mysql_error()."\n".$sql);

	if (mysql_num_rows($result)){

        $inCore->loadModel('content');
        $model = new cms_model_content();

		echo '<table class="contentlist" cellpadding="5" cellspacing="0" border="0" width="100%">';
		while($con = mysql_fetch_assoc($result)){
			$row++;
			$style = ($row % 2) ? 'background-color:#EBEBEB' : 'background-color:#FFFFFF';
			if($inCore->checkUserAccess('material', $con['id'])){
				echo '<tr>';
					echo '<td style="'.$style.'" width="20" valign="top"><img src="/images/markers/article.png" border="0" /></td>';
					echo '<td style="'.$style.'" width="" valign="top">';
						echo '<a href="?do=editdoc&id='.$con['id'].'">'.$con['title'].'</a>';
					echo '</td>';
					echo '<td style="'.$style.'" width="16" valign="top"><img src="/images/icons/comments.gif" alt="Комментарии" border="0"/></td>';
                    echo '<td style="'.$style.'" width="25" valign="top"><a href="'.$model->getArticleURL(0, $con['seolink']).'#c" title="Комментарии">'.$inCore->getCommentsCount('article', $con['id']).'</a></td>';

					echo '<td style="'.$style.'" width="16" valign="top"><img src="/images/icons/date.gif" alt="Дата публикации" /></td>';
					echo '<td style="'.$style.'" width="140" align="center" valign="top">'.$con['fpubdate'].' [ <span style="color:gray">'.$con['fpubtime'].'</span> ]</td>';

					if ($con['published']){
						echo '<td style="'.$style.'" width="16" valign="top"><img src="../images/actions/on.gif" alt="Статья опубликована"/></td>';
						echo '<td style="'.$style.'" width="60" align="center" valign="top"><a href="?do=hidedoc&id='.$con['id'].'">Скрыть</a></td>';
					} else {
						echo '<td style="'.$style.'" width="16" valign="top"><img src="../images/actions/off.gif" alt="Статья скрыта"/></td>';
						echo '<td style="'.$style.'" width="60" align="center" valign="top"><a href="?do=showdoc&id='.$con['id'].'">Показать</a></td>';
					}

					echo '<td style="'.$style.'" width="16" valign="top"><img src="../images/actions/delete.gif" /></td>';
					echo '<td style="'.$style.'" width="60" align="center" valign="top"><a href="?do=deletedoc&id='.$con['id'].'">Удалить</a></td>';

				echo '</tr>';
			}
		}
		echo '</table>';
		echo pageBar($id, $page, $perpage);
	}

	echo '</div>';
}
/*********************************************************************************************/
if ($do=='newcat'){

	//MENU
	$GLOBALS['ed_menu'][0]['link'] = 'index.php';
	$GLOBALS['ed_menu'][0]['title'] = 'Отмена';

	if ($do=='newcat'){
		$cat = editorGetCat() or die('ACCESS DENIED');
		$GLOBALS['ed_page_title'] = 'Новый подраздел';
		echo '<div class="title">Новый подраздел</div>';
		echo '<div class="desc"></div>';
	} else {
		$sql = "SELECT * FROM cms_category WHERE id = $id LIMIT 1";
		$result = dbQuery($sql) ;
		if (mysql_num_rows($result)){
			$mod = mysql_fetch_assoc($result);
			$GLOBALS['ed_page_title'] = 'Редактировать раздел';
			echo '<div class="title">Раздел: '.$mod['title'].'</div>';
			echo '<div class="desc"></div>';
		} else { die('Категория не найдена'); }
	}

	echo '<div class="content">';

	ob_start(); ?>
		<form id="addform" name="addform" method="post" action="index.php">
		  <table width="600" border="0" cellspacing="5" class="proptable">
			<tr>
			  <td width="222" valign="top">Название раздела:<br />
				  <span class="hinttext">Отображается на сайте</span></td>
			  <td width="319" valign="top"><input name="title" type="text" id="title" size="45" value="<?php echo @$mod['title'];?>" /></td>
			</tr>
			<tr>
			  <td valign="top">Сортировка статей:<br />
				  <span class="hinttext">Сортировка статей при просмотре раздела на сайте </span></td>
			  <td valign="top"><select name="orderby" id="orderby">
				<option value="pubdate" <?php if(@$mod['orderby']=='pubdate') { echo 'selected'; } ?>>По дате</option>
				<option value="title" <?php if(@$mod['orderby']=='title') { echo 'selected'; } ?>>По заголовку</option>
				<option value="ordering" <?php if(@$mod['orderby']=='ordering') { echo 'selected'; } ?>>По порядку</option>
				<option value="hits" <?php if(@$mod['orderby']=='hits') { echo 'selected'; } ?>>По просмотрам</option>
			  </select>
				  <select name="orderto" id="orderto">
					<option value="ASC" <?php if(@$mod['orderto']=='ASC') { echo 'selected'; } ?>>По возрастанию</option>
					<option value="DESC" <?php if(@$mod['orderto']=='DESC') { echo 'selected'; } ?>>По убыванию</option>
				  </select>			  </td>
			</tr>
			<tr>
			  <td valign="top">Публиковать раздел?</td>
			  <td valign="top"><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
				Да
				<label>
				  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
				  Нет</label></td>
			</tr>
			<tr>
			  <td valign="top">Показывать даты:<br />
				  <span class="hinttext">При просмотре раздела на сайте</span></td>
			  <td valign="top"><input name="showdate" type="radio" value="1" checked="checked" <?php if (@$mod['showdate']) { echo 'checked="checked"'; } ?> />
				Да
				<label>
				  <input name="showdate" type="radio" value="0"  <?php if (@!$mod['showdate']) { echo 'checked="checked"'; } ?> />
				  Нет</label></td>
			</tr>
			<tr>
			  <td valign="top">Показывать число комментариев: <br />
				  <span class="hinttext">При просмотре раздела на сайте</span></td>
			  <td valign="top"><input name="showcomm" type="radio" value="1" checked="checked" <?php if (@$mod['showcomm']) { echo 'checked="checked"'; } ?> />
				Да
				<label>
				  <input name="showcomm" type="radio" value="0"  <?php if (@!$mod['showcomm']) { echo 'checked="checked"'; } ?> />
				  Нет</label></td>
			</tr>
		  </table>
		  <table width="100%" border="0">
			<tr>
			  <td>
                <h3>Описание раздела</h3>
                <?php $inCore->insertEditor('description', $mod['description'], '400', '100%'); ?>
			  </td>
			</tr>
		  </table>
		  <p>
			<label>
			  <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='newcat') { echo 'value="Создать раздел"'; } else { echo 'value="Сохранить раздел"'; } ?> />
			</label>
			<label><span style="margin-top:15px">
			  <input name="back" type="button" id="back" value="Отмена" onclick="window.history.back();"/>
			  </span></label>
			<input name="do" type="hidden" id="do" <?php if ($do=='newcat') { echo 'value="submitcat"'; } else { echo 'value="updatecat"'; } ?> />
			<?php
					if ($do=='editcat'){
					 echo '<input name="id" type="hidden" value="'.$mod['id'].'" />';
					}
					if ($do=='newcat'){
					 echo '<input name="parent" type="hidden" value="'.$cat['id'].'" />';
					}
				  ?>
		  </p>
		</form>
	<?php echo ob_get_clean();

	echo '</div>';

}
/*********************************************************************************************/
if ($do == 'submitcat'){

    $category['id']				= (int)$_REQUEST['id'];
    $category['title']			= $inCore->request('title', 'str');
    $category['parent_id']		= $inCore->request('parent', 'int');
    $category['description']	= $inCore->request('description', 'html');
    $category['description']	= $inDB->escape_string($category['description']);
	$category['description']    = $inCore->badTagClear($category['description']);
    $category['published'] 		= (int)$_REQUEST['published'];
    $category['showdate'] 		= (int)$_REQUEST['showdate'];
    $category['showcomm'] 		= (int)$_REQUEST['showcomm'];
    $category['orderby'] 		= $inCore->request('orderby', 'str');
    $category['orderto']		= $inCore->request('orderto', 'str');
    $category['modgrp_id'] 		= 0;
    $category['maxcols'] 		= 1;
    $category['showtags'] 		= 1;
    $category['showrss'] 		= 1;
    $category['showdesc'] 		= 1;
    $category['is_public'] 		= 0;

    $ns = $inCore->nestedSetsInit('cms_category');
    $category['id'] = $ns->AddNode($category['parent_id']);

    $inCore->loadModel('content');
    $model = new cms_model_content();

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
                    seolink='$seolink'
                WHERE id = {$category['id']}
                LIMIT 1";

        dbQuery($sql) ;
    }

	header('location:index.php');
}
/*********************************************************************************************/
if ($do=='newdoc' || $do=='editdoc'){

	//MENU
	$GLOBALS['ed_menu'][0]['link'] = 'javascript:window.history.go(-1)';
	$GLOBALS['ed_menu'][0]['title'] = 'Отмена';

	$cat = editorGetCat() or die('ACCESS DENIED');

	if ($do=='newdoc'){
		$GLOBALS['ed_page_title'] = 'Новая статья';
		echo '<div class="title">Новая статья</div>';
		echo '<div class="desc"></div>';
	} else {
		$sql = "SELECT * FROM cms_content WHERE id = $id LIMIT 1";
		$result = dbQuery($sql) ;
		if (mysql_num_rows($result)){
			$mod = mysql_fetch_assoc($result);
			$GLOBALS['ed_page_title'] = 'Редактировать документ';
			echo '<div class="title">Статья: '.$mod['title'].'</div>';
			echo '<div class="desc"></div>';
		} else { die('Статья не найдена'); }
	}

	echo '<div class="content">';

	ob_start(); ?>
		<form id="addform" name="addform" method="post" action="index.php">

		  <table width="605" border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td><table width="605" border="0" cellspacing="5" class="proptable">
					<tr>
					  <td width="236" valign="top">Заголовок статьи:<br />
						  <span class="hinttext">Отображается на сайте</span></td>
					  <td width="348" valign="top"><input name="title" type="text" id="title2" size="45" value="<?php echo @$mod['title'];?>" /></td>
					</tr>
					<tr>
					  <td valign="top">Раздел:<br />
					  <span class="hinttext">Куда поместить статью</span></td>
					  <td valign="top">
                        <select name="category_id" size="8" id="category_id" style="width:250px">
                              <option value="<?php echo $editor_category_id; ?>" <?php if (@$mod['category_id']==$editor_category_id || !isset($mod['category_id'])) { echo 'selected'; }?>>-- Корневой раздел --</option>
                                <?php
                                    if (isset($mod['category_id'])){
                                        echo $inCore->getListItemsNS('cms_category', $mod['category_id'], '', '', $editor_category_id);
                                    } else {
                                        echo $inCore->getListItemsNS('cms_category', 0, '', '', $editor_category_id);
                                    }
                                ?>
                        </select>
                      </td>
					</tr>
					<tr>
					  <td valign="top">Публиковать статью?</td>
					  <td valign="top"><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
						Да
						<label>
					<input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
						  Нет</label></td>
					</tr>
					<tr>
					  <td valign="top">Показывать дату? <br />
						  <span class="hinttext">Отображается на сайте</span></td>
					  <td valign="top"><input name="showdate" type="radio" value="1" checked="checked" <?php if (@$mod['showdate']) { echo 'checked="checked"'; } ?> />
						Да
						<label>
					<input name="showdate" type="radio" value="0"  <?php if (@!$mod['showdate']) { echo 'checked="checked"'; } ?> />
						  Нет</label></td>
					</tr>
					<tr>
					  <td valign="top">Разрешить комментарии: <br />
						  <span class="hinttext">Отображается на сайте</span></td>
					  <td valign="top"><input name="comments" type="radio" value="1" checked="checked" <?php if (@$mod['comments']) { echo 'checked="checked"'; } ?> />
						Да
						<label>
					<input name="comments" type="radio" value="0"  <?php if (@!$mod['comments']) { echo 'checked="checked"'; } ?> />
						  Нет</label></td>
					</tr>
				  </table>
				</td>
			</tr>
		  </table>
		  <table width="100%" border="0">
			<tr>
                <td>
                    <p><b>Анонс статьи</b> (не обязательно):</p>
                    <?php $inCore->insertEditor('description', $mod['description'], '250', '100%'); ?>
                    <p><b>Текст статьи:</b></p>
                    <?php $inCore->insertEditor('content', $mod['content'], '450', '100%'); ?>
                </td>
			</tr>
		  </table>
		  <p style="margin-left:2px">
			<input name="add_mod" type="submit" id="add_mod" <?php if ($do=='newdoc') { echo 'value="Создать статью"'; } else { echo 'value="Сохранить статью"'; } ?> />
			<span style="margin-top:15px">
			<input name="back2" type="button" id="back2" value="Отмена" onclick="window.history.back();"/>
			</span>
			<input name="do" type="hidden" id="do" <?php if ($do=='newdoc') { echo 'value="submitdoc"'; } else { echo 'value="updatedoc"'; } ?> />
			<?php
				if ($do=='editdoc'){
				 echo '<input name="id" type="hidden" value="'.$mod['id'].'" />';
				}
			  ?>
		  </p>
	</form>
	<?php echo ob_get_clean();

	echo '</div>';

}
/*********************************************************************************************/
if ($do == 'updatedoc'){
	if(isset($_REQUEST['id'])) {

		$doc['id']              = (int)$_REQUEST['id'];
		$doc['category_id']     = $inCore->request('category_id', 'int', 0);
		$doc['title']           = $inCore->request('title', 'str', 'Статья без названия');
		$doc['description']     = $inCore->request('description', 'html', '');
		$doc['content']         = $inCore->request('content', 'html', '');
		$doc['description']	    = $inDB->escape_string($doc['description']);
		$doc['description']     = $inCore->badTagClear($doc['description']);
		$doc['content']	        = $inDB->escape_string($doc['content']);
		$doc['content']         = $inCore->badTagClear($doc['content']);
		$doc['published']       = $inCore->request('published', 'int', 0);
		$doc['showdate']        = $inCore->request('showdate', 'int', 1);
		$doc['comments']        = $inCore->request('comments', 'int', 0);

        $inCore->loadModel('content');
        $model = new cms_model_content();

        $seolink = $model->getSeoLink($doc);

		$sql = "UPDATE cms_content
				SET category_id = {$doc['category_id']},
					title='{$doc['title']}',
					description='{$doc['description']}',
					content='{$doc['content']}',
					published={$doc['published']},
					showdate={$doc['showdate']},
					comments={$doc['comments']},
                    seolink='{$seolink}'
				WHERE id = {$doc['id']}
				LIMIT 1";

		dbQuery($sql);

		header('location:index.php?do=view&id='.$doc['category_id']);
	}
}
/*********************************************************************************************/
if ($do == 'submitdoc'){

    $doc['category_id']     = $inCore->request('category_id', 'int', 0);
    $doc['title']           = $inCore->request('title', 'str', 'Статья без названия');
    $doc['description']     = $inCore->request('description', 'html', '');
    $doc['content']         = $inCore->request('content', 'html', '');
	$doc['description']	    = $inDB->escape_string($doc['description']);
	$doc['description']     = $inCore->badTagClear($doc['description']);
	$doc['content']	        = $inDB->escape_string($doc['content']);
	$doc['content']         = $inCore->badTagClear($doc['content']);
    $doc['published']       = $inCore->request('published', 'int', 0);
    $doc['showdate']        = $inCore->request('showdate', 'int', 1);
    $doc['comments']        = $inCore->request('comments', 'int', 0);
    $doc['showlatest']      = 0;
    $doc['enddate']         = '';
    $doc['is_end']          = 0;
    $doc['user_id']         = $inUser->id;

    $doc['meta_desc']       = strtolower($doc['title']);
    $doc['meta_keys']       = $inCore->getKeywords($inCore->strClear($doc['content']));

	$sql = "INSERT INTO cms_content (category_id, user_id, pubdate, enddate, is_end, title, description, content, published, hits, meta_desc, meta_keys, showtitle, showdate, showlatest, ordering, comments, seolink, canrate)
			VALUES ({$doc['category_id']}, '{$doc['user_id']}', NOW(), '{$doc['enddate']}', {$doc['is_end']},
                    '{$doc['title']}', '{$doc['description']}', '{$doc['content']}', {$doc['published']}, 0,
                    '{$doc['meta_desc']}', '{$doc['meta_keys']}', '1', '{$doc['showdate']}', '{$doc['showlatest']}',
                    LAST_INSERT_ID()+1, {$doc['comments']}, '', 1)";
	dbQuery($sql) or die(mysql_error().'<pre>'.$sql.'</pre>');

    $doc['id'] = $inDB->get_last_id('cms_content');

    if ($doc['id']){
        $inCore->loadModel('content');
        $model = new cms_model_content();
        $seolink = $model->getSeoLink($doc);
        dbQuery("UPDATE cms_content SET seolink='{$seolink}' WHERE id={$doc['id']}");
		if ($doc['published']){
			$doc['category']    = $inDB->get_fields('cms_category', "id='{$doc['category_id']}'", 'title, seolink');
			//регистрируем событие
			cmsActions::log('add_article', array(
				  'object' => $doc['title'],
				  'object_url' =>  "/{$seolink}.html",
				  'object_id' =>  $doc['id'],
				  'target' => $doc['category']['title'],
				  'target_url' => "/{$doc['category']['seolink']}",
				  'target_id' =>  $doc['category_id'],
				  'description' => ''
			));
		}
    }

	header('location:index.php?do=view&id='.$doc['category_id']);
}
/*********************************************************************************************/
if ($do == 'showcat'){
	if (isset($id)){
		dbQuery("UPDATE cms_category SET published = 1 WHERE id = $id");
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}
if ($do == 'hidecat'){
	if (isset($id)){
		dbQuery("UPDATE cms_category SET published = 0 WHERE id = $id");
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}
if ($do == 'showdoc'){
	if (isset($id)){
		dbQuery("UPDATE cms_content SET published = 1 WHERE id = $id");
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}
if ($do == 'hidedoc'){
	if (isset($id)){
		dbQuery("UPDATE cms_content SET published = 0 WHERE id = $id");
	}
	header('location:'.$_SERVER['HTTP_REFERER']);
}
/*********************************************************************************************/
if ($do == 'deletedoc'){
	if (isset($id)){
		if (isset($_GET['confirm'])){

            $inCore->loadModel('content');
            $model = new cms_model_content();

            $model->deleteArticle($id);

			header('location:index.php');

		} else {
			$result = dbQuery("SELECT title FROM cms_content WHERE id = $id");
			$data = mysql_fetch_assoc($result);

			//MENU
			$GLOBALS['ed_menu'][0]['link'] = 'javascript:window.history.go(-1)';
			$GLOBALS['ed_menu'][0]['title'] = 'Отмена';

			$GLOBALS['ed_page_title'] = 'Удаление статьи';

			echo '<div class="title">Удаление статьи</div>';
			echo '<p style="margin-bottom:30px">Вы действительно хотите удалить статью "'.$data['title'].'"?</p>';
			echo '<a href="?do=deletedoc&id='.$id.'&confirm=yes" class="btnlink">ДА</a><a href="javascript:window.history.go(-1)" class="btnlink">НЕТ</a>';
		}
	}
}
/*********************************************************************************************/
	$GLOBALS['ed_page_body'] = ob_get_clean();
	include("template.php");

?>
