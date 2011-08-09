{* ================================================================================ *}
{* ========================= Просмотр фотоальбомов клуба ========================== *}
{* ================================================================================ *}
{strip}
{if $albums}
    <div class="usr_albums_block" style="margin-top:30px">
        <ul class="usr_albums_list">
            {foreach key=key item=album from=$albums}
                <li id="{$album.id}">
                    <div class="usr_album_thumb">
                        <a href="/photos/{$album.id}" title="{$album.title|escape:'html'}">
                            <img src="/images/photos/small/{$album.file}" width="64" height="64" border="0" alt="{$album.title|escape:'html'}" />
                        </a>
                    </div>
                    <div class="usr_album">
                        <div class="link">
                            <a href="/photos/{$album.id}">{$album.title}</a>
                            {if $is_admin || $is_moder}
                            	&nbsp;<a class="delete" title="{$LANG.DEL_ALBUMS}" href="javascript:void(0)" onclick="javascript:deleteAlbum({$album.id}, '{$album.title|escape:'html'}', {$club_id})">X</a>
                            {/if}
                        </div>
                        <div class="count">{if $album.content_count} {$album.content_count|spellcount:$LANG.PHOTO:$LANG.PHOTO2:$LANG.PHOTO10} {else} {$LANG.NOT_PHOTO}{/if}</div>
                        {if $album.on_moderate && ($is_admin || $is_moder)}
                        	<div class="count"><span class="on_moder">({$LANG.PHOTO_ON_MOD} &mdash; {$album.on_moderate})</span></div>
                        {/if}
                        <div class="date">{$album.pubdate}</div>
                    </div>
                </li>
            {/foreach}
         </ul>
    </div>
        
{else}
    <div class="usr_albums_block" style="margin-top:30px">
        <ul class="usr_albums_list">
    		<li class="no_albums">{$LANG.NO_PHOTOALBUM}</li>
        </ul>
    </div>
{/if}

{/strip}