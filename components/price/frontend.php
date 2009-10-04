<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
//                                   LICENSED BY GNU/GPL v2                                  //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function searchForm($query=''){
    $inCore = cmsCore::getInstance();
	//SEARCH FORM
	echo '<div class="price_search_form">
		  <form action="/index.php" method="GET" style="clear:both">';
		echo '<input type="hidden" name="view" value="price"/>';
		echo '<input type="hidden" name="do" value="search"/>';					
		echo '<input type="hidden" name="menuid" value="'.$inCore->menuId().'"/>';
		echo '<label>';							 
			echo '<b>����� ������:</b> ';
			echo '<input type="text" name="query" value="'.$query.'" class="price_search">';
			echo '<select name="look" style="margin-left:2px">
					<option value="anyword">����� �����</option>
					<option value="allwords">��� �����</option>
					<option value="phrase">����� �������</option>
				  </select>';
		echo '</label>';				
	echo '</form></div>';
}

function pageBar($cat_id, $current, $perpage){
    $inCore = cmsCore::getInstance();
    $inDB = cmsDatabase::getInstance();
	$html = '';
	
	$result = $inDB->query("SELECT id FROM cms_price_items WHERE category_id = $cat_id") ;
	$records = $inDB->num_rows($result);
	
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<div class="pagebar">';
			$html .= '<span class="pagebar_title"><strong>��������: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					
					$link = '/price/'.$inCore->menuId().'/'.@$_REQUEST['id'].'-'.$p;
					
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

	$menuid = $inCore->menuId();
	$cfg    = $inCore->loadComponentConfig('price');
	
	if (isset($_REQUEST['id'])){ if(is_numeric($_REQUEST['id'])) { $id = (int)$_REQUEST['id']; } else { die('HACKING ATTEMPT BLOCKED'); } } else { $id = 0; }
	if (isset($_REQUEST['do'])){ $do = htmlentities($_REQUEST['do'], ENT_QUOTES); } else { $do = 'view'; 	}
	
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
		$inPage->setTitle('����� ������');
		$inPage->backButton(false);
		
		$error = '';
		$customer = array();

		if(!empty($_REQUEST['customer_fio'])) { $customer['fio'] = $_REQUEST['customer_fio']; } else { $error .= '������� ���� ���!<br/>'; }
		if(!empty($_REQUEST['customer_phone'])) { $customer['phone'] = $_REQUEST['customer_phone']; } else { $error .= '������� ���������� �������!<br/>'; }		
		$customer['company'] = @$_REQUEST['customer_company'];
		$customer['comment'] = @$_REQUEST['customer_comment'];
		
		if(!$inCore->checkCaptchaCode($_REQUEST['code'])) { $error .= '����������� ������ �������� ��� � ��������!<br/>'; }
				
		if($error==''){
		
			$mail_message = '';
			
			$mail_message .= "������� ����� �� ���������� �����.\n\n";
			$mail_message .= "����������\n-----------------------------\n";
			
			$mail_message .= "���: " . $customer['fio'] . "\n";
			$mail_message .= "��������: " . @$customer['company'] . "\n";
			$mail_message .= "�������: " . $customer['phone'] . "\n";
			$mail_message .= "�������������: " . @$customer['comment'] . "\n\n";

			$mail_message .= "�����\n---------------------------------\n";
		
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
					$mail_message .= $num . '. ' . $con['title'] . ' (' . $_SESSION['cart'][$con['id']] . '  x ' . $con['price'] . ' ���) = ' . $item_totalcost . ' ���.' . "\n";
				}
			}
				
			$mail_message .= "\n" . '����� ����� ������: '.$total_summ.' ���.' . "\n";				
			$inCore->mailText($cfg['email'], 'InstantCMS: ����� �� ����������', $mail_message);
			//////////////////////////////////////////////////////////////////////////
			
			unset($_SESSION['cart']);		
			
			echo '<div class="con_heading">�������!</div>';						
			echo '<p style="clear:both"><b>��� ����� �������� � ���������.</b><br/>���� ��������� �������� � ���� �� ���������� ���� �������� � ����� ��������� �����.</p>';		
			echo '<p><a href="/">����������</a></p>';
		
		} else {
			echo '<div class="con_heading">������!</div>';						
			echo '<p style="clear:both; color:red">'.$error.'</p>';		
			echo '<p><a href="#" onClick="window.history.back()">�����</a></p>';	
		}
		
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='order'){
		$inPage->setTitle('���������� ������');
		echo '<div class="con_heading">���������� ������</div>';				
		
		//PATHWAY ENTRY
		$inPage->addPathway('�������', '/price/cart.html');					
		$inPage->addPathway('���������� ������', $_SERVER['REQUEST_URI']);	
		
		//GET ITEMS DATA FROM DATABASE			
		$sql = "SELECT * FROM cms_price_items WHERE ";	
		$match = ""; $n=0;				
		foreach($_REQUEST['kolvo'] as $key=>$val){ 
			if ($val) { 
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
					echo '<td class="'.$class.'" width="160" align="right" valign="top">'.$con['price'].' x '.$_REQUEST['kolvo'][$con['id']].' ��. = <b>'.$item_totalcost.' ���.</b></td>';															
				echo '</tr>';
			}
			echo '</table>';	
			
			echo '<p><b>����� ����� ������:</b> '.$total_summ.' ���.</p>';
			//DELIVERY INFO FORM
			echo '<div class="con_heading">���������� ����������</div>';				
			
			echo '<form action="/price/finish.html" method="POST">';
			echo '<table width="100%" cellspacing="0" cellpadding="5">';
			echo '<tr>';		
				echo '<td width="30%" align="right">��� ����������: </td>';
				echo '<td width="70%" align="left"><input name="customer_fio" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="30%" align="right">�����������: </td>';
				echo '<td width="70%" align="left"><input name="customer_company" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="30%" align="right">���������� �������: </td>';
				echo '<td width="70%" align="left"><input name="customer_phone" type="text" size="45" /></td>';			
			echo '</tr>';
			echo '<tr>';		
				echo '<td width="30%" align="right">�������������� ��������: </td>';
				echo '<td width="70%" align="left"><input name="customer_comment" type="text" size="45" /></td>';			
			echo '</tr>';


			echo '<tr>';		
                echo '<td width="30%" align="right">&nbsp;</td>';
                echo '<td width="70%" align="left">'.cmsPage::getCaptcha().'</td>';
			echo '</tr>';

			echo '<tr>';		
				echo '<td width="30%" align="right">&nbsp;</td>';
				echo '<td width="70%" align="left"><input name="order" type="submit" value="��������� �����" /></td>';			
			echo '</tr>';
			echo '</table>';
			echo '</form>';
					
		}				
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($do=='cart'){
		echo '<div class="con_heading">�������</div>';	
		
		$inPage->setTitle('�������');
		$inPage->backButton(false);
		
		//PATHWAY ENTRY
		$inPage->addPathway('�������', $_SERVER['REQUEST_URI']);
				
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
				echo '<a style="clear:both" href="/components/price/cart.php?clearcart">�������� �������</a>';
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
						echo '<td class="'.$class.'" width="16" valign="top"><a href="/index.php?view=price&do=removeitem&id='.$con['id'].'" title="������� �������"><img src="/images/icons/delete.gif" border="0"/></a></td>';
						echo '<td class="'.$class.'" width="16" valign="top"><img src="/images/markers/priceitem.png" border="0" /></td>';
						echo '<td class="'.$class.'" width="" valign="top">';															
						echo $con['title'];
						echo '</td>';	
						echo '<td class="'.$class.'" width="160" align="right" valign="top">'.$con['price'].' x '.$count_field.' ��. = </td>';															
						echo '<td class="'.$class.'" width="80" align="right" valign="top"><b>'.$item_totalcost.' ���.</b></td>';			
					echo '</tr>';
				}
				echo '</table></form>';
	
				echo '<table style="margin-top:15px" cellpadding="6" cellspacing="0" border="0" width="100%">';
				echo '<tr>';
					echo '<td width="" style="border-top:solid 1px black" align="left">
							<a href="/price">&laquo; ��������� � ����������</a> | <a href="#" onClick="document.orderform.submit();">������� � ���������� ������ &raquo;</a>				
						  </td>';
					echo '<td width="160" style="border-top:solid 1px black" align="right"><b>�����:</b> '.$total_summ.' ���.</td>';
				echo '</tr>';
				echo '</table>';
				
			}	
		} else { 
					echo '<p style="clear:both"><b>� ������� ��� �������</b></p>';
					echo '<p>��������� � ���������, �������� ������������ ��� ������� ��������� � ������� ������ "�������� � �������". ������ ����� ����� �� ������� �������� ��������� ������.</p>';
					echo '<p><a href="/price">&laquo; ��������� � ����������</a></p>';
		}			
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='view'){ 

		if ($id==0){ //SHOW CATEGORIES LIST
		$pagetitle = $inCore->menuTitle();
		if ($pagetitle) { $inPage->setTitle($pagetitle); } 
		else { $inPage->setTitle('���������'); $pagetitle = '���������'; }
		
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
							echo '<a href="/price/'.$menuid.'/'.$cat['id'].'">'.$cat['title'].'</a> ('.$cat['content_count'].')';
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
						echo '<td class="'.$class.'" width="120" align="right" valign="top"><div id="kdiv'.$num.'" name="kdiv" style="display:none">'.$count_field.' ��. x</div></td>';			
						echo '<td class="'.$class.'" width="100" align="right" valign="top">'.$con['price'].' ���.</td>';			
					echo '</tr>';
				}
				echo '</table>';
				echo pageBar($id, $page, $perpage);
				echo '<div align="right" style="margin-top:10px"><label><b>����� ��������� �������:</b> <input style="border:solid 1px black" type="text" name="summField" value="0" size="6"/> ���. <input type="submit" name="addtocart" value="�������� � �������"/></label></div>';
				
				echo '</form>';
			} else { 
						echo '<p>��� ������� ��� �����������.</p>'; 
					}
		} //ALBUM CONTENT
		
		
	} // DO = VIEW CATEGORIES
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	if ($do=='search'){

			$inPage->setTitle('����� ������');
			$inPage->addHeadJS('components/price/common.js');

			echo '<div class="con_heading">����� ������</div>';				
					
			$sql = "SELECT *
					FROM cms_price_items i
					WHERE published = 1 AND ";
					
			//Search conditions//////////////////////////////////////////
			if($look == 'anyword'){
				//$looktype = '����� �����';
				foreach($words as $w){
					if(strlen($w)>1){
						$n++;
						if ($n==1) { $sql .= "title LIKE '%$w%'"; }
						else { $sql .= " OR title LIKE '%$w%'"; }
					}
				}		
			}
		
			if($look == 'allwords'){
				//looktype = '��� �����';		
				foreach($words as $w){
					if(strlen($w)>1){
						$n++;
						if ($n==1) { $sql .= "title LIKE '%$w%'"; }
						else { $sql .= " AND title LIKE '%$w%'"; }
					}
				}		
			}
			
			if($look == 'phrase'){
				//$looktype = '����� �������';		
				$sql .= "title LIKE '%$query%'";			
			}
			/////////////////////////////////////////////////////////////				
							
			$result = $inDB->query($sql) ;
			$items_count = $inDB->num_rows($result);
			
			searchForm($query);
			
			if ($items_count){	
				$num = 0;
				echo '<p style="clear:both"><b>������� �������:</b> '.$items_count.'</p>';
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
						echo '<td bgcolor="'.$bgcolor.'" width="120" align="right"><div id="kdiv'.$num.'" name="kdiv" style="display:none">'.$count_field.' ��. x</div></td>';			
						echo '<td bgcolor="'.$bgcolor.'" width="80" align="right">'.$con['price'].' ���.</td>';			
					echo '</tr>';
				}
				echo '</table>';
				echo '<div align="right" style="margin-top:10px"><label><b>����� ��������� �������:</b> <input style="border:solid 1px black" type="text" name="summField" value="0" size="6"/> ���. <input type="submit" name="addtocart" value="�������� � �������"/></label></div>';
				
				echo '</form>';
			} else { 
						echo '<p style="clear:both"><b>�� �������</b> "'.$query.'" <b>�� ������� �������.</b></p>'; 
					}	
	}

} //function
?>