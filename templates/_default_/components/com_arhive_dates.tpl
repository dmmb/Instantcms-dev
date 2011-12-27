{* ================================================================================ *}
{* =================== Список дат в архиве материалов ============================= *}
{* ================================================================================ *}

<div class="con_heading">{$heading}</div>

{if $items_count}
    <ul class="arhive_list">
        {foreach key=id item=item from=$items}
            <li>
                <a href="/arhive/{$item.year}/{$item.month}">{$item.fdate}</a> ({$item.num})
            </li>
        {/foreach}
    </ul>
{else}
    <p>{$LANG.ARHIVE_NO_MATERIALS}</p>
{/if}