<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

defined('VALID_CMS_ADMIN') or die( '������ ��������' );

function dbQuery($sql){

    $inDB = cmsDatabase::getInstance();

	if (!$GLOBALS['DEMO_MODE']){
		return $inDB->query($sql);
	} else {
	
		if (strstr($sql, 'SELECT')){
			return $inDB->query($sql);
		}
	
	}
    
	return;
    
}

function cpAccessDenied(){
	header('location:/admin/index.php?view=noaccess');
}

function cpWarning($text){
	return '<div id="warning"><span>��������: </span>'.$text.'</div>';	
}

function cpWritable($file){ //relative path with starting "/"
	if (is_writable($_SERVER['DOCUMENT_ROOT'].$file)){
		return true;
	} else {
		return @chmod($_SERVER['DOCUMENT_ROOT'].$file, 0755);
	}	
}

function cpCheckWritable($file, $type='file'){	
	if (!cpWritable($file)){	
		if ($type=='file'){
			echo cpWarning('���� "<strong>'.$file.'</strong>" �� �������� ��� ������! ���������� ����� 755 �� ���� ����.');
		} else {
			echo cpWarning('����� "<strong>'.$file.'</strong>" �� �������� ��� ������! ���������� ����� 755 �� ��� �����.');
		}	
	}
}

function cpUpdates(){
    $inCore = cmsCore::getInstance();
    $inUser = cmsUser::getInstance();
	$userid = $inUser->id;

	$html = '';
	
	### COMMENTS 
	$sql = "SELECT *, DATE_FORMAT(pubdate, '%d-%m-%Y - %H:%i') as fpubdate
			FROM cms_comments
			ORDER BY pubdate DESC
			LIMIT 5";
	$result = dbQuery($sql);
	if(mysql_num_rows($result)){
		while($item = mysql_fetch_assoc($result)){
            $text = $inCore->strClear($item['content']);
            if (strlen($text)>100) { $text = substr($text, 0, 100) . ' (...)'; }
			$html .= '<div class="upd_listitem">
						<table width="100%" cellpadding="2" cellspacing="0"><tr>
							<td valign="top" width="16">
								<img src="images/updates/comment.gif" border="0"/>
							</td>
							<td valign="top">
								<div><a href="'.$item['target_link'].'#c'.$item['id'].'">'.$item['target_title'].'</a>:</div>
								<div style="color:silver">'.$item['fpubdate'].'</div>
								<div>'.$text.'</div>
							</td>
						</tr></table>
					  </div>';
		}
	}

	if ($html == '') { $html = '<div>�� ����� ��� ���������� ��� ������.</div>'; }
	return $html;
}

function cpWhoOnline(){
    $inCore = cmsCore::getInstance();
	$people = cmsUser::getOnlineCount();
	
	$html .= '<div>';
	
		$html .= '<table width="100%" cellpadding="2" cellspacing="2"><tr>';
			
			$html .= '<td width="24" valign="top">';
				$html .= '<img src="images/user.gif"/>';
			$html .= '</td>';
			
			$html .= '<td width="120" valign="top">';
				$html .= '<div><strong>�������������: </strong>'.$people['users'].'</div>';
				$html .= '<div><strong>������: </strong>'.$people['guests'].'</div>';	
			$html .= '</td>';
		
		include $_SERVER['DOCUMENT_ROOT'].'/includes/config.inc.php';
		
		if ($_CFG['stats']){
			$html .= '<td width="24" valign="top">';
				$html .= '<img src="images/on.gif"/>';
			$html .= '</td>';
			
			$html .= '<td width="" valign="top">';
				$html .= '<div style="color:#00BB00">���� ���������� �������</div>';
				$html .= '<div><a href="index.php?view=components&do=config&id=13">�������� ����������</a></div>';	
			$html .= '</td>';		
		} else {
			$html .= '<td width="24" valign="top">';
				$html .= '<img src="images/off.gif"/>';
			$html .= '</td>';
			
			$html .= '<td width="" valign="top">';
				$html .= '<div style="color:#BB0000">���� ���������� ��������</div>';
				$html .= '<div><a href="index.php?view=config">�������� ���������</a></div>';	
			$html .= '</td>';			
		}
	
		$html .= '</tr></table>';
	$html .= '</div>';
	
	return $html;

}

/////////////////////////// PAGE GENERATION ////////////////////////////////////////////////////////////////
function cpHead(){
	if ($GLOBALS['cp_page_title']){
		echo '<title>'.$GLOBALS['cp_page_title'].' - ������ ���������� InstantCMS</title>';
	} else {
		echo '<title>������ ���������� InstantCMS</title>';	
	}
	
	echo '<script language="JavaScript" type="text/javascript" src="js/common.js"></script>' ."\n";	
	echo '<script language="JavaScript" type="text/javascript" src="/includes/jquery/jquery.js"></script>' ."\n";	


	foreach($GLOBALS['cp_page_head'] as $key=>$value) { 
		echo $GLOBALS['cp_page_head'][$key] ."\n"; 
		unset ($GLOBALS['cp_page_head'][$key]);
	}
	
	return;
}

