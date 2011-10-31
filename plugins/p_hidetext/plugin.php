<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

class p_hidetext extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // Информация о плагине

        $this->info['plugin']           = 'p_hidetext';
        $this->info['title']            = 'Скрытый текст';
        $this->info['description']      = 'Скрывает содержимое тега [hide] от незарегистрированных';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        // События, которые будут отлавливаться плагином

        $this->events[]                 = 'GET_POSTS';
        $this->events[]                 = 'GET_POST';
        $this->events[]                 = 'GET_LATEST_POSTS';
        $this->events[]                 = 'GET_BEST_POSTS';
        $this->events[]                 = 'GET_COMMENTS';
        $this->events[]                 = 'GET_FORUM_POSTS';
        $this->events[]                 = 'GET_WALL_POSTS';

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
     * @return mixed
     */
    public function execute($event, $item){

        parent::execute();

        switch ($event){
            case 'GET_POST': $item = $this->eventGetPost($item); break;
            case 'GET_POSTS': $item = $this->eventGetPosts($item); break;
            case 'GET_LATEST_POSTS': $item = $this->eventGetPosts($item); break;
            case 'GET_BEST_POSTS': $item = $this->eventGetPosts($item); break;
            case 'GET_COMMENTS': $item = $this->eventGetComments($item); break;
            case 'GET_FORUM_POSTS': $item = $this->eventGetComments($item); break;
            case 'GET_WALL_POSTS': $item = $this->eventGetComments($item); break;
        }

        return $item;

    }

// ==================================================================== //

    private function parseHide($text){

        $inUser = cmsUser::getInstance();

        $pattern        = '/\[hide\](.*?)\[\/hide\]/i';
        $hidden_text    = 'Скрытый текст виден только <a href="/registration">зарегистрированным</a> пользователям';

        if (!$inUser->id){
            $replacement = '<div class="bb_tag_hide">'.$hidden_text.'</div>';
        } else {
            $replacement = '<div class="bb_tag_hide">${1}</div>';
        }

        return preg_replace($pattern, $replacement, $text);

    }

    private function eventGetPost($item) {

        if (!is_array($item)){ return $item; }

        $item['content_html'] = $this->parseHide($item['content_html']);

        return $item;

    }

    private function eventGetPosts($items){

        if (!is_array($items)){ return $items; }

        foreach($items as $i=>$item){
            $items[$i]['content_html'] = $this->parseHide($item['content_html']);
        }
        
        return $items;
    }

    private function eventGetComments($items){

        if (!is_array($items)){ return $items; }

        foreach($items as $i=>$item){
            $items[$i]['content'] = $this->parseHide($item['content']);
        }

        return $items;
    }

// ==================================================================== //

}

?>
