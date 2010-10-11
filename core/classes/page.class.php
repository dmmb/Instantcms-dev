<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/

class cmsPage {

    public $title       = '';
    public $back_button = true;

    public $page_head   = array();
    public $page_keys   = '';
    public $page_desc   = '';
    public $page_body   = '';

    public $pathway     = array();

    public $captcha_count = 1;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private static $instance;

    private function __construct() {
        $this->title = $this->homeTitle();
    }

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Добавляет указанный тег в <head> страницы
 * @param string $tag
 * @return true
 */
public function addHead($tag){
    $this->page_head[] = $tag;
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Добавляет тег <script> с указанным путем
 * @param string $src - Первый слеш не требуется
 * @return true
 */
public function addHeadJS($src){
    $src = '/'.$src;
    $this->page_head[] = '<script language="JavaScript" type="text/javascript" src="'.$src.'"></script>';
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Добавляет тег <link> с указанным путем к CSS-файлу
 * @param string $src - Первый слеш не требуется
 * @return true
 */
public function addHeadCSS($src){
    $src = '/'.$src;
    $this->page_head[] = '<link href="'.$src.'" rel="stylesheet" type="text/css" />';
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Возвращает заголовок главной страницы
 * @global array $_CFG
 * @return string
 */
public function homeTitle(){
    $inConf = cmsConfig::getInstance();
    if ($inConf->hometitle) { return $inConf->hometitle; }
    else { return $inConf->sitename; }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Устанавливает заголовок страницы
 */
public function setTitle($title=''){
    $this->title = $title;
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Выводит заголовок h1 на странице
 */
public function printHeading($heading=''){
    echo '<h1 class="con_heading">'.$heading.'</h1>';
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Устанавливает ключевые слова страницы
 */
public function setKeywords($keywords){
    $this->page_keys = $keywords;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Устанавливает описание страницы
 */
public function setDescription($text){
    $this->page_desc = $text;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Печатает название сайта из конфига
 * @return true
 */
public function printSitename(){
    require('includes/config.inc.php');
    echo $_CFG['sitename'];
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Включает/выключает показ ссылки "Назад" внизу страницы
 * @return true
 */
public function backButton($show=true){
    $this->back_button = (bool)$show;
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Печатает головную область страницы
 */
public function printHead(){

    $inCore = cmsCore::getInstance();
    $inConf = cmsConfig::getInstance();

    $this->page_head = cmsCore::callEvent('PRINT_PAGE_HEAD', $this->page_head);

    ob_start();
    
    //Заголовок
    $title = strip_tags($this->title);
    $title = ($inCore->menuId()==1 ? $this->homeTitle() : $title.' - '.$inConf->sitename);
    echo '<title>'.$title.'</title>' ."\n";
    //Ключевые слова
    if (!$this->page_keys) { $this->page_keys = $inConf->keywords; }
    echo '<meta name="keywords" content="'.$this->page_keys.'" />' ."\n";
    //Описание
    if (!$this->page_desc) { $this->page_desc = $inConf->metadesc; }
    echo '<meta name="description" content="'.$this->page_desc.'" />' ."\n";
    //Генератор
    echo '<meta name="generator" content="InstantCMS - www.instantcms.ru"/>' ."\n";

    //jQuery библиотека
    echo '<script type="text/javascript" src="/includes/jquery/jquery.js"></script>' ."\n";
    //JS-функции ядра
    echo '<script type="text/javascript" src="/core/js/common.js"></script>' ."\n";

    //Остальные JS-файлы
    foreach($this->page_head as $key=>$value) {
        if(strstr($this->page_head[$key], '<script')) {
            echo $this->page_head[$key] ."\n"; unset($this->page_head[$key]);
        }
    }

    //CSS-файлы
    foreach($this->page_head as $key=>$value) {
        if(strstr($this->page_head[$key], '<link')) {
            echo $this->page_head[$key] ."\n"; unset($this->page_head[$key]);
        }
    }

    //Оставшиеся теги
    foreach($this->page_head as $key=>$value) { echo $this->page_head[$key] ."\n"; }
    
    echo ob_get_clean();

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Печатает подвал страницы
 * @global array $_CFG
 */
public function printFooter(){
    global $_CFG;
    $currentYear = date('Y');
    echo '<div id="footer" align="center"><a href="/" title="'.$_CFG['sitename'].'">'.$_CFG['sitename'].'</a> &copy; '.$currentYear;
        echo '<br/>';
        echo 'Сайт работает на <a href="http://www.instantcms.ru/">InstantCMS</a> v'.CORE_VERSION.'<br/>';
    echo '<div>';
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Выводит тело страницы (результат работы компонента)
 * @global array $_CFG
 */
public function printBody(){
    global $_CFG;
    if ($_CFG['slight']){
        if (isset($_SESSION['searchquery'])){
            if ($_REQUEST['view']!='search'){
                $this->page_body = str_replace($_SESSION['searchquery'], '<span class="search_match">'.$_SESSION['searchquery'].'</span>', $this->page_body);
                $this->page_body = str_replace(ucfirst($_SESSION['searchquery']), '<span class="search_match">'.ucfirst($_SESSION['searchquery']).'</span>', $this->page_body);
                $this->page_body = str_replace(strtolower($_SESSION['searchquery']), '<span class="search_match">'.strtolower($_SESSION['searchquery']).'</span>', $this->page_body);
                unset($_SESSION['searchquery']);
            }
        }
    }

    $this->page_body = cmsCore::callEvent('PRINT_PAGE_BODY', $this->page_body);

    echo $this->page_body;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Печатает глубиномер
 * @param string $separator
 */
public function printPathway($separator='&rarr;'){

    //Проверяем, на главной мы или нет
    require($_SERVER['DOCUMENT_ROOT'].'/includes/config.inc.php');
    if (($GLOBALS['menuid']==1 && !$_CFG['index_pw']) || !$_CFG['show_pw']) { return false; }

    echo '<div class="pathway">';
    foreach($this->pathway as $key => $value){
        echo '<a href="'.$this->pathway[$key]['link'].'" class="pathwaylink">'.$this->pathway[$key]['title'].'</a> ';
        if ($key<sizeof($this->pathway)-1) {
            echo ' '.$separator.' ';
        }
    }
    echo '</div>';

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Добавляет звено к глубиномеру
 * @param string $title
 * @param string $link
 * @return bool
 */
public function addPathway($title, $link=''){
    //Если ссылка не указана, берем текущий URI
    if (empty($link)) { $link = $_SERVER['REQUEST_URI']; }
    //Проверяем, есть ли уже в глубиномере такое звено
    $already = false;
    foreach($this->pathway as $key => $val){
        if ($this->pathway[$key]['title'] == $title || $this->pathway[$key]['link'] == $link){
            $already = true;
        }
    }
    //Если такого звена еще нет, добавляем его
    if(!$already){
        $this->pathway[] = array('title'=>$title, 'link'=>$link);
    }
    return true;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Добавляет к глубиномеру путь до указанного пункта меню
 * @param int $menuid
 */
public function addMenuPathway($menuid){

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();

    //Получаем путь к пункту меню
    $rs_item = $inDB->query("SELECT * FROM cms_menu WHERE id = $menuid");

    if ($inDB->num_rows($rs_item)){
        $current_item   = $inDB->fetch_assoc($rs_item);
        
        $left_key       = $current_item['NSLeft'];
        $right_key      = $current_item['NSRight'];

        $sql            = "SELECT id, title, link, linktype
                           FROM cms_menu
                           WHERE NSLeft <= $left_key AND NSRight >= $right_key AND parent_id > 0
                           ORDER BY NSLeft";

        $rs_rows = $inDB->query($sql);

        while($item=$inDB->fetch_assoc($rs_rows)){
            if ($item['id']>1){
                $this->addPathway($item['title'], $inCore->menuSeoLink($item['link'], $item['linktype'], $item['id']));
            }
        }        
    }

    return true;

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Выводит на экран шаблон сайта
 * Какой именно шаблон выводить определяют константы TEMPLATE и TEMPLATE_DIR
 * Эти константы задаются в файле /index.php
 */
public function showTemplate(){

    $inCore = cmsCore::getInstance();

    $menu_template = $inCore->menuTemplate($inCore->menuId());

    if ($menu_template && file_exists(PATH.'/templates/'.$menu_template.'/template.php')){
        require(PATH.'/templates/'.$menu_template.'/template.php');
        return;
    }

    if (file_exists(TEMPLATE_DIR.'template.php')){
        require(TEMPLATE_DIR.'template.php');
        return; 
    }
    
    $inCore->halt('Шаблон "'.TEMPLATE.'" не найден.');

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Подключает файл из папки с шаблоном
 * Если в папке текущего шаблона такой файл не найден, ищет в дефолтном
 * @param string $file, например "special/error404.html"
 * @return <type>
 */
public function includeTemplateFile($file){

    if (file_exists(TEMPLATE_DIR.$file)){
        include(TEMPLATE_DIR.$file);
        return true;
    }

    if (file_exists(DEFAULT_TEMPLATE_DIR.$file)){
        include(DEFAULT_TEMPLATE_DIR.$file);
        return true;
    }

    return false;

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function showSplash(){

    $inCore = cmsCore::getInstance();

    if ($this->includeTemplateFile('splash/splash.php')){

        $inCore->setCookie('splash', md5('splash'), time()+60*60*24*30);
        $_SESSION['splash'] = 1;
        return true;
        
    }

    return false;

}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Возвращает html с пользовательскими ссылками (профиль, сообщения, выход)
 * @return html
 */
public function getUserLinks(){
    $inCore = cmsCore::getInstance();
    $inUser = cmsUser::getInstance();
    $html   = '';
    if(!isset($inUser->id)){
            $html .= '<a href="/registration" id="ht_reg">Регистрация</a><a href="javascript:auth()" id="ht_auth">Авторизация</a>';
    }
    else{
        $uid    = isset($inUser->id) ? $inUser->id : 0;
        $newmsg = cmsUser::isNewMessages($uid);
        $html  .= '<a href="'.cmsUser::getProfileURL($inUser->login).'" id="ht_profile">Мой профиль</a>';
        if (!$newmsg){
                $html .= '<a href="/users/'.$uid.'/messages.html" id="ht_messages">Cообщения</a>';
        }
        else{
                $html .= '<a href="/users/'.$uid.'/messages.html" id="ht_messages_new">Cообщения '.strip_tags($newmsg).'</a>';
        }
        $html .= '<a href="/logout" id="ht_logout">Выход</a>';
    }
    return $html;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Возвращает количество модулей, назначенных на указанную позицию для текущего пункта меню
 * @param string $position
 * @return int
 */
public function countModules_old($position){
    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
    $menuid = $inCore->menuId();
    $sql = "SELECT m.id
            FROM cms_modules m, cms_modules_bind mb
            WHERE m.position = '$position' AND m.published = 1 AND m.id = mb.module_id AND (mb.menu_id = $menuid OR mb.menu_id = 0)
            ";
    $result = $inDB->query($sql) ;
    return mysql_num_rows($result);
}

public function countModules($position){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
	
	if ($inUser->id) {
    	//if authorized, but not admin, get group_id
        if (!$inCore->userIsAdmin($inUser->id)){ 
			$gid = $_SESSION['user']['group_id']; 
		} else { 
			$gid = false;  
		}
    } else { 
		$gid = cmsUser::getGuestGroupId();  
	}

    //if not admin, add access check to sql
    if ($gid !== false) { $group_sql = "AND ((m.allow_group=-1) OR (m.allow_group=$gid))"; } else { $group_sql = ""; }

    if (!$inCore->isMenuIdStrict()){ $strict_sql = "AND (m.is_strict_bind = 0)"; } else { $strict_sql = ""; }

	$menuid = $inCore->menuId();
    $sql = "SELECT m.id
            FROM cms_modules m, cms_modules_bind mb
            WHERE m.position = '$position' AND
                  m.published = 1 AND
                  m.id = mb.module_id AND
                  (mb.menu_id = $menuid OR mb.menu_id = 0)
                  $strict_sql
                  $group_sql
            ";


    $result = $inDB->query($sql) ;
    return $inDB->num_rows($result);
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Выводит модули для указанной позиции и текущего пункта меню
 * @param string $position
 * @return html
 */
public function printModules($position){
    $inCore     = cmsCore::getInstance();
    $inDB       = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    global $_CFG;
    global $_LANG;
    //check menu item number
    $menuid = $inCore->menuId();
    //check position
    if (!$position) { return false; }
    if ($position=='top' && @$_REQUEST['view']=='search') { return true; }

    if ($inUser->id) {
        //if authorized, but not admin, get group_id
        if (!$inCore->userIsAdmin($inUser->id)){ $gid = $_SESSION['user']['group_id']; } else { $gid = false; }
    } else { $gid = cmsUser::getGuestGroupId(); }

    //if not admin, add access check to sql
    if ($gid !== false) { $group_sql = "AND ((m.allow_group=-1) OR (m.allow_group=$gid))"; } else { $group_sql = ""; }

    if (!$inCore->isMenuIdStrict()){ $strict_sql = "AND (m.is_strict_bind = 0)"; } else { $strict_sql = ""; }

    //get modules info
    $sql = "SELECT *, m.id as mid, m.template as tpl
            FROM cms_modules m, cms_modules_bind mb
            WHERE m.position = '$position' AND 
                  m.published = 1 AND
                  m.id = mb.module_id AND
                  (mb.menu_id = $menuid OR mb.menu_id = 0)
                  $strict_sql
                  $group_sql
            ORDER BY m.ordering ASC
            ";

    $result = $inDB->query($sql);
    //draw module
    if($inDB->num_rows($result)){
        while ($mod = $inDB->fetch_assoc($result)){
            
            $modulefile = PATH.'/modules/'.$mod['content'].'/module.php';

            if (!$mod['user']) { cmsCore::loadLanguage('modules/'.$mod['content']); }
            
            if( !$mod['is_external'] ){
                    //PROCESS FILTERS
                    $filters = $inCore->getFilters();
                    if ($filters){
                        foreach($filters as $id=>$_data){
                            require_once 'filters/'.$_data['link'].'/filter.php';
                            $_data['link']($mod['content']);
                        }
                    }
                    $callback = true;
                    $modulebody = $mod['content'];
            }

            if( $mod['is_external'] ){
                if (file_exists($modulefile)){
                    //load module file
                    require_once $modulefile;
                    //run module and get its output to $modulebody

                    if ($mod['cache'] && $inCore->isCached('module', $mod['mid'], $mod['cachetime'], $mod['cacheint'])){
                        $modulebody = $inCore->getCache('module', $mod['mid']);
                        $callback = true;
                    } else {
                                $config = $inCore->yamlToArray($mod['config']);
                                $inCore->cacheModuleConfig($mod['module_id'], $config);

                                ob_start();
                                $callback = $mod['content']($mod['module_id']);
                                $modulebody = ob_get_clean();
                                if($mod['cache']) { $inCore->saveCache('module', $mod['mid'], $modulebody); }
                           }
                }
            }

            if ( $callback ){ //if module returns TRUE
                $module             = array();
                $mod['body']        = $modulebody;
                $smarty             = $inCore->initSmartyModule();
                $_CFG['fastcfg']    = isset($_CFG['fastcfg']) ? $_CFG['fastcfg'] : 0;
                
                if ($_CFG['fastcfg'] && $inCore->userIsAdmin($inUser->id)){
                    $smarty->assign('cfglink', '/admin/index.php?view=modules&do=edit&id='.$mod['mid']);
                }

                $smarty->assign('mod', $mod);

                $module_tpl = file_exists($smarty->template_dir.'/'.$mod['tpl']) ? $mod['tpl'] : 'module.tpl';

                $smarty->display($module_tpl);
            }

        }//while
    }//if num rows
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Возвращает html-код каптчи
 * @param string $input_name
 * @return html
 */
public static function getCaptcha($input_name='code'){
    ob_start();
    $captcha_count = self::getInstance()->captcha_count;
    $input_id = 'kcaptcha' . $captcha_count;
    ?>
        <table>
            <tr>
                <td valign="middle" width="130"><img id="<?php echo $input_id; ?>" class="captcha" src="/includes/codegen/cms_codegen.php" border="0" /></td>
                <td valign="middle">
                    <div>Введите код:</div>
                    <div><input name="<?php echo $input_name; ?>" type="text" style="width:120px" /></div>
                    <div><a href="javascript:reloadCaptcha('<?php echo $input_id;  ?>')"><small>Обновить картинку</small></a></div>
                </td>
            </tr>
        </table>
    <?php
    self::getInstance()->captcha_count += 1;
    return ob_get_clean();
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Генерирует и возращает html-код указанной формы из конструктора форм
 * @param int $form_id
 * @param bool $admin
 * @param bool $showtitle
 * @return html
 */
public function buildForm($form_id, $admin=false, $showtitle=true){

    $inDB = cmsDatabase::getInstance();

    $html = '';

    //GET FORM DATA
    $sql    = "SELECT * FROM cms_forms WHERE id = $form_id LIMIT 1";
    $result = $inDB->query($sql) ;

    if (!$inDB->num_rows($result)) { return false; }
    else {
            $form = $inDB->fetch_assoc($result);
            //BUILD FORM
            if ($showtitle) { $html .= '<h3 class="userform_title">'.$form['title'].'</h3>'; }
            if($form['description']) { $html .= '<p>'.$form['description'].'</p>'; }

            //CHECK PREVIOUS FORM SUBMITTION
            if(isset($_SESSION['form_ok'.$form_id])) {
                $html .= '<p style="color:green">Форма успешно отправлена</p>';
                unset($_SESSION['form_ok'.$form_id]);
                return $html;
            }

            //CHECK PREVIOUS FORM ERRORS
            if(isset($_SESSION['form_error'.$form_id])) {
                $html .= '<p style="color:red">'.$_SESSION['form_error'.$form_id].'</p>';
                unset($_SESSION['form_error'.$form_id]);
            }

            $html .= '<form name="userform" id="userform" action="/forms/process" method="POST">';
            $html .= '<input type="hidden" name="form_id" value="'.$form_id.'">';

                //GET FIELDS DATA
                $sql = "SELECT * FROM cms_form_fields WHERE form_id = $form_id ORDER BY ordering ASC";
                $result = $inDB->query($sql) ;

                if ($inDB->num_rows($result)){
                    //BUILD FORM FIELDS
                    $html .= '<table class="userform_table">';
                    while($field = $inDB->fetch_assoc($result)){
                        $html .= '<tr><td class="userform_fieldtitle">';
                            if ($admin) { $html .= '[<font color="gray">'.$field['ordering'].'</font>] '; }
                            $html .= $field['title'];
                            if ($field['mustbe']) { $html .= '<span style="color:red;font-size:20px">*</span>'; }
                            if($admin) {
                                $html .= '<a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=del_field&form_id='.$form_id.'&item_id='.$field['id'].'" title="Удалить"><img src="/admin/images/actions/delete.gif" border="0" /></a>';
                                $html .= '<a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=up_field&form_id='.$form_id.'&item_id='.$field['id'].'" title="Переместить вверх"><img src="/admin/images/actions/top.gif" border="0" /></a>';
                                $html .= '<a href="?view=components&do=config&id='.$_REQUEST['id'].'&opt=down_field&form_id='.$form_id.'&item_id='.$field['id'].'" title="Переместить вниз"><img src="/admin/images/actions/down.gif" border="0" /></a>';
                            }
                        $html .= '</td></tr>';
                        $html .= '<tr><td>'.$this->buildFormField($form_id, $field).'</td></tr>';
                    }
                    if (!$admin){
                        //CAPTCHA
                        $html .= '<tr><td>';
                            $html .= cmsPage::getCaptcha();
                        $html .= '</td></tr>';
                        //Submit buttons
                        $html .= '<tr><td><div style="margin-top:10px">';
                            $html .= '<input type="submit" value="Отправить" /> ';
                            $html .= '<input type="reset" value="Очистить" />';
                        $html .= '</div></td></tr>';
                    }
                    $html .= '</table>';

                } else { $html .= '<p>В форме нет полей.</p>'; }

            $html .= '</form>';
    }
    if(isset($_SESSION['form_last'.$form_id])){ unset($_SESSION['form_last'.$form_id]); }
    return $html;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Генерирует и возращает html-код поля формы
 * @param array $field
 * @param string $default
 * @return html
 */
public function buildFormField($form_id, $field, $default=''){

    $html   = '';
    $cfg    = unserialize($field['config']);

    $style  = 'background-color:white';

    if(isset($_SESSION['form_last'.$form_id])){
        if (isset($_SESSION['form_last'.$form_id][$field['id']])){
            $cfg['default'] = $_SESSION['form_last'.$form_id][$field['id']];
        }
    }

    if ($default){
        $cfg['default'] = $default;
    }

    switch($field['kind']){
        case 'text': $html .= '<input type="text"
                                      name="field['.$field['id'].']"
                                      maxlength="'.$cfg['max'].'"
                                      size="'.$cfg['size'].'"
                                      value="'.$cfg['default'].'"
                                      style="'.$style.'"
                                      />';
                     break;
        case 'textarea': $html .= '<textarea name="field['.$field['id'].']"
                                             maxlength="'.$cfg['max'].'"
                                             cols="'.$cfg['size'].'"
                                             rows="'.$cfg['rows'].'" style="'.$style.'">'.$cfg['default'].'</textarea>';
                         break;
        case 'checkbox':  $html .= '<label><input type="radio" name="field['.$field['id'].']" value="Да" ';
                          if($cfg['checked']) { $html .= 'checked'; }
                          $html .= '/>Да</label> ';
                          $html .= '<label><input type="radio" name="field['.$field['id'].']" value="Нет" ';
                          if(!$cfg['checked']) { $html .= 'checked'; }
                          $html .= '/>Нет</label> ';
                         break;
        case 'radiogroup': $items = explode('/', $cfg['items']);
                           foreach($items as $i){
                                $i = str_replace('_', ' ', $i);
                               $html .= '<label><input type="radio" name="field['.$field['id'].']" value="'.$i.'" ';
                               if($i == @$cfg['default']) { $html .= 'checked'; }
                               $html .= ' />'.$i.'</label><br/>';
                           }
                         break;
        case 'list': $items = explode('/', $cfg['items']);
                     $html .= '<select style="'.$style.'" name="field['.$field['id'].']">';
                     foreach($items as $i){
                          $i = str_replace('_', ' ', $i);
                          $html .= '<option value="'.$i.'"';
                          if($i == @$cfg['default']) { $html .= 'selected'; }
                          $html .= ' >'.$i.'</option>';
                     }
                     $html .= '</select>';
                     break;
        case 'menu': $items = explode('/', $cfg['items']);
                     $html .= '<select style="'.$style.'" name="field['.$field['id'].']" size="8">';
                     foreach($items as $i){
                          $i = str_replace('_', ' ', $i);
                          $html .= '<option value="'.$i.'">'.$i.'</option>';
                     }
                     $html .= '</select>';
                     break;
    }
    return $html;

}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Разбивает текст на слова и делает каждое слово ссылкой, добавляя в его начало $link
 * @param string $link
 * @param string $text
 * @return html
 */
public static function getMetaSearchLink($link, $text){
    $html='';
    $text = strip_tags($text);
    $text = trim($text);
    if (!strstr($text, ',')){
        $html .= '<a href="'.$link.urlencode($text).'">'.$text.'</a>';
    } else {
        $text = str_replace(', ', ',', $text);
        $text = str_replace('&nbsp;', '', $text);
        $text = str_replace('&#8217;', "'", $text);
        $text = str_replace('&quot;', '"', $text);
        $words = array();
        $words = explode(',', $text);

        $n=0;
        foreach($words as $key=>$value){
            $n++;
            $value = strip_tags($value);
            $value = str_replace("\r", '', $value);
            $value = str_replace("\n", '', $value);
            $value = trim($value, ' .');
            $html .= '<a href="'.$link.urlencode($value).'">'.$value.'</a>';
            if ($n<sizeof($words)) { $html .= ', '; } else { $html .= '.'; }
        }

    }

    return $html;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Возвращает html-код панели для вставки BBCode
 * @param string $field_id
 * @param bool $images
 * @param string $placekind
 * @return html
 */
public static function getBBCodeToolbar($field_id, $images=0, $placekind='forum'){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inUser = cmsUser::getInstance();

    $inPage->addHeadJS('core/js/smiles.js');
    $inPage->addHeadJS('includes/jquery/upload/ajaxfileupload.js');

    $html = '<a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[b]\', \'[/b]\')" title="Жирный">
                <img src="/includes/bbcode/images/b.gif" border="0" alt="Жирный" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[i]\', \'[/i]\')" title="Курсив">
                <img src="/includes/bbcode/images/i.gif" border="0" alt="Курсив" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[u]\', \'[/u]\')"  title="Подчеркнутый">
                <img src="/includes/bbcode/images/u.gif" border="0" alt="Подчеркнутый" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[s]\', \'[/s]\')"  title="Зачеркнутый">
                <img src="/includes/bbcode/images/s.gif" border="0" alt="Зачеркнутый" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[align=left]\', \'[/align]\')" title="По левому краю">
                <img src="/includes/bbcode/images/align_left.gif" border="0" alt="По левому краю" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[align=center]\', \'[/align]\')" title="По центру">
                <img src="/includes/bbcode/images/align_center.gif" border="0" alt="По центру" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[align=right]\', \'[/align]\')" title="По правому краю">
                <img src="/includes/bbcode/images/align_right.gif" border="0" alt="По правому краю" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[h1]\', \'[/h1]\')" title="Большой заголовок">
                <img src="/includes/bbcode/images/h1.gif" border="0" alt="Большой заголовок" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[h2]\', \'[/h2]\')" title="Средний заголовок">
                <img src="/includes/bbcode/images/h2.gif" border="0" alt="Средний заголовок" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[h3]\', \'[/h3]\')" title="Маленький заголовок">
                <img src="/includes/bbcode/images/h3.gif" border="0" alt="Маленький заголовок" />
             </a>
             <a class="usr_bb_button" href="javascript:addTagQuote(\''.$field_id.'\')" title="Цитата">
                <img src="/includes/bbcode/images/quote.gif" border="0" alt="Цитата" />
             </a>
             <a class="usr_bb_button" href="javascript:addTagUrl(\''.$field_id.'\')" title="Вставить ссылку">
                <img src="/includes/bbcode/images/url.gif" border="0" alt="Вставить ссылку" />
             </a>
             <a class="usr_bb_button" href="javascript:addTagEmail(\''.$field_id.'\')" title="Вставить email">
                <img src="/includes/bbcode/images/email.gif" border="0" alt="Вставить email" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[code=php]\', \'[/code]\')" title="Вставить код">
                <img src="/includes/bbcode/images/code.gif" border="0" alt="Вставить код" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[hide]\', \'[/hide]\')" title="Вставить скрытый текст">
                <img src="/includes/bbcode/images/hide.gif" border="0" alt="Вставить скрытый текст" />
             </a>
             <a class="usr_bb_button" href="javascript:addTag(\''.$field_id.'\', \'[spoiler=Спойлер]\', \'[/spoiler]\')" title="Вставить спойлер">
                <img src="/includes/bbcode/images/spoiler.gif" border="0" alt="Вставить спойлер" />
             </a>
             <a class="usr_bb_button" href="javascript:void(0)" onclick="$(\'#smilespanel\').slideToggle(\'slow\')" title="Вставить смайл">
                <img src="/includes/bbcode/images/smiles.gif" border="0" alt="Вставить смайл" />
             </a>';

    if ($placekind=='blog'){

        $html .= '<a class="usr_bb_button" href="javascript:addTagCut(\''.$field_id.'\')" title="Вставить конец анонса (кат)">
                    <img src="/includes/bbcode/images/cut.gif" border="0" alt="Вставить конец анонса (кат)" />
                  </a>';

    }

    if ($images){
        $html .= '<a class="usr_bb_button" href="javascript:addTagVideo(\''.$field_id.'\')" title="Вставить видео">
                    <img src="/includes/bbcode/images/video.gif" border="0" alt="Вставить видео" />
                 </a>
                 <a class="usr_bb_button" href="javascript:addTagAudio(\''.$field_id.'\')" title="Вставить mp3">
                    <img src="/includes/bbcode/images/audio.gif" border="0" alt="Вставить mp3" />
                 </a>
                 <a class="usr_bb_button" href="javascript:addImage(\''.$field_id.'\')" title="Загрузить и вставить фото">
                    <img src="/includes/bbcode/images/image.gif" border="0" alt="Загрузить и вставить фото" />
                 </a>
                 <a class="usr_bb_button" href="javascript:addAlbumImage()" title="Вставить фото из альбома">
                    <img src="/includes/bbcode/images/albumimage.gif" border="0" alt="Вставить фото из альбома" />
                 </a>
                <div class="usr_bb_button" id="imginsert" style="padding:3px;display:none">
                    <strong>Загрузить фото:</strong> <input type="file" id="attach_img" name="attach_img"/>
                     <input type="button" name="goinsert" value="Вставить" onclick="loadImage(\''.$field_id.'\', \''.session_id().'\', \''.$placekind.'\')" />
                 </div>
                 <div class="usr_bb_button" id="imgloading" style="padding:5px;display:none">
                    Загрузка изображения...
                 </div>
                <div class="usr_bb_button" id="albumimginsert" style="padding:3px;display:none">
                    <strong>Вставить фото:</strong> '.cmsUser::getPhotosList($inUser->id).'
                     <input type="button" name="goinsert" value="Вставить" onclick="insertAlbumImage(\''.$field_id.'\')" />
                 </div>';
    }

    return $html;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Возвращает html-код панели со смайлами
 * @param string $for_field_id
 * @return html
 */
public static function getSmilesPanel($for_field_id){
    $html = '<div class="usr_msg_smilebox" id="smilespanel" style="display:none">';
    if ($handle = opendir(PATH.'/images/smilies')) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && strstr($file, '.gif')){
             $tag = str_replace('.gif', '', $file);
             $dir = '/images/smilies/';

             $html .= '<a href="javascript:addSmile(\''.$tag.'\', \''.$for_field_id.'\');"><img src="'.$dir.$file.'" border="0" /></a> ';
            }
        }

        closedir($handle);
    }
    $html .= '</div>';
    return $html;
}

// AUTOCOMPLETE PLUGIN  /////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Подключает JS и CSS для автокомплита
 */
public function initAutocomplete(){
    $this->addHeadJS('includes/jquery/autocomplete/jquery.autocomplete.min.js');
    $this->addHeadCSS('includes/jquery/autocomplete/jquery.autocomplete.css');
}

/**
 * Возвращает JS-код инициализации автокомплита для указанного поля ввода и скрипта
 * @param string $script
 * @param string $field_id
 * @param bool $multiple
 * @return js
 */
public function getAutocompleteJS($script, $field_id='tags', $multiple=true){
    $multiple = $multiple ? 'true' : 'false';
    return '$("#'.$field_id.'").autocomplete(
                "/core/ajax/'.$script.'.php",
                {
                    width: 280,
                    selectFirst: false,
                    multiple: '.$multiple.'
                }
            );';
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * Возвращает код панели для постраничной навигации
 * @param int $total
 * @param int $page
 * @param int $perpage
 * @param string $link
 * @param array $params
 * @return html
 */
public static function getPagebar($total, $page, $perpage, $link, $params=array()){

    global $_LANG;

    $html = '<div class="pagebar">';
    $html .= '<span class="pagebar_title"><strong>'.$_LANG['PAGES'].': </strong></span>';

    $total_pages = ceil($total / $perpage);

    if ($total_pages < 2) { return; }

    //if more than one page of results
    if($total_pages!=1){

        //configure for the starting links per page
        $max = 4;

        //used in the loop
        $max_links = $max+1;
        $h=1;

        //if page is above max link
        if($page>$max_links){
            //start of loop
            $h=(($h+$page)-$max_links);
        }

        //if page is not page one
        if($page>=1){
            //top of the loop extends
            $max_links = $max_links+($page-1);
        }

        //if the top page is visible then reset the top of the loop to the $total_pages
        if($max_links>$total_pages){
            $max_links=$total_pages+1;
        }

        //next and prev buttons
        if($page>1){

            $href = $link;
            if (is_array($params)){
                foreach($params as $param=>$value){
                    $href = str_replace('%'.$param.'%', $value, $href);
                }
            }
            $html .= ' <a href="'.str_replace('%page%', 1, $href).'" class="pagebar_page">'.$_LANG['FIRST'].'</a> ';
            $html .= ' <a href="'.str_replace('%page%', ($page-1), $href).'" class="pagebar_page">'.$_LANG['PREVIOUS'].'</a> ';

        }

        //create the page links
        for ($i=$h;$i<$max_links;$i++){
            if($i==$page){
                $html .= '<span class="pagebar_current">'.$i.'</span>';
            }
            else{
                $href = $link;
                if (is_array($params)){
                    foreach($params as $param=>$value){
                        $href = str_replace('%'.$param.'%', $value, $href);
                    }
                }
                $href = str_replace('%page%', $i, $href);
                $html .= ' <a href="'.$href.'" class="pagebar_page">'.$i.'</a> ';
            }
        }

        //Next and last buttons
        if(($page >= 1)&&($page!=$total_pages)){
            $href = $link;
            if (is_array($params)){
                foreach($params as $param=>$value){
                    $href = str_replace('%'.$param.'%', $value, $href);
                }
            }
            $html .= ' <a href="'.str_replace('%page%', ($page+1), $href).'" class="pagebar_page">'.$_LANG['NEXT'].'</a> ';
            $html .= ' <a href="'.str_replace('%page%', $total_pages, $href).'" class="pagebar_page">'.$_LANG['LAST'].'</a> ';
        }
    }

    //if one page of results
    else{
        $href = $link;
        if (is_array($params)){
            foreach($params as $param=>$value){
                $href = str_replace('%'.$param.'%', $value, $href);
            }
        }
        $href = str_replace('%page%', 1, $href);
        $html .= ' <a href="'.$href.'" class="pagebar_page">1</a> ';
    }

    $html.='</div>';

    return $html;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public static function getModuleTemplates() {

    $tpl_dir    = is_dir(TEMPLATE_DIR.'modules') ? TEMPLATE_DIR.'modules' : PATH.'/templates/_default_/modules';
    $pdir       = opendir($tpl_dir);

    $templates  = array();

    while ($nextfile = readdir($pdir)){
        if (
                ($nextfile != '.')  &&
                ($nextfile != '..') &&
                !is_dir($tpl_dir.'/'.$nextfile) &&
                ($nextfile!='.svn') &&
                (substr($nextfile, 0, 6)=='module')
           ) {
            $templates[$nextfile] = $nextfile;
        }
    }

    if (!sizeof($templates)){ return false; }

    return $templates;
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
public function siteOffNotify() {
    return '<div style="margin:0px; padding:5px; border-bottom:2px solid gray; border-right:2px solid gray; background:#FFF; position:absolute;">
                <strong style="color:red">Сайт отключен.</strong> 
                Только администраторы видят его содержимое.
                Вы можете <a href="/admin/index.php?view=config">включить сайт</a> в панели управления.
            </div>';
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}

?>
