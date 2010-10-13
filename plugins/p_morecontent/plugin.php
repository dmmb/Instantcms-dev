<?php
class p_morecontent extends cmsPlugin {
	
// ==================================================================== //

    public function __construct(){
		
        parent::__construct();
		
		// Информация о плагине
		
        $this->info['plugin']           = 'p_morecontent';
        $this->info['title']            = 'Plugin More Content';
        $this->info['description']      = 'Добавляет в конец каждой статьи список похожих статей. ВНИМАНИЕ: при большом количестве тегов статьи, производительность упадет';
        $this->info['author']           = 'Maximov & InstantCMS Team';
        $this->info['version']          = '1.0';
		
		// События, которые будут отлавливаться плагином
		
        $this->events[]                 = 'GET_ARTICLE';
		
		// Настройки по-умолчанию
		
        $this->config['limit']          = 5;
        $this->config['unsort']         = 1;
    }
	
// ==================================================================== //

    /**
     * Процедура установки плагина
     * @return bool
     */
    public function install(){
        return parent::install();
    }
	
// ==================================================================== //

    /**
     * Процедура обновления плагина
     * @return bool
     */
    public function upgrade(){
          return parent::upgrade();
    }
	
// ==================================================================== //

    /**
     * Обработка событий
     * @param string $event
     * @param mixed $item
     * @return html
     */
	 
    public function execute($event, $item){
        parent::execute();
        switch ($event){
            case 'GET_ARTICLE': $item = $this->eventGetArticle($item); break;
        }
        return $item;
    }

// ==================================================================== //

    private function eventGetArticle($item) {
		
        $inDB 		= cmsDatabase::getInstance();
		
		$item_id 	= $item['id']; 
  		$tag_arr 	= explode(', ', cmsTagLine('content', $item_id, false));
		
		// Получаем id назначения таких же тегов, не более пяти на каждый
  		foreach ($tag_arr as $tag) {

    		$sql = "SELECT item_id FROM cms_tags WHERE tag = '$tag' AND item_id<>'$item_id' AND target='content' LIMIT 5";
			$rs = $inDB->query($sql) ;
			if ($inDB->num_rows($rs)) {
				while ($tagitem = $inDB->fetch_assoc($rs)){
					$id_target[]= $tagitem['item_id'];
				}
			}
			
        }
		
        if (count($id_target)){
			
	        $id_target	= array_unique($id_target);
	        $id_target 	= array_slice($id_target, 0, $this->config['limit']);
	        if ($this->config['unsort']) shuffle($id_target);
			
			$morecontent = '';
	        foreach ($id_target as $n) {
	        	$morecontent .= '<p>'.cmsTagItemLink('content', $n)."</p>";
	        }
			
		    $item['content'] .= '<h4>Похожие статьи:</h4>'.$morecontent;
        }
        return $item;
	}
}
?>
