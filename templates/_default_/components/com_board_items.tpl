{* ================================================================================ *}
{* ==================== Cписок объ€влений (на доске объ€влений) =================== *}
{* ================================================================================ *}
{$order_form}
<div class="board_gallery">
	{if $items}
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			{assign var="col" value="1"}	
			{foreach key=tid item=con from=$items}									
				{if $col==1} <tr> {/if} 				
				<td valign="top" width="{$colwidth}%">
                    <div class="bd_item{if $con.is_vip}_vip{/if}">
					<table width="100%" height="" cellspacing="" cellpadding="0">
						<tr>
							{if $cfg.photos}
								<td width="30" valign="top">
									<img class="bd_image_small" src="/images/board/small/{$con.file}" border="0" alt="{$con.title|escape:'html'}"/>
								</td>
							{/if}
							<td valign="top">
								<div class="bd_title">
									<a href="/board/read{$con.id}.html" title="{$con.title|escape:'html'}">{$con.title}</a>
								</div>
								<div class="bd_text">
									{$con.content}
								</div>																													
								<div class="bd_item_details">
                                		{if $cat.showdate}
										<span class="bd_item_date">{$con.fpubdate}</span>
                                        {/if}
										{if $con.city}
											<span class="bd_item_city"><a href="/board/city/{$con.enc_city|escape:'html'}">{$con.city}</a></span>
										{/if}
										{if $con.user}
											<span class="bd_item_user"><a href="{profile_url login=$con.user_login}">{$con.user}</a></span>
										{/if}
										{if $con.moderator}
											<span class="bd_item_edit"><a href="/board/edit{$con.id}.html">{$LANG.EDIT}</a></span>
											<span class="bd_item_delete"><a href="/board/delete{$con.id}.html">{$LANG.DELETE}</a></span>
										{/if}
								</div>										
							</td>
						</tr>
					</table>
                    </div>
				</td> 
				{if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
			{/foreach}
			{if $col>1} 
				<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
			{/if}
		</table>
		{$pagebar}
	{elseif $cat.id != $root_id}
		<p>{$LANG.ADVS_NOT_FOUND}</p>
	{/if}
</div>						
