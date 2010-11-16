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

        //подключим базу и конфиг
        $this->loadClass('db');
        $this->loadClass('config');

        //загрузим структуру меню в память
        $this->loadMenuStruct();

        //получим URI
        $this->uri = $this->detectURI();

        //определим компонент
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
     * Сортирует ассоциативный массив
     * @param array $array
     * @param string $sort_by (критерий сортировки)
     * @param int $desc (1 - по возрастанию, 0 - по убыванию)
     * @param string $f (функция сравнения)
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
     * Преобразует массив в YAML
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
     * Преобразует YAML в массив
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
     * Производит событие, вызывая все назначенные на него плагины
     * @param string $event
     * @param mixed $item
     * @return mixed
     */
    public static function callEvent($event, $item){

        //получаем все активные плагины, привязанные к указанному событию
        $plugins = self::getInstance()->getEventPlugins($event);

        //если активных плагинов нет, возвращаем элемент $item без изменений
        if (!$plugins) { return $item; }

        //перебираем плагины и вызываем каждый из них, передавая элемент $item
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

        //возращаем $item обратно
        return $item;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает массив с именами плагинов, привязанных к событию $event
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
     * Устанавливает плагин и делает его привязку к событиям
     * Возвращает ID установленного плагина
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

        //добавляем плагин в базу
        $install_query  = "INSERT INTO cms_plugins (id, plugin, title, description, author, version, plugin_type, published, config)
                           VALUE ('', '{$plugin['plugin']}', '{$plugin['title']}', '{$plugin['description']}', '{$plugin['author']}',
                                  '{$plugin['version']}', '{$plugin['type']}', 0, '{$config_yaml}')";

        $inDB->query($install_query);

        //получаем ID плагина
        $plugin_id = $inDB->get_last_id('cms_plugins');

        //возвращаем ложь, если плагин не установился
        if (!$plugin_id)    { return false; }

        //добавляем хуки событий для плагина
        foreach($events as $event){
            $event_query = "INSERT INTO cms_event_hooks (event, plugin_id) VALUES ('{$event}', {$plugin_id})";
            $inDB->query($event_query);
        }

        //возращаем ID установленного плагина
        return $plugin_id;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Делает апгрейд установленного плагина
     * @param array $plugin
     * @param array $events
     * @param array $config
     * @return bool
     */
    public function upgradePlugin($plugin, $events, $config) {
        $inDB = cmsDatabase::getInstance();

        //находим ID установленной версии
        $plugin_id = $this->getPluginId( $plugin['plugin'] );

        //если плагин еще не был установлен, выходим
        if (!$plugin_id) { return false; }

        //загружаем текущие настройки плагина
        $old_config = $this->loadPluginConfig( $plugin['plugin'] );

        //удаляем настройки, которые больше не нужны
        foreach($old_config as $param=>$value){
            if ( !isset($config[$param]) ){
                unset($old_config[$param]);
            }
        }

        //добавляем настройки, которых раньше не было
        foreach($config as $param=>$value){
            if ( !isset($old_config[$param]) ){
                $old_config[$param] = $value;
            }
        }

        //конвертируем массив настроек в YAML
        $config_yaml   = $this->arrayToYaml($old_config);

        //обновляем плагин в базе
        $update_query  = "UPDATE cms_plugins
                          SET title='{$plugin['title']}',
                              description='{$plugin['description']}',
                              author='{$plugin['author']}',
                              version='{$plugin['version']}',
                              config='{$config_yaml}'
                          WHERE id = {$plugin_id}";

        $inDB->query($update_query);

        //добавляем новые хуки событий для плагина
        foreach($events as $event){
            if ( !$this->isPluginHook($plugin_id, $event) ){
                $event_query = "INSERT INTO cms_event_hooks (event, plugin_id) VALUES ('{$event}', {$plugin_id})";
                $inDB->query($event_query);
            }
        }

        //плагин успешно обновлен
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Удаляет установленный плагин
     * @param array $plugin
     * @param array $events
     * @return bool
     */
    public function removePlugin($plugin_id) {
        $inDB = cmsDatabase::getInstance();

        //если плагин не был установлен, выходим
        if (!$plugin_id) { return false; }

        //удаляем плагин из базы
        $delete_query  = "DELETE FROM cms_plugins WHERE id = {$plugin_id}";

        $inDB->query($delete_query);

        //Удаляем хуки событий плагина
        $unhook_query  = "DELETE FROM cms_event_hooks WHERE plugin_id = {$plugin_id}";

        $inDB->query($unhook_query);

        //плагин успешно удален
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает список плагинов, имеющихся на диске, но не установленных
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
     * Возвращает список плагинов, версия которых изменилась в большую сторону
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
     * Возвращает список папок с плагинами
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
     * Возвращает ID плагина по названию
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
     * Возвращает название плагина по ID
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
     * Возвращает версию плагина по названию
     * @param string $plugin
     * @return float
     */
    public function getPluginVersion($plugin){

        $inDB = cmsDatabase::getInstance();

        return $inDB->get_field('cms_plugins', "plugin='{$plugin}'", 'version');

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Устанавливает компонент
     * Возвращает ID установленного плагина
     * @param array $component
     * @param array $config
     * @return int
     */
    public function installComponent($component, $config) {
        $inDB = cmsDatabase::getInstance();

        $config_yaml    = $this->arrayToYaml($config);

        if (!$config_yaml) { $config_yaml = ''; }

        //добавляем компонент в базу
        $install_query  = "INSERT INTO cms_components (`title`, `link`, `config`, `internal`, `author`, `published`, `version`, `system`)
                           VALUES ('{$component['title']}', '{$component['link']}', '{$config_yaml}', '{$component['internal']}',
                                    '{$component['author']}', '1', '{$component['version']}', '0')";

        $inDB->query($install_query);

        //получаем ID компонента
        $component_id = $inDB->get_last_id('cms_components');

        //возвращаем ложь, если компонент не установился
        if (!$component_id)    { return false; }

        //возращаем ID установленного компонента
        return $component_id;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Делает апгрейд установленного компонента
     * @param array $component
     * @param array $config
     * @return bool
     */
    public function upgradeComponent($component, $config) {
        $inDB = cmsDatabase::getInstance();

        //находим ID установленной версии
        $component_id = $this->getComponentId( $component['link'] );

        //если компонент еще не был установлен, выходим
        if (!$component_id) { return false; }

        //загружаем текущие настройки компонента
        $old_config = $this->loadComponentConfig( $component['link'] );

        //удаляем настройки, которые больше не нужны
        foreach($old_config as $param=>$value){
            if ( !isset($config[$param]) ){
                unset($old_config[$param]);
            }
        }

        //добавляем настройки, которых раньше не было
        foreach($config as $param=>$value){
            if ( !isset($old_config[$param]) ){
                $old_config[$param] = $value;
            }
        }

        //конвертируем массив настроек в YAML
        $config_yaml   = $this->arrayToYaml($old_config);

        //обновляем компонент в базе
        $update_query  = "UPDATE cms_components
                          SET title='{$component['title']}',
                              author='{$component['author']}',
                              version='{$component['version']}',
                              internal='{$component['internal']}',
                              config='{$config_yaml}'
                          WHERE id = {$component_id}";

        $inDB->query($update_query);

        //компонент успешно обновлен
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Удаляет установленный компонент
     * @param int $component_id
     * @return bool
     */
    public function removeComponent($component_id) {

        $inDB = cmsDatabase::getInstance();

        //если компонент не был установлен, выходим
        if (!$component_id) { return false; }

        //удаляем компонент из базы, но только если он не системный
        $delete_query  = "DELETE FROM cms_components WHERE id = {$component_id} AND system = 0";

        $inDB->query($delete_query);

        //компонент успешно удален
        return true;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает список компонентов, имеющихся на диске, но не установленных
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
     * Возвращает список компонентов, версия которых изменилась в большую сторону
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
     * Возвращает список папок с компонентами
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
     * Возвращает ID компонента по названию
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
     * Возвращает название компонента по ID
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
     * Возвращает версию компонента по названию
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
     * Устанавливает модуль
     * Возвращает ID установленного модуля
     * @param array $module
     * @param array $config
     * @return int
     */
    public function installModule($module, $config) {

        $inDB = cmsDatabase::getInstance();

        $config_yaml    = $this->arrayToYaml($config);

        if (!$config_yaml) { $config_yaml = ''; }

        //добавляем модуль в базу
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

        //получаем ID модуля
        $module_id = $inDB->get_last_id('cms_modules');

        //возвращаем ложь, если модуль не установился
        if (!$module_id)    { return false; }

        //возращаем ID установленного модуля
        return $module_id;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Делает апгрейд установленного модуля
     * @param array $component
     * @param array $config
     * @return bool
     */
    public function upgradeModule($module, $config) {
        $inDB = cmsDatabase::getInstance();

        //находим ID установленной версии
        $module_id = $this->getModuleId( $module['link'] );

        //если модуль еще не был установлен, выходим
        if (!$module_id) { return false; }

        //загружаем текущие настройки модуля
        $old_config = $this->loadModuleConfig( $module_id );

        //удаляем настройки, которые больше не нужны
        foreach($old_config as $param=>$value){
            if ( !isset($config[$param]) ){
                unset($old_config[$param]);
            }
        }

        //добавляем настройки, которых раньше не было
        foreach($config as $param=>$value){
            if ( !isset($old_config[$param]) ){
                $old_config[$param] = $value;
            }
        }

        //конвертируем массив настроек в YAML
        $config_yaml   = $this->arrayToYaml($old_config);

        //обновляем модуль в базе
        $update_query  = "UPDATE cms_modules
                          SET title='{$module['title']}',
                              name='{$module['name']}',
                              version='{$module['version']}',
                              config='{$config_yaml}'
                          WHERE id = {$module_id}";

        $inDB->query($update_query);

        //модуль успешно обновлен
        return true;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Удаляет установленный модуль
     * @param int $module_id
     * @return bool
     */
    public function removeModule($module_id) {

        $inDB = cmsDatabase::getInstance();

        //если модуль не был установлен, выходим
        if (!$module_id) { return false; }

        //удаляем модуль из базы
        $delete_query  = "DELETE FROM cms_modules WHERE id = {$module_id}";

        $inDB->query($delete_query);

        //модуль успешно удален
        return true;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает список модулей, имеющихся на диске, но не установленных
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
     * Возвращает список модулей, версия которых изменилась в большую сторону
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
     * Возвращает список папок с модулями
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
     * Возвращает ID модуля по названию
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
     * Возвращает название модуля по ID
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
     * Возвращает версию модуля по названию
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
     * Возвращает кофигурацию плагина в виде массива
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
     * Сохраняет настройки плагина в базу
     * @param string $plugin_name
     * @param array $config
     * @return bool
     */
    public function savePluginConfig($plugin_name, $config) {

        $inDB = cmsDatabase::getInstance();

        //конвертируем массив настроек в YAML
        $config_yaml   = $this->arrayToYaml($config);

        //обновляем плагин в базе
        $update_query  = "UPDATE cms_plugins
                          SET config='{$config_yaml}'
                          WHERE plugin = '{$plugin_name}'";

        $inDB->query($update_query);

        //настройки успешно сохранены
        return true;
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Проверяет привязку плагина к событию
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
     * Загружает плагин и возвращает его объект
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
     * Уничтожает объект плагина
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
     * Загружает библиотеку из файла /core/lib_XXX.php, где XXX = $lib
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
     * Загружает класс из файла /core/classes/XXX.class.php, где XXX = $class
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
     * Загружает модель для указанного компонента
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
     * Подключает внешний файл
     * @param string $lib
     */
    public function includeFile($file){
        include_once PATH.'/'.$file;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Подключает функции для работы с графикой
     */
    public function includeGraphics(){
        include_once PATH.'/includes/graphic.inc.php';
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Подключает файл конфигурации
     */
    public function includeConfig(){
        include_once PATH.'/includes/config.inc.php';
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Подключает визуальный редактор
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
                <div>Визуальный редактор не найден либо не включен.</div>
                <div>Если редактор установлен, включите его в админке (меню <em>Дополнения</em> &rarr; <em>Плагины</em>).</div>
              </p>';

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Устанавливает кукис посетителю
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
     * Удаляет кукис пользователя
     * @param string $name
     */
    public function unsetCookie($name){
        setcookie('InstantCMS['.$name.']', '', time()-3600, '/');
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает значение кукиса
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
     * Добавляет сообщение в сессию
     * @param string $message
     * @param string $class
     */
    public static function addSessionMessage($message, $class='info'){
        $_SESSION['core_message'][] = '<div class="message_'.$class.'">'.$message.'</div>';
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /*
     * Возвращает массив сообщений сохраненных в сессии
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
     * Очищает очередь сообщений сессии
     */
    public static function clearSessionMessages(){
        unset($_SESSION['core_message']);
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Обновляет статистику посещений сайта
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

        //удаляем старые записи
        $sql = "DELETE FROM cms_online WHERE lastdate <= DATE_SUB(NOW(), INTERVAL 3 MINUTE)";
        $inDB->query($sql) ;

        if (!$inUser->checkStatTimer()){ return true; }

        $inUser->resetStatTimer();

        //собираем информацию о текущем пользователе
        $sess_id    = session_id();
        $ip         = $this->strClear($_SERVER['REMOTE_ADDR']);
        $useragent  = $this->strClear($_SERVER['HTTP_USER_AGENT']);
        $page       = $this->strClear($_SERVER['REQUEST_URI']);
        $refer      = $this->strClear($_SERVER['HTTP_REFERER']);

        $user_id    = $inUser->id;

        if (strstr(strtolower($useragent), 'select')) { return false; }
        if (strstr(strtolower($useragent), 'from'))   { return false; }

        //проверяем, есть ли текущий пользователь в таблице "кто онлайн"
        $sql = "SELECT id FROM cms_online WHERE (sess_id = '$sess_id' AND ip = '$ip')";
        $result = $inDB->query($sql) ;

        if (!$inDB->num_rows($result)){
            //Проверяем, пользователь это или поисковый бот
            $crawler = false;
            foreach($bots as $bot=>$uagent){ if (strpos($useragent, $uagent)) { $crawler = true; }	}
            //Если не бот, вставляем запись в "кто онлайн"
            if (!$crawler){
                $sql = "INSERT INTO cms_online (ip, sess_id, lastdate, user_id, viewurl) VALUES ('$ip', '$sess_id', NOW(), '$user_id', '$page')";
                $inDB->query($sql) ;
            }
        } else {
            //Если пользователь уже онлайн, обновляем время
            $sql = "UPDATE cms_online
                     SET lastdate = NOW(),
                         user_id = '$user_id',
                         viewurl = '$page'
                     WHERE (sess_id = '$sess_id' AND ip = '$ip')";
            $inDB->query($sql) ;
        }

        if (@$_CFG['stats']){ //если включен сбор статистики на сайте
            //смотрим, есть ли запись про текущего пользователя
            $sql = "SELECT id FROM cms_stats WHERE (ip = '$ip' AND page = '$page')";
            $result = $inDB->query($sql) ;
            //если записи нет - добавляем
            if (!$inDB->num_rows($result)){
                $sql = "INSERT INTO cms_stats (ip, logdate, page, agent, refer) VALUES ('$ip', NOW(), '$page', '$useragent', '$refer')";
                $inDB->query($sql) ;
            }
        }

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает текущий URI
     * Нужна для того, чтобы иметь возможность переопределить URI.
     * По сути является эмулятором внутреннего mod_rewrite
     * @return string
     */
    private function detectURI(){

        $uri    = $this->request('uri', 'str', '');
        $rules  = array();

        //специальный хак для поиска по сайту, для совместимости со старыми шаблонами
        if (strstr($_SERVER['QUERY_STRING'], 'view=search')){ $uri = 'search'; }

        if(file_exists(PATH.'/url_rewrite.php')) {
            //подключаем список rewrite-правил
            $this->includeFile('url_rewrite.php');
            if(function_exists('rewrite_rules')){
                //получаем правила
                $rules = rewrite_rules();
            }
        }

        if(file_exists(PATH.'/custom_rewrite.php')) {
            //подключаем список пользовательских rewrite-правил
            $this->includeFile('custom_rewrite.php');
            if(function_exists('custom_rewrite_rules')){
                //добавляем к полученным ранее правилам пользовательские
                $rules = array_merge($rules, custom_rewrite_rules());
            }
        }

        $found = false;

        if ($rules){
            //перебираем правила
            foreach($rules as $rule_id=>$rule) {
                //небольшая валидация правила
                if (!$rule['source'] || !$rule['target'] || !$rule['action']) { continue; }
                //проверяем совпадение выражения source с текущим uri
                if (preg_match($rule['source'], $uri, $matches)){

                    //перебираем совпавшие сегменты и добавляем их в target
                    //чтобы сохранить параметры из $uri в новом адресе
                    foreach($matches as $key=>$value){
                        if (!$key) { continue; }
                        if (strstr($rule['target'], '{'.$key.'}')){
                            $rule['target'] = str_replace('{'.$key.'}', $value, $rule['target']);
                        }
                    }

                    //действие по-умолчанию: rewrite
                    if (!$rule['action']) { $rule['action'] = 'rewrite'; }

                    //выполняем действие
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
     * Определяет текущий компонент
     * Считается, что компонент указан в первом сегменте URI,
     * иначе подключается компонент для главной страницы
	 * Критерий "включенности" компонента определяется в функции loadComponentConfig
     * @return string $component
     */
    private function detectComponent(){

        $inConf     = cmsConfig::getInstance();

        $component  = '';

        //компонент на главной
        if (!$this->uri && $inConf->homecom) { return $inConf->homecom; }

        //определяем, есть ли слэши в адресе
        $first_slash_pos = strpos($this->uri, '/');

        if ($first_slash_pos){
            //если есть слэши, то компонент это сегмент до первого слэша
            $component  = substr($this->uri, 0, $first_slash_pos);
        } else {
            //если слэшей нет, то компонент совпадает с адресом
            $component  = $this->uri;            
        }


        if (is_dir(PATH.'/components/'.$component)){
            //если компонент определен и существует
            return $component;
        } else {
            //если компонент не существует, считаем что это content
            $this->uri = 'content/'.$this->uri;
            $this->is_content = true;
            return 'content';
        }
        
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Функция подключает файл router.php из папки с текущим компонентом
     * и вызывает метод route_component(), которые возвращает массив правил
     * для анализа URI. Если в массиве найдено совпадение с текущим URI,
     * то URI парсится и переменные, содержащиеся в нем, забиваются в массив $_REQUEST.
     * @return boolean
     */
    private function parseComponentRoute(){

        $component = $this->component;

        //проверяем что компонент и адрес указаны
        if (!$component || !$this->uri) { return false; }

        if(!file_exists('components/'.$component.'/router.php')){ return false; }
		/**
		 * Критерий "включенности" компонента определяется в функции loadComponentConfig
		 */
        //подключаем список маршрутов компонента
        $this->includeFile('components/'.$component.'/router.php');

        $routes = call_user_func('routes_'.$component);

        //перебираем все маршруты
        foreach($routes as $route_id=>$route){

            //сравниваем шаблон маршрута с текущим URI
            preg_match($route['_uri'], $this->uri, $matches);

            //Если найдено совпадение
            if ($matches){

                //удаляем шаблон из параметров маршрута, чтобы не мешал при переборе
                unset($route['_uri']);

                //перебираем параметры маршрута в виде ключ=>значение
                foreach($route as $key=>$value){
                    if (is_integer($key)){
                        //Если ключ - целое число, то значением является сегмент URI
                        $_REQUEST[$value] = $matches[$key];
                    } else {
                        //иначе, значение берется из маршрута
                        $_REQUEST[$key]   = $value;
                    }
                }

                //раз найдено совпадение, прерываем цикл
                break;

            }

        }

        return true;

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Генерирует тело страницы, вызывая нужный компонент
     */
    public function proceedBody(){

        $inPage         = cmsPage::getInstance();
        $inDB           = cmsDatabase::getInstance();        
        $inConf         = cmsConfig::getInstance();
        $is_component   = false;
        $component      = $this->component;

        //проверяем что компонент указан
        if (!$component) { return false; }
		/**
		 * Критерий "включенности" компонента определяется в функции loadComponentConfig
		 */
        //проверяем что в названии только буквы и цифры
        if (!preg_match("/^([a-z0-9])+$/", $component)){ cmsCore::error404(); }
        
        $this->loadLanguage('components/'.$component);

        //проверяем наличие компонента
        if(!file_exists('components/'.$component.'/frontend.php')){
            $inPage->page_body = '<p>Компонент не найден!</p>';
            return false;
        }

        //парсим адрес и заполняем массив $_REQUEST (временное решение)
        $this->parseComponentRoute();

        ob_start();

            require('components/'.$component.'/frontend.php');
            call_user_func($component);

        if ($inConf->back_btn && $menuid != 1 && $inPage->back_button) {
            echo "<p><a href='javascript:history.go(-1)' class=\"backlink\">&larr; Назад</a></p>";
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
     * Инициализирует вложенные множества и возвращает объект CCelkoNastedSet
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
     * Проверяет, нужно ли показывать сплеш-страницу (приветствие)
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
     * Возвращает ключевые слова для заданного текста
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
     * Проверяет наличие переменной $var во входных параметрах
     * @param string $var
     * @return bool
     */
    public function inRequest($var){
        return isset($_REQUEST[$var]);
    }
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Проверяет наличие переменной $var во входных параметрах
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
     * Возвращает предыдущий URL для редиректа назад. Если находит переменную $_REQUEST['back'], то возвращает ее
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
     * Закачивает файл на сервер и отслеживает ошибки
     * @param string $source
     * @param string $destination
     * @param int $errorCode
     * @return bool
     */
    public function moveUploadedFile($source, $destination, $errorCode){
        $max_size = ini_get('upload_max_filesize');
        $max_size = str_replace('M', 'Мб', $max_size);
        $max_size = str_replace('K', 'Кб', $max_size);

        //Possible upload errors
        $uploadErrors = array(
            UPLOAD_ERR_OK => 'Файл успешно загружен',
            UPLOAD_ERR_INI_SIZE => 'Размер файла превышает допустимый &mdash; '.$max_size,
            UPLOAD_ERR_FORM_SIZE => 'Размер файла превышает допустимый',
            UPLOAD_ERR_PARTIAL => 'Файл был загружен не полностью',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Не найдена папка для временных файлов на сервере',
            UPLOAD_ERR_CANT_WRITE => 'Ошибка записи файла на диск',
            UPLOAD_ERR_EXTENSION => 'Загрузка файла была прервана расширением PHP'
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
     * Загружает класс Smarty
     */
    public function loadSmarty(){
        $this->includeFile('/includes/smarty/libs/Smarty.class.php');
        $this->smarty = new Smarty();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает объект Smarty для дальнейшей работы с шаблоном
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
     * Возвращает массив с настройками модуля
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
     * Сохраняет настройки модуля в базу
     * @param string $plugin_name
     * @param array $config
     * @return bool
     */
    public function saveModuleConfig($module_id, $config) {

        $inDB = cmsDatabase::getInstance();

        //конвертируем массив настроек в YAML
        $config_yaml   = $this->arrayToYaml($config);

        //обновляем модуль в базе
        $update_query  = "UPDATE cms_modules 
                          SET config='{$config_yaml}'
                          WHERE id = {$module_id}";

        $inDB->query($update_query);

        //настройки успешно сохранены
        return true;

    }

    /**
     * Кэширует конфигурацию модуля на время выполнения скрипта
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
     * Возвращает кофигурацию компонента в виде массива
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
     * Сохраняет настройки компонента в базу
     * @param string $plugin_name
     * @param array $config
     * @return bool
     */
    public function saveComponentConfig($component, $config) {

        $inDB = cmsDatabase::getInstance();

        //конвертируем массив настроек в YAML
        $config_yaml   = $this->arrayToYaml($config);

        //обновляем плагин в базе
        $update_query  = "UPDATE cms_components
                          SET config='{$config_yaml}'
                          WHERE link = '{$component}'";

        $inDB->query($update_query);

        //настройки успешно сохранены
        return true;

    }

    /**
     * Кэширует конфигурацию компонента на время выполнения скрипта
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
     * Возвращает массив с установленными в системе фильтрами
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
     * Возвращает количество загрузок файла
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
     * Возвращает тег <img> с иконкой, соответствующей типу файла
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
     * Проверяет доступ группы пользователей к пункту меню
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
     * Определяет группу текущего пользователя и перетирает содержание страницы
     * в случае остутствия у группы доступа к текущему пункту меню
     */
    public function сheckMenuAccess(){
        $inPage = cmsPage::getInstance();
        $inUser = cmsUser::getInstance();
        global $menuid;
        $group_id = $inUser->group_id;
        if ($menuid!=0){
            if(!$this->isMenuAccess($menuid, $group_id)){
                if (!$inUser->id){
                    $inPage->page_body = '<p>Доступ запрещен</p>';
                } else {
                    if (!$inUser->is_admin){
                        $inPage->page_body = '<p>Доступ запрещен</p>';
                    }
                }
            }
        }
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает заголовок текущего пункта меню
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
     * Возвращает ссылку на пункт меню
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
     * Возвращает название шаблона, назначенного на пункт меню
     * Если используется шаблон по-умолчанию, то возвращает false
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
     * Возвращает true если URI страницы и ссылка активного пункта меню совпали полностью
     * @return boolean
     */
    public function isMenuIdStrict() {
        
        return $this->is_menu_id_strict;
        
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Возвращает ID текущего пункта меню
     * @return int
     */
    public function menuId(){

        //если menu_id был определен ранее, то вернем и выйдем
        if ($this->menu_id) { return $this->menu_id; }

        $view       = $this->request('view', 'str', '');
        
        if ($this->is_content){
            $uri = substr($this->uri, strlen('content/'));
        } else {
            $uri = $this->uri;
        }

        $uri        = '/'.$uri;

        //флаг, показывающий было совпадение URI и ссылки пунта меню
        //полным или частичным
        $is_strict  = false;

        //главная страница?
        $menuid     = ($uri == '/' ? 1 : 0);
        if ($menuid == 1) {
            $this->is_menu_id_strict = 1;
            return $menuid;
        }

        //перевернем массив меню чтобы перебирать от последнего пункта к первому
        $menu       = array_reverse($this->menu_struct);

        //перебираем меню в поисках текущего пункта
        foreach($menu as $item){

            if (!$item['link']) { continue; }

            //полное совпадение ссылки и адреса?
            if ($uri == $item['link']){
                $menuid = $item['id'];
                $is_strict = true; //полное совпадение
                break;
            }

            //частичное совпадение ссылки и адреса (по началу строки)?
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
     * Загружает данные о текущем пункте меню
     * @return array
     */
    public function getMenuItem($menuid){

        $inDB = cmsDatabase::getInstance();

        return $this->menu_struct[$menuid];
        
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Загружает всю структуру меню
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
     * Возвращает ID пункта меню, к которому привязан компонент
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
     * Возвращает прямую ссылку на пункт меню по его типу и опции
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
     * Возвращает элементы <option> для списка записей из указанной таблицы БД
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
     * Возвращает элементы <option> для списка записей из указанной таблицы БД c вложенными множествами
     * @param string $table таблица
     * @param int $selected id выделенного элемента
     * @param string $differ идентификатор множества (NSDiffer)
     * @param string $need_field выводить только элементы содержащие указанное поле
     * @param int $rootid корневой элемент
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
     * Возвращает элементы <option> для списка баннерных позиций
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
     * Возвращает элементы <option> для списка типов доски объявлений
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
     * Возвращает элементы <option> для списка городов доски объявлений
     * @param string $selected
     * @param string $none_label
     * @return html
     */
    public function boardCities($selected='', $none_label = 'Все города'){
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
     * Возвращает элементы <option> для списка шаблонов
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
     * Возвращает элементы <option> для списка языков
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
     * Регистрирует тип цели для рейтингов в базе
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
     * Удаляет все рейтинги для указанной цели
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
     * Подключает комментарии
     */
    public function includeComments(){
        include_once PATH."/components/comments/frontend.php";
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Регистрирует тип цели для комментариев в базе
     * @param string $target - Цель
     * @param string $component - Компонент
     * @param string $title - Название цели во множ.числе (например "Статьи")
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
     * Удаляет все комментарии для указанной цели
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
     * Возвращает количество комментариев для указанной цели
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
     * Возвращает панель с выбором страниц для статьи
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
            $html .= '<span class="pagebar_title"><strong>Страницы: </strong></span>';
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
     * Проверяет, установлен ли компонент
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
     * Переводит название месяца в дате на русский
     * @param string $datestr
     * @return string
     */
    public function getRusDate($datestr){
        $datestr = str_replace('January', 'Январь', $datestr);
        $datestr = str_replace('February', 'Февраль', $datestr);
        $datestr = str_replace('March', 'Март', $datestr);
        $datestr = str_replace('April', 'Апрель', $datestr);
        $datestr = str_replace('May', 'Май', $datestr);
        $datestr = str_replace('June', 'Июнь', $datestr);
        $datestr = str_replace('July', 'Июль', $datestr);
        $datestr = str_replace('August', 'Август', $datestr);
        $datestr = str_replace('September', 'Сентябрь', $datestr);
        $datestr = str_replace('October', 'Октябрь', $datestr);
        $datestr = str_replace('November', 'Ноябрь', $datestr);
        $datestr = str_replace('December', 'Декабрь', $datestr);
        return $datestr;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	static function dateFormat($date, $is_full_m = true, $is_time=false, $is_now_time = true){
	    global $_LANG;
		// получаем значение даты и времени
		list($day, $time) = explode(' ', $date);
		switch( $day ) {
		// Если дата совпадает с сегодняшней
		case date('Y-m-d'):
					$result = ''.$_LANG['TODAY'].'';
					if ($is_now_time && $time) {
						list($h, $m, $s)  = explode(':', $time);
						$result .= ' '.$_LANG['IN'].' '.$h.':'.$m;
					}
					break;
		//Если дата совпадает со вчерашней
		case date( 'Y-m-d', mktime(0, 0, 0, date("m")  , date("d")-1, date("Y")) ):
					$result = ''.$_LANG['YESTERDAY'].'';
					if ($is_now_time && $time) {
						list($h, $m, $s)  = explode(':', $time);
						$result .= ' '.$_LANG['IN'].' '.$h.':'.$m;
					}
					break;
			default: {
				// Разделяем отображение даты на составляющие
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
				// Замена числового обозначения месяца на словесное (склоненное в падеже)
				if ($is_full_m){
					$m = str_replace($month_int, $month_full_str, $m);
				}else{
					$m = str_replace($month_int, $month_short_str, $m);
				}
				// Замена чисел 01 02 на 1 2
				$d = str_replace($day_int, $day_norm, $d);
				// Формирование окончательного результата
				$result = $d.' '.$m.' '.$y;
				if( $is_time && $time)   {
					// Получаем отдельные составляющие времени
					// Секунды нас не интересуют
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
        $months['00'] = 'Январь';
        $months['01'] = 'Февраль';
        $months['02'] = 'Март';
        $months['03'] = 'Апрель';
        $months['04'] = 'Май';
        $months['05'] = 'Июнь';
        $months['06'] = 'Июль';
        $months['07'] = 'Август';
        $months['08'] = 'Сентябрь';
        $months['09'] = 'Октябрь';
        $months['10'] = 'Ноябрь';
        $months['11'] = 'Декабрь';

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
     * Проверяет, является ли пользователь администратором
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
     * Проверяет, является ли пользователь редактором
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
     * Возвращает массив с администраторскими правами доступа текущего пользователя
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
     * Проверяет что администратор имеет право на указанное действие
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
     * Проверяет что пользователь имеет право на указанное действие
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
        //Если magic_quotes_gpc = On, сначала убираем экранирование
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
     * Удаляет теги script iframe style meta
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
     * Проверяет совпадения кода каптчи с кодом введенным пользователем
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
     * Создает и отправляет письмо электронной почтой
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
     * Проверяет наличие кэша для указанного контента
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
     * Возвращает кэш указанного контента
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
     * Сохраняет переданный кэш указанного контента
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
     * Удаляет кэш указанного контента
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
     * Возвращает код баннера с минимальный количеством показов для указанной позиции
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
     * Возвращает код баннера по ID
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
        $string = preg_replace ('/[^a-zA-Zа-яА-Я0-9\-]/i', '-', $str);
        $string = rtrim($string, '-');

        while(strstr($string, '--')){ $string = str_replace('--', '-', $string); }

        $ru_en = array(
                        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d',
                        'е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z',
                        'и'=>'i','й'=>'i','к'=>'k','л'=>'l','м'=>'m',
                        'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s',
                        'т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c',
                        'ч'=>'ch','ш'=>'sh','щ'=>'sh','ъ'=>'','ы'=>'y',
                        'ь'=>'','э'=>'ye','ю'=>'yu','я'=>'ja'
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
     * Выводит словами разницу между текущей и указанной датой
     * @param string $date
     * @return string
     */
    public static function dateDiffNow($date) {

        $diff_str   = '';

        $now        = time();
        $date       = strtotime($date);

        if ($date == 0) { return 'не известно'; }

        $diff_sec   = $now - $date;

        $diff_day   = (string)round($diff_sec/60/60/24);
        $diff_hour  = (string)round(($diff_sec/60/60) - ($diff_day*24));
        $diff_min   = (string)round(($diff_sec/60)-($diff_hour*60));

        //Выводим разницу в днях
        if ($diff_day){

            if ($diff_day == 11 || $diff_day == 12 || $diff_day == 13 || $diff_day == 14) {
                $diff_str = $diff_day. " дней";
            } elseif ($diff_day[strlen($diff_day)-1] == "2" || $diff_day[strlen($diff_day)-1] == "3" || $diff_day[strlen($diff_day)-1] == "4") {
                $diff_str = $diff_day." дня";
            } elseif($diff_day[strlen($diff_day)-1] == "1") {
                $diff_str = $diff_day. " день";
            } else {
                $diff_str = $diff_day. " дней";
            }

            return $diff_str;

        }

        //Выводим разницу в часах
        if ($diff_hour){

            if ($diff_hour == 1 || $diff_hour == 21) $diff_str = $diff_hour." час"; else
            if ($diff_hour == 2 || $diff_hour == 3 or $diff_hour == 4 || $diff_hour == 22 || $diff_hour == 23) $diff_str = $diff_hour." часа";
            else $diff_str = $diff_hour." часов";

            return $diff_str;

        }

        //Выводим разницу в минутах
        if ($diff_min){

            if ($diff_min == "11" || $diff_min == "12" || $diff_min == "13" || $diff_min == "14") {
                $diff_str = $diff_min. " минут";
            } elseif ($diff_min[strlen($diff_min)-1] == "2" || $diff_min[strlen($diff_min)-1] == "3" || $diff_min[strlen($diff_min)-1] == "4") {
                $diff_str = $diff_min." минуты";
            } elseif($diff_min[strlen($diff_min)-1] == "1") {
                $diff_str = $diff_min. " минуту";
            } else {
                $diff_str = $diff_min. " минут";
            }

            return $diff_str;

        }

        $diff_str = 'меньше минуты';

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
//Функции ниже оставлены для совместимости со старыми шаблонами
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
