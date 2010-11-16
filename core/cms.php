<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

define('CORE_VERSION', 		'1.7 RC');
define('CORE_BUILD', 		'1');
define('CORE_VERSION_DATE', '2010-11-14');
define('CORE_BUILD_DATE', 	'2010-11-14');

if (!defined('USER_UPDATER')) { define('USER_UPDATER', -1); }
if (!defined('USER_MASSMAIL')) { define('USER_MASSMAIL', -2); }

class cmsCore {

    private static  $instance;

    private         $start_time;

    private         $menu_item;
    private         $menu_id = 0;
    private         $menu_struct;
    private         $is_menu_id_strict;

    private         $uri;
    private         $component;
    private         $is_content = false;

    private         $module_configs = array();
    private         $component_configs = array();

    private         $smarty = false;

    private function __construct($install_mode=false) {

        if ($install_mode){ return; }

        //��������� ���� � ������
        $this->loadClass('db');
        $this->loadClass('config');

        //�������� ��������� ���� � ������
        $this->loadMenuStruct();

        //������� URI
        $this->uri = $this->detectURI();

        //��������� ���������
        $this->component = $this->detectComponent();

    }

    private function __clone() {}  

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public $single_run_plugins = array('wysiwyg');

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function getInstance($install_mode=false) {
        if (self::$instance === null) {  
            self::$instance = new self($install_mode);
        }  
        return self::$instance;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function startGenTimer() {

        $start_time         = microtime();
        $start_array        = explode(" ",$start_time);
        $this->start_time   = $start_array[1] + $start_array[0];
        return true;

    }

    public function getGenTime(){

        $end_time   = microtime();
        $end_array  = explode(" ", $end_time);
        $end_time   = $end_array[1] + $end_array[0];
        $time       = $end_time - $this->start_time;

        return $time;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function loadLanguage($file) {    
        global $_CFG;
        global $_LANG;

        $langfile = PATH.'/languages/'.$_CFG['lang'].'/'.$file.'.php';

        if (!file_exists($langfile)){ $langfile = PATH.'/languages/ru/'.$file.'.php'; }

        if (!file_exists($langfile)){ return false; }

        include_once($langfile);
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������������� ������
     * @param array $array
     * @param string $sort_by (�������� ����������)
     * @param int $desc (1 - �� �����������, 0 - �� ��������)
     * @param string $f (������� ���������)
     * @return array
     */
    public static function sortArray($array, $sort_by, $desc = 0, $f='strcmp') {

		if (!$desc) { $desc = 1; } else { $desc = -1;}
		
		usort($array, create_function('$a, $b', "return $desc*$f(\$b['$sort_by'], \$a['$sort_by']);"));

    	return($array);

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ����������� ������ � YAML
     * @param array $array
     * @return string
     */
    public function arrayToYaml($array) {

        $this->includeFile('includes/spyc/spyc.php');

        $yaml = Spyc::YAMLDump($array,2,40);

        return $yaml;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ����������� YAML � ������
     * @param string $yaml
     * @return array
     */
    public function yamlToArray($yaml) {

        $this->includeFile('includes/spyc/spyc.php');

        $array = Spyc::YAMLLoad($yaml);

        return $array;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������, ������� ��� ����������� �� ���� �������
     * @param string $event
     * @param mixed $item
     * @return mixed
     */
    public static function callEvent($event, $item){

        //�������� ��� �������� �������, ����������� � ���������� �������
        $plugins = self::getInstance()->getEventPlugins($event);

        //���� �������� �������� ���, ���������� ������� $item ��� ���������
        if (!$plugins) { return $item; }

        //���������� ������� � �������� ������ �� ���, ��������� ������� $item
        foreach($plugins as $plugin_name){

            $plugin = self::getInstance()->loadPlugin( $plugin_name );

            if ($plugin!==false){
                $item = $plugin->execute($event, $item);
                self::getInstance()->unloadPlugin($plugin);

                if ( in_array($plugin->info['type'], self::getInstance()->single_run_plugins)) {
                    return $item;
                }
            }
            
        }

        //��������� $item �������
        return $item;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ � ������� ��������, ����������� � ������� $event
     * @param string $event
     * @return array
     */
    public function getEventPlugins($event) {
        $inDB = cmsDatabase::getInstance();

        $plugins_sql = "SELECT p.plugin as plugin
                        FROM cms_plugins p, cms_event_hooks e
                        WHERE p.published = 1 AND e.plugin_id = p.id AND e.event = '{$event}'
                        LIMIT 10";

        $result = $inDB->query($plugins_sql);

        if ( !$inDB->num_rows($result) ) { return false; }

        $plugins_list = array();

        while($plugin = $inDB->fetch_assoc($result)){
            $plugins_list[] = $plugin['plugin'];
        }

        return $plugins_list;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������������� ������ � ������ ��� �������� � ��������
     * ���������� ID �������������� �������
     * @param array $plugin
     * @param array $events
     * @param array $config
     * @return int
     */
    public function installPlugin($plugin, $events, $config) {
        $inDB = cmsDatabase::getInstance();

        if (!$plugin['type']) { $plugin['type'] = 'plugin'; }

        $config_yaml    = $this->arrayToYaml($config);

        if (!$config_yaml) { $config_yaml = ''; }

        //��������� ������ � ����
        $install_query  = "INSERT INTO cms_plugins (id, plugin, title, description, author, version, plugin_type, published, config)
                           VALUE ('', '{$plugin['plugin']}', '{$plugin['title']}', '{$plugin['description']}', '{$plugin['author']}',
                                  '{$plugin['version']}', '{$plugin['type']}', 0, '{$config_yaml}')";

        $inDB->query($install_query);

        //�������� ID �������
        $plugin_id = $inDB->get_last_id('cms_plugins');

        //���������� ����, ���� ������ �� �����������
        if (!$plugin_id)    { return false; }

        //��������� ���� ������� ��� �������
        foreach($events as $event){
            $event_query = "INSERT INTO cms_event_hooks (event, plugin_id) VALUES ('{$event}', {$plugin_id})";
            $inDB->query($event_query);
        }

        //��������� ID �������������� �������
        return $plugin_id;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������ ������� �������������� �������
     * @param array $plugin
     * @param array $events
     * @param array $config
     * @return bool
     */
    public function upgradePlugin($plugin, $events, $config) {
        $inDB = cmsDatabase::getInstance();

        //������� ID ������������� ������
        $plugin_id = $this->getPluginId( $plugin['plugin'] );

        //���� ������ ��� �� ��� ����������, �������
        if (!$plugin_id) { return false; }

        //��������� ������� ��������� �������
        $old_config = $this->loadPluginConfig( $plugin['plugin'] );

        //������� ���������, ������� ������ �� �����
        foreach($old_config as $param=>$value){
            if ( !isset($config[$param]) ){
                unset($old_config[$param]);
            }
        }

        //��������� ���������, ������� ������ �� ����
        foreach($config as $param=>$value){
            if ( !isset($old_config[$param]) ){
                $old_config[$param] = $value;
            }
        }

        //������������ ������ �������� � YAML
        $config_yaml   = $this->arrayToYaml($old_config);

        //��������� ������ � ����
        $update_query  = "UPDATE cms_plugins
                          SET title='{$plugin['title']}',
                              description='{$plugin['description']}',
                              author='{$plugin['author']}',
                              version='{$plugin['version']}',
                              config='{$config_yaml}'
                          WHERE id = {$plugin_id}";

        $inDB->query($update_query);

        //��������� ����� ���� ������� ��� �������
        foreach($events as $event){
            if ( !$this->isPluginHook($plugin_id, $event) ){
                $event_query = "INSERT INTO cms_event_hooks (event, plugin_id) VALUES ('{$event}', {$plugin_id})";
                $inDB->query($event_query);
            }
        }

        //������ ������� ��������
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ������������� ������
     * @param array $plugin
     * @param array $events
     * @return bool
     */
    public function removePlugin($plugin_id) {
        $inDB = cmsDatabase::getInstance();

        //���� ������ �� ��� ����������, �������
        if (!$plugin_id) { return false; }

        //������� ������ �� ����
        $delete_query  = "DELETE FROM cms_plugins WHERE id = {$plugin_id}";

        $inDB->query($delete_query);

        //������� ���� ������� �������
        $unhook_query  = "DELETE FROM cms_event_hooks WHERE plugin_id = {$plugin_id}";

        $inDB->query($unhook_query);

        //������ ������� ������
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ��������, ��������� �� �����, �� �� �������������
     * @return array
     */
    public function getNewPlugins() {
        
        $inDB = cmsDatabase::getInstance();

        $new_plugins    = array();
        $all_plugins    = $this->getPluginsDirs();

        if (!$all_plugins) { return false; }

        foreach($all_plugins as $plugin){            
            $installed = $inDB->rows_count('cms_plugins', "plugin='{$plugin}'", 1);
            if (!$installed){
                $new_plugins[] = $plugin;
            }
        }

        if (!$new_plugins) { return false; }

        return $new_plugins;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ��������, ������ ������� ���������� � ������� �������
     * @return array
     */
    public function getUpdatedPlugins() {

        $upd_plugins    = array();
        $all_plugins    = $this->getPluginsDirs();

        if (!$all_plugins) { return false; }

        foreach($all_plugins as $plugin){
            $plugin_obj     = $this->loadPlugin($plugin);
            $version        = $this->getPluginVersion($plugin);
            if ($version){
                if ($version < $plugin_obj->info['version']){
                    $upd_plugins[] = $plugin;
                }
            }
        }

        if (!$upd_plugins) { return false; }

        return $upd_plugins;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ����� � ���������
     * @return array
     */
    public function getPluginsDirs() {
        $dir    = PATH . '/plugins';
        $pdir   = opendir($dir);

        $plugins = array();

        while ($nextfile = readdir($pdir)){
            if (
                    ($nextfile != '.')  &&
                    ($nextfile != '..') &&
                    is_dir($dir.'/'.$nextfile) &&
                    ($nextfile!='.svn') &&
                    (substr($nextfile, 0, 2)=='p_')
               ) {
                $plugins[$nextfile] = $nextfile;
            }
        }

        if (!sizeof($plugins)){ return false; }

        return $plugins;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ID ������� �� ��������
     * @param string $plugin
     * @return int
     */
    public function getPluginId($plugin){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_plugins', "plugin='{$plugin}'", 'id');
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� ������� �� ID
     * @param int $plugin_id
     * @return string
     */
    public function getPluginById($plugin_id){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_plugins', "id='{$plugin_id}'", 'plugin');

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ������� �� ��������
     * @param string $plugin
     * @return float
     */
    public function getPluginVersion($plugin){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_plugins', "plugin='{$plugin}'", 'version');

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������������� ���������
     * ���������� ID �������������� �������
     * @param array $component
     * @param array $config
     * @return int
     */
    public function installComponent($component, $config) {
        $inDB = cmsDatabase::getInstance();

        $config_yaml    = $this->arrayToYaml($config);

        if (!$config_yaml) { $config_yaml = ''; }

        //��������� ��������� � ����
        $install_query  = "INSERT INTO cms_components (`title`, `link`, `config`, `internal`, `author`, `published`, `version`, `system`)
                           VALUES ('{$component['title']}', '{$component['link']}', '{$config_yaml}', '{$component['internal']}',
                                    '{$component['author']}', '1', '{$component['version']}', '0')";

        $inDB->query($install_query);

        //�������� ID ����������
        $component_id = $inDB->get_last_id('cms_components');

        //���������� ����, ���� ��������� �� �����������
        if (!$component_id)    { return false; }

        //��������� ID �������������� ����������
        return $component_id;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������ ������� �������������� ����������
     * @param array $component
     * @param array $config
     * @return bool
     */
    public function upgradeComponent($component, $config) {
        $inDB = cmsDatabase::getInstance();

        //������� ID ������������� ������
        $component_id = $this->getComponentId( $component['link'] );

        //���� ��������� ��� �� ��� ����������, �������
        if (!$component_id) { return false; }

        //��������� ������� ��������� ����������
        $old_config = $this->loadComponentConfig( $component['link'] );

        //������� ���������, ������� ������ �� �����
        foreach($old_config as $param=>$value){
            if ( !isset($config[$param]) ){
                unset($old_config[$param]);
            }
        }

        //��������� ���������, ������� ������ �� ����
        foreach($config as $param=>$value){
            if ( !isset($old_config[$param]) ){
                $old_config[$param] = $value;
            }
        }

        //������������ ������ �������� � YAML
        $config_yaml   = $this->arrayToYaml($old_config);

        //��������� ��������� � ����
        $update_query  = "UPDATE cms_components
                          SET title='{$component['title']}',
                              author='{$component['author']}',
                              version='{$component['version']}',
                              internal='{$component['internal']}',
                              config='{$config_yaml}'
                          WHERE id = {$component_id}";

        $inDB->query($update_query);

        //��������� ������� ��������
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ������������� ���������
     * @param int $component_id
     * @return bool
     */
    public function removeComponent($component_id) {

        $inDB = cmsDatabase::getInstance();

        //���� ��������� �� ��� ����������, �������
        if (!$component_id) { return false; }

        //������� ��������� �� ����, �� ������ ���� �� �� ���������
        $delete_query  = "DELETE FROM cms_components WHERE id = {$component_id} AND system = 0";

        $inDB->query($delete_query);

        //��������� ������� ������
        return true;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ �����������, ��������� �� �����, �� �� �������������
     * @return array
     */
    public function getNewComponents() {

        $inDB = cmsDatabase::getInstance();

        $new_components    = array();
        $all_components    = $this->getComponentsDirs();

        if (!$all_components) { return false; }

        foreach($all_components as $component){

            $installer_file = PATH . '/components/' . $component . '/install.php';

            if (file_exists($installer_file)){

                $installed = $inDB->rows_count('cms_components', "link='{$component}'", 1);
                if (!$installed){
                    $new_components[] = $component;
                }

            }
            
        }

        if (!$new_components) { return false; }

        return $new_components;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ �����������, ������ ������� ���������� � ������� �������
     * @return array
     */
    public function getUpdatedComponents() {

        $inDB = cmsDatabase::getInstance();

        $upd_components    = array();
        $all_components    = $inDB->get_table('cms_components');

        if (!$all_components) { return false; }

        foreach($all_components as $component){            
            if($this->loadComponentInstaller($component['link'])){
                $version    = $component['version'];
                $_component = call_user_func('info_component_'.$component['link']);
                if ($version){
                    if ($version < $_component['version']){
                        $upd_components[] = $component['link'];
                    }
                }
            }
        }

        if (!$upd_components) { return false; }

        return $upd_components;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ����� � ������������
     * @return array
     */
    public function getComponentsDirs() {
        $dir    = PATH . '/components';
        $pdir   = opendir($dir);

        $components = array();

        while ($nextfile = readdir($pdir)){
            if (
                    ($nextfile != '.')  &&
                    ($nextfile != '..') &&
                    is_dir($dir.'/'.$nextfile) &&
                    ($nextfile!='.svn')
               ) {
                $components[$nextfile] = $nextfile;
            }
        }

        if (!sizeof($components)){ return false; }

        return $components;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ID ���������� �� ��������
     * @param string $component
     * @return int
     */
    public function getComponentId($component){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_components', "link='{$component}'", 'id');

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� ���������� �� ID
     * @param int $component_id
     * @return string
     */
    public function getComponentById($component_id){

        $inDB = cmsDatabase::getInstance();

        $link = $inDB->get_field('cms_components', "id={$component_id}", 'link');

        return $link;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ���������� �� ��������
     * @param string $component
     * @return float
     */
    public function getComponentVersion($component){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_components', "link='{$component}'", 'version');

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function loadComponentInstaller($component){

        $installer_file = PATH . '/components/' . $component . '/install.php';

        if (!file_exists($installer_file)){ return false; }

        $this->includeFile('components/'.$component.'/install.php');

        return true;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������������� ������
     * ���������� ID �������������� ������
     * @param array $module
     * @param array $config
     * @return int
     */
    public function installModule($module, $config) {

        $inDB = cmsDatabase::getInstance();

        $config_yaml    = $this->arrayToYaml($config);

        if (!$config_yaml) { $config_yaml = ''; }

        //��������� ������ � ����
        $install_query  = "INSERT INTO cms_modules (`position`, `name`, `title`, `is_external`,
                                                    `content`, `ordering`, `showtitle`, `published`,
                                                    `user`, `config`, `original`, `css_prefix`,
                                                    `allow_group`, `cache`, `cachetime`, `cacheint`,
                                                    `template`, `is_strict_bind`, `version`)
                VALUES ('{$module['position']}', '{$module['name']}', '{$module['title']}', '1',
                        '{$module['link']}', '1', '1', '1',
                        '0', '{$config_yaml}', '1', '',
                        '-1', '0', '1', 'HOUR',
                        'module.tpl', '0', '{$module['version']}')";

        $inDB->query($install_query);

        //�������� ID ������
        $module_id = $inDB->get_last_id('cms_modules');

        //���������� ����, ���� ������ �� �����������
        if (!$module_id)    { return false; }

        //��������� ID �������������� ������
        return $module_id;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������ ������� �������������� ������
     * @param array $component
     * @param array $config
     * @return bool
     */
    public function upgradeModule($module, $config) {
        $inDB = cmsDatabase::getInstance();

        //������� ID ������������� ������
        $module_id = $this->getModuleId( $module['link'] );

        //���� ������ ��� �� ��� ����������, �������
        if (!$module_id) { return false; }

        //��������� ������� ��������� ������
        $old_config = $this->loadModuleConfig( $module_id );

        //������� ���������, ������� ������ �� �����
        foreach($old_config as $param=>$value){
            if ( !isset($config[$param]) ){
                unset($old_config[$param]);
            }
        }

        //��������� ���������, ������� ������ �� ����
        foreach($config as $param=>$value){
            if ( !isset($old_config[$param]) ){
                $old_config[$param] = $value;
            }
        }

        //������������ ������ �������� � YAML
        $config_yaml   = $this->arrayToYaml($old_config);

        //��������� ������ � ����
        $update_query  = "UPDATE cms_modules
                          SET title='{$module['title']}',
                              name='{$module['name']}',
                              version='{$module['version']}',
                              config='{$config_yaml}'
                          WHERE id = {$module_id}";

        $inDB->query($update_query);

        //������ ������� ��������
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ������������� ������
     * @param int $module_id
     * @return bool
     */
    public function removeModule($module_id) {

        $inDB = cmsDatabase::getInstance();

        //���� ������ �� ��� ����������, �������
        if (!$module_id) { return false; }

        //������� ������ �� ����
        $delete_query  = "DELETE FROM cms_modules WHERE id = {$module_id}";

        $inDB->query($delete_query);

        //������ ������� ������
        return true;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ �������, ��������� �� �����, �� �� �������������
     * @return array
     */
    public function getNewModules() {

        $inDB = cmsDatabase::getInstance();

        $new_modules    = array();
        $all_modules    = $this->getModulesDirs();

        if (!$all_modules) { return false; }

        foreach($all_modules as $module){

            $installer_file = PATH . '/modules/' . $module . '/install.php';

            if (file_exists($installer_file)){

                $installed = $inDB->rows_count('cms_modules', "content='{$module}' AND user=0", 1);
                if (!$installed){
                    $new_modules[] = $module;
                }

            }

        }

        if (!$new_modules) { return false; }

        return $new_modules;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ �������, ������ ������� ���������� � ������� �������
     * @return array
     */
    public function getUpdatedModules() {

        $inDB = cmsDatabase::getInstance();

        $upd_modules    = array();
        $all_modules    = $inDB->get_table('cms_modules', 'user=0');

        if (!$all_modules) { return false; }

        foreach($all_modules as $module){
            if($this->loadModuleInstaller($module['content'])){
                $version    = $module['version'];
                $_module    = call_user_func('info_module_'.$module['content']);
                if ($version){
                    if ($version < $_module['version']){
                        $upd_modules[] = $module['content'];
                    }
                }
            }
        }

        if (!$upd_modules) { return false; }

        return $upd_modules;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ����� � ��������
     * @return array
     */
    public function getModulesDirs() {
        $dir    = PATH . '/modules';
        $pdir   = opendir($dir);

        $modules = array();

        while ($nextfile = readdir($pdir)){
            if (
                    ($nextfile != '.')  &&
                    ($nextfile != '..') &&
                    is_dir($dir.'/'.$nextfile) &&
                    ($nextfile!='.svn')
               ) {
                $modules[$nextfile] = $nextfile;
            }
        }

        if (!sizeof($modules)){ return false; }

        return $modules;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ID ������ �� ��������
     * @param string $component
     * @return int
     */
    public function getModuleId($module){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_modules', "content='{$module}' AND user=0", 'id');

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� ������ �� ID
     * @param int $component_id
     * @return string
     */
    public function getModuleById($module_id){

        $inDB = cmsDatabase::getInstance();

        $link = $inDB->get_field('cms_modules', "id={$module_id} AND user=0", 'content');

        return $link;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ������ �� ��������
     * @param string $component
     * @return float
     */
    public function getModuleVersion($module){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_modules', "content='{$module}' AND user=0", 'version');

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function loadModuleInstaller($module){

        $installer_file = PATH . '/modules/' . $module . '/install.php';

        if (!file_exists($installer_file)){ return false; }

        $this->includeFile('modules/'.$module.'/install.php');

        return true;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ����������� ������� � ���� �������
     * @param string $plugin
     * @return float
     */
    public function loadPluginConfig($plugin){

        $inDB = cmsDatabase::getInstance();

        $config_yaml = $inDB->get_field('cms_plugins', "plugin='{$plugin}'", 'config');

        return $this->yamlToArray($config_yaml);

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ��������� ������� � ����
     * @param string $plugin_name
     * @param array $config
     * @return bool
     */
    public function savePluginConfig($plugin_name, $config) {

        $inDB = cmsDatabase::getInstance();

        //������������ ������ �������� � YAML
        $config_yaml   = $this->arrayToYaml($config);

        //��������� ������ � ����
        $update_query  = "UPDATE cms_plugins
                          SET config='{$config_yaml}'
                          WHERE plugin = '{$plugin_name}'";

        $inDB->query($update_query);

        //��������� ������� ���������
        return true;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� �������� ������� � �������
     * @param int $plugin_id
     * @param string $event
     * @return bool
     */
    public function isPluginHook($plugin_id, $event) {

        $inDB = cmsDatabase::getInstance();

        return (bool)$inDB->num_rows('cms_event_hooks', "plugin_id={$plugin_id} AND event='{$event}'", 1);

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������ � ���������� ��� ������
     * @param string $plugin
     * @return cmsPlugin
     */
    public function loadPlugin($plugin) {
        $plugin_file = PATH.'/plugins/'.$plugin.'/plugin.php';
        if (file_exists($plugin_file)){
            include_once($plugin_file);
            $plugin_obj = new $plugin();
            return $plugin_obj;
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ �������
     * @param cmsPlugin $plugin_obj
     * @return true
     */
    public function unloadPlugin($plugin_obj) {
        unset($plugin_obj);
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ���������� �� ����� /core/lib_XXX.php, ��� XXX = $lib
     * @param string $lib
     * @return bool
     */
    public function loadLib($lib){
        $libfile = PATH.'/core/lib_'.$lib.'.php';
        if (file_exists($libfile)){
            include_once($libfile);
            return true;
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ����� �� ����� /core/classes/XXX.class.php, ��� XXX = $class
     * @param string $class
     * @return bool
     */
    public function loadClass($class){
        $classfile = PATH.'/core/classes/'.$class.'.class.php';
        if (file_exists($classfile)){
            include_once($classfile);
            return true;
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������ ��� ���������� ����������
     * @param string $component
     * @return bool
     */
    public function loadModel($component){
        $modelfile = PATH.'/components/'.$component.'/model.php';
        if (file_exists($modelfile)){
            include_once($modelfile);
            return true;
        }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������� ����
     * @param string $lib
     */
    public function includeFile($file){
        include_once PATH.'/'.$file;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������� ��� ������ � ��������
     */
    public function includeGraphics(){
        include_once PATH.'/includes/graphic.inc.php';
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ���� ������������
     */
    public function includeConfig(){
        include_once PATH.'/includes/config.inc.php';
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ���������� ��������
     */
    public function includeWysiwyg(){
        include_once PATH."/wysiwyg/fckeditor.php";
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function insertEditor($name, $text='', $height='350', $width='500') {

        $editor = self::callEvent('INSERT_WYSIWYG', array(
                                                        'name'=>$name,
                                                        'text'=>$text,
                                                        'height'=>$height,
                                                        'width'=>$width
                                                    ));

        if (!is_array($editor)){ echo $editor; return; }

        echo '<p>
                <div>���������� �������� �� ������ ���� �� �������.</div>
                <div>���� �������� ����������, �������� ��� � ������� (���� <em>����������</em> &rarr; <em>�������</em>).</div>
              </p>';

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������������� ����� ����������
     * @param string $name
     * @param string $value
     * @param int $time
     */
    public function setCookie($name, $value, $time){
        setcookie('InstantCMS['.$name.']', $value, $time, '/');        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ����� ������������
     * @param string $name
     */
    public function unsetCookie($name){
        setcookie('InstantCMS['.$name.']', '', time()-3600, '/');
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� ������
     * @param string $name
     * @return string || false
     */
    public function getCookie($name){
        if (isset($_COOKIE['InstantCMS'][$name])){
            return $_COOKIE['InstantCMS'][$name];
        } else {
            return false;
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ��������� � ������
     * @param string $message
     * @param string $class
     */
    public static function addSessionMessage($message, $class='info'){
        $_SESSION['core_message'][] = '<div class="message_'.$class.'">'.$message.'</div>';
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /*
     * ���������� ������ ��������� ����������� � ������
     */
    public static function getSessionMessages(){

        if (isset($_SESSION['core_message'])){
            $messages = $_SESSION['core_message'];
        } else {
            $messages = false;
        }

        self::clearSessionMessages();
        return $messages;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /*
     * ������� ������� ��������� ������
     */
    public static function clearSessionMessages(){
        unset($_SESSION['core_message']);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ���������� ��������� �����
     * @global array $_CFG
     */
    public function onlineStats(){
        
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
        global $_CFG;

        $bots = array();
        $bots['Aport']              ='Aport';
        $bots['msnbot']             ='MSNbot';
        $bots['Yandex']             ='Yandex';
        $bots['Lycos.com']          ='Lucos';
        $bots['Googlebot']          ='Google';
        $bots['Openbot']            ='Openfind';
        $bots['FAST-WebCrawler']    ='AllTheWeb';
        $bots['TurtleScanner']      ='TurtleScanner';
        $bots['Yahoo-MMCrawler']    ='Y!MMCrawler';
        $bots['Yahoo!']             ='Yahoo!';
        $bots['rambler']            ='Rambler';
        $bots['W3C_Validator']      ='W3C Validator';

        //������� ������ ������
        $sql = "DELETE FROM cms_online WHERE lastdate <= DATE_SUB(NOW(), INTERVAL 3 MINUTE)";
        $inDB->query($sql) ;

        if (!$inUser->checkStatTimer()){ return true; }

        $inUser->resetStatTimer();

        //�������� ���������� � ������� ������������
        $sess_id    = session_id();
        $ip         = $this->strClear($_SERVER['REMOTE_ADDR']);
        $useragent  = $this->strClear($_SERVER['HTTP_USER_AGENT']);
        $page       = $this->strClear($_SERVER['REQUEST_URI']);
        $refer      = $this->strClear($_SERVER['HTTP_REFERER']);

        $user_id    = $inUser->id;

        if (strstr(strtolower($useragent), 'select')) { return false; }
        if (strstr(strtolower($useragent), 'from'))   { return false; }

        //���������, ���� �� ������� ������������ � ������� "��� ������"
        $sql = "SELECT id FROM cms_online WHERE (sess_id = '$sess_id' AND ip = '$ip')";
        $result = $inDB->query($sql) ;

        if (!$inDB->num_rows($result)){
            //���������, ������������ ��� ��� ��������� ���
            $crawler = false;
            foreach($bots as $bot=>$uagent){ if (strpos($useragent, $uagent)) { $crawler = true; }	}
            //���� �� ���, ��������� ������ � "��� ������"
            if (!$crawler){
                $sql = "INSERT INTO cms_online (ip, sess_id, lastdate, user_id, viewurl) VALUES ('$ip', '$sess_id', NOW(), '$user_id', '$page')";
                $inDB->query($sql) ;
            }
        } else {
            //���� ������������ ��� ������, ��������� �����
            $sql = "UPDATE cms_online
                     SET lastdate = NOW(),
                         user_id = '$user_id',
                         viewurl = '$page'
                     WHERE (sess_id = '$sess_id' AND ip = '$ip')";
            $inDB->query($sql) ;
        }

        if (@$_CFG['stats']){ //���� ������� ���� ���������� �� �����
            //�������, ���� �� ������ ��� �������� ������������
            $sql = "SELECT id FROM cms_stats WHERE (ip = '$ip' AND page = '$page')";
            $result = $inDB->query($sql) ;
            //���� ������ ��� - ���������
            if (!$inDB->num_rows($result)){
                $sql = "INSERT INTO cms_stats (ip, logdate, page, agent, refer) VALUES ('$ip', NOW(), '$page', '$useragent', '$refer')";
                $inDB->query($sql) ;
            }
        }

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������� URI
     * ����� ��� ����, ����� ����� ����������� �������������� URI.
     * �� ���� �������� ���������� ����������� mod_rewrite
     * @return string
     */
    private function detectURI(){

        $uri    = $this->request('uri', 'str', '');
        $rules  = array();

        //����������� ��� ��� ������ �� �����, ��� ������������� �� ������� ���������
        if (strstr($_SERVER['QUERY_STRING'], 'view=search')){ $uri = 'search'; }

        if(file_exists(PATH.'/url_rewrite.php')) {
            //���������� ������ rewrite-������
            $this->includeFile('url_rewrite.php');
            if(function_exists('rewrite_rules')){
                //�������� �������
                $rules = rewrite_rules();
            }
        }

        if(file_exists(PATH.'/custom_rewrite.php')) {
            //���������� ������ ���������������� rewrite-������
            $this->includeFile('custom_rewrite.php');
            if(function_exists('custom_rewrite_rules')){
                //��������� � ���������� ����� �������� ����������������
                $rules = array_merge($rules, custom_rewrite_rules());
            }
        }

        $found = false;

        if ($rules){
            //���������� �������
            foreach($rules as $rule_id=>$rule) {
                //��������� ��������� �������
                if (!$rule['source'] || !$rule['target'] || !$rule['action']) { continue; }
                //��������� ���������� ��������� source � ������� uri
                if (preg_match($rule['source'], $uri, $matches)){

                    //���������� ��������� �������� � ��������� �� � target
                    //����� ��������� ��������� �� $uri � ����� ������
                    foreach($matches as $key=>$value){
                        if (!$key) { continue; }
                        if (strstr($rule['target'], '{'.$key.'}')){
                            $rule['target'] = str_replace('{'.$key.'}', $value, $rule['target']);
                        }
                    }

                    //�������� ��-���������: rewrite
                    if (!$rule['action']) { $rule['action'] = 'rewrite'; }

                    //��������� ��������
                    switch($rule['action']){
                        case 'rewrite'      : $uri = $rule['target']; $found = true; break;
                        case 'redirect'     : $this->redirect($rule['target']); break;
                        case 'redirect-301' : $this->redirect($rule['target'], '301'); break;
                        case 'alias'        : $this->includeFile($rule['target']); $this->halt();break;
                    }

                }

                if ($found) { break; }

            }
        }

        return $uri;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������� ���������
     * ���������, ��� ��������� ������ � ������ �������� URI,
     * ����� ������������ ��������� ��� ������� ��������
	 * �������� "������������" ���������� ������������ � ������� loadComponentConfig
     * @return string $component
     */
    private function detectComponent(){

        $inConf     = cmsConfig::getInstance();

        $component  = '';

        //��������� �� �������
        if (!$this->uri && $inConf->homecom) { return $inConf->homecom; }

        //����������, ���� �� ����� � ������
        $first_slash_pos = strpos($this->uri, '/');

        if ($first_slash_pos){
            //���� ���� �����, �� ��������� ��� ������� �� ������� �����
            $component  = substr($this->uri, 0, $first_slash_pos);
        } else {
            //���� ������ ���, �� ��������� ��������� � �������
            $component  = $this->uri;            
        }


        if (is_dir(PATH.'/components/'.$component)){
            //���� ��������� ��������� � ����������
            return $component;
        } else {
            //���� ��������� �� ����������, ������� ��� ��� content
            $this->uri = 'content/'.$this->uri;
            $this->is_content = true;
            return 'content';
        }
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ���������� ���� router.php �� ����� � ������� �����������
     * � �������� ����� route_component(), ������� ���������� ������ ������
     * ��� ������� URI. ���� � ������� ������� ���������� � ������� URI,
     * �� URI �������� � ����������, ������������ � ���, ���������� � ������ $_REQUEST.
     * @return boolean
     */
    private function parseComponentRoute(){

        $component = $this->component;

        //��������� ��� ��������� � ����� �������
        if (!$component || !$this->uri) { return false; }

        if(!file_exists('components/'.$component.'/router.php')){ return false; }
		/**
		 * �������� "������������" ���������� ������������ � ������� loadComponentConfig
		 */
        //���������� ������ ��������� ����������
        $this->includeFile('components/'.$component.'/router.php');

        $routes = call_user_func('routes_'.$component);

        //���������� ��� ��������
        foreach($routes as $route_id=>$route){

            //���������� ������ �������� � ������� URI
            preg_match($route['_uri'], $this->uri, $matches);

            //���� ������� ����������
            if ($matches){

                //������� ������ �� ���������� ��������, ����� �� ����� ��� ��������
                unset($route['_uri']);

                //���������� ��������� �������� � ���� ����=>��������
                foreach($route as $key=>$value){
                    if (is_integer($key)){
                        //���� ���� - ����� �����, �� ��������� �������� ������� URI
                        $_REQUEST[$value] = $matches[$key];
                    } else {
                        //�����, �������� ������� �� ��������
                        $_REQUEST[$key]   = $value;
                    }
                }

                //��� ������� ����������, ��������� ����
                break;

            }

        }

        return true;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ���� ��������, ������� ������ ���������
     */
    public function proceedBody(){

        $inPage         = cmsPage::getInstance();
        $inDB           = cmsDatabase::getInstance();        
        $inConf         = cmsConfig::getInstance();
        $is_component   = false;
        $component      = $this->component;

        //��������� ��� ��������� ������
        if (!$component) { return false; }
		/**
		 * �������� "������������" ���������� ������������ � ������� loadComponentConfig
		 */
        //��������� ��� � �������� ������ ����� � �����
        if (!preg_match("/^([a-z0-9])+$/", $component)){ cmsCore::error404(); }
        
        $this->loadLanguage('components/'.$component);

        //��������� ������� ����������
        if(!file_exists('components/'.$component.'/frontend.php')){
            $inPage->page_body = '<p>��������� �� ������!</p>';
            return false;
        }

        //������ ����� � ��������� ������ $_REQUEST (��������� �������)
        $this->parseComponentRoute();

        ob_start();

            require('components/'.$component.'/frontend.php');
            call_user_func($component);

        if ($inConf->back_btn && $menuid != 1 && $inPage->back_button) {
            echo "<p><a href='javascript:history.go(-1)' class=\"backlink\">&larr; �����</a></p>";
        }

        $component_html = ob_get_clean();

        $inPage->page_body = '<div class="component">' . $component_html . '</div>';

        if ($is_component) { $inPage->page_body = cmsCore::callEvent('AFTER_COMPONENT_'.mb_strtoupper($component), $inPage->page_body); }

        return true;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function error404(){

        header("HTTP/1.0 404 Not Found");
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");

        $inConf = cmsConfig::getInstance();
        $inPage = cmsPage::getInstance();
        $inCore = self::getInstance();

        if (!$inPage->includeTemplateFile('special/error404.php')){
            echo '<h1>404</h1>';
        }
        
        $inCore->halt();

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * �������������� ��������� ��������� � ���������� ������ CCelkoNastedSet
     * @return object NS
     */
    public function nestedSetsInit($table){
        $inDB = cmsDatabase::getInstance();
        $this->includeFile('includes/nestedsets.php');
        $ns = new CCelkoNastedSet();
        $ns->MyLink     = $inDB->db_link;
        $ns->TableName  = $table;
        return $ns;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������, ����� �� ���������� �����-�������� (�����������)
     * @global array $_CFG
     * @return bool
     */
    public function isSplash(){
        $inConf = cmsConfig::getInstance();        
        if ($inConf->splash){
            $show_splash = !($this->getCookie('splash') || isset($_SESSION['splash']));
            return $show_splash;
        } else { return false; }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� ����� ��� ��������� ������
     * @param string $text
     * @return string
     */
    public function getKeywords($text){
        $this->includeFile('includes/keywords.inc.php');
        $params['content'] = $text; //page content
        $params['min_word_length'] = 5;  //minimum length of single words
        $params['min_word_occur'] = 2;  //minimum occur of single words

        $params['min_2words_length'] = 5;  //minimum length of words for 2 word phrases
        $params['min_2words_phrase_length'] = 10; //minimum length of 2 word phrases
        $params['min_2words_phrase_occur'] = 2; //minimum occur of 2 words phrase
        
        $params['min_3words_length'] = 5;  //minimum length of words for 3 word phrases
        $params['min_3words_phrase_length'] = 10; //minimum length of 3 word phrases
        $params['min_3words_phrase_occur'] = 2; //minimum occur of 3 words phrase
        $keyword = new autokeyword($params, "cp1251");
        return $keyword->get_keywords();
    }

    // REQUESTS /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������� ���������� $var �� ������� ����������
     * @param string $var
     * @return bool
     */
    public function inRequest($var){
        return isset($_REQUEST[$var]);
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������� ���������� $var �� ������� ����������
     * @param string $var
     * @param string $type = int | str | html
     * @param string $default
     */
    public function request($var, $type='str', $default=false){
        if (isset($_REQUEST[$var])){
            switch($type){
                case 'int':   return (int)$_REQUEST[$var]; break;
                case 'str':   if ($_REQUEST[$var]) { return $this->strClear($_REQUEST[$var]); } else { return $default; } break;
                case 'email': if(preg_match("/^([a-zA-Z0-9\._-]+)@([a-zA-Z0-9\._-]+)\.([a-zA-Z]{2,4})$/i", $_REQUEST[$var])){ return $_REQUEST[$var]; } else { return $default; } break;
                case 'html':  if ($_REQUEST[$var]) { return $this->strClear($_REQUEST[$var], false); } else { return $default; } break;
                case 'array': if (is_array($_REQUEST[$var])) { return $_REQUEST[$var]; } else { return $default; } break;
                case 'array_int': if (is_array($_REQUEST[$var])) { foreach($_REQUEST[$var] as $k=>$i){ $arr[$k] = (int)$i; } return $arr; } else { return $default; } break;
                case 'array_str': if (is_array($_REQUEST[$var])) { foreach($_REQUEST[$var] as $k=>$s){ $arr[$k] = $this->strClear($s); } return $arr; } else { return $default; } break;
            }
        } else {
            return $default;
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function redirectBack(){
        header('Location:'.$_SERVER['HTTP_REFERER']);
        $this->halt();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function redirect($url, $code='303'){
        if ($code == '301'){
            header('HTTP/1.1 301 Moved Permanently');
        } else {
            header('HTTP/1.1 303 See Other');
        }
        header('Location:'.$url);
        $this->halt();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ���������� URL ��� ��������� �����. ���� ������� ���������� $_REQUEST['back'], �� ���������� ��
     * @return string
     */
    public function getBackURL(){
        if($this->inRequest('back')){
            $back = $this->request('back');
        } else {
            if (isset($_SERVER['HTTP_REFERER'])){
                $back = $_SERVER['HTTP_REFERER'];
            } else { $back = "/"; }
        }
        return $back;
    }

    // FILE UPLOADING //////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ���� �� ������ � ����������� ������
     * @param string $source
     * @param string $destination
     * @param int $errorCode
     * @return bool
     */
    public function moveUploadedFile($source, $destination, $errorCode){
        $max_size = ini_get('upload_max_filesize');
        $max_size = str_replace('M', '��', $max_size);
        $max_size = str_replace('K', '��', $max_size);

        //Possible upload errors
        $uploadErrors = array(
            UPLOAD_ERR_OK => '���� ������� ��������',
            UPLOAD_ERR_INI_SIZE => '������ ����� ��������� ���������� &mdash; '.$max_size,
            UPLOAD_ERR_FORM_SIZE => '������ ����� ��������� ����������',
            UPLOAD_ERR_PARTIAL => '���� ��� �������� �� ���������',
            UPLOAD_ERR_NO_FILE => '���� �� ��� ��������',
            UPLOAD_ERR_NO_TMP_DIR => '�� ������� ����� ��� ��������� ������ �� �������',
            UPLOAD_ERR_CANT_WRITE => '������ ������ ����� �� ����',
            UPLOAD_ERR_EXTENSION => '�������� ����� ���� �������� ����������� PHP'
        );

        if($errorCode !== UPLOAD_ERR_OK && isset($uploadErrors[$errorCode])){
            //if is error, save it and return false
            $_SESSION['file_upload_error'] = $uploadErrors[$errorCode];
            return false;
        } else {
            //clear error, if upload is ok
            $_SESSION['file_upload_error'] = '';
            //get upload directory and check it is writable
            $upload_dir = dirname($destination);
            if (!is_writable($upload_dir)){	@chmod($upload_dir, 0644); @chmod($upload_dir, 0755); }
            //move uploaded file
            return @move_uploaded_file($source, $destination);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function uploadError(){
        if ($_SESSION['file_upload_error']){ return $_SESSION['file_upload_error']; } else { return false; }
    }

    // SMARTY //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ����� Smarty
     */
    public function loadSmarty(){
        $this->includeFile('/includes/smarty/libs/Smarty.class.php');
        $this->smarty = new Smarty();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ Smarty ��� ���������� ������ � ��������
     * @param string $tpl_folder = modules / components / plugins
     * @param string $tpl_file
     * @return obj
     */
    public function initSmarty($tpl_folder='modules', $tpl_file=''){

        global $_LANG;

        if (!$this->smarty){ $this->loadSmarty(); }

        $this->smarty->compile_dir = PATH.'/cache';

        $template_has_tpl = file_exists(TEMPLATE_DIR . "{$tpl_folder}/{$tpl_file}");

        $this->smarty->template_dir = $template_has_tpl ? TEMPLATE_DIR . $tpl_folder : DEFAULT_TEMPLATE_DIR . $tpl_folder;

        $this->smarty->assign('LANG', $_LANG);
        $this->smarty->register_modifier("NoSpam", "cmsSmartyNoSpam");
        $this->smarty->register_function('add_js', 'cmsSmartyAddJS');
        $this->smarty->register_function('add_css', 'cmsSmartyAddCSS');
        $this->smarty->register_function('wysiwyg', 'cmsSmartyWysiwyg');
        $this->smarty->register_function('comments', 'cmsSmartyComments');
        $this->smarty->register_function('profile_url', 'cmsSmartyProfileURL');

        return $this->smarty;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function initSmartyModule(){

        if (!$this->smarty){ $this->loadSmarty(); }

        $this->smarty->compile_dir = PATH.'/cache';

        $template_has_dir = is_dir(TEMPLATE_DIR.'modules');

        $this->smarty->template_dir = $template_has_dir ? TEMPLATE_DIR.'modules' : DEFAULT_TEMPLATE_DIR.'modules';

        $this->smarty->register_modifier("NoSpam", "cmsSmartyNoSpam");
        $this->smarty->register_function('add_js', 'cmsSmartyAddJS');
        $this->smarty->register_function('add_css', 'cmsSmartyAddCSS');
        $this->smarty->register_function('wysiwyg', 'cmsSmartyWysiwyg');
        $this->smarty->register_function('profile_url', 'cmsSmartyProfileURL');

        return $this->smarty;
    }

    // CONFIGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ � ����������� ������
     * @param int $module_id
     * @return array
     */
    public function loadModuleConfig($module_id){
        
        $inDB = cmsDatabase::getInstance();

        $config = array();

        if (isset($this->module_configs[$module_id])) { return $this->module_configs[$module_id]; }

        $config_yaml = $inDB->get_field('cms_modules', "id='{$module_id}'", 'config');

        $config = $this->yamlToArray($config_yaml);

        $this->cacheModuleConfig($module_id, $config);

        return $config;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ��������� ������ � ����
     * @param string $plugin_name
     * @param array $config
     * @return bool
     */
    public function saveModuleConfig($module_id, $config) {

        $inDB = cmsDatabase::getInstance();

        //������������ ������ �������� � YAML
        $config_yaml   = $this->arrayToYaml($config);

        //��������� ������ � ����
        $update_query  = "UPDATE cms_modules 
                          SET config='{$config_yaml}'
                          WHERE id = {$module_id}";

        $inDB->query($update_query);

        //��������� ������� ���������
        return true;

    }

    /**
     * �������� ������������ ������ �� ����� ���������� �������
     * @param int $module_id
     * @param array $config
     * @return boolean
     */
    public function cacheModuleConfig($module_id, $config){
        $this->module_configs[$module_id] = $config;
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ����������� ���������� � ���� �������
     * @param string $plugin
     * @return float
     */
    public function loadComponentConfig($component){

        $inDB = cmsDatabase::getInstance();

        $config = array();

        if (isset($this->component_configs[$component])) { return $this->component_configs[$component]; }

        $config_yaml = $inDB->get_field('cms_components', "link='{$component}' AND published = 1", 'config');

		if (!$config_yaml) { $this->error404(); }

        $config = $this->yamlToArray($config_yaml);

        $this->cacheComponentConfig($component, $config);

        return $config;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ��������� ���������� � ����
     * @param string $plugin_name
     * @param array $config
     * @return bool
     */
    public function saveComponentConfig($component, $config) {

        $inDB = cmsDatabase::getInstance();

        //������������ ������ �������� � YAML
        $config_yaml   = $this->arrayToYaml($config);

        //��������� ������ � ����
        $update_query  = "UPDATE cms_components
                          SET config='{$config_yaml}'
                          WHERE link = '{$component}'";

        $inDB->query($update_query);

        //��������� ������� ���������
        return true;

    }

    /**
     * �������� ������������ ���������� �� ����� ���������� �������
     * @param string $component
     * @param array $config
     * @return boolean
     */
    public function cacheComponentConfig($component, $config){
        $this->component_configs[$component] = $config;
        return true;
    }


    // FILTERS //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ � �������������� � ������� ���������
     * @return array or false
     */
    public function getFilters(){
        $inDB   = cmsDatabase::getInstance();
        $sql    = "SELECT * FROM cms_filters WHERE published = 1 ORDER BY id ASC";
        $result = $inDB->query($sql);
        if($inDB->num_rows($result)){
            $filters = array();
            while($f = $inDB->fetch_assoc($result)){
                $filters[$f['id']] = $f;
            }
            return $filters;
        } else { return false; }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function processFilters(&$content) {

        $filters = $this->getFilters();

        if ($filters){
            foreach($filters as $id=>$_filter){
                $this->includeFile('filters/'.$_filter['link'].'/filter.php');
                $_filter['link']($content);
            }
        }

        return true;

    }

    // FILE DOWNLOADS STATS /////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ���������� �������� �����
     * @param string $fileurl
     * @return int
     */
    public function fileDownloadCount($fileurl){
        
        $inDB       = cmsDatabase::getInstance();
        $fileurl    = mysql_escape_string($fileurl);
        
        $sql        = "SELECT hits FROM cms_downloads WHERE fileurl = '$fileurl'";
        $result     = $inDB->query($sql) ;
        
        if ($inDB->num_rows($result)){
            $data = $inDB->fetch_assoc($result);
            return $data['hits'];
        } else {
            return 0;
        }
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ��� <img> � �������, ��������������� ���� �����
     * @param string $filename
     * @return int
     */
    public function fileIcon($filename){
        $standart_icon = 'file.gif';
        $ftypes[0]['ext'] = 'avi mpeg mpg mp4 flv divx xvid vob';
        $ftypes[0]['icon'] = 'video.gif';
        $ftypes[1]['ext'] = 'mp3 ogg wav';
        $ftypes[1]['icon'] = 'audio.gif';
        $ftypes[2]['ext'] = 'zip rar gz arj 7zip';
        $ftypes[2]['icon'] = 'archive.gif';
        $ftypes[3]['ext'] = 'zip rar gz arj 7zip';
        $ftypes[3]['icon'] = 'archive.gif';
        $ftypes[4]['ext'] = 'gif jpg jpeg png bmp pcx wmf cdr ai';
        $ftypes[4]['icon'] = 'image.gif';
        $ftypes[5]['ext'] = 'pdf djvu';
        $ftypes[5]['icon'] = 'pdf.gif';
        $ftypes[6]['ext'] = 'doc';
        $ftypes[6]['icon'] = 'word.gif';
        $ftypes[7]['ext'] = 'iso mds mdf 000';
        $ftypes[7]['icon'] = 'cd.gif';

        $path_parts = pathinfo($filename);
        $ext = $path_parts['extension'];
        $icon = '';
        foreach($ftypes as $key=>$value){
            if (strstr($ftypes[$key]['ext'], $ext)) { $icon = $ftypes[$key]['icon']; break; }
        }

        if ($icon == '') { $icon = $standart_icon; }

        $html = '<img src="/images/icons/filetypes/'.$icon.'" border="0" />';
        return $html;
    }

    // MENU //////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������ ������ ������������� � ������ ����
     * @param int $menuid
     * @param int $groupid
     * @return bool
     */
    public function isMenuAccess($menuid, $groupid=-1){

        if (!$this->menu_item) { $this->menu_item = $this->getMenuItem($menuid); }

        $allow = $this->menu_item['allow_group'];

        if ($allow == -1 || $menuid==0 || $allow == $groupid){
            return true;
        } else {
            return false;
        }
        
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ �������� ������������ � ���������� ���������� ��������
     * � ������ ���������� � ������ ������� � �������� ������ ����
     */
    public function �heckMenuAccess(){
        $inPage = cmsPage::getInstance();
        $inUser = cmsUser::getInstance();
        global $menuid;
        $group_id = $inUser->group_id;
        if ($menuid!=0){
            if(!$this->isMenuAccess($menuid, $group_id)){
                if (!$inUser->id){
                    $inPage->page_body = '<p>������ ��������</p>';
                } else {
                    if (!$inUser->is_admin){
                        $inPage->page_body = '<p>������ ��������</p>';
                    }
                }
            }
        }
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ��������� �������� ������ ����
     * @return string
     */
    public function menuTitle(){

        if ($this->menuId()==1) { return ''; }

        if (!$this->menu_item) { $this->menu_item = $this->getMenuItem($this->menuId()); }

        return $this->menu_item['title'];

    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ �� ����� ����
     * @param string $link
     * @param string $linktype
     * @param int $menuid
     * @return string
     */
    public function menuSeoLink($link, $linktype, $menuid=1){
        return $link;
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� �������, ������������ �� ����� ����
     * ���� ������������ ������ ��-���������, �� ���������� false
     * @param int $menuid
     * @return string or false
     */
    public function menuTemplate($menuid){

        if (!$this->menu_item) { $this->menu_item = $this->getMenuItem($menuid); }

        return $this->menu_item['template'];

    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� true ���� URI �������� � ������ ��������� ������ ���� ������� ���������
     * @return boolean
     */
    public function isMenuIdStrict() {
        
        return $this->is_menu_id_strict;
        
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ID �������� ������ ����
     * @return int
     */
    public function menuId(){

        //���� menu_id ��� ��������� �����, �� ������ � ������
        if ($this->menu_id) { return $this->menu_id; }

        $view       = $this->request('view', 'str', '');
        
        if ($this->is_content){
            $uri = substr($this->uri, strlen('content/'));
        } else {
            $uri = $this->uri;
        }

        $uri        = '/'.$uri;

        //����, ������������ ���� ���������� URI � ������ ����� ����
        //������ ��� ���������
        $is_strict  = false;

        //������� ��������?
        $menuid     = ($uri == '/' ? 1 : 0);
        if ($menuid == 1) {
            $this->is_menu_id_strict = 1;
            return $menuid;
        }

        //���������� ������ ���� ����� ���������� �� ���������� ������ � �������
        $menu       = array_reverse($this->menu_struct);

        //���������� ���� � ������� �������� ������
        foreach($menu as $item){

            if (!$item['link']) { continue; }

            //������ ���������� ������ � ������?
            if ($uri == $item['link']){
                $menuid = $item['id'];
                $is_strict = true; //������ ����������
                break;
            }

            //��������� ���������� ������ � ������ (�� ������ ������)?
            $uri_first_part = substr($uri, 0, strlen($item['link']));
            if ($uri_first_part == $item['link']){
                $menuid = $item['id'];
                break;
            }
            
        }

        $this->menu_id              = $menuid;
        $this->is_menu_id_strict    = $is_strict;

        return $menuid;

    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������ � ������� ������ ����
     * @return array
     */
    public function getMenuItem($menuid){

        $inDB = cmsDatabase::getInstance();

        return $this->menu_struct[$menuid];
        
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ��� ��������� ����
     * 
     */
    private function loadMenuStruct(){

        if (is_array($this->menu_struct)){ return; }

        $inDB = cmsDatabase::getInstance();

        $sql    = "SELECT * FROM cms_menu";
        $result = $inDB->query($sql);

        if (!$inDB->num_rows($result)){ return; }

        while ($item = $inDB->fetch_assoc($result)){
            $this->menu_struct[$item['id']] = $item;
        }

        return;        

    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ID ������ ����, � �������� �������� ���������
     * @param string $component
     * @return int
     */
    public function getComponentMenuId($component) {

        $target_linkid      = array();
        $target_linktype    = array();

        switch($component){
            case 'content':     $target_linktype    = array('content', 'category');
                                $target_linkid      = array('content');
                                break;

            case 'catalog':     $target_linktype    = array('uccat');
                                $target_linkid      = array('catalog');
                                break;

            case 'price':       $target_linktype    = array('pricecat');
                                $target_linkid      = array('price');
                                break;

            case 'blogs':       $target_linktype    = array('blogs');
                                $target_linkid      = array('blogs', 'clubs');
                                break;

            case 'photos':      $target_linkid      = array('photos', 'clubs');
                                break;

            case 'users':       $target_linkid      = array('users');
                                break;

            default:            $target_linktype    = array($component);
                                $target_linkid      = array($component);
                                break;

        }

        foreach($this->menu_struct as $item){
            if (in_array($item['linktype'], $target_linktype) ||
                in_array($item['linkid'], $target_linkid)){
                    return $item['id'];
            }
        }

        return 0;

    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getMenuStruct() {
        return $this->menu_struct;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ ������ �� ����� ���� �� ��� ���� � �����
     * @param string $linktype
     * @param string $linkid
     * @param int $menuid
     * @return string
     */
    public function getMenuLink($linktype, $linkid, $menuid){

        $inDB = cmsDatabase::getInstance();
        $inCore = cmsCore::getInstance();

        $menulink = '';

        if ($linktype=='component'){
            $menulink = '/'.$linkid;
        }

        if ($linktype=='link'){
            $menulink = $linkid;
        }

        if ($linktype=='category' || $linktype=='content'){
            $inCore->loadModel('content');
            $model = new cms_model_content();
            switch($linktype){
                case 'category': $menulink = $model->getCategoryURL(null, $inDB->get_field('cms_category', "id={$linkid}", 'seolink')); break;
                case 'content':  $menulink = $model->getArticleURL(null, $inDB->get_field('cms_content', "id={$linkid}", 'seolink')); break;
            }
        }

        if ($linktype=='blog'){
            $inCore->loadModel('blogs');
            $model = new cms_model_blogs();
            $menulink = $model->getBlogURL(null, $inDB->get_field('cms_blogs', "id={$linkid}", 'seolink'));
        }

        if ($linktype=='uccat'){
            $menulink = '/catalog/'.$linkid;
        }

        if ($linktype=='pricecat'){
            $menulink = '/price/'.$linkid;
        }
        if ($linktype=='photoalbum'){
            $menulink = '/photos/'.$linkid;
        }

        return $menulink;

    }

    // LISTS /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� <option> ��� ������ ������� �� ��������� ������� ��
     * @param string $table
     * @param int $selected
     * @param string $order_by
     * @param string $order_to
     * @param string $where
     * @return html
     */
    public function getListItems($table, $selected=0, $order_by='id', $order_to='ASC', $where='', $id_field='id', $title_field='title'){
        $inDB = cmsDatabase::getInstance();
        $html = '';
        $sql  = "SELECT {$id_field}, {$title_field} FROM {$table} \n";
        if ($where){
            $sql .= "WHERE {$where} \n";
        }
        $sql .= "ORDER BY {$order_by} {$order_to}";
        $result = $inDB->query($sql) ;

        while($item = $inDB->fetch_assoc($result)){
            if (@$selected==$item[$id_field]){
                $s = 'selected';
            } else {
                $s = '';
            }
            $html .= '<option value="'.htmlspecialchars($item[$id_field]).'" '.$s.'>'.$item[$title_field].'</option>';
        }
        return $html;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� <option> ��� ������ ������� �� ��������� ������� �� c ���������� �����������
     * @param string $table �������
     * @param int $selected id ����������� ��������
     * @param string $differ ������������� ��������� (NSDiffer)
     * @param string $need_field �������� ������ �������� ���������� ��������� ����
     * @param int $rootid �������� �������
     * @return html
     */
    public function getListItemsNS($table, $selected=0, $differ='', $need_field='', $rootid=0, $no_padding=false){
        $inDB = cmsDatabase::getInstance();
        $html = '';
        $nested_sets = $this->nestedSetsInit($table);

        $lookup = "parent_id=0 AND NSDiffer='{$differ}'";
        
        if(!$rootid) { $rootid = $inDB->get_field($table, $lookup, 'id'); }

        if(!$rootid) { return; }

        $rs_rows = $nested_sets->SelectSubNodes($rootid);

        if ($rs_rows){
            while($node = $inDB->fetch_assoc($rs_rows)){
                if (!$need_field || $node[$need_field]){
                    if (@$selected==$node['id']){
                        $s = 'selected';
                    } else {
                        $s = '';
                    }
                    if (!$no_padding){
                        $padding = str_repeat('--', $node['NSLevel']) . ' ';
                    } else {
                        $padding = '';
                    }
                    $html .= '<option value="'.htmlspecialchars($node['id']).'" '.$s.'>'.$padding.$node['title'].'</option>';
                }
            }
        }
        return $html;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� <option> ��� ������ ��������� �������
     * @param int $selected
     * @return html
     */
    public function bannersList($selected=0){
        $html = '';
        for($bp=1; $bp<=10; $bp++){
            if (@$selected==$bp){
                $s = 'selected';
            } else {
                $s = '';
            }
            $html .= '<option value="banner'.$bp.'" '.$s.'>banner'.$bp.'</option>'."\n";
        }
        return $html;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� <option> ��� ������ ����� ����� ����������
     * @param int $selected
     * @return html
     */
    public function boardTypesList($selected, $list=false){
        $inDB = cmsDatabase::getInstance();
        $html = '';
        if (!$list){
            $cfg = $this->loadComponentConfig('board');
            $list = explode("\n", $cfg['obtypes']);
        } else {
            $list = explode("\n", $list);
        }
        if ($list){
            foreach($list as $id=>$type){
                $type = trim($type);
                if (strtolower($selected) == strtolower($type)) { $sel = 'selected="selected"'; } else { $sel =''; }
                $html .= '<option value="'.ucfirst($type).'" '.$sel.'>'.ucfirst($type).'</option>';
            }
        }
        return $html;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� <option> ��� ������ ������� ����� ����������
     * @param string $selected
     * @param string $none_label
     * @return html
     */
    public function boardCities($selected='', $none_label = '��� ������'){
        $inDB = cmsDatabase::getInstance();
        $sql = "SELECT city FROM cms_board_items WHERE published = 1 GROUP BY city";
        $result = $inDB->query($sql) ;
        $html = '<select name="city">';
        $html .= '<option value="">'.$none_label.'</option>';
        while($c = $inDB->fetch_assoc($result)){
            if (strtolower($selected)==strtolower($c['city'])){
                $s = 'selected';
            } else {
                $s = '';
            }
            $pretty = ucfirst(strtolower($c['city']));
            $html .= '<option value="'.$pretty.'" '.$s.'>'.$pretty.'</option>';
        }
        $html .= '</select>';
        return $html;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �������� <option> ��� ������ ��������
     * @param string $selected
     */
    public function templatesList($selected=''){
        $dir = PATH.'/templates';
        $tdir = opendir($dir);
            while ($nextfile = readdir($tdir))
            {
                if(($nextfile!='.')&&($nextfile!='..')&&(is_dir($dir.'/'.$nextfile))&&($nextfile!='.svn'))
                {
                    if (@$selected==$nextfile){
                        $s = 'selected';
                    } else {
                        $s = '';
                    }
                    echo '<option value="'.$nextfile.'" '.$s.'>'.$nextfile.'</option>';
                }
            }
    }

    /**
     * ���������� �������� <option> ��� ������ ������
     * @param string $selected
     */
    public function langList($selected=''){
        $dir = PATH.'/languages';
        $tdir = opendir($dir);
            while ($nextfile = readdir($tdir))
            {
                if(($nextfile!='.')&&($nextfile!='..')&&(is_dir($dir.'/'.$nextfile))&&($nextfile!='.svn'))
                {
                    if (@$selected==$nextfile){
                        $s = 'selected';
                    } else {
                        $s = '';
                    }
                    echo '<option value="'.$nextfile.'" '.$s.'>'.$nextfile.'</option>';
                }
            }
    }

    // RATINGS  //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������������ ��� ���� ��� ��������� � ����
     * @param string $target
     * @param string $component
     * @param boolean $is_user_affect
     * @param int $user_weight
     * @return boolean
     */
    public function registerRatingsTarget($target, $component, $target_title, $is_user_affect=true, $user_weight=1, $target_table='') {

        $inDB = cmsDatabase::getInstance();

        $is_user_affect = (int)$is_user_affect;

        $sql  = "INSERT INTO cms_rating_targets (target, component, is_user_affect, user_weight, target_table, target_title)
                 VALUES ('$target', '$component', '$is_user_affect', '$user_weight', '$target_table', '$target_title')";

        $inDB->query($sql);

        return true;

    }

    /**
     * ������� ��� �������� ��� ��������� ����
     * @param string $target
     * @param int $item_id
     * @return boolean
     */
    public function deleteRatings($target, $item_id){

        $inDB = cmsDatabase::getInstance();

        $sql  = "DELETE FROM cms_ratings WHERE target='{$target}' AND item_id='{$item_id}'";
        $inDB->query($sql);

        $sql  = "DELETE FROM cms_ratings_total WHERE target='{$target}' AND item_id='{$item_id}'";
        $inDB->query($sql);

        return true;

    }


    // COMMENTS //////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� �����������
     */
    public function includeComments(){
        include_once PATH."/components/comments/frontend.php";
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������������ ��� ���� ��� ������������ � ����
     * @param string $target - ����
     * @param string $component - ���������
     * @param string $title - �������� ���� �� ����.����� (�������� "������")
     */
    public function registerCommentsTarget($target, $component, $title) {

        $inDB = cmsDatabase::getInstance();

        $sql  = "INSERT INTO cms_comment_targets (target, component, title)
                 VALUES ('$target', '$component', '$title')";

        $inDB->query($sql);

        return true;

    }

    public function getCommentsTargets() {

        $inDB = cmsDatabase::getInstance();

        $targets = $inDB->get_table('cms_comment_targets', 'id>0', '*');

        return $targets;

    }

    /**
     * ������� ��� ����������� ��� ��������� ����
     * @param string $target
     * @param int $target_id
     * @return boolean
     */
    public function deleteComments($target, $target_id){

        $inDB = cmsDatabase::getInstance();

		$comments = $inDB->get_table('cms_comments', "target='{$target}' AND target_id='{$target_id}'", 'id');

        if ($comments){

            foreach($comments as $comment){
                cmsActions::removeObjectLog('add_comment', $comment['id']);
            }
			
        $sql  = "DELETE FROM cms_comments WHERE target='{$target}' AND target_id='{$target_id}'";

        $inDB->query($sql);

        }

        return true;

    }

    /**
     * ���������� ���������� ������������ ��� ��������� ����
     * @param string $target
     * @param int $target_id
     * @return int
     */
    public function getCommentsCount($target, $target_id){
        $inDB = cmsDatabase::getInstance();
        if ($this->isComponentInstalled('comments')){
            $sql = "SELECT id FROM cms_comments WHERE target = '$target' AND target_id = '$target_id' AND published = 1";
            $result = $inDB->query($sql) ;
            return $inDB->num_rows($result);
        } else { return 0; }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ � ������� ������� ��� ������
     * @param int $pages
     * @param int $current
     * @return html
     */
    public function getPageBar($id, $title, $pages, $current){

        $inDB = cmsDatabase::getInstance();

        $html = '';

        $this->loadModel('content');
        $model = new cms_model_content();

        if($pages>1){
            $html .= '<div class="pagebar">';
            $html .= '<span class="pagebar_title"><strong>��������: </strong></span>';
            for ($p=1; $p<=$pages; $p++){
                if ($p != $current) {
                    $seolink = $inDB->get_field('cms_content', "id={$id}", 'seolink');                    
                    $link    = $model->getArticleURL($this->menuId(), $seolink, $p);
                    $html   .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';
                } else {
                    $html .= '<span class="pagebar_current">'.$p.'</span>';
                }
            }
            $html .= '</div>';
        }
        return $html;
    }

    // UTILS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������, ���������� �� ���������
     * @param string $component
     * @return bool
     */
    public function isComponentInstalled($component){
        return (file_exists(PATH.'/components/'.$component.'/frontend.php'));
    }

    // DATE METHODS /////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� �������� ������ � ���� �� �������
     * @param string $datestr
     * @return string
     */
    public function getRusDate($datestr){
        $datestr = str_replace('January', '������', $datestr);
        $datestr = str_replace('February', '�������', $datestr);
        $datestr = str_replace('March', '����', $datestr);
        $datestr = str_replace('April', '������', $datestr);
        $datestr = str_replace('May', '���', $datestr);
        $datestr = str_replace('June', '����', $datestr);
        $datestr = str_replace('July', '����', $datestr);
        $datestr = str_replace('August', '������', $datestr);
        $datestr = str_replace('September', '��������', $datestr);
        $datestr = str_replace('October', '�������', $datestr);
        $datestr = str_replace('November', '������', $datestr);
        $datestr = str_replace('December', '�������', $datestr);
        return $datestr;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	static function dateFormat($date, $is_full_m = true, $is_time=false, $is_now_time = true){
	    global $_LANG;
		// �������� �������� ���� � �������
		list($day, $time) = explode(' ', $date);
		switch( $day ) {
		// ���� ���� ��������� � �����������
		case date('Y-m-d'):
					$result = ''.$_LANG['TODAY'].'';
					if ($is_now_time && $time) {
						list($h, $m, $s)  = explode(':', $time);
						$result .= ' '.$_LANG['IN'].' '.$h.':'.$m;
					}
					break;
		//���� ���� ��������� �� ���������
		case date( 'Y-m-d', mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")) ):
					$result = ''.$_LANG['YESTERDAY'].'';
					if ($is_now_time && $time) {
						list($h, $m, $s)  = explode(':', $time);
						$result .= ' '.$_LANG['IN'].' '.$h.':'.$m;
					}
					break;
			default: {
				// ��������� ����������� ���� �� ������������
				list($y, $m, $d)  = explode('-', $day);
				$month_full_str = array(
					''.$_LANG['MONTH_01'].'', ''.$_LANG['MONTH_02'].'', ''.$_LANG['MONTH_03'].'',
					''.$_LANG['MONTH_04'].'', ''.$_LANG['MONTH_05'].'', ''.$_LANG['MONTH_06'].'',
					''.$_LANG['MONTH_07'].'', ''.$_LANG['MONTH_08'].'', ''.$_LANG['MONTH_09'].'',
					''.$_LANG['MONTH_10'].'', ''.$_LANG['MONTH_11'].'', ''.$_LANG['MONTH_12'].''
				 );
				 $month_short_str = array(
					''.$_LANG['MONTH_01_SHORT'].'', ''.$_LANG['MONTH_02_SHORT'].'', ''.$_LANG['MONTH_03_SHORT'].'',
					''.$_LANG['MONTH_04_SHORT'].'', ''.$_LANG['MONTH_05_SHORT'].'', ''.$_LANG['MONTH_06_SHORT'].'',
					''.$_LANG['MONTH_07_SHORT'].'', ''.$_LANG['MONTH_08_SHORT'].'', ''.$_LANG['MONTH_09_SHORT'].'',
					''.$_LANG['MONTH_10_SHORT'].'', ''.$_LANG['MONTH_11_SHORT'].'', ''.$_LANG['MONTH_12_SHORT'].''
				 );
				 $month_int = array(
					'01', '02', '03',
					'04', '05', '06',
					'07', '08', '09',
					'10', '11', '12'
				 );
				 $day_int = array(
					'01', '02', '03',
					'04', '05', '06',
					'07', '08', '09'
				 );
				 $day_norm = array(
					'1', '2', '3',
					'4', '5', '6',
					'7', '8', '9'
				 );
				// ������ ��������� ����������� ������ �� ��������� (���������� � ������)
				if ($is_full_m){
					$m = str_replace($month_int, $month_full_str, $m);
				}else{
					$m = str_replace($month_int, $month_short_str, $m);
				}
				// ������ ����� 01 02 �� 1 2
				$d = str_replace($day_int, $day_norm, $d);
				// ������������ �������������� ����������
				$result = $d.' '.$m.' '.$y;
				if( $is_time && $time)   {
					// �������� ��������� ������������ �������
					// ������� ��� �� ����������
					list($h, $m, $s)  = explode(':', $time);
					$result .= ' '.$_LANG['IN'].' '.$h.':'.$m;
				}
			}
		}
		 return $result;
	}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function initAutoGrowText($element_id){
        $inPage = cmsPage::getInstance();
        $inPage->addHead('<script type="text/javascript" src="/includes/jquery/autogrow/jquery.autogrow.js"></script>');
        $inPage->addHead('<script type="text/javascript">$(document).ready (function() {$(\''.$element_id.'\').autogrow(); });</script>');
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getDateForm($element, $seldate=false, $day_default=1, $month_default=1, $year_default=1980){

        $year_from = 1950;
        $year_to = intval(date('Y'));

        $html = '';

        if(@$seldate){
            $parts = explode('-', $seldate);
            if ($parts[2]){
                $day_default = $parts[2];
            }
            if ($parts[1]){
                $month_default = $parts[1];
            }
            if ($parts[0]){
                $year_default = $parts[0];
            }
        }

        $html .= '<select name="'.$element.'[day]">' . "\n";
        for($day=1; $day<=31;$day++){
            if ($day<10){ $day = '0'.$day; }

            if (intval($day)==intval($day_default)){
                $html .= '<option value="'.$day.'" selected="selected">'.$day.'</option>'. "\n";
            } else {
                $html .= '<option value="'.$day.'">'.$day.'</option>'. "\n";
            }
        }
        $html .= '</select>'. "\n";

        $months = array();
        $months['00'] = '������';
        $months['01'] = '�������';
        $months['02'] = '����';
        $months['03'] = '������';
        $months['04'] = '���';
        $months['05'] = '����';
        $months['06'] = '����';
        $months['07'] = '������';
        $months['08'] = '��������';
        $months['09'] = '�������';
        $months['10'] = '������';
        $months['11'] = '�������';

        $html .= '<select name="'.$element.'[month]">' . "\n";
        for($month=0; $month<12; $month++){
            if ($month<10){ $month = '0'.$month; }

            if ((intval($month)+1)==intval($month_default)){
                $html .= '<option value="'.($month+1).'" selected="selected">'.$months[$month].'</option>'. "\n";
            } else {
                $html .= '<option value="'.($month+1).'">'.$months[$month].'</option>'. "\n";
            }
        }
        $html .= '</select>'. "\n";

        $html .= '<select name="'.$element.'[year]">'. "\n";
        for($year=$year_from; $year<=$year_to;$year++){
            if ($year == $year_default){
                $html .= '<option value="'.$year.'" selected="selected">'.$year.'</option>'. "\n";
            } else {
                $html .= '<option value="'.$year.'">'.$year.'</option>'. "\n";
            }
        }
        $html .= '</select>'. "\n";

        return $html;
    }

    // USERS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������, �������� �� ������������ ���������������
     * @param int $userid
     * @return bool
     */
    public function userIsAdmin($userid){
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
        if (!$userid) { return false; }
        if ($userid == $inUser->id) {
            return $inUser->is_admin;
        } else {
            $sql = "SELECT g.is_admin is_admin
                    FROM cms_users u
					LEFT JOIN cms_user_groups g ON g.id = u.group_id
                    WHERE u.id = '$userid' LIMIT 1";
            $result = $inDB->query($sql) ;
            if ($inDB->num_rows($result)){
                $data = $inDB->fetch_assoc($result);
                return $data['is_admin'];
            } else { return false; }
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������, �������� �� ������������ ����������
     * @param int $userid
     * @return bool
     */
    public function userIsEditor($userid){
        $inDB = cmsDatabase::getInstance();
        if (!$userid) { return false; }

        $sql = "SELECT c.id as id
                FROM cms_users u
				LEFT JOIN cms_user_groups g ON g.id = u.group_id
				LEFT JOIN cms_category c ON c.modgrp_id = g.id
                WHERE u.id = '$userid'
                LIMIT 1";
        $result = $inDB->query($sql) ;

        if ($inDB->num_rows($result)){
            $editor_category = $inDB->fetch_assoc($result);
            return $editor_category['id'];
        } else { 
            return false; 
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function checkUserAccess($content_type, $content_id){
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
        $access = false;

        if ($inUser->id) {
            $group_id = $_SESSION['user']['group_id'];
            if ($this->userIsAdmin($inUser->id)){
                $access = true;
            }
        }
        else { $group_id = cmsUser::getGuestGroupId(); }

        $sql = "SELECT group_id FROM cms_content_access WHERE content_type='$content_type' AND content_id = $content_id";
        $result = $inDB->query($sql) ;

        if ($inDB->num_rows($result)){
            while($ac = $inDB->fetch_assoc($result)){
                if ($ac['group_id']==$group_id) { $access = true; }
            }
        } else { $access = true; }

        return $access;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ������ � ������������������ ������� ������� �������� ������������
     * @return array
     */
    public function checkAdminAccess(){ //return admin access-options list
        $inUser     = cmsUser::getInstance();
        $inDB       = cmsDatabase::getInstance();

        $group_id 	= $inUser->group_id;

        $sql = "SELECT access FROM cms_user_groups WHERE id = $group_id LIMIT 1";
        $result = $inDB->query($sql) ;

        if ($inDB->num_rows($result)){
            $ac         = $inDB->fetch_assoc($result);
            $access_str = $ac['access'];
            $access     = str_replace(', ', ',', $access_str);
            $access     = explode(',', $access);
            return $access;
        }

        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ��� ������������� ����� ����� �� ��������� ��������
     * @param string $access_type
     * @param array $access_list
     * @return bool
     */
    public function isAdminCan($access_type, $access_list){ //$access_type like "admin/modules" or "admin/users"
        $inUser = cmsUser::getInstance();
        if (!$inUser->is_admin){ return false; }
        if($inUser->id==1) { return true; }
        if (in_array($access_type, $access_list)){ return true; }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ��� ������������ ����� ����� �� ��������� ��������
     * @param string $access_type
     * @return bool
     */
    public function isUserCan($access_type){ //$access_type like "comments/delete" or "photo/edit"
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
        $do_access = false;

        if ($inUser->id) {
            $group_id = $_SESSION['user']['group_id'];
            if ($inUser->is_admin){
                return true;
            }
            if (in_array($access_type, $_SESSION['user']['access'])){
                $do_access = true;
            }
        }
        else { $group_id = cmsUser::getGuestGroupId(); }

        if (!$do_access) {
            if ($group_id > 0) {
                $sql = "SELECT access FROM cms_user_groups WHERE id = $group_id LIMIT 1";
            }

            $result = $inDB->query($sql) ;

            if ($inDB->num_rows($result)){
                $ac = $inDB->fetch_assoc($result);
                $access_str = $ac['access'];

                $access = str_replace(', ', ',', $access_str);
                $access = explode(',', $access);

                if (in_array($access_type, $access)){
                    $do_access = true;
                }
            }
        }

        return $do_access;
    }

    // SECURITY /////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function strClear($string, $strip_tags=true){
        $string = trim($string);
        //���� magic_quotes_gpc = On, ������� ������� �������������
        $string = (@get_magic_quotes_gpc()) ? stripslashes($string) : $string;        
        $string = rtrim($string, ' \\');
        if ($strip_tags) {
            $string = strip_tags($string);
            $string = mysql_real_escape_string($string);
        }
        return $string;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ���� script iframe style meta
     * @param string $string
     * @return bool
     */
    public static function badTagClear($string){
		$bad_teg = array ("'<script[^>]*?>.*?</script>'si",
						 "'<iframe[^>]*?>.*?</iframe>'si",
						 "'<style[^>]*?>.*?</style>'si",
						 "'<meta[^>]*?>'si");
		$string = preg_replace($bad_teg, '', $string);
        return $string;
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ���������� ���� ������ � ����� ��������� �������������
     * @param string $code
     * @return bool
     */
    public function checkCaptchaCode($code){
        $sess_id        = session_id();
        $real_code      = $_SESSION['captcha_keystring'];
        unset($_SESSION['captcha_keystring']);
        return ($real_code == $code);
    }

    // MAIL ROUTINES ////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� � ���������� ������ ����������� ������
     * @param string $email
     * @param string $subject
     * @param string $message
     * @param string $content
     */
    public function mailText($email, $subject, $message, $content='text/plain'){
        $inConf = cmsConfig::getInstance();
        $headers = 'MIME-Version: 1.0' . "\r\n" .
                   'Content-type: '.$content.'; charset=windows-1251;' . "\r\n" .
                   'From: '.$inConf->sitename.' <'.$inConf->sitemail.'>' . "\r\n" .
                   'Reply-To: '.$inConf->sitename.' <'.$inConf->sitemail.'>' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
        $message = wordwrap($message, 70);
        $this->sendMail( $inConf->sitemail, $inConf->sitename, $email, $subject, $message );
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function createMail( $from='', $fromname='', $subject, $body ) {
        $inConf = cmsConfig::getInstance();
        if(!isset($inConf->mailer)) { $inConf->mailer = 'mail'; }

        $this->includeFile('includes/phpmailer/phpmailer.php');

        $mail = new mosPHPMailer();

        $mail->PluginDir = PATH . '/includes/phpmailer/';
        $mail->SetLanguage( 'en', PATH . '/includes/phpmailer/language/' );
        $mail->CharSet     = 'windows-1251';
        $mail->IsMail();
        $mail->From     = $from ? $from : $inConf->sitemail;
        $mail->FromName = $fromname ? $fromname : $inConf->sitename;
        $mail->Mailer   = $inConf->mailer;

        // Add smtp values if needed
        if ( $inConf->mailer == 'smtp' ) {
            $mail->SMTPAuth = $inConf->smtpauth;
            $mail->Username = $inConf->smtpuser;
            $mail->Password = $inConf->smtppass;
            $mail->Host     = $inConf->smtphost;
        } else

        // Set sendmail path
        if ( $inConf->mailer == 'sendmail' ) {
            if (isset($inConf->sendmail)){
                $mail->Sendmail = $inConf->sendmail;
            } else {
                $mail->Sendmail = '/usr/sbin/sendmail';
            }
        } // if

        $mail->Subject     = $subject;
        $mail->Body        = $body;

        return $mail;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private function sendMail( $from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL ) {
        $inConf = cmsConfig::getInstance();

        // Allow empty $from and $fromname settings (backwards compatibility)
        if ($from == '') { $from = $inConf->sitemail;	}
        if ($fromname == '') { $fromname = $inConf->sitename; }

        $mail = $this->createMail( $from, $fromname, $subject, $body );

        // activate HTML formatted emails
        if ( $mode ) {	$mail->IsHTML(true); }

        if (is_array( $recipient )) {
            foreach ($recipient as $to) {
                $mail->AddAddress( $to );
            }
        } else { $mail->AddAddress( $recipient ); }

        if (isset( $cc )) {
            if (is_array( $cc )) {
                foreach ($cc as $to) {
                    $mail->AddCC($to);
                }
            } else { $mail->AddCC($cc);	}
        }
        if (isset( $bcc )) {
            if (is_array( $bcc )) {
                foreach ($bcc as $to) {
                    $mail->AddBCC( $to );
                }
            } else {
                $mail->AddBCC( $bcc );
            }
        }
        if ($attachment) {
            if (is_array( $attachment )) {
                foreach ($attachment as $fname) {
                    $mail->AddAttachment( $fname[0], $fname[1] );
                }
            } else {
                $mail->AddAttachment($attachment);
            }
        }
        //Important for being able to use mosMail without spoofing...
        if ($replyto) {
            if (is_array( $replyto )) {
                reset( $replytoname );
                foreach ($replyto as $to) {
                    $toname = ((list( $key, $value ) = each( $replytoname )) ? $value : '');
                    $mail->AddReplyTo( $to, $toname );
                }
            } else {
                $mail->AddReplyTo($replyto, $replytoname);
            }
        }

        $mailssend = $mail->Send();

        return $mailssend;
    }

    // UC SEARCH ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function getUCSearchLink($cat_id, $menuid, $field_id, $text){
        $html='';
        $text = html_entity_decode($text);
        $text = strip_tags($text);
        $text = trim($text);
        if (!strstr($text, ',')){
            $html .= '<a href="/catalog/'.$cat_id.'/find/'.urlencode($text).'">'.$text.'</a>';
        } else {
            $text = str_replace(', ', ',', $text);
            $words = array();
            $words = explode(',', $text);

            $n=0;
            foreach($words as $key=>$value){
                $n++;
                $value = strip_tags($value);
                $value = str_replace("\r", '', $value);
                $value = str_replace("\n", '', $value);
                $value = trim($value);

                $html .= '<a href="/catalog/'.$cat_id.'/find/'.urlencode($value).'">'.$value.'</a>';
                if ($n<sizeof($words)) { $html .= ', '; } else { $html .= '.'; }
            }

        }
        return $html;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function registerUploadImages($session_id, $post_id, $target){
        $inDB = cmsDatabase::getInstance();
        $sql = "UPDATE cms_upload_images SET post_id = $post_id, session_id = '' WHERE session_id = '$session_id' AND target='$target'";
        $inDB->query($sql);
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function deleteUploadImages($post_id, $target){
        $inDB = cmsDatabase::getInstance();
        $sql = "SELECT * FROM cms_upload_images WHERE post_id = '$post_id' AND target='$target'";
        $rs = $inDB->query($sql);
        if ($inDB->num_rows($rs)){
            while($file = $inDB->fetch_assoc($rs)){
                $filename = PATH.$file['fileurl'];
                if (file_exists($filename)){ @unlink($filename); }
                $inDB->query("DELETE FROM cms_upload_images WHERE id=".$file['id']);
            }
        }
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function parseSmiles($text, $parse_bbcode=false){
        if (!$parse_bbcode){
            //convert URLs to links
            $text = ereg_replace("/(?<!http:\\/\\/)(www)(\\S+)/si",'http://www\\2', $text);
            $text = ereg_replace("/(http:\\/\\/)(\\S+)/si",'<a href="http://\\2" target=_new>http://\\2</a>',$text);
        } else {
            //parse bbcode
            include_once PATH.'/includes/bbcode/bbcode.lib.php';
            $bb = new bbcode($text);
            $text = $bb->get_html();            
        }

        //convert emoticons to smileys
        $smilefix = array();
        $smilefix[' :) '] = 'smile';
        $smilefix[' =) '] = 'smile';
        $smilefix[':-)'] = 'smile';
        $smilefix[' :( '] = 'sad';
        $smilefix[':-('] = 'sad';
        $smilefix[';-)'] = 'joke';
        $smilefix[' ;) '] = 'joke';
        $smilefix[' =0 '] = 'shock';
        $smilefix['=-0'] = 'shock';
        $smilefix[' Oo '] = 'shock';
        $smilefix[':-0'] = 'shock';
        $smilefix[' :D '] = 'laugh';
        $smilefix[':-D'] = 'laugh';

        foreach($smilefix as $find=>$tag){
            $text = str_replace($find, ':'.$tag.':', $text);
        }

        $tags = explode(':', $text);

        foreach($tags as $key=>$value){
            if (strlen($value)<15){
                $file = '/images/smilies/'.$value.'.gif';
                if (@file_exists(PATH.$file)){
                    $text = str_replace(':'.$value.':', '<img src="'.$file.'" alt="'.$value.'" border="0"/>', $text);
                }
            }
        }

        return $text;
    }

    // PAGE CACHE   /////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ������� ���� ��� ���������� ��������
     * @param string $target
     * @param int $target_id
     * @param int $cachetime
     * @param string $cacheint
     * @return bool
     */
    public function isCached($target, $target_id, $cachetime=1, $cacheint='MINUTES'){
        $where = "target='$target' AND target_id='$target_id' AND cachedate >= DATE_SUB(NOW(), INTERVAL $cachetime $cacheint)";
        $page = dbGetFields('cms_cache', $where, '*');
        if ($page){
            $cachefile = PATH.'/cache/'.$page['cachefile'];
            if (file_exists($cachefile)){
                return true;
            } else {
                return false;
            }
        } else {
            $this->deleteCache($target, $target_id);
            return false;
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ��� ���������� ��������
     * @param string $target
     * @param int $target_id
     * @return html
     */
    public function getCache($target, $target_id){
            $page = dbGetFields('cms_cache', "target='$target' AND target_id='$target_id'", '*');
            if ($page){
                $cachefile = PATH.'/cache/'.$page['cachefile'];
                if (file_exists($cachefile)){
                    $cache = file_get_contents($cachefile);
                    if(strstr($_SERVER['QUERY_STRING'], 'show_cache')){
                        $cache .= '<div style="background-color:gray;color:white">Printed from cache!</div>';
                    }
                    return $cache;
                }
            }
        return false;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ��������� ���������� ��� ���������� ��������
     * @param string $target
     * @param int $target_id
     * @param string $html
     * @return bool
     */
    public function saveCache($target, $target_id, $html){
        $inDB = cmsDatabase::getInstance();
        $filename = md5($target.$target_id).'.html';
        $sql = "INSERT INTO cms_cache (target, target_id, cachedate, cachefile)
                VALUES ('$target', $target_id, NOW(), '$filename')";
        $inDB->query($sql) or die('ERROR CREATING CACHE: '.mysql_error());
        $filename = PATH.'/cache/'.$filename;
        file_put_contents($filename, $html);
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ��� ���������� ��������
     * @param string $target
     * @param int $target_id
     * @return bool
     */
    public function deleteCache($target, $target_id){
        $inDB = cmsDatabase::getInstance();
        $inDB->query("DELETE FROM cms_cache WHERE target='$target' AND target_id='$target_id'");
        $oldcache = PATH.'/cache/'.md5($target.$target_id).'.html';
        if (file_exists($oldcache)) { @unlink($oldcache); }
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ��� ������� � ����������� ����������� ������� ��� ��������� �������
     * @param string $position
     * @return html
     */
    public function getBanner($position){
        $inDB = cmsDatabase::getInstance();
        $html = '';

        //get active banners with enough hits
        $sql = "SELECT *
                FROM cms_banners
                WHERE position = '$position' AND published = 1 AND ((maxhits > hits) OR (maxhits = 0))
                ORDER BY hits ASC
                LIMIT 1";
        $rs = $inDB->query($sql);

        if ($inDB->num_rows($rs)==1){
            $banner = $inDB->fetch_assoc($rs);
            if ($banner['typeimg']=='image'){
                $html = '<a href="/gobanner'.$banner['id'].'" title="'.$banner['title'].'" target="_blank"><img src="/images/banners/'.$banner['fileurl'].'" border="0" alt="'.$banner['title'].'"/></a>';
            }
            if ($banner['typeimg']=='swf'){
                $html = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="468" height="60">'."\n".
                            '<param name="movie" value="/images/banners/'.$banner['fileurl'].'?banner_id='.$banner['id'].'" />'."\n".
                            '<param name="quality" value="high" />'."\n".
                            '<param name="FlashVars" value="banner_id='.$banner['id'].'" />'."\n".
                            '<embed src="/images/banners/'.$banner['fileurl'].'?banner_id='.$banner['id'].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="468" height="60">'."\n".
                            '</embed>'."\n".
                        '</object>';
            }
            if ($html) { $inDB->query("UPDATE cms_banners SET hits = hits + 1 WHERE id=".$banner['id']);	}
        }
        return $html;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ���������� ��� ������� �� ID
     * @param int $id
     * @return html
     */
    public function getBannerById($id){
        $inDB = cmsDatabase::getInstance();
        $html = '';

        $sql = "SELECT *
                FROM cms_banners
                WHERE id = $id
                LIMIT 1";
        $rs = $inDB->query($sql);

        if ($inDB->num_rows($rs)==1){
            $banner = $inDB->fetch_assoc($rs);
            if ($banner['typeimg']=='image'){
                $html = '<a href="/gobanner'.$banner['id'].'" title="'.$banner['title'].'"><img src="/images/banners/'.$banner['fileurl'].'" border="0" alt="'.$banner['title'].'"/></a>';
            }
            if ($banner['typeimg']=='swf'){
                $html = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="468" height="60">'."\n".
                            '<param name="movie" value="/images/banners/'.$banner['fileurl'].'?banner_id='.$banner['id'].'" />'."\n".
                            '<param name="quality" value="high" />'."\n".
                            '<param name="FlashVars" value="banner_id='.$banner['id'].'" />'."\n".
                            '<embed src="/images/banners/'.$banner['fileurl'].'?banner_id='.$banner['id'].'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="468" height="60">'."\n".
                            '</embed>'."\n".
                        '</object>';
            }
        }
        return $html;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    static public function strToURL($str){

        $str    = trim($str);        
        $str    = mb_strtolower($str, 'cp1251');
        $string = str_replace(' ', '-', $string);
        $string = preg_replace ('/[^a-zA-Z�-��-�0-9\-]/i', '-', $str);
        $string = rtrim($string, '-');

        while(strstr($string, '--')){ $string = str_replace('--', '-', $string); }

        $ru_en = array(
                        '�'=>'a','�'=>'b','�'=>'v','�'=>'g','�'=>'d',
                        '�'=>'e','�'=>'yo','�'=>'zh','�'=>'z',
                        '�'=>'i','�'=>'i','�'=>'k','�'=>'l','�'=>'m',
                        '�'=>'n','�'=>'o','�'=>'p','�'=>'r','�'=>'s',
                        '�'=>'t','�'=>'u','�'=>'f','�'=>'h','�'=>'c',
                        '�'=>'ch','�'=>'sh','�'=>'sh','�'=>'','�'=>'y',
                        '�'=>'','�'=>'ye','�'=>'yu','�'=>'ja'
                      );

        foreach($ru_en as $ru=>$en){
            $string = preg_replace('/(['.$ru.']+)/i', $en, $string);
        }

        if (!$string){ $string = 'untitled'; }

        return $string;

}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function halt($message=''){
        die($message);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function flushUpload(){
        $inDB = cmsDatabase::getInstance();
        $inDB->query("DELETE FROM cms_upload_images WHERE session_id='".session_id()."'");
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function clearSessionTrash(){
        unset($_SESSION['bbcode']);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * ������� ������� ������� ����� ������� � ��������� �����
     * @param string $date
     * @return string
     */
    public static function dateDiffNow($date) {

        $diff_str   = '';

        $now        = time();
        $date       = strtotime($date);

        if ($date == 0) { return '�� ��������'; }

        $diff_sec   = $now - $date;

        $diff_day   = (string)round($diff_sec/60/60/24);
        $diff_hour  = (string)round(($diff_sec/60/60) - ($diff_day*24));
        $diff_min   = (string)round(($diff_sec/60)-($diff_hour*60));

        //������� ������� � ����
        if ($diff_day){

            if ($diff_day == 11 || $diff_day == 12 || $diff_day == 13 || $diff_day == 14) {
                $diff_str = $diff_day. " ����";
            } elseif ($diff_day[strlen($diff_day)-1] == "2" || $diff_day[strlen($diff_day)-1] == "3" || $diff_day[strlen($diff_day)-1] == "4") {
                $diff_str = $diff_day." ���";
            } elseif($diff_day[strlen($diff_day)-1] == "1") {
                $diff_str = $diff_day. " ����";
            } else {
                $diff_str = $diff_day. " ����";
            }

            return $diff_str;

        }

        //������� ������� � �����
        if ($diff_hour){

            if ($diff_hour == 1 || $diff_hour == 21) $diff_str = $diff_hour." ���"; else
            if ($diff_hour == 2 || $diff_hour == 3 or $diff_hour == 4 || $diff_hour == 22 || $diff_hour == 23) $diff_str = $diff_hour." ����";
            else $diff_str = $diff_hour." �����";

            return $diff_str;

        }

        //������� ������� � �������
        if ($diff_min){

            if ($diff_min == "11" || $diff_min == "12" || $diff_min == "13" || $diff_min == "14") {
                $diff_str = $diff_min. " �����";
            } elseif ($diff_min[strlen($diff_min)-1] == "2" || $diff_min[strlen($diff_min)-1] == "3" || $diff_min[strlen($diff_min)-1] == "4") {
                $diff_str = $diff_min." ������";
            } elseif($diff_min[strlen($diff_min)-1] == "1") {
                $diff_str = $diff_min. " ������";
            } else {
                $diff_str = $diff_min. " �����";
            }

            return $diff_str;

        }

        $diff_str = '������ ������';

        return $diff_str;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} //cmsCore


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
function dbRowsCount($table, $where){
    $inDB = cmsDatabase::getInstance();
    return $inDB->rows_count($table, $where);
}

function dbGetField($table, $where, $field){
    $inDB = cmsDatabase::getInstance();
    return $inDB->get_field($table, $where, $field);
}

function dbGetFields($table, $where, $fields, $order='id ASC'){
    $inDB = cmsDatabase::getInstance();
    return $inDB->get_fields($table, $where, $fields, $order);
}

function dbGetTable($table, $where='', $fields='*'){
    $inDB = cmsDatabase::getInstance();
    return $inDB->get_table($table, $where, $fields);
}

function dbLastId($table){
    $inDB = cmsDatabase::getInstance();
    return $inDB->get_last_id($table);
}

function dbDeleteNS($table, $id){
    $inCore = cmsCore::getInstance();
    $ns = $inCore->nestedSetsInit($table);
    $ns->DeleteNode($id);
}

function dbDeleteListNS($table, $list){
    $inCore = cmsCore::getInstance();
    $ns = $inCore->nestedSetsInit($table);
    if (is_array($list)){
        foreach($list as $key => $value){
            $ns->DeleteNode($value);
        }
    }
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//������� ���� ��������� ��� ������������� �� ������� ���������
//

function cmsPrintSitename(){
    $inPage = cmsPage::getInstance();
    $inPage->printSitename();
}

function cmsPrintHead(){
    $inPage = cmsPage::getInstance();
    $inPage->printHead();
}

function cmsPathway($separator){
    $inPage = cmsPage::getInstance();
    $inPage->printPathway($separator);
}

function cmsBody(){
    $inPage = cmsPage::getInstance();
    $inPage->printBody();
}

function cmsPrintFooter(){
    $inPage = cmsPage::getInstance();
    $inPage->printFooter();
}

function cmsCountModules($position){
    $inPage = cmsPage::getInstance();
    return $inPage->countModules($position);
}

function cmsModule($position){
    $inPage = cmsPage::getInstance();
    $inPage->printModules($position);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cmsGetUserLink(){
    $inPage = cmsPage::getInstance();
    return $inPage->getUserLinks();
}

function cmsMenuId(){
   $inCore = cmsCore::getInstance();
   return $inCore->menuId();
}

function cmsUserId(){
   $inUser = cmsUser::getInstance();
   return $inUser->id;
}

function cmsLoadModuleConfig($module_id){
   $inCore = cmsCore::getInstance();
   return $inCore->loadModuleConfig($module_id);
}

function cmsLoadComponentConfig($component){
   $inCore = cmsCore::getInstance();
   return $inCore->loadComponentConfig($component);
}

function cmsNestedSetsInit($table){
   $inCore = cmsCore::getInstance();
   return $inCore->nestedSetsInit($table);
}

function cmsUserIsAdmin(){
    $inUser = cmsUser::getInstance();
    return $inUser->is_admin;
}

function cmsGuestGroup(){
    return cmsUser::getGuestGroupId();
}

function cmsMenuSeoLink($link, $linktype, $menuid=1){
    $inCore = cmsCore::getInstance();
    return $inCore->menuSeoLink($link, $linktype, $menuid);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cmsSmartyComments($params){

    if (!$params['target']) { return false; }
    if (!$params['target_id']) { return false; }

    $inCore = cmsCore::getInstance();
    $inCore->includeComments();

    comments($params['target'], $params['target_id']);

    return;

}

function cmsSmartyNoSpam($email, $filterLevel = 'normal'){
    $email = strrev($email);
    $email = preg_replace('[\.]', '/', $email, 1);
    $email = preg_replace('[@]', '/', $email, 1);

    if($filterLevel == 'low')
    {
        $email = strrev($email);
    }

    return $email;
}

function cmsSmartyAddJS($params){
    $inPage = cmsPage::getInstance();
    $inPage->addHeadJS($params['file']);
}

function cmsSmartyWysiwyg($params){
    $inCore = cmsCore::getInstance();
    ob_start();
    $inCore->insertEditor($params['name'], $params['value'], $params['height'], $params['width']);
    return ob_get_clean();
}

function cmsSmartyAddCSS($params){
    $inPage = cmsPage::getInstance();
    $inPage->addHeadCSS($params['file']);
}

function cmsSmartyProfileURL($params){
    return cmsUser::getProfileURL($params['login']);
}

function usrNewMessages($user_id){
   $inCore = cmsCore::getInstance();
   return cmsUser::isNewMessages($user_id);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>
