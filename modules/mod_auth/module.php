<?php
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.5   (c) 2009 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2009                        //
//                                                                                           //
/*********************************************************************************************/
	function mod_auth($module_id){
        $inCore = cmsCore::getInstance();
        $inDB = cmsDatabase::getInstance();
        $inUser = cmsUser::getInstance();
	global $_LANG;
	
		$cfg = $inCore->loadModuleConfig($module_id);
		$menuid = 0;
		
		if ($inUser->id){ return false; }
		//login form
		?>
			<form action="/login" method="post" name="authform" style="margin:0px" target="_self" id="authform">
			  <table class="authtable" width="100%" border="0" cellspacing="0" cellpadding="2">
				<tr>
				  <td width="86"><? echo $_LANG['AUTH_LOGIN']; ?>:</td>
				  <td width="917"><input name="login" type="text" id="login" size="15" /></td>
				</tr>
				<tr>
				  <td height="30" valign="top"><? echo $_LANG['AUTH_PASS']; ?>:</td>
				  <td valign="top"><input name="pass" type="password" id="pass" size="15" /></td>
				</tr>
				<tr>
				  <td height="27" colspan="2" align="right" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr style="font-size:10px">
                      <td width="13%"><input style="width:60px" type="submit" name="Submit" value="<? echo $_LANG['AUTH_ENTER']; ?>" /></td>
                      <td width="87%" align="right">
                       <?php if (@$cfg['autolog']) { ?>
					    <input name="remember" type="checkbox" id="remember" value="1" />
						<? echo $_LANG['AUTH_REMEMBER']; ?><br /><?php } ?>
						<?php if (@$cfg['passrem']) { ?>
	                      <a href="/0/passremind.html"><? echo $_LANG['AUTH_FORGOT']; ?></a></td>
	 				   <?php } ?>
                    </tr>
                  </table></td>
			    </tr>
			  </table>
			</form>
		<?php
		
		return true;
	}
?>