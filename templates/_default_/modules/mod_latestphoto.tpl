{if $is_photo}
        <table cellspacing="2" border="0" width="100%">
              {assign var="col" value="1"}	
              {foreach key=tid item=con from=$photos}
              {if $col==1}
              <tr> {/if}
                <td align="center" valign="middle" class="mod_lp_photo" width="">
				<table width="100%" height="100" cellspacing="0" cellpadding="0">
				{if $cfg.showtype == 'full'}
					<tr><td align="center"><div class="mod_lp_titlelink"><a href="/photos/photo{$con.id}.html" title="{$con.title|escape:'html'}">{$con.title|truncate:18}</a></div></td></tr>
				{/if}
				<tr>
					  <td valign="middle" align="center">
						<a href="/photos/photo{$con.id}.html" title="{$con.title|escape:'html'}">
							<img class="photo_thumb_img" src="/images/photos/small/{$con.file}" alt="{$con.title|escape:'html'}" border="0" />
						</a>
				</td></tr>
				{if $cfg.showtype == 'full'}
					<tr>
					<td align="center">
						{if $cfg.showalbum}
							<div class="mod_lp_albumlink"><a href="/photos/{$con.album_id}" title="{$con.album|escape:'html'}">{$con.album|truncate:18}</a></div>
						{/if}
						{if $cfg.showcom || $cfg.showdate}
							<div class="mod_lp_details">
							<table cellpadding="2" cellspacing="2" align="center" border="0"><tr>
								{if $cfg.showdate}
									<td><img src="/images/icons/date.gif" border="0"/></td>
									<td>{$con.fpubdate}</td>
								{/if}
								{if $cfg.showcom}
									<td><img src="/images/icons/comments.gif" border="0"/></td>
									<td><a href="/photos/photo{$con.id}.html#c">{$con.comments}</a></td>
								{/if}
							</tr></table>
							</div>
						{/if}
					</td>
					</tr>
				{/if}
				</table>
				
				</td> 
                {if $col==$cfg.maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
  			{/foreach}
			</table>
			{if $cfg.showmore}
				<div><a href="/photos/latest.html">{$LANG.LATESTPHOTO_ALLNEW}</a> &rarr;</div>
			{/if}
{else}
<p>{$LANG.LATESTPHOTO_NO_MATERIAL}</p>
{/if}