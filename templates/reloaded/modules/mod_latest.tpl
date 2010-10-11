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
                {$article.date} &mdash; <a href="{$article.authorhref}">{$article.author}</a> &mdash; <a href="{$article.href}">{$article.comments|spellcount:$LANG.COMMENT1:$LANG.COMMENT2:$LANG.COMMENT10}</a>
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