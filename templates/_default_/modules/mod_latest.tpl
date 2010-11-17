{if $is_con}
{if $cfg.is_pag}<script type="text/javascript" src="/modules/mod_latest/js/latest.js" ></script>{/if}
{if !$is_ajax}<div id="module_ajax_{$module_id}">{/if}

{foreach key=aid item=article from=$articles}
	<div class="mod_latest_entry">
        {if $article.image}
            <div class="mod_latest_image">
                <img src="/images/photos/small/{$article.image}" border="0" width="32" height="32" alt="{$article.title}"/>
            </div>
        {/if}
	    <a class="mod_latest_title" href="{$article.href}">{$article.title}</a>
		{if $cfg.showdate}
            <div class="mod_latest_date">
                {$article.date} - <a href="{$article.authorhref}">{$article.author}</a>{if $cfg.showcom} - <a href="{$article.href}" title="{$article.comments|spellcount:$LANG.COMMENT1:$LANG.COMMENT2:$LANG.COMMENT10}" class="mod_latest_comments">{$article.comments}</a> - <span class="mod_latest_hits">{$article.hits}</span>{/if}
            </div>
        {/if}

        {if $cfg.showdesc}
            <div class="mod_latest_desc" style="overflow:hidden">                
                {$article.description}
            </div>
        {/if}

        {if $cfg.showcom}
        {/if}
	</div>
{/foreach}
{if $cfg.showrss}
	<div class="mod_latest_rss">
		<a href="/rss/content/{$rssid}/feed.rss">{$LANG.LATEST_RSS}</a>
	</div>
{/if}
{$pagebar}
{if !$is_ajax}</div>{/if}
{else}
    <p>{$LANG.LATEST_NOT_MATERIAL}</p>
{/if}