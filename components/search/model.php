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

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_search{

	protected static $instance;

	public $query    = ''; // ��������� ������
	public $look     = ''; // ��� ������
	protected $against = ''; // �������������� ������ ��� �������
	public $words    = ''; // �������������� ����� �������
	public $parametrs_array = array(); // ������ ���������� �������

	public $page     = 1; // ������� ��������
	public $from_component = array(); // ������ ������ �����������, � ������� ��������� �����

	public $components = array(); // ������ �����������, � ������� ���� ��������� ������ ��������
	public $config     = array(); // ������������ ����������
	
	private $default_array_result = array('session_id'=>'','pubdate'=>'','title'=>'','description'=>'','link'=>'','place'=>'','placelink'=>''); // ���� ����������� ������

/* ==================================================================================================== */
/* ==================================================================================================== */

	public function __construct(){
		$this->inDB   = cmsDatabase::getInstance();
		$this->inCore = cmsCore::getInstance();
		$this->query  = self::getQuery();
		$this->look   = $this->inCore->request('look', 'str', 'allwords');
		$this->page   = $this->inCore->request('page', 'int', 1);
		$this->from_component = self::getComponentsArrayForSearch();
		$this->config = self::getConfig();
		$this->inCore->loadLib('tags');
		$this->getComponentsWithSupportSearch();
		$this->parametrs_array = $this->gatherAnArrayOfQueryParametrs();
	}

/* ========================================================================== */
/* ========================================================================== */
    /**
     * �������������� � ���������� ������ ������
	 * Singleton
     * @return obj $instance
     */
    public static function initModel() {

        if (self::$instance === null) {
			
			// ��������� ������������ ����������
			$config = self::getConfig();
			// �������������� ����� ����������
			if (self::loadProviderClass($config['search_engine'])){
				self::$instance = new $config['search_engine']();
			} else {
				self::$instance = new self;
			}

        }
        return self::$instance;

    }

/* ========================================================================== */
/* ========================================================================== */
    /**
     * ��������� ����� ����������
     * @return bool
     */
    public static function loadProviderClass($provider) {

		if(!$provider) { return false; }

		$class_file = PATH.'/components/search/providers/'.$provider.'.php';

		if (file_exists($class_file)){
			require_once($class_file);
			return true;
		} else {
			return false;
		}

    }

/* ========================================================================== */
/* ========================================================================== */
    /**
     * ���������� ������������ �� ��������� ��� ����������
     * @return array $cfg
     */
    public static function getDefaultConfig() {

        $cfg = array(
                     'search_engine'=>'',
                     'perpage'=>15,
                     'comp'=>array()
               );

        return $cfg;

    }

/* ========================================================================== */
/* ========================================================================== */
    /**
     * ���������� ������������ ����������
     * @return array $cfg
     */
    public static function getConfig() {

        $inCore = cmsCore::getInstance();

        $default_cfg = self::getDefaultConfig();
        $cfg         = $inCore->loadComponentConfig('search');
        $cfg         = array_merge($default_cfg, $cfg);

        return $cfg;

    }

/* ========================================================================== */
/* ========================================================================== */
    /**
     * �������� ������ ������� �� �����
	 * ������� �������� �� ���
     * @return str $query
     */
    private static function getQuery() {

		$inCore = cmsCore::getInstance();
		// �������� ���� ������ ������� �� GET
		$query  = $inCore->request('query', 'str', '');
		// �������� �� � ������ �������
		$query  = strtolower($query);
		// ���������� ������ � URL
		$query  = urldecode($query);
		// ��������� ������ �����, �����, - _
		$query  = preg_replace('/[^\w\s\-]/i', '', $query);
		// ������� ������� �� �����
		$query  = trim($query);
		// ������� ������� �������
		$query  = preg_replace('/\s+/', ' ', $query);
		// ������������ ������ ������� 64 ���������
		$query  = substr($query, 0, 64);

		return $query;

    }
/* ========================================================================== */
/* ========================================================================== */
    /**
     * ��������� ������ ���������� �������
     * @return array $parametrs
     */
    private function gatherAnArrayOfQueryParametrs() {

		$parametrs = array(
					 'query'=>$this->query,
					 'look'=>$this->look,
					 'from_component'=>$this->from_component
		);

		return $parametrs;

    }
/* ========================================================================== */
/* ========================================================================== */
    /**
     * �������� ������ ����������� ��� ������
     * @return array
     */
    protected static function getComponentsArrayForSearch() {

		$inCore = cmsCore::getInstance();
		// �������� ���� ������ ������� �� GET
		$from_component = $inCore->request('from_component', 'array_str');
		if(!$from_component || !is_array($from_component)) { return array(); }
		// ������� �������������� ��������
		$from_component = array_unique($from_component);
		// ������� ������ �������� �������
		// ����� ������� ������ ��������� ������ �����
		if($from_component){
			foreach ($from_component as $key=>$val){
			   if(!$from_component[$key]){
				  unset($from_component[$key]);
			   } else {
				   $from_component[$key] = preg_replace('/[^a-z_]/i', '', $from_component[$key]);
			   }
			}
		}

		return $from_component;

    }
/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * �������� ������ ����������� ������ (�����������)
	 * �������� ��������� ������� ����
     * @return array $rs
     */
    public function getResults() {

        $sql = "SELECT pubdate, title, description, link, place, placelink
                FROM cms_search
                WHERE session_id = '".session_id()."' ORDER BY id ASC LIMIT ".(($this->page-1)*$this->config['perpage']).", {$this->config['perpage']}";

		$result = $this->inDB->query($sql);

		if(!$this->inDB->num_rows($result)){ return false; }

        $rs = array();

		while ($res = $this->inDB->fetch_assoc($result)){

			$res['pubdate'] = cmsCore::dateFormat($res['pubdate'], true, false, false);
			// ��������� � ������ ��������� ���� ������
			$res['s_title'] = $this->getHighlightedText($res['title']);
			// �������� � ������ ��������� ���� ������
			$res['description'] = $this->getHighlightedText($res['description']);

			$rs[] = $res;

		}

        return $rs;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * �������� ����� ���������� ����������� ������
     * @return int
     */
    public function getCountResults() {

		return $this->inDB->rows_count('cms_search', "session_id = '".session_id()."'");
        
    }
// ============================================================================ //
// ============================================================================ //
    protected function checkArrayResults($input_array = array()) {

		// ������� �������� ������ �������
		foreach($input_array as $k=>$v){
		   if (!isset($this->default_array_result[$k])) { unset($input_array[$k]); }
		}

		if(!$input_array || !is_array($input_array)) { return array(); }

		return $input_array;

    }
/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ��������� ��������� ������ � �������
     * @return bool
     */
    public function addResult($item = array()) {

		// ��������� ������������ ����� ��� ����������
		$item = $this->checkArrayResults($item);
		if(!$item) { return false; }
		// ���� ����� ������ ��� ����, ������������
		if ($this->inDB->rows_count('cms_search', "session_id='{$item['session_id']}' AND link='{$item['link']}'")){
			 return false;
		}

		foreach($item as $field=>$value){
			$set .= "{$field} = '{$this->inDB->escape_string($value)}',";
		}

		$set = rtrim($set, ',');

		$this->inDB->query("INSERT INTO cms_search SET {$set}");

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * �������� ����� ���������� ����������� ������ �� ����
     * @return int
     */
    public function getCountTags() {

		return $this->inDB->rows_count('cms_tags', "tag = '{$this->query}'");
        
    }
/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * �������� ������ ����������� ������ (�����������) ��� �����
     * @return array $rs
     */
    public function searchByTag() {

        $sql = "SELECT *
                FROM cms_tags
                WHERE tag = '{$this->query}' ORDER BY tag DESC LIMIT ".(($this->page-1)*$this->config['perpage']).", {$this->config['perpage']}";

		$result = $this->inDB->query($sql);

		if(!$this->inDB->num_rows($result)){ return false; }

        $rs = array();

		$row = 1;
		while ($res = $this->inDB->fetch_assoc($result)){
			if ($itemlink = cmsTagItemLink($res['target'], $res['item_id'])){
				$res['itemlink'] = $itemlink;
				$res['tag_bar']  = cmsTagBar($res['target'], $res['item_id'], $this->query);
				if($row % 2 == 0) { $res['class'] = 'search_row2'; } else { $$res['class'] = 'search_row1'; }
				$rs[] = $res;
				$row++;
			}
		}

        return $rs;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ���������, ���������� �� ��������� �������
     * @return bool
     */
    public function isChangedParams() {

		return $this->parametrs_array == cmsUser::sessionGet('query_params');
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ������������ ������� ����� � ������
     * @return str $text
     */
    protected function getHighlightedText($text) {

		if (!cmsUser::sessionGet('searchquery')){ return $text; }

		if ($this->look == 'phrase'){
			$text = str_ireplace(cmsUser::sessionGet('searchquery'), '<strong class="search_match">'.cmsUser::sessionGet('searchquery').'</strong>', $text);
		} elseif($this->look == 'allwords') {
			$text = str_ireplace(cmsUser::sessionGet('searchquery'), '<strong class="search_match">'.cmsUser::sessionGet('searchquery').'</strong>', $text);
			$words = explode(' ', cmsUser::sessionGet('searchquery'));
			foreach($words as $w){
				$text = str_ireplace($w, '<strong class="search_match">'.$w.'</strong>', $text);
			}
		} else {
			$words = explode(' ', cmsUser::sessionGet('searchquery'));
			foreach($words as $w){
				$text = str_ireplace($w, '<strong class="search_match">'.$w.'</strong>', $text);
			}
		}

		return $text;
        
    }
/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ������� � ������ ����������� � ������� ������
	 * � ���������� ������ ���������
     * @return str $proposal
     */
    public function getProposalWithSearchWord($text) {

		if(!$text) { return ''; }

		// ������� �������� � ������
		$text = strip_tags($text);
		$text = preg_replace('/\s+/', ' ', $text);

		// ��������� ������ ����������� �� ������
		$text_array = explode('.', $text);
		// ��������� ������ ���� �������
		$words_array = explode(' ', $this->words);

		$result_array = array();
		foreach($words_array as $w){
			$result_array = preg_grep('/'.$w.'/i', $text_array);
			if($result_array) { break; }
		}

		$proposal = implode('. ', $result_array);

		return $proposal;
        
    }

/* ========================================================================================================= */
/* ========================================================================================================= */
    /**
     * ��������� ������
     * @return bool
     */
    public function prepareSearch() {

		// ��������� ������
		$this->getAgainst();

		// �������� ������ ����������� � ������ �����������
		$enable_components = $this->getEnableComponentsWithSupportSearch();

		// ���� ���-�� ����������� � ������� ����� ������ �� �������
		// ��������� � ����������� �����������, ����������� � ������
		// �������, ��� ������� $this->from_component �� ���� � ����
		// �� ���� ����������� �����������
		if(count($this->from_component) == count($enable_components)){ $this->from_component = array(); }

		// ���� ������ ������ �� ����� � ������������ �����������
		if($this->from_component){

			foreach($this->from_component as $component){
				if (in_array($component, $this->config['comp'])){
					$reply = $this->callComponentSearch($component);
				}
			}

		} else { // ��������� ����� �� ��� ���������� �����������

			foreach($enable_components as $component){
				$reply = $this->callComponentSearch($component['link']);
			}

		}

        return $reply;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * �������� ������� ������ ��� ����������
     * @return bool
     */
    protected function callComponentSearch($component) {

		if (file_exists(PATH.'/components/'.$component.'/psearch.php')){
			include_once PATH.'/components/'.$component.'/psearch.php';
			$search_func = 'search_' . $component;
			if(function_exists($search_func)){
				call_user_func($search_func, $this->against, $this->look, $this->words);
				return true;
			}
		}

        return false;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ������� ���������� ������ ��� ������� ������
     * @return bool
     */
    public function deleteResultsFromThisSession() {

        $this->inDB->query("DELETE FROM cms_search WHERE session_id = '".session_id()."'");

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ������� ������ � ���� ������ ������ 1 ���
     * @return bool
     */
    public function deleteOldResults() {

        $this->inDB->query("DELETE FROM cms_search WHERE DATEDIFF(NOW(), date) > 1");

        return true;
        
    }
/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ��������� ������� ������� cms_search
     * @return bool
     */
    public function truncateResults() {

        $this->inDB->query("TRUNCATE TABLE `cms_search`");

        return true;
        
    }
/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * �������������� ������ ������ ��� sql �������
	 * ��������� $this->against � $this->words
     * @return bool
     */
    protected function getAgainst() {

		// ���� ��� ��������, ������������
		if($this->against && $this->words) { return true; }

		// ���������� ������� �������
		$this->inCore->includeFile('includes/stemmer/stemmer.php');
		$stemmer = new Lingua_Stem_Ru();
		// ��������� ������ ����
		$words = explode(' ', $this->query);
		// ����� �����
		if ($this->look == 'anyword'){
			foreach($words as $w){
				$w = trim($w); 
				if(strlen($w)>3){
					if(strlen($w)==4){
						$this->against .= $w.'* ';
						$this->words   .= $w.' ';
					} else {
						$this->against .= $stemmer->stem_word($w).'* ';
						$this->words   .= $stemmer->stem_word($w).' ';
						
					}
				}
			}
		}
		// ��� �����
		if ($this->look == 'allwords'){
			$this->against  = '>\"'.$this->query.'\" ';
			$this->against .= '<(';
			foreach($words as $w){
				$w = trim($w); 
				if(strlen($w)>3){
					if(strlen($w)==4){
						$this->against .= '+'.$w.'* ';
						$this->words   .= $w.' ';
					} else {
						$this->against .= '+'.$stemmer->stem_word($w).'* ';
						$this->words   .= $stemmer->stem_word($w).' ';
						
					}
				}
			}
			$this->against .= ')';
		}
		// ����� �������
		if ($this->look == 'phrase'){
			$this->against = '\"'.$this->query.'\"';
			$this->words   = $this->query;
		}

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ��������� ������ �����������, � ������� ���� ��������� ������ ��������
	 * ��������� $this->components
     * @return bool
     */
    private function getComponentsWithSupportSearch() {

		// ���� ��� ��������, ������������
		if($this->components && is_array($this->components)) { return true; }

		// �������� ������ �����������
		// � ������ ���������� ���������� ���� ���� psearch.php
		$rs = $this->inDB->query('SELECT link, title FROM cms_components WHERE internal = 0 AND published = 1') ;
		if (!$this->inDB->num_rows($rs)){ return false; }

		while ($component = $this->inDB->fetch_assoc($rs)){
			if (file_exists(PATH.'/components/'.$component['link'].'/psearch.php')){
				$component['title'] = str_replace('InstantShop', '������� �������', $component['title']);
				$component['title'] = str_replace('InstantMaps', '������� �������� �� �����', $component['title']);
				$component['title'] = str_replace('InstantVideo', '����� �������', $component['title']);
				$this->components[] = $component;
			}
		}

        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */
    /**
     * ���������� ������ �����������, ���������� ��� ������� � ������
     * @return array $enable_components
     */
    public function getEnableComponentsWithSupportSearch() {

		$enable_components = array();

		foreach($this->components as $component){
			if (in_array($component['link'], $this->config['comp'])){
				$enable_components[] = $component;
			}
		}

        return $enable_components;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}