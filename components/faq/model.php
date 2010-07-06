<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_faq{

	function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function getCommentTarget($target, $target_id) {

        $result = array();

        switch($target){

            case 'faq': $item               = $this->inDB->get_fields('cms_faq_quests', "id={$target_id}", 'quest');
                        if (!$item) { return false; }
                        $result['link']     = '/faq/quest'.$target_id.'.html';
                        $result['title']    = (strlen($item['quest'])<100 ? $item['quest'] : substr($item['quest'], 0, 100).'...');
                        break;

        }

        return ($result ? $result : false);

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}