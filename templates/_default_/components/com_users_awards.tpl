{* ================================================================================ *}
{* =================== Список наград пользователя ================================= *}
{* ================================================================================ *}
	{foreach key=tid item=aw from=$aws}
        <div class="usr_award_block">
          <table style="width:100%; margin-bottom:2px;" cellspacing="2">
            <tr>
              <td class="usr_com_title"><strong>{$aw.title}</strong> 
              {if $aw.award_id > 0}
              	<td width="20" class="usr_awlist_link"><a href="/users/awardslist.html">?</a></td>
              {else}
              {if $is_admin}
              	[<a href="/users/delaward{$aw.id}.html">{$LANG.DELETE}</a>]
              {/if}
              </td>
              {/if}
            </tr>
            <tr>
            {if $aw.award_id > 0}
              <td class="usr_com_body" colspan="2">
            {else}
              <td class="usr_com_body">
            {/if}
                <table border="0" cellpadding="5" cellspacing="0">
                  <tr>
                    <td valign="top"><img src="/images/users/awards/{$aw.imageurl}" border="0" alt="{$aw.title|escape:'html'}"/></td>
                    <td valign="top">{$aw.description}
                      <div class="usr_award_date">{$aw.pubdate}</div></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
{/foreach}