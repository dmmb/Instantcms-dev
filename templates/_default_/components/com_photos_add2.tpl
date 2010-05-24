{* ================================================================================ *}
{* ========================= Загрузка фото, Шаг 2 ================================= *}
{* ================================================================================ *}

<h3 style="border-bottom: solid 1px gray">
	<strong>{$LANG.STEP} 2</strong>: {$LANG.PHOTO_DESC}
</h3>

<form action="{$form_action}" method="POST">
	<input type="hidden" name="imageurl" value="{$filename}"/>
	<table width="500">
		<tr>
			<td width="140">{$LANG.PHOTO_TITLE}: </td>
			<td>
				<input name="title" type="text" id="title" style="width:350px;" maxlength="250" />
			</td>
		</tr>
		<tr>
			<td valign="top">{$LANG.PHOTO_DESCRIPTION}: </td>
			<td valign="top">
				<textarea name="description" style="width:350px;" rows="5" id="description"></textarea>
			</td>
		</tr>
		<tr>
			<td>Тэги:</td>
			<td>
				<input name="tags" type="text" id="tags" style="width:350px;"/>
				<div><small>{$LANG.KEYWORDS}</small></div>
				<script type="text/javascript">
					{$autocomplete_js}
				</script>
			</td>
		</tr>
		{if isset($allow_who)}
			<tr>
				<td>{$LANG.SHOW}:</td>
				<td>
					<select name="allow_who" id="allow_who" style="width:350px;">
						<option value="all">{$LANG.TO_ALL}</option>
						<option value="registered">{$LANG.TO_REGISTERED}</option>
						<option value="friends">{$LANG.TO_MY_FRIEND}</option>
					</select>
				</td>
			</tr>
		{/if}
		<tr>
			<td valign="top">&nbsp;</td>
			<td valign="top"><input type="submit" name="submit" value="{$LANG.SAVE}" /></td>
		</tr>
	</table>							
</form>