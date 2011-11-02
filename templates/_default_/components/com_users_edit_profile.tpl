{* ================================================================================ *}
{* =================== Редактирование профиля пользователя ======================== *}
{* ================================================================================ *}

{add_js file='includes/jquery/tabs/jquery.ui.min.js'}
{add_css file='includes/jquery/tabs/tabs.css'}

{literal}
	<script type="text/javascript">
		$(document).ready(function(){
			$("#profiletabs > ul#tabs").tabs();
		});
	</script>
{/literal}

<div class="con_heading">{$LANG.CONFIG_PROFILE}</div>

{if $messages && ($opt=='save' || $opt=='changepass')}
    <div class="sess_messages">
        {foreach key=id item=message from=$messages}
            {$message}
        {/foreach}
    </div>
{/if}

<form id="editform" name="editform" method="post" action="">
    <input type="hidden" name="opt" value="save" />

    <div id="profiletabs">
        <ul id="tabs">
            <li><a href="#about"><span>{$LANG.ABOUT_ME}</span></a></li>
            <li><a href="#contacts"><span>{$LANG.CONTACTS}</span></a></li>
            <li><a href="#notices"><span>{$LANG.NOTIFIC}</span></a></li>
            <li><a href="#policy"><span>{$LANG.PRIVACY}</span></a></li>
        </ul>

            <div id="about">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>{$LANG.YOUR_NAME}: </strong><br />
                            <span class="usr_edithint">{$LANG.YOUR_NAME_TEXT}</span>
                        </td>
                        <td valign="top"><input name="nickname" type="text" class="text-input" id="nickname" style="width:300px" value="{$usr.nickname|escape:'html'}"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>{$LANG.SEX}:</strong></td>
                        <td valign="top">
                            <select name="gender" id="gender" style="width:307px">
                                <option value="0" {if $usr.gender==0} selected {/if}>{$LANG.NOT_SPECIFIED}</option>
                                <option value="m" {if $usr.gender=='m'} selected {/if}>{$LANG.MALES}</option>
                                <option value="f" {if $usr.gender=='f'} selected {/if}>{$LANG.FEMALES}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>{$LANG.CITY}:</strong><br />
                            <span class="usr_edithint">{$LANG.CITY_TEXT}</span>
                        </td>
                        <td valign="top">
                            <input name="city" type="text" id="city" class="text-input" style="width:300px" value="{$usr.city|escape:'html'}"/>
                            <script type="text/javascript">
                                {$autocomplete_js}
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>{$LANG.BIRTH}:</strong> </td>
                        <td valign="top">
                            {$dateform}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>{$LANG.HOBBY} ({$LANG.TAGSS}): </strong><br/>
                            <span class="usr_edithint">{$LANG.YOUR_KEYWORDS}</span><br />
                            <span class="usr_edithint">{$LANG.TAGSS_TEXT}</span>
                        </td>
                        <td valign="top">
                            <textarea name="description" class="text-input" style="width:300px" rows="2" id="description">{$usr.description}</textarea>
                        </td>
                    </tr>
                    {if $cfg_forum.component_enabled}
                    <tr>
                        <td valign="top">
                            <strong>{$LANG.SIGNED_FORUM}:</strong><br />
                            <span class="usr_edithint">{$LANG.CAN_USE_BBCODE} </span>
                        </td>
                        <td valign="top">
                            <textarea name="signature" class="text-input" style="width:300px" rows="2" id="signature">{$usr.signature}</textarea>
                        </td>
                    </tr>
                    {/if}
                </table>
                <p>	{$private_forms} </p>
            </div>

            <div id="contacts">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>E-mail:</strong><br />
                            <span class="usr_edithint">{$LANG.REALY_ADRESS_EMAIL}</span>
                        </td>
                        <td valign="top">
                            <input name="email" type="text" class="text-input" id="email" style="width:300px" value="{$usr.email}"/>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>{$LANG.NUMBER_ICQ} :</strong></td>
                        <td valign="top"><input name="icq" class="text-input" type="text" id="icq" style="width:300px" value="{$usr.icq}"/></td>
                    </tr>
                </table>
            </div>

            <div id="notices">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>
                                {$LANG.NOTIFY_NEW_MESS}:
                            </strong><br/>
                            <span class="usr_edithint">
                                {$LANG.NOTIFY_NEW_MESS_TEXT}
                            </span>
                        </td>
                        <td valign="top">
                            <input name="email_newmsg" type="radio" value="1" {if $usr.email_newmsg}checked{/if}/> {$LANG.YES}
                            <input name="email_newmsg" type="radio" value="0" {if !$usr.email_newmsg}checked{/if}/> {$LANG.NO}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>{$LANG.HOW_NOTIFY_NEW_MESS} </strong><br />
                            <span class="usr_edithint">{$LANG.WHERE_TO_SEND}</span>
                        </td>
                        <td valign="top">
                            <select name="cm_subscribe" id="cm_subscribe" style="width:307px">
                                <option value="mail" {if $usr.cm_subscribe=='mail'}selected{/if}>{$LANG.TO_EMAIL}</option>
                                <option value="priv" {if $usr.cm_subscribe=='priv'}selected{/if}>{$LANG.TO_PRIVATE_MESS}</option>
                                <option value="both" {if $usr.cm_subscribe=='both'}selected{/if}>{$LANG.TO_EMAIL_PRIVATE_MESS}</option>
                                <option value="none" {if $usr.cm_subscribe=='none'}selected{/if}>{$LANG.NOT_SEND}</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="policy">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>{$LANG.SHOW_EMAIL}:</strong><br/>
                            <span class="usr_edithint">{$LANG.SHOW_EMAIL_TEXT}</span>
                        </td>
                        <td valign="top">
                            <input name="showmail" type="radio" value="1" {if $usr.showmail}checked{/if}/> {$LANG.YES}
                            <input name="showmail" type="radio" value="0" {if !$usr.showmail}checked{/if}/> {$LANG.NO}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>{$LANG.SHOW_ICQ}:</strong></td>
                        <td valign="top">
                            <input name="showicq" type="radio" value="1" {if $usr.showicq}checked{/if}/> {$LANG.YES}
                            <input name="showicq" type="radio" value="0" {if !$usr.showicq}checked{/if}/> {$LANG.NO}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>{$LANG.SHOW_BIRTH}:</strong> </td>
                        <td valign="top">
                            <input name="showbirth" type="radio" value="1" {if $usr.showbirth}checked{/if}/>{$LANG.YES}
                            <input name="showbirth" type="radio" value="0" {if !$usr.showbirth}checked{/if}/>{$LANG.NO}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>{$LANG.SHOW_PROFILE}:</strong><br/>
                            <span class="usr_edithint">{$LANG.WHOM_SHOW_PROFILE} </span>
                        </td>
                        <td valign="top">
                            <select name="allow_who" id="allow_who" style="width:307px">
                                <option value="all" {if $usr.allow_who=='all'}selected{/if}>{$LANG.EVERYBODY}</option>
                                <option value="registered" {if $usr.allow_who=='registered'}selected{/if}>{$LANG.REGISTERED}</option>
                                <option value="friends" {if $usr.allow_who=='friends'}selected{/if}>{$LANG.MY_FRIENDS}</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
