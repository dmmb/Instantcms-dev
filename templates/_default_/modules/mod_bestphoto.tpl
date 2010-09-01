{if $is_best}
<table cellspacing="2" border="0" width="100%">
  {assign var="col" value="1"}	
  {foreach key=tid item=con from=$cons}
  {if $col==1}
  <tr> {/if}
    <td align="center" valign="middle" class="mod_lp_photo" width="">
    	<table width="100%" height="100" cellspacing="0" cellpadding="0">
        	{if $cfg.showtype == 'full'}
        	<tr>
          		<td align="center"><div class="mod_lp_titlelink"><a href="/photos/photo{$con.id}.html" title="{$con.title} ({$con.rating})">{$con.title}</a></div></td>
        	</tr>
            {/if}
        	<tr>
          		<td valign="middle" align="center"><a href="/photos/photo{$con.id}.html" title="{$con.title}"> <img class="photo_thumb_img" src="/images/photos/small/{$con.file}" alt="{$con.title} ({$con.rating})" border="0" /></a></td>
        	</tr>
            {if $cfg.showtype == 'full'}
       		<tr>
          		<td align="center">
                	{if $cfg.showalbum}
            			<div class="mod_lp_albumlink"><a href="/photos/{$con.album_id}" title="{$con.album}">{$con.album}</a></div>
            		{/if}
            		{if $cfg.showcom || $cfg.showdate}
            			<div class="mod_lp_details">
              				<table cellpadding="2" cellspacing="2" align="center" border="0">
                				<tr>
                                	{if $cfg.showdate}
                  						{if $cfg.sort == 'rating'}
                  							<td style="font-weight:bold">{$con.votes}</td>
                  						{else}
                  							<td>{$con.votes}</td>
                  						{/if}
                  					{/if}
                  					{if $cfg.showcom}
                  					<td><img src="/images/icons/comments.gif" border="0"/></td>
                  					<td><a href="/photos/photo{$con.id}.html#c">{$con.comments}</a></td>
                  					{/if}
                               </tr>
              				</table>
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
<div style="text-align:right"><a style="text-decoration:underline" href="/photos/top.html">{$LANG.BESTPHOTO_ALL_BEST_PHOTO}</a> &rarr;</div>
{/if}

{else}
<p>{$LANG.BESTCONTENT_NOT_MATERIALS}</p>
{/if}