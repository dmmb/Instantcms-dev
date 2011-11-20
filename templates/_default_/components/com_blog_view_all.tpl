{* ================================================================================ *}
{* ========================= Список всех блогов =================================== *}
{* ================================================================================ *}

<h1 class="con_heading">{$LANG.BLOGS}</h1>

{if $is_blogs}

    <div class="blog_type_menu">

        {if !$ownertype}
            <span class="blog_type_active">{$LANG.POSTS_RSS}</span>
        {else}
            <a class="blog_type_link" href="/blogs">{$LANG.POSTS_RSS}</a>
        {/if}

         {if $ownertype == 'all'}
            <span class="blog_type_active">{$LANG.ALL_BLOGS} ({$total_blogs})</span>
         {else}
            <a class="blog_type_link" href="/blogs/all.html">{$LANG.ALL_BLOGS}</a>
         {/if}

		{if $single_blogs && $multi_blogs}
			{if $ownertype == 'single'}
				<span class="blog_type_active">{$LANG.PERSONALS} <span class="blog_type_num">({$single_blogs})</span></span>
			{else}
				<a class="blog_type_link" href="/blogs/single.html">{$LANG.PERSONALS}</a>
			{/if}
		{/if}

		{if $single_blogs && $multi_blogs}
			{if $ownertype == 'multi' && $multi_blogs}
				<span class="blog_type_active">{$LANG.COLLECTIVES} <span class="blog_type_num">({$multi_blogs})</span></span>
			{else}
				<a class="blog_type_link" href="/blogs/multi.html">{$LANG.COLLECTIVES}</a>
			{/if}
		{/if}
	</div>

	<table width="100%" cellspacing="0" cellpadding="5" class="blog_full_list">
		{foreach key=tid item=blog from=$blogs}
				<tr>
					<td style="{$blog.style}" class="blog_title_td"><a class="blog_title" href="/blogs/{$blog.seolink}">{$blog.title}</a></td>
					{if $blog.ownertype =='single'}						
						<td width="220" style="{$blog.style}"><a class="blog_user" href="{profile_url login=$blog.author_login}">{$blog.author}</a></td>
					{else}
						<td width="220" style="{$blog.style}">&nbsp;</td>
					{/if}
                    <td width="40" style="{$blog.style}"><span class="blog_posts">{$blog.records}</span></td>
					<td width="40" style="{$blog.style}"><span class="blog_comm">{$blog.comments}</span></td>
					{if $cfg.rss_one}
						<td width="16" style="{$blog.style}">
                            <a class="blog_rss" href="/rss/blogs/{$blog.id}/feed.rss"></a>
                        </td>
					{/if}
					<td width="20" style="{$blog.style}; text-align:center;">{$blog.karma}</td>
				</tr>
		{/foreach}
	</table>
	
	{if $cfg.rss_all}
		<div class="blogs_full_rss">
			<a href="/rss/blogs/all/feed.rss">{$LANG.BLOGS_RSS}</a>
		</div>
	{/if}
	{$pagination}	
{else}
	<p>{$LANG.NOT_ACTIVE_BLOGS}</p>
{/if}