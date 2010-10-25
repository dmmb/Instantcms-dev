{* ================================================================================ *}
{* ======================== Ôמעמאכüבמל ןמכüחמגאעוכÿ =============================== *}
{* ================================================================================ *}

{if $my_profile}
    <div class="float_bar">
        <a href="/users/{$user_id}/addphoto.html" class="usr_photo_add">{$LANG.ADD_PHOTO}</a>
        {if $album_type == 'private'}
            <a href="/users/delalbum{$album.id}.html" onclick="if(!confirm('{$LANG.DELETE_ALBUM_CONFIRM}')){literal}{ return false; }{/literal}" class="usr_del_album">{$LANG.DELETE_ALBUM}</a>
        {/if}
    </div>
{/if}

<div class="con_heading">
    <a href="{profile_url login=$usr.login}">{$usr.nickname}</a> &rarr; {$page_title}
</div>

{if $album_type == 'public'}
    <div class="usr_photos_notice">{$LANG.IS_PUBLIC_ALBUM} <a href="/photos/{$album.id}">{$LANG.ALL_PUBLIC_PHOTOS}</a></div>
{/if}

{if $photos}

        {if $my_profile && $album_type == 'private'}
        <form action="/users/photos/editlist" method="post">
            <input type="hidden" name="album_id" value="{$album.id}" />
        {/if}

		<table width="" cellpadding="0" cellspacing="0" border="0">
            
            {assign var="maxcols" value="7"}
            {assign var="col" value="1"}
			
			{foreach key=id item=photo from=$photos}
				{if $col==1} <tr> {/if} 				
				<td valign="top" width="">
					<div class="usr_photo_thumb">
                        <a class="usr_photo_link" href="{$photo.url}" title="{$photo.title}">
                            <img border="0" src="{$photo.file}" alt="{$photo.title}"/>
                        </a>
                        <div>
                            <span class="usr_photo_date">{$photo.fpubdate}</span>
                            <span class="usr_photo_hits"><strong>{$LANG.HITS}:</strong> {$photo.hits}</span>
                        </div>
                        {if $my_profile && $album_type == 'private'}
                            <input type="checkbox" name="photos[]" class="photo_id" value="{$photo.id}" />
                        {/if}
                    </div>
				</td> 
				{if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
			{/foreach}

            {if $col>1}
				<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
			{/if}

		</table>

        {if $my_profile && $album_type == 'private'}
            <div class="usr_photo_sel_bar bar">
                {$LANG.SELECTED_ITEMS}:
                <input type="submit" name="edit" value="{$LANG.EDIT}" />
                <input type="submit" name="delete" value="{$LANG.DELETE}" />
            </div>
            </form>
        {/if}

		{$pagebar}
        
{else}
    <p>{$LANG.NOT_PHOTOS}</p>
{/if}