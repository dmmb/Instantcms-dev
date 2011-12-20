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
	global $db;
	
	$GLOBALS['db'] = mysql_connect($_CFG['db_host'], $_CFG['db_user'], $_CFG['db_pass']) ;
	mysql_select_db($_CFG['db_base'], $GLOBALS['db']) ;
	
	mysql_query("SET NAMES utf8");
?>