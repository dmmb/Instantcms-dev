{* ================================================================================ *}
{* =================== —писок объ€влений пользовател€ ============================= *}
{* ================================================================================ *}

<div class="con_heading"><a href="{profile_url login=$usr.login}">{$usr.nickname}</a> &rarr; {$LANG.ADVS}</div>
{if $is_con}
			<div class="board_gallery">
				<table width="100%" cellpadding="0" cellspacing="0" border="0">
                {foreach key=tid item=con from=$cons}	
				 <tr>
                    <td valign="top" width="">
						<table width="100%" height="" cellspacing="" cellpadding="0" class="bd_item">
							<tr>
								<td width="64" valign="top">
										<img class="bd_image_small" src="/images/board/small/{$con.file}" border="0" alt="{$con.title}'"/>
								</td>
								<td valign="top">
									<div class="bd_title"><a href="/board/read{$con.id}.html" title="{$con.title}">{$con.title}</a></div>
									<div class="bd_text">{$con.content}</div>	
									<div class="bd_item_details">
											{if $con.published}
												<span class="bd_item_status_ok">{$LANG.PUBLISHED}</span>
											{elseif !$con.published && $con.is_overdue}
												<span class="bd_item_status_bad">{$LANG.ADV_EXTEND}</span>
											{else}
												<span class="bd_item_status_bad">{$LANG.WAIT_MODER}</span>
											{/if}											
											<span class="bd_item_date">{$con.pubdate}</span>
											{if $con.city}
												<span class="bd_item_city">{$con.city}</span>
											{/if}
											{if $con.moderator}
												<span class="bd_item_edit"><a href="/board/edit{$con.id}.html">{$LANG.EDIT}</a></span>
												<span class="bd_item_delete"><a href="/board/delete{$con.id}.html">{$LANG.DELETE}</a></span>
											{/if}
									</div>									
								</td>
							</tr>
						</table>
                      </td>
                  </tr>
                {/foreach}
				</table>
			</div>		
{$pagebar}
{else}
<p>{$LANG.NOT_ADVS}</p>
{/if}