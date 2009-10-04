<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/
function pageSelect($records, $current, $perpage){
	$html = '';
	if ($records){
		$pages = ceil($records / $perpage);

		if($pages>1){
			$html .= '<td width="60"><strong>Страница: </strong></span></td>';	
			$html .= '<td width="60" align="center"><form style="margin:0px;padding:0px" action="" name="pageform" method="POST">';
			$html .= '<select style="width:60px" name="cpage" onchange="document.pageform.submit()">';
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$html .= '<option value="'.$p.'">'.$p.'</option>';		
				} else {
					$html .= '<option value="'.$p.'" selected>'.$p.'</option>';		
				}
			}
			$html .= '</select></form></td>';
			$html .= '<td width="30"> из <strong>'.$pages.'</strong><td>';
		}
	}
	return $html;
}

	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt='in'; } //in, out
	if (isset($_REQUEST['with_id'])) { $with_id = $_REQUEST['with_id']; } else { $with_id=0; } //messages history - user id
	
	if (usrCheckAuth()){

		echo '<div class="con_heading" style="margin-bottom:25px">Мои сообщения</div>';

		//current page
		$perpage = 15;
		if (isset($_POST['cpage'])) { $page = $_REQUEST['cpage']; } else { $page = 1; }

		if ($opt=='in'){
			//how many records
			$sql = "SELECT m.id	FROM cms_user_msg m WHERE m.to_id = $id";	
			$result = $inDB->query($sql) ;
			$msg_count = $inDB->num_rows($result);
			//sql
			$sql = "SELECT m.*, DATE_FORMAT(m.senddate, '%d-%m-%Y (%H:%i)') as fpubdate, m.from_id as sender_id
					FROM cms_user_msg m
					WHERE m.to_id = $id
					ORDER BY senddate DESC
					LIMIT ".(($page-1)*$perpage).", $perpage";	
		} else {
			if ($opt=='out'){
				//how many records
				$sql = "SELECT m.id	FROM cms_user_msg m, cms_users u WHERE m.from_id = $id AND m.to_id = u.id";	
				$result = $inDB->query($sql) ;
				$msg_count = $inDB->num_rows($result);
				//sql
				$sql = "SELECT m.*, u.nickname as author, u.login as author_login, DATE_FORMAT(m.senddate, '%d-%m-%Y (%H:%i)') as fpubdate, m.to_id as sender_id
						FROM cms_user_msg m, cms_users u
						WHERE m.from_id = $id AND m.to_id = u.id
						ORDER BY senddate DESC
						LIMIT ".(($page-1)*$perpage).", $perpage";							
			}
			if ($opt=='history'){
				$with_name = dbGetField('cms_users', "id = $with_id", 'nickname');
				//how many records
				$sql = "SELECT m.id
						FROM cms_user_msg m, cms_users u
						WHERE ((m.from_id = $id AND m.to_id = $with_id) OR (m.from_id = $with_id AND m.to_id = $id)) AND m.from_id = u.id
						ORDER BY senddate ASC";
				$result = $inDB->query($sql) ;
				$msg_count = $inDB->num_rows($result);
				//sql		
				$sql = "SELECT m.*, u.nickname as author, u.login as author_login, DATE_FORMAT(m.senddate, '%d-%m-%Y (%H:%i)') as fpubdate, m.from_id as sender_id
						FROM cms_user_msg m, cms_users u
						WHERE ((m.from_id = $id AND m.to_id = $with_id) OR (m.from_id = $with_id AND m.to_id = $id)) AND m.from_id = u.id
						ORDER BY senddate ASC
						LIMIT ".(($page-1)*$perpage).", $perpage";							
			}
		}

		$result = $inDB->query($sql) or die(mysql_error().'<br/><br/>'.$sql);

		echo '<div style="margin-bottom:10px">';				
			if ($opt=='in'){				
				$inPage->addPathway('Входящие');
				echo '<span class="usr_msgmenu_active in_span">Входящие</span> ';
				echo '<a class="usr_msgmenu_link out_link" href="/users/'.$menuid.'/'.$id.'/messages-sent.html">Отправленные</a>';	
				echo '<a class="usr_msgmenu_link new_link" href="/users/'.$menuid.'/'.$id.'/messages-new.html">Написать</a>';	
			} elseif ($opt=='out') {
				$inPage->addPathway('Отправленные');
				echo '<a class="usr_msgmenu_link in_link" href="/users/'.$menuid.'/'.$id.'/messages.html">Входящие</a> ';
				echo '<span class="usr_msgmenu_active out_span">Отправленные</span>';
				echo '<a class="usr_msgmenu_link new_link" href="/users/'.$menuid.'/'.$id.'/messages-new.html">Написать</a>';
			} elseif ($opt=='new') {
				$inPage->addPathway('Новое сообщение');
				echo '<a class="usr_msgmenu_link in_link" href="/users/'.$menuid.'/'.$id.'/messages.html">Входящие</a> ';
				echo '<a class="usr_msgmenu_link out_link" href="/users/'.$menuid.'/'.$id.'/messages-sent.html">Отправленные</a>';	
				echo '<span class="usr_msgmenu_active new_span">Написать</span>';						
			} elseif ($opt=='history') {
				$inPage->addPathway('Переписка с '.$with_name, $_SERVER['REQUEST_URI']);				
				echo '<a class="usr_msgmenu_link in_link" href="/users/'.$menuid.'/'.$id.'/messages.html">Входящие</a> ';
				echo '<a class="usr_msgmenu_link out_link" href="/users/'.$menuid.'/'.$id.'/messages-sent.html">Отправленные</a>';
				echo '<a class="usr_msgmenu_link new_link" href="/users/'.$menuid.'/'.$id.'/messages-new.html">Написать</a>';
				echo '<span class="usr_msgmenu_active history_span">Переписка &rarr; '.$with_name.'</span>';
			}
		echo '</div>';
		
		if ($opt=='in' || $opt=='out' || $opt=='history'){
		
			echo '<table class="usr_msgmenu_bar" width="100%" height="30" border="0" cellpadding="5" cellspacing="0"><tr>';
		
				echo '<td><strong>Сообщений в папке:</strong> '.$msg_count.'</td>';	
			
				if ($opt=='out'){
					echo '<td align="center"><span style="color:gray">Доставленные сообщения удаляет только получатель</span></td>';
				}
				
				if ($msg_count > $perpage){
					echo pageSelect($msg_count, $page, $perpage);
				}
				
				if ($opt=='in' && $msg_count>0){
					echo '<td width="100" align="right"><a href="/users/'.$menuid.'/'.$id.'/delmessages.html">Очистить папку</a></td>';
				}
			
			echo '</tr></table>';
									
			if ($inDB->num_rows($result)){
					echo '<div>';
					while($record = $inDB->fetch_assoc($result)){
	
						if($record['sender_id']>0){ 
							$author     = $inDB->get_fields('cms_users', 'id = '.$record['sender_id'], 'nickname, login');
							$authorlink = '<a href="'.cmsUser::getProfileURL($author['login']).'">'.$author['nickname'].'</a>';
						} else {
							if ($record['sender_id']==USER_UPDATER){
								$authorlink = 'Служба обновлений';
							}
							if ($record['sender_id']==USER_MASSMAIL){
								$authorlink = 'Служба рассылки';
							}
						}										
	
						echo '<table style="width:100%" cellspacing="0">';
						echo '<tr>';
							echo '<td class="usr_msg_title" width=""><strong>'.$authorlink.'</strong>, '.$record['fpubdate'].'</td>';
							if ($record['is_new']){
								if ($opt=='in'){
									//erase new mark
									$inDB->query("UPDATE cms_user_msg SET is_new = 0 WHERE id = ".$record['id']);
									echo '<td class="usr_msg_title" width="14" align="right"><img src="/components/users/images/warning.gif" /></td>';
									echo '<td class="usr_msg_title" width="20" align="right"><span style="color: red">Новое!</span></td>';
								} else {
									echo '<td class="usr_msg_title" width="90" align="right"><a class="msg_delete" href="/users/'.$menuid.'/delmsg'.$record['id'].'.html">Отозвать</a></td>';
								}
							} else {
								echo '<td class="usr_msg_title" width="14" align="right">&nbsp;</td>';						
								echo '<td class="usr_msg_title" width="20" align="right">&nbsp;</td>';
							}
							if ($opt=='in'){
								if ($record['sender_id']>0){
									echo '<td class="usr_msg_title" width="80" align="right"><a class="msg_reply" href="/users/'.$menuid.'/'.$record['from_id'].'/reply'.$record['id'].'.html">Ответить</a></td>';
									echo '<td class="usr_msg_title" width="80" align="right"><a class="msg_history" href="/users/'.$menuid.'/'.$id.'/messages-history'.$record['from_id'].'.html">История</a></td>';
								}
							}
							if ($opt=='in' || $record['to_id']==$inUser->id){
								echo '<td class="usr_msg_title" width="70" align="right"><a class="msg_delete" href="/users/'.$menuid.'/delmsg'.$record['id'].'.html">Удалить</a></td>';						
							}
						echo '</tr>';
						echo '</table>';
						echo '<table style="width:100%; margin-bottom:8px; padding-bottom:10px;background-color:#FFFFFF; border-bottom:dashed 1px #666;" cellspacing="4">';		
	
						$text = $record['message'];// nl2br();
						$text = $inCore->parseSmiles($text, true);
						$text = str_replace('&gt;', '>', $text);
						$text = str_replace('&lt;', '<', $text);					
						$text = str_replace('&amp;', '&', $text);					
						$text = strip_tags($text, '<img><br><a><b><u><i><table><tr><td><th><h1><h2><h3><div><span><pre>');
						
						if ($record['sender_id']>0){
							$user_img = '<a href="'.cmsUser::getProfileURL($author['login']).'">'.usrImage($record['sender_id'], 'small').'</a>';
						} else {
							$user_img = usrImage($record['sender_id'], 'small');
						}
						
						echo '<tr>';						
							echo '<td width="70" height="70" valign="middle" align="center" style="border:solid 1px silver">'.$user_img.'</td>';			
							echo '<td width="" valign="top"><div style="padding:6px">'.$text.'</div></td>';
						echo '</tr>';
						echo '</table>';
					}
					echo '</div>';
				} else { echo '<p>Нет сообщений в этой папке.</p>'; }
	
		}

		if ($opt=='new'){
			$inPage->addHeadJS('components/users/js/newmessage.js');
	
			echo '<form action="" id="newmessage" method="POST" name="msgform">';			
				echo '<table class="usr_msgmenu_bar" width="100%" height="30" border="0" cellpadding="5" cellspacing="0"><tr>';
					echo '<tr>';
						echo '<td width="40"><strong>Кому:</strong> </td>';					
						echo '<td width="160"><select name="id" id="to_id" style="width:150px">'.cmsUser::getFriendsList($inUser->id).'</select></td>';
						if ($inUser->is_admin){
							echo '<td width="10"><input name="massmail" type="checkbox" value="1" /></td>';					
							echo '<td width="">Отправить всем (массовая рассылка)</td>';		
						} else {
							echo '<td>&nbsp;</td>';
						}
					echo '</tr>';								
				echo '</table>';
				
					echo '<div>';
							echo '<input type="hidden" name="gosend"   value="1"/>';
							echo '<div class="usr_msg_bbcodebox">';
								echo cmsPage::getBBCodeToolbar('message');
							echo '</div>';							
							echo cmsPage::getSmilesPanel('message');
							echo '<textarea style="font-size:18px;border:solid 1px gray;width:100%;height:200px;" name="message" id="message"></textarea>';						
							echo '<div style="margin-top:6px;"><input type="button" id="gosend" value="Отправить" onclick="sendMessage()" style="font-size:18px"/></div>';							
					echo '</div>';			
			echo '</form>';				
		}
}

?>