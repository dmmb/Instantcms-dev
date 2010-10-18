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

function pluginsList($new_plugins, $action_name, $action){

    $inCore = cmsCore::getInstance();

    echo '<table cellpadding="3" cellspacing="0" border="0" style="margin-left:40px">';
    foreach($new_plugins as $plugin){
        $plugin_obj = $inCore->loadPlugin($plugin);

        if ($action == 'install_plugin') { $version = $plugin_obj->info['version']; }
        if ($action == 'upgrade_plugin') { $version = $inCore->getPluginVersion($plugin) . ' &rarr; '. $plugin_obj->info['version']; }

        echo '<tr>';
            echo '<td width="16"><img src="/admin/images/icons/hmenu/plugins.png" /></td>';
            echo '<td><a style="font-weight:bold;font-size:14px" title="'.$action_name.' '.$plugin_obj->info['title'].'" href="index.php?view=install&do='.$action.'&id='.$plugin.'">'.$plugin_obj->info['title'].'</a> v'.$version.'</td>';
        echo '<tr>';
        echo '<tr>';
            echo '<td width="16">&nbsp;</td>';
            echo '<td>
                        <div style="margin-bottom:6px;">'.$plugin_obj->info['description'].'</div>
                        <div style="color:gray"><strong>�����:</strong> '.$plugin_obj->info['author'].'</div>
                        <div style="color:gray"><strong>�����:</strong> /plugins/'.$plugin_obj->info['plugin'].'</div>
                  </td>';
        echo '<tr>';
    }
    echo '</table>';

    return;

}

function componentsList($new_components, $action_name, $action){

    $inCore = cmsCore::getInstance();

    echo '<table cellpadding="3" cellspacing="0" border="0" style="margin-left:40px">';
    foreach($new_components as $component){        
        if ($inCore->loadComponentInstaller($component)) {

            $_component = call_user_func('info_component_'.$component);

            if ($action == 'install_component') { $version = $_component['version']; }
            if ($action == 'upgrade_component') { $version = $inCore->getComponentVersion($component) . ' &rarr; '. $_component['version']; }

            echo '<tr>';
                echo '<td width="16"><img src="/admin/images/icons/hmenu/plugins.png" /></td>';
                echo '<td><a style="font-weight:bold;font-size:14px" title="'.$action_name.' '.$_component['title'].'" href="index.php?view=install&do='.$action.'&id='.$component.'">'.$_component['title'].'</a> v'.$version.'</td>';
            echo '<tr>';
            echo '<tr>';
                echo '<td width="16">&nbsp;</td>';
                echo '<td>
                            <div style="margin-bottom:6px;">'.$_component['description'].'</div>
                            <div style="color:gray"><strong>�����:</strong> '.$_component['author'].'</div>
                            <div style="color:gray"><strong>�����:</strong> /components/'.$_component['link'].'</div>
                      </td>';
            echo '<tr>';

        }
    }
    echo '</table>';

    return;

}

