{* ================================================================================ *}
{* ========================= Форма добавления комментария ========================= *}
{* ================================================================================ *}

<div class="cm_addentry">
	<form action="/comments/add" id="msgform" method="POST">
        <input type="hidden" name="parent_id" value="{$parent_id}" />
		{if $no_guests && !$is_user}
			<p>Комментарии могут добавлять только <a href="/registration" />зарегистрированные</a> пользователи.</p>
		{else}
			{if $user_can_add}
            
				{if $can_by_karma || !$cfg.min_karma}

					{if !$is_user}
						<div style="margin-bottom:10px"><label>Ваше имя: <input type="text" maxchars="20" size="30" name="guestname"/></label></div>
					{else}
						<input type="hidden" name="user_id" value="{$is_user}"/>
					{/if}
	
					<input type="hidden" name="target" value="{$target}"/>
					<input type="hidden" name="target_id" value="{$target_id}"/>
	
                    {if $cfg.bbcode}
                        <div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
                    {/if}

					{if $cfg.smiles}
						<div class="cm_smiles">{if !$cfg.bbcode}<a href="javascript:void(0);" onclick="$('#smilespanel').toggle()">Вставить смайл</a> &darr;{/if}
							{$smilies}
						</div>
					{/if}
	
					<div class="cm_editor">
						<textarea id="content" name="content" class="ajax_autogrowarea"></textarea>
					</div>
	
					{if $is_user}
						{if !$user_subscribed}
							<div style="margin-top:5px;margin-bottom:5px">
								<label style="padding:5px"><input name="subscribe" type="checkbox" value="1" /> Уведомлять о новых комментариях [<a href="/users/0/{$is_user}/editprofile.html#notices" target="_blank">Настройка уведомлений</a>]</label>
							</div>
						{/if}
					{/if}						
			
					<div class="cm_codebar">
						<table width="100%">
							<tr>
								{if $need_captcha}
									<td width="">{php}echo cmsPage::getCaptcha();{/php}</td>
								{/if}
								<td width="" align="right">
									<input class="cm_submit" type="submit" value="Отправить"/> 									
								</td>
							</tr>
						</table>
					</div>
					
				{else}
					<p>У вас не хватает <a href="/users/0/{$is_user}/karma.html">кармы</a> для добавления комментария. Требуется &mdash; {$karma_need}, имеется &mdash; {$karma_has}.</p>
				{/if}

			{else}
				<p>У вас нет прав на добавление комментариев. Обратитесь к администрации сайта.</p>
			{/if}
		{/if}
	</form>
</div>