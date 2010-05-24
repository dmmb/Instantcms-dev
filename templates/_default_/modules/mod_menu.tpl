<link href="/includes/jquery/treeview/jquery.treeview.css" rel="stylesheet" type="text/css" />

<div>
    <ul id="{$menu}" class="filetree treeview-famfamfam">
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

                        {if !$item.iconurl}
                            {php}
                                $fileicon   = '/includes/jquery/treeview/images/file.gif';
                                $foldericon = '/includes/jquery/treeview/images/folder-closed.gif';
                            {/php}
                        {else}
                            {php}
                                $fileicon   = '/images/menuicons/'.$this->_tpl_vars['item']['iconurl'];
                                $foldericon = '/images/menuicons/'.$this->_tpl_vars['item']['iconurl'];
                            {/php}
                        {/if}

                        {if $menuid != $item.id}
                            {php}
                                $this->_tpl_vars['item']['link'] = '<a target="'.$this->_tpl_vars['item']['target'].'" class="" href="'.$this->_tpl_vars['item']['url'].'" >'.$this->_tpl_vars['item']['title'].'</a>';
                            {/php}
                        {else}
                            {php}
                                $this->_tpl_vars['item']['link'] = $this->_tpl_vars['item']['title'];
                            {/php}
                        {/if}

                {if $item.NSRight - $item.NSLeft == 1}
                    <li style="padding:{php}echo $padding;{/php}">
                                <span class="file" style="background: url({php}echo $fileicon;{/php}) 0 0 no-repeat;">{$item.link}</span>
                    </li>
                {else}
                    <li {if $currentmenu.NSLeft > $item.NSLeft && $currentmenu.NSRight < $item.NSRight}class="open"{/if} style="padding:{php}echo $padding;{/php}">
                                <span class="folder" style="background: url({php}echo $foldericon;{/php}) 0 0 no-repeat;">
                                <a href="javascript:">{$item.title}</a>
                        </span>
                        <ul>
                {/if}
                {assign var="last_level" value=$item.NSLevel}
            {/if}
        {else}
                    {php}$this->_tpl_vars['hide_parent'] = $this->_tpl_vars['item']['id'];{/php}
        {/if}
    {/if}
{/foreach}
    </ul>
</div>

<script type="text/javascript" src="/includes/jquery/treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="/includes/jquery/treeview/menu.js"></script>
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
