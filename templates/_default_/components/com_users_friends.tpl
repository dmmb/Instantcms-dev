{* ================================================================================ *}
{* ============================ Äðóçüÿ ============================================ *}
{* ================================================================================ *}

{assign var="col" value="1"}
<table width="" cellpadding="10" cellspacing="0" border="0" class="usr_friends_list" align="left">
  {foreach key=tid item=friend from=$friends}
  {if $col==1}<tr>{/if}
            <td align="center" valign="top">
				<div align="center"><a href="{profile_url login=$friend.login}">{$friend.nickname}</a></div>
				<div align="center"><a href="{profile_url login=$friend.login}">{$friend.avatar}</a></div>
				<div align="center">{$friend.flogdate}</div>
            </td>
              
      {if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
  {/foreach}
  {if $col>1}<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>{/if}
</table>
        {if $not_all && $short}
        <div style="text-align:right"><a href="/users/{$user_id}/friendlist.html" class="usr_friendslink">{$LANG.ALL_FRIENDS}</a> &rarr;</div>
        {/if}