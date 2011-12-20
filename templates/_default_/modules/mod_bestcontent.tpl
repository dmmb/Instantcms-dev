<table cellspacing="2" cellpadding="4" border="0" width="100%">
{foreach key=tid item=article from=$articles}
	<tr>
		<td class="mod_blog_karma" valign="top">{$article.karma}</td>
		<td valign="top">
			<div>
				<a class="mod_bcon_content" style="font-size:16px" href="{$article.href}">{$article.title|truncate:60}</a> &mdash; 
				<span class="mod_bcon_date">{$article.date}</span> (<a class="mod_bcon_author" href="{$article.authorhref}">{$article.author}</a>)
			</div>
		{if $cfg.showdesc neq 0}
			<div>{$article.description}</div>							  
		{/if}
		</td>
	</tr>								
{/foreach}
{if $cfg.showlink neq 0}
	<tr><td colspan="2">
		<div style="text-align:right">
			<a href="/content/top.html">Полный рейтинг</a> &rarr;
		</div>
	</td></tr>
{/if}
</table>