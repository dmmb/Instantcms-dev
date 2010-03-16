{* ================================================================================ *}
{* ====================== Форма добавления вопроса FAQ ============================ *}
{* ================================================================================ *}

<div class="con_heading">{$LANG.SET_QUESTION}</div>

<div style="margin-top:10px">{$LANG.SET_QUESTION_TEXT}</div>
<div style="margin-bottom:10px">{$LANG.CONTACTS_TEXT}</div>

<form action="" method="POST" name="questform">
	<table cellpadding="4" cellspacing="0">
		<tr>
			<td>
				<strong>{$LANG.CAT_QUESTIONS}: </strong>
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
		<input type="button" style="font-size:16px;margin-right:2px;margin-top:3px;" onclick="sendQuestion()" name="gosend" value="{$LANG.SEND}"/>
		<input type="button" style="font-size:16px;margin-top:3px;" name="cancel" onclick="window.history.go(-1)" value="{$LANG.CANCEL}"/>
	</div>
</form>