function applet_install(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = '��������� ����������';

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }
	if (isset($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { $id = -1; }
	if (isset($_REQUEST['co'])) { $co = $_REQUEST['co']; } else { $co = -1; } //current ordering, while resort

// ============================================================================== //

//    if ($do != 'plugin' && $do != 'install_plugin' && $do != 'upgrade_plugin' && $do != 'finish_plugin' && $do != 'remove_plugin'){
//
//        echo '<p><b>� ������� ������ ������� ��������� ��������� ���������� �� ��������.</b></p>';
//        echo '<p>���������� �� <a href="http://www.instantcms.ru">����������� ����</a>.</p>';
//        echo '<p><a href="javascript:window.history.go(-1);">�����</a></p>';
//
//        return;
//
//    }

// ============================================================================== //

    if ($do == 'component'){

      	cpAddPathway('��������� ����������', 'index.php?view=install&do=component');

        $new_components = $inCore->getNewComponents();
        $upd_components = $inCore->getUpdatedComponents();

        echo '<h3>��������� �����������</h3>';

        if (!$new_components && !$upd_components){

            echo '<p>� ������� �� ������� ����������, ������� ��� �� �����������.</p>';
            echo '<p>���� �� ������� ����� � ����������� � ������ ����� � ������ ���������� ��� ��������, �� ���������� ��� � ������ ����� � ������������� ��������.</p>';
            echo '<p><a href="javascript:window.history.go(-1);">��������� �����</a></p>';
            return;

        }

        if ($new_components){

            echo '<p><strong>������� ����������, ��������� ��� ���������:</strong></p>';
            componentsList($new_components, '����������', 'install_component');

        }

        if ($upd_components){

            echo '<p><strong>������� ����������, ��������� ��� ����������:</strong></p>';
            componentsList($upd_components, '��������', 'upgrade_component');

        }

        echo '<p>�������� �� �������� ����������, ����� ����������.</p>';

        echo '<p><a href="javascript:window.history.go(-1);">�����</a></p>';

    }

// ============================================================================== //

    if ($do == 'install_component'){

        cpAddPathway('��������� ����������', 'index.php?view=install&do=component');

        $error = '';

        $component_id = $inCore->request('id', 'str', '');

        if(!$component_id){
            $inCore->redirectBack();
        }

        if ($inCore->loadComponentInstaller($component_id)){
            $_component = call_user_func('info_component_'.$component_id);
            $error      = call_user_func('install_component_'.$component_id);
        } else {
            $error = '�� ������� ��������� ���������� ����������.';
        }

        if ($error === true) {
            $inCore->installComponent($_component, $_component['config']);
            $inCore->redirect('/admin/index.php?view=install&do=finish_component&id='.$component_id.'&task=install');
        } else {

            echo '<p style="color:red">'.$error.'</p>';
            
        }

        echo '<p><a href="index.php?view=install&do=component">�����</a></p>';

    }

// ============================================================================== //

    if ($do == 'upgrade_component'){

        cpAddPathway('��������� ����������', 'index.php?view=install&do=component');

        $error = '';

        $component_id = $inCore->request('id', 'str', '');

        if(!$component_id){
            $inCore->redirectBack();
        }

        if ($inCore->loadComponentInstaller($component_id)) {
            $_component = call_user_func('info_component_'.$component_id);
            $error      = call_user_func('upgrade_component_'.$component_id);
        } else {
            $error = '�� ������� ��������� ���������� ����������.';
        }

        if ($error === true) {
            $inCore->upgradeComponent($_component, $_component['config']);
            $inCore->redirect('/admin/index.php?view=install&do=finish_component&id='.$component_id.'&task=upgrade');
        } else {

            echo '<p style="color:red">'.$error.'</p>';
            
        }

        echo '<p><a href="index.php?view=install&do=component">�����</a></p>';

    }

// ============================================================================== //

    if ($do == 'remove_component'){

        $component_id = $inCore->request('id', 'int', '');

        if(!$component_id){
            $inCore->redirectBack();
        }

        $inCore->removeComponent($component_id);

        $inCore->redirect('/admin/index.php?view=install&do=finish_component&id='.$component_id.'&task=remove');

    }

// ============================================================================== //

    if ($do == 'finish_component'){

        $component_id   = $inCore->request('id', 'str', '');
        $task           = $inCore->request('task', 'str', 'install');

        $inCore->redirect('/admin/index.php?view=components&installed='.$component_id.'&task='.$task);

    }


// ============================================================================== //

    if ($do == 'plugin'){

      	cpAddPathway('��������� ����������', 'index.php?view=install&do=plugin');

        $new_plugins = $inCore->getNewPlugins();
        $upd_plugins = $inCore->getUpdatedPlugins();

        echo '<h3>��������� ��������</h3>';

        if (!$new_plugins && !$upd_plugins){

            echo '<p>� ������� �� ������� �������, ������� ��� �� �����������.</p>';
            echo '<p>���� �� ������� ����� � �������� � ������ ����� � ������ ���������� ��� ��������, �� ���������� ��� � ����� <strong>/plugins</strong> � ������������� ��������.</p>';
            echo '<p><a href="javascript:window.history.go(-1);">��������� �����</a></p>';
            return;

        }

        if ($new_plugins){

            echo '<p><strong>������� �������, ��������� ��� ���������:</strong></p>';
            pluginsList($new_plugins, '����������', 'install_plugin');

        }

        if ($upd_plugins){

            echo '<p><strong>������� �������, ��������� ��� ����������:</strong></p>';
            pluginsList($upd_plugins, '��������', 'upgrade_plugin');

        }

        echo '<p>�������� �� �������� �������, ����� ����������.</p>';

        echo '<p><a href="javascript:window.history.go(-1);">�����</a></p>';

    }

// ============================================================================== //

    if ($do == 'install_plugin'){

        cpAddPathway('��������� ����������', 'index.php?view=install&do=plugin');

        $error = '';

        $plugin_id = $inCore->request('id', 'str', '');

        if(!$plugin_id){
            $inCore->redirectBack();
        }

        $plugin = $inCore->loadPlugin($plugin_id);

        if (!$plugin) { $error = '�� ������� ��������� ���� �������.'; }

        if (!$error && $plugin->install()) {
            $inCore->redirect('/admin/index.php?view=install&do=finish_plugin&id='.$plugin_id.'&task=install');
        }

        if ($error){
            echo '<p style="color:red">'.$error.'</p>';
        }

        echo '<p><a href="index.php?view=install&do=plugin">�����</a></p>';

    }

// ============================================================================== //

    if ($do == 'upgrade_plugin'){

        cpAddPathway('��������� ����������', 'index.php?view=install&do=plugin');

        $error = '';

        $plugin_id = $inCore->request('id', 'str', '');

        if(!$plugin_id){
            $inCore->redirectBack();
        }

        $plugin = $inCore->loadPlugin($plugin_id);

        if (!$plugin) { $error = '�� ������� ��������� ���� �������.'; }

        if (!$error && $plugin->upgrade()) {
            $inCore->redirect('/admin/index.php?view=install&do=finish_plugin&id='.$plugin_id.'&task=upgrade');
        }

        if ($error){
            echo '<p style="color:red">'.$error.'</p>';
        }

        echo '<p><a href="index.php?view=install&do=plugin">�����</a></p>';

    }

// ============================================================================== //

    if ($do == 'remove_plugin'){

        $plugin_id = $inCore->request('id', 'str', '');

        if(!$plugin_id){
            $inCore->redirectBack();
        }

        $inCore->removePlugin($plugin_id);

        $inCore->redirect('/admin/index.php?view=install&do=finish_plugin&id='.$plugin_id.'&task=remove');

    }

// ============================================================================== //

    if ($do == 'finish_plugin'){

        $plugin_id  = $inCore->request('id', 'str', '');
        $task       = $inCore->request('task', 'str', 'install');

        $inCore->redirect('/admin/index.php?view=plugins&installed='.$plugin_id.'&task='.$task);

    }

}

?>