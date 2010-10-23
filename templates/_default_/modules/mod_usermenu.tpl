<div style="text-align:center;margin-bottom:5px;">
    <strong>{$nickname}</strong>
</div>

{if $cfg.avatar}
    <table width="100%" border="0">
        <tr>
            <td valign="top" class="pmenu_avatar"><a href="/users/{$id}/avatar.html">{$avatar}</a></td>
            <td>
{/if}

    <table width="" border="0" cellspacing="0" cellpadding="2" class="pmenu" align="center">
        <tr>
            <td width="27"><img src="/components/users/images/menu/profile.gif" border="0"/></td>
            <td><a href="{profile_url login=$login}">{$LANG.USERMENU_MY_PROFILE}</a></td>
        </tr>
        {if $users_cfg.sw_msg}
            <tr>
                {if $newmsg}
                    <td width="27"><img src="/components/users/images/menu/messages_new.gif" border="0"/></td>
                    <td><a href="/users/{$id}/messages.html" class="new_messages_link">{$LANG.USERMENU_MESS}</a> (<a style="color:red" href="/users/{$id}/messages.html">{$newmsg}</a>)</td>
                {else}
                    <td width="27"><img src="/components/users/images/menu/messages.gif" border="0"/></td>
                    <td><a href="/users/{$id}/messages.html">{$LANG.USERMENU_MESS}</a></td>
                {/if}
            </tr>
        {/if}
        {if $users_cfg.sw_blogs}
        <tr>
            <td width="27"><img src="/components/users/images/menu/blog.gif" border="0"/></td>
            <td><a href="{$blog_href}">{$LANG.USERMENU_MY_BLOG}</a></td>
        </tr>
        {/if}
        <tr>
            <td width="27"><img src="/components/users/images/menu/my.gif" border="0"/></td>
            <td><a href="javascript:" onclick="$('div#mycontent').slideToggle('slow');">{$LANG.USERMENU_MY_CONTENT}</a></td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="mycontent" style="display:none">
                    <table width="" border="0" cellspacing="0" cellpadding="2" align="center">
                        {if $users_cfg.sw_photo}
                        <tr>
                            <td width="27"><img src="/components/users/images/menu/my-photos.gif" border="0"/></td>
                            <td><a href="/users/{$id}/photoalbum.html">{$LANG.USERMENU_PHOTOALBUM}</a></td>
                        </tr>
                        {/if}
                        {if $users_cfg.sw_board}
                        <tr>
                            <td width="27"><img src="/components/users/images/menu/my-board.gif" border="0"/></td>
                            <td><a href="/users/{$id}/board.html">{$LANG.USERMENU_ADV}</a></td>
                        </tr>
                        {/if}
                        {if $is_can_add}
                        <tr>
                            <td width="27"><img src="/components/users/images/menu/my-articles.gif" border="0"/></td>
                            <td><a href="/content/my.html">{$LANG.USERMENU_ARTICLES}</a></td>
                        </tr>
                        {/if}
                    </table>
                </div>
            </td>
        </tr>
        {if $is_admin || $is_editor}
        <tr>
            <td width="27"><img src="/components/users/images/menu/cpanel.gif" border="0"/></td>
            <td><a href="/admin" target="_blank">{$LANG.USERMENU_ADMININTER}</a></td>
        </tr>
        {/if}
        {if $is_can_add}
        <tr>
            <td width="27"><img src="/components/users/images/menu/add.gif" border="0"/></td>
            <td><a href="/content/add.html">{$LANG.USERMENU_ADD_ARTICLE}</a></td>
        </tr>
        {/if}
        <tr>
            <td width="27"><img src="/components/users/images/menu/logout.gif" border="0"/></td>
            <td><a href="/logout" style="color:#990000">{$LANG.USERMENU_EXIT}</a></td>
        </tr>
    </table>

{if $cfg.avatar} </td></tr></table> {/if}