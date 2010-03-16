{* ================================================================================ *}
{* ========================= Редактирование фото ================================== *}
{* ================================================================================ *}

<form action="/users/{$menuid}/{$id}/editphoto{$photoid}.html" method="POST" enctype="multipart/form-data">
<input type="hidden" name="imageurl" value="{$photo.imageurl}" />
	<table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="120" valign="top">
            	<table width="110" border="0" cellspacing="0" cellpadding="0"><tr>
            	<td width="110" align="center" valign="top" style="border:solid 1px gray; padding:5px; background-color:#FFFFFF;"><img alt="" src="/images/users/photos/small/{$photo.imageurl}" border="0" style="border:solid 1px black" /></td>
        		</tr></table>
   			</td>
            <td width="460" align="right" valign="top">
            	<table width="460">
                  <tr>
                      <td valign="top"><strong>{$_LANG.PHOTO_TITLE}: </strong></td>
                  </tr>
                  <tr>
                      <td valign="top"><input name="title" type="text" id="title" size="40" maxlength="250" value="{$photo.title}"/></td>
                  </tr>
                  <tr>
                      <td valign="top"><strong>{$_LANG.PHOTO_DESCRIPTION}:</strong> </td>
                  </tr>
                  <tr>
                      <td valign="top"><textarea name="description" cols="39" rows="5" id="description">{$photo.description}</textarea></td>
                  </tr>
                  <tr>
                      <td valign="top"><strong>{$_LANG.TAGS}:</strong></td>
                  </tr>
                  <tr>
                     <td valign="top"><input name="tags" type="text" id="tags" size="40" value="{$photo_tag}"/>
                                              <br />
                                              <span><small>{$_LANG.KEYWORDS}</small></span></td>
                  </tr>
                  <tr>
                     <td valign="top"><strong>{$_LANG.REPLACE_FILE}</strong>:</td>
                  </tr>
                  <tr>
                     <td valign="top"><input name="MAX_FILE_SIZE" type="hidden" value="{$photo_max_size}"/>
                                              <input name="picture" type="file" size="30" /></td>
                  </tr>
                  <tr>
                     <td valign="top"><strong>{$_LANG.SHOW}:</strong></td>
                  </tr>
                  <tr>
                     <td valign="top">
                     		<select name="allow_who" id="allow_who">
                            <option value="all" {if $photo.allow_who==all} selected {/if}>{$_LANG.EVERYBODY}</option>
                            <option value="registered" {if $photo.allow_who==registered} selected {/if}>{$_LANG.REGISTERED}</option>
                            <option value="friends" {if $photo.allow_who==friends} selected {/if}>{$_LANG.MY_FRIENDS}</option>
                            </select>
                     </td>
                  </tr>
                  <tr>
                     <td valign="top">
                            <input style="margin-top:10px;font-size:18px" type="submit" name="save" value="{$_LANG.SAVE}" />
							<input style="margin-top:10px;font-size:18px" type="button" name="cancel" value="{$_LANG.CANCEL}" onclick="window.history.go(-1)"/>
                     </td>
                 </tr>
               </table>
              </td>
         </tr>
   </table>
</form>