{* ================================================================================ *}
{* ==================== Загрузка ававтара ========================================= *}
{* ================================================================================ *}

<form enctype="multipart/form-data" action="/users/{$id}/avatar.html" method="POST">
<p>{$LANG.SELECT_UPLOAD_FILE}: </p>
<input name="upload" type="hidden" value="1"/>		
<input name="userid" type="hidden" value="{$id}"/>
<input name="picture" type="file" id="picture" size="30" />
<p><input type="submit" value="{$LANG.UPLOAD}"> <input type="button" onclick="window.history.go(-1);" value="{$LANG.CANCEL}"/></p>
</form>
<p><a href="/users/{$id}/select-avatar.html" class="select-avatar">{$LANG.SELECT_AVATAR_FROM_COLL}</a></p>