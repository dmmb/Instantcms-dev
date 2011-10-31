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

////////////////////////////////////////////////////////////////////////////////////////////////
function shopAddToCart($item_id, $itemscount=1){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser     = cmsUser::getInstance();
    
	$user_id    = $inUser->id;
    $sid        = session_id();
    $can_many   = dbGetField('cms_uc_items', "id={$item_id}", 'canmany');
    $in_cart    = shopIsInCart($item_id);

	shopCheckCarts();

    if (!$in_cart){
		$sql = "INSERT INTO cms_uc_cart (user_id, session_id, item_id, pubdate, itemscount)
				VALUES ('$user_id', '$sid', '$item_id', NOW(), '$itemscount')";
    	$inDB->query($sql) ;
    }

    if ($in_cart && $can_many){
		$sql = "UPDATE cms_uc_cart SET itemscount = itemscount + 1 WHERE item_id = ".$item_id." AND (user_id=$user_id OR (user_id=0 AND session_id='$sid'))";	
        $inDB->query($sql) ;
    }
	
	return true;
}

function shopClearCart(){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if (isset($inUser->id)){ $user_id = $inUser->id; } else { $user_id = 0; }	
	$sid = session_id();
	$sql = "DELETE FROM cms_uc_cart WHERE (user_id=$user_id OR (user_id=0 AND session_id='$sid'))";	
	$rs = $inDB->query($sql) ;
	return true;
}

function shopRemoveFromCart($item_id=0){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if (isset($inUser->id)){ $user_id = $inUser->id; } else { $user_id = 0; }	
	$sid = session_id();
	$sql = "DELETE FROM cms_uc_cart WHERE item_id = $item_id AND (user_id=$user_id OR (user_id=0 AND session_id='$sid'))";	
	$rs = $inDB->query($sql) ;
	return true;
}

function shopIsInCart($item_id=0){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inUser = cmsUser::getInstance();
	if (isset($inUser->id)){ $user_id = $inUser->id; } else { $user_id = 0; }	
	$sid = session_id();
	
	if ($user_id){ $user_sql = "(user_id=$user_id OR session_id='$sid')"; } else { $user_sql = "(user_id=0 AND session_id='$sid')"; }

	if ($item_id){        
		$isin = dbRowsCount('cms_uc_cart', "item_id = $item_id AND $user_sql");
	} else {        
		$isin = dbRowsCount('cms_uc_cart', "$user_sql");
	}

	return $isin;
}

function shopCartLink(){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    global $_LANG;
	$items = shopIsInCart();	
	$html = '';
		$html .= '<a id="shop_cartlink" href="/catalog/viewcart.html">'.$_LANG['CART'];
		if ($items){
			$html .= ' ('.$items.')';
		}
		$html .= '</a>';
	return $html;
}

function shopCheckCarts(){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$sql = "DELETE FROM cms_uc_cart WHERE user_id = 0 AND pubdate <= DATE_SUB(NOW(), INTERVAL 3 HOUR)";
	$inDB->query($sql) ;
	return true;
}

function shopUpdateCart($itemcounts){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	foreach($itemcounts as $id => $count){
        $id     = intval($id);
        $count  = intval($count);
		$sql = "UPDATE cms_uc_cart SET itemscount = '$count' WHERE id = '$id'";
		$inDB->query($sql) ;
	}
	return true;
}

