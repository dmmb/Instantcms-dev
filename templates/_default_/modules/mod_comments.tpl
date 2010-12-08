{if $is_com}
            {foreach key=aid item=comment from=$comments}
                <div class="mod_com_line">
                        <a class="mod_com_link" href="{$comment.link}">{$comment.text}</a> {if $cfg.showtarg} <strong>({$comment.rating})</strong>{/if}
                </div>
                <div class="mod_com_details">
                    <a class="mod_com_userlink" href="{$comment.user_url}">{$comment.author}</a> {$comment.fpubdate}<br/><a class="mod_com_targetlink" href="{$comment.target_link}">{$comment.target_title}</a>
                </div>
            {/foreach}
            {if $cfg.showrss}
                <div style="margin-top:15px">
                    <a href="/rss/comments/all/feed.rss" class="mod_latest_rss">{$LANG.COMMENTS_RSS}</a>
                </div>
            {/if}
            <div style="margin-top:5px">
                <a href="/comments" class="mod_com_all">{$LANG.COMMENTS_ALL}</a>
            </div>
{else}            
<p>{$LANG.COMMENTS_NOT_COMM}</p>
{/if}