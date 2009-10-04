{* ================================================================================ *}
{* =============================== "��� ������" =================================== *}
{* ================================================================================ *}

<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<thead>
		<tr>
			<td width="60"><strong>����</strong></td>
			<td width="16">&nbsp;</td>
			<td><strong>������</strong></td>
			<td width="100"><strong>������</strong></td>
			<td width="16">&nbsp;</td>
			<td width="20">&nbsp;</td>
			<td width="200"><strong>������</strong></td>
			<td width="100" align="center"><strong>��������</strong></td>
		</tr>
	</thead>
	<tbody>
	{foreach key=tid item=article from=$articles}
		<tr>
			<td class="{$article.class}">{$article.pubdate}</td>
			<td class="{$article.class}"><img src="/images/markers/article.png" border="0"></td>
			<td class="{$article.class}"><a href="/content/0/read{$article.id}.html">{$article.title}</a></td>
			<td class="{$article.class}">{$article.status}</td>
			<td class="{$article.class}"><img src="/images/icons/comments.gif" border="0"></td>
			<td class="{$article.class}">{$article.comments}</td>
			<td class="{$article.class}"><a href="/content/0/{$article.category_id}">{$article.category}</a></td>
			<td class="{$article.class}" align="center">
				<a href="/content/edit{$article.id}.html" title="�������������"><img src="/admin/images/actions/edit.gif" border="0"/></a>
				{if $user_can_delete}
					<a href="javascript:deleteArticle({$article.id})" title="�������"><img src="/admin/images/actions/delete.gif" border="0"/></a>					
				{/if}
			</td>
		</tr>			
	{/foreach}
	</tbody>
</table>

{$pagebar}