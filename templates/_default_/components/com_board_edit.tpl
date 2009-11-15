<form action="{$action}" method="post" enctype="multipart/form-data">
	<table cellpadding="2">
		<tr>
			<td width="150">
				<span>Заголовок:</span>
			</td>
			<td height="35">
				<select name="obtype" id="obtype" style="width:120px">
					{$obtypes}
				</select>
				<input name="title" type="text" id="title" style="width:280px" maxlength="250"  value="{$title}"/>
			</td>
		</tr>
		<tr class="proptable">
			<td>
				<span>Город:</span>
			</td>
			<td height="35" valign="top">
				<input name="city_ed" type="text" id="city_ed" style="width:182px" value="{$city}"/> или выберите {$cities}
			</td>
		</tr>
		<tr>
			<td valign="top">
				<span>Текст объявления:</span>
			</td>
			<td height="100" valign="top">
				<textarea name="content" style="width:400px" rows="5" id="content">{$content}</textarea>
			</td>
		</tr>
		{if $form_do == 'edit'}
			<tr>
				<td height="35"><span>Срок публикации:</span></td>
				<td height="35">{$pubdays} дней</td>
			</tr>
		{elseif $cfg.srok}
			<tr>
				<td><span>Срок публикации:</span></td>
				<td>
					<select name="pubdays" id="pubdays">
						<option value="5">5</option>
						<option value="10" selected="selected">10</option>
						<option value="14">14</option>
						<option value="30">30</option>
						<option value="50">50</option>
					</select> дней
				</td>
			</tr>
		{/if}
		{if $cfg.photos}
			<tr>
				<td><span>Фотография:</span></td>
				<td><input name="picture" type="file" id="picture" style="width:400px;" /></td>
			</tr>
			{if strlen($file)}
				<tr>
					<td height="30" valign="middle"><span>Удалить фотографию:</span></td>
					<td valign="middle"><input type="checkbox" name="delphoto" value="1" id="delphoto" /></td>
				</tr>
			{/if}
		{/if}

		{if $category_id}
			<tr>
				<td height="30"><span>Перенести в рубрику:</span></td>
				<td>
					<select name="category_id" id="category_id" style="width:400px">
						<option value="0">-- не переносить --</option>
						{$catslist}
					</select>
				</td>
			</tr>	
		{/if}
        {if !$is_admin}
		<tr>
			<td valign="top">&nbsp;</td>
			<td>{php}echo cmsPage::getCaptcha();{/php}</td>
		</tr>
        {/if}
		<tr>
			<td height="40" colspan="2" valign="middle">
				<input name="submit" type="submit" id="submit" style="margin-top:10px;font-size:18px" value="Сохранить объявление" />
			</td>
		</tr>
	</table>
</form>