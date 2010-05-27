{* ================================================================================ *}
{* ========================= Просмотр фотографии ================================== *}
{* ================================================================================ *}
{strip}
<h1 class="con_heading">{$photo.title}</h1>
		<table width="100%" cellpadding="5" cellspacing="0">
			<tr>
            	<td colspan="3" align="center">
                {if $photo.description}
                	<div class="photo_desc">
                    	{$photo.description}
                    </div>
                {/if}
                </td>
            </tr>	
			<tr>
            	<td colspan="3" align="center">
					<div>&larr; {$LANG.BACK_TO} <a href="/photos/{$photo.cat_id}">{$LANG.TO_ALBUM}</a>
						{if $photo.NSDiffer==''} | <a href="/photos">{$LANG.TO_LIST_ALBUMS}</a>{/if}
            		</div>
				</td>
            </tr>
			<tr>
				<td style="text-align:center"><img src="/images/photos/medium/{$photo.file}" border="0" /></td>
			</tr>
			{if $photo.a_bbcode}
			<tr>
            	<td style="text-align:center">		
					<label for="bbcode">{$LANG.CODE_INPUT_TO_FORUMS}: </label>
                    <input type="text" id="bbcode" name="bbcode" class="photo_bbinput" value="{$bbcode}"/>
				</td>
            </tr>
			{/if}
			{if $photo.album_nav}
			<tr>
            	<td>
					<div class="photo_nav">
						<table cellpadding="5" cellspacing="0" border="0" align="center" style="margin-left:auto;margin-right:auto">
                        	<tr>
							{if $previd}
								<td align="right">
									<div>&larr; <a href="/photos/photo{$previd.id}.html">{$LANG.PREVIOUS}</a></div>
								</td>
							{/if}
							{if $previd && $nextid} <td>|</td> {/if}
							{if $nextid}
								<td align="left">
									<div><a href="/photos/photo{$nextid.id}.html">{$LANG.NEXT}</a> &rarr;</div>
								</td>
							{/if}						
							</tr>
                        </table>
					</div>			
				</td>
            </tr>
			{/if}
		</table>
				
		{if $photo.a_type != 'simple'}
			<div class="photo_bar">
				<table width="" cellspacing="0" cellpadding="4" align="center">
                	<tr>
						<td width=""><strong>{$LANG.ADDED}:</strong> {$photo.pubdate}</td>
						{if $photo.public}
							{if $usr.id}							
							<td>{$usr.genderlink}</td>
							{/if}
						{/if}
						<td width=""><strong>{$LANG.HITS}: </strong> {$photo.hits}</td>
						<td width=""><strong>{$LANG.RATING}: </strong><span id="karmapoints">{$photo.karma}</span></td>
						<td width="">{$photo.karma_buttons}</td>
						{if $cfg.link}
							<td>{$photo.file_orig}</td>
						{/if}
						{if $is_can_operation}
							{if $is_author || $is_admin}
								<td><a href="/photos/editphoto{$photo.id}.html" title="{$LANG.EDIT}"><img src="/images/icons/edit.gif" border="0"/></a></td>
							{if is_admin}
								<td><a href="/photos/movephoto{$photo.id}.html" title="{$LANG.MOVE}"><img src="/images/icons/move.gif" border="0"/></a></td>
							{/if}
							<td><a href="/photos/delphoto{$photo.id}.html" title="{$LANG.DELETE}"><img src="/images/icons/delete.gif" border="0"/></a></td>
							{/if}
                        {/if}
					</tr>
               </table>
			</div>
            {$tagbar}
        {/if}
{/strip}