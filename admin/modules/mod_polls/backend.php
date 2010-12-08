<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	cpAddPathway('Голосования', '?view=modules&do=edit&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
	$toolmenu = array();
	$toolmenu[0]['icon'] = 'new.gif';
	$toolmenu[0]['title'] = 'Новое голосование';
	$toolmenu[0]['link'] = '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=add';

	$toolmenu[1]['icon'] = 'list.gif';
	$toolmenu[1]['title'] = 'Все голосования';
	$toolmenu[1]['link'] = '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=list';

	$toolmenu[2]['icon'] = 'config.gif';
	$toolmenu[2]['title'] = 'Настройки модуля';
	$toolmenu[2]['link'] = '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=config';

	$toolmenu[3]['icon'] = 'cancel.gif';
	$toolmenu[3]['title'] = 'Отмена';
	$toolmenu[3]['link'] = '?view=modules';

	cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
    $cfg = $inCore->loadModuleConfig($_REQUEST['id']);

	if($opt=='saveconfig'){	
		$cfg = array();
		$cfg['shownum'] = $_REQUEST['shownum'];
		$cfg['poll_id'] = $_REQUEST['poll_id'];

        $inCore->saveModuleConfig($_REQUEST['id'], $cfg);

        header('location:index.php?view=modules&do=config&id='.$_REQUEST['id']);
	}
	
	if ($opt == 'list'){
		cpAddPathway('Все голосования', '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=list');
		echo '<h3>Все голосования</h3>';

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '';		$fields[1]['link'] = '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=edit&poll_id=%id%';
		$fields[1]['filter'] = 15;
				
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=edit&poll_id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить голосование?';
		$actions[1]['link']  = '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=delete&poll_id=%id%';
				
		//Print table
		cpListTable('cms_polls', $fields, $actions);		
	}

	if ($opt == 'submit'){
	
		$title = $inCore->strClear($_REQUEST['title']); $title = str_replace('"', '&quot;', $title);
		$answers_title = $_REQUEST['answers'];
		$answers = array();		
		foreach($answers_title as $key=>$value){
			$value = $inCore->strClear($value); $value = str_replace('"', '&quot;', $value);
			if ($value!='') { $answers[$value] = 0; }
		}
		$str_answers = serialize($answers);
		
		$sql = "INSERT INTO cms_polls (title, pubdate, answers)
				VALUES ('$title', NOW(), '$str_answers')";
		dbQuery($sql) ;
		
		header('location:index.php?view=modules&do=config&id='.$_REQUEST['id'].'&opt=list');
	}	  
	
	if($opt == 'delete'){
		if(isset($_REQUEST['poll_id'])) { 
			$id = $_REQUEST['poll_id'];
			$sql = "DELETE FROM cms_polls WHERE id = $id LIMIT 1";
			dbQuery($sql) ;
			$sql = "DELETE FROM cms_polls_log WHERE poll_id = $id";
			dbQuery($sql) ;
		}
		header('location:index.php?view=modules&do=config&id='.$_REQUEST['id'].'&opt=list');
	}
	
	if ($opt == 'update'){
		if(isset($_REQUEST['poll_id'])) { 
			$id = $_REQUEST['poll_id'];
			
			$title = $inCore->strClear($_REQUEST['title']); $title = str_replace('"', '&quot;', $title);
			$answers_title = $_REQUEST['answers'];
			$nums = $_REQUEST['num'];
			
				$answers = array();
				
				foreach($answers_title as $key=>$value){
					if($value!='') { 
						$value = $inCore->strClear($value); $value = str_replace('"', '&quot;', $value);
						if (isset($nums[$value])) { 
							$answers[$value] = $nums[$value];  
						}
						else {
							$answers[$value] = 0;
						}
					}
				}
				$str_answers = serialize($answers);
				
				$sql = "UPDATE cms_polls
						SET title='$title', 
							answers='$str_answers'
						WHERE id = $id
						LIMIT 1";
				dbQuery($sql) ;
										
			header('location:index.php?view=modules&do=config&id='.$_REQUEST['id'].'&opt=list');
		
		}
	}
	
	if($opt=='add' || $opt=='edit'){
	
		if ($opt=='add'){
			cpAddPathway('Новое голосование', '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=add');
			echo '<h3>Добавить голосование</h3>';
			unset($mod);
		} else {
			if(isset($_REQUEST['poll_id'])){
				 $id = $_REQUEST['poll_id'];
				 $sql = "SELECT * FROM cms_polls WHERE id = $id LIMIT 1";
				 $result = dbQuery($sql) ;
				 if (mysql_num_rows($result)){
					$mod = mysql_fetch_assoc($result);
					
					$mod['title'] = str_replace('"', '&quot;', $mod['title']);
					
					$answers = unserialize($mod['answers']);					 
					$answers_title = array();
					$answers_num = array();
					$item = 1;
					foreach($answers as $key=>$value){
						$key = str_replace('"', '&quot;', $key);
						$answers_title[$item] = $key;
						$answers_num[$item] = $value;
						$item++;
					}
				 }
			}			
			cpAddPathway($mod['title'], '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=add');
			echo '<h3>Редактировать голосование</h3>';
		}

	
?>
      <form id="addform" name="addform" method="post" action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>">
        <table width="600" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="200">Вопрос: </td>
            <td width="213"><input name="title" type="text" id="title" size="30" value="<?php echo $mod['title']; ?>"/></td>
            <td width="173">&nbsp;</td>
          </tr>
          <?php for ($v=1; $v<=8; $v++) { ?>
          <tr>
            <td>Вариант ответа №<?php echo $v?>:</td>
            <td><input name="answers[]" type="text" id="title2" size="30" value="<?php echo @$answers_title[$v];?>"/></td>
            <td><?php if (isset($answers_num[$v])) { echo 'Голосов: '.$answers_num[$v]; echo '<input type="hidden" name="num['.@$answers_title[$v].']" value="'.$answers_num[$v].'" />'; } else { echo '&nbsp;'; }?></td>
          </tr>
          <?php } ?>
        </table>
        <label>
        <input name="add_mod" type="submit" id="add_mod" <?php if ($opt=='add') { echo 'value="Создать голосование"'; } else { echo 'value="Сохранить голосование"'; } ?> />
        </label>
        <label></label>
        <input name="opt" type="hidden" id="opt" <?php if ($opt=='add') { echo 'value="submit"'; } else { echo 'value="update"'; } ?> />
        <?php
		  	if ($opt=='edit'){
			 echo '<input name="poll_id" type="hidden" value="'.$mod['id'].'" />';
			}
		  ?>
      </form>
      <p>
        <?php	

	}//if (add || edit)

	if($opt=='config'){

	cpAddPathway('Настройки', '?view=modules&do=config&id='.$_REQUEST['id'].'&opt=config');	
	echo '<h3>Настройки модуля</h3>';
	
	?>
      </p>
      <form action="index.php?view=modules&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
        <table border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="215"><strong>Показывать результаты до голосования: </strong></td>
            <td width="126"><input name="shownum" type="radio" value="1" <?php if (@$cfg['shownum']) { echo 'checked="checked"'; } ?>/>
              Да
              <input name="shownum" type="radio" value="0" <?php if (@!$cfg['shownum']) { echo 'checked="checked"'; } ?>/>
              Нет </td>
          </tr>
          <tr>
            <td><strong>Активное голосование : </strong></td>
            <td>
                <select name="poll_id" id="poll_id">
                    <option value="0">-- Случайное голосование --</option>
                    <?php
                        if (isset($cfg['poll_id'])) {
                            echo $inCore->getListItems('cms_polls', $cfg['poll_id']);
                        } else {
                            echo $inCore->getListItems('cms_polls');
                        }
                    ?>
                </select>
            </td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="opt" value="saveconfig" />
          <input name="save" type="submit" id="save" value="Сохранить" />
          <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='/admin/components.php';"/>
        </p>
      </form>
    <?php
	
	}

?>