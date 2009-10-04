<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/
	function mod_menu($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
		$menuid = $inCore->menuId();
		$cfg = $inCore->loadModuleConfig($module_id);

		if (!isset($cfg['menu'])) { $menu = 'mainmenu'; } else { $menu = $cfg['menu']; }

		if ($inUser->id){
			$user_group = $inUser->group_id;
		} else {
			$user_group = cmsUser::getGuestGroupId();
		}

		$sql         = "SELECT NSLeft, NSRight, NSLevel FROM cms_menu WHERE id = $menuid";
		$result      = $inDB->query($sql);
		$currentmenu = $inDB->fetch_assoc($result);

		$root_id     = dbGetField('cms_menu', 'parent_id=0', 'id');

		$nested_sets = $inCore->nestedSetsInit('cms_menu');
		$rs_rows     = $nested_sets->SelectSubNodes($root_id);

		$last_level = -1;

		ob_start();

		if ($cfg['jtree']){
			echo '<div><ul id="'.$menu.'" class="filetree treeview-famfamfam">';
		} else {
			echo '<div><ul id="'.$menu.'" class="'.$menu.'">';
		}

		$hide_parent = 0;

		$is_admin = false;
		if(isset($inUser->id)) { if ($inCore->userIsAdmin($inUser->id)) { $is_admin = true; } }

		while ($row = $inDB->fetch_assoc($rs_rows)){
			if ($row['menu'] == $menu){

				$menulink = $inCore->menuSeoLink($row['link'], $row['linktype'], $row['id']);

				if ($row['allow_group']==-1 || $row['allow_group']==$user_group || $is_admin){
					if($row['published']){
						if ($row['parent_id']!=$hide_parent){
							if ($row['id']!=$menuid){
								//link target
								if ($row['target']) { $target = $row['target']; } else { $target = '_self'; }
								$link = '<a target="'.$target.'" class="" href="'.$menulink.'" >'.$row['title'].'</a>';
							} else {
								$link = $row['title'];
							}
							// BUILD ITEM /////////////////////////////////////////////////////////////////////
							if (!$row['iconurl']) {
								$fileicon = '/includes/jquery/treeview/images/file.gif';
								$foldericon = '/includes/jquery/treeview/images/folder-closed.gif';
							} else {
								$fileicon = '/images/menuicons/'.$row['iconurl'];
								$foldericon = '/images/menuicons/'.$row['iconurl'];
							}
							//////
							if ( $row['NSLevel'] > 1 ) { $padding = '0px 0px 0px 15px'; } else { $padding = "0px"; }
							if ( $row['NSLevel'] < $last_level ) { echo str_repeat('</ul></li>', $last_level-$row['NSLevel']); }
							if ( $row['NSRight'] - $row['NSLeft'] == 1 ){ // если разница единица, то значит у него нет потомков
								echo '<li style="padding:'.$padding.';"><span class="file" style="background: url('.$fileicon.') 0 0 no-repeat;">'.$link.'</span></li>';
							}
							else {
								if (!$cfg['jtree']){
									if ($currentmenu['NSLeft'] > $row['NSLeft'] && $currentmenu['NSRight'] < $row['NSRight']){
										echo '<li class="open" style="padding:'.$padding.';"><span class="folder" style="background: url('.$foldericon.') 0 0 no-repeat;"><a href="'.$menulink.'">'.$row['title'].'</a></span><ul>';
									} else {
										echo '<li style="padding:'.$padding.';"><span class="folder" style="background: url('.$foldericon.') 0 0 no-repeat;"><a href="'.$menulink.'">'.$row['title'].'</a></span><ul>';
									}
								} else {
									if ($currentmenu['NSLeft'] > $row['NSLeft'] && $currentmenu['NSRight'] < $row['NSRight']){
										echo '<li class="open" style="padding:'.$padding.';"><span class="folder" style="background: url('.$foldericon.') 0 0 no-repeat;"><a href="#">'.$row['title'].'</a></span><ul>';
									} else {
										echo '<li style="padding:'.$padding.';"><span class="folder" style="background: url('.$foldericon.') 0 0 no-repeat;"><a href="#">'.$row['title'].'</a></span><ul>';
									}
								}
							}
							$last_level = $row['NSLevel'];
							////////////////////////////////////////////////////////////////////////////////////
						}
					} else {
						$hide_parent = $row['id'];
					}
				}
			}
		}
		echo '</ul></div>';

		if ($cfg['jtree']){
			echo '<script type="text/javascript">
					$(document).ready(function(){
						 $("#'.$menu.'").treeview({
							animated: true,
							collapsed: true,
							unique: false
							});
						}
					);
				 </script>' ."\n";
		}
		
		return true;
	
	}
?>