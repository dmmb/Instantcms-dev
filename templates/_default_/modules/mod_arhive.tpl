{foreach key=tid item=item from=$items}
	<div class="arhive_month"><a href="/arhive/{$item.year}/{$item.month}">{$item.fdate}</a>({$item.num})</div>
{/foreach}
