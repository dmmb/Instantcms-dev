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

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_actions{

    private $inDB;
	public  $config = array();

/* ========================================================================== */
/* ========================================================================== */

    function __construct(){
        $this->inDB = cmsDatabase::getInstance();
		$this->config = self::getConfig();
    }

/* ========================================================================== */
/* ========================================================================== */

    public static function getDefaultConfig() {

        $cfg = array(
                     'show_target'=>1,
                     'perpage'=>10,
					 'perpage_tab'=>15,
                     'action_types'=>''
               );

        return $cfg;

    }

/* ========================================================================== */
/* ========================================================================== */

    public static function getConfig() {

        $inCore = cmsCore::getInstance();

        $default_cfg = self::getDefaultConfig();
        $cfg         = $inCore->loadComponentConfig('actions');
        $cfg         = array_merge($default_cfg, $cfg);

        return $cfg;

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