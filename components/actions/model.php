<?php
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_actions{

    private $inDB;

    function __construct(){
        $this->inDB = cmsDatabase::getInstance();
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function deleteAction($id){

        cmsActions::removeLogById($id);
        
        return true;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}