function cpGenerateMenu(){
	$GLOBALS['mainmenu'][0]['title'] = '����'; 
	$GLOBALS['mainmenu'][0]['link'] = 'index.php?view=menu'; 
	$GLOBALS['mainmenu'][0]['img'] = 'menu.gif';
	$GLOBALS['mainmenu'][0]['view'] = 'menu'; 

	$GLOBALS['mainmenu'][1]['title'] = '������'; 
	$GLOBALS['mainmenu'][1]['link'] = 'index.php?view=modules'; 
	$GLOBALS['mainmenu'][1]['img'] = 'modules.gif';
	$GLOBALS['mainmenu'][1]['view'] = 'modules'; 

	$GLOBALS['mainmenu'][2]['title'] = '�������'; 
	$GLOBALS['mainmenu'][2]['link'] = 'index.php?view=cats'; 
	$GLOBALS['mainmenu'][2]['img'] = 'category.gif';
	$GLOBALS['mainmenu'][2]['view'] = 'cats'; 
	
	$GLOBALS['mainmenu'][3]['title'] = '������'; 
	$GLOBALS['mainmenu'][3]['link'] = 'index.php?view=content'; 
	$GLOBALS['mainmenu'][3]['img'] = 'content.gif';
	$GLOBALS['mainmenu'][3]['view'] = 'content'; 

	$GLOBALS['mainmenu'][4]['title'] = '����������'; 
	$GLOBALS['mainmenu'][4]['link'] = 'index.php?view=components'; 
	$GLOBALS['mainmenu'][4]['img'] = 'components.gif';
	$GLOBALS['mainmenu'][4]['view'] = 'components'; 

	$GLOBALS['mainmenu'][5]['title'] = '�������';
	$GLOBALS['mainmenu'][5]['link'] = 'index.php?view=filters'; 
	$GLOBALS['mainmenu'][5]['img'] = 'filters.gif';
	$GLOBALS['mainmenu'][5]['view'] = 'filters'; 

	$GLOBALS['mainmenu'][6]['title'] = '������������'; 
	$GLOBALS['mainmenu'][6]['link'] = 'index.php?view=users'; 
	$GLOBALS['mainmenu'][6]['img'] = 'users.gif';
	$GLOBALS['mainmenu'][6]['view'] = 'users'; 

	$GLOBALS['mainmenu'][7]['title'] = '���������'; 
	$GLOBALS['mainmenu'][7]['link'] = 'index.php?view=config'; 
	$GLOBALS['mainmenu'][7]['img'] = 'config.gif';
	$GLOBALS['mainmenu'][7]['view'] = 'config'; 

	$GLOBALS['mainmenu'][8]['title'] = '���� &rarr;'; 
	$GLOBALS['mainmenu'][8]['link'] = '/'; 
	$GLOBALS['mainmenu'][8]['img'] = 'site.gif';
	$GLOBALS['mainmenu'][8]['target'] = '_blank';
	$GLOBALS['mainmenu'][8]['view'] = 'site';
}

function cpMenu(){
    $inCore = cmsCore::getInstance();
	global $adminAccess;
	ob_start(); ?>
	<div id="hmenu">
		<ul id="nav">
			<?php if ($inCore->isAdminCan('admin/menu', $adminAccess)){ ?>
			<li>
				<a href="index.php?view=menu" class="menu">����</a>
				<ul>
					<li><a class="add" href="index.php?view=menu&do=add">������� �����</a></li>
					<li><a class="add" href="index.php?view=menu&do=addmenu">������� ����</a></li>
					<li><a class="list" href="index.php?view=menu">�������� ���</a></li>
				</ul>
			</li>
			<?php } ?>
			<?php if ($inCore->isAdminCan('admin/modules', $adminAccess)){ ?>
			<li>
				<a href="index.php?view=modules" class="modules">������</a>
				<ul>
					<li><a class="add" href="index.php?view=modules&do=add">������� ������</a></li>
					<li><a class="list" href="index.php?view=modules">�������� ���</a></li>
				</ul>
			</li>
			<?php } ?>
			<?php if ($inCore->isAdminCan('admin/content', $adminAccess)){ ?>
			<li>
				<a class="cats" href="index.php?view=tree">�������</a>
				<ul>
					<li><a class="cats" href="index.php?view=cats">�������</a></li>
					<li><a class="content" href="index.php?view=content">������ / ��������</a></li>
					<li><a class="arhive" href="index.php?view=arhive">����� ������</a></li>
					<li><a class="add" href="index.php?view=cats&do=add">������� ������</a></li>
					<li><a class="add" href="index.php?view=content&do=add">������� ������</a></li>
				</ul>
			</li>
			<?php } ?>
			<?php if ($inCore->isAdminCan('admin/components', $adminAccess)){ ?>
			<li>
				<a href="index.php?view=components" class="components">����������</a>
				<ul>
                    <?php

                        $inDB = cmsDatabase::getInstance();

                        $components_sql = "SELECT *
                                           FROM cms_components
                                           ORDER BY title";

                        $result = $inDB->query($components_sql);

                        $showed_count   = 0;
                        $total_count    = $inDB->num_rows($result);

                        if ($total_count){

                            while ($com = $inDB->fetch_assoc($result)){

                                if ($com['published'] && $inCore->isAdminCan('admin/com_'.$com['link'], $adminAccess)){ ?>

                                    <li>
                                        <a style="margin-left:5px; background:url(/admin/images/components/<?php echo $com['link']; ?>.png) no-repeat 6px 6px;" href="index.php?view=components&do=config&link=<?php echo $com['link']; ?>">
                                            <?php echo $com['title']; ?>
                                        </a>
                                    </li>

                                <?php

                                    $showed_count++;

                                }

                            }

                        }

                        if ($total_count != $showed_count){

                    ?>
                        <li><a class="list" href="index.php?view=components">�������� ���...</a></li>
                    <?php

                        }

                    ?>

				</ul>
			</li>
			<?php } ?>
			<?php if ($inCore->isAdminCan('admin/plugins', $adminAccess)){ ?>
			<li>
				<a class="plugins">����������</a>
				<ul>
                    <li><a href="index.php?view=plugins" class="plugins">�������</a></li>
                    <?php if ($inCore->isAdminCan('admin/filters', $adminAccess)){ ?>
                        <li><a href="index.php?view=filters" class="filters">�������</a></li>
                    <?php } ?>
				</ul>
			</li>
			<?php } ?>
			<?php if ($inCore->isAdminCan('admin/users', $adminAccess)){ ?>
			<li>
                <a href="index.php?view=users" class="users">������������</a>
                <ul>
                    <li><a href="index.php?view=users" class="users">������������</a></li>
                    <li><a class="users" href="index.php?view=usergroups">������</a></li>
                    <li><a class="add" href="index.php?view=users&do=add">������� ������������</a></li>
                    <li><a class="add" href="index.php?view=usergroups&do=add">������� ������</a></li>
                    <li><a class="config" href="index.php?view=components&do=config&link=users">��������� ��������</a></li>
                </ul>
			</li>			
			<?php } ?>
			<?php if ($inCore->isAdminCan('admin/config', $adminAccess)){ ?>	
			<li>
				<a href="index.php?view=config" class="config">���������</a>			
				<ul>
					<li><a class="backup" href="index.php?view=backup">��������� ����� ��</a></li>
					<!-- <li><a class="repair" href="index.php?view=repair">�������� ��</a></li> -->
					<li><a class="repairnested" href="index.php?view=repairnested">�������� ��������</a></li>
				</ul>	
			</li>		
			<?php } ?>					
			<li>
				<a href="http://www.instantcms.ru/wiki" target="_blank" class="help">������������</a>
			</li>
		</ul>
	</div>
	
	<?php echo ob_get_clean();

	return;
}

