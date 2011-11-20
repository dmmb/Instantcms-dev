{* ================================================================================ *}
{* ==================== Просмотр объявления (на доске объявлений) ================= *}
{* ================================================================================ *}
<h1 class="con_heading">{$item.title}</h1>
<div class="bd_item_details_full">
    {if $item.is_vip}
        <span class="bd_item_is_vip">{$LANG.VIP_ITEM}</span>
    {/if}
	<span class="bd_item_date">{$item.pubdate}</span>
    <span class="bd_item_hits">{$item.hits}</span>
	{if $item.city}
		<span class="bd_item_city">
			<a href="/board/city/{$item.enc_city}">{$item.city}</a>
		</span>
	{/if}
	{if $item.user}
		<span class="bd_item_user">
			<a href="{profile_url login=$item.user_login}">{$item.user}</a>
		</span>
	{else}
    	<span class="bd_item_user">{$LANG.BOARD_GUEST}</span>
	{/if}
	{if $item.moderator}
		<span class="bd_item_edit"><a href="/board/edit{$item.id}.html">{$LANG.EDIT}</a></span>
        {if !$item.published && ($is_admin || $is_moder)}
        	<span class="bd_item_publish"><a href="/board/publish{$item.id}.html">{$LANG.PUBLISH}</a></span>
        {/if}
		<span class="bd_item_delete"><a href="/board/delete{$item.id}.html">{$LANG.DELETE}</a></span>
	{/if}				
</div>

<table width="100%" height="" cellspacing="" cellpadding="0" class="bd_item_full">
	<tr>
		{if $item.file && $cfg.photos}
			<td width="64">
					<img class="bd_image_small" src="/images/board/medium/{$item.file}" border="0" alt="{$item.title|escape:'html'}"/>
			 </td>
		{/if}
		<td valign="top">
			<div class="bd_text_full">
            	<p>{$item.content}</p>
                {if $formsdata}
                    <table width="100%" cellspacing="0" cellpadding="2" style="border-top:1px solid #C3D6DF; margin:5px 0 0 0">
                        {foreach key=tid item=form from=$formsdata}
                        <tr>
                            <td valign="top" width="140px">
                                <strong>{$form.title}:</strong>
                            </td>
                            <td valign="top">
                                {$form.value}
                            </td>
                        </tr>
                        {/foreach}
                     </table>
                {/if}
            </div>
		</td>
	</tr>
</table>		

<div class="bd_links">
	{if $user_id}
		{if $item.user_id && $item.user_id != $user_id}
			<span class="bd_message"><a href="/users/{$item.user_id}/sendmessage.html">{$LANG.WRITE_MESS_TO_AVTOR}</a></span>
		{/if}
	{/if}
    {if $item.user_login}
	<span class="bd_author"><a href="/board/by_user_{$item.user_login}">{$LANG.ALL_AVTOR_ADVS}</a></span>
    {/if}
</div>

{if $cfg.comments}
    {comments target='boarditem' target_id=$item.id}
{/if}
