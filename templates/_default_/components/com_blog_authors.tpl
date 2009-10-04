{* ================================================================================ *}
{* ========================= Список авторов блога ================================= *}
{* ================================================================================ *}

<h1 class="con_heading">{$blog} - Авторы</h1>
    
    {assign var="col" value="1"}
    {assign var="maxcols" value="5"}

{if $is_authors}
    <table width="100%" cellpadding="5" cellspacing="0" border="0">
        {foreach key=id item=author from=$authors}
			{if $col==1} <tr> {/if}
                <td align="center" valign="middle">
                    <table width="100%" cellpadding="1" cellspacing="0" border="0" align="center">
                        <tr><td align="center"><a href="{profile_url login=$author.user_login}"><img src="/images/users/avatars/small/{$author.avatar}" border="0" /></a></td></tr>
                        <tr><td align="center"><a href="{profile_url login=$author.user_login}">{$author.nickname}</a></tr>
                    </table>
                </td>
            {if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
        {/foreach}
        {if $col>1}
			<td colspan="{math equation="x-y+1" x=$maxcols y=$col}">&nbsp;</td></tr>
		{/if}
    </table>
{else}
    <span style="padding:5px">
        Нет авторов.
    </span>
{/if}