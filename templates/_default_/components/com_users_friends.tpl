{* ================================================================================ *}
{* ============================ Äðóçüÿ ============================================ *}
{* ================================================================================ *}
<div class="con_heading"><a href="{profile_url login=$usr.login}">{$usr.nickname}</a> &rarr; {$LANG.FRIENDS} ({$total})</div>
				<div class="users_list">
					<table width="100%" cellspacing="0" cellpadding="0" class="users_list">
  {foreach key=tid item=friend from=$friends}
								<tr>
									<td width="80" valign="top">
										<div class="avatar"><a href="{profile_url login=$friend.login}">{$friend.avatar}</a></div>
									</td>
									<td valign="top">
                                         <div class="status">{$friend.flogdate}<br />
                                         	<a href="/users/{$friend.id}/sendmessage.html">{$LANG.WRITE_MESS}</a><br />
                                            <a href="/users/{$friend.id}/nofriends.html">{$LANG.STOP_FRIENDLY}</a>
                                         </div>
										<div class="nickname">
                                        	<a class="friend_link" href="{profile_url login=$friend.login}">{$friend.nickname}</a><br />
                                            {if $friend.status}
                                            	<span class="microstatus">{$friend.status}</span>
                                            {/if}    
                </div>
            </td>
                                 </tr>
  {/foreach}
</table>
				</div>

{$pagebar}