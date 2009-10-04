<form style="margin-top:15px" action="" method="post" name="addform">
	<table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="6">
		<tr>
			<td width="192"><strong>Название рубрики: </strong></td>
			<td width="363"><input name="title" type="text" id="title" size="40" value="{$mod.title}"/></td>
		</tr>
	</table>
	<p>
		<input name="goadd" type="submit" id="goadd" value="Сохранить рубрику" /> 
		<input name="cancel" type="button" onclick="window.history.go(-1)" value="Отмена" />
	</p>
</form>
