{* ================================================================================ *}
{* =============================== "Мои статьи" =================================== *}
{* ================================================================================ *}

{if $messages}
    <div class="sess_messages">
        {foreach key=id item=message from=$messages}
            {$message}
        {/foreach}
    </div>
{/if}

<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<thead>
		<tr>
			<td width="110"><strong>{$LANG.DATE}</strong></td>
			<td colspan="2"><strong>{$LANG.ARTICLE}</strong></td>
			<td width="100" align="center"><strong>{$LANG.STATUS}</strong></td>
			<td width="16">&nbsp;</td>
			<td width="20">&nbsp;</td>
			<td width="100"><strong>{$LANG.CAT}</strong></td>
			<td width="70" align="center"><strong>{$LANG.ACTION}</strong></td>
		</tr>
	</thead>
	<tbody>
	{foreach key=tid item=article from=$articles}
		<tr>
			<td class="{$article.class}">{$article.pubdate}</td>
			<td class="{$article.class}"><img src="/templates/_default_/images/icons/article.png" border="0"></td>
			<td class="{$article.class}"><a href="{$article.href}">{$article.title}</a></td>
			<td class="{$article.class}" align="center">{$article.status}</td>
			<td class="{$article.class}"><img src="/templates/_default_/images/icons/comments.png" border="0"></td>
			<td class="{$article.class}">{$article.comments}</td>
			<td class="{$article.class}"><a style="font-size:11px" href="{$article.category_href}">{$article.category}</a></td>
			<td class="{$article.class}" align="center">
				<a href="/content/edit{$article.id}.html" title="{$LANG.EDIT}"><img src="/templates/_default_/images/icons/edit.png" border="0"/></a>
				{if $user_can_delete}
					<a href="/content/delete{$article.id}.html" title="{$LANG.DELETE}"><img src="/templates/_default_/images/icons/delete.png" border="0"/></a>
				{/if}
			</td>
		</tr>			
	{/foreach}
	</tbody>
</table>

{$pagebar}