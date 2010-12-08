{strip}
    <table class="module" width="100%" cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td class="moduletitle">
                {$LANG.FRIEND_ON_SITE} ({$total})
            </td>
        </tr>
        <tr>
            <td class="modulebody">
            {if $total}
            	{if $cfg.view_type == 'table'}
                    {foreach key=tid item=frien from=$friends}
                        <div align="center">{$frien.avatar}</div>
                        <div align="center">{$frien.user_link}</div>
                    {/foreach}
                 {/if}
                 {if $cfg.view_type == 'list'}
                    {assign var="now" value="0"}
                        {foreach key=tid item=frien from=$friends}
                            {$frien.user_link}
                            {math equation="x + 1" x=$now assign="now"}
                            {if $now==$total}{else}, {/if}
                        {/foreach}
                 {/if}
            {else}
                <div align="center">{$LANG.FRIEND_NO_SITE}</div>
            {/if} 
            </td>
        </tr>
    </tbody>
    </table>
{/strip}