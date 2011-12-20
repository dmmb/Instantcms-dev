{* ================================================================================ *}
{* =================== Форма награждения пользователя ============================= *}
{* ================================================================================ *}
<div class="con_heading">{$LANG.AWARD_USER}</div>
<form action="" method="POST" name="addform" id="addform">
  <table width="100%" cellpadding="0" cellspacing="5">
    <tr>
      <td width="150" valign="middle">{$LANG.AWARD_IMG}:</td>
      <td valign="middle"><div style="overflow:hidden;_height:1%">{$awardslist}</div></td>
    </tr>
    <tr>
      <td width="150">{$LANG.AWARD_NAME}:</td>
      <td><input type="text" name="title" size="35" /></td>
    </tr>
    <tr>
      <td width="150">{$LANG.AWARD_DESC}:</td>
      <td><textarea name="description" cols="35" rows="4"></textarea></td>
    </tr>
  </table>
  <div style="margin-top:6px;">
    <input type="submit" name="gosend" value="{$LANG.TO_AWARD}" style="font-size:18px"/>
    <input type="button" name="gosend" value="{$LANG.CANCEL}" style="font-size:18px" onclick="window.history.go(-1)"/>
  </div>
</form>
