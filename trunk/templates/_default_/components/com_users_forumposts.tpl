{* ================================================================================ *}
{* =================== Список постов пользователя на форуме ======================= *}
{* ================================================================================ *}

<div class="con_heading"><a href="{profile_url login=$user_login}">{$nickname}</a> &rarr; {$LANG.FORUM}</div>

<div style="margin-top:15px">
    {foreach key=tid item=post from=$posts}
        <table style="width:100%; margin-bottom:2px;" cellspacing="0" cellpadding="4">
            <tr>
                <td colspan="2" class="usr_com_title"><a href="{$post.link}">{$post.topic}</a> &mdash; {$post.date}</td>
            </tr>
            <tr>
                <td class="usr_com_avatar" width="64" valign="top">{$avatar}</td>
                <td class="usr_com_body" valign="top">{$post.content}</td>
            </tr>
        </table>
    {/foreach}
</div>
{$pagebar}