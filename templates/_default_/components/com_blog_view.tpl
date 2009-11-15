{* ================================================================================ *}
{* ========================= Просмотр блога  ====================================== *}
{* ================================================================================ *}

{if $cfg.rss_one}
	{* ============================== Заголовок + RSS ==================================== *}
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="top"><h1 class="con_heading">{$blog.title}</h1></td>
			<td valign="top">
				<div class="con_icons">
					<div class="con_rss_icon">
						<a href="/rss/blog/{$blog.id}/feed.rss" title="RSS лента"><img src="/images/markers/rssfeed.png" border="0" alt="RSS лента"/></a>
					</div>
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
				<td width=""><strong>Автор блога:</strong></td>
				<td width="">{$blog.author}</td>
			</tr>
		</table>
	{else}
		<table cellspacing="0" cellpadding="2" class="blog_desc">
			<tr>
				<td width=""><strong>Администратор блога:</strong></td>
				<td width="">{$blog.author} {if $authors_status}({$authors_status}){/if}</td>
			</tr>
		</table>
	{/if}
{/if}

{* ============================== Тулбар ==================================== *}
{if $myblog || $is_author || $is_admin}		
	{if $myblog || $is_admin}
		<table cellspacing="0" cellpadding="5" class="blog_toolbar">
			<tr>
				{if $on_moderate && $is_admin}
					<td width="16"><img src="/components/blog/images/moderate.gif" border="0"/></td>
					<td width=""><a class="blog_moderate_link" href="/blogs/{$menuid}/{$blog.id}/moderate.html">Модерация</a> ({$on_moderate})</td>
				{/if}						
				<td width="16"><img src="/components/blog/images/record_add.gif" border="0"/></td>
				<td width=""><a href="/blogs/{$menuid}/{$blog.id}/newpost.html">Новая запись</a></td>
				<td width="16"><img src="/components/blog/images/cat_add.gif" border="0"/></td>
				<td width=""><a href="/blogs/{$menuid}/{$blog.id}/newcat.html">Новая рубрика</a></td>
				{if $cat_id>0}
					<td width="16"><img src="/components/blog/images/cat_edit.gif" border="0"/></td>
					<td width=""><a href="/blogs/{$menuid}/{$blog.id}/editcat{$cat_id}.html">Переименовать рубрику</a></td>
					<td width="16"><img src="/components/blog/images/cat_delete.gif" border="0"/></td>
					<td width=""><a href="/blogs/{$menuid}/{$blog.id}/delcat{$cat_id}.html">Удалить рубрику</a></td>
				{/if}
				{if $is_config}
					<td width="16"><img src="/components/blog/images/blog_edit.gif" border="0"/></td>
					<td width=""><a href="/blogs/{$menuid}/{$blog.id}/editblog.html">Настройки</a></td>
				{/if}
			</tr>
		</table>
	{elseif $is_author}
		<table cellspacing="0" cellpadding="5" class="blog_toolbar">
			<tr>
				<td width="16"><img src="/components/blog/images/record_add.gif" border="0"/></td>
				<td width=""><a href="/blogs/{$menuid}/{$blog.id}/newpost.html">Новая запись</a></td>
			</tr>
		</table>
	{/if}
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
				<table width="100%" cellspacing="0" cellpadding="5" class="blog_records">
					<tr>
						<td width="" class="blog_entry_title_td">
							<div class="blog_entry_title"><a href="{$post.url}">{$post.title}</a></div>
							<div class="blog_entry_karma">&uarr; {$post.karma} &darr;</div>
							<div class="blog_entry_info"><a href="/users/{$menuid}/{$post.author_id}/profile.html">{$post.author}</a> &rarr; <span class="blog_entry_date">{$post.fpubdate}</span></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="blog_entry_text">{$post.msg}</div>
							<div class="blog_comments">
								{if ($post.comments > 0)}
									<a class="blog_comments_link" href="{$post.url}#c">{$post.comments|spellcount:'комментарий':'комментария':'комментариев'}</a>
								{else}
									<a class="blog_comments_link" href="{$post.url}#c">Нет комментариев</a>
								{/if}
							{if $post.tagline != false}
								 <span class="tagline">{$post.tagline}</span>
							{/if}
							{if $myblog || $post.user_id == $uid || $is_admin}
								<span class="editlinks">
									| <a href="/blogs/{$menuid}/{$blog.id}/editpost{$post.id}.html" class="blog_entry_edit">Редактировать</a>
									| <a href="/blogs/{$menuid}/{$blog.id}/delpost{$post.id}.html" class="blog_entry_delete">Удалить</a>				
								</span>
							{/if}
							</div>
						</td>
					</tr>
				</table>
			</div>
		{/foreach}		
	</div>	
	<script type="text/javascript">{$round_corners_js}</script>
	
	{$pagination}
{else}
	<p style="clear:both">Нет записей.</p>
{/if}
