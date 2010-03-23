{* ================================================================================ *}
{* ================================ �������� ������ =============================== *}
{* ================================================================================ *}

{* ======================= ��������� ������ =============================== *}
{if $cfg.showtitle}
	{if $article.showtitle}
		<h1 class="con_heading">{$article.title}</h1>
	{/if}
{/if}

{* ======================= ���� ���������� =============================== *}
{if $article.showdate} 
	<div class="con_pubdate">
		{$article.pubdate} - <a href="{profile_url login=$article.user_login}">{$article.author}</a>
	</div>
{/if}

{* =============== C��������� ������ (������ �������) ========================= *}
{if $is_pages}
	<div class="con_pt" id="pt">	
		<span class="con_pt_heading">
			<a class="con_pt_hidelink" href="javascript:void;" onClick="{literal}$('#pt_list').toggle();{/literal}">{$LANG.CONTENT}</a>
			{if $cfg.pt_hide} [<a href="javascript:void(0);" onclick="{literal}$('#pt').hide();{/literal}">{$LANG.HIDE}</a>] {/if}
		</span>		
		<div id="pt_list" style="{$pt_disp_style} width:100%">
			<div>
				<ul id="con_pt_list">
				{foreach key=tid item=page from=$pt_pages}
					{if ($tid+1 != $page)}
						{math equation="x + 1" x=$tid assign="key"}
						<li><a href="{$page.url}">{$page.title}</a></li>
					{else}
						<li>{$page.title}</li>
					{/if}
				{/foreach}
				<ul>
			</div>
		</div>
	</div>
{/if}

{* =============== ����� ������ =============================== *}
<div class="con_text" style="overflow:hidden">
    {if $article_image}
        <div class="con_image" style="float:left;margin-top:10px;margin-right:20px;margin-bottom:20px">
            <img src="/images/photos/medium/{$article_image}" border="0" alt="{$article_image}"/>
        </div>
    {/if}
    {$article_content}
</div>

{* ============= ������ �� ���� ������ ======================== *}
{if $cfg.af_showlink && $forum_thread_id}
    <div class="con_forum_link">
        <a href="/forum/thread{$forum_thread_id}.html">{$LANG.DISCUSS_ON_FORUM}</a>
    </div>
{/if}

{* ================ ���� ������ =============================== *}
{if $article.showtags}
	{$tagbar}
{/if}

{* =============== ������� ������ =============================== *}
{if $cfg.rating && $article.canrate}
	<div id="con_rating_block">
		<div>
			<strong>{$LANG.RATING}: </strong><span id="karmapoints">{$karma_points}</span>
			<span style="padding-left:10px;color:#999"><strong>�������:</strong> {$karma_votes}</span>
		</div>
		{if $karma_buttons} 
			<div><strong>{$LANG.RAT_ARTICLE}:</strong> {$karma_buttons}</div>
		{/if}
	</div>
{/if}

{* ======================= ������ ������������ ��. � ����� com_comments_view.tpl =============================== *}

