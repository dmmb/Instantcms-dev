{* ================================================================================ *}
{* =============================== Создание клуба ================================= *}
{* ================================================================================ *}

<div class="con_heading">Создать клуб</div>

<p>
	<strong>Клубы</strong> позволяют людям объединяться. Создавая новый клуб вы автоматически становитесь его администратором.
<p>
Вы сможете назначать модераторов и устанавливать ограничения на публикацию постов и фотографий.
</p>

<p>
	Сейчас вам нужно указать название клуба и его тип. Расширенные настройки будут доступны после создания клуба.
</p>

<form style="margin-top:15px" action="" method="post" name="addform">
  <table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="10">
	<tr>
	  <td width="140">
	  	<strong>Название клуба: </strong>
	  </td>
	  <td>
	  	<input name="title" type="text" id="title" style="width:300px" />
	</td>
	</tr>
	<tr>
	  <td><strong>Тип клуба: </strong></td>
	  <td>
		  <select name="clubtype" id="clubtype" style="width:300px">
			<option value="public">Открыт для всех (public)</option>
			<option value="private">Открыт для избранных (private)</option>
		  </select>
	  </td>
	</tr>
  </table>			
  <p>
  	<input name="create" type="submit" id="create" value="Создать клуб" /> 
  	<input name="cancel" type="button" onclick="window.history.go(-1)" value="Отмена" />
  </p>
</form>