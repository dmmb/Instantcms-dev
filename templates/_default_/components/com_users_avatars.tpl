{* ================================================================================ *}
{* ==================== Список аватаров, доступных для выбора ===================== *}
{* ================================================================================ *}

<div class="con_heading">Выберите аватар</div>
<div class="con_text">Щелкните по аватару, чтобы установить его в свой профиль:</div>

<table class="" style="margin-top:15px;margin-bottom:15px;" cellpadding="5" width="100%" border="0">
    {assign var="col" value="1"}
    {foreach key=tid item=avatar from=$avatars}
        {if $col==1} <tr> {/if}
            <td width="25%" valign="middle" align="center">
                    <a href="/users/{$menuid}/{$userid}/select-avatar/{$avatar|urlencode}" title="Выбрать аватар">
                        <img src="{$avatars_dir}/{$avatar}" border="0" />
                    </a>
            </td>
        {if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
    {/foreach}

    {if $col>1}
        <td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
    {/if}
</table>

{$pagebar}