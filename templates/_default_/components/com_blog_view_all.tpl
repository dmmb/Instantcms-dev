{* ================================================================================ *}
{* ========================= Список всех блогов =================================== *}
{* ================================================================================ *}

<h1 class="con_heading">Блоги</h1>

{if $is_blogs}

	<div class="blog_type_menu">

        {if !$ownertype}
            <span class="blog_type_active">Лента записей</span>
        {else}
            <a class="blog_type_link" href="/blogs/{$menuid}/latest.html">Лента записей</a>
        {/if}

         {if $ownertype == 'all'}
            <span class="blog_type_active">Все блоги</span>
         {else}
            <a class="blog_type_link" href="/blogs/{$menuid}/all.html">Все блоги</a>
         {/if}
		 
		{if $single_blogs && $multi_blogs} 
			{if $ownertype == 'single'}
				<span class="blog_type_active">Персональные <span class="blog_type_num">({$single_blogs})</span></span>
			{else}
				<a class="blog_type_link" href="/blogs/{$menuid}/single.html">Персональные <span class="blog_type_num">({$single_blogs})</span></a>
			{/if} 
		{/if}
		
		{if $single_blogs && $multi_blogs} 
			{if $ownertype == 'multi' && $multi_blogs}
				<span class="blog_type_active">Коллективные <span class="blog_type_num">({$multi_blogs})</span></span>
			{else}
				<a class="blog_type_link" href="/blogs/{$menuid}/multi.html">Коллективные <span class="blog_type_num">({$multi_blogs})</span></a>
			{/if}
		{/if}		
	</div>
	
	<table width="100%" cellspacing="0" cellpadding="5">
		{foreach key=tid item=blog from=$blogs}
			{if $blog.can_view || $is_admin}
				<tr>
					<td width="16" style="{$blog.style}"><img src="/components/blog/images/blog.gif" border="0"/></td>
					<td width="" style="{$blog.style}"><a href="/blogs/{$menuid}/{$blog.seolink}">{$blog.title}</a></td>
					{if $blog.ownertype =='single'}
						<td width="16" style="{$blog.style}"><img src="/components/blog/images/user.gif" alt="Автор" border="0"/></td>
						<td width="200" style="{$blog.style}"><a href="/users/{$menuid}/{$blog.uid}/profile.html">{$blog.author}</a></td>
					{else}
						<td width="16" style="{$blog.style}">&nbsp;</td>
						<td width="200" style="{$blog.style}">&nbsp;</td>				
					{/if}
					<td width="16" style="{$blog.style}"><img src="/components/blog/images/records.gif" alt="Записей в блоге" border="0"/></td>
					<td width="20" style="{$blog.style}">{$blog.records}</td>
					<td width="16" style="{$blog.style}"><img src="/components/blog/images/comment.gif" alt="Комментариев в блоге" border="0"/></td>
					<td width="20" style="{$blog.style}">{$blog.comments}</td>
					{if $cfg.rss_one}
						<td width="16" style="{$blog.style}"><a href="/rss/blog/{$blog.id}/feed.rss"><img src="/images/markers/rssfeed.png" alt="RSS лента блога" border="0"/></a></td>
					{/if}
					<td width="20" style="{$blog.style}; text-align:center;">{$blog.karma}</td>
				</tr>
			{/if}
		{/foreach}
	</table>
	
	{if $cfg.rss_all}
		<div style="margin-top:10px;padding:5px">
			<a style="background:url(/images/markers/rssfeed.png) no-repeat left center; padding-left:25px" href="/rss/blog/all/feed.rss">RSS-лента блогов</a>
		</div>
	{/if}
		
{else}
	<p>Нет активных блогов</p>
{/if}