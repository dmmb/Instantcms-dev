{if $is_last_reg}
	{if $cfg.view_type == 'table'}
        <table cellspacing="5" border="0">
              {foreach key=aid item=usr from=$usrs}
					<tr>
						<td width="20" class="new_user_avatar">{$usr.avatar}</td>
						<td width="">
							<a href="{profile_url login=$usr.login}" class="new_user_link">{$usr.nickname}</a>
						</td>				
					</tr>
              {/foreach}
        </table>
     {/if}
     {if $cfg.view_type == 'list'}
     	{assign var="now" value="0"}
     		{foreach key=aid item=usr from=$usrs}
            	<a href="{profile_url login=$usr.login}" class="new_user_link">{$usr.nickname}</a>
                {math equation="x + 1" x=$now assign="now"}
                {if $now==$total}{else} ,{/if}
            {/foreach}
            <p><strong>{$LANG.LASTREG_TOTAL}:</strong> {$total_all}</p>
     {/if}
{else}            
<p>{$LANG.LASTREG_NOT_DATA}</p>
{/if}