</div>
	<div style="padding:5px; padding-bottom:15px; margin-bottom:5px;">
		<input style="font-size:16px" name="save" type="submit" id="save" value="{$LANG.SAVE}" />
        <input style="font-size:16px" name="chpassbtn" type="button" id="chpassbtn" value="{$LANG.CHANGE_PASS}" onclick="{literal}$('div#change_password').slideToggle();{/literal}" />
		<input style="font-size:16px" name="delbtn2" type="button" id="delbtn2" value="{$LANG.DEL_PROFILE}" onclick="location.href='/users/{$usr.id}/delprofile.html';" />
	</div>
</form>

<div id="change_password" style="display:none">
    <div class="con_heading">{$LANG.CHANGING_PASS}</div>
    {if $emsg && $opt=='changepass'}
        <div style="color:red">{$emsg}</div>
    {/if}
    {if $msg && $opt=='changepass'}
        <div style="color:green">{$msg}</div>
    {/if}
    <form id="editform" name="editform" method="post" action="">
        <input type="hidden" name="opt" value="changepass" />
            <table width="100%" border="0" cellspacing="0" cellpadding="5" style="margin:11px">
                <tr>
                    <td width="300" valign="top"><strong>{$LANG.OLD_PASS}: </strong></td>
                    <td valign="top"><input name="oldpass" type="password" id="oldpass" size="30" /></td>
                </tr>
                <tr>
                    <td valign="top"><strong>{$LANG.NEW_PASS}:</strong></td>
                    <td valign="top"><input name="newpass" type="password" id="newpass" size="30" /></td>
                </tr>
                <tr>
                    <td valign="top"><strong>{$LANG.NEW_PASS_REPEAT}</strong>:</td>
                    <td valign="top"><input name="newpass2" type="password" id="newpass2" size="30" /></td>
                </tr>
            </table>
        <div style="padding:5px; padding-bottom:15px; margin-bottom:20px;">
            <input style="font-size:16px" name="save2" type="submit" id="save2" value="{$LANG.CHANGE_PASSWORD}" />
        </div>
    </form>
</div>