function cpToolMenu($toolmenu){

	if (sizeof($toolmenu)>0){
		echo '<table width="100%" cellpadding="2" border="0" class="toolmenu" style="margin:0px"><tr><td>';
		foreach($toolmenu as $key => $value){			
			$icon = $toolmenu[$key]['icon'];
			$link = $toolmenu[$key]['link'];
			$title = $toolmenu[$key]['title'];
			echo '<a class="toolmenuitem" href="'.$link.'" title="'.$title.'"><img src="images/toolmenu/'.$icon.'" border="0" /></a>';
		}
		echo '</td></tr></table>';
	}

	return;
}

function cpProceedBody(){
	
	ob_start();
	
	$link = str_replace('/', '', $GLOBALS['applet']);
	$link = str_replace(':', '', $link);
	$link = str_replace('-', '', $link);
	$file = $link . '.php';
	include('applets/'.$file);
	eval('applet_'.$link.'();');
	
	$GLOBALS['cp_page_body'] = ob_get_clean();
	
}

function cpBody(){
	echo $GLOBALS['cp_page_body'];
	return;
}

//////////////////////////////////////////////// PATHWAY ///////////////////////////////////////////////////////
function cpPathway($separator='&raquo;'){

	echo '<div class="pathway">';
	foreach($GLOBALS['cp_pathway'] as $key => $value){
	
		echo '<a href="'.$GLOBALS['cp_pathway'][$key]['link'].'" class="pathwaylink">'.$GLOBALS['cp_pathway'][$key]['title'].'</a> ';
		
		if ($key<sizeof($GLOBALS['cp_pathway'])-1) {
			echo ' '.$separator.' ';
		}
	
	}
	echo '</div>';

}

function cpAddPathway($title, $link){
	$already = false;
	
	foreach($GLOBALS['cp_pathway'] as $key => $val){
	 if ($GLOBALS['cp_pathway'][$key]['title'] == $title || $GLOBALS['cp_pathway'][$key]['link'] == $link){
	 	$already = true;
	 }
	}
	
	if(!$already){
		$next = sizeof($GLOBALS['cp_pathway']);
		$GLOBALS['cp_pathway'][$next]['title'] = $title;
		$GLOBALS['cp_pathway'][$next]['link'] = $link;
	}

	return true;
}

function cpModulePositions($template){
	
	$pos = array();
	
	$posfile = $_SERVER['DOCUMENT_ROOT'].'/templates/'.$template.'/positions.txt';
	
	if(file_exists($posfile)){
		$file = fopen($posfile, 'r');
		while(!feof($file)){
			$str = fgets($file);
			$str = str_replace("\n", '', $str);
			$str = str_replace("\r", '', $str);
			if (!strstr($str, '#') && strlen($str)>1){
				$pos[] = $str;
			}
		}
		fclose($file);
		return $pos;
	} else {
		return false;
	}
	
}

function cpAddParam($query, $param, $value){
	$new_query = '';
	parse_str($query, $params);
	$l = 0; $added= false;
	foreach($params as $key => $val){
		$l ++;
		if ($key != $param && $key!='nofilter'){ $new_query .= $key .'='.$val; } else {	$new_query .= $key .'='.$value; $added = true;	}
		if ($l<sizeof($params)) { $new_query .= '&'; }
	}	
	if (!$added) {  
		if (strlen($new_query)>1){ $new_query .= '&'.$param . '=' . $value; } else {$new_query .= $param . '=' . $value; }
	}	
	return $new_query;
}

