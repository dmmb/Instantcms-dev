{if $is_targeting}
	{if $is_tags}
		<div>
			{foreach key=tid item=tag from=$tags}
			
					<a class="tag" href="/search/tag/{$tag.title|urlencode}" style="padding:2px; font-size: {$tag.fontsize}px">{$tag.title|ucfirst}</a>
						
			{/foreach}
		</div>
				
	{else} 
		<p>Нет тегов для отображения</p>
	{/if}
			
{else}
	<p>Не выбраны источники тегов для показа.</p>
{/if}

