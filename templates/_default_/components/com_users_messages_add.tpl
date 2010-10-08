{* ================================================================================ *}
{* ======================== Написать сообщение ==================================== *}
{* ================================================================================ *}
{if $messages}
    <div class="sess_messages">
        {foreach key=id item=message from=$messages}
            {$message}
        {/foreach}
    </div>
{/if}
{if $is_reply_user}
<div>
  <div class="con_heading">{$LANG.ORIGINAL_MESS}</div>
  <div class="usr_msgreply_source">
    <div class="usr_msgreply_sourcetext">{$msg.message}</div>
    <div class="usr_msgreply_author"><a href="{profile_url login=$msg.login}">{$msg.nickname}</a>, {$msg.senddate}</div>
  </div>
</div>
{/if}
<div class="con_heading">{$LANG.SEND_MESS}</div>
<table width="100%" cellpadding="0" cellspacing="5">
  <tr>
    <td width="200" height="200" valign="top">
    <div style="background-color:#FFFFFF;padding:5px;border:solid 1px gray;text-align:center"><a href="{profile_url login=$usr.login}">{$usr.avatar}</a></div>
      <div style="padding:5px;width:100%"> Кому: <a href="{profile_url login=$usr.login}">{$usr.nickname}</a></div></td>
    <td valign="top"><form action="" method="POST" name="msgform">
        <div class="usr_msg_bbcodebox">{$bbcodetoolbar}</div>
        {$smilestoolbar}
        <textarea style="font-size:18px;border:solid 1px gray;width:100%;height:200px;" name="message" id="message"></textarea>
        {if $id_admin}
        <input name="massmail" type="checkbox" value="1" />
        {$LANG.SEND_TO_ALL}
        {/if}
        <div style="margin-top:6px;">
          <input type="submit" name="gosend" value="{$LANG.SEND}" style="font-size:18px"/>
          <input type="button" name="gosend" value="{$LANG.CANCEL}" style="font-size:18px" onclick="window.history.go(-1)"/>
        </div>
      </form></td>
  </tr>
</table>
