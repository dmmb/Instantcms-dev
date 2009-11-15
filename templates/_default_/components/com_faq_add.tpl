{* ================================================================================ *}
{* ====================== Форма добавления вопроса FAQ ============================ *}
{* ================================================================================ *}

<div class="con_heading">Задать вопрос</div>

<div style="margin-top:10px">Опишите вашу проблему. Вопрос будет опубликован вместе с ответом, после того как его проверит администратор.</div>
<div style="margin-bottom:10px">Укажите свое имя и контакты, если хотите чтобы с вами связались лично.</div>

<form action="" method="POST" name="questform">
	<table cellpadding="4" cellspacing="0">
		<tr>
			<td>
				<strong>Категория вопроса: </strong>
			</td>
			<td>
				<select name="category_id" style="width:300px">
					{$catslist}
				</select>
			</td>
		</tr>
	</table>

	<textarea name="message" id="message" style="border:solid 1px #666666;width:421px;height:200px"></textarea>
	
	<div>
		<input type="button" style="font-size:16px;margin-right:2px;margin-top:3px;" onclick="sendQuestion()" name="gosend" value="Отправить"/>
		<input type="button" style="font-size:16px;margin-top:3px;" name="cancel" onclick="window.history.go(-1)" value="Отмена"/>
	</div>
</form>