function cpListTable($table, $_fields, $_actions, $where='', $orderby='title'){

	$perpage = 50;

	$sql = 'SELECT *';
	$is_actions = sizeof($_actions);
	
	foreach($_fields as $key => $value){
		if (isset($_fields[$key]['fdate'])){
			$sql .= ", DATE_FORMAT(".$_fields[$key]['field'].", '".$_fields[$key]['fdate']."') as `".$_fields[$key]['field']."`" ;
		}
	}
	
	$sql .= ' FROM '.$table;
	
	if (isset($_REQUEST['nofilter'])){
		unset($_SESSION['filter']);
		header('Location:index.php?'.str_replace('&nofilter', '', $_SERVER['QUERY_STRING']));
	}
	
	$filter = false;
	
	if (isset($_REQUEST['filter'])) { 
		$filter = $_REQUEST['filter'];
		$_SESSION['filter'] = $filter;
	}
	
	if ($filter){
		$f = 0;
		$sql .= ' WHERE ';
		foreach($filter as $key => $value){
			if($filter[$key]!=-100){
				$f++;
				if ($f > 1){
					$sql .= ' AND ';
				}
				if ($key != 'category_id'){
				$sql .= $key . " LIKE '%" . $filter[$key] . "%'";
				} else {
					$sql .= $key . " = '" . $filter[$key] . "'";
				}
			}				
		}
		if (!isset($_SESSION['filter'])) { $_SESSION['filter'] = $filter; }
	}
	
	if (strlen($where)>3) {	
		if (strstr($sql, 'WHERE')){ $sql .= ' AND '.$where; }
		else { $sql .= ' WHERE '.$where; }
	}
	
	if (isset($_REQUEST['sort'])) { $sort = $_REQUEST['sort']; } else { $sort = false; }
	
	if ($sort == false){
		if ($orderby) { $sort = $orderby; } else {
			foreach($_fields as $key => $value){
				if ($_fields[$key]['field'] == 'ordering' && $sort!='NSLeft'){ $sort = 'ordering'; $so = 'asc';}
			}	
		}
	}
	
	if ($sort) { 	
		$sql .= ' ORDER BY '.$sort; 
		if (isset($_REQUEST['so'])) { $sql .= ' '. $_REQUEST['so']; }
	}
	
	if (isset($_REQUEST['page'])) { 
		$page = abs((int)$_REQUEST['page']); 
	} else { $page = 1; }
	
	$total_rs = dbQuery($sql);
	$total = mysql_num_rows($total_rs);
	
	$sql .= " LIMIT ".($page-1)*$perpage.", $perpage";
	
	$result = dbQuery($sql);
	
	if (mysql_error()) { 
		unset($_SESSION['filter']);
		header('Location:index.php?'.$_SERVER['QUERY_STRING']);
	}

	$filters = 0; $f_html = '';
	//Find and render filters
	foreach($_fields as $key => $value){
		 if (isset($_fields[$key]['filter'])){
				$f_html .= '<td width="90">'.$_fields[$key]['title'].': </td>';
				if(!isset($filter[$_fields[$key]['field']])) { $initval = ''; }
				else { $initval =  $filter[$_fields[$key]['field']]; }
				$f_html .= '<td width="">';
					$inputname = 'filter['.$_fields[$key]['field'].']';
					if(!isset($_fields[$key]['filterlist'])){
						$f_html .= '<input name="'.$inputname.'" type="text" size="'.$_fields[$key]['filter'].'" class="filter_input" value="'.$initval.'"/></td>';
					} else {
						$f_html .= cpBuildList($inputname, $_fields[$key]['filterlist'], $initval);
					}
				$f_html .= '</td>';					
				$filters += 1;
		 }
	}
	//draw filters
	if ($filters>0){
		echo '<div class="filter">';
		echo '<form name="filterform" action="index.php?'.$_SERVER['QUERY_STRING'].'" method="POST">';
		echo '<table width="250"><tr>';
		echo $f_html;		
		echo '<td width="80"><input type="submit" class="filter_submit" value="������" /></td>';			
		if (@$f>0){
			echo '<td width="80"><input type="button" onclick="window.location.href=\'index.php?'.$_SERVER['QUERY_STRING'].'&nofilter\'" class="filter_submit" value="���" /></td>';					
		}
		echo '</tr></table>';
		echo '</form>';
		echo '</div>';
	}

	if (mysql_num_rows($result)){

		//DRAW LIST TABLE	
		echo '<form name="selform" action="index.php?view='.$GLOBALS['applet'].'&do=saveorder" method="post">';
		echo '<table id="listTable" border="0" class="tablesorter" width="100%" cellpadding="0" cellspacing="0">';
			//TABLE HEADING
			echo '<thead>'."\n";
				echo '<tr>'."\n";		
					echo '<th width="20" class="lt_header" align="center"><a class="lt_header_link" href="javascript:invert();" title="������������� ���������">#</a></th>'. "\n";		
					foreach($_fields as $key => $value){		
						echo '<th width="'.$_fields[$key]['width'].'" class="lt_header">';
							echo $_fields[$key]['title'];
						echo '</th>'. "\n";		
					}		
					if ($is_actions){
						echo '<th width="80" class="lt_header" align="center">��������</th>'. "\n";
					}
				echo '</tr>'."\n";
			echo '</thead><tbody>'."\n";
			//TABLE BODY
			$r = 0;
			while ($item = mysql_fetch_assoc($result)){			
				$r++;
				if ($r % 2) { $row_class = 'lt_row1'; } else { $row_class = 'lt_row2'; }
				echo '<tr id="lt_row2">'."\n";		
					echo '<td class="'.$row_class.'" align="center" valign="middle"><input type="checkbox" name="item[]" value="'.$item['id'].'" /></td>'. "\n";
					foreach($_fields as $key => $value){								
						if (isset($_fields[$key]['link'])){
							 $link = str_replace('%id%', $item['id'], $_fields[$key]['link']); 
							 $data = $item[$_fields[$key]['field']];
							 
							 if (isset($_fields[$key]['maxlen'])){
								if (strlen($data)>$_fields[$key]['maxlen']){
									$data = substr($data, 0, $_fields[$key]['maxlen']).'...';
								}
							 }
							 //nested sets otstup
							if (isset($item['NSLevel']) && $_fields[$key]['field']=='title'){ 
								$otstup = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', ($item['NSLevel']-1)); 
								if ($item['NSLevel']-1 > 0){ $otstup .=  ' &raquo; '; }
							} else { $otstup = ''; }
							 echo '<td class="'.$row_class.'" valign="middle">'.$otstup.'<a class="lt_link" href="'.$link.'">'.$data.'</a></td>'. "\n";
						} else {						
							if ($_fields[$key]['field'] != 'ordering'){
								if ($_fields[$key]['field'] == 'published'){
									if (isset($_fields[$key]['do'])) { $do = $_fields[$key]['do']; } else { $do = 'do'; }
									if (isset($_fields[$key]['do_suffix'])) { $dos = $_fields[$key]['do_suffix']; $ids = 'item_id'; } else { $dos = ''; $ids = 'id'; }
									if ($item['published']){
										$qs = cpAddParam($_SERVER['QUERY_STRING'], $do, 'hide'.$dos);
										$qs = cpAddParam($qs, $ids, $item['id']);										
											$qs2 = cpAddParam($_SERVER['QUERY_STRING'], $do, 'show'.$dos);
											$qs2 = cpAddParam($qs2, $ids, $item['id']);										
										$qs = "pub(".$item['id'].", '".$qs."', '".$qs2."', 'off', 'on');";
										echo '<td class="'.$row_class.'" valign="middle">
												<a title="������" id="publink'.$item['id'].'" href="javascript:'.$qs.'"><img id="pub'.$item['id'].'" src="images/actions/on.gif" border="0"/></a>
											 </td>'. "\n";
									} else {
										$qs = cpAddParam($_SERVER['QUERY_STRING'], $do, 'show'.$dos);
										$qs = cpAddParam($qs, $ids, $item['id']);
											$qs2 = cpAddParam($_SERVER['QUERY_STRING'], $do, 'hide'.$dos);
											$qs2 = cpAddParam($qs2, $ids, $item['id']);																				
										$qs = "pub(".$item['id'].", '".$qs."', '".$qs2."', 'on', 'off');";
										echo '<td class="'.$row_class.'" valign="middle">
												<a title="��������" id="publink'.$item['id'].'" href="javascript:'.$qs.'"><img id="pub'.$item['id'].'" src="images/actions/off.gif" border="0"/></a>
											 </td>'. "\n";								
									}
								} else {
											if (isset($_fields[$key]['prc'])) {
												//field processor
												$data = $_fields[$key]['prc']($item[$_fields[$key]['field']]);
											} else {
												$data = $item[$_fields[$key]['field']];
												 if (isset($_fields[$key]['maxlen'])){
													if (strlen($data)>$_fields[$key]['maxlen']){
														$data = substr($data, 0, $_fields[$key]['maxlen']).'...';
													}
												 }
											}
											 //nested sets otstup
											if (isset($item['NSLevel']) && $_fields[$key]['field']=='title'){ 
												$otstup = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', ($item['NSLevel']-1)); 
												if ($item['NSLevel']-1 > 0){ $otstup .=  ' &raquo; '; }
											} else { $otstup = ''; }
											echo '<td class="'.$row_class.'" valign="middle">'.$otstup.$data.'</td>'. "\n";
									   }
							} else {
								echo '<td class="'.$row_class.'" valign="middle">
									<a title="����" href="?view='.$GLOBALS['applet'].'&do=move_down&co='.$item[$_fields[$key]['field']].'&id='.$item['id'].'"><img src="images/actions/down.gif" border="0"/></a>';
									if ($table != 'cms_menu' && $table != 'cms_category'){
										echo '<input class="lt_input" type="text" size="4" name="ordering[]" value="'.$item['ordering'].'" />';
										echo '<input name="ids[]" type="hidden" value="'.$item['id'].'" />';
									} else {
										echo '<input class="lt_input" type="text" size="4" name="ordering[]" value="'.$item['ordering'].'" disabled/>';
									}
									echo '<a title="�����" href="?view='.$GLOBALS['applet'].'&do=move_up&co='.$item[$_fields[$key]['field']].'&id='.$item['id'].'""><img src="images/actions/top.gif" border="0"/></a>'.
								'</td>'. "\n";
							}
						}
					}		
					if ($is_actions){
						echo '<td width="80" class="'.$row_class.'" align="right" valign="middle"><div style="padding-right:8px">';
						foreach($_actions as $key => $value){
							if (isset($_actions[$key]['condition'])){
								$print = $_actions[$key]['condition']($item['id']);
							} else { $print = true; }
							if ($print){
								$icon   = $_actions[$key]['icon'];
								$title  = $_actions[$key]['title'];
                                $link   = $_actions[$key]['link'];

                                foreach($item as $f=>$v){
                                    $link = str_replace('%'.$f.'%', $v, $link);
                                }
								
								if (!isset($_actions[$key]['confirm'])){
									echo '<a href="'.$link.'" title="'.$title.'"><img hspace="2" src="images/actions/'.$icon.'" border="0" alt="'.$title.'"/></a>';
								} else {
									echo '<a href="#" onclick="jsmsg(\''.$_actions[$key]['confirm'].'\', \''.$link.'\')" title="'.$title.'"><img hspace="2" src="images/actions/'.$icon.'" border="0" alt="'.$title.'"/></a>';
								}
							}
						}
						echo '</div></td>'. "\n";						
					}					
				echo '</tr>'."\n";			
			}
			
		echo '</tbody></table></form>';
		
		echo '<script type="text/javascript">highlightTableRows("listTable","hoverRow","clickedRow");</script>';
		echo '<script type="text/javascript">activateListTable("listTable");</script>';
			
		$link = '?view='.$GLOBALS['applet'];		

		if ($sort){
			$link .= '&sort='.$sort; 
			if (isset($_REQUEST['so'])) { $link .= '&so='.$_REQUEST['so']; }
		}
		
		cpPageLinks($total, $perpage, $page, $link);		
	
	} else {	
		echo '<p class="cp_message">��� �������� ��� �����������.</p>';
	}
}

