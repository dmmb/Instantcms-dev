<?php
/*********************************************************************************************/
//																							 //
//						   InstantCMS v1.0 (c) 2008 COMMERCIAL VERSION                       //
//   						Source code protected by copyright laws                          //
//                                                                                           //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/
if(!defined('VALID_CMS')) { die('ACCESS DENIED'); }

function usrFormEditor($user_id, $form_id, $formsdata){
    
    $inPage = cmsPage::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$html   = '';
		
	//GET FORM DATA
	$sql = "SELECT *
			FROM cms_forms
			WHERE id = $form_id";
	$result = $inDB->query($sql);
	
	if (!$inDB->num_rows($result)) { return false; }

    $form = $inDB->fetch_assoc($result);
    
    //BUILD FORM
    if($form['description']) { $html .= '<div>'.$form['description'].'</div>'; }

    //GET FIELDS DATA
    $sql    = "SELECT * FROM cms_form_fields WHERE form_id = $form_id ORDER BY ordering ASC";
    $result = $inDB->query($sql);
    if ($inDB->num_rows($result)){
        //BUILD FORM FIELDS
        $html .= '<table cellpadding="5" width="100%">';
        while($field = $inDB->fetch_assoc($result)){
            $html .= '<tr><td width="300" valign="top">';
                $html .= '<strong>'.$field['title'].'</strong>';
            if(is_array($formsdata)){
                if (array_key_exists($field['id'], $formsdata)){
                    $default = $formsdata[$field['id']];
                    $default = str_replace('&quot;', '"', $default);
                } else {
                    $default = '';
                }
            }
            $html .= '<td valign="top">'.$inPage->buildFormField(1, $field, $default).'</td></tr>';
        }
        $html .= '</table>';
    } else { $html .= '<p>� ����� ��� �����.</p>'; }

	return $html;

}

function usrForm($user_id, $form_id, $formsdata){
    global $menuid;

    $inCore = cmsCore::getInstance();
    $inDB   = cmsDatabase::getInstance();
	$html = '';
		
	//GET FORM DATA
	$sql = "SELECT *
			FROM cms_forms
			WHERE id = $form_id";
	$result = $inDB->query($sql) ;
	
	if (!$inDB->num_rows($result)) { return false; }
	else {
			$form = $inDB->fetch_assoc($result);
			//BUILD FORM				
			$html .= '<div id="usr_prof_form">';
				//GET FIELDS DATA
				$sql = "SELECT * FROM cms_form_fields WHERE form_id = $form_id ORDER BY ordering ASC";
				$result = $inDB->query($sql) ;
				if ($inDB->num_rows($result)){				
				    //BUILD FORM FIELDS
					$html .= '<table cellpadding="0" cellspacing="0">';					
					while($field = $inDB->fetch_assoc($result)){
						$html .= '<tr><td valign="top" id="title">';
							$html .= '<strong>'.$field['title'].'</strong>';
							
						$default = '<em>�� �������</em>';
						if(is_array($formsdata)){
							if (array_key_exists($field['id'], $formsdata)){
								if ($formsdata[$field['id']]){
									$default = $formsdata[$field['id']];	
									$default = nl2br($default);
								}
							}                            
						}
						$html .= '<td valign="top" id="field">'.cmsPage::getMetaSearchLink('/users/'.$menuid.'/hobby/', $default).'</td></tr>';
					}					
					$html .= '</table>';												
				} else { $html .= '<p>� ����� "'.$form['title'].'" ��� �����.</p>'; }
					
			$html .= '</div>';
	}
	return $html;			
}

?>
