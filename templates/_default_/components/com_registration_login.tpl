{* ================================================================================ *}
{* ============================ ����� ����������� ================================= *}
{* ================================================================================ *}

<h1 class="con_heading">{$LANG.SITE_LOGIN}</h1>

{if $is_sess_back}
    <p class="lf_notice">{$LANG.PAGE_ACCESS_NOTICE}</p>
{/if}

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="login_form">
    <tr>
        <td valign="top">
            <form method="post" action="">
                <div class="lf_title">{$LANG.LOGIN} {$LANG.OR} {$LANG.EMAIL}</div>
                <div class="lf_field">
                    <input type="text" name="login" id="login_field" tabindex="1" /> <a href="/registration" class="lf_link">{$LANG.REGISTRATION}</a>
                </div>
                <div class="lf_title">{$LANG.PASS}</div>
                <div class="lf_field">
                    <input type="password" name="pass" id="pass_field" tabindex="2"/> <a href="/passremind.html" class="lf_link">{$LANG.FORGOT_PASS}</a>
                </div>
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td width="20"><input type="checkbox" name="remember" value="1" id="remember" /></td>
                        <td>
                            <label for="remember">{$LANG.REMEMBER_ME}</label>
                        </td>
                    </tr>
                </table>
                <p class="lf_submit">
                    <input type="submit" name="login_btn" value="{$LANG.SITE_LOGIN_SUBMIT}" />
                </p>
            </form>
        </td>
        <td valign="top">

        </td>
    </tr>
</table>

<script type="text/javascript">
    {literal}
    $(document).ready(function(){
        $('.login_form #login_field').focus();
    });
    {/literal}
</script>