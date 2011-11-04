<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
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

function getChart($data, $processor='', $rownums = true, $rowlines = false){
	//remove duplicates
	$olddata = $data;
	$data = array();
	foreach($olddata as $key=>$value){	
		$title = $olddata[$key]['title'];
		$found_same = false;
		foreach($data as $k=>$val){
			if(isset($val['title'])){
				if ($val['title']==$title){
					$val['cnt'] += $olddata[$key]['cnt'];
					$found_same = true;
					break;
				}
			}
		}
		if(!$found_same){
			$next = sizeof($data);
			$data[$next]['title'] = $olddata[$key]['title'];
			$data[$next]['cnt'] = $olddata[$key]['cnt'];
		}
	}
	
	//calculate max hits
	$total = 0;
	foreach($data as $key=>$value){	if($data[$key]['cnt']>$total) { $total = $data[$key]['cnt']; } }
	
	//draw table
	$html = '<table style="margin-top:10px" width="100%" cellpadding="3" cellspacing="0" border="0">';
	$row = 1;
	foreach($data as $key=>$value){	
		$html .= '<tr>';
		
			if ($row<sizeof($data) && $rowlines) { $style = 'border-bottom: solid 1px silver'; } else { $style = ''; } 
			
			if ($total<>0)
			{
			$percent = round(($data[$key]['cnt'] * 100)/$total);
			}
			else
			{
			$percent=0;	
			}

			if ($rownums) { $html .= '<td width="30" style="'.$style.'">'.$row.'.</td>'; }
			
			if ($data[$key]['title']){
				if (!$processor){
					$html .= '<td width="30%" style="'.$style.'">'.$data[$key]['title'].'</td>';
				} else {
					$title = $processor($data[$key]['title']);
					$html .= '<td width="30%" style="'.$style.'">'.$title.'</td>';
				}
			} else {
				$html .= '<td width="30%" style="'.$style.'">Не определен</td>';			
			}
			$html .= '<td style="'.$style.'">';
				$html .= '<div style="width:'.$percent.'%;float:left;padding:4px;height:15px;background-color:gray;color:white;font-size:10px;">';
					$html .= '<div style="float:right;padding-right:8px;font-weight:bold">'.$data[$key]['cnt'].'</div>';
				$html .= '</div>';
			$html .= '</td>';		
		
		$html .= '</tr>';	
		$row++;
	}
	$html .= '</table>';

	return $html;
}

function getLink($link){

	return '<a target="_blank" href="'.$link.'">'.$link.'</a>';

}

