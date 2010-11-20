{* ================================================================================ *}
{* ==================Закрытый профиль пользователя ================================ *}
{* ================================================================================ *}

<div id="usertitle">
    <div class="con_heading" id="nickname">
        {$usr.nickname}
    </div>
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:14px">
	<tr>
		<td width="200" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center" valign="middle">
                        <div class="usr_avatar">
                            {$avatar}
                        </div>
                        <div>{$LANG.LAST_VISIT}: {$usr.flogdate}</div>
						{if $is_auth}
							<div id="usermenu">
                            <div class="usr_profile_menu">
                                <table cellpadding="0" cellspacing="6" ><tr>
                                        <tr>
                                            <td><img src="/templates/_default_/images/icons/profile/friends.png" border="0"/></td>
                                            <td><a href="/users/{$usr.id}/friendship.html" title="{$LANG.ADD_TO_FRIEND}">{$LANG.ADD_TO_FRIEND}</a></td>
    
                                        </tr>
                                </table>
                                </div>
                            </div>
                        {/if}
					</td>
				</tr>
			</table>
	    </td>
    	<td valign="top" style="padding-left:10px">	
					<h3>{$LANG.ACCESS_SECURITY}</h3>
	</td>
  </tr>
</table>
