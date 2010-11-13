{if $is_best}
<table cellspacing="2" cellpadding="2" border="0" width="100%">
  {assign var="col" value="1"}	
  {foreach key=tid item=con from=$cons}
  	{if $col==1}  <tr> {/if}
    <td align="center" valign="bottom" class="mod_lp_photo" width="{math equation="100 / z" z=$cfg.maxcols}%">

        	{if $cfg.showtype == 'full'}
	          	<div class="mod_lp_titlelink"><a href="/photos/photo{$con.id}.html" title="{$con.title} ({$con.rating})">{$con.title}</a></div>
            {/if}
        	<a href="/photos/photo{$con.id}.html" title="{$con.title}"> <img class="photo_thumb_img" src="/images/photos/small/{$con.file}" alt="{$con.title} ({$con.rating})" border="0" /></a>

            {if $cfg.showtype == 'full'}
                	{if $cfg.showalbum}
            			<div class="mod_lp_albumlink"><a href="/photos/{$con.album_id}" title="{$con.album}">{$con.album}</a></div>
            		{/if}
            		{if $cfg.showcom || $cfg.showdate || $cfg.showrating}
            			<div class="mod_lp_details">
                               	  {if $cfg.showrating}
                  						{if $cfg.sort == 'rating'}
                                          	<strong>{$con.votes}</strong>
                  						{else}
                                              {$con.votes}
                  						{/if}
                  					{/if}
                  					{if $cfg.showcom}
										<a class="blog_comments_link" href="/photos/photo{$con.id}.html#c">{$con.comments}</a>
                  					{/if}
                                {if $cfg.showdate}
                                <div>
                                	<span class="blog_entry_date">{$con.pubdate}</span>
                                </div>
                  					{/if}
            			</div>
            		{/if}
        {/if}
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