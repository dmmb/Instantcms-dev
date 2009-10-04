{* ================================================================================ *}
{* ========================= Просмотр раздела со статьями ========================= *}
{* ================================================================================ *}

{if $cat.showrss}
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td valign="top"><h1 class="con_heading">{$pagetitle}</h1></td>
			<td valign="top">
				<div class="con_icons">
					<div class="con_rss_icon">
						<a href="/rss/content/{$id}/feed.rss" title="RSS лента"><img src="/images/markers/rssfeed.png" border="0" alt="RSS лента"/></a>
					</div>
				</div>
			</td>
	</table>
{else}
	<h1 class="con_heading">{$pagetitle}</h1>
{/if}

{if $cat.description}
	<div class="con_description">{$cat.description}</div>
{/if}

{if $is_subcats}
	<table class="categorylist" cellspacing="2" width="100%">
		{foreach key=tid item=subcat from=$subcats}
			<tr>
				<td width="20" valign="top"><img src="/images/markers/folder.png" border="0" /></td>
				<td width="" valign="top">
					<a href="{$subcat.url}">{$subcat.title}</a> ({$subcat.content_count}{$subtext})
					<div class="con_description">{$subcat.description}</div>
				</td>
			</tr>
		{/foreach}
	</table>
{/if}

{if $cat.photoalbum}
	<table border="0" cellpadding="0" cellspacing="0" class="con_photos_block" style="float:right">
		<tr>
			<td>{$photos_html}</td>
		</tr>
	</table>
{/if}

{if $is_articles}
	{assign var="col" value="1"}	
	<table class="contentlist" cellspacing="2" border="0" width="">
		{foreach key=tid item=article from=$articles}
			{if $article.user_access}
				{if $col==1} <tr> {/if}
					<td width="20" valign="top">
                        <img src="/images/markers/article.png" border="0" class="con_icon"/>
                    </td>
					<td width="" valign="top">
						<div class="con_title">
                            <a href="{$article.url}" class="con_titlelink">{$article.title}</a>
                        </div>
						{if $cat.showdesc}
							<div class="con_desc">{$article.description}</div>
						{/if}
							
						{if $cat.showcomm || $showdate || ($cat.showtags && $article.tagline)}
							<div class="con_details">
								{if $showdate}
									{$article.fpubdate} - <a href="{profile_url login=$article.user_login}" style="color:#666">{$article.author}</a>
								{/if}
								{if $cat.showcomm}
									{if $showdate} | {/if}
                                    <a href="{$article.url}" title="Подробнее">Подробнее</a>
									| <a href="{$article.url}#c" title="Комментарии">{$article.comments|spellcount:'комментарий':'комментария':'комментариев'}</a>
								{/if}
								{if $cat.showtags && $article.tagline}
									{if $showdate || $cat.showcomm} <br/> {/if}
									{if $article.tagline} <strong>Теги:</strong> {$article.tagline} {/if}
								{/if}
							</div>
						{/if}					
					</td>
					{if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
			{/if}
		{/foreach}
		{if $col>1} 
			<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
		{/if}
	</table>
	{$pagebar}
{/if}