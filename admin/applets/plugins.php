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

function cpPluginDir($plugin){
    return '/plugins/'.$plugin;
}

function applet_plugins(){

    $inCore = cmsCore::getInstance();

    $GLOBALS['cp_page_title'] = 'Плагины';
    cpAddPathway('Плагины', 'index.php?view=plugins');

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/plugins', $adminAccess)) { cpAccessDenied(); }

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort

// ===================================================================================== //

	if ($do == 'hide'){
		dbHide('cms_plugins', $id);
		echo '1'; exit;
	}

// ===================================================================================== //

	if ($do == 'show'){
		dbShow('cms_plugins', $id);
		echo '1'; exit;		
	}

// ===================================================================================== //

	if ($do == 'list'){

		$toolmenu = array();
		$toolmenu[1]['icon']    = 'install.gif';
		$toolmenu[1]['title']   = 'Установить плагины';
		$toolmenu[1]['link']    = '?view=install&do=plugin';

		cpToolMenu($toolmenu);


        $plugin_id = $inCore->request('installed', 'str', '');

        if ($plugin_id){
            $task       = $inCore->request('task', 'str', 'install');

            if ($task == 'install' || $task == 'upgrade'){
                $plugin     = $inCore->loadPlugin($plugin_id);
                $task_str   = ($task=='install') ? 'установлен' : 'обновлен';
                echo '<div style="color:green;margin-top:12px;margin-bottom:5px;">Плагин <strong>"'.$plugin->info['title'].'"</strong> успешно '.$task_str.'. Включите его, для начала работы.</div>';
            }

            if ($task == 'remove'){
                echo '<div style="color:green;margin-top:12px;margin-bottom:5px;">Плагин успешно удален из системы.</div>';
            }
        }

		//TABLE COLUMNS
		$fields = array();

		$fields[0]['title'] = 'id';			$fields[0]['field'] = 'id';			$fields[0]['width'] = '20';
		$fields[1]['title'] = 'Название';	$fields[1]['field'] = 'title';		$fields[1]['width'] = '250';
		$fields[2]['title'] = 'Описание';	$fields[2]['field'] = 'description';$fields[2]['width'] = '';
		$fields[3]['title'] = 'Автор';      $fields[3]['field'] = 'author';		$fields[3]['width'] = '160';
		$fields[4]['title'] = 'Версия';     $fields[4]['field'] = 'version';	$fields[4]['width'] = '50';

        $fields[5]['title'] = 'Папка';     $fields[5]['field'] = 'plugin';      $fields[5]['width'] = '100';        

		$fields[6]['title'] = 'Включен';	$fields[6]['field'] = 'published';	$fields[6]['width'] = '60';
		
		//ACTIONS
		$actions = array();
		$actions[0]['title'] = 'Настроить';
		$actions[0]['icon']  = 'config.gif';
		$actions[0]['link']  = '?view=plugins&do=config&id=%id%';

		$actions[1]['title'] = 'Удалить';
		$actions[1]['icon']  = 'delete.gif';
		$actions[1]['confirm'] = 'Удалить плагин из системы?';
		$actions[1]['link']  = '?view=install&do=remove_plugin&id=%id%';
		//Print table
		cpListTable('cms_plugins', $fields, $actions);
	}

// ===================================================================================== //

    if ($do == 'save_config'){

        $plugin_name    = $inCore->request('plugin', 'str', 0);
        $config         = $inCore->request('config', 'array');

        if (!$config || !$plugin_name) { $inCore->redirectBack(); }

        $inCore->savePluginConfig($plugin_name, $config);

        $inCore->redirect('index.php?view=plugins');

    }

// ===================================================================================== //

    if ($do == 'config'){

        $plugin_id    = $inCore->request('id', 'int', 0);

        if (!$plugin_id) { $inCore->redirectBack(); }

        $plugin_name    = $inCore->getPluginById($plugin_id);
        $plugin         = $inCore->loadPlugin($plugin_name);
        $config         = $inCore->loadPluginConfig($plugin_name);

        $GLOBALS['cp_page_title'] = $plugin->info['title'];
        cpAddPathway($plugin->info['title'], 'index.php?view=plugins&do=config&plugin='.$plugin->info['plugin']);

        echo '<h3>'.$plugin->info['title'].'</h3>';

        if (!$config) {

            echo '<p>Плагин не имеет конфигурации.</p>';
            echo '<p><a href="javascript:window.history.go(-1);">Назад</a></p>';
            return;
            
        }

        echo '<form action="index.php?view=plugins&do=save_config&plugin='.$plugin_name.'" method="POST">';

            echo '<table class="proptable" width="605" cellpadding="8" cellspacing="0" border="0">';
                foreach ($config as $field=>$value){
                    echo '<tr>';
                        echo '<td width="150"><strong>'.$field.':</strong></td>';
                        echo '<td><input type="text" style="width:90%" name="config['.$field.']" value="'.$value.'" /></td>';
                    echo '</tr>';
                }
            echo '</table>';

            echo '<div style="margin-top:6px;">';
                echo '<input type="submit" name="save" value="Сохранить" /> ';
                echo '<input type="button" name="back" value="Отменить" onclick="window.history.go(-1)" />';
            echo '</div>';

        echo '</form>';

    }

// ===================================================================================== //

}

?>