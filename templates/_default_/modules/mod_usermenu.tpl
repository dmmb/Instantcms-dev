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
            <td><a href="{profile_url login=$login}">Мой профиль</a></td>
        </tr>
        {if $users_cfg.sw_msg}
            <tr>
                {if $newmsg}
                    <td width="27"><img src="/components/users/images/menu/messages_new.gif" border="0"/></td>
                    <td><a href="/users/{$id}/messages.html" class="new_messages_link">Сообщения</a>{$newmsg}</td>
                {else}
                    <td width="27"><img src="/components/users/images/menu/messages.gif" border="0"/></td>
                    <td><a href="/users/{$id}/messages.html">Сообщения</a></td>
                {/if}
            </tr>
        {/if}
        {if $users_cfg.sw_blogs}
        <tr>
            <td width="27"><img src="/components/users/images/menu/blog.gif" border="0"/></td>
            <td><a href="{$blog_href}">Мой блог</a></td>
        </tr>
        {/if}
        <tr>
            <td width="27"><img src="/components/users/images/menu/my.gif" border="0"/></td>
            <td><a href="javascript:" onclick="$('div#mycontent').slideToggle('slow');">Мой контент</a></td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="mycontent" style="display:none">
                    <table width="" border="0" cellspacing="0" cellpadding="2" align="center">
                        {if $users_cfg.sw_photo}
                        <tr>
                            <td width="27"><img src="/components/users/images/menu/my-photos.gif" border="0"/></td>
                            <td><a href="/users/{$id}/photoalbum.html">Фотоальбом</a></td>
                        </tr>
                        {/if}
                        {if $users_cfg.sw_board}
                        <tr>
                            <td width="27"><img src="/components/users/images/menu/my-board.gif" border="0"/></td>
                            <td><a href="/users/{$id}/board.html">Объявления</a></td>
                        </tr>
                        {/if}
                        {if $is_can_add}
                        <tr>
                            <td width="27"><img src="/components/users/images/menu/my-articles.gif" border="0"/></td>
                            <td><a href="/content/my.html">Статьи</a></td>
                        </tr>
                        {/if}
                    </table>
                </div>
            </td>
        </tr>
        {if $is_admin || $is_editor}
        <tr>
            <td width="27"><img src="/components/users/images/menu/cpanel.gif" border="0"/></td>
            <td><a href="/admin" target="_blank">Админка</a></td>
        </tr>
        {/if}
        {if $is_can_add}
        <tr>
            <td width="27"><img src="/components/users/images/menu/add.gif" border="0"/></td>
            <td><a href="/content/add.html">Добавить статью</a></td>
        </tr>
        {/if}
        <tr>
            <td width="27"><img src="/components/users/images/menu/logout.gif" border="0"/></td>
            <td><a href="/logout" style="color:#990000">Выход</a></td>
        </tr>
    </table>

{if $cfg.avatar} </td></tr></table> {/if}