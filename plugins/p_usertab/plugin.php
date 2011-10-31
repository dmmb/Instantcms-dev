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

class p_usertab extends cmsPlugin {

// ==================================================================== //

    public function __construct(){
        
        parent::__construct();

        // ���������� � �������

        $this->info['plugin']           = 'p_usertab';
        $this->info['title']            = 'Demo Profile Plugin';
        $this->info['description']      = '������ ������� - ��������� ������� "������" � ������� ���� �������������';
        $this->info['author']           = 'InstantCMS Team';
        $this->info['version']          = '1.0';

        $this->info['tab']              = '������'; //-- ��������� �������� � �������

        // ��������� ��-���������
        $this->config['���������� ������'] = 10;

        // �������, ������� ����� ������������� ��������

        $this->events[]                 = 'USER_PROFILE';

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
     * @param array $user
     * @return html
     */
    public function execute($event, $user){

        parent::execute();

        $inCore     = cmsCore::getInstance();

        $articles   = array();

        $user_id    = $user['id'];
        $limit      = $this->config['���������� ������'];

        $sql        = "SELECT id, title, pubdate, seolink
                       FROM cms_content
                       WHERE user_id = {$user_id}
                       ORDER BY pubdate DESC
                       LIMIT {$limit}";

        $result     = $this->inDB->query($sql);

        $total      = $this->inDB->num_rows($result);

        if ($total){

            $inCore->loadModel('content');
            $model = new cms_model_content();

            while($article = $this->inDB->fetch_assoc($result)){
                $article['url'] = $model->getArticleURL(null, $article['seolink']);
                $articles[] = $article;
            }
            
        }

        ob_start();

        $smarty= $this->inCore->initSmarty('plugins', 'p_usertab.tpl');
        $smarty->assign('total', $total);
        $smarty->assign('articles', $articles);
        $smarty->display('p_usertab.tpl');

        $html = ob_get_clean();

        return $html;

    }

// ==================================================================== //

}

?>
