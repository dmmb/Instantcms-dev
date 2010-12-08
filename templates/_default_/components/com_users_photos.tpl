{* ================================================================================ *}
{* ======================== Ôמעמאכüבמל ןמכüחמגאעוכÿ =============================== *}
{* ================================================================================ *}

{if $my_profile || $is_admin}
    <div class="float_bar">
        {if $my_profile}
            <a href="/users/{$user_id}/addphoto.html" class="usr_photo_add">{$LANG.ADD_PHOTO}</a>
        {/if}
        {if $album_type == 'private'}
            <a href="/users/{$user_id}/delalbum{$album.id}.html" onclick="if(!confirm('{$LANG.DELETE_ALBUM_CONFIRM}')){literal}{ return false; }{/literal}" class="usr_del_album">{$LANG.DELETE_ALBUM}</a>
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

        {if ($is_admin || $my_profile) && $album_type == 'private'}
        <form action="/users/{$user_id}/photos/editlist" method="post">
            <input type="hidden" name="album_id" value="{$album.id}" />
            <script type="text/javascript">
                {literal}
                function toggleButtons(){
                    var is_sel = $('.photo_id:checked').length;
                    if (is_sel > 0) {
                        $('#edit_btn').attr('disabled', '');
                        $('#delete_btn').attr('disabled', '');
                    } else {
                        $('#edit_btn').attr('disabled', 'disabled');
                        $('#delete_btn').attr('disabled', 'disabled');
                    }
                }
                {/literal}
            </script>
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
                        {if ($is_admin || $my_profile) && $album_type == 'private'}
                            <input type="checkbox" name="photos[]" class="photo_id" value="{$photo.id}" onclick="toggleButtons()" />
                        {/if}                        
                    </div>
				</td> 
				{if $col==$maxcols} </tr> {assign var="col" value="1"} {else} {math equation="x + 1" x=$col assign="col"} {/if}
			{/foreach}

            {if $col>1}
				<td colspan="{math equation="x - y + 1" x=$col y=$maxcols}">&nbsp;</td></tr>
			{/if}

		</table>

        {if ($is_admin || $my_profile) && $album_type == 'private'}
            <div class="usr_photo_sel_bar bar">
                {$LANG.SELECTED_ITEMS}:
                <input type="submit" name="edit" id="edit_btn" value="{$LANG.EDIT}" disabled="disabled" />
                <input type="submit" name="delete" id="delete_btn" value="{$LANG.DELETE}" disabled="disabled" />
            </div>
            </form>
        {/if}

		{$pagebar}
        
{else}
    <p>{$LANG.NOT_PHOTOS}</p>
{/if}