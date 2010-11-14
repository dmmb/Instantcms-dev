<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/
	
	define("VALID_CMS", 1);	
	session_start();

	include('../../includes/config.inc.php');			//configuration
	include('../../includes/database.inc.php');		//database connection
	include('../../core/cms.php');					//CMS engine
	
	if (isset($_REQUEST['addtocart'])){
	
		if(!isset($_SESSION['cart'])) { session_register('cart'); $_SESSION['cart'] = array(); }
	
		foreach($_REQUEST as $key=>$value){
			if (strpos($key, 'item')===0){
				if(isset($_SESSION['cart'][$value])){
					$_SESSION['cart'][$value] += $_REQUEST['kolvo'][$value];
				} else {
					$_SESSION['cart'][$value] = $_REQUEST['kolvo'][$value];
				}
			}
		}
		header('location:/price/cart.html');
	}

	if (isset($_REQUEST['clearcart'])){
		$_SESSION['cart'] = '';
		session_unregister('cart');
		header('location:/price/cart.html');
	}


?>