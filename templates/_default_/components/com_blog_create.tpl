{* ================================================================================ *}
{* ============================== Создание блога ================================== *}
{* ================================================================================ *}

<div class="con_heading">Создать блог</div>

<p><strong>Блог</strong> - это отличный способ поделиться мыслями с миром. В отличие от обычного бумажного дневника, который
не принято показывать окружающим, интернет-дневник создан для общения. Публикуемые в блогe записи могут комментироваться другими пользователями.</p>
<p>Расскажите всем о своей ежедневной жизни, поделитесь своим творчеством или просто создавайте поводы для дискуссий.</p>
<p>Создайте персональный интернет-блог - покажите себя миру!</p>
<form style="margin-top:15px" action="" method="post" name="addform">
  <table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="6">
	<tr>
	  <td width="180"><strong>Название блога: </strong></td>
	  <td><input name="title" type="text" id="title" size="40" /></td>
	</tr>
	<tr>
	  <td><strong>Тип блога: </strong></td>
	  <td>
	  	  <select name="ownertype" id="ownertype">
			  <option value="single" selected>Персональный {$min_karma_private}</option>
			  <option value="multi" >Коллективный {$min_karma_public}</option>
		  </select>
	  </td>
	</tr>
	<tr>
	  <td><strong>Показывать:</strong></td>
	  <td>
	  	<select name="allow_who" id="allow_who">
			<option value="all" selected="selected">Всем</option>
			<option value="friends" {if $friends eq 1}selected="selected"{/if}>Моим друзьям</option>
			<option value="nobody">Только мне</option>
		</select>
	   </td>
	</tr>
  </table>			
  <p>
  	<input name="goadd" type="submit" id="goadd" value="Создать блог" /> 
  	<input name="cancel" type="button" onclick="window.history.go(-1)" value="Отмена" />
  </p>
</form>