function shopCart(){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inPage = cmsPage::getInstance();
    $inUser = cmsUser::getInstance();
	global $_LANG;
	if (isset($inUser->id)){ $user_id = $inUser->id; } else { $user_id = 0; }	
	$sid = session_id();
	
		$inPage->backButton(false);
	 	$inPage->setTitle($_LANG['CART']);
		$inPage->addPathway($_LANG['CART']);

		$inPage->printHeading($_LANG['CART']);
		
		if ($user_id){ $user_sql = "(c.user_id=$user_id OR session_id='$sid')"; } else { $user_sql = "(c.user_id=0 AND c.session_id='$sid')"; }
		
		$sql = "SELECT i.title as title, i.id as id, i.canmany as canmany, c.id as cid, cat.id as category_id, cat.title as category, c.itemscount as itemscount, i.price as price
				FROM cms_uc_items i, cms_uc_cart c, cms_uc_cats cat
				WHERE $user_sql AND c.item_id = i.id AND i.category_id = cat.id
				ORDER BY c.pubdate";
		$rs = $inDB->query($sql) ;
		
		if ($inDB->num_rows($rs)){
			//BUILD LIST
			//delete confirmation js
			echo '<script type="text/javascript">'."\n";
				echo "function deleteItem(id){
						if(confirm('".$_LANG['DEL_POSITION_FROM_CART']."')){
							window.location.href = '/catalog/cartremove'+id+'.html';	
						}
					}";
				echo "function clearCart(){
						if(confirm('".$_LANG['CLEAR_CART']."?')){
							window.location.href = '/catalog/clearcart.html';	
						}
					}";				
				echo "function saveCart(){
						document.cartform.submit();	
					}";									
			echo '</script>'."\n";
			//table
			echo '<form action="/catalog/savecart.html" method="POST" name="cartform">';
			echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
			echo '<tr>';
				echo '<td width="16">&nbsp;</td>';
				echo '<td><strong>'.$_LANG['ITEM'].'</strong></td>';
				echo '<td width="200"><strong>'.$_LANG['CAT'].'</strong></td>';
				echo '<td width="120" align="center"><strong>'.$_LANG['QTY'].'</strong></td>';
				echo '<td width="100"  align="center"><strong>'.$_LANG['PRICE'].'</strong></td>';
				echo '<td width="20" align="center">&nbsp;</td>';
			echo '</tr>';
			$row=0; $total = 0;
			while($item = $inDB->fetch_assoc($rs)){
                
				$row++;
				if ($row%2) { $class="search_row1"; } else { $class="search_row2"; }

				$item['realprice']      = $item['price'];
                $item['price']          = shopDiscountPrice($item['id'], $item['category_id'], $item['price']);
                $item['totalprice']     = $item['price'] * $item['itemscount'];
				$item['price']          = number_format($item['price'], 2, '.', '');
				$item['totalprice']     = number_format($item['totalprice'], 2, '.', '');
                $total += $item['totalprice'];

				echo '<tr>';
					echo '<td class="'.$class.'"><img src="/components/catalog/images/icons/cart.png" border="0"></td>';
					echo '<td class="'.$class.'"><a href="/catalog/item'.$item['id'].'.html">'.$item['title'].'</a></td>';
					echo '<td class="'.$class.'"><a href="/catalog/'.$item['category_id'].'">'.$item['category'].'</a></td>';
					echo '<td class="'.$class.'" align="center">'.$item['price'].' x '.shopItemsCounter($item['cid'], $item['itemscount'], $item['canmany']).' =</td>';
					echo '<td class="'.$class.'" align="center"><strong>'.$item['totalprice'].'</strong></td>';
					echo '<td class="'.$class.'" align="center">';
							echo '<a href="javascript:deleteItem('.$item['id'].')" title="'.$_LANG['DELETE'].'"><img src="/admin/images/actions/delete.gif" border="0"/></a>';
					echo '</td>';
				echo '</tr>';
                
			}
			echo '</table>';
			echo '</form>';

            shopDiscountsInfo($total, true);
			$total = number_format($total, 2, '.', '');
			echo '<div id="cart_total">';
				echo '<span>'.$_LANG['CART_TOTAL'].':</span> '.$total.' '.$_LANG['RUB'];
			echo '</div>';
			
			//buttons			
			echo '<div id="cart_buttons">';
				echo '<div id="cart_buttons1">';
					echo '<a href="javascript:saveCart()" title="'.$_LANG['SAVE'].'">';
						echo '<img src="/components/catalog/images/shop/savecart.jpg" border="0" alt="'.$_LANG['SAVE'].'"/>';
					echo '</a> ';
					echo '<a href="javascript:clearCart();" title="'.$_LANG['CLEAR_CART'].'">';
						echo '<img src="/components/catalog/images/shop/clearcart.jpg" border="0" alt="'.$_LANG['CLEAR_CART'].'"/>';
					echo '</a> ';
				echo '</div>';
				echo '<div id="cart_buttons2">';
					echo '<a href="/catalog" title="'.$_LANG['BACK_TO_SHOP'].'">';
						echo '<img src="/components/catalog/images/shop/cartback.jpg" border="0" alt="'.$_LANG['BACK_TO_SHOP'].'"/>';
					echo '</a> ';
					echo '<a href="/catalog/order.html" title="'.$_LANG['CART_ORDER'].'">';
						echo '<img src="/components/catalog/images/shop/cartorder.jpg" border="0" alt="'.$_LANG['CART_ORDER'].'"/>';
					echo '</a> ';
				echo '</div>';
			echo '</div>';
		} else {
			//NO ITEMS
			echo '<p>'.$_LANG['NOITEMS_IN_CART'].'</p>';
			echo '<div id="cart_buttons2">';
				echo '<a href="/catalog" title="'.$_LANG['BACK_TO_SHOP'].'">';
					echo '<img src="/components/catalog/images/shop/cartback.jpg" border="0" alt="'.$_LANG['BACK_TO_SHOP'].'"/>';
				echo '</a> ';
			echo '</div>';

		}
		
}

