{if $is_items}
<ul class="new_board_items">
	{foreach key=tid item=item from=$items}
		<li>
            <a href="/board/read{$item.id}.html">{$item.title}</a> &mdash; {$item.pubdate} {if $cfg.showcity}- <span class="board_city">{$item.city}</span>{/if}
		</li>
	{/foreach}
</ul>
{else}
<p>{$LANG.LATESTBOARD_NOT_ADV}</p>
{/if}