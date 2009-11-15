{* ================================================================================ *}
{* ==================== Cписок объ€влений (на доске объ€влений) =================== *}
{* ================================================================================ *}

<div class="board_gallery">
	{if $is_items}
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			{assign var="col" value="1"}	
			{foreach key=tid item=con from=$items}									
				{if $col==1} <tr> {/if} 				
				<td valign="top" width="{$colwidth}%">
					<table width="100%" height="" cellspacing="" cellpadding="0" class="bd_item">
						<tr>
							{if $cfg.photos}
								<td width="30" valign="top">
									<img class="bd_image_small" src="/images/board/small/{$con.file}" border="0" alt="{$con.title}"/>
								</td>
							{/if}
							<td valign="top">
								<div class="bd_title">
									<a href="/board/{$menuid}/read{$con.id}.html" title="{$con.title}">{$con.title}</a>
								</div>
								<div class="bd_text">
									{$con.content}
								</div>																													
								<div class="bd_item_details">
										<span class="bd_item_date">{$con.fpubdate}</span>
										{if $con.city}
											<span class="bd_item_city"><a href="/board/{$menuid}/city/{$con.enc_city}">{$con.city}</a></span>
										{/if}
										{if $con.user}
											<span class="bd_item_user"><a href="/users/0/{$con.user_id}/profile.html">{$con.user}</a></span>
										{/if}
										{if $con.moderator}
											<span class="bd_item_edit"><a href="/board/{$menuid}/edit{$con.id}.html">–едактировать</a></span>
											<span class="bd_item_delete"><a href="/board/{$menuid}/delete{$con.id}.html">”далить</a></span>
										{/if}
								</div>										
							</td>
						</tr>
					</table>
				</td> 
				{if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
			{/foreach}
			{if $col>1} 
				<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
			{/if}
		</table>
		{$pagebar}
	{elseif $cat.id != $root_id}
		<p>ќбъ€влени€ не найдены.</p>
	{/if}
</div>

{if $cat.public && $is_user}
	<table cellpadding="2" cellspacing="0" style="margin-bottom:10px">
		<tr><td><img src="/components/board/images/add.gif" border="0"/></td>
		<td><a style="text-decoration:underline" href="/board/{$menuid}/{$cat.id}/add.html">ƒобавить объ€вление в эту рубрику</a></td></tr>
	</table>
{/if}						
