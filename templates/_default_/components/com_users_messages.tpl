{* ================================================================================ *}
{* ======================== Личные сообщения ====================================== *}
{* ================================================================================ *}

	<div class="con_heading" style="margin-bottom:25px">{$LANG.MY_MESS}</div>
		<div class="usr_msgmenu_tabs">
			{if $opt == 'in'}				
				<span class="usr_msgmenu_active in_span">{$LANG.INBOX}</span> 
				<a class="usr_msgmenu_link out_link" href="/users/{$id}/messages-sent.html">{$LANG.SENT}</a>
				<a class="usr_msgmenu_link new_link" href="/users/{$id}/messages-new.html">{$LANG.WRITE}</a>
			{elseif $opt == 'out'}
				<a class="usr_msgmenu_link in_link" href="/users/{$id}/messages.html">{$LANG.INBOX}</a> 
				<span class="usr_msgmenu_active out_span">{$LANG.SENT}</span>
				<a class="usr_msgmenu_link new_link" href="/users/{$id}/messages-new.html">{$LANG.WRITE}</a>
			{elseif $opt == 'new'}
				<a class="usr_msgmenu_link in_link" href="/users/{$id}/messages.html">{$LANG.INBOX}</a> 
				<a class="usr_msgmenu_link out_link" href="/users/{$id}/messages-sent.html">{$LANG.SENT}</a>
				<span class="usr_msgmenu_active new_span">{$LANG.WRITE}</span>
			{elseif $opt == 'history'}
				<a class="usr_msgmenu_link in_link" href="/users/{$id}/messages.html">{$LANG.INBOX}</a> 
				<a class="usr_msgmenu_link out_link" href="/users/{$id}/messages-sent.html">{$LANG.SENT}</a>
				<a class="usr_msgmenu_link new_link" href="/users/{$id}/messages-new.html">{$LANG.WRITE}</a>
				<span class="usr_msgmenu_active history_span">{$with_name}</span>
			{/if}
		</div>
		
		{if $opt == 'in' || $opt == 'out' || $opt == 'history'}
		
			<table class="usr_msgmenu_bar" width="100%" height="30" border="0" cellpadding="10" cellspacing="0"><tr>
		
				<td><strong>{$LANG.MESS_INBOX}:</strong> {$msg_count}</td>
			
				{if ($opt=='in' || $opt=='out') && $msg_count>0}
					<td width="100" align="right"><a href="/users/{$id}/delmessages-{$opt}.html">{$LANG.CLEAN_CAT}</a></td>
				{/if}
			
			</tr></table>
									
			{if $is_mes}
					<div>
                    {foreach key=tid item=record from=$records}
                    <div class="usr_msg_entry">
						<table style="width:100%" cellspacing="0">
						<tr>
                            <td class="usr_msg_title" width=""><strong>{$record.authorlink}</strong>, <span class="usr_msg_date">{$record.fpubdate}</span></td>
							{if $record.is_new}
								{if $opt=='in'}									
									<td class="usr_msg_title" width="90" align="right"><span class="msg_new">{$LANG.NEW}!</span></td>
								{else}
									<td class="usr_msg_title" width="90" align="right"><a class="msg_delete" href="/users/delmsg{$record.id}.html">{$LANG.CANCEL_MESS}</a></td>
								{/if}
							{else}
								<td class="usr_msg_title" width="14" align="right">&nbsp;</td>						
								<td class="usr_msg_title" width="20" align="right">&nbsp;</td>
							{/if}
							{if $opt=='in'}
								{if $record.sender_id>0}
									<td class="usr_msg_title" width="80" align="right"><a class="msg_reply" href="/users/{$record.from_id}/reply{$record.id}.html">{$LANG.REPLY}</a></td>
									<td class="usr_msg_title" width="80" align="right"><a class="msg_history" href="/users/{$id}/messages-history{$record.from_id}.html">{$LANG.HISTORY}</a></td>
								{/if}
							{/if}
							{if $opt=='in' || ($opt=='out' && !$record.is_new)}
								<td class="usr_msg_title" width="70" align="right"><a class="msg_delete" href="/users/delmsg{$record.id}.html">{$LANG.DELETE}</a></td>
							{/if}
						</tr>
						</table>
						<table cellspacing="4">		
						<tr>						
							<td width="70" height="70" valign="middle" align="center" style="border:solid 1px #C3D6DF">
                            	{$record.user_img}
                                {if $record.is_online}
                                <br /><span class="online" style="font-size:10px;">{$LANG.ONLINE}</span>
                                {/if}
                            </td>
							<td width="" valign="top"><div style="padding:6px">{$record.message}</div></td>
						</tr>
						</table>
                    </div>
					{/foreach}
					</div>
                    {if $msg_count > $perpage}
                        {$pagebar}
                    {/if}
				{else}
                <p style="padding:20px 10px">{$LANG.NOT_MESS_IN_CAT}</p>
                {/if}
	
		{/if}

		{if $opt == 'new'}
			<form action="" id="newmessage" method="POST" name="msgform">			
				<table class="usr_msgmenu_bar" width="100%" height="30" border="0" cellpadding="10" cellspacing="0"><tr>
					<tr>
						<td width="40"><strong>{$LANG.SEND_TO}:</strong> </td>
						<td width="120">
                            <select name="id" id="to_id" class="s_usr" style="width:150px">{$user_opt}</select>
                            <select name="group_id" class="s_grp" id="group_id" style="width:150px;display:none">
                                {foreach key=gid item=group from=$groups}
                                    <option value="{$group.id}">{$group.title}</option>
                                {/foreach}
                            </select>
                            <input type="hidden" name="send_to_group" id="send_to_group" value="0" />
                        </td>
						{if $is_admin}
							<td width="110">
                                <a href="javascript:" class="s_usr ajaxlink" onclick="{literal}$('.s_grp').show();$('.s_usr').hide();$('#send_to_group').val(1);{/literal}">
                                    {$LANG.SEND_TO_GROUP}
                                </a>
                                <a href="javascript:" class="s_grp ajaxlink" onclick="{literal}$('.s_grp').hide();$('.s_usr').show();$('#send_to_group').val(0);{/literal}" style="display:none">
                                    {$LANG.SEND_TO_FRIEND}
                                </a>
                            </td>
							<td width="24" style="padding-right:0">
                                <input name="massmail" type="checkbox" value="1" id="massmail" />
                            </td>
							<td width="" style="padding-left:0">
                                <label for="massmail">{$LANG.SEND_TO_ALL}</label>
                            </td>
						{else}
							<td>&nbsp;</td>
						{/if}
					</tr>								
				</table>
				<div>
						<input type="hidden" name="gosend"   value="1"/>
						<div class="usr_msg_bbcodebox">
							{$bb_toolbar}
						</div>							
							{$bb_smiles}
						<textarea style="font-size:18px;border:solid 1px gray;width:100%;height:200px;" name="message" id="message"></textarea>						
						<div style="margin-top:6px;"><input type="button" id="gosend" value="{$LANG.SEND}" onclick="sendMessage()" style="font-size:18px"/></div>
				</div>			
			</form>				
		{/if}
