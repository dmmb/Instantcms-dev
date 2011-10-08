<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function applet_userbanlist(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/users', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = 'Бан-лист';
 	cpAddPathway('Пользователи', 'index.php?view=users');	
 	cpAddPathway('Бан-лист', 'index.php?view=userbanlist');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort
	if (isset($_REQUEST['to'])) { $to = $_REQUEST['to']; $_SESSION['banback'] = $_SERVER['HTTP_REFERER']; } else { $to = 0; }
		
	if ($do == 'list'){
		$toolmenu = array();
		$toolmenu[0]['icon'] = 'useradd.gif';
		$toolmenu[0]['title'] = 'Добавить в бан-лист';
		$toolmenu[0]['link'] = "?view=userbanlist&do=add";
		
		$toolmenu[1]['icon'] = 'edit.gif';
		$toolmenu[1]['title'] = 'Редактировать выбранные';
		$toolmenu[1]['link'] = "javascript:checkSel('?view=userbanlist&do=edit&multiple=1');";

		$toolmenu[4]['icon'] = 'delete.gif';
		$toolmenu[4]['title'] = 'Удалить выбранные';
		$toolmenu[4]['link'] = "javascript:checkSel('?view=userbanlist&do=delete&multiple=1');";

		$toolmenu[5]['icon'] = 'cancel.gif';
		$toolmenu[5]['title'] = 'Отмена';
		$toolmenu[5]['link'] = "?view=users";

		cpToolMenu($toolmenu);

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '30';

		$fields[1]['title'] = 'Пользователь';	$fields[1]['field'] = 'user_id';	$fields[1]['width'] = '120';
		$fields[1]['filter'] = 12;		$fields[1]['prc'] = 'cpUserNick';

		$fields[2]['title'] = 'IP-Адрес';	$fields[2]['field'] = 'ip';		$fields[2]['width'] = '100';		$fields[2]['link'] = '?view=userbanlist&do=edit&id=%id%';
		$fields[2]['filter'] = 12;

		$fields[3]['title'] = 'Дата';	$fields[3]['field'] = 'bandate';	$fields[3]['width'] = '';
		$fields[3]['filter'] = 12; $fields[3]['fdate'] = '%d/%m/%Y %H:%i:%s';

		$fields[4]['title'] = 'Срок';	$fields[4]['field'] = 'int_num';	$fields[4]['width'] = '55';
		$fields[5]['title'] = '';	$fields[5]['field'] = 'int_period';	$fields[5]['width'] = '190';
		
		$fields[14]['title'] = 'Автоудаление';	$fields[14]['field'] = 'autodelete';	$fields[14]['width'] = '90';
		$fields[14]['prc'] = 'cpYesNo'; 

//		$fields[2]['title'] = 'Псевдоним';	$fields[2]['field'] = 'alias';		$fields[2]['width'] = '150';	$fields[2]['link'] = '?view=usergroups&do=edit&id=%id%';
//		$fields[2]['filter'] = 12;
	
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Редактировать';
		$actions[0]['icon']  = 'edit.gif';
		$actions[0]['link']  = '?view=userbanlist&do=edit&id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить правило?';
		$actions[1]['link']  = '?view=userbanlist&do=delete&id=%id%';
				
		//Print table
		cpListTable('cms_banlist', $fields, $actions, '1=1', 'ip DESC');		
	}
		
	if ($do == 'delete'){
		if (!isset($_REQUEST['item'])){
			if ($id >= 0){ dbDelete('cms_banlist', $id);  }
		} else {
			dbDeleteList('cms_banlist', $_REQUEST['item']);				
		}
		header('location:?view=userbanlist');
	}
	
	if ($do == 'submit'){	
		$user_id = $_REQUEST['user_id'];
		$ip = trim($_REQUEST['ip']);
		
		if (isset($_REQUEST['forever'])){ $forever = true; } else { $forever = false; }
		if (isset($_REQUEST['autodelete'])){ $autodelete = 1; } else { $autodelete = 0; }

		$int_num = $_REQUEST['int_num'];
		$int_period = $_REQUEST['int_period'];
		
		if ($forever) { $int_num = 0; }
		
		$error = '';
				
		if (!$ip){	$error = 'Нужно указать IP-адрес!';	}		
		if ($ip == $_SERVER['REMOTE_ADDR'] || $user_id == $inUser->id){ $error = 'IP-адрес совпадает с вашим!';	}
		
		if($inCore->userIsAdmin($user_id)){
			$error = 'Нельзя забанить администратора!';
		}

		$back = '?view=userbanlist';
			
		if (!$error){		
			$sql = "INSERT INTO cms_banlist (user_id, ip, bandate, int_num, int_period, status, autodelete)
					VALUES ('$user_id', '$ip', NOW(), '$int_num', '$int_period', '1', $autodelete)";
			dbQuery($sql) ;
			if (isset($_SESSION['banback'])){
				$back = $_SESSION['banback'];
				unset($_SESSION['banback']);
			}
			header('location:'.$back);
		} else {
			$do='add';
			$mod['user_id'] = $user_id;
			$mod['ip'] = $ip;
			$mod['int_num'] = $int_num;
			$mod['int_period'] = $int_period;
			$mod['autodelete'] = $autodelete;
		}					
	}	  
	
	if ($do == 'update'){
		if(isset($_REQUEST['id'])) { 
			$user_id = $_REQUEST['user_id'];
			$ip = $_REQUEST['ip'];
			
			if (isset($_REQUEST['forever'])){ $forever = true; } else { $forever = false; }
			if (isset($_REQUEST['autodelete'])){ $autodelete = 1; } else { $autodelete = 0; }
	
			$int_num = $_REQUEST['int_num'];
			$int_period = $_REQUEST['int_period'];
					
			if ($forever) { $int_num = 0; }
					
			$sql = "UPDATE cms_banlist
					SET user_id='$user_id',
						ip='$ip', 
						int_num='$int_num',
						int_period='$int_period',
						autodelete=$autodelete
					WHERE id = $id
					LIMIT 1";
			dbQuery($sql) ;
		}
		if (!isset($_SESSION['editlist']) || @sizeof($_SESSION['editlist'])==0){
			header('location:?view=userbanlist');		
		} else {
			header('location:?view=userbanlist&do=edit');		
		}
	}
	
   if ($do == 'add' || $do == 'edit'){
 
		 $GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/admin/js/banlist.js"></script>';
 
 		$toolmenu = array();
		$toolmenu[0]['icon'] = 'save.gif';
		$toolmenu[0]['title'] = 'Сохранить';
		$toolmenu[0]['link'] = 'javascript:document.addform.submit();';

		$toolmenu[1]['icon'] = 'cancel.gif';
		$toolmenu[1]['title'] = 'Отмена';
		$toolmenu[1]['link'] = 'javascript:history.go(-1);';

		cpToolMenu($toolmenu);
   
		if ($do=='add'){
			 echo '<h3>Добавить в бан-лист</h3>';
 	 		 cpAddPathway('Добавить в бан-лист', 'index.php?view=userbanlist&do=add');
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
	
					 $sql = "SELECT * FROM cms_banlist WHERE id = $id LIMIT 1";
					 $result = dbQuery($sql) ;
					 if (mysql_num_rows($result)){
						$mod = mysql_fetch_assoc($result);
					 }
					
					 echo '<h3>Редактировать правило '.$ostatok.'</h3>';
					 
					 cpAddPathway('Редактировать правило', 'index.php?view=userbanlist&do=edit&id='.$mod['id']);
			}   

	if(isset($mod['access'])){
		$mod['access'] = str_replace(', ', ',', $mod['access']);
		$mod['access'] = explode(',', $mod['access']);
	}
	
	$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="/includes/jquery/jquery.js"></script>';
	
	?>
	  <div style="margin-top:2px;padding:10px;border:dotted 1px silver; width:508px;background:#FFFFCC">
	  	<div style="font-weight:bold">Внимание!</div>
		<div>Добавление IP-адреса в бан-лист полностью запретит доступ к сайту!</div>
		<div>Если вы хотите запретить доступ не полностью, а только авторизацию, то воспользуйтесь функцией "Заблокировать"
		в настройках нужного пользователя.</div>
	  </div>
	  <?php if (@$error){ ?>
	  	<div style="padding:15px;color:red"><?php echo $error;?></div>
	  <?php } ?>
      <form id="addform" name="addform" method="post" action="index.php?view=userbanlist">
        <table width="530" border="0" cellspacing="5" class="proptable">
          <tr>
            <td width="150" valign="top"><div><strong>Пользователь: </strong></div></td>
			<?php if($do=='add' && $to) { $mod['user_id'] = $to; $mod['ip'] = dbGetField('cms_users', 'id='.$to, 'last_ip'); } ?>
            <td valign="top">
				<select name="user_id" id="user_id" onchange="loadUserIp()">
					<option value="0" <?php if (@!$mod['user_id']){ echo 'selected'; } ?>>-- без привязки к пользователю --</option>
                    <?php
                        if (isset($mod['user_id'])) {
                            echo $inCore->getListItems('cms_users', $mod['user_id'], 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0', 'id', 'nickname');
                        } else {
                            echo $inCore->getListItems('cms_users', 0, 'nickname', 'ASC', 'is_deleted=0 AND is_locked=0', 'id', 'nickname');
                        }
                    ?>
				</select>
            </td>
          </tr>
          <tr>
            <td valign="top"><strong>IP-адрес:</strong></td>
            <td valign="top"><input name="ip" type="text" id="ip" size="30" value="<?php echo @$mod['ip'];?>"/></td>
          </tr>
		  <?php $forever=false; if (@$mod['int_num']==0){ $mod['int_num']=1; $forever = true; }?>
          <tr>
            <td valign="top"><strong>Бан навсегда:</strong></td>
            <td valign="top"><input type="checkbox" name="forever" value="1" <?php if($forever){ echo 'checked'; } ?> onclick="$('tr.bantime').toggle();"/></td>
          </tr>
          <tr class="bantime">
            <td valign="top"><strong>Бан на время:</strong> </td>
			
            <td valign="top"><p>
            <input name="int_num" type="text" id="int_num" size="5" value="<?php echo @(int)$mod['int_num']?>"/>
              <select name="int_period" id="int_period">
                <option value="MINUTE"  <?php if(@strstr($mod['int_period'], 'MINUTE')) { echo 'selected'; } ?>>минут</option>
                <option value="HOUR"  <?php if(@strstr($mod['int_period'], 'HOUR')) { echo 'selected'; } ?>>часов</option>
                <option value="DAY" <?php if(@strstr($mod['int_period'], 'DAY')) { echo 'selected'; } ?>>дней</option>
                <option value="MONTH" <?php if(@strstr($mod['int_period'], 'MONTH')) { echo 'selected'; } ?>>месяцев</option>
              </select>
            </p>
              <p>
                <input name="autodelete" type="checkbox" id="autodelete" value="1" <?php if($mod['autodelete']){ echo 'checked'; } ?>/>
            Удалить бан автоматически после истечения срока</p></td>
          </tr>
		  <?php if ($forever) { ?><script type="text/javascript">$('tr.bantime').hide();</script><?php } ?>
        </table>
        <p>
          <label>
          <input name="add_mod" type="submit" id="add_mod" <?php if ($do=='add') { echo 'value="Добавить в бан-лист"'; } else { echo 'value="Сохранить правило"'; } ?> />
          </label>
          <label><span style="margin-top:15px">
          <input name="back" type="button" id="back" value="Отмена" onclick="window.history.back();"/>
          </span></label>
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