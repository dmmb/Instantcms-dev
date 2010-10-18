<div class="mod_user_menu">

    <span class="my_profile">
        <a href="{profile_url login=$login}">Мой профиль</a>
    </span>

    {if $users_cfg.sw_msg}
    <span class="my_messages">
        {if $newmsg}
            <a class="has_new" href="/users/{$id}/messages.html">Сообщения ({$newmsg})</a>
        {else}
            <a href="/users/{$id}/messages.html">Сообщения</a>
        {/if}
    </span>
    {/if}

    {if $users_cfg.sw_blogs}
    <span class="my_blog">
        <a href="{$blog_href}">Мой блог</a>
    </span>
    {/if}

    {if $users_cfg.sw_photo}
    <span class="my_photos">
        <a href="/users/{$id}/photoalbum.html">Фото</a>
    </span>
    {/if}

    {if $is_can_add}
    <span class="my_content">
        <a href="/content/my.html">Статьи</a>
    </span>
    {/if}

    {if $is_admin || $is_editor}
    <span class="admin">
        <a href="/admin" target="_blank">Админка</a>
    </span>
    {/if}

    <span class="logout">
        <a href="/logout">Выход</a>
    </span>

</div>

{*
{if $cfg.avatar}
    <a href="/users/{$id}/avatar.html">{$avatar}</a>
{/if}
*}
