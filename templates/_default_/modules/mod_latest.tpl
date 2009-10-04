{foreach key=aid item=article from=$articles}	
	<div class="mod_latest_entry">	
	    <a class="mod_latest_title" href="{$article.href}">{$article.title}</a>
		{if $cfg.showdate}<div class="mod_latest_date"><a href="{$article.authorhref}">{$article.author}</a> &mdash; {$article.date}</div>{/if}
		{if $cfg.showdesc}<div class="mod_latest_desc">{$article.description}</div>{/if}

        {if $cfg.showcom}
            <div class="mod_latest_comments"><a href="{$article.href}">{$article.comments|spellcount:'комментарий':'комментария':'комментариев'}</a></div>
        {/if}
	</div>
{/foreach}
{if $cfg.showrss}
	<div class="mod_latest_rss">
		<a href="/rss/content/{$rssid}/feed.rss">Лента материалов</a>
	</div>
{/if}