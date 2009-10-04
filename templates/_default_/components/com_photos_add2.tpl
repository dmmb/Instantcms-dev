{* ================================================================================ *}
{* ========================= Загрузка фото, Шаг 2 ================================= *}
{* ================================================================================ *}

<h3 style="border-bottom: solid 1px gray">
	<strong>Шаг 2</strong>: Описание фотографии
</h3>

<form action="{$form_action}" method="POST">
	<input type="hidden" name="imageurl" value="{$filename}"/>
	<table width="500">
		<tr>
			<td width="140">Название фото: </td>
			<td>
				<input name="title" type="text" id="title" style="width:350px;" maxlength="250" />
			</td>
		</tr>
		<tr>
			<td valign="top">Описание фото: </td>
			<td valign="top">
				<textarea name="description" style="width:350px;" rows="5" id="description"></textarea>
			</td>
		</tr>
		<tr>
			<td>Тэги:</td>
			<td>
				<input name="tags" type="text" id="tags" style="width:350px;"/>
				<div><small>ключевые слова, через запятую</small></div>
				<script type="text/javascript">
					{$autocomplete_js}
				</script>
			</td>
		</tr>
		{if isset($allow_who)}
			<tr>
				<td>Показывать:</td>
				<td>
					<select name="allow_who" id="allow_who" style="width:350px;">
						<option value="all">Всем</option>
						<option value="registered">Зарегистрированным</option>
						<option value="friends">Моим друзьям</option>
					</select>
				</td>
			</tr>
		{/if}
		<tr>
			<td valign="top">&nbsp;</td>
			<td valign="top"><input type="submit" name="submit" value="Сохранить" /></td>
		</tr>
	</table>							
</form>