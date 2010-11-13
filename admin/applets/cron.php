<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function applet_cron(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }
	
	$GLOBALS['cp_page_title'] = 'Задачи CRON';
 	cpAddPathway('Настройки сайта', 'index.php?view=config');
 	cpAddPathway('Задачи CRON', 'index.php?view=cron');

    $do = $inCore->request('do', 'str', 'list');
    $id = $inCore->request('id', 'int', '0');

    $inDB = cmsDatabase::getInstance();

    $inCore->loadClass('cron');
		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'new.gif';
		$toolmenu[0]['title'] = 'Создать задачу';
		$toolmenu[0]['link'] = "?view=cron&do=add";

		cpToolMenu($toolmenu);

        $items = cmsCron::getJobs(false);

        include(TEMPLATE_DIR.'admin/cron.php');

	}

    if ($do == 'show'){

        if ($id){ cmsCron::jobEnabled($id, true);  }
        echo '1'; exit;

	}

	if ($do == 'hide'){

        if ($id){ cmsCron::jobEnabled($id, false);  }
        echo '1'; exit;

	}
		
	if ($do == 'delete'){

        if ($id) { cmsCron::removeJobById($id); }

        $inCore->redirect('index.php?view=cron');

	}
	
	if ($do == 'submit'){

        $job_name       = $inCore->request('job_name', 'str');
        $comment        = $inCore->request('comment', 'str');
        $job_interval   = $inCore->request('job_interval', 'int');
        $enabled        = $inCore->request('enabled', 'int');
        $component      = $inCore->request('component', 'str');
        $model_method   = $inCore->request('model_method', 'str');
        $custom_file    = $inCore->request('custom_file', 'str');
        $class_name     = $inCore->request('class_name', 'str');
        $class_method   = $inCore->request('class_method', 'str');

        cmsCron::registerJob($job_name, array(
                                        'interval' => $job_interval,
                                        'component' => $component,
                                        'model_method' => $model_method,
                                        'comment' => $comment,
                                        'custom_file' => $custom_file,
                                        'enabled' => $enabled,
                                        'class_name' => $class_name,
                                        'class_method' => $class_method
                                  ));

        $inCore->redirect('index.php?view=cron');
        
	}
	
	if ($do == 'update'){
		
        if (!$id) { $inCore->halt(); }

        $job_name       = $inCore->request('job_name', 'str');
        $comment        = $inCore->request('comment', 'str');
        $job_interval   = $inCore->request('job_interval', 'int');
        $enabled        = $inCore->request('enabled', 'int');
        $component      = $inCore->request('component', 'str');
        $model_method   = $inCore->request('model_method', 'str');
        $custom_file    = $inCore->request('custom_file', 'str');
        $class_name     = $inCore->request('class_name', 'str');
        $class_method   = $inCore->request('class_method', 'str');

        cmsCron::updateJob($id, array(
                                        'name' => $job_name,
                                        'interval' => $job_interval,
                                        'component' => $component,
                                        'model_method' => $model_method,
                                        'comment' => $comment,
                                        'custom_file' => $custom_file,
                                        'enabled' => $enabled,
                                        'class_name' => $class_name,
                                        'class_method' => $class_method
                                  ));

        $inCore->redirect('index.php?view=cron');

	}

   if ($do == 'edit' || $do== 'add'){

 		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);

		if ($do=='edit'){

                    $mod = cmsCron::getJobById($id);

					 echo '<h3>Редактировать задачу</h3>';
					 cpAddPathway($mod['job_name'], 'index.php?view=cron&do=edit&id='.$mod['id']);
		
		} else {
					 echo '<h3>Создать задачу</h3>';
					 cpAddPathway('Создать задачу', 'index.php?view=cron&do=add');
		}
	?>
      <?php if ($error){ ?>
          <div style="color:red;margin-bottom:10px;">
            <?php echo $error; ?>
          </div>
      <?php } ?>

    <form action="index.php?view=cron" method="post" enctype="multipart/form-data" name="addform" id="addform">
        <table width="750" border="0" cellpadding="0" cellspacing="10" class="proptable">
            <tr>
                <td width="300" valign="middle">
                    <strong>Название: </strong><br/>
                    <span class="hinttext">Только латинские буквы, цифры и знак подчеркивания</span>
                </td>
                <td width="" valign="middle">
                    <input name="job_name" type="text" style="width:220px" value="<?php echo @$mod['job_name'];?>" />
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>Описание: </strong><br/>
                    <span class="hinttext">Максимум 200 символов</span>
                </td>
                <td valign="middle">
                    <input name="comment" type="text" maxlength="200" style="width:400px" value="<?php echo @$mod['comment'];?>" />
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>Задача активна: </strong><br/>
                    <span class="hinttext">Неактивные задачи не выполняются</span>
                </td>
                <td valign="middle">
                    <label>
                        <input name="enabled" type="radio" value="1" <?php if ($mod['is_enabled']) { echo 'checked="checked"'; } ?> /> Да
                    </label>
                    <label>
                        <input name="enabled" type="radio" value="0"  <?php if (!$mod['is_enabled']) { echo 'checked="checked"'; } ?> /> Нет
                    </label>
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>Интервал: </strong><br/>
                    <span class="hinttext">Периодичность запуска задачи</span>
                </td>
                <td valign="middle">
                    <input name="job_interval" type="text" maxlength="4" style="width:50px" value="<?php echo @$mod['job_interval'];?>" /> ч.
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>PHP-файл: </strong><br/>
                    <span class="hinttext">Пример: <strong>includes/myphp/test.php</strong></span><br/>
                </td>
                <td valign="middle">
                    <input name="custom_file" type="text" maxlength="250" style="width:220px" value="<?php echo @$mod['custom_file'];?>" />
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>Компонент: </strong><br/>
                </td>
                <td valign="middle">
                    <input name="component" type="text" maxlength="250" style="width:220px" value="<?php echo @$mod['component'];?>" />
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>Метод модели: </strong><br/>
                </td>
                <td valign="middle">
                    <input name="model_method" type="text" maxlength="250" style="width:220px" value="<?php echo @$mod['model_method'];?>" />
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>Класс: </strong><br/>
                    <span class="hinttext">
                        <span style="color:#666;font-family: mono">файл|класс</span>, пример: <strong>actions|cmsActions</strong> или<br/>
                        <span style="color:#666;font-family: mono">класс</span>, пример: <strong>cmsDatabase</strong>
                    </span>
                </td>
                <td valign="top">
                    <input name="class_name" type="text" maxlength="50" style="width:220px" value="<?php echo @$mod['class_name'];?>" />
                </td>
            </tr>
            <tr>
                <td width="" valign="middle">
                    <strong>Статический метод класса: </strong><br/>
                </td>
                <td valign="middle">
                    <input name="class_method" type="text" maxlength="50" style="width:220px" value="<?php echo @$mod['class_method'];?>" />
                </td>
            </tr>
        </table>
        <p>
		  <?php if($do=='edit'){ ?>
	          <input name="do" type="hidden" id="do" value="update" />
	          <input name="add_mod" type="submit" id="add_mod" value="Сохранить задачу" />
		  <?php } else { ?>
	          <input name="do" type="hidden" id="do" value="submit" />	  
	          <input name="add_mod" type="submit" id="add_mod" value="Создать задачу" />
		  <?php } ?>
          <span style="margin-top:15px">
          <input name="back2" type="button" id="back2" value="Отмена" onclick="window.history.back();"/>
          </span>
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