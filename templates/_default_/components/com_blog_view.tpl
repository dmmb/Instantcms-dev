{* ================================================================================ *}
{* ========================= Просмотр блога  ====================================== *}
{* ================================================================================ *}

{if $cfg.rss_one}
	{* ============================== Заголовок + RSS ==================================== *}
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td><h1 class="con_heading">{$blog.title}</h1></td>
			<td valign="top" style="padding-left:6px">
					<div class="con_rss_icon">
						<a href="/rss/blogs/{$blog.id}/feed.rss" title="{$LANG.RSS}">
                            <img src="/templates/_default_/images/icons/rss.png" border="0" alt="{$LANG.RSS}"/>
                        </a>
					</div>
			</td>
		</tr>
	</table>
{else}
	{* ============================== Просто заголовок ==================================== *}
	<h1 class="con_heading">{$blog.title}</h1>
{/if}

{* ============================== Автор блога ==================================== *}
{if !$myblog}
	{if $blog.ownertype == 'single'}
		<table cellspacing="0" cellpadding="5" class="blog_desc">
			<tr>
				<td width=""><strong>{$LANG.BLOG_AVTOR}:</strong></td>
				<td width="">{$blog.author}</td>
			</tr>
		</table>
	{else}
		<table cellspacing="0" cellpadding="2" class="blog_desc">
			<tr>
				<td width=""><strong>{$LANG.BLOG_ADMIN}:</strong></td>
				<td width="">{$blog.author}</td>
			</tr>
		</table>
	{/if}
{/if}

{* ============================== Тулбар ==================================== *}
{if $myblog || $is_author || $is_admin}
    <div class="blog_toolbar">
	{if $myblog || $is_admin}
    
		<table cellspacing="0" cellpadding="5">
			<tr>
				{if $on_moderate && ($is_admin || $is_moder || $myblog)}
					<td width="16"><img src="/components/blogs/images/moderate.gif" border="0"/></td>
					<td width=""><a class="blog_moderate_link" href="/blogs/{$blog.id}/moderate.html">{$LANG.MODERATING}</a> ({$on_moderate})</td>
				{/if}						
				<td width="16"><img src="/templates/_default_/images/icons/edit.png" border="0"/></td>
				<td width=""><a href="/blogs/{$blog.id}/newpost{if $cat_id>0}{$cat_id}{/if}.html">{$LANG.NEW_POST}</a></td>
                {if $blog.owner=='user' || $is_moder || $is_admin}
                    <td width="16"><img src="/templates/_default_/images/icons/addcat.png" border="0"/></td>
                    <td width=""><a href="/blogs/{$blog.id}/newcat.html">{$LANG.NEW_CAT}</a></td>
                    {if $cat_id>0}
                        <td width="16"><img src="/templates/_default_/images/icons/editcat.png" border="0"/></td>
                        <td width=""><a href="/blogs/{$blog.id}/editcat{$cat_id}.html">{$LANG.RENAME_CAT}</a></td>
                        <td width="16"><img src="/templates/_default_/images/icons/deletecat.png" border="0"/></td>
                        <td width=""><a href="/blogs/{$blog.id}/delcat{$cat_id}.html">{$LANG.DEL_CAT}</a></td>
                    {/if}
                {/if}
				{if $is_config}
					<td width="16"><img src="/templates/_default_/images/icons/settings.png" border="0"/></td>
					<td width=""><a href="/blogs/{$blog.id}/editblog.html">{$LANG.CONFIG}</a></td>
				{/if}
			</tr>
		</table>
    
	{elseif $is_author}
		<table cellspacing="0" cellpadding="5">
			<tr>
				<td width="16"><img src="/components/blogs/images/record_add.gif" border="0"/></td>
				<td width=""><a href="/blogs/{$blog.id}/newpost{if $cat_id>0}{$cat_id}{/if}.html">{$LANG.NEW_POST}</a></td>
			</tr>
		</table>
	{/if}
    </div>
{/if}

{* ============================== Список рубрик блога ==================================== *}
{if $blogcats != false}
	{$blogcats} {* дизайн списка рубрик см. в файле com_blog_catslist.tpl *}
{/if}

{* ============================== Список записей блога ==================================== *}
{if $is_posts==true}
	<div class="blog_entries">
		{foreach key=tid item=post from=$posts}
			<div class="blog_entry">
				<table width="100%" cellspacing="0" cellpadding="0" class="blog_records">
					<tr>
						<td width="" class="blog_entry_title_td">
							<div class="blog_entry_title"><a href="{$post.url}">{$post.title}</a></div>
							<div class="blog_entry_karma">{$post.karma}</div>
							<div class="blog_entry_info">{$post.author} &rarr; <span class="blog_entry_date">{$post.fpubdate}</span></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="blog_entry_text">{$post.msg}</div>
							<div class="blog_comments">
								{if ($post.comments > 0)}
									<a class="blog_comments_link" href="{$post.url}#c">{$post.comments|spellcount:$LANG.COMMENT:$LANG.COMMENT2:$LANG.COMMENT10}</a>
								{else}
									<a class="blog_comments_link" href="{$post.url}#c">{$LANG.NOT_COMMENTS}</a>
								{/if}
							{if $post.tagline != false}
								 <span class="tagline">{$post.tagline}</span>
							{/if}
							{if $post.user_id == $uid || $is_admin || $is_moder || $myblog}
								<span class="editlinks">
									| <a href="/blogs/{$blog.id}/editpost{$post.id}.html" class="blog_entry_edit">{$LANG.EDIT}</a>
									| <a href="/blogs/{$blog.id}/delpost{$post.id}.html" class="blog_entry_delete">{$LANG.DELETE}</a>
								</span>
							{/if}
							</div>
						</td>
					</tr>
				</table>
			</div>
		{/foreach}		
	</div>	
	
	{$pagination}
{else}
	<p style="clear:both">{$LANG.NOT_POSTS}</p>
{/if}
