{* ================================================================================ *}
{* ====================== Стена пользователя - Написать =========================== *}
{* ================================================================================ *}

{if $my_id}
    <form action="/users/wall-add" method="POST">
        <input type="hidden" name="user_id" value="{$user_id}" />
        <input type="hidden" name="usertype" value="{$usertype}" />

        <div class="usr_msg_bbcodebox">{$bb_toolbar}</div>

        <div class="cm_smiles">{$smilies}</div>

        <div style="margin-bottom:5px">
            <textarea name="message" id="message" class="wall_message"></textarea>
        </div>
        <div style="text-align:right">
            <input type="submit" value="{$LANG.SEND}" />
            <input name="Button" type="button" value="{$LANG.CANCEL}" onclick="{literal}$('#addwall').slideToggle();$('.usr_wall_addlink').toggle();{/literal}"/>
        </div>
    </form>
{else}
    <p>{$LANG.ONLY_REG_USER_CAN_WALL}</p>
{/if}
