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

class p_demo extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // ���������� � �������

        $this->info['plugin']           = 'p_demo';
        $this->info['title']            = 'Demo Plugin';
        $this->info['description']      = '������ ������� - ��������� ����� � ����� ������ ������ �� �����';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        // ��������� ��-���������

        $this->config['text']           = 'Added By Plugin From Parameter';
        $this->config['color']          = 'blue';
        $this->config['counter']        = 1;

        // �������, ������� ����� ������������� ��������

        $this->events[]                 = 'GET_ARTICLE';

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

        switch ($event){
            case 'GET_ARTICLE': $item = $this->eventGetArticle($item); break;
        }

        return $item;

    }

// ==================================================================== //

    private function eventGetArticle($item) {

        $item['content'] .= '<p style="color:'.$this->config['color'].'"><strong>'.$this->config['text'].' - '.$this->config['counter'].'</strong></p>';

        $this->config['counter'] += 1;

        $this->saveConfig();

        return $item;

    }

// ==================================================================== //

}

?>
