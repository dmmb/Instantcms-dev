{if $is_com}
            {foreach key=aid item=comment from=$comments}
            		<div class="mod_com_line">
                        	<a class="mod_com_userlink" href="{$comment.user_url}">{$comment.author}</a> &rarr; 
                        	<a class="mod_com_link" href="{$comment.link}">{$comment.text}</a> ({$comment.fpubdate})
                   </div>
            {/foreach}
            {if $cfg.showrss}
                <a href="/rss/comments/all/feed.rss" class="mod_latest_rss">{$LANG.COMMENTS_RSS}</a>
            {/if}
{else}            
<p>{$LANG.COMMENTS_NOT_COMM}</p>
{/if}