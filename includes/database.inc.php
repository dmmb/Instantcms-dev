<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }
	global $db;
	
	$GLOBALS['db'] = mysql_connect($_CFG['db_host'], $_CFG['db_user'], $_CFG['db_pass']) ;
	mysql_select_db($_CFG['db_base'], $GLOBALS['db']) ;
	
	mysql_query("SET NAMES cp1251");
?>