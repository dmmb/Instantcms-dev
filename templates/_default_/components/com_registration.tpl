{* ================================================================================ *}
{* ============================ ����� ����������� ================================= *}
{* ================================================================================ *}

<form id="regform" name="regform" method="post" action="/index.php?view=registration&menuid={$menuid}">
    <table width="100%" border="0" align="center" cellpadding="5" cellspacing="0">
        <tr>
            <td width="269" valign="top" class="">
                <div><strong>�����:</strong></div>
                <div><small>����� �������������� ��� �����������.<br/>������ ��������� ����� � �����.</small></div>
            </td>
            <td  valign="top" class="">
                <input name="login" id="logininput" type="text" size="30" value="{$login}" onchange="checkLogin()" autocomplete="off"/>
                <span class="regstar">*</span>
                <div id="logincheck"></div>
            </td>
        </tr>
        {if $cfg.name_mode == 'nickname'}
            <tr>
                <td valign="top" class="">
                    <div><strong>�������:</strong></div>
                    <small>���� ������ ����� ������������� ���� ���������. ������� � ���������� ����� � �����.</small>
                </td>
                <td valign="top" class="">
                    <input name="nickname" id="nickinput" type="text" size="30" value="{$nickname}" />
                    <span class="regstar">*</span>
                </td>
            </tr>
        {else}
            <tr>
                <td valign="top" class="">
                    <div><strong>���:</strong></div>
                </td>
                <td valign="top" class="">
                    <input name="realname1" id="realname1" type="text" size="30" value="{$realname1}" />
                    <span class="regstar">*</span>
                </td>
            </tr>
            <tr>
                <td valign="top" class="">
                    <div><strong>�������:</strong></div>
                </td>
                <td valign="top" class="">
                    <input name="realname2" id="realname2" type="text" size="30" value="{$realname2}" />
                    <span class="regstar">*</span>
                </td>
            </tr>
        {/if}
        <tr>
            <td valign="top" class=""><strong>������:</strong></td>
            <td valign="top" class="">
                <input name="pass" id="pass1input" type="password" size="30" onchange="{literal}$('#passcheck').html('');{/literal}"/>
                <span class="regstar">*</span>
            </td>
        </tr>
        <tr>
            <td valign="top" class=""><strong>��������� ������: </strong></td>
            <td valign="top" class="">
                <input name="pass2" id="pass2input" type="password" size="30" onchange="checkPasswords()" />
                <span class="regstar">*</span>
                <div id="passcheck"></div>
            </td>
        </tr>
        <tr>
            <td valign="top" class="">
                <div><strong>E-Mail:</strong></div>
                <div><small>��-���������  �� �����������</small></div>
            </td>
            <td valign="top" class="">
                <input name="email" type="text" size="30" value="{$email}"/>
                <span class="regstar">*</span>
            </td>
        </tr>
        {if $cfg.ask_icq}
            <tr>
                <td valign="top" class=""><strong>ICQ:</strong></td>
                <td valign="top" class="">
                    <input name="icq" type="text" id="icq" value="{$icq}" size="30"/>
                </td>
            </tr>
        {/if}
        {if $cfg.ask_birthdate}
            <tr>
                <td valign="top" class="">
                    <div><strong>���� ��������:</strong></div>
                    <div><small>��-���������  �� �����������</small></div>
                </td>
                <td valign="top" class="">{php}$inCore=cmsCore::getInstance(); echo $inCore->getDateForm('birthdate'){/php}</td>
            </tr>
        {/if}
        <tr>
            <td valign="top" class="">
                <div><strong>������ �� �����: </strong></div>
                <div><small>������� �����, ������������ �� ��������</small></div>
            </td>
            <td valign="top" class="">
                {php}echo cmsPage::getCaptcha();{/php}
            </td>
        </tr>
        <tr>
            <td valign="top" class="">&nbsp;</td>
            <td valign="top" class="">
                <input name="do" type="hidden" value="register" />
                <input name="save" type="submit" id="save" value="�����������" />
            </td>
        </tr>
    </table>
</form>