function shopOrder($cfg){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inPage = cmsPage::getInstance();
    $inUser = cmsUser::getInstance();
	global $_LANG;
	if (isset($inUser->id)){ $user_id = $inUser->id; } else { $user_id = 0; }	
	$sid = session_id();
	
		$inPage->backButton(false);
	 	$inPage->setTitle($_LANG['CART_ORDERING']);
		$inPage->addPathway($_LANG['CART'], '/catalog/viewcart.html');
		$inPage->addPathway($_LANG['CART_ORDERING'], $_SERVER['REQUEST_URI']);

		echo '<div class="con_heading">'.$_LANG['CART_ORDERING'].'</div>';
		
		echo '<div class="con_description">'.nl2br($cfg['delivery']).'</div>';
		
		if ($user_id){ $user_sql = "(c.user_id=$user_id OR session_id='$sid')"; } else { $user_sql = "(c.user_id=0 AND c.session_id='$sid')"; }
		
		$sql = "SELECT i.title as title, i.id as id, i.canmany as canmany, c.id as cid, cat.id as category_id, cat.title as category, c.itemscount as itemscount, i.price as price
				FROM cms_uc_items i, cms_uc_cart c, cms_uc_cats cat
				WHERE $user_sql AND c.item_id = i.id AND i.category_id = cat.id
				ORDER BY c.pubdate";
		$rs = $inDB->query($sql) ;
		
		if ($inDB->num_rows($rs)){
			//BUILD LIST
			//table
			echo '<form action="/catalog/savecart.html" method="POST" name="cartform">';
			echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
			echo '<tr>';
				echo '<td width="16">&nbsp;</td>';
				echo '<td><strong>'.$_LANG['ITEM'].'</strong></td>';
				echo '<td width="200"><strong>'.$_LANG['CAT'].'</strong></td>';
				echo '<td width="120" align="center"><strong>'.$_LANG['QTY'].'</strong></td>';
				echo '<td width="100"  align="center"><strong>'.$_LANG['PRICE'].'</strong></td>';
			echo '</tr>';
			$row=0; $total = 0;
			while($item = $inDB->fetch_assoc($rs)){
				$row++;
				if ($row%2) { $class="search_row1"; } else { $class="search_row2"; }

                $item['realprice'] = $item['price'];
                $item['price'] = shopDiscountPrice($item['id'], $item['category_id'], $item['price']);
                $item['totalprice'] = $item['price'] * $item['itemscount'];

				$item['price'] = number_format($item['price'], 2, '.', '');
				$item['totalprice'] = number_format($item['totalprice'], 2, '.', '');
                $total += $item['totalprice'];
				echo '<tr>';
					echo '<td class="'.$class.'"><img src="/components/catalog/images/icons/cart.png" border="0"></td>';
					echo '<td class="'.$class.'"><a href="/catalog/item'.$item['id'].'.html">'.$item['title'].'</a></td>';
					echo '<td class="'.$class.'"><a href="/catalog/'.$item['category_id'].'">'.$item['category'].'</a></td>';
					echo '<td class="'.$class.'" align="center">'.$item['price'].' x '.$item['itemscount'].' =</td>';
					echo '<td class="'.$class.'" align="center"><strong>'.$item['totalprice'].'</strong></td>';
				echo '</tr>';			
			}
			echo '</table>';
			echo '</form>';
			
			shopDiscountsInfo($total);
            $total = number_format($total, 2, '.', '');

			echo '<div id="cart_total">';
				echo '<span>'.$_LANG['TOTAL_PRICE'].':</span> '.$total.' '.$_LANG['RUB'];
			echo '</div>';            

			//DELIVERY INFO FORM
			echo '<div class="con_heading">'.$_LANG['INFO_CUSTOMER'].'</div>';
			
			echo '<form action="/catalog/finish.html" method="POST">';
			echo '<table width="100%" cellspacing="0" cellpadding="5">';
			echo '<tr>';		
				echo '<td width="40%" align="right">'.$_LANG['FIO_CUSTOMER'].': </td>';
				echo '<td width="60%" align="left"><input name="customer_fio" type="text" size="45" value="'.$inUser->nickname.'" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="40%" align="right">'.$_LANG['ORGANIZATION'].': </td>';
				echo '<td width="60%" align="left"><input name="customer_company" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="40%" align="right">'.$_LANG['CONTACT_PHONE'].': </td>';
				echo '<td width="60%" align="left"><input name="customer_phone" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';
				echo '<td width="40%" align="right">'.$_LANG['ADRESS_EMAIL'].': </td>';
				echo '<td width="60%" align="left"><input name="customer_email" type="text" size="45" value="'.$inUser->email.'" /></td>';
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="40%" align="right">'.$_LANG['CUSTOMER_COMMENT'].': </td>';
				echo '<td width="60%" align="left"><input name="customer_comment" type="text" size="45" /></td>';			
			echo '</tr>';

			echo '<tr>';
                echo '<td width="40%" align="right">&nbsp;</td>';
				echo '<td width="60%" align="left">';
						echo cmsPage::getCaptcha();
				echo '</td>';			
			echo '</tr>';

			echo '<tr>';		
				echo '<td width="30%" align="right">&nbsp;</td>';
				echo '<td width="70%" align="left"><input name="order" type="submit" value="'.$_LANG['SUBMIT_ORDER'].'" /></td>';
			echo '</tr>';
			echo '</table>';
			echo '</form>';

		} else {
			//NO ITEMS
			echo '<p>'.$_LANG['NOITEMS_IN_CART'].'</p>';
			echo '<div id="cart_buttons2">';
				echo '<a href="/catalog" title="'.$_LANG['BACK_TO_SHOP'].'">';
					echo '<img src="/components/catalog/images/shop/cartback.jpg" border="0" alt="'.$_LANG['BACK_TO_SHOP'].'"/>';
				echo '</a> ';
			echo '</div>';

		}

}

