{* ================================================================================ *}
{* ======================== Фотоальбом пользователя =============================== *}
{* ================================================================================ *}

{if $my_profile}
    <table cellspacing="0" cellpadding="2" style="margin-bottom: 10px;">
        <tbody>
            <tr>
                <td><img border="0" src="/components/photos/images/addphoto.gif"/></td>
                <td><a href="/users/{$menuid}/{$user_id}/addphoto.html" style="text-decoration: underline;">Добавить фото</a></td></tr>
        </tbody>
    </table>
{/if}

{if $photos}

		<table width="" cellpadding="0" cellspacing="0" border="0">
            
            {assign var="maxcols" value="4"}
            {assign var="col" value="1"}
			
			{foreach key=id item=photo from=$photos}
				{if $col==1} <tr> {/if} 				
				<td valign="top" width="">
					<div class="usr_photo_thumb">
                        <a class="usr_photo_link" href="{$photo.url}" title="{$photo.title}">
                            <img border="0" src="{$photo.file}" alt="{$photo.title}"/>
                        </a>
                        <div style="padding:4px;">
                            <span style="font-size:10px; display:block"><strong>{$_LANG.DATE}:</strong> {$photo.fpubdate}</span>
                            <span style="font-size:10px; display:block"><strong>{$_LANG.HITS}:</strong> {$photo.hits}</span>
                        </div>
                    </div>
				</td> 
				{if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
			{/foreach}

            {if $col>1}
				<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
			{/if}

		</table>

		{$pagebar}
        
{else}
    <p>{$LANG.NOT_PHOTOS}</p>
{/if}