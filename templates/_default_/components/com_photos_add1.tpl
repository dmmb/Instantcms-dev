{* ================================================================================ *}
{* ========================= Загрузка фото, Шаг 1 ================================= *}
{* ================================================================================ *}
{literal}
<script type="text/javascript">
		$(document).ready(function() {
			$('#title').focus();
			
			$("#id").change(function () {
		
				var cat_id = "";
				$("#id option:selected").each(function () {
					cat_id = $(this).val();
				});
				if(cat_id != 0) {
					$("#add_form").attr("action", '/photos/'+cat_id+'/addphoto.html');
				} else {
					$("#add_form").attr("action", "");
				}
        
        })
        .change();
			
		});
    function mod_text(){
        if ($('#only_mod').attr('checked')){{/literal}
			$('#text_mes').html('<strong>{$LANG.STEP} 1</strong>: {$LANG.PHOTO_DESCS}.');
			$('#text_title').html('{$LANG.PHOTO_TITLES}:');
			$('.usr_photos_notice').show();
			$('#text_desc').html('{$LANG.PHOTO_DESCS}:');{literal}
        } else {{/literal}
			$('#text_mes').html('<strong>{$LANG.STEP} 1</strong>: {$LANG.PHOTO_DESC}.');
			$('#text_title').html('{$LANG.PHOTO_TITLE}:');
			$('.usr_photos_notice').hide();
			$('#text_desc').html('{$LANG.PHOTO_DESC}:');{literal}
        }
    }

</script>
{/literal}

<h3 style="border-bottom: solid 1px gray" id="text_mes">
	<strong>{$LANG.STEP} 1</strong>: {$LANG.PHOTO_DESC}.
</h3>
<div class="usr_photos_notice" style="display:none;">{$LANG.PHOTO_PLEASE_NOTE}</div>
<form action="{$form_action}" method="POST">
	<input type="hidden" name="imageurl" value="{$filename}"/>
	<table width="500">
		<tr>
			<td width="140" id="text_title">{$LANG.PHOTO_TITLE}: </td>
			<td>
				<input name="title" type="text" id="title" class="text-input" style="width:350px;" maxlength="250" value="{$mod.title|escape:'html'}" />
			</td>
		</tr>
		<tr>
			<td valign="top" id="text_desc">{$LANG.PHOTO_DESC}: </td>
			<td valign="top">
				<textarea name="description" style="width:350px;" rows="5" id="description">{$mod.description}</textarea>
			</td>
		</tr>
		<tr>
			<td>Тэги:</td>
			<td>
				<input name="tags" type="text" id="tags" class="text-input" style="width:350px;" value="{$mod.tags|escape:'html'}"/>
				<div><small>{$LANG.KEYWORDS}</small></div>
				<script type="text/javascript">
					{$autocomplete_js}
				</script>
			</td>
		</tr>
		<tr>
			<td colspan="2" valign="top">
		    <input id="only_mod" name="only_mod" type="checkbox" value="1" onclick="mod_text()" />  <label for="only_mod">{$LANG.ADD_MULTY}</label></td>
		</tr>
		<tr>
			<td colspan="2" valign="top"><input type="submit" name="submit" id="text_subm" value="{$LANG.GO_TO_UPLOAD}" /></td>
		</tr>
	</table>							
</form>