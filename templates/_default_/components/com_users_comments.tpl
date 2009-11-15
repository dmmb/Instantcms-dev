{* ================================================================================ *}
{* =================== Список комментариев пользователя =========================== *}
{* ================================================================================ *}

<div class="con_heading"><a href="{profile_url login=$login}">{$nickname}</a> &rarr; Комментарии</div>

<div style="margin-top:15px">
    {foreach key=tid item=comment from=$comments}
        <table style="width:100%; margin-bottom:2px;" cellspacing="0" cellpadding="4">
            <tr>
                <td colspan="2" class="usr_com_title">
                    <div style="float:left">{$comment.link} &mdash; {$comment.fpubdate}</div>
                    <div style="float:right">{$comment.votes}</div>
                </td>
            </tr>
            <tr>
                <td class="usr_com_avatar" width="64" valign="top">{$avatar}</td>
                <td class="usr_com_body" valign="top">{$comment.content}</td>
            </tr>
        </table>
    {/foreach}
</div>

{$pagebar}