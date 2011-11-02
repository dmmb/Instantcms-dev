{* ================================================================================ *}
{* ==================== Добавить файл ============================================= *}
{* ================================================================================ *}

{literal} 
<script type="text/javascript">
	  function startUpload(){
			$("#upload_btn").attr('disabled', 'true');
			$("#upload_btn").attr('value', '{/literal}{$LANG.LOADING}{literal} ...');
			$("#cancel_btn").css('display', 'none');
			$("#loadergif").css('display', 'block');
			document.uploadform.submit();													
	  }
</script> 
{/literal}
<div class="con_heading">{$LANG.UPLOAD_FILES}</div>
{if $free_mb > 0 || !$cfg.filessize}
<div>{$LANG.SELECT_FILE_TEXT}</div>
{if $cfg.filessize}
<div style="margin:10px 0px 0px 0px"><strong>{$LANG.YOUR_FILE_LIMIT}:</strong> {$free_mb} {$LANG.MBITE}</div>
{/if}
<div style="margin:0px 0px 10px 0px"><strong>{$LANG.MAX_FILE_SIZE}:</strong> {$post_max_mb}</div>
<div style="margin:0px 0px 10px 0px"><strong>{$LANG.TYPE_FILE}:</strong> {$types}</div>
<form action="" method="post" enctype="multipart/form-data" name="uploadform">
  <input name="MAX_FILE_SIZE" type="hidden" value="{$post_max_b}"/>
  <input type="file" class="multi" name="upfile[]" id="upfile" accept="{$types}" />
  <div style="margin-top:20px;overflow:hidden">
    <input style="float:left;margin-right:4px" type="button" name="upload_btn" id="upload_btn" value="{$LANG.UPLOAD_FILES}" onclick="startUpload()"/>
    <input style="float:left" type="button" name="cancel_btn" id="cancel_btn" value="{$LANG.CANCEL}" onclick="window.history.go(-1)" />
    <div id="loadergif" style="display:none;float:left;margin:6px"><img src="/images/ajax-loader.gif" border="0"/></div>
  </div>
  <input type="hidden" name="upload" value="1"/>
</form>
{else}
<div style="color:#660000;margin-bottom:10px;font-weight:bold">{$LANG.YOUR_FILE_LIMIT} ({$max_mb} {$LANG.MBITE}) {$LANG.IS_OVER_LIMIT}.</div>
<div style="color:#660000;font-weight:bold">{$LANG.FOR_NEW_FILE_DEL_OLD}</div>
<div style="margin-top:20px">
  <input type="button" name="cancel" value="{$LANG.CANCEL}" onclick="window.history.go(-1)" />
</div>
{/if} 