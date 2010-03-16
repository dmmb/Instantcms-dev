<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/

    session_start();
    include('kcaptcha.php');
    $captcha = new KCAPTCHA();
    $_SESSION['captcha_keystring'] = $captcha->getKeyString();

?>