{* ================================================================================ *}
{* ==================== Просмотр объявления (на доске объявлений) ================= *}
{* ================================================================================ *}

<div class="bd_item_details_full">
	<span class="bd_item_date">{$item.pubdate}</span>
	{if $item.city}
		<span class="bd_item_city">
			<a href="/board/{$menuid}/city/{$item.enc_city}">{$item.city}</a>
		</span>
	{/if}
	{if $item.user}
		<span class="bd_item_user">
			<a href="{profile_url login=$item.user_login}">{$item.user}</a>
		</span>
	{/if}
	{if $moderator}
		<span class="bd_item_edit"><a href="/board/{$menuid}/edit{$item.id}.html">{$LANG.EDIT}</a></span>
		<span class="bd_item_delete"><a href="/board/{$menuid}/delete{$item.id}.html">{$LANG.DELETE}</a></span>
	{/if}				
</div>

<table width="100%" height="" cellspacing="" cellpadding="0" class="bd_item_full">
	<tr>
		{if $item.file && $cfg.photos}
			<td width="64">
					<img class="bd_image_small" src="/images/board/medium/{$item.file}" border="0" alt="{$item.title}"/>
			 </td>
		{/if}
		<td valign="top">
			<div class="bd_text_full">{$item.content}</div>
		</td>
	</tr>
</table>		

<div class="bd_links">
	{if $is_user}
		{if $item.user_id != $user_id}
			<span class="bd_message"><a href="/users/0/{$item.user_id}/sendmessage.html">{$LANG.WRITE_MESS_TO_AVTOR}</a></span>
		{/if}
	{/if}
	<span class="bd_author"><a href="/users/{$menuid}/{$item.user_id}/board.html">{$LANG.ALL_AVTOR_ADVS}</a></span>
</div>
