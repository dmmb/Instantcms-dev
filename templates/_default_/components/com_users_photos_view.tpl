{* ================================================================================ *}
{* ========================= Просмотр фотографии ================================== *}
{* ================================================================================ *}
{strip}
{if $is_photo}
					<div class="con_heading">{$photo.title}</div>				
					<div class="con_description"><strong>{$LANG.CREATE_DATE}:</strong> {$photo.pubdate} &mdash; {$photo.genderlink} &mdash; <strong>{$LANG.HITS}:</strong> {$photo.hits} &mdash; <strong>{$LANG.SIZE}:</strong> {$photo.filesize} {$LANG.KBITE}</div>
					<div class="usr_photo_nav">
						<table cellpadding="5" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto"><tr>
							{if $previd}
								<td align="right">
									<div>&larr; <a href="/users/{$usr.id}/photo{$previd.id}.html">{$previd.title}</a></div>
								</td>
							{/if}
							{if $previd && $nextid} <td>|</td> {/if}
							{if $nextid}
								<td align="left">
									<div><a href="/users/{$usr.id}/photo{$nextid.id}.html">{$nextid.title}</a> &rarr;</div>
								</td>
							{/if}						
						</tr></table>
					</div>
					<div class="usr_photo_view">
							<a href="/images/users/photos/medium/{$photo.imageurl}" target="_blank"><img border="0" src="/images/users/photos/medium/{$photo.imageurl}" alt="{$photo.title}" /></a>
<!--					<div style="margin-top:15px">
                        <label for="bbcode">{$LANG.CODE_FOR_FORUM}: </label>
                        <input type="text" id="bbcode" name="bbcode" size="50" value="{$bbcode}"/>
                    </div>-->
					{if $photo.description}
                    	<div class="photo_desc">{$photo.description}</div>
                    {/if}		
					{if $myprofile || $is_admin}
						<div style="margin-top:5px">
							<a style="height:16px; line-height:16px; margin-right:5px; padding-left:20px; background:url(/components/users/images/edit.gif) no-repeat;" href="/users/{$usr.id}/editphoto{$photo.id}.html">{$LANG.EDIT}</a> 
							<a style="height:16px; line-height:16px; padding-left:20px; background:url(/components/users/images/delete.gif) no-repeat;"  href="/users/{$usr.id}/delphoto{$photo.id}.html">{$LANG.DELETE}</a> 
						</div>
					{/if}
					</div>
            {$tagbar}
{else}
<div class="con_heading">{$LANG.PHOTO_NOT_FOUND}</div>
<p>{$LANG.PHOTO_NOT_FOUND_TEXT}</p>
{/if}
{/strip}