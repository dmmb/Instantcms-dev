{* ================================================================================ *}
{* ========================= ����� ���������� ����������� ========================= *}
{* ================================================================================ *}

<div class="cm_addentry">
	<form action="/comments/add" id="msgform" method="POST">
        <input type="hidden" name="parent_id" value="{$parent_id}" />
		{if $no_guests && !$is_user}
			<p>����������� ����� ��������� ������ <a href="/registration" />������������������</a> ������������.</p>
		{else}
			{if $user_can_add}
            
				{if $can_by_karma || !$cfg.min_karma}

					{if !$is_user}
						<div style="margin-bottom:10px"><label>���� ���: <input type="text" maxchars="20" size="30" name="guestname"/></label></div>
					{else}
						<input type="hidden" name="user_id" value="{$is_user}"/>
					{/if}
	
					<input type="hidden" name="target" value="{$target}"/>
					<input type="hidden" name="target_id" value="{$target_id}"/>
	
                    {if $cfg.bbcode}
                        <div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
                    {/if}

					{if $cfg.smiles}
						<div class="cm_smiles">{if !$cfg.bbcode}<a href="javascript:void(0);" onclick="$('#smilespanel').toggle()">�������� �����</a> &darr;{/if}
							{$smilies}
						</div>
					{/if}
	
					<div class="cm_editor">
						<textarea id="content" name="content" class="ajax_autogrowarea"></textarea>
					</div>
	
					{if $is_user}
						{if !$user_subscribed}
							<div style="margin-top:5px;margin-bottom:5px">
								<label style="padding:5px"><input name="subscribe" type="checkbox" value="1" /> ���������� � ����� ������������ [<a href="/users/0/{$is_user}/editprofile.html#notices" target="_blank">��������� �����������</a>]</label>
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
									<input class="cm_submit" type="submit" value="���������"/> 									
								</td>
							</tr>
						</table>
					</div>
					
				{else}
					<p>� ��� �� ������� <a href="/users/0/{$is_user}/karma.html">�����</a> ��� ���������� �����������. ��������� &mdash; {$karma_need}, ������� &mdash; {$karma_has}.</p>
				{/if}

			{else}
				<p>� ��� ��� ���� �� ���������� ������������. ���������� � ������������� �����.</p>
			{/if}
		{/if}
	</form>
</div>