{if $is_usr}
	{foreach key=tid item=usr from=$users}
	
		<a href="/users/{$usr.uid}/photo{$usr.id}.html">
			<div align="center"><img src="/images/users/photos/small/{$usr.imageurl}" border="0"/></div>
		</a>
		
		{if $cfg.showtitle}
			<div style="margin-top:5px" align="center"><strong>{$usr.title}</strong></div>
			<div align="center">{$usr.genderlink}</a></div>
		{/if}
		
	{/foreach}
	
{else}
	<p>Нет данных для отображения.</p>
{/if}