{* ================================================================================ *}
{* ========================= Просмотр раздела со статьями ========================= *}
{* ================================================================================ *}

{if !$is_homepage}
    {if $cat.showrss}
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
			<td><h1 class="con_heading">{$pagetitle}</h1></td>
			<td valign="top" style="padding-left:6px">
                        <div class="con_rss_icon">
                            <a href="/rss/content/{$id}/feed.rss" title="{$LANG.RSS}"><img src="/templates/_default_/images/icons/rss.png" border="0" alt="{$LANG.RSS}"/></a>
                        </div>
                </td>
        </table>
    {else}
        <h1 class="con_heading">{$pagetitle}</h1>
    {/if}

    {if $cat.description}
        <div class="con_description">{$cat.description}</div>
    {/if}
{/if}

{if $is_subcats}
	<div class="categorylist">
		{foreach key=tid item=subcat from=$subcats}
            <div class="subcat">
                <a href="{$subcat.url}" class="con_subcat">{$subcat.title}</a> ({$subcat.content_count}{$subtext})
                <div class="con_description">{$subcat.description}</div>
            </div>
		{/foreach}
	</div>
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
                        <img src="/templates/_default_/images/icons/article.png" border="0" class="con_icon"/>
                    </td>
					<td width="" valign="top">
						<div class="con_title">
                            <a href="{$article.url}" class="con_titlelink">{$article.title}</a>
                        </div>
						{if $cat.showdesc}
							<div class="con_desc">
                                {if $article.image}
                                    <div class="con_image">
                                        <img src="/images/photos/small/{$article.image}" border="0" alt="{$article.title}"/>
                                    </div>
                                {/if}
                                {$article.description}
                            </div>
						{/if}
							
						{if $cat.showcomm || $showdate || ($cat.showtags && $article.tagline)}
							<div class="con_details">
								{if $showdate}
									{$article.fpubdate} - <a href="{profile_url login=$article.user_login}" style="color:#666">{$article.author}</a>
								{/if}
								{if $cat.showcomm}
									{if $showdate} | {/if}
                                    <a href="{$article.url}" title="{$LANG.DETAIL}">{$LANG.DETAIL}</a>
									| <a href="{$article.url}#c" title="{$LANG.COMMENTS}">{$article.comments|spellcount:$LANG.COMMENT:$LANG.COMMENT2:$LANG.COMMENT10}</a> 
								{/if}
                                 | {$article.hits|spellcount:$LANG.HIT:$LANG.HIT2:$LANG.HIT10}
								{if $cat.showtags && $article.tagline}
									{if $showdate || $cat.showcomm} <br/> {/if}
									{if $article.tagline} <strong>{$LANG.TAGS}:</strong> {$article.tagline} {/if}
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