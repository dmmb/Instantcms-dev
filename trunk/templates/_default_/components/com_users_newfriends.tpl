{* ================================================================================ *}
{* ============================ Заявки на дружбу ================================== *}
{* ================================================================================ *}

<span>{$LANG.NEXT_USERS_WANT_BE_FRIEND}:</span>

<div style="margin-top:10px">
{foreach key=id item=query from=$friends}
    <table width="100%" cellspacing="4" cellpadding="2" style=""><tr>
        <td width="70" height="70" valign="middle" align="center" style="border:solid 1px silver;background-color:#FFFFFF">
            <a href="{profile_url login=$query.sender_login}">
                <img src="{$query.sender_img}" border="0" />
            </a>
        </td>
        <td valign="top" style="padding-left:10px">
            <div style="margin-bottom:5px"><a class="usr_q_link" href="{profile_url login=$query.sender_login}">{$query.sender}</a></div>
            <div>
                <div><a style="height:16px; line-height:16px; padding-left:20px; background:url(/components/users/images/yes.gif) no-repeat" href="/users/{$menuid}/{$query.from_id}/friendship.html">{$LANG.ACCEPT}</a></div>
                <div><a style="height:16px; line-height:16px; padding-left:20px; background:url(/components/users/images/no.gif) no-repeat" href="/users/{$menuid}/{$query.from_id}/nofriends.html">{$LANG.REJECT}</a></div>
            </div>
        </td>
    </tr></table>
{/foreach}
</div>