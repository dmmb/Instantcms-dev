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

function searchForm($query=''){
    $inCore = cmsCore::getInstance();
    global $_LANG;
	//SEARCH FORM
	echo '<div class="price_search_form">
		  <form action="/index.php" method="GET" style="clear:both">';
		echo '<input type="hidden" name="view" value="search"/>';
		echo '<label>';							 
			echo '<b>'.$_LANG['SEARCH_PRODUCT'].':</b> ';
			echo '<input type="text" name="query" value="'.$query.'" class="price_search">';
			echo '<select name="look" style="margin-left:2px">
					<option value="anyword">'.$_LANG['ANY_WORD'].'</option>
					<option value="allwords">'.$_LANG['ALL_WORD'].'</option>
					<option value="phrase">'.$_LANG['PHRASE'].'</option>
				  </select>';
		echo '</label>';				
	echo '</form></div>';
}

function pageBar($cat_id, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
	$html = '';
	
	$result = $inDB->query("SELECT id FROM cms_price_items WHERE category_id = $cat_id") ;
	$records = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>'.$_LANG['PAGES'].': </strong></span>';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					
					$link = '/price/'.(int)$_REQUEST['id'].'-'.$p;
					
					$html .= ' <a href="'.$link.'" class="pagebar_page">'.$p.'</a> ';		
				} else {
					$html .= '<span class="pagebar_current">'.$p.'</span>';
				}
			}
			$html .= '</div>';
		}
	}
	return $html;
}

