{if $is_blog}
<table width="100%" cellspacing="0" cellpadding="5" border="0" >
{foreach key=tid item=post from=$posts}
	<tr>
		<td>
			<div>
				<a class="mod_blog_userlink" href="{$post.bloghref}">{$post.blog}</a> &rarr; 
				<a class="mod_blog_link" href="{$post.href}">{$post.title}</a> ({$post.fpubdate})
			</div>
		</td>
	</tr>
{/foreach}
</table>
{if $cfg.showrss}
    <a href="/rss/blogs/all/feed.rss" class="mod_latest_rss">{$LANG.LATESTBLOGS_RSS}</a>
{/if}
{else}            
<p>{$LANG.LATESTBLOGS_NOT_POSTS}</p>
{/if}