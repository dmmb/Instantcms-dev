{* ================================================================================ *}
{* ============================== Создание блога ================================== *}
{* ================================================================================ *}

<div class="con_heading">{$LANG.CREATE_BLOG}</div>

<p><strong>{$LANG.BLOG}</strong> {$LANG.BLOG_DESCRIPTION}</p>
<form style="margin-top:15px" action="" method="post" name="addform">
  <table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="6">
	<tr>
	  <td width="180"><strong>{$LANG.BLOG_TITLE}: </strong></td>
	  <td><input name="title" type="text" id="title" size="40" /></td>
	</tr>
	<tr>
	  <td><strong>{$LANG.BLOG_TYPE}: </strong></td>
	  <td>
	  	  <select name="ownertype" id="ownertype">
			  <option value="single" selected>{$LANG.PERSONAL} {$min_karma_private}</option>
			  <option value="multi" >{$LANG.COLLECTIVE} {$min_karma_public}</option>
		  </select>
	  </td>
	</tr>
	<tr>
	  <td><strong>{$LANG.SHOW_BLOG}:</strong></td>
	  <td>
	  	<select name="allow_who" id="allow_who">
			<option value="all" selected="selected">{$LANG.TO_ALL}</option>
			<option value="friends" {if $friends eq 1}selected="selected"{/if}>{$LANG.TO_MY_FRIENDS}</option>
			<option value="nobody">{$LANG.TO_ONLY_ME}</option>
		</select>
	   </td>
	</tr>
  </table>			
  <p>
  	<input name="goadd" type="submit" id="goadd" value="{$LANG.CREATE_BLOG}" />
  	<input name="cancel" type="button" onclick="window.history.go(-1)" value="{$LANG.CANCEL}" />
  </p>
</form>