function price(){

    $inCore = cmsCore::getInstance();
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
    global $_LANG;
	$cfg    = $inCore->loadComponentConfig('price');
	// Проверяем включени ли компонент
	if(!$cfg['component_enabled']) { cmsCore::error404(); }
	
	$id     =   $inCore->request('id', 'int', 0);
	$do     =   $inCore->request('do', 'str', 'view');
	
	if ($inCore->inRequest('query')){ 
		//PREPARE QUERY
		$query = $inCore->request('query', 'str'); 
		$look  = $inCore->request('look', 'str');
		//SPLIT WORDS
		$words = preg_split('/ /', $query);
		$count = sizeof($words);
		$n=0; //START SEARCH FROM FIRST WORD		
	} else { $query = ''; }
	
	if ($do == 'removeitem'){
	
		unset($_SESSION['cart'][$id]);
		header('location:/price/cart.html');
	
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='finish'){
		$inPage->setTitle($_LANG['ORDER_FINISH']);
		$inPage->backButton(false);
		
		$error = '';
		$customer = array();

		if(!empty($_REQUEST['customer_fio'])) { $customer['fio'] = $inCore->request('customer_fio', 'str'); } else { $error .= $_LANG['SET_YOUR_NAME'].'<br/>'; }
		if(!empty($_REQUEST['customer_phone'])) { $customer['phone'] = $inCore->request('customer_phone', 'str'); } else { $error .= $_LANG['SET_YOUR_PHONE'].'<br/>'; }
		$customer['company'] = $inCore->request('customer_company', 'str', '');
		$customer['comment'] = $inCore->request('customer_comment', 'str', '');
		
		if(!$inCore->checkCaptchaCode($inCore->request('code', 'str'))) { $error .= $_LANG['ERR_CAPTCHA'].'<br/>'; }
				
		if($error==''){
		
			$mail_message = '';
			
			$mail_message .= $_LANG['ORDER_FROM_PRICE'].".\n\n";
			$mail_message .= $_LANG['BUYER']."\n-----------------------------\n";
			
			$mail_message .= $_LANG['FIO'].": " . $customer['fio'] . "\n";
			$mail_message .= $_LANG['COMPANY'].": " . @$customer['company'] . "\n";
			$mail_message .= $_LANG['PHONE'].": " . $customer['phone'] . "\n";
			$mail_message .= $_LANG['ORDER_COMMENT'].": " . @$customer['comment'] . "\n\n";

			$mail_message .= $_LANG['ORDER']."\n---------------------------------\n";
		
			//GET ITEMS DATA FROM DATABASE			
			$sql = "SELECT * FROM cms_price_items WHERE ";	
			$match = ""; $n=0;				
			foreach($_SESSION['cart'] as $key=>$val){ 
				if ($val) { 
					if($n==0) { $match .= "id = $key"; $n++; } 
					else { $match .= " OR id = $key"; } 
				}
			}				
			$sql .= $match;
			$result = $inDB->query($sql) ;
	
			$items_count = $inDB->num_rows($result);
						
			//CONSTRUCT MAIL MESSAGE
			if ($items_count){	
				$num = 0; $total_summ = 0;
				while($con = $inDB->fetch_assoc($result)){
					$num++;			
					$item_totalcost = $con['price'] * $_SESSION['cart'][$con['id']];
					$total_summ += $item_totalcost;				
					$mail_message .= $num . '. ' . $con['title'] . ' (' . $_SESSION['cart'][$con['id']] . '  x ' . $con['price'] . ' '.$_LANG['RUB'].') = ' . $item_totalcost . ' '.$_LANG['RUB'].'.' . "\n";
				}
			}
				
			$mail_message .= "\n" . $_LANG['TOTAL_SUM_ORDER'].': '.$total_summ.' '.$_LANG['RUB'].'.' . "\n";
			$inCore->mailText($cfg['email'], $_LANG['MAIL_SUBJECT'], $mail_message);
			//////////////////////////////////////////////////////////////////////////
			
			unset($_SESSION['cart']);		
			
			echo '<div class="con_heading">'.$_LANG['THANK'].'!</div>';
			echo '<p style="clear:both"><b>'.$_LANG['CUSTOMER_EMAIL_SUBJECT'].'.</b><br/>'.$_LANG['CUSTOMER_EMAIL_TEXT'].'</p>';
			echo '<p><a href="/">'.$_LANG['CONTINUE'].'</a></p>';
		
		} else {
			echo '<div class="con_heading">'.$_LANG['ERROR'].'!</div>';
			echo '<p style="clear:both; color:red">'.$error.'</p>';		
			echo '<p><a href="#" onClick="window.history.back()">'.$_LANG['BACK'].'</a></p>';
		}
		
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='order'){
		$inPage->setTitle($_LANG['ORDERING']);
		echo '<div class="con_heading">'.$_LANG['ORDERING'].'</div>';
		
		//PATHWAY ENTRY
		$inPage->addPathway($_LANG['CART'], '/price/cart.html');
		$inPage->addPathway($_LANG['ORDERING'], $_SERVER['REQUEST_URI']);
		
		//GET ITEMS DATA FROM DATABASE			
		$sql = "SELECT * FROM cms_price_items WHERE ";	
		$match = ""; $n=0;				
		foreach($_REQUEST['kolvo'] as $key=>$val){ 
			if ($val) { 
				$key = (int)$key;
				if($n==0) { $match .= "id = $key"; $n++; } 
				else { $match .= " OR id = $key"; } 
				$_SESSION['cart'][$key] = $val;
			}
		}				
		$sql .= $match;
		$result = $inDB->query($sql) ;

		//PRINT ITEMS LIST	
		echo '<div style="width:100%; clear:both;margin-top:15px;margin-bottom:15px">'.$cfg['delivery'].'</div>';
		
		$items_count = $inDB->num_rows($result);
					
		if ($items_count){	
			$num = 0; $total_summ = 0;
			echo ' <table style="border-top: solid 1px black; border-bottom: solid 1px black" class="contentlist" cellpadding="6" cellspacing="0" border="0" width="100%">';
			while($con = $inDB->fetch_assoc($result)){
				$num++;
				if (!($num%2)) { $class="pricerow2"; } else { $class="pricerow1"; }
				
				$item_totalcost = $con['price'] * $_REQUEST['kolvo'][$con['id']];
				$total_summ += $item_totalcost;
				
				echo '<tr>';
					echo '<td class="'.$class.'" width="16" valign="top"><img src="/images/markers/priceitem.png" border="0" /></td>';
					echo '<td class="'.$class.'" width="" valign="top">';															
					echo $con['title'];
					echo '</td>';	
					echo '<td class="'.$class.'" width="160" align="right" valign="top">'.$con['price'].' x '.$_REQUEST['kolvo'][$con['id']].' '.$_LANG['QTY'].'. = <b>'.$item_totalcost.' '.$_LANG['RUB'].'.</b></td>';
				echo '</tr>';
			}
			echo '</table>';	
			
			echo '<p><b>'.$_LANG['TOTAL_SUM_ORDER'].':</b> '.$total_summ.' '.$_LANG['RUB'].'.</p>';
			//DELIVERY INFO FORM
			echo '<div class="con_heading">'.$_LANG['INFO_CUSTOMER'].'</div>';
			
			echo '<form action="/price/finish.html" method="POST">';
			echo '<table width="100%" cellspacing="0" cellpadding="5">';
			echo '<tr>';		
				echo '<td width="30%" align="right">'.$_LANG['FIO_CUSTOMER'].': </td>';
				echo '<td width="70%" align="left"><input name="customer_fio" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="30%" align="right">'.$_LANG['ORGANIZATION'].': </td>';
				echo '<td width="70%" align="left"><input name="customer_company" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="30%" align="right">'.$_LANG['CONTACT_PHONE'].': </td>';
				echo '<td width="70%" align="left"><input name="customer_phone" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="30%" align="right">'.$_LANG['CUSTOMER_COMMENT'].': </td>';
				echo '<td width="70%" align="left"><input name="customer_comment" type="text" size="45" /></td>';			
			echo '</tr>';


			echo '<tr>';		
                echo '<td width="30%" align="right">&nbsp;</td>';
                echo '<td width="70%" align="left">'.cmsPage::getCaptcha().'</td>';
			echo '</tr>';

			echo '<tr>';		
				echo '<td width="30%" align="right">&nbsp;</td>';
				echo '<td width="70%" align="left"><input name="order" type="submit" value="'.$_LANG['SUBMIT_ORDER'].'" /></td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';
					
		}				
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($do=='cart'){
		echo '<div class="con_heading">'.$_LANG['CART'].'</div>';
		
		$inPage->setTitle($_LANG['CART']);
		$inPage->backButton(false);
		
		//PATHWAY ENTRY
		$inPage->addPathway($_LANG['CART'], $_SERVER['REQUEST_URI']);
				
		if(@sizeof($_SESSION['cart'])){
			//GET ITEMS DATA FROM DATABASE			
			$sql = "SELECT * FROM cms_price_items WHERE ";	
			$match = ""; $n=0;				
			foreach($_SESSION['cart'] as $key=>$val){ 
				if($n==0) { $match .= "id = $key"; $n++; } else { $match .= " OR id = $key"; }
			}				
			$sql .= $match;
			$result = $inDB->query($sql) ;
			//PRINT ITEMS LIST
			$items_count = $inDB->num_rows($result);
						
			if ($items_count){	
				echo '<a style="clear:both" href="/components/price/cart.php?clearcart">'.$_LANG['CLEAR_CART'].'</a>';
				$num = 0; $total_summ = 0;
				echo '<form name="orderform" action="/price/order.html" method="post"><table class="contentlist" cellpadding="6" cellspacing="0" border="0" width="100%">';
				while($con = $inDB->fetch_assoc($result)){
					$num++;
					if (!($num%2)) { $class="pricerow2"; } else { $class = "pricerow1"; }
					
					$item_totalcost = $con['price'] * $_SESSION['cart'][$con['id']];
					$total_summ += $item_totalcost;

					if ($con['canmany']) { $count_field = '<input type="text" style="text-align: center; font-size:9px; border:solid 1px black" size="5" name="kolvo['.$con['id'].']" value="'.$_SESSION['cart'][$con['id']].'"/>'; }
					else { $count_field = '<input type="hidden" name="kolvo['.$con['id'].']" value="1"/>1'; }
					
					echo '<tr>';
						echo '<td class="'.$class.'" width="16" valign="top"><a href="/price/cart/removeitem'.$con['id'].'.html" title="'.$_LANG['DEL_POSITION'].'"><img src="/images/icons/delete.gif" border="0"/></a></td>';
						echo '<td class="'.$class.'" width="16" valign="top"><img src="/images/markers/priceitem.png" border="0" /></td>';
						echo '<td class="'.$class.'" width="" valign="top">';															
						echo $con['title'];
						echo '</td>';	
						echo '<td class="'.$class.'" width="160" align="right" valign="top">'.$con['price'].' x '.$count_field.' '.$_LANG['QTY'].'. = </td>';
						echo '<td class="'.$class.'" width="80" align="right" valign="top"><b>'.$item_totalcost.' '.$_LANG['RUB'].'.</b></td>';
					echo '</tr>';
				}
				echo '</table></form>';
	
				echo '<table style="margin-top:15px" cellpadding="6" cellspacing="0" border="0" width="100%">';
				echo '<tr>';
					echo '<td width="" style="border-top:solid 1px black" align="left">
							<a href="/price">&laquo; '.$_LANG['BACK_TO_PRICE'].'</a> | <a href="#" onClick="document.orderform.submit();">'.$_LANG['GOTO_ORDERING'].' &raquo;</a>
						  </td>';
					echo '<td width="160" style="border-top:solid 1px black" align="right"><b>'.$_LANG['TOTAL'].':</b> '.$total_summ.' '.$_LANG['RUB'].'.</td>';
				echo '</tr>';
				echo '</table>';
				
			}	
		} else { 
					echo '<p style="clear:both"><b>'.$_LANG['NOITEMS_IN_CART'].'</b></p>';
					echo '<p>'.$_LANG['NOITEMS_IN_CART_TEXT'].'</p>';
					echo '<p><a href="/price">&laquo; '.$_LANG['BACK_TO_PRICE'].'</a></p>';
		}			
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='view'){ 

		if ($id==0){ //SHOW CATEGORIES LIST
		$pagetitle = $inCore->menuTitle();
		if ($pagetitle) { $inPage->setTitle($pagetitle); } 
		else { $inPage->setTitle($_LANG['PRICE']); $pagetitle = $_LANG['PRICE']; }
		
		echo '<div class="con_heading">'.$pagetitle.'</div>';
		
		$sql = "SELECT c.*, COUNT(i.id) as content_count
				FROM cms_price_cats c, cms_price_items i
				WHERE c.published = 1 AND i.category_id = c.id
				GROUP BY i.category_id
				ORDER BY c.title DESC
				";
						
		$result = $inDB->query($sql) ;
		
			if ($inDB->num_rows($result)){	
			
				searchForm();
			
				//PRINT CATEGORY LIST
				echo '<table class="categorylist" cellspacing="10" width="100%">';
				while($cat = $inDB->fetch_assoc($result)){
					echo '<tr>';
						echo '<td width="16" valign="top"><img src="/images/markers/pricelist.png" border="0" /></td>';
						echo '<td width="500">';
							echo '<a href="/price/'.$cat['id'].'">'.$cat['title'].'</a> ('.$cat['content_count'].')';
						echo '</td>';				
					echo '</tr>';
				}
				echo '</table>';
			}
		}
		else { //SHOW CATEGORY ITEMS
						
			$sql = "SELECT * FROM cms_price_cats WHERE id = $id LIMIT 1";				
			$result = $inDB->query($sql) ;
			
			if ($inDB->num_rows($result)){	
				$cat = $inDB->fetch_assoc($result);

				$inPage->setTitle($cat['title']);
				$inPage->addHeadJS('components/price/common.js');

				echo '<div class="con_heading">'.$cat['title'].'</div>';	
				if ($cat['description']) { echo '<div class="con_description">'.$cat['description'].'</div>'; }
			}
			
			//PATHWAY ENTRY
			$inPage->addPathway($cat['title'], $_SERVER['REQUEST_URI']);
						
			searchForm();
			
			$perpage = 20;
			if (isset($_REQUEST['page'])) { $page = abs((int)$_REQUEST['page']); } else { $page = 1; }
			
			$sql = "SELECT *
					FROM cms_price_items
					WHERE category_id = $id AND published = 1					
					GROUP BY id
					ORDER BY price ASC
					LIMIT ".(($page-1)*$perpage).", $perpage";		
			
			$result = $inDB->query($sql) ;
			$items_count = $inDB->num_rows($result);
			
			if ($items_count){	
				$num = 0;
				echo '<form name="listform" method="POST" action="/components/price/cart.php"><table class="contentlist" cellpadding="2" cellspacing="0" border="0" width="100%">';
				while($con = $inDB->fetch_assoc($result)){
					$num++; 
					if (!($num%2)) { $class="pricerow2"; } else { $class = "pricerow1"; }
					
					if($con['canmany']) { $count_field = '<input onChange="checkSumm('.$items_count.')" type="text" style="text-align: center; font-size:9px; border:solid 1px black" size="5"  name="kolvo['.$con['id'].']" value="1"/>'; }
					else { $count_field = '<input type="hidden" name="kolvo['.$con['id'].']" value="1"/>1'; };
					
					echo '<tr>';
						echo '<td class="'.$class.'" width="20" valign="top"><input onClick="checkSumm('.$items_count.')" name="item'.$num.'" type="checkbox" value="'.$con['id'].'" />
							  <input type="hidden" name="price'.$num.'" value="'.$con['price'].'" /></td>';
						echo '<td class="'.$class.'" width="16" valign="top"><img src="/images/markers/priceitem.png" border="0" /></td>';
						echo '<td class="'.$class.'" width="" valign="top">';															
						echo $con['title'];
						echo '</td>';	
						echo '<td class="'.$class.'" width="120" align="right" valign="top"><div id="kdiv'.$num.'" name="kdiv" style="display:none">'.$count_field.' шт. x</div></td>';			
						echo '<td class="'.$class.'" width="100" align="right" valign="top">'.$con['price'].' руб.</td>';			
					echo '</tr>';
				}
				echo '</table>';
				echo pageBar($id, $page, $perpage);
				echo '<div align="right" style="margin-top:10px"><label><b>'.$_LANG['SUM_SELECTED_PRODUCTS'].':</b> <input style="border:solid 1px black" type="text" name="summField" value="0" size="6"/> '.$_LANG['RUB'].'. <input type="submit" name="addtocart" value="'.$_LANG['ADD_TO_CART'].'"/></label></div>';
				
				echo '</form>';
			} else { 
						echo '<p>'.$_LANG['NOITEMS_IN_CART_TO_SHOW'].'</p>';
					}
		} //ALBUM CONTENT
		
		
	} // DO = VIEW CATEGORIES
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='search'){

			$inPage->setTitle($_LANG['SEARCH_PRODUCT']);
			$inPage->addHeadJS('components/price/common.js');

			echo '<div class="con_heading">'.$_LANG['SEARCH_PRODUCT'].'</div>';
					
			$sql = "SELECT *
					FROM cms_price_items i
					WHERE published = 1 AND ";
					
			//Search conditions//////////////////////////////////////////
			if($look == 'anyword'){
				//$looktype = 'любое слово';
				foreach($words as $w){
					if(strlen($w)>1){
						$n++;
						if ($n==1) { $sql .= "title LIKE '%$w%'"; }
						else { $sql .= " OR title LIKE '%$w%'"; }
					}
				}		
			}
		
			if($look == 'allwords'){
				//looktype = 'все слова';		
				foreach($words as $w){
					if(strlen($w)>1){
						$n++;
						if ($n==1) { $sql .= "title LIKE '%$w%'"; }
						else { $sql .= " AND title LIKE '%$w%'"; }
					}
				}		
			}
			
			if($look == 'phrase'){
				//$looktype = 'фраза целиком';		
				$sql .= "title LIKE '%$query%'";			
			}
			/////////////////////////////////////////////////////////////				
							
			$result = $inDB->query($sql) ;
			$items_count = $inDB->num_rows($result);
			
			searchForm($query);
			
			if ($items_count){	
				$num = 0;
				echo '<p style="clear:both"><b>'.$_LANG['SEARCHED_ITEMS'].':</b> '.$items_count.'</p>';
				echo '<form name="listform" method="POST" action="/components/price/cart.php"><table class="contentlist" cellpadding="6" cellspacing="0" border="0" width="100%">';
				while($con = $inDB->fetch_assoc($result)){
					$num++; 
					if (!($num%2)) { $bgcolor="#FFFFFF"; } else { $bgcolor = "#EBEBEB"; }

					if($con['canmany']) { $count_field = '<input onChange="checkSumm('.$items_count.')" type="text" style="text-align: center; font-size:9px; border:solid 1px black" size="5"  name="kolvo['.$con['id'].']" value="1"/>'; }
					else { $count_field = '<input type="hidden" name="kolvo['.$con['id'].']" value="1"/>1'; };

					echo '<tr>';
						echo '<td bgcolor="'.$bgcolor.'" width="20" valign="top"><input onClick="checkSumm('.$items_count.')" name="item'.$num.'" type="checkbox" value="'.$con['id'].'" />
							  <input type="hidden" name="price'.$num.'" value="'.$con['price'].'" /></td>';
						echo '<td bgcolor="'.$bgcolor.'" width="16" valign="top"><img src="/images/markers/priceitem.png" border="0" /></td>';
						echo '<td bgcolor="'.$bgcolor.'" width="">';															
						echo $con['title'];
						echo '</td>';	
						echo '<td bgcolor="'.$bgcolor.'" width="120" align="right"><div id="kdiv'.$num.'" name="kdiv" style="display:none">'.$count_field.' '.$_LANG['QTY'].'. x</div></td>';
						echo '<td bgcolor="'.$bgcolor.'" width="80" align="right">'.$con['price'].' '.$_LANG['RUB'].'.</td>';
					echo '</tr>';
				}
				echo '</table>';
				echo '<div align="right" style="margin-top:10px"><label><b>'.$_LANG['SUM_SELECTED_PRODUCTS'].':</b> <input style="border:solid 1px black" type="text" name="summField" value="0" size="6"/> '.$_LANG['RUB'].'. <input type="submit" name="addtocart" value="'.$_LANG['ADD_TO_CART'].'"/></label></div>';
				
				echo '</form>';
			} else { 
						echo '<p style="clear:both"><b>'.$_LANG['ON_REQUEST'].'</b> "'.$query.'" <b>'.$_LANG['NOT_SEARCHED_ITEMS'].'.</b></p>';
					}	
	}
$inCore->executePluginRoute($do);
} //function
?>