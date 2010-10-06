			<table align="center" cellpadding="0" cellspacing="0" border="0"><tr>
			{foreach key=aid item=item from=$items}
				{if $item.id != $menuid}
					<td class="menutd"><a target="{$item.target}" class="menulink" href="{$item.link}" >{$item.title}</a></td>	
				{else}
					<td class="menutd_active"><a target="{$item.target}" class="menulink_active" href="{$item.link}">{$item.title}</a></td>					
				{/if}
            {/foreach}
			</tr></table>
