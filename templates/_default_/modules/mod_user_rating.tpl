{if $is_usr}
	<table cellspacing="5" border="0" class="mod_user_rating">
	{foreach key=tid item=usr from=$users}
		<tr>
			<td width="20" class="avatar">{$usr.usrimage}</td>
			<td width="">
					<a href="{$usr.profileurl}" class="nickname">{$usr.nickname}</a>
					
					{if $cfg.view_type == 'rating'}
						<div class="rating">{$usr.rating}</div>
					{elseif $usr.karma > 0}
						<div class="karma"><span style="color:green">+{$usr.karma}</span></div>
					{elseif $usr.karma == 0}
						<div class="karma"><span style="color:gray">{$usr.karma}</span></div>
					{else}
						<div class="karma"><span style="color:red">{$usr.karma}</span></div>							
					{/if}
                    {if $usr.status}
                    	<div class="microstatus">{$usr.status}</div>
                    {/if}
			</td>
		</tr>
	{/foreach}
	</table>
{else}
	<p>Нет данных для отображения.</p>
{/if}