////////////////////////////////////////////////////// PAGINATION /////////////////////////////////////////////////////////////////////
function cpPageLinks($records, $perpage, $current, $first_url=''){
	if ($records){
		$pages = ceil($records / $perpage);
			echo '<div style="margin-top:10px; margin-bottom: 15px; font-size:10px">';
			echo '<span style="margin:5px; padding:4px"><strong>��������: </strong></span>';	
			for ($p=1; $p<=$pages; $p++){
				if ($p != $current) {			
					$qs = $_SERVER['QUERY_STRING'];
					$qs = cpAddParam($qs, 'page', $p);
					$link = $_SERVER['PHP_SELF'].'?'.$qs;
					echo ' <a href="'.$link.'" style="margin:5px; padding:5px">'.$p.'</a> ';		
				} else {
					echo '<span style="margin:5px; padding:5px; border:solid 1px silver; background-color: white">'.$p.'</span>';
				}
			}
			$from = (($current-1) * $perpage) + 1;
			$to = $from+$perpage - 1;
			if ($records < $to) { $to = $records; }
			echo '<span style="margin:5px; padding:4px">�������� '.$from.'-'.$to.' �� '.$records.'</span>';	
			echo '</div>';
	}
}

//////////////////////////////////////// LIST TABLE PROCESSORS ///////////////////////////////////////////////////////////////////
function cpCommentAuthor($comment_id){
	$sql = "SELECT user_id, guestname
			FROM cms_comments
			WHERE id = $comment_id";
	$result = dbQuery($sql);
	$mod = mysql_fetch_assoc($result);
	
	if($mod['user_id']==0) { $author = $mod['guestname']; }
	else {
		$usersql = "SELECT * FROM cms_users WHERE id = ".$mod['user_id'];
		$userres = dbQuery($usersql);
		$u = mysql_fetch_assoc($userres);
		$author = $u['nickname'].' (<a href="/admin/users.php?do=edit_user&id='.$u['id'].'">'.$u['login'].'</a>)';
	}
	
	return $author;
}

