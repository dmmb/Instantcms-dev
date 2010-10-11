{if $is_blog}
    
    {foreach key=tid item=post from=$posts}
        <div class="mod_latest_entry">

            <div class="mod_latest_image">
                {$post.image}
            </div>

            <a class="mod_latest_title" href="{$post.href}">{$post.title}</a>

            <div class="mod_latest_date">
                {$post.fpubdate} &mdash; <a href="{$post.bloghref}">{$post.blog}</a> &mdash; <a href="{$post.href}">{$post.comments|spellcount:$LANG.COMMENT1:$LANG.COMMENT2:$LANG.COMMENT10}</a>
            </div>

        </div>
    {/foreach}

    {if $cfg.showrss}
        <div class="mod_latest_rss">
            <a href="/rss/blogs/all/feed.rss">{$LANG.LATESTBLOGS_RSS}</a>
        </div>
    {/if}

{else}            
    <p>{$LANG.LATESTBLOGS_NOT_POSTS}</p>
{/if}