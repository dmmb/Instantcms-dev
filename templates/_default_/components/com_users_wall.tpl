{* ================================================================================ *}
{* ============================ Стена пользователя ================================ *}
{* ================================================================================ *}

{if $total}

    <input type="hidden" name="user_id" value="{$wall_user_id}" />
    <input type="hidden" name="usertype" value="{$usertype}" />

    {foreach key=id item=record from=$records}
        <div class="usr_wall_entry">
            <div class="usr_wall_title"><a href="{profile_url login=$record.author_login}">{$record.author}</a> {$LANG.WROTE} {$record.fpubdate} {$LANG.BACK}:</div>
            {if $myprofile || $record.author_id==$user_id}
                <div class="usr_wall_delete"><a href="/users/wall-delete/{$usertype}/{$record.id}">{$LANG.DELETE}</a></div>
            {/if}

            <table style="width:100%; margin-bottom:2px;" cellspacing="0" cellpadding="0">
            <tr>
                <td width="70" valign="top" align="center" style="text-align:center">
                    <div class="usr_wall_avatar">
                        <a href="{profile_url login=$record.author_login}">{$record.avatar}</a>
                    </div>
                </td>
                <td width="" valign="top" class="usr_wall_text">{$record.content}</td>
            </tr>
            </table>
        </div>
    {/foreach}

    <div class="wall_loading" style="display:none;color:gray;margin:15px">
        <span style="background:url(/images/ajax-loader.gif) no-repeat left center;padding-left:60px"><em>{$LANG.MESS_LOADING}...</em></span>
    </div>

    {if $pages>1}
        <div>
            {$pagebar}
        </div>
    {/if}

{else}
    <p>{$LANG.NOT_POSTS_ON_WALL_TEXT}</p>
{/if}