function cpCommentTarget($comment_id){
    $inCore = cmsCore::getInstance();
	$sql = "SELECT target_title, target_link
			FROM cms_comments
			WHERE id = $comment_id";
	$result = dbQuery($sql) ;
	$mod = mysql_fetch_assoc($result);

	$target = '<a target="_blank" href="'.$mod['target_link'].'#c'.$comment_id.'">'.$mod['target_title'].'</a>';
	return $target;
}

function cpCommentIsNew($is_new){

	if ($is_new) { return '<img src="images/new.png" border="0" />'; } else { return "&nbsp;"; }

}

function cpForumCatById($id){

	$result = dbQuery("SELECT title FROM cms_forum_cats WHERE id = $id") ;
	
	if (mysql_num_rows($result)) { 
		$cat = mysql_fetch_assoc($result);		
		return '<a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$id.'">'.$cat['title'].'</a> ('.$id.')';
	} else { return '--'; }

}

function cpPriceCatById($id){

	$result = dbQuery("SELECT title FROM cms_price_cats WHERE id = $id") ;
	
	if (mysql_num_rows($result)) { 
		$cat = mysql_fetch_assoc($result);		
		return '<a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$id.'">'.$cat['title'].'</a> ('.$id.')';
	} else { return '--'; }

}

function cpFaqCatById($id){

	$result = dbQuery("SELECT title FROM cms_faq_cats WHERE id = $id") ;
	
	if (mysql_num_rows($result)) { 
		$cat = mysql_fetch_assoc($result);		
		return '<a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$id.'">'.$cat['title'].'</a>';
	} else { return '--'; }

}


function cpCatalogCatById($id){

	$result = dbQuery("SELECT title, parent_id FROM cms_uc_cats WHERE id = $id") ;
	
	if (mysql_num_rows($result)) { 
		$cat = mysql_fetch_assoc($result);
        if ($cat['parent_id']){
            return '<a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$id.'">'.$cat['title'].'</a> ('.$id.')';
        } else {
            return $cat['title'];
        }
	} else { return '--'; }

}

function cpPhotoAlbumById($id){

	$result = dbQuery("SELECT title FROM cms_photo_albums WHERE id = $id") ;
	
	if (mysql_num_rows($result)) { 
		$cat = mysql_fetch_assoc($result);		
		return '<a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_album&item_id='.$id.'">'.$cat['title'].'</a> ('.$id.')';
	} else { return '--'; }

}

function cpBoardCatById($id){

	$result = dbQuery("SELECT title FROM cms_board_cats WHERE id = $id") ;
	
	if (mysql_num_rows($result)) { 
		$cat = mysql_fetch_assoc($result);		
		return '<a href="index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=edit_cat&item_id='.$id.'">'.$cat['title'].'</a> ('.$id.')';
	} else { return '--'; }

}


function cpGroupById($id){

	$result = dbQuery("SELECT title FROM cms_user_groups WHERE id = $id") ;
	
	if (mysql_num_rows($result)) { 
		$cat = mysql_fetch_assoc($result);		
		return '<a href="index.php?view=usergroups&do=edit&id='.$id.'">'.$cat['title'].'</a> ('.$id.')';
	} else { return '--'; }

}

function cpCatById($id){

	$result = dbQuery("SELECT title, parent_id FROM cms_category WHERE id = $id") ;
	
	if (mysql_num_rows($result)) {
		$cat = mysql_fetch_assoc($result);
        if ($cat['parent_id']){
            return '<a href="index.php?view=cats&do=edit&id='.$id.'">'.$cat['title'].'</a> ('.$id.')';
        } else {
            return $cat['title'];
        }
	} else { return '--'; }

}

function cpComponentById($id){
	$sql = "SELECT link FROM cms_components WHERE id = $id";
	$result = dbQuery($sql);
	
	if (mysql_num_rows($result)) { $mod = mysql_fetch_assoc($result); return $mod['link']; }
	else { return false; }
}

function cpModuleById($id){
	$sql = "SELECT content FROM cms_modules WHERE id = $id AND is_external = 1";
	$result = dbQuery($sql);
	if (mysql_num_rows($result)) { $mod = mysql_fetch_assoc($result); return $mod['content']; }
	else { return false; }
}

function cpModuleTitleById($id){
	$sql = "SELECT name FROM cms_modules WHERE id = $id";
	$result = dbQuery($sql);
	if (mysql_num_rows($result)) { $mod = mysql_fetch_assoc($result); return $mod['name']; }
	else { return false; }
}

function cpTemplateById($template_id){

	if ($template_id) { return $template_id; } else { return '<span style="color:silver">��� �� �����</span>'; }

}

function cpModuleHasConfig($id){

	$mod = cpModuleById($id);
	
	if ($mod) {
		$file = 'modules/'.$mod.'/backend.php';
		if (file_exists($file)){ return true; }
		$file = 'modules/'.$mod.'/backend.xml';
		if (file_exists($file)){ return true; }
	}
	
	return false;
    
}

function cpUserNick($user_id=0){
	if ($user_id){
		$sql = "SELECT nickname FROM cms_users WHERE id = $user_id";
		$result = dbQuery($sql);
		if (mysql_num_rows($result)) { $usr = mysql_fetch_assoc($result); return $usr['nickname']; }
		else { return false; }
	} else {
		return '<em style="color:gray">�� ���������</em>';
	}
}

