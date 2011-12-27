{* ================================================================================ *}
{* ================= Разослать сообщение участникам клуба ========================= *}
{* ================================================================================ *}
{literal}
<script type="text/javascript">

    function mod_text(){
        if ($('#only_mod').attr('checked')){
			$('#text_mes').html('{/literal}{$LANG.SEND_MESSAGE_TEXT_MOD} "{$club.title|escape:'html'}"{literal}.');
        } else {
			$('#text_mes').html('{/literal}{$LANG.SEND_MESSAGE_TEXT} "{$club.title|escape:'html'}".{literal}');
        }
    }

</script>
{/literal}
<div class="con_heading">{$LANG.SEND_MESSAGE}</div>

<p id="text_mes">{$LANG.SEND_MESSAGE_TEXT} "{$club.title}".</p>
<form action="" method="POST" name="msgform">
        <div class="usr_msg_bbcodebox">{$bbcodetoolbar}</div>
        {$smilestoolbar}
        <textarea style="font-size:18px;border:solid 1px gray;width:100%;height:200px;" name="message" id="message"></textarea>
        <div style="margin-top:6px;">
          <input type="submit" name="gosend" value="{$LANG.SEND}" style="font-size:18px"/>
          <input type="button" name="gosend" value="{$LANG.CANCEL}" style="font-size:18px" onclick="window.history.go(-1)"/>
          <label><input id="only_mod" name="only_mod" type="checkbox" value="1" onclick="mod_text()" /> {$LANG.MESSAGE_ONLY_MODERS}</label>
        </div>
</form>