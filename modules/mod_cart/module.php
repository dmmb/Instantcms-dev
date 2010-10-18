<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

	function mod_cart($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
        global $_LANG;

		$cfg = $inCore->loadModuleConfig($module_id);

		if (@$cfg['source'] == 'price'){
			//SOURCE: Pricelist
			$totalitems = @sizeof($_SESSION['cart']);
			$cartlink = '/price/cart.html';

			$sql = "SELECT * FROM cms_price_items WHERE ";	
			$match = ""; $n=0;				
			foreach($_SESSION['cart'] as $key=>$val){ 
				if($n==0) { $match .= "id = $key"; $n++; } else { $match .= " OR id = $key"; }
			}				
			$sql .= $match;
		} else {
			//SOURCE: Catalog (shop)
			if (isset($inUser->id)){ $user_id = $inUser->id; } else { $user_id = 0; }	
			$sid = session_id();
			if (!function_exists('shopOrder')){
				include $_SERVER['DOCUMENT_ROOT'].'/components/catalog/includes/shopcore.php';
			}
			$totalitems = shopIsInCart();
			$cartlink = '/catalog/viewcart.html';

			if ($user_id){ $user_sql = "(c.user_id=$user_id OR c.session_id='$sid')"; } else { $user_sql = "(c.user_id=0 AND c.session_id='$sid')"; }

			$sql = "SELECT i.title as title, i.price as price, c.itemscount as itemscount, i.category_id as category_id
					FROM cms_uc_cart c, cms_uc_items i
					WHERE c.item_id = i.id AND $user_sql";
		}

		if($totalitems){
			//GET ITEMS DATA FROM DATABASE			
			$result = $inDB->query($sql);
			$items_count = $inDB->num_rows($result);

			if ($items_count){	
				echo '<div class="cart_detaillink">
						<a href="'.$cartlink.'">'.$_LANG['CART_GOTO_CART'].'  &raquo;</a>
					  </div>';
				$num = 0; $total_summ = 0;

				if ($cfg['showtype']=='list'){
				
					echo '<table style="font-size:9px" cellpadding="4" cellspacing="0" border="0" width="100%">';
					while($con = $inDB->fetch_assoc($result)){
						$num++;
						if (!($num%2)) { $bgcolor="cartrow2"; } else { $bgcolor = "cartrow1"; }
						
						$price = $con['price'];
						
						if ($cfg['source']=='price'){
							$quantity = $_SESSION['cart'][$con['id']];
						} else {
							$quantity = $con['itemscount'];
                            $price = shopDiscountPrice($con['id'], $con['category_id'], $price);
						}
	
						$item_totalcost =  $price * $quantity;
						$total_summ += $item_totalcost;
						
						$price = number_format($price, 2, '.', ' ');
						$item_totalcost = number_format($item_totalcost, 2, '.', ' ');
						
						echo '<tr>';
							echo '<td class="'.$bgcolor.'" width="" valign="top">';															
								echo '<div class="cart_item">'.$con['title'].'</div>';
								if ($quantity==1){
									echo '<div class="cart_price">'.$item_totalcost.' '.$_LANG['CART_R'].'.</div>';
								} else {
									echo '<div class="cart_price">'.$quantity.' x '.$price.' = '.$item_totalcost.' '.$_LANG['CART_R'].'.</div>';
								}
							echo '</td>';														
						echo '</tr>';
					}
					echo '</table>';
					
					$total_summ = number_format($total_summ, 2, '.', ' ');
					echo '<div align="right" class="cart_total">
							<b>'.$_LANG['CART_SUMM'].':</b> '.$total_summ.' '.$_LANG['CART_RUB'].'.
						  </div>';				
				}
				
				if (strstr($cfg['showtype'], 'qty')){
					while($con = $inDB->fetch_assoc($result)){
						$num++;
						$price = $con['price'];
						if ($cfg['source']=='price'){
							$quantity = $_SESSION['cart'][$con['id']];
						} else {
							$quantity = $con['itemscount'];
						}
						$item_totalcost =  $price * $quantity;
						$total_summ += $item_totalcost;
					}
					echo '<div class="cart_count"><strong>'.$_LANG['CART_ITEMS'].':</strong> <a href="/price/cart.html">'.$num.' '.$_LANG['CART_QTY'].'.</a></div>';
					$total_summ = number_format($total_summ, 2, '.', ' ');
					if ($cfg['showtype']=='qtyprice'){
						echo '<div class="cart_total"><strong>'.$_LANG['CART_TOTAL'].':</strong> '.$total_summ.' '.$_LANG['CART_RUB'].'.</div>';
					}
				}
			}	
		} else { 
					echo '<p style="clear:both"><b>'.$_LANG['CART_NOT_ITEMS'].'</b></p>';
			}			
		
				
		return true;
	
	}
?>