function shopDiscountsInfo(&$total, $is_cart=false){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $total_orig = $total;

    $sql = "SELECT title, sign, unit, value, if_limit FROM cms_uc_discount WHERE cat_id = 0 AND sign>=2 ORDER BY sign DESC";
    $res = $inDB->query($sql) ;

    if($inDB->num_rows($res)){
        echo '<table width="100%" cellpadding="5" cellspacing="0" border="0">';
        while ($dis = $inDB->fetch_assoc($res)){
            $show_dis = false;
            $show_sign = '+';
            if ($dis['sign']==2){
                if ($dis['unit']=='%'){ $total = $total + ($total*($dis['value']/100)); }
                if ($dis['unit']=='руб.'){ $total = $total + $dis['value']; }
                $show_dis = true;
                $show_sign = '+';
            }
            if ($dis['sign']==3){
                $show_dis = ($total_orig >= $dis['if_limit']);
                if ($show_dis){
                    if ($dis['unit']=='%'){ $total = $total - ($total*($dis['value']/100)); }
                    if ($dis['unit']=='руб.'){ $total = $total - $dis['value']; }
                    $show_sign = '-';
                }
            }
            if ($show_dis){
                echo '<tr>';
                    echo '<td width="16" align="center">'.$show_sign.'</td>';
                    echo '<td>'.$dis['title'].'</td>';
                    echo '<td width="200">&nbsp</td>';
                    echo '<td width="120" align="center">&nbsp</td>';
                    if ($dis['unit']=='руб.'){
                        echo '<td width="100" align="center">'.number_format($dis['value'], 2, '.', '').'</td>';
                    } else {
                        echo '<td width="100" align="center">'.$dis['value'].'%</td>';
                    }
                    if ($is_cart){
                        echo '<td width="20">&nbsp;</td>';
                    }
                echo '</tr>';
            }
        }
        echo '</table>';
    }
}

