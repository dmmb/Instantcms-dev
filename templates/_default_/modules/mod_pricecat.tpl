{if $is_item}
	<table cellspacing="2" border="0">
		{foreach key=tid item=item from=$items}
			<tr>
			
				{if $item.is_icon}
					<td width="12" valign="top"><img src="{$cfg.icon}" border="0" /></td>
				{/if}
		
				<td width="" valign="top">
			
					{if !$item.is_current}

						<a href="{$item.link}" class="mod_pcat_link">
				
					{else}
						<div class="mod_pcat_current">
					{/if}
				
					{$item.title}
				
					{if !$item.is_current}
						</a>
					{else}
						</div>
					{/if}	
				</td>			
			</tr>
		
			{if $cfg.showdesc}
				<tr>
					{if $item.is_icon}
						<td>&nbsp;</td>
					{/if}
		
					<td><div class="mod_pcat_desc">{$item.description}</div></td>
				</tr>
			{/if}
		{/foreach}
	</table>
{else}
	<p>Нет категорий для отображения.</p>
{/if}