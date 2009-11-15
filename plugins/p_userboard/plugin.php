<?php

class p_userboard extends cmsPlugin {
// ==================================================================== //
    public function __construct(){
        parent::__construct();
        $this->info['plugin']           = 'p_userboard';
        $this->info['title']            = 'Объявления пользователя';
        $this->info['description']      = 'Добавляет вкладку "Объявления" в профили всех пользователей';
        $this->info['author']           = 'InstantCMS Team. {MaxiSoft}';
        $this->info['version']          = '1.0';
        $this->info['tab']              = 'Объявления'; 
        $this->config['Количество объявлении'] = 10;
        $this->events[]                 = 'USER_PROFILE';
    }
// ==================================================================== //
    public function install(){
        return parent::install();
    }
// ==================================================================== //
    public function upgrade(){
        return parent::upgrade();
    }
// ==================================================================== //
    public function execute($event, $user){
        parent::execute();
        $articles   = array();
        $user_id    = $user['id'];
        $limit      = $this->config['Количество объявлении'];
        $sql        = "SELECT *
                       FROM cms_board_items
                       WHERE user_id = {$user_id} AND published = 1
                       ORDER BY pubdate DESC
                       LIMIT {$limit}";
        $result     = $this->inDB->query($sql);
        $total      = $this->inDB->num_rows($result);
        if ($total){
            while($article = $this->inDB->fetch_assoc($result)){
                $articles[] = $article;
            }
        }
        ob_start();
        $smarty= $this->inCore->initSmarty('plugins', 'p_userboard.tpl');
        $smarty->assign('total', $total);
        $smarty->assign('articles', $articles);
        $smarty->display('p_userboard.tpl');
        $html = ob_get_clean();
        return $html;
    }
}
?>
