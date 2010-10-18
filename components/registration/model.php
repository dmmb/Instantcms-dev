<?php
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
		
		$types 		= $cfg['badnickname'] ? $cfg['badnickname'] : "�������������\n�����\nqwert\nqwerty\n123\nadmin\n���� ������";
		$maytypes 	= explode("\n", $types);
		$nickname	= mb_strtolower($nickname);
		$may        = in_array($nickname, $maytypes);		
	
		return $may;

    }

/* ==================================================================================================== */
/* ==================================================================================================== */

}