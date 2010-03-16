{* ================================================================================ *}
{* ============================= —писок попул€рных статей ========================= *}
{* ================================================================================ *}

<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<thead>
		<tr>
			<td width="70" align="center"><strong>{$LANG.RATING}</strong></td>
			<td width="50" align="center"><strong>{$LANG.VOTES}</strong></td>
			<td width="80"><strong>{$LANG.DATE}</strong></td>
			<td colspan="2"><strong>{$LANG.ARTICLE}</strong></td>
			<td width="16">&nbsp;</td>
			<td width="20">&nbsp;</td>
			<td width="200"><strong>{$LANG.CAT}</strong></td>
		</tr>
	</thead>
	<tbody>
		{foreach key=tid item=article from=$articles}
			<tr>
				<td class="{$article.class}" align="center"><span style="font-size:18px">{$article.karma}</span></td>
				<td class="{$article.class}" align="center"><span style="font-size:14px">{$article.votes}</span></td>
				<td class="{$article.class}">{$article.pubdate}</td>
				<td class="{$article.class}" width="16"><img src="/images/markers/article.png" border="0"></td>
				<td class="{$article.class}"><a href="{$article.url}">{$article.title}</a></td>
				<td class="{$article.class}"><img src="/images/icons/comments.gif" border="0"></td>
				<td class="{$article.class}">{$article.comments}</td>
				<td class="{$article.class}"><a href="/content/0/{$article.category_id}">{$article.category}</a></td>
			</tr>			
		{/foreach}
	</tbody>
</table>