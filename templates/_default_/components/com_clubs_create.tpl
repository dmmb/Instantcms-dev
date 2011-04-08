{* ================================================================================ *}
{* =============================== Создание клуба ================================= *}
{* ================================================================================ *}

<div class="con_heading">{$LANG.CREATE_CLUB}</div>
<p>
	<strong>{$LANG.CLUBS}</strong> {$LANG.CLUBS_DESC}
</p>
<form style="margin-top:15px" action="" method="post" name="addform">
  <table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="10">
	<tr>
	  <td width="140">
	  	<strong>{$LANG.CLUB_NAME}: </strong>
	  </td>
	  <td>
	  	<input name="title" type="text" id="title" style="width:300px" />
	</td>
	</tr>
	<tr>
	  <td><strong>{$LANG.CLUB_TYPE}: </strong></td>
	  <td>
		  <select name="clubtype" id="clubtype" style="width:300px">
			<option value="public">{$LANG.PUBLIC} (public)</option>
			<option value="private">{$LANG.PRIVATE} (private)</option>
		  </select>
	  </td>
	</tr>
  </table>			
  <p style="margin-top:20px">
  	<input name="create" type="submit" id="create" value="{$LANG.CREATE_CLUB}" />
  	<input name="cancel" type="button" onclick="window.history.go(-1)" value="{$LANG.CANCEL}" />
  </p>
</form>