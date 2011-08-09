{* ================================================================================ *}
{* ====================== Просмотр последних фотографий =========================== *}
{* ================================================================================ *}
{strip}
<h1 class="con_heading">{$LANG.NEW_PHOTO_IN_GALLERY}</h1>
	{if $is_latest_yes}
    	{assign var="col" value="1"}
		<table cellspacing="2" border="0" width="100%">
        {foreach key=tid item=con from=$cons}
            {if $col==1} <tr> {/if}
            <td align="center" valign="middle" class="mod_lp_photo" width="">
			<table width="100%" height="100" cellspacing="0" cellpadding="0">
				<tr>
                	<td align="center">
                    	<div class="mod_lp_titlelink"><a href="/photos/photo{$con.id}.html" title="{$con.title|escape:'html'}">{$con.title}</a>
                        </div>
                    </td>
                </tr>
				<tr>
			  		<td valign="middle" align="center">
						<a href="/photos/photo{$con.id}.html" title="{$con.title|escape:'html'}">
							<img class="photo_thumb_img" src="/images/photos/small/{$con.file}" alt="{$con.title|escape:'html'}" border="0" />
						</a>
                    </td>
				</tr>
				<tr>
					<td align="center">
						<div class="mod_lp_albumlink"><a href="/photos/{$con.album_id}" title="{$con.album|escape:'html'}">{$con.album}</a></div>
						<div class="mod_lp_details">
						<table cellpadding="2" cellspacing="2" align="center" border="0"><tr>
                        <td><img src="/images/icons/date.gif" border="0"/></td>
						<td>{$con.fpubdate}</td>
						<td><img src="/images/icons/comments.gif" border="0"/></td>
						<td><a href="/photos/photo{$con.id}.html#c">{$con.comcount}</a></td>
						</tr></table>
						</div>
					</td>
                </tr>
           </table></div>
			</td> {if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
		{/foreach}
		{if $col>1} 
			<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
		{/if}
		</table>
    {else}
    <p>{$LANG.NO_MATERIALS_TO_SHOW}</p>
    {/if}
{/strip}