{* ================================================================================ *}
{* ======================== ������ ��������� ====================================== *}
{* ================================================================================ *}
{if $messages}
    <div class="sess_messages">
        {foreach key=tid item=message from=$messages}
            {$message}
        {/foreach}
    </div>
{/if}
	<div class="con_heading" style="margin-bottom:25px">{$LANG.MY_MESS}</div>
		<div style="margin-bottom:10px">				
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
				<span class="usr_msgmenu_active history_span">{$LANG.MESSEN} &rarr; {$with_name}</span>
			{/if}
		</div>
		
		{if $opt == 'in' || $opt == 'out' || $opt == 'history'}
		
			<table class="usr_msgmenu_bar" width="100%" height="30" border="0" cellpadding="5" cellspacing="0"><tr>
		
				<td><strong>{$LANG.MESS_INBOX}:</strong> {$msg_count}</td>
			
				{if $opt=='out'}
					<td align="center"><span style="color:gray">{$LANG.SENT_TEXT}</span></td>
				{/if}
				{if $opt=='in' && $msg_count>0}
					<td width="100" align="right"><a href="/users/{$id}/delmessages.html">{$LANG.CLEAN_CAT}</a></td>
				{/if}
			
			</tr></table>
									
			{if $is_mes}
					<div>
                    {foreach key=tid item=record from=$records}
						<table style="width:100%" cellspacing="0">
						<tr>
							<td class="usr_msg_title" width=""><strong>{$record.authorlink}</strong>, {$record.fpubdate}</td>
							{if $record.is_new}
								{if $opt=='in'}
									<td class="usr_msg_title" width="14" align="right"><img src="/components/users/images/warning.gif" /></td>
									<td class="usr_msg_title" width="20" align="right"><span style="color: red">{$LANG.NEW}!</span></td>
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
							{if $opt=='in' || $record.to_id==$usr_id}
								<td class="usr_msg_title" width="70" align="right"><a class="msg_delete" href="/users/delmsg{$record.id}.html">{$LANG.DELETE}</a></td>
							{/if}
						</tr>
						</table>
						<table style="width:100%; margin-bottom:8px; padding-bottom:10px;background-color:#FFFFFF; border-bottom:dashed 1px #666;" cellspacing="4">		
						<tr>						
							<td width="70" height="70" valign="middle" align="center" style="border:solid 1px silver">{$record.user_img}</td>			
							<td width="" valign="top"><div style="padding:6px">{$record.message}</div></td>
						</tr>
						</table>
					{/foreach}
					</div>
                    {if $msg_count > $perpage}
                        {$pagebar}
                    {/if}
				{else}
                <p>{$LANG.NOT_MESS_IN_CAT}</p>
                {/if}
	
		{/if}

		{if $opt == 'new'}
			<form action="" id="newmessage" method="POST" name="msgform">			
				<table class="usr_msgmenu_bar" width="100%" height="30" border="0" cellpadding="5" cellspacing="0"><tr>
					<tr>
						<td width="40"><strong>{$LANG.SEND_TO}:</strong> </td>
						<td width="160"><select name="id" id="to_id" style="width:150px">{$user_opt}</select></td>
						{if $is_admin}
							<td width="10"><input name="massmail" type="checkbox" value="1" /></td>					
							<td width="">{$LANG.SEND_TO_ALL}</td>
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