function cpYesNo($option){
	if ($option) { return '��'; } else { return '���'; } 
}

//////////////////////////////////////////////// DATABASE //////////////////////////////////////////////////////////
function dbMoveUp($table, $id, $current_ord){
	$sql = "UPDATE $table SET ordering = ordering + 1 WHERE ordering = ($current_ord-1) LIMIT 1";
	dbQuery($sql) ;
	$sql = "UPDATE $table SET ordering = ordering - 1 WHERE id = $id LIMIT 1";
	dbQuery($sql) ;
}
function dbMoveDown($table, $id, $current_ord){
	$sql = "UPDATE $table SET ordering = ordering - 1 WHERE ordering = ($current_ord+1) LIMIT 1";
	dbQuery($sql) ;
	$sql = "UPDATE $table SET ordering = ordering + 1 WHERE id = $id LIMIT 1";
	dbQuery($sql) ;
}

function dbShow($table, $id){
	$sql = "UPDATE $table SET published = 1 WHERE id = $id";
	dbQuery($sql) ;
}
function dbShowList($table, $list){
	if (is_array($list)){
		$sql = "UPDATE $table SET published = 1 WHERE ";
		$item = 0;
		foreach($list as $key => $value){
			$item ++;
			$sql .= 'id = '.$value;
			if ($item<sizeof($list)) { $sql .= ' OR '; }
		}
		$sql .= ' LIMIT '.sizeof($list);
		dbQuery($sql) ;
	}
}

function dbHide($table, $id){
	$sql = "UPDATE $table SET published = 0 WHERE id = $id";
	dbQuery($sql) ;
}
function dbHideList($table, $list){
	if (is_array($list)){
		$sql = "UPDATE $table SET published = 0 WHERE ";
		$item = 0;
		foreach($list as $key => $value){
			$item ++;
			$sql .= 'id = '.$value;
			if ($item<sizeof($list)) { $sql .= ' OR '; }
		}
		$sql .= ' LIMIT '.sizeof($list);
		dbQuery($sql) ;
	}
}

function dbDelete($table, $id){
    $inCore = cmsCore::getInstance();
	$sql = "DELETE FROM $table WHERE id = $id LIMIT 1";
	dbQuery($sql) ;
	if ($table=='cms_content'){
		cmsClearTags('content', $id);
        $inCore->deleteRatings('content', $id);
        $inCore->deleteComments('article', $id);
		dbQuery("DELETE FROM cms_tags WHERE target='content' AND item_id=$id");
	}	
	if ($table=='cms_modules'){
		dbQuery("DELETE FROM cms_modules_bind WHERE module_id=$id");
	}
}
function dbDeleteList($table, $list){
	if (is_array($list)){
		$sql = "DELETE FROM $table WHERE ";
		$item = 0;
		foreach($list as $key => $value){
			$item ++;
			$sql .= 'id = '.$value;
			if ($item<sizeof($list)) { $sql .= ' OR '; }
			if ($table=='cms_content'){
				cmsClearTags('content', $value);
				dbQuery("DELETE FROM cms_comments WHERE target='article' AND target_id=$value");
				dbQuery("DELETE FROM cms_ratings WHERE target='content' AND item_id=$value");
				dbQuery("DELETE FROM cms_tags WHERE target='content' AND item_id=$value");
			}
			if ($table=='cms_modules'){
				dbQuery("DELETE FROM cms_modules_bind WHERE module_id=$value");
			}
		}
		$sql .= ' LIMIT '.sizeof($list);
		dbQuery($sql) ;
	}
}

