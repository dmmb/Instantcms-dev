<form action="{$action}" method="post" enctype="multipart/form-data">
	<table cellpadding="2">
		<tr>
			<td width="150">
				<span>{$LANG.TITLE}:</span>
			</td>
			<td height="35">
				<select name="obtype" id="obtype" style="width:120px">
					{$obtypes}
				</select>
				<input name="title" type="text" id="title" style="width:280px" maxlength="250"  value="{$title|escape:'html'}"/>
			</td>
		</tr>
		<tr class="proptable">
			<td>
				<span>{$LANG.CITY}:</span>
			</td>
			<td height="35" valign="top">
				<input name="city_ed" type="text" id="city_ed" style="width:182px" value="{$city|escape:'html'}"/> {$LANG.OR_SELECTING} {$cities}
			</td>
		</tr>
		<tr>
			<td valign="top">
				<span>{$LANG.TEXT_ADV}:</span>
			</td>
			<td height="100" valign="top">
				<textarea name="content" style="width:400px" rows="5" id="content">{$content}</textarea>
			</td>
		</tr>
		{if $form_do == 'edit'}
			<tr>
				<td height="35"><span>{$LANG.PERIOD_PUBL}:</span></td>
				<td height="35">{$pubdays} {$LANG.DAYS}, {$LANG.DAYS_TO} {$pubdate}.</td>
			</tr>
		{elseif $cfg.srok}
			<tr>
				<td><span>{$LANG.PERIOD_PUBL}:</span></td>
				<td>
					<select name="pubdays" id="pubdays">
						<option value="5">5</option>
						<option value="10" selected="selected">10</option>
						<option value="14">14</option>
						<option value="30">30</option>
						<option value="50">50</option>
					</select>  {$LANG.DAYS}
				</td>
			</tr>
		{/if}
        {if $cfg.extend && $form_do == 'edit' && !$published && $is_overdue}
        	{if $cfg.srok}
                <tr>
                    <td height="35"><span>{$LANG.ADV_EXTEND}:</span></td>
                    <td height="35">
                        <select name="pubdays" id="pubdays">
                            <option value="5">5</option>
                            <option value="10" selected="selected">10</option>
                            <option value="14">14</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                        </select>  {$LANG.DAYS}</td>
                </tr>
            {else}
                <tr>
                    <td height="35"><span>{$LANG.ADV_EXTEND}:</span></td>
                    <td height="35">{$LANG.ADV_EXTEND_SROK} {$pubdays} {$LANG.DAYS}</td>
                </tr>
            {/if}
        {/if}
		{if $cfg.photos && $cat.is_photos}
			<tr>
				<td><span>{$LANG.PHOTO}:</span></td>
				<td><input name="picture" type="file" id="picture" style="width:400px;" /></td>
			</tr>
			{if strlen($file)}
				<tr>
					<td height="30" valign="middle"><span>{$LANG.DEL_PHOTO}:</span></td>
					<td valign="middle"><input type="checkbox" name="delphoto" value="1" id="delphoto" /></td>
				</tr>
			{/if}
		{/if}

		{if $category_id}
			<tr>
				<td height="30"><span>{$LANG.MOVE_TO_CAT}:</span></td>
				<td>
					<select name="category_id" id="category_id" style="width:400px">
						<option value="0">-- {$LANG.DONT_MOVE} --</option>
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
				<input name="submit" type="submit" id="submit" style="margin-top:10px;font-size:18px" value="{$LANG.SAVE_ADV}" />
			</td>
		</tr>
	</table>
</form>