{* ================================================================================ *}
{* ========================== Вывод комментариев ================================== *}
{* ================================================================================ *}

{* ====================================== Заголовок ============================================ *}
<div class="cmm_heading">
	<a name="c" />{$LANG.COMMENTS} ({$comments_count})</a>
</div>

{* ========================= Сообщение о добавлении/удалении коммента ========================== *}
{if $cm_message}
	<p style="color:green">{$cm_message}</p>
{/if}

	{* ================================= Ссылка на RSS =============================== *}
	<div class="cmm_icons">
		<table>
			<tr>
				<td><img src="/images/markers/rssfeed.png" border="0" alt="{$LANG.RSS}"/></td>
				<td><a href="/rss/comments/{$target}-{$target_id}/feed.rss">{$LANG.RSS_COMM}</a></td>
			</tr>
		</table>
	</div>

    <div class="cm_ajax_list">
        <p style="margin:30px; margin-left:0px; padding-left:50px;background:url(/images/ajax-loader.gif) no-repeat">{$LANG.LOADING_COMM}...</p>
    </div>
    <script type="text/javascript">
        {literal}
            var anc = '';
            if (window.location.hash){
                var anc = window.location.hash;
            }
        {/literal}
        loadComments('{$target}', {$target_id}, anc);
    </script>

{* ========================= Сообщение об ошибке добавления коммента ========================== *}
{if $cm_error}
	<p style="color:red">{$cm_error}</p>
{/if}

{* ===================== Ссылки на добавление комментария и подписку ========================== *}
<div id="addcommentlink" src="#">
	<table cellspacing="0" cellpadding="2">
		<tr>
			<td width="16"><img src="/components/comments/images/new.gif" /></td>
			<td><a href="javascript:void(0);" id="addcommentlink" onclick="{$add_comment_js}">{$LANG.ADD_COMM}</a></td>
			{if $cfg.subscribe}
				{if $is_user}
					{if !$user_subscribed}
						<td width="16"><img src="/components/comments/images/subscribe.gif"/></td>
						<td><a href="/subscribe/{$target}/{$target_id}">{$LANG.SUBSCRIBE_TO_NEW}</a></td>
					{else}
						<td width="16"><img src="/components/comments/images/unsubscribe.gif"/></td>
						<td><a href="/unsubscribe/{$target}/{$target_id}">{$LANG.UNSUBSCRIBE}</a></td>
					{/if}
				{/if}	
			{/if}
		</tr>
	</table>	
</div>

<div id="cm_addentry0" style="display:block"></div>