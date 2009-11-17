{if $cfg.jtree}
    <div><ul id="{$menu}" class="filetree treeview-famfamfam">
{else}
    <div><ul id="{$menu}" class="{$menu}">
{/if}

{foreach key=key item=item from=$items}
    {if $item.allow_group == -1 || $item.allow_group == $user_group || $is_admin}
        {if $item.published}
            {if $item.parent_id != $hide_parent}

                {if $item.NSLevel > 1} {php}$padding = '0px 0px 0px 15px';{/php} {else} {php}$padding = '0px';{/php} {/if}
                {if $item.NSLevel < $last_level}                    
                    {math equation="x - y" x=$last_level y=$item.NSLevel assign="tail"}
                    {section name=foo start=0 loop=$tail step=1}
                        </ul></li>
                    {/section}
                {/if}
                {if $item.NSRight - $item.NSLeft == 1}
                    <li style="padding:{php}echo $padding;{/php}">
                        <span class="file" style="background: url({$item.fileicon}) 0 0 no-repeat;">{$item.link}</span>
                    </li>
                {else}
                    <li {if $currentmenu.NSLeft > $item.NSLeft && $currentmenu.NSRight < $item.NSRight}class="open"{/if} style="padding:{php}echo $padding;{/php}">
                        <span class="folder" style="background: url({$item.foldericon}) 0 0 no-repeat;">
                           {if $cfg.jtree}                           
                                <a href="javascript:">{$item.title}</a>
                           {else}
                                {$item.link}
                           {/if}
                        </span>
                        <ul>
                {/if}
                {assign var="last_level" value=$item.NSLevel}
            {/if}
        {else}
            {php}$hide_parent = $item['id'];{/php}
        {/if}
    {/if}
{/foreach}
</ul></div>

{if $cfg.jtree}
    <script type="text/javascript">
        {literal}
            $(document).ready(function(){
                 $(".filetree").treeview({
                    animated: true,
                    collapsed: true,
                    unique: false
                    });
                }
            );
        {/literal}
     </script>
{/if}