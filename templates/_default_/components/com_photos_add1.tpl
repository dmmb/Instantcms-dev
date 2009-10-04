{* ================================================================================ *}
{* ========================= Загрузка фото, Шаг 1 ================================= *}
{* ================================================================================ *}

<h3 style="border-bottom: solid 1px gray">
	<strong>Шаг 1</strong>: Загрузка файла
</h3>

<form enctype="multipart/form-data" action="{$form_action}" method="POST">
	<input name="upload" type="hidden" value="1"/>
	<input name="userid" type="hidden" value="{$user_id}"/>

	<p>Выберите файл для загрузки: </p>
	<input name="picture" type="file" id="picture" size="30" />
	
	<div style="margin-top:5px">
		<strong>Допустимые типы файлов:</strong> gif, jpg, jpeg, png
	</div>
	
	<p>
		<input type="submit" value="Загрузить"> 
		<input type="button" onclick="window.history.go(-1);" value="Отмена"/>
	</p>
</form>