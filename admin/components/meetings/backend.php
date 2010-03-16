<?php

    //Запрет прямого вызова этого файла из браузера
    if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

    //Добавляем звено в глубиномер
	cpAddPathway('Встречи', '?view=components&do=config&id='.$_REQUEST['id']);

    //Выводим заголовок
    echo '<h3>Встречи</h3>';

    //Получаем текущее действие, по-умолчанию это list_cats, т.е. "показать все категории"
    $opt = $inCore->request('opt', 'str', 'list_cats');

    //
    // Формируем управляющее меню компонента c кнопками. 
    // Каждая кнопка описывается как элемент массива, с указанием иконки, заголовка и ссылки.
    // Иконки должны лежать в папке /admin/images/toolmenu.
    // В ссылках имеет смысл менять только последний параметр, opt=...
    // в этом параметре передается текущее действие (добавить категорию, добавить запись и тд)
    //
    // У админки нашего компонента будут такие действия:
    //   - Добавить категорию (показать форму) (add_cat) *
    //   - Добавить мероприятие (показать форму) (add_item) *
    //   - Создать категорию в БД (submit_cat)
    //   - Создать мероприятие в БД (submit_item)
    //   - Показать все категории (list_cats) *
    //   - Показать все мероприятия (list_items) *
    //   - Настройки (показать форму) (config) *
    //   - Сохранить настройки (saveconfig)
    //   - Редактировать категорию (показать форму) (edit_cat) **
    //   - Редактировать мероприятие (показать форму) (edit_item) **
    //   - Обновить категорию (update_cat)
    //   - Обновить мероприятие (update_item)
    //   - Удалить категорию (delete_cat)
    //   - Удалить мероприятие (delete_item)
    //   - Показать/скрыть категорию (show_cat/hide_cat) **
    //   - Показать/скрыть мероприятие (show_item/hide_item) **
    //
    // * Для этих 5-ти действий будут кнопки на панели компонента,
    // ** Эти 4 действия будут вызываться из списков категорий и мероприятий
    //

	$toolmenu = array();

    if($opt == 'add_cat' || $opt == 'add_item' || $opt == 'edit_cat' || $opt == 'edit_item' || $opt == 'config'){

        // Для действий "добавить/редактировать/настройки" показываем
        // только кнопки "сохранить" и "отменить"

        $toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

	} else {

        // Для остальных действий показываем полный набор кнопок

        $toolmenu[0]['icon'] = 'newfolder.gif';
        $toolmenu[0]['title'] = 'Новая категория';
        $toolmenu[0]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat';

        $toolmenu[2]['icon'] = 'newquest.gif';
        $toolmenu[2]['title'] = 'Новое мероприятие';
        $toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item';

        $toolmenu[1]['icon'] = 'folders.gif';
        $toolmenu[1]['title'] = 'Категории мероприятий';
        $toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats';

        $toolmenu[3]['icon'] = 'listquest.gif';
        $toolmenu[3]['title'] = 'Все мероприятия';
        $toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items';

        $toolmenu[4]['icon'] = 'config.gif';
        $toolmenu[4]['title'] = 'Настройки';
        $toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';

    }

    // Выводим меню на экран
	cpToolMenu($toolmenu);

	// Загружаем текущие настройки компонента
	$cfg = $inCore->loadComponentConfig('meetings');

// ============================================================================================= //
// ============================================================================================= //

    // Действие "сохранить настройки"
	if($opt=='saveconfig'){

        // Получаем настройки компонента, переданные из формы настроек
        // упаковываем их в массив и говорим ядру сохранить
		$cfg                = array();
		$cfg['showtime']    = $inCore->request('showtime', 'int', 1);

        // Функция сохранения настроек в ядре требует указать
        // имя компонента и массив c настройками
		$inCore->saveComponentConfig('meetings', $cfg);

	}

