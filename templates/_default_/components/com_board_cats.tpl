{* ================================================================================ *}
{* ==================== Cписок [под]рубрик доски объявлений ======================= *}
{* ================================================================================ *}

{if $is_subcats}
	<table class="categorylist" style="margin-bottom:10px" cellspacing="3" width="100%" border="0">
		{assign var="col" value="1"}	
		{foreach key=tid item=cat from=$cats}			
			{if $col==1} <tr> {/if}
				<td width="48" valign="top"><img class="bd_cat_main_icon" src="/images/board/icons/{$cat.icon}" border="0" /></td>
				<td width="" valign="top">
					<div class="bd_cat_main_title"><a href="/board/{$cat.id}">{$cat.title}</a> ({$cat.content_count}{$cat.subtext})</div>					
					{if $cat.description} 
						<div class="bd_cat_main_desc">{$cat.description}</div>
					{/if}					
					<div class="bd_cat_main_obtypes">{$cat.ob_links}</div>
				</td>
			{if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
		{/foreach}
		
		{if $col>1} 
			<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
		{/if}
	</table>
{/if}