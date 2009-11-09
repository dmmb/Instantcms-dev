{* ================================================================================ *}
{* =================== Список статей в архиве материалов ========================== *}
{* ================================================================================ *}

<div class="con_heading">{$heading}</div>

{if $items_count}
    <table width="100%" cellspacing="2" border="0" class="contentlist" >
        {foreach key=id item=item from=$items}
            <tr>
                <td width="20" valign="top">
                    <img src="/images/markers/article.png" border="0" />
                </td>
                <td width="">
                    <a href="/content/{$menuid}/read{$item.id}.html">{$item.title}</a>
                </td>
                <td width="20" align="left">
                    <img src="/images/markers/folder.png" border="0">
                </td>
                <td width="200" align="left">
                    <a href="/content/{$menuid}/{$item.cid}">{$item.category}</a>
                </td>
                <td width="80" align="right">{$item.fdate}</td>
            </tr>
        {/foreach}
    </table>
{else}
    <p>{$LANG.ARHIVE_NO_MATERIALS}</p>
{/if}