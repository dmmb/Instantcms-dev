<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.8.1   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by InstantCMS Team, 2007-2011                        //
//                                                                                           //
/*********************************************************************************************/
	if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
	global $db;
	
	$GLOBALS['db'] = mysql_connect($_CFG['db_host'], $_CFG['db_user'], $_CFG['db_pass']) ;
	mysql_select_db($_CFG['db_base'], $GLOBALS['db']) ;
	
	mysql_query("SET NAMES cp1251");
?>