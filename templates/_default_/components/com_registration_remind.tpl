<h1 class="con_heading">{$LANG.RECOVER_PASS}</h1>
{if !$is_changed}

    {if $errors}
        <p style="color:red">{$errors}</p>
    {/if}

    <form action="" method="post">

        <table cellpadding="0" cellspacing="2" border="0" style="margin-bottom: 15px">
            <tr>
                <td width="120">{$LANG.LOGIN}:</td>
                <td width="" height="24"><input type="text" name="pass" value="{$user.login}" disabled="disabled" style="width:200px"/></td>
            </tr>
            <tr>
                <td>{$LANG.PASS}:</td>
                <td><input type="password" name="pass" value="" style="width:200px"/></td>
            </tr>
            <tr>
                <td>{$LANG.REPEAT_PASS}:</td>
                <td><input type="password" name="pass2" value="" style="width:200px"/></td>
            </tr>
        </table>

        <input type="submit" id="submit" name="submit" value="{$LANG.CHANGE_PASS}" />

    </form>
    <script type="text/javascript">
        {literal}
            $('input[name=pass]').focus();
        {/literal}
    </script>

{else}

    <p>{$LANG.CHANGE_PASS_COMPLETED}</p>
    <p><input type="button" value="{$LANG.CONTINUE}" onclick="window.location.href='/'" /></p>

{/if}