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

class p_ping extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // Информация о плагине

        $this->info['plugin']           = 'p_ping';
        $this->info['title']            = 'Пинг поисковых систем';
        $this->info['description']      = 'Пингует Яндекс и Гугл при добавлении статей и постов в блоги';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        // Настройки по-умолчанию

        $this->config['Yandex HOST']     = 'ping.blogs.yandex.ru';
        $this->config['Yandex PATH']     = '/RPC2';
        $this->config['Google HOST']     = 'blogsearch.google.com';
        $this->config['Google PATH']     = '/ping/RPC2';

        // События, которые будут отлавливаться плагином

        $this->events[]                 = 'ADD_POST_DONE';
        $this->events[]                 = 'ADD_ARTICLE_DONE';

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

        $siteURL  = 'http://'.$_SERVER['HTTP_HOST'].'/';

        switch ($event){

            case 'ADD_POST_DONE': 
                $pageURL = $siteURL . 'blogs/' . $item['seolink'] . '.html';
                $feedURL = $siteURL . 'rss/blogs/all/feed.rss';
                $this->ping($pageURL, $feedURL);
            break;

            case 'ADD_ARTICLE_DONE':
                $pageURL = $siteURL . $item['seolink'] . '.html';
                $feedURL = $siteURL . 'rss/content/all/feed.rss';
                $this->ping($pageURL, $feedURL);
            break;

        }

        return;

    }

// ==================================================================== //

    private function ping($pageURL, $feedURL) {

        $inConf = cmsConfig::getInstance();

        require(PATH.'/plugins/p_ping/IXR_Library.php');

        $siteName = $inConf->sitename;
        $siteURL  = 'http://'.$_SERVER['HTTP_HOST'].'/';

        $result   = array();

        //
        // Яндекс.Блоги
        //
        if ($this->config['Yandex HOST']){

            $pingClient = new IXR_Client($this->config['Yandex HOST'], $this->config['Yandex PATH']);

            // Посылаем запрос
            if ($pingClient->query('weblogUpdates.ping', $siteName, $siteURL, $pageURL)) {
                $result[] = 'Отправлен ping Яндексу';
            }

        }

        //
        // Google
        //
        if($this->config['Google HOST']){

            $pingClient = new IXR_Client($this->config['Google HOST'], $this->config['Google PATH']);

            // Посылаем запрос
            if ($pingClient->query('weblogUpdates.extendedPing', $siteName, $siteURL, $pageURL, $feedURL)) {
                $result[] = 'Отправлен ping Google';
            }

        }

        $_SESSION['ping_result'] = $result;

        return;

    }

// ==================================================================== //

}

?>
