{* ================================================================================ *}
{* ========================= Загрузка фото, Шаг 1 ================================= *}
{* ================================================================================ *}

<h3 style="border-bottom: solid 1px gray">
	<strong>{$LANG.STEP} 1</strong>: {$LANG.FILE_UPLOAD}
</h3>

<form enctype="multipart/form-data" action="{$form_action}" method="POST">
	<input name="upload" type="hidden" value="1"/>
	<input name="userid" type="hidden" value="{$user_id}"/>

	<p>{$LANG.SELECT_FILE_TO_UPLOAD}: </p>
	<input name="picture" type="file" id="picture" size="30" />
	
	<div style="margin-top:5px">
		<strong>{$LANG.ALLOW_FILE_TYPE}:</strong> gif, jpg, jpeg, png
	</div>
	
	<p>
		<input type="submit" value="{$LANG.LOAD}">
		<input type="button" onclick="window.history.go(-1);" value="{$LANG.CANCEL}"/>
	</p>
</form>