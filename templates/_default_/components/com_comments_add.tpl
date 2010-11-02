{* ================================================================================ *}
{* ========================= Форма добавления комментария ========================= *}
{* ================================================================================ *}

<div class="cm_addentry">
	<form action="/comments/add" id="msgform" method="POST">
        <input type="hidden" name="parent_id" value="{$parent_id}" />
		{if $no_guests && !$is_user}
			<p>{$LANG.COMMENTS_CAN_ADD_ONLY} <a href="/registration" />{$LANG.REGISTERED}</a> {$LANG.USERS}.</p>
		{else}
			{if $user_can_add}
            
				{if $can_by_karma || !$cfg.min_karma}

					{if !$is_user}
						<div class="cm_guest_name"><label>{$LANG.YOUR_NAME}: <input type="text" maxchars="20" size="30" name="guestname"/></label></div>
					{else}
						<input type="hidden" name="user_id" value="{$is_user}"/>
					{/if}
	
					<input type="hidden" name="target" value="{$target}"/>
					<input type="hidden" name="target_id" value="{$target_id}"/>
	
                    {if $cfg.bbcode}
                        <div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
                    {/if}

					{if $cfg.smiles && $smilies}
						<div class="cm_smiles">{if !$cfg.bbcode}<a href="javascript:void(0);" onclick="$('#smilespanel').toggle()">{$LANG.INSERT_SMILE}</a> &darr;{/if}
							{$smilies}
						</div>
					{/if}
	
					<div class="cm_editor">
						<textarea id="content" name="content" class="ajax_autogrowarea"></textarea>
					</div>
	
					{if $is_user}
						{if !$user_subscribed}
							<div style="margin-top:5px;margin-bottom:5px">
								<label style="padding:5px"><input name="subscribe" type="checkbox" value="1" /> {$LANG.NOTIFY_NEW_COMM} [<a href="/users/{$is_user}/editprofile.html#notices" target="_blank">{$LANG.CONFIG_NOTIFY}</a>]</label>
							</div>
						{/if}
					{/if}						
			
					<div class="cm_codebar">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								{if $need_captcha}
									<td width="">{php}echo cmsPage::getCaptcha();{/php}</td>
								{/if}
								<td width="" align="right">
									<input class="cm_submit" type="submit" value="{$LANG.SEND}"/>
								</td>
							</tr>
						</table>
					</div>
					
				{else}
					<p>{$LANG.YOU_NEED} <a href="/users/{$is_user}/karma.html">{$LANG.KARMS}</a> {$LANG.TO_ADD_COMM}. {$LANG.NEED} &mdash; {$karma_need}, {$LANG.HAS} &mdash; {$karma_has}.</p>
				{/if}

			{else}
				<p>{$LANG.YOU_HAVENT_ACCESS_TEXT}</p>
			{/if}
		{/if}
	</form>
</div>