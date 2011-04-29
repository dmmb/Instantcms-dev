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

if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

class cms_model_registration{

	function __construct(){}

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function install(){

        return true;

    }

/* ========================================================================================================= */
/* ========================================================================================================= */

    public function getBadNickname($nickname){
	
		$inCore     = cmsCore::getInstance();
		$cfg 		= $inCore->loadComponentConfig('registration');
		
		$types 		= $cfg['badnickname'] ? $cfg['badnickname'] : "администратор\nадмин\nqwert\nqwerty\n123\nadmin\nвася пупкин";
		$maytypes 	= explode("\n", $types);
		$nickname	= mb_strtolower($nickname);
		$may        = in_array($nickname, $maytypes);		
	
		return $may;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

    public function sendGreetsMessage($user_id, $message_html) {

        cmsUser::sendMessage(USER_MASSMAIL, $user_id, $message_html);
        return true;
        
    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}