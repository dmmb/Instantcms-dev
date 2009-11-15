{* ================================================================================ *}
{* ================================ Просмотр статьи =============================== *}
{* ================================================================================ *}

{* ======================= Заголовок статьи =============================== *}
{if $cfg.showtitle}
	{if $article.showtitle}
		<h1 class="con_heading">{$article.title}</h1>
	{/if}
{/if}

{* ======================= Дата публикации =============================== *}
{if $article.showdate} 
	<div class="con_pubdate">
		{$article.pubdate} - <a href="/users/0/{$article.user_id}/profile.html">{$article.author}</a>
	</div>
{/if}

{* =============== Cодержание статьи (список страниц) ========================= *}
{if $is_pages}
	<div class="con_pt" id="pt">	
		<span class="con_pt_heading">
			<a class="con_pt_hidelink" href="javascript:void;" onClick="{literal}$('#pt_list').toggle();{/literal}">Содержание</a>
			{if $cfg.pt_hide} [<a href="javascript:void(0);" onclick="{literal}$('#pt').hide();{/literal}">скрыть</a>] {/if}
		</span>		
		<div id="pt_list" style="{$pt_disp_style} width:100%">
			<div>
				<ul id="con_pt_list">
				{foreach key=tid item=value from=$pt_pages}							
					{if ($tid+1 != $page)}
						{math equation="x + 1" x=$tid assign="key"}
						<li><a href="/content/{$menuid}/read{$article.id}-{$key}.html">{$value}</a></li>
					{else}
						<li>{$value}</li>
					{/if}
				{/foreach}
				<ul>
			</div>
		</div>
	</div>
{/if}

{* =============== Текст статьи =============================== *}
<div class="con_text">{$article_content}</div>

{* ============= Ссылка на тему форума ======================== *}
{if $cfg.af_showlink && $forum_thread_id}
    <div class="con_forum_link">
        <a href="/forum/0/thread{$forum_thread_id}.html">Обсудить на форуме</a>
    </div>
{/if}

{* ================ Теги статьи =============================== *}
{if $article.showtags}
	{$tagbar}
{/if}

{* =============== Рейтинг статьи =============================== *}
{if $cfg.rating && $article.canrate}
	<div id="con_rating_block">
		<div>
			<strong>Рейтинг: </strong><span id="karmapoints">{$karma_points}</span>
			<span style="padding-left:10px;color:#999"><strong>Голосов:</strong> {$karma_votes}</span>
		</div>
		{if $karma_buttons} 
			<div><strong>Оцените статью:</strong> {$karma_buttons}</div> 
		{/if}
	</div>
{/if}

{* ======================= Шаблон комментариев см. в файле com_comments_view.tpl =============================== *}