///////////////////////////////////////////// HTML GENERATORS ////////////////////////////////////////////////
function insertPanel(){
    $inCore=cmsCore::getInstance();
    
    $submit_btn = '<input type="button" value="��������" style="width:100px" onClick="insertTag(document.addform.ins.options[document.addform.ins.selectedIndex].value)">';

echo '<table width="100%" border="0" cellspacing="0" cellpadding="8" class="proptable"><tr><td>';
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="2">';
	echo '<tr>';
		echo '<td width="120">';
			echo '<strong>��������:</strong> ';
		echo '</td>';
		echo '<td width="">';
			echo '<select name="ins" id="ins" style="width:99%" onChange="showIns()">
					<option value="material">������ �� ������</option>
					<option value="photo">������ �� ����������</option>
					<option value="album">������ �� ����������</option>
					<option value="price">������ �� ��������� ������</option>
					<option value="frm">����� ��� ��������</option>
					<option value="blank">����� ��� ���������</option>	
					<option value="include">������� ������</option>	
					<option value="filelink">������ "������� ����"</option>
					<option value="banpos">��������� �������</option>	
					<option value="pagebreak">-- ������ �������� --</option>
					<option value="pagetitle">-- ����� �������� --</option>
				  </select>';
		echo '</td>';
        echo '<td width="100">&nbsp;</td>';
	echo '</tr>';
	echo '<tr id="material">';
		echo '<td width="120">
                    <strong>������:</strong>
              </td>';
        echo '<td>
                    <select name="m" style="width:99%">'.$inCore->getListItems('cms_content').'</select>
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="photo">';
		echo '<td width="120">
                    <strong>����:</strong>
              </td>';
        echo '<td>
                    <select name="f" style="width:99%">'.$inCore->getListItems('cms_photo_files').'</select>
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="album">';
		echo '<td width="120">
                    <strong>������:</strong>
              </td>';
        echo '<td>
                    <select name="a" style="width:99%">'.$inCore->getListItemsNS('cms_photo_albums', 0, '',  '', 0, true).'</select>
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="price">';
		echo '<td width="120">
                    <strong>���������::</strong>
              </td>';
        echo '<td>
                    <select name="p" style="width:99%">'.$inCore->getListItems('cms_price_cats').'</select>
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="frm">';
		echo '<td width="120">
                    <strong>�����:</strong>
              </td>';
        echo '<td>
                    <select name="fm" style="width:99%">'.$inCore->getListItems('cms_forms').'</select>
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="blank">';
		echo '<td width="120">
                    <strong>�����:</strong>
              </td>';
        echo '<td>
                    <select name="b" style="width:99%">'.$inCore->getListItems('cms_forms').'</select>
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="include">';
		echo '<td width="120">
                    <strong>����:</strong>
              </td>';
        echo '<td>
                    /includes/myphp/<input name="i" type="text" value="myscript.php" />
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="filelink">';
		echo '<td width="120">
                    <strong>����:</strong>
              </td>';
        echo '<td>
                    <input name="fl" type="text" value="/files/myfile.rar" />
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="banpos">';
		echo '<td width="120">
                    <strong>�������:</strong>
              </td>';
        echo '<td>
                    <select name="ban" style="width:99%">'.$inCore->bannersList().'</select>
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="pagebreak">';
		echo '<td width="120">
                    <strong>���:</strong>
              </td>';
        echo '<td>
                    {pagebreak}
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';
	echo '<tr id="pagetitle">';
		echo '<td width="120">
                    <strong>���������:</strong>
              </td>';
        echo '<td>
                    <input type="text" name="ptitle" style="width:99%" />
              </td>';
        echo '<td width="100">'.$submit_btn.'</td>';
    echo '</tr>';


	echo '</table>';

   echo '</td></tr></table>';

   echo '<script type="text/javascript">showIns();</script>';
		
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function cpBuildList($attr_name, $list, $selected_id=false){
	$html = '';
	
	$html .= '<select name="'.$attr_name.'" id="'.$attr_name.'">' . "\n";
	
	$html .= '<option value="-100">-- ��� --</option>'."\n";
	
	foreach($list as $key=>$value){	
		if ($selected_id == $list[$key]['id']) { $sel = 'selected'; } else { $sel = ''; }
		$html .= '<option value="'.$list[$key]['id'].'" '.$sel.'>'.$list[$key]['title'].'</option>' . "\n";	
	}
	
	$html .= '</select>' . "\n";

	return $html;
}

function cpGetList($listtype){

	$list = array();	
	//Module positions
	if ($listtype == 'positions'){
		$list[0]['title'] = 'left';		$list[0]['id'] = 'left';
		$list[1]['title'] = 'right';	$list[1]['id'] = 'right';
		$list[2]['title'] = 'top';		$list[2]['id'] = 'top';
		$list[3]['title'] = 'bottom';	$list[3]['id'] = 'bottom';
		$list[4]['title'] = 'column1';	$list[4]['id'] = 'column1';
		$list[5]['title'] = 'column2';	$list[5]['id'] = 'column2';
		$list[6]['title'] = 'column3';	$list[6]['id'] = 'column3';
		return $list;
	}
	//Menu types
	if ($listtype == 'menu'){
		$list[0]['title'] = '������� ����';			$list[0]['id'] = 'mainmenu';
		$list[1]['title'] = '�������������� ���� 1';	$list[1]['id'] = 'menu1';
		$list[2]['title'] = '�������������� ���� 2';	$list[2]['id'] = 'menu2';
		$list[3]['title'] = '�������������� ���� 3';	$list[3]['id'] = 'menu3';
		$list[4]['title'] = '�������������� ���� 4';	$list[4]['id'] = 'menu4';
		$list[5]['title'] = '�������������� ���� 5';	$list[5]['id'] = 'menu5';						
		return $list;
	}

	//...or table records
	$sql = "SELECT id, title FROM $listtype ORDER BY title ASC";
	$result = dbQuery($sql) ;
	
	if (mysql_num_rows($result)>0) { 
		while($item = mysql_fetch_assoc($result)){
			$next = sizeof($list);
			$list[$next]['title'] = $item['title'];
			$list[$next]['id'] = $item['id'];
		}		
	}
	
	return $list;

}

function cpMenutypeById($id){
    $inDB   = cmsDatabase::getInstance();

	$html   = '';
	$maxlen = 35;
	
	$item   = $inDB->get_fields('cms_menu', 'id='.$id, 'linktype, linkid, link');

	switch($item['linktype']){	
		case 'link':  			$html = '<span id="menutype"><a href="'.$item['link'].'">C�����</a></span> - '.$item['linkid'];
								break;
		case 'component':		$html = '<span id="menutype"><a href="'.$item['link'].'">���������</a></span> - '.$inDB->get_field('cms_components', "link='".$item['linkid']."'", 'title');
					 			break;
		case 'content':			$html = '<span id="menutype"><a href="'.$item['link'].'">������</a></span> - '.$inDB->get_field('cms_content', 'id='.$item['linkid'], 'title');
					 			break;
		case 'category':		$html = '<span id="menutype"><a href="'.$item['link'].'">������</a></span> - '.$inDB->get_field('cms_category', 'id='.$item['linkid'], 'title');
					 			break;
		case 'pricecat':		$html = '<span id="menutype"><a href="'.$item['link'].'">�����</a></span> - '.$inDB->get_field('cms_price_cats', 'id='.$item['linkid'], 'title');
					 			break;
		case 'uccat':			$html = '<span id="menutype"><a href="'.$item['link'].'">�������</a></span> - '.$inDB->get_field('cms_uc_cats', 'id='.$item['linkid'], 'title');
					 			break;
		case 'blog':			$html = '<span id="menutype"><a href="'.$item['link'].'">����</a></span> - '.$inDB->get_field('cms_blogs', 'id='.$item['linkid'], 'title');
					 			break;
		case 'photoalbum':		$html = '<span id="menutype"><a href="'.$item['link'].'">����������</a></span> - '.$inDB->get_field('cms_photo_albums', 'id='.$item['linkid'], 'title');
					 			break;
	}	
	$clear = strip_tags($html);
	$r = strlen($html) - strlen($clear);
	if (strlen($clear)>$maxlen) { $html = substr($html, 0, $maxlen+$r).'...'; }
	return $html;
}

?>