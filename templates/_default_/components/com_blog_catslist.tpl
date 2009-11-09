{* ================================================================================ *}
{* ========================= Список рубрик блога ================================== *}
{* ================================================================================ *}

<div class="blog_catlist">

	<div class="blog_cat">
		<table cellspacing="0" cellpadding="0"><tr><td><strong>{$LANG.CATS}:</strong></div></td></tr></table>
	</div>

	<div class="blog_cat">
		<table cellspacing="0" cellpadding="1">
			<tr>
				<td width="16"><img src="/components/blog/images/cat.gif" border="0" /></td>
				{if $cat_id!=-1}
					<td><a href="/blogs/{$menuid}/{$bloglink}">{$LANG.ALL_CATS}</a> <span style="color:#666666">({$rootposts})</span></td>
				{else}
					<td>{$LANG.ALL_CATS} <span style="color:#666666">({$rootposts})</span></td>
				{/if}
			</tr>
		</table>
	</div>
	
	{foreach key=tid item=cat from=$cats}
		<div class="blog_cat">
			<table cellspacing="0" cellpadding="2">
				<tr>
					<td width="16"><img src="/components/blog/images/cat.gif" border="0" /></td>
					{if $cat_id!=$cat.id}
						<td><a href="/blogs/{$menuid}/{$bloglink}/cat-{$cat.id}">{$cat.title}</a> <span style="color:#666666">({$cat.num})</span></td>
					{else}
						<td>{$cat.title} <span style="color:#666666">({$cat.num})</span></td>
					{/if}
				</tr>
			</table>
		</div>		
	{/foreach}

</div>