function shopDiscountPrice($item_id, $cat_id, $price){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $sql = "SELECT sign, unit, value FROM cms_uc_discount WHERE cat_id = $cat_id OR cat_id = 0 AND sign<2";
    $res = $inDB->query($sql) ;

    if($inDB->num_rows($res)){
        while ($dis = $inDB->fetch_assoc($res)){
            if ($dis['unit']=='%'){ $price = $price + ($price*($dis['value']/100))*$dis['sign']; }
            if ($dis['unit']=='руб.'){ $price = $price + $dis['value']*$dis['sign']; }
        }
    }
    return $price;
}

function shopFinishOrder($cfg){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
    $inPage = cmsPage::getInstance();
    $inUser = cmsUser::getInstance();
    $inConf = cmsConfig::getInstance();
    global $_CFG;
    global $_LANG;
	if (isset($inUser->id)){ $user_id = $inUser->id; } else { $user_id = 0; }	
	$sid = session_id();
	
		$inPage->backButton(false);
	 	$inPage->setTitle($_LANG['ORDER_COMPLETE']);
		
		if ($user_id){ $user_sql = "(c.user_id=$user_id OR session_id='$sid')"; } else { $user_sql = "(c.user_id=0 AND c.session_id='$sid')"; }
		
		$sql = "SELECT i.title as title, i.id as id, i.canmany as canmany, i.price as price, 
						c.id as cid, c.itemscount as itemscount,
						cat.id as category_id, cat.title as category
				FROM cms_uc_items i, cms_uc_cart c, cms_uc_cats cat
				WHERE $user_sql AND c.item_id = i.id AND i.category_id = cat.id
				ORDER BY c.pubdate";
		$rs = $inDB->query($sql) ;
		
		if ($inDB->num_rows($rs)){
			//check user data
			$customer = array();
			if(!empty($_REQUEST['customer_fio'])) { $customer['fio'] = $inCore->request('customer_fio', 'str'); } else { $error .= $_LANG['EMPTY_NAME'].'<br/>'; }
			if(!empty($_REQUEST['customer_phone'])) { $customer['phone'] = $inCore->request('customer_phone', 'str'); } else { $error .= $_LANG['EMPTY_PHONE'].'<br/>'; }
			$customer['company'] = $inCore->request('customer_company', 'str');
            $customer['email']   = $inCore->request('customer_email', 'str');
			$customer['comment'] = $inCore->request('customer_comment', 'str');
			if(!$inCore->checkCaptchaCode($_REQUEST['code'])) { $error .= $_LANG['ERR_CAPTCHA'].'<br/>'; }

			//BUILD MESSAGE
			if($error==''){
				//message heading
				$mail_message = '';				
				$mail_message .= $_LANG['GET_ORDER_FROM_CATALOG']." \"".$inConf->sitename."\".\n\n";
				$mail_message .= $_LANG['CUSTOMER']."\n-----------------------------\n";
				$mail_message .= $_LANG['FIO'].": " . $customer['fio'] . "\n";
				$mail_message .= $_LANG['COMPANY'].": " . $customer['company'] . "\n";
				$mail_message .= $_LANG['PHONE'].": " . $customer['phone'] . "\n";
                $mail_message .= "EMAIL: " . $customer['email'] . "\n";
				$mail_message .= $_LANG['ORDER_COMMENT'].": " . @$customer['comment'] . "\n\n";
				//list of items
				$mail_message .= $_LANG['ORDER']."\n---------------------------------\n";
				$row=0; $total = 0;
				while($item = $inDB->fetch_assoc($rs)){
					$row++;
                    $item['price']          = shopDiscountPrice($item['id'], $item['category_id'], $item['price']);
                    $item['totalprice']     = $item['price'] * $item['itemscount'];
                    $item['price']          = number_format($item['price'], 2, '.', '');
                    $item['totalprice']     = number_format($item['totalprice'], 2, '.', '');
                    $total += $item['totalprice'];
					$mail_message .= $row . '. ' . $item['title'] . ' (' . $item['itemscount'] . '  x ' . $item['price'] . ' '.$_LANG['RUB'].') = ' . $item['totalprice'] . ' '.$_LANG['RUB'] . "\n";
				}

                ob_start(); shopDiscountsInfo($total); ob_clean();
				$total = number_format($total, 2, '.', '');
                
				$mail_message .= "\n" . $_LANG['TOTAL_ORDER_PRICE'].': '.$total.' '.$_LANG['RUB'] . "\n";
				$email_subj = str_replace('{sitename}', $inConf->sitename, $_LANG['EMAIL_SUBJECT']);
				$inCore->mailText($cfg['email'], $email_subj, $mail_message);

                if ($cfg['notice'] && $customer['email']){
                    $inCore->mailText($customer['email'], $_LANG['CUSTOMER_EMAIL_SUBJECT'], $mail_message);
                }
				//order completed							
				echo '<div class="con_heading">'.$_LANG['THANK'].'!</div>';
				echo '<p style="clear:both"><b>'.$_LANG['CUSTOMER_EMAIL_SUBJECT'].'.</b><br/>'.$_LANG['CUSTOMER_EMAIL_TEXT'].'</p>';
				echo '<p><a href="/">'.$_LANG['CONTINUE'].'</a></p>';
				shopClearCart();	
			} else {			
				//order failed
				echo '<div class="con_heading">'.$_LANG['ERROR'].'!</div>';
				echo '<p style="clear:both; color:red">'.$error.'</p>';		
				echo '<p><a href="#" onClick="window.history.back()">'.$_LANG['BACK'].'</a></p>';
			}
		} else {
			//NO ITEMS
			echo '<p>'.$_LANG['NOITEMS_IN_CART'].'</p>';
			echo '<div id="cart_buttons2">';
				echo '<a href="/catalog" title="'.$_LANG['BACK_TO_SHOP'].'">';
					echo '<img src="/components/catalog/images/shop/cartback.jpg" border="0" alt="'.$_LANG['BACK_TO_SHOP'].'"/>';
				echo '</a> ';
			echo '</div>';

		}
}

function shopItemsCounter($cartitem_id, $count, $canmany=1){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$count_field = '';
	if ($canmany) { $count_field = '<input type="text" style="text-align: center; font-size:9px; border:solid 1px black" size="5" name="kolvo['.$cartitem_id.']" value="'.$count.'"/>'; }
	else { $count_field = '<input type="hidden" name="kolvo['.$cartitem_id.']" value="1"/>1'; }
	return $count_field;
}

?>