function getAgent($agent){
	$bot = array();
	//SEARCH ENGINES USER-AGENTS
	$bot['Aport']='Aport';
	$bot['msnbot']='MSNbot';
	$bot['Yandex']='Yandex';
	$bot['Lycos.com']='Lucos';
	$bot['Googlebot']='Google';
	$bot['Openbot']='Openfind';
	$bot['FAST-WebCrawler']='AllTheWeb';
	$bot['TurtleScanner']='TurtleScanner';
	$bot['Yahoo-MMCrawler']='Y!MMCrawler';
	$bot['Yahoo!']='Yahoo!';
	$bot['rambler']='Rambler';
	$bot['W3C_Validator']='W3C Validator';
	//BROWSERS
	$bot['MSIE 6.0']='MS Internet Explorer 6.0';
	$bot['MSIE 7.0']='MS Internet Explorer 7.0';
	$bot['Opera']='Opera';
	
	$back = $agent;
	
	foreach($bot as $id => $title){
	
		if (strstr($agent, $id)){
			$back = $title;
		}
	
	}				  
	
	return $back;

}

	cpAddPathway('Статистика сайта', '?view=components&do=config&id='.$_REQUEST['id']);
	if (isset($_REQUEST['opt'])) { $opt = $_REQUEST['opt']; } else { $opt = 'list'; }
		$toolmenu = array();
		$toolmenu[1]['icon'] = 'list.gif';
		$toolmenu[1]['title'] = 'Статистика';
		$toolmenu[1]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=list';

		$toolmenu[2]['icon'] = 'clock.gif';
		$toolmenu[2]['title'] = 'Распределение по времени';
		$toolmenu[2]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=time';

		$toolmenu[3]['icon'] = 'calendar.gif';
		$toolmenu[3]['title'] = 'Статистика за месяц';
		$toolmenu[3]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=month';

		$toolmenu[4]['icon'] = 'refers.gif';
		$toolmenu[4]['title'] = 'Источники просмотров';
		$toolmenu[4]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=refers';

		$toolmenu[9]['icon'] = 'config.gif';
		$toolmenu[9]['title'] = 'Настройки компонента';
		$toolmenu[9]['link'] = '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config';
	
		cpToolMenu($toolmenu);

	//LOAD CURRENT CONFIG
	$cfg = $inCore->loadComponentConfig('statistics');

	if($opt=='saveconfig'){	
		$cfg = array();
        $inCore->saveComponentConfig('statistics', $cfg);
		header('location:index.php?view=components&do=config&id='.$_REQUEST['id'].'&opt=list');	
	}

	if ($opt == 'list'){
		echo '<h3>Статистика</h3>';
		
		//visitors today
		$sql = "SELECT id FROM cms_stats WHERE DATE(logdate) = CURDATE() GROUP BY ip";
		$result = dbQuery($sql) ;
		$today_v = mysql_num_rows($result);

		//hits today
		$sql = "SELECT id FROM cms_stats WHERE DATE(logdate) = CURDATE()";
		$result = dbQuery($sql) ;
		$today_h = mysql_num_rows($result);

		//visitors last 24h
		$sql = "SELECT id FROM cms_stats WHERE logdate >= DATE_SUB(NOW(), INTERVAL 1 DAY) GROUP BY ip";
		$result = dbQuery($sql) ;
		$day_v = mysql_num_rows($result);

		//hits last 24h
		$sql = "SELECT id FROM cms_stats WHERE logdate >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
		$result = dbQuery($sql) ;
		$day_h = mysql_num_rows($result);
	
		//visitors all time
		$sql = "SELECT id FROM cms_stats GROUP BY ip";
		$result = dbQuery($sql) ;
		$alltime_v = mysql_num_rows($result);

		//hits all time
		$sql = "SELECT id FROM cms_stats";
		$result = dbQuery($sql) ;
		$alltime_h = mysql_num_rows($result);

		$sql = "SELECT DATE_FORMAT(logdate, '%d/%m/%Y (%H:%i)') as logdate FROM cms_stats ORDER BY logdate ASC LIMIT 1";
		$result = dbQuery($sql) ;

		$startdate = mysql_fetch_assoc($result);
		$startdate = $startdate['logdate'];
	
		echo '<div style="padding:10px;clear:both"><a href="/" target="_blank">Открыть сайт в новом окне</a> &rarr;</div>';
		
		echo '<div class="blockdiv">';		
			echo '<div><strong>Посетителей сегодня: </strong>'.$today_v.'</div>';
			echo '<div><strong>Просмотров сегодня: </strong>'.$today_h.'</div>';				
		echo '</div>';
		echo '<div class="blockdiv">';		
			echo '<div><strong>Посетителей за сутки: </strong>'.$day_v.'</div>';
			echo '<div><strong>Просмотров за сутки: </strong>'.$day_h.'</div>';				
		echo '</div>';

		echo '<div class="blockdiv">';		
			echo '<div><strong>Начало сбора статистики: </strong></div>';
			echo '<div>'.$startdate.'</div>';
		echo '</div>';

		echo '<div class="blockdiv" style="border:solid 1px gray; background-color:white">';		
			echo '<div><strong>Посетителей за все время: </strong>'.$alltime_v.'</div>';
			echo '<div><strong>Просмотров за все время: </strong>'.$alltime_h.'</div>';				
		echo '</div>';

		//popular site pages
		$sql = "SELECT page as title, COUNT( page ) AS cnt
				FROM cms_stats
				GROUP BY page
				ORDER BY cnt DESC 
				LIMIT 10";
		$result = dbQuery($sql) ;
		
		if (mysql_num_rows($result)){
			echo '<div class="blockdiv_pop">';
			echo '<div><strong>Популярные страницы сайта</strong></div>';
			$data = array();
			while ($ag = mysql_fetch_assoc($result)) { $data[] = $ag; }
			echo getChart($data, 'getLink');
			echo '</div>';		
		}
	
		//useragents
		$sql = "SELECT agent as title, COUNT( agent ) AS cnt
				FROM cms_stats
				GROUP BY agent
				ORDER BY cnt DESC 
				LIMIT 10";
		$result = dbQuery($sql) ;
		
		if (mysql_num_rows($result)){
			echo '<div class="blockdiv_pop">';
			echo '<div><strong>Популярные агенты</strong></div>';
			$data = array();
			while ($ag = mysql_fetch_assoc($result)) { $data[] = $ag; }
			echo getChart($data, 'getAgent');
			echo '</div>';		
		}
		
		//search engines
		$sql = "SELECT agent as title, COUNT( agent ) AS cnt
				FROM cms_stats
				WHERE (agent LIKE '%Googlebot%') OR (agent LIKE '%Yandex%') OR (agent LIKE '%Aport%') OR
					  (agent LIKE '%msnbot%') OR (agent LIKE '%Lycos.com%') OR (agent LIKE '%Yahoo!%') OR
					  (agent LIKE '%Yahoo-MMCrawler%') OR (agent LIKE '%rambler%') OR (agent LIKE '%W3C_Validator%') OR
					  (agent LIKE '%FAST-WebCrawler%') OR (agent LIKE '%Openbot%') OR (agent LIKE '%TurtleScanner%')
				GROUP BY agent
				ORDER BY cnt DESC 
				LIMIT 10";
		$result = dbQuery($sql) ;
		
		if (mysql_num_rows($result)){
			echo '<div class="blockdiv_pop">';
			echo '<div><strong>Активность поисковых ботов</strong></div>';
			$data = array();
			while ($ag = mysql_fetch_assoc($result)) { $data[] = $ag; }
			echo getChart($data, 'getAgent');
			echo '</div>';		
		}
		

	}

	if ($opt == 'refers'){

		cpAddPathway('Источники', $_SERVER['REQUEST_URI']);
		echo '<h3>Источники</h3>';
		
		//useragents
		$sql = "SELECT refer as title, COUNT( refer ) AS cnt
				FROM cms_stats
				GROUP BY refer
				ORDER BY cnt DESC 
				LIMIT 15";
		$result = dbQuery($sql) ;
		
		if (mysql_num_rows($result)){
			echo '<div class="blockdiv_pop">';
			$data = array();
			while ($ag = mysql_fetch_assoc($result)) { $data[] = $ag; }
			echo getChart($data);
			echo '</div>';		
		}
		
	}
	
	if ($opt == 'time'){
		cpAddPathway('По времени суток', $_SERVER['REQUEST_URI']);
	
		echo '<h3>Активность посетителей по времени суток</h3>';
		//activity for last 24h
		echo '<div style="width:50%;float:left">';
			echo '<div class="blockdiv_pop">';
			echo '<div><strong>За последние сутки</strong></div>';			
						
			$data = array();
			for($h = 0; $h < 24; $h++){
				$next = sizeof($data);
				
				if ($h >= 8 && $h <= 17){
					$data[$next]['title'] = date('H:00', mktime($h, 0, 0, 0, 0, 0)) . ' - ' . date('H:00', mktime(($h+1), 0, 0, 0, 0, 0));
				} else {
					$data[$next]['title'] = '<span style="color:gray;background-color:#EBEBEB">'.date('H:00', mktime($h, 0, 0, 0, 0, 0)) . ' - ' . date('H:00', mktime(($h+1), 0, 0, 0, 0, 0)).'</span>';			
				}
				
				$h = str_pad($h, 2, "0", STR_PAD_LEFT);  
				
				$sql = "SELECT id, COUNT(id) as cnt 
						FROM cms_stats 
						WHERE DATE_FORMAT(logdate, '%H') LIKE '$h' AND logdate >= DATE_SUB(NOW(), INTERVAL 1 DAY)
						GROUP BY DATE_FORMAT(logdate, '%H')";
				$result = dbQuery($sql) ;
				
				while ($ag = mysql_fetch_assoc($result)) { $data[$next]['cnt'] = $ag['cnt']; }
			}
			echo getChart($data, '', false, true);						
			echo '</div>';
		echo '</div>';
		//activity for all time
		echo '<div style="width:50%;float:left">';
			echo '<div class="blockdiv_pop">';
			echo '<div><strong>За все время</strong></div>';			
			
			$data = array();
			for($h = 0; $h < 24; $h++){
				$next = sizeof($data);
				
				if ($h >= 8 && $h <= 17){
					$data[$next]['title'] = date('H:00', mktime($h, 0, 0, 0, 0, 0)) . ' - ' . date('H:00', mktime(($h+1), 0, 0, 0, 0, 0));
				} else {
					$data[$next]['title'] = '<span style="color:gray;background-color:#EBEBEB">'.date('H:00', mktime($h, 0, 0, 0, 0, 0)) . ' - ' . date('H:00', mktime(($h+1), 0, 0, 0, 0, 0)).'</span>';			
				}
				
				$h = str_pad($h, 2, "0", STR_PAD_LEFT);  
				
				$sql = "SELECT id, COUNT(id) as cnt FROM cms_stats WHERE DATE_FORMAT(logdate, '%H') LIKE '$h' GROUP BY DATE_FORMAT(logdate, '%H')";
				$result = dbQuery($sql) ;
				
				while ($ag = mysql_fetch_assoc($result)) { $data[$next]['cnt'] = $ag['cnt']; }
			}
			echo getChart($data, '', false, true);						
			echo '</div>';
		echo '</div>';
	}
	
	if ($opt == 'month'){
		cpAddPathway('За месяц', $_SERVER['REQUEST_URI']);
	
		echo '<h3>Статистика за месяц</h3>';
		echo '<div class="blockdiv_pop">';

		$m = str_replace('0', '', date('m'));
		$y = date('Y');
		
//		echo $m;
		
		$data = array();
		for($d = 1; $d <= 31; $d++){
			if(checkdate($m, $d, $y)){			
				$next = sizeof($data);
				$data[$next]['title'] = $inCore->getRusDate(date('F / '.$d));		
				
				$d = str_pad($d, 2, "0", STR_PAD_LEFT);  				
				$m = str_pad($m, 2, "0", STR_PAD_LEFT); 
				
				$sql = "SELECT id, COUNT(id) as cnt 
						FROM cms_stats 
						WHERE DATE_FORMAT(logdate, '%d') LIKE '$d' AND DATE_FORMAT(logdate, '%m') = '$m' AND DATE_FORMAT(logdate, '%Y') = '$y' 
						GROUP BY DATE_FORMAT(logdate, '%d')";

				$result = dbQuery($sql) ;
				while ($ag = mysql_fetch_assoc($result)) { $data[$next]['cnt'] = $ag['cnt']; }
			}
			
		}
		echo getChart($data, '', false, true);						
		echo '</div>';
	}

	if($opt=='config'){

	cpAddPathway('Настройки', '?view=components&do=config&id='.$_REQUEST['id'].'&opt=config');	
	echo '<h3>Настройки компонента</h3>';
	
	?>
	<form action="index.php?view=components&do=config&id=<?php echo $_REQUEST['id'];?>" method="post" name="optform" target="_self" id="form1">
        <table width="370" border="0" cellpadding="10" cellspacing="0" class="proptable">
          <tr>
            <td width="350" colspan="2" valign="top"><p><b>Внимание!</b></p>
              <p>Компонент &quot;Статистика пользователя&quot; работает только если в общих настройках сайта включен сбор статистики.</p>
              <p>Работа компонента может несколько увеличивать время загрузки страниц при большом количестве посетителей. </p>
            <label></label></td>
          </tr>
        </table>
        <p>
          <input name="opt" type="hidden" id="do" value="saveconfig" />
          <input name="back" type="button" id="back" value="Отмена" onclick="window.location.href='index.php?view=components&do=config&id=<?php echo $_REQUEST['id']; ?>';"/>
        </p>
</form>    <?php
	
	}

?>