<form style="margin-top:15px" action="" method="post" name="msgform" enctype="multipart/form-data">
	{if !$blog.showcats}
		<input type="hidden" name="cat_id" value="0"/>
	{/if}
	<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
			<td width="160"><strong>Заголовок записи: </strong></td>
		  	<td><input name="title" type="text" id="title" style="width:400px" value="{$mod.title}"/></td>
		</tr>

		{if $blog.showcats}
			<tr>
				<td><strong>Рубрика блога:</strong></td>
				<td>
					<select name="cat_id" id="cat_id" style="width:400px">
						<option value="0" {if !isset($mod.cat_id) || $mod.cat_id==0}  selected {/if}>Без рубрики</option>
						{$cat_list}
					</select>
				</td>
			</tr>
		{/if}
		
		{if $myblog}
			<tr>
				<td><strong>Показывать запись:</strong></td>
				<td>
					<select name="allow_who" id="allow_who" style="width:400px">
						<option value="all" {if !isset($mod.allow_who) || $mod.allow_who=='all'} selected {/if}>Всем</option>
						<option value="friends" {if $mod.allow_who=='friends'} selected {/if}>Моим друзьям</option>
						<option value="nobody" {if $mod.allow_who=='nobody'} selected {/if}>Только мне</option>
					</select>
				</td>
			</tr>
		{else}
			<input type="hidden" name="allow_who" value="all" />
		{/if}
		
		<tr>
			<td><strong>Ваше настроение:</strong></td>
			<td><input name="feel" type="text" id="feel" style="width:400px" value="{$mod.feel}"/></td>
		</tr>
		<tr>
			<td><strong>Играет музыка:</strong></td>
			<td><input name="music" type="text" id="music" style="width:400px" value="{$mod.music}"/></td>
		</tr>			
		<tr>
			<td colspan="2">
				<div class="usr_msg_bbcodebox">{$bb_toolbar}</div>
				{$smilies}
				{$autogrow}
				<div><textarea class="ajax_autogrowarea" name="content" id="message">{$msg}</textarea></div>
                <div style="margin-top:12px;margin-bottom:15px;">
                    <strong>Важно:</strong> если текст поста достаточно большой, не забудьте разделить его на две части (анонс и основное тело), 
                    <a href="javascript:addTagCut('message');">вставив разделитель</a> между ними.
                </div>
			</td>
		</tr>
		<tr>
			<td>
				<strong>Теги:</strong><br />
				<span><small>Ключевые слова, через запятую</small></span>
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
		<input name="goadd" type="submit" id="goadd" value="Сохранить запись" /> 
		<input name="cancel" type="button" onclick="window.history.go(-1)" value="Отмена" />
	</p>
</form>