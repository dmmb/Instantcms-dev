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
	
	define("VALID_CMS", 1);	
	session_start();

	include('../../includes/config.inc.php');			//configuration
	include('../../includes/database.inc.php');		//database connection
	include('../../core/cms.php');					//CMS engine
	
	if (isset($_REQUEST['addtocart'])){
	
		if(!isset($_SESSION['cart'])) { $_SESSION['cart'] = array(); }
	
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
		unset($_SESSION['cart']);
		header('location:/price/cart.html');
	}


?>