<form style="margin-top:15px" action="" method="post" name="msgform" enctype="multipart/form-data">
	{if !$blog.showcats}
		<input type="hidden" name="cat_id" value="0"/>
	{/if}
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
			<td width="160"><strong>��������� ������: </strong></td>
		  	<td><input name="title" type="text" id="title" style="width:400px" value="{$mod.title}"/></td>
		</tr>

		{if $blog.showcats}
			<tr>
				<td><strong>������� �����:</strong></td>
				<td>
					<select name="cat_id" id="cat_id" style="width:400px">
						<option value="0" {if !isset($mod.cat_id) || $mod.cat_id==0}  selected {/if}>��� �������</option>
						{$cat_list}
					</select>
				</td>
			</tr>
		{/if}
		
		{if $myblog}
			<tr>
				<td><strong>���������� ������:</strong></td>
				<td>
					<select name="allow_who" id="allow_who" style="width:400px">
						<option value="all" {if !isset($mod.allow_who) || $mod.allow_who=='all'} selected {/if}>����</option>
						<option value="friends" {if $mod.allow_who=='friends'} selected {/if}>���� �������</option>
						<option value="nobody" {if $mod.allow_who=='nobody'} selected {/if}>������ ���</option>
					</select>
				</td>
			</tr>
		{else}
			<input type="hidden" name="allow_who" value="all" />
		{/if}
		
		<tr>
			<td><strong>���� ����������:</strong></td>
			<td><input name="feel" type="text" id="feel" style="width:400px" value="{$mod.feel}"/></td>
		</tr>
		<tr>
			<td><strong>������ ������:</strong></td>
			<td><input name="music" type="text" id="music" style="width:400px" value="{$mod.music}"/></td>
		</tr>			
		<tr>
			<td colspan="2">
				<div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
				{$smilies}
				{$autogrow}
				<div><textarea class="ajax_autogrowarea" name="content" id="message">{$msg}</textarea></div>
                <div style="margin-top:12px;margin-bottom:15px;">
                    <strong>�����:</strong> ���� ����� ����� ���������� �������, �� �������� ��������� ��� �� ��� ����� (����� � �������� ����), 
                    <a href="javascript:addTagCut('message');">������� �����������</a> ����� ����.
                </div>
			</td>
		</tr>
		<tr>
			<td>
				<strong>����:</strong><br />
				<span><small>�������� �����, ����� �������</small></span>
			</td>
			<td>
				<input name="tags" type="text" id="tags" style="width:400px" value="{$tagline}"/>
				
				<script type="text/javascript">
					{$autocomplete_js}
				</script>
			</td>
		</tr>
	</table>
	<p>
		<input name="goadd" type="submit" id="goadd" value="��������� ������" /> 
		<input name="cancel" type="button" onclick="window.history.go(-1)" value="������" />
	</p>
</form>