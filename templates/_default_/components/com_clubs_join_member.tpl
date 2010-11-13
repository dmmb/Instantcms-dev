{* ================================================================================ *}
{* ========================= пригласить в группу ================================== *}
{* ================================================================================ *}

<div class="con_heading">{$LANG.SEND_INVITE_CLUB} "{$club.title}"</div>

        <h3>{$LANG.SELECT_FRIEND}!</h3>

<form action="" method="post" name="addform">
<h4><select name="usr_to_id" id="usr_to_id" style="width:200px">{$friends}</select></h4>
<div style="margin-top:10px;">
   <input type="submit" name="join" value="{$LANG.INVITE}" style="font-size:18px"/>
   <input type="button" name="gosend" value="{$LANG.CANCEL}" style="font-size:18px" onclick="window.history.go(-1)"/>
</div>
</form>