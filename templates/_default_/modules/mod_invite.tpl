<form action="" method="post">

    {if $errors}
        <p style="color:red">{$errors}</p>
    {/if}

    {if $success}
        <p style="color:green">{$success}</p>
    {/if}

    {if !$user_id}
    <div style="margin-bottom:10px">
        <input type="text" class="text-input" style="width:98%;color:#666" name="username" value="{$LANG.YOUR_NAME}" onclick="{literal}$(this).val('');{/literal}" onblur="{literal}if ($(this).val()==''){ $(this).val{/literal}('{$LANG.YOUR_NAME}'){literal}; }{/literal}" />
    </div>
    {/if}

    <div>
        <input type="text" class="text-input" style="width:98%;color:#666" name="friend_email" value="{$LANG.FRIEND_EMAIL}" onclick="{literal}$(this).val('');{/literal}" onblur="{literal}if ($(this).val()==''){ $(this).val{/literal}('{$LANG.FRIEND_EMAIL}'){literal}; }{/literal}"/>
    </div>

    <p style="margin-top:10px">
        <input type="submit" name="send_invite_email" value="{$LANG.DO_INVITE}" />
    </p>

</form>