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

	function mod_polls($module_id){

        $inCore = cmsCore::getInstance();
        $inDB   = cmsDatabase::getInstance();
		$cfg    = $inCore->loadModuleConfig($module_id);
	
		if ($cfg['poll_id']!=0){
			$sql = "SELECT *
					FROM cms_polls
					WHERE id = ".$cfg['poll_id']."
					LIMIT 1
					";
		} else {
			$sql = "SELECT *
					FROM cms_polls
					ORDER BY RAND()
					LIMIT 1
					";	
		}
		
		$result = $inDB->query($sql) ;
		
		if ($inDB->num_rows($result)){
			$poll=$inDB->fetch_assoc($result);
			
			$answers = unserialize($poll['answers']);
					 
			$answers_title = array();
			$answers_num = array();
			$item = 1;
			foreach($answers as $key=>$value){
				$key = str_replace('"', '&quot;', $key);
				$answers_title[$item] = $key;
				$answers_num[$item] = $value;
				$item++;
			}
			
			if ((!isset($_SESSION['poll_voted']) || @$_SESSION['poll_voted']!=$poll['id']) && !cmsUser::isUserVoted($poll['id']) ){
				$total = 0;
				//SHOW POLL
				echo '<p class="mod_poll_title"><b>'.$poll['title'].'</b></p>';
				echo '<form action="/polls/vote" method="post">';
				echo '<input type="hidden" name="poll_id" value="'.$poll['id'].'" />';
				echo '<table class="mod_poll_answers">';
				foreach($answers_title as $key=>$value){
					  echo '<tr>';
					  echo '<td><input name="answer" id="answer'.$module_id . $key.'" type="radio" value="'.$value.'"></td>';
					  echo '<td class="mod_poll_answer"><label for="answer'.$module_id . $key.'">'.$value;
					  if ($cfg['shownum']){
						  echo ' ('.$answers_num[$key].')';
					  }
					  echo '</label></td>';
					  echo '</tr>';
					  $total += $answers_num[$key];
				  }
				 echo '</table>';
				 echo '<div align="center"><input type="submit" class="mod_poll_submit" value="Голосовать ('.$total.')"></div>';
				echo '</form>';
				
			} else {
				//SHOW RESULTS
				echo '<p class="mod_poll_title"><b>'.$poll['title'].'</b></p>';
				$total = 0;
				foreach($answers_num as $key=>$value){
					$total += $value;
			    }
				foreach($answers_title as $key=>$value){
					
					if ($total == 0) { $percent = 0; } else {
						$percent = round(($answers_num[$key] / $total) * 100);
					}
					
					echo '<span class="mod_poll_gauge_title">'.$value.' ('.$answers_num[$key].')</span>';
					if($percent>0){
						echo '<table class="mod_poll_gauge" width="'.round($percent*0.8).'%"><tr><td></td></tr></table>';
					} else {
						echo '<table class="mod_poll_gauge" width="5"><tr><td></td></tr></table>';					
					}
					
				}		
				unset($_SESSION['poll_voted']);						
			}

		}

		return true;	
	}
?>