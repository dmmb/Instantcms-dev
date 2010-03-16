<table width="100%" cellspacing="0" cellpadding="5" border="0" >
{foreach key=tid item=post from=$posts}
	<tr>
		<td class="mod_blog_karma">{$post.karma}</td>
		<td>
			<div>
				<a class="mod_blog_userlink" href="{$post.bloghref}">{$post.blog}</a> &rarr; 
				<a class="mod_blog_link" href="{$post.href}">{$post.title}</a> ({$post.date})
			</div>
		</td>
	</tr>
{/foreach}
</table>