// ============================================================================================= //
// ============================================================================================= //

    // Действие "показывать мероприятие"
    // здесь мы просто поставили в нужные места название нашей таблицы с мероприятиями
	if ($opt == 'show_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_meet_meetings', $_REQUEST['item_id']);  }
		} else {
			dbShowList('cms_meet_meetings', $_REQUEST['item']);
		}			
		echo '1'; exit;
	}

    // Действие "скрыть мероприятие"
    // здесь мы просто поставили в нужные места название нашей таблицы с мероприятиями
	if ($opt == 'hide_item'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_meet_meetings', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_meet_meetings', $_REQUEST['item']);
		}			
		echo '1'; exit;
	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "создать мероприятие"
    // Будет вызываться после добавления нового мероприятия
    //
	if ($opt == 'submit_item'){	

        // Получаем значения полей формы и складываем их в массив

        $item['category_id']    = $inCore->request('category_id', 'int');
		$item['title']          = $inCore->request('title', 'int');
        $item['pubdate']        = $inCore->request('pubdate', 'str');
		$item['published']      = $inCore->request('published', 'int');

        // Загружаем модель нашего компонента...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...и передаем ей полученные данные о мероприятии
        // модель создаст запись мероприятия в базе

        $model->addMeeting($item);

        // перенаправляем пользователя к списку мероприятий

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

	}	  

// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "обновить мероприятие"
    // Будет вызываться после редактирования мероприятия
    //
	if ($opt == 'update_item'){

        // Получаем ID редактируемого мероприятия

        $id = $inCore->request('id', 'int');

        // Получаем значения полей формы и складываем их в массив

        $item['category_id']    = $inCore->request('category_id', 'int');
		$item['title']          = $inCore->request('title', 'int');
        $item['pubdate']        = $inCore->request('pubdate', 'str');
		$item['published']      = $inCore->request('published', 'int');

        // Загружаем модель нашего компонента...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...и передаем ей ID и новые данные о мероприятии
        // модель обновит мероприятие в базе

        $model->updateMeeting($id, $item);

        // перенаправляем пользователя к списку мероприятий

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "удалить мероприятие"
    //
	if($opt == 'delete_item'){

        // Получаем ID удаляемого мероприятия

        $id = $inCore->request('id', 'int');

        // Загружаем модель нашего компонента...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...и просим ее удалить мероприятие с указанным ID

        $model->deleteMeeting($id);

        // перенаправляем пользователя к списку мероприятий

		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');

	}

// ============================================================================================= //
// ============================================================================================= //

    // Действие "показывать категорию"
    // здесь мы просто поставили в нужные места название нашей таблицы с категориями
	if ($opt == 'show_cat'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbShow('cms_meet_category', $_REQUEST['item_id']);  }
		} else {
			dbShowList('cms_meet_category', $_REQUEST['item']);
		}
		echo '1'; exit;
	}

    // Действие "скрыть категорию"
    // здесь мы просто поставили в нужные места название нашей таблицы с категориями
	if ($opt == 'hide_cat'){
		if (!isset($_REQUEST['item'])){
			if (isset($_REQUEST['item_id'])){ dbHide('cms_meet_category', $_REQUEST['item_id']);  }
		} else {
			dbHideList('cms_meet_category', $_REQUEST['item']);
		}
		echo '1'; exit;
	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "создать категорию"
    // Будет вызываться после добавления новой категории
    //
	if ($opt == 'submit_cat'){	

        // Получаем значения полей формы и складываем их в массив

		$item['title']          = $inCore->request('title', 'int');
		$item['published']      = $inCore->request('published', 'int');

        // Загружаем модель нашего компонента...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...и передаем ей полученные данные о мероприятии
        // модель создаст запись мероприятия в базе

        $model->addCategory($item);

        // перенаправляем пользователя к списку категорий

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "обновить категорию"
    // Будет вызываться после редактирования категории
    //
	if ($opt == 'update_cat'){

        // Получаем ID редактируемой категории

        $id = $inCore->request('id', 'int');

        // Получаем значения полей формы и складываем их в массив

		$item['title']          = $inCore->request('title', 'int');
		$item['published']      = $inCore->request('published', 'int');

        // Загружаем модель нашего компонента...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...и передаем ей ID и новые данные о категории
        // модель обновит запись категории в базе

        $model->updateCategory($id, $item);

        // перенаправляем пользователя к списку категорий

        $inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "удалить категорию"
    //
	if($opt == 'delete_cat'){

        // Получаем ID удаляемой категории

        $id = $inCore->request('id', 'int');

        // Загружаем модель нашего компонента...

        $inCore->loadModel('meetings');
        $model = new cms_model_meetings();

        // ...и просим ее удалить категорию с указанным ID

        $model->deleteCategory($id);

        // перенаправляем пользователя к списку категорий

		$inCore->redirect('?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

	}


// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "Список категорий"
    //
	if ($opt == 'list_cats'){

        // запись глубиномера
		cpAddPathway('Категории вопросов', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');

        // заголовок
		echo '<h3>Категории вопросов</h3>';

        //
        // Вывод списка (таблицы) категорий осуществляется автоматически системой
        // Нам нужно лишь указать какие поля таблицы БД (столбцы) мы хотим выводить
        // и какие действия (редактировать/удалить) должны быть у каждой записи (строки)
        //

        // Создаем массив со списком столбцов и заполняем его
		$fields = array();

        // Столбец ID, соответствует полю id в таблице
		$fields[0]['title']     = 'id';
        $fields[0]['field']     = 'id';
        $fields[0]['width']     = '30'; //ширина столбца в пикселях

        // Столбец "Название", соответствует полю title в таблице
        // для этого столбца так же добавляем фильтр, длиной в 20 символов
        // и говорим что названия должны сопровождаться ссылками, ведущими к
        // редактированию записей
		$fields[1]['title']     = 'Название';
        $fields[1]['field']     = 'title';
        $fields[1]['width']     = '';
		$fields[1]['filter']    = 20;
		$fields[1]['link']      = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

        // Столбец "Показ", соответствует полю published в таблице
        // для поля published система автоматически будет показывать зеленую(1) или красную(0) галочку
        // щелчки по этим галочкам будут вызывать действия hide_cat и show_cat соответственно
		$fields[3]['title']     = 'Показ';
        $fields[3]['field']     = 'published';
        $fields[3]['width']     = '100';
		$fields[3]['do']        = 'opt';
        $fields[3]['do_suffix'] = '_cat';

        //
		// Создаем массив со списком действий для каждой строки таблицы
        // (т.е. в нашем случае для каждой категории)
        // в результате в каждой строке будут иконки карандаша и красного крестика
        // ссылки с этих иконок будут вызывать действия edit_cat и delete_cat соответственно
        //
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить категорию вопросов?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_cat&item_id=%id%';

        //
		// Выводим таблицу на экран
        // в качестве параметров передаем название таблицы в БД, массив с полями и массив с действиями для строк
        //
		cpListTable('cms_faq_cats', $fields, $actions);

	}

// ============================================================================================= //
// ============================================================================================= //

    //
    // Действие "список мероприятий"
    //
	if ($opt == 'list_items'){

        //Здесь все аналогично выводу списка категорий

		cpAddPathway('Вопросы', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
		echo '<h3>Вопросы</h3>';
		
		$fields = array();

		$fields[0]['title'] = 'id';			
        $fields[0]['field'] = 'id';
        $fields[0]['width'] = '30';

		$fields[1]['title']     = 'Мероприятие';
        $fields[1]['field']     = 'title';
        $fields[1]['width']     = '';
		$fields[1]['link']      = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';
		$fields[1]['filter']    = 15;
		$fields[1]['maxlen']    = 80; // Если заголовок мероприятия будет длиннее 80 символов, он обрежется до этой длины
		
        //
        // Чтобы система выводила в столбце "категория" не номер, а название
        // мы указываем параметр 'prc', в котором передаем название функции cpMeetCatById
        // эта функция определена в начале файла и будет возвращать название категории по ее номеру
        //
		$fields[2]['title']         = 'Категория';
        $fields[2]['field']         = 'category_id';
        $fields[2]['width']         = '300';
		$fields[2]['prc']           = 'cpMeetCatById';
        $fields[2]['filter']        = 1;
        $fields[2]['filterlist']    = cpGetList('cms_meet_category'); //Это поле нужно, чтобы сделать выпадающий список в фильтре по категориям

		$fields[3]['title']     = 'Показ';
        $fields[3]['field']     = 'published';
        $fields[3]['width']     = '100';
		$fields[3]['do']        = 'opt';
        $fields[3]['do_suffix'] = '_item';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить вопрос?';
		$actions[1]['link']  = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=delete_item&item_id=%id%';
				
		//Print table
		cpListTable('cms_faq_quests', $fields, $actions, '', 'pubdate DESC');

	}

// ============================================================================================= //
// ============================================================================================= //

	if ($opt == 'add_item' || $opt == 'edit_item'){
		if ($opt=='add_item'){
		 echo '<h3>Добавить вопрос</h3>';
		 cpAddPathway('Добавить вопрос', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_item');
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
		
		
					 $sql = "SELECT *, DATE_FORMAT(pubdate, '%d.%m.%Y') as pubdate, DATE_FORMAT(answerdate, '%d.%m.%Y') as answerdate
					 		 FROM cms_faq_quests 
							 WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }

					 echo '<h3>Просмотр вопроса</h3>';
					 cpAddPathway('Вопросы', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_items');
					 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_item&item_id='.$id);
			}

		?>
<form action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>" method="post" enctype="multipart/form-data" name="addform" id="addform">
        <table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
          <tr>
            <td><strong>Категория вопроса:</strong></td>
            <td width="220"><select name="category_id" id="category_id" style="width:220px">
                <?php
                    if (isset($mod['category_id'])) {
                        echo $inCore->getListItems('cms_faq_cats', $mod['category_id']);
                    } else {
                        if (isset($_REQUEST['addto'])){
                            echo $inCore->getListItems('cms_faq_cats', $_REQUEST['addto']);
                        } else {
                            echo $inCore->getListItems('cms_faq_cats');
                        }
                    }
                ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Автор вопроса:</strong></td>
            <td><select name="user_id" id="user_id" style="width:220px">
              <?php
                  if (isset($mod['user_id'])) {
                        echo $inCore->getListItems('cms_users', $mod['user_id'], 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                  } else {
                        echo $inCore->getListItems('cms_users', $inUser->id, 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0');
                  }
              ?>
            </select></td>
          </tr>
          <tr>
            <td><strong>Публиковать вопрос?</strong></td>
            <td><input name="published" type="radio" value="1" checked="checked" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
              Да
              <label>
        <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
                Нет</label></td>
          </tr>
          <tr>
            <td valign="top"><strong>Дата подачи вопроса: </strong></td>
            <td valign="top"><input name="pubdate" type="text" style="width:190px" id="pubdate" <?php if(@!$mod['pubdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['pubdate'].'"'; } ?>/>
                <?php 
					//include javascript
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/jquery.js"></script>';
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/date_ru_win1251.js"></script>';
					$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/datepicker/datepicker.js"></script>';		
					$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/datepicker/datepicker.css" rel="stylesheet" type="text/css" />';
					if (@!$mod['pubdate']){					
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(new Date().asString()).trigger(\'change\');});</script>';
					} else {
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#pubdate\').datePicker({startDate:\'01/01/1996\'}).val(\''.$mod['pubdate'].'\').trigger(\'change\');});</script>';
					}
			  ?>
                <input type="hidden" name="oldpubdate" value="<?php echo @$mod['pubdate']?>"/>            </td>
          </tr>
          <tr>
            <td valign="top"><strong>Дата ответа: </strong></td>
            <td valign="top"><input name="answerdate" style="width:190px" type="text" id="answerdate" <?php if(@!$mod['answerdate']) { echo 'value="'.date('Y-m-d').'"'; } else { echo 'value="'.$mod['answerdate'].'"'; } ?>/>
                <?php 
					//include javascript
					if (@!$mod['answerdate']){					
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#answerdate\').datePicker({startDate:\'01/01/1996\'}).val(new Date().asString()).trigger(\'change\');});</script>';
					} else {
						$GLOBALS['cp_page_head'][] = '<script type="text/javascript">$(document).ready(function(){$(\'#answerdate\').datePicker({startDate:\'01/01/1996\'}).val(\''.$mod['answerdate'].'\').trigger(\'change\');});</script>';
					}
			  ?>
                <input type="hidden" name="oldanswerdate" value="<?php echo @$mod['answerdate']?>"/>
            </td>
          </tr>
  </table>
        <table width="507" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="377">
			<div style="margin-bottom:10px"><strong>Текст вопроса:</strong></div>
			<div>
				<textarea name="quest" rows="6" id="quest" style="border:solid 1px gray;width:605px"><?php echo @$mod['quest'];?></textarea>
			</div>			</td>
          </tr>
          <tr>
            <td>
			<div style="margin-bottom:10px"><strong>Ответ на вопрос:</strong></div>
			<div>
			<?php
                $inCore->insertEditor('answer', $mod['answer'], '300', '605');
			?>
			</div>			</td>
          </tr>
        </table>
        <p>
          <label>
          <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add_item') { echo 'value="Добавить вопрос"'; } else { echo 'value="Сохранить изменения"'; } ?> />
          </label>
          <label>
          <input name="back2" type="button" id="back2" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
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

// ============================================================================================= //
// ============================================================================================= //

	if ($opt == 'add_cat' || $opt == 'edit_cat'){		
		if ($opt=='add_cat'){
			 echo '<h3>Добавить категорию</h3>';
			 cpAddPathway('Добавить категорию', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=add_cat');	 
			} else {
				 if(isset($_REQUEST['item_id'])){
					 $id = $_REQUEST['item_id'];
					 $sql = "SELECT * FROM cms_faq_cats WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
				 }
				
				 echo '<h3>Категория: '.$mod['title'].'</h3>';
	 	 		 cpAddPathway('Категории вопросов', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list_cats');
				 cpAddPathway($mod['title'], '?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$_REQUEST['item_id']);	 
			}
			?>
		<form id="addform" name="addform" method="post" action="index.php?view=components&amp;do=config&amp;id=<?php echo $_REQUEST['id'];?>">
			<table width="620" border="0" cellpadding="0" cellspacing="10" class="proptable">
			  <tr>
				<td><strong>Название категории: </strong></td>
				<td width="220"><input name="title" type="text" id="title" style="width:220px" value="<?php echo @$mod['title'];?>"/></td>
			  </tr>
			  <tr>
			    <td><strong>Родительская категория</strong>: </td>
			    <td><select name="parent_id" id="parent_id" style="width:220px">
					<option value="0" <?php if (!isset($mod['parent_id'])||@$mod['parent_id']==0){ echo 'selected'; } ?>>--</option>
				<?php if (isset($mod['parent_id'])) 
					  { 
							echo $inCore->getListItems('cms_faq_cats', $mod['id']);
					  }	else { 
								echo $inCore->getListItems('cms_faq_cats');
							 }
				?>
                </select></td>
		      </tr>
			  <tr>
				<td><strong>Публиковать категорию?</strong></td>
				<td><input name="published" type="radio" value="1" <?php if (@$mod['published']) { echo 'checked="checked"'; } ?> />
				  Да
				  <label>
			  <input name="published" type="radio" value="0"  <?php if (@!$mod['published']) { echo 'checked="checked"'; } ?> />
					Нет</label></td>
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
			  <input name="back3" type="button" id="back3" value="Отмена" onclick="window.location.href='index.php?view=components';"/>
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

// ============================================================================================= //
// ============================================================================================= //

	if ($opt == 'config') {
		?>
<?php
	}


?>