<ul class="new_board_items">
	{foreach key=tid item=item from=$items}
		<li>
			<a href="/board/{$menuid}/read{$item.id}.html">{$item.title}</a> &mdash; {$item.pubdate} {if $cfg.showcity}- {$item.city}{/if}
		</li>
	{/foreach}
</ul>
