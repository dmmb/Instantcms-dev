{* ================================================================================ *}
{* ========================= Перемещение фото ===================================== *}
{* ================================================================================ *}
<div class="con_heading">{$LANG.MOVE_PHOTO}</div>
		<div style="margin-top:10px; margin-bottom:15px;"><strong>{$LANG.PHOTO}:</strong> <a href="/photos/photo{$photo.id}.html">{$photo.title}</a></div>
		<div>
        	<form action="" method="POST">
				<table border="0" cellspacing="10" style="background-color:#EBEBEB">
            		<tr>
                		<td>{$LANG.MOVE_INTO_ALBUM}:</td>
						<td><select name="album_id">{$html}</select></td>
						<td><input type="submit" name="gomove" value="{$LANG.MOVING}"/></td>
                	</tr>
            	</table>
			</form>
        </div>