<div class="mod_user_menu">

    <span class="my_profile">
        <a href="{profile_url login=$login}">��� �������</a>
    </span>

    {if $users_cfg.sw_msg}
    <span class="my_messages">
        {if $newmsg}
            <a class="has_new" href="/users/{$id}/messages.html">��������� ({$newmsg})</a>
        {else}
            <a href="/users/{$id}/messages.html">���������</a>
        {/if}
    </span>
    {/if}

    {if $users_cfg.sw_blogs}
    <span class="my_blog">
        <a href="{$blog_href}">��� ����</a>
    </span>
    {/if}

    {if $users_cfg.sw_photo}
    <span class="my_photos">
        <a href="/users/{$id}/photoalbum.html">����</a>
    </span>
    {/if}

    {if $is_can_add}
    <span class="my_content">
        <a href="/content/my.html">������</a>
    </span>
    {/if}

    {if $is_admin || $is_editor}
    <span class="admin">
        <a href="/admin" target="_blank">�������</a>
    </span>
    {/if}

    <span class="logout">
        <a href="/logout">�����</a>
    </span>

</div>

{*
{if $cfg.avatar}
    <a href="/users/{$id}/avatar.html">{$avatar}</a>
{/if}
*}
