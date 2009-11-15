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

<div class="con_heading">Настройки профиля</div>

{if $emsg && $opt=='save'} 
	<div style="color:red">{$emsg}</div>
{/if}

{if $msg && $opt=='save'}
	<div style="color:green">{$msg}</div>
{/if}

<form id="editform" name="editform" method="post" action="">
    <input type="hidden" name="opt" value="save" />

    <div id="profiletabs">
        <ul id="tabs">
            <li><a href="#about"><span>О себе</span></a></li>
            <li><a href="#contacts"><span>Контакты</span></a></li>
            <li><a href="#notices"><span>Уведомления</span></a></li>
            <li><a href="#policy"><span>Приватность</span></a></li>
        </ul>

            <div id="about">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>Ваше имя: </strong><br />
                            <span class="usr_edithint">Имя, отображаемое на сайте</span>
                        </td>
                        <td valign="top"><input name="nickname" type="text" id="nickname" style="width:300px" value="{$usr.nickname}"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Пол:</strong></td>
                        <td valign="top">
                            <select name="gender" id="gender" style="width:300px">
                                <option value="0" {if $usr.gender==0} selected {/if}>Не указан</option>
                                <option value="m" {if $usr.gender=='m'} selected {/if}>Мужской</option>
                                <option value="f" {if $usr.gender=='f'} selected {/if}>Женский</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>Город:</strong><br />
                            <span class="usr_edithint">Указав город вы сможете искать своих земляков</span>
                        </td>
                        <td valign="top">
                            <input name="city" type="text" id="city" style="width:300px" value="{$usr.city}"/>
                            <script type="text/javascript">
                                {$autocomplete_js}
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Дата рождения:</strong> </td>
                        <td valign="top">
                            {$dateform}
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>Интересы (метки): </strong><br/>
                            <span class="usr_edithint">Ваши ключевые слова , через запятую.</span><br />
                            <span class="usr_edithint">Другие пользователи смогут найти вас, щелкая по меткам.</span>
                        </td>
                        <td valign="top">
                            <textarea name="description" style="width:300px" rows="2" id="description">{$usr.description}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>Подпись на форуме:</strong><br />
                            <span class="usr_edithint">Можно использовать BBCode </span>
                        </td>
                        <td valign="top">
                            <textarea name="signature" style="width:300px" rows="2" id="signature">{$usr.signature}</textarea>
                        </td>
                    </tr>
                </table>
                <p>	{$private_forms} </p>
            </div>

            <div id="contacts">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>E-mail:</strong><br />
                            <span class="usr_edithint">Укажите существующий адрес</span>
                        </td>
                        <td valign="top">
                            <input name="email" type="text" id="email" style="width:300px" value="{$usr.email}"/>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Номер ICQ :</strong></td>
                        <td valign="top"><input name="icq" type="text" id="icq" style="width:300px" value="{$usr.icq}"/></td>
                    </tr>
                </table>
            </div>

            <div id="notices">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>
                                Уведомлять о новых личных сообщениях<br />
                                и комментариях на  e-mail:
                            </strong><br/>
                            <span class="usr_edithint">
                                Уведомление отправляется после каждого получения <br />
                                новых входящих сообщений, появлении записей на стене <br />
                                и новых комментариев к вашим фотографиям и записям в блоге
                            </span>
                        </td>
                        <td valign="top">
                            <input name="email_newmsg" type="radio" value="1" {if $usr.email_newmsg}checked{/if}/> Да
                            <input name="email_newmsg" type="radio" value="0" {if !$usr.email_newmsg}checked{/if}/> Нет
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>Как уведомлять при  подписке на новые комментарии? </strong><br />
                            <span class="usr_edithint">Куда отправлять уведомления при обновлениях</span>
                        </td>
                        <td valign="top">
                            <select name="cm_subscribe" id="cm_subscribe" style="width:300px">
                                <option value="mail" {if $usr.cm_subscribe=='mail'}selected{/if}>На e-mail</option>
                                <option value="priv" {if $usr.cm_subscribe=='priv'}selected{/if}>Личным сообщением</option>
                                <option value="both" {if $usr.cm_subscribe=='both'}selected{/if}>Личным сообщением и на е-mail</option>
                                <option value="none" {if $usr.cm_subscribe=='none'}selected{/if}>Не отправлять</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="policy">
                <table width="100%" border="0" cellspacing="0" cellpadding="5">
                    <tr>
                        <td width="300" valign="top">
                            <strong>Показывать e-mail:</strong><br/>
                            <span class="usr_edithint">Смогут ли другие пользователи отправлять вам письма</span>
                        </td>
                        <td valign="top">
                            <input name="showmail" type="radio" value="1" {if $usr.showmail}checked{/if}/> Да
                            <input name="showmail" type="radio" value="0" {if !$usr.showmail}checked{/if}/> Нет
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Показывать ICQ:</strong></td>
                        <td valign="top">
                            <input name="showicq" type="radio" value="1" {if $usr.showicq}checked{/if}/> Да
                            <input name="showicq" type="radio" value="0" {if !$usr.showicq}checked{/if}/> Нет
                        </td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Показывать дату рождения:</strong> </td>
                        <td valign="top">
                            <input name="showbirth" type="radio" value="1" {if $usr.showbirth}checked{/if}/>Да
                            <input name="showbirth" type="radio" value="0" {if !$usr.showbirth}checked{/if}/>Нет
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <strong>Показывать профиль:</strong><br/>
                            <span class="usr_edithint">Кому позволить просматривать профиль </span>
                        </td>
                        <td valign="top">
                            <select name="allow_who" id="allow_who" style="width:300px">
                                <option value="all" {if $usr.allow_who=='all'}selected{/if}>Всем</option>
                                <option value="registered" {if $usr.allow_who=='registered'}selected{/if}>Зарегистрированным</option>
                                <option value="friends" {if $usr.allow_who=='friends'}selected{/if}>Моим друзьям</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

	<div style="padding:5px; padding-bottom:15px; margin-bottom:5px;">
		<input style="font-size:16px" name="save" type="submit" id="save" value="Сохранить" />
        <input style="font-size:16px" name="chpassbtn" type="button" id="chpassbtn" value="Сменить пароль" onclick="{literal}$('div#change_password').slideToggle();{/literal}" />
		<input style="font-size:16px" name="delbtn2" type="button" id="delbtn2" value="Удалить профиль" onclick="location.href='/users/{$menuid}/{$usr.id}/delprofile.html';" />
	</div>
</form>

<div id="change_password" style="display:none">
    <div class="con_heading">Смена пароля</div>
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
                    <td width="300" valign="top"><strong>Старый пароль: </strong></td>
                    <td valign="top"><input name="oldpass" type="password" id="oldpass" size="30" /></td>
                </tr>
                <tr>
                    <td valign="top"><strong>Новый пароль:</strong></td>
                    <td valign="top"><input name="newpass" type="password" id="newpass" size="30" /></td>
                </tr>
                <tr>
                    <td valign="top"><strong>Новый пароль еще раз</strong>:</td>
                    <td valign="top"><input name="newpass2" type="password" id="newpass2" size="30" /></td>
                </tr>
            </table>
        <div style="padding:5px; padding-bottom:15px; margin-bottom:20px;">
            <input style="font-size:16px" name="save2" type="submit" id="save2" value="Изменить пароль" />
        </div>
    </form>
</div>