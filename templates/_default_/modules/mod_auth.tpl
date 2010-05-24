<form action="/login" method="post" name="authform" style="margin:0px" target="_self" id="authform">
    <table class="authtable" width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td width="86">{$LANG.AUTH_LOGIN}:</td>
            <td width="917"><input name="login" type="text" id="login" size="15" /></td>
        </tr>
        <tr>
            <td height="30" valign="top">{$LANG.AUTH_PASS}:</td>
            <td valign="top"><input name="pass" type="password" id="pass" size="15" /></td>
        </tr>
        <tr>
            <td height="27" colspan="2" align="right" valign="top">
                <table width="100%" border="0" cellspacing="0" cellpadding="4">
                    <tr style="font-size:10px">
                        <td width="13%"><input style="width:60px" type="submit" name="Submit" value="{$LANG.AUTH_ENTER}" /></td>
                        <td width="87%" align="right">
                            {if $cfg.autolog}
                                <input name="remember" type="checkbox" id="remember" value="1"  style="margin-right:0px"/> {$LANG.AUTH_REMEMBER}<br />
                            {/if}
                            {if $cfg.passrem}
                                <a href="/passremind.html">{$LANG.AUTH_FORGOT}</a>
                            {/if}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>