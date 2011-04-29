{* ================================================================================ *}
{* ========================== Вывод комментариев ================================== *}
{* ================================================================================ *}

{* ====================================== Заголовок ============================================ *}
<div class="cmm_heading">
	<a name="c" />{$labels.comments} ({$comments_count})</a>
</div>

{* ========================= Сообщение о добавлении/удалении коммента ========================== *}
{if $cm_message}
	<p style="color:green">{$cm_message}</p>
{/if}


    <div class="cm_ajax_list">
    {if $cfg.cmm_ajax}
    <script type="text/javascript">
        {literal}
            var anc = '';
            if (window.location.hash){
                var anc = window.location.hash;
            }
        {/literal}
        loadComments('{$target}', {$target_id}, anc);
    </script>
	{else}
    {$html}
    {/if}
    </div>

{* ========================= Сообщение об ошибке добавления коммента ========================== *}
{if $cm_error}
	<p style="color:red">{$cm_error}</p>
{/if}

{* ===================== Ссылки на добавление комментария и подписку ========================== *}
<div id="addcommentlink" src="#">
	<table cellspacing="0" cellpadding="2">
		<tr>
			<td width="16"><img src="/templates/_default_/images/icons/comment.png" /></td>
			<td><a href="javascript:void(0);" id="addcommentlink" onclick="{$add_comment_js}">{$labels.add}</a></td>
			{if $cfg.subscribe}
				{if $is_user}
					{if !$user_subscribed}
						<td width="16"><img src="/templates/_default_/images/icons/subscribe.png"/></td>
						<td><a href="/subscribe/{$target}/{$target_id}">{$LANG.SUBSCRIBE_TO_NEW}</a></td>
					{else}
						<td width="16"><img src="/templates/_default_/images/icons/unsubscribe.png"/></td>
						<td><a href="/unsubscribe/{$target}/{$target_id}">{$LANG.UNSUBSCRIBE}</a></td>
					{/if}
				{/if}	
			{/if}
            <td width="16"><img src="/templates/_default_/images/icons/rss.png" border="0" alt="{$LANG.RSS}"/></td>
            <td><a href="/rss/comments/{$target}-{$target_id}/feed.rss">{$labels.rss}</a></td>
		</tr>
	</table>	
</div>

<div id="cm_addentry0" style="display:block"></div>