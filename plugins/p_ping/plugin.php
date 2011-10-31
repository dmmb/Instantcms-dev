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

        // ���������� � �������

        $this->info['plugin']           = 'p_ping';
        $this->info['title']            = '���� ��������� ������';
        $this->info['description']      = '������� ������ � ���� ��� ���������� ������ � ������ � �����';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        // ��������� ��-���������

        $this->config['Yandex HOST']     = 'ping.blogs.yandex.ru';
        $this->config['Yandex PATH']     = '/RPC2';
        $this->config['Google HOST']     = 'blogsearch.google.com';
        $this->config['Google PATH']     = '/ping/RPC2';

        // �������, ������� ����� ������������� ��������

        $this->events[]                 = 'ADD_POST_DONE';
        $this->events[]                 = 'ADD_ARTICLE_DONE';

    }

// ==================================================================== //

    /**
     * ��������� ��������� �������
     * @return bool
     */
    public function install(){

        return parent::install();

    }

// ==================================================================== //

    /**
     * ��������� ���������� �������
     * @return bool
     */
    public function upgrade(){

        return parent::upgrade();

    }

// ==================================================================== //

    /**
     * ��������� �������
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
        // ������.�����
        //
        if ($this->config['Yandex HOST']){

            $pingClient = new IXR_Client($this->config['Yandex HOST'], $this->config['Yandex PATH']);

            // �������� ������
            if ($pingClient->query('weblogUpdates.ping', $siteName, $siteURL, $pageURL)) {
                $result[] = '��������� ping �������';
            }

        }

        //
        // Google
        //
        if($this->config['Google HOST']){

            $pingClient = new IXR_Client($this->config['Google HOST'], $this->config['Google PATH']);

            // �������� ������
            if ($pingClient->query('weblogUpdates.extendedPing', $siteName, $siteURL, $pageURL, $feedURL)) {
                $result[] = '��������� ping Google';
            }

        }

        $_SESSION['ping_result'] = $result;

        return;

    }

// ==================================================================== //

}

?>
