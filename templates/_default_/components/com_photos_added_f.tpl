{* ================================================================================ *}
{* ========================= фото успешно загружено =============================== *}
{* ================================================================================ *}

<p><strong>{$LANG.PHOTO_ADDED}</strong></p>
{if !$photo.published} <p>{$LANG.PHOTO_PREMODER_TEXT}</p>{/if}
<ul>
<li><a href="/photos/photo{$id}.html">{$LANG.GOTO_PHOTO}</a></li>
<li><a href="/photos/{$photo.album_id}/addphoto.html">{$LANG.ADD_MORE_PHOTO}</a></li>
<li><a href="/photos/{$photo.album_id}">{$LANG.BACK_TO_PHOTOALBUM}</a></li>
</ul>