{* ================================================================================ *}
{* ========================= Просмотр фотографии ================================== *}
{* ================================================================================ *}
{strip}
<h1 class="con_heading">{$photo.title}</h1>

{if $photo.description}
    <div class="photo_desc">
        {$photo.description}
    </div>
{/if}

<table cellpadding="0" cellspacing="0" border="0" class="photo_layout">
    <tr>
        <td valign="top" style="padding-right:15px">
            <img src="/images/photos/medium/{$photo.file}" border="0" alt="{$photo.title|escape:'html'}" />

            {if $photo.album_nav}
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
			{/if}
        </td>
        {if $photo.a_type != 'simple'}
            <td width="7" class="photo_larr">&nbsp;
                
            </td>
            <td width="250" valign="top">
                <div class="photo_details">

                    <table cellpadding="0" cellspacing="0" border="0" width="100%">
                        <tr>
                            <td>
                                <p><strong>{$LANG.RATING}: </strong><span id="karmapoints">{$photo.karma}</span></p>
                                <p><strong>{$LANG.HITS}: </strong> {$photo.hits}</p>
                            </td>
                            {if $photo.karma_buttons}
                                <td width="25">{$photo.karma_buttons}</td>
                            {/if}
                        </tr>
                    </table>

                    <div class="photo_date_details">
                        <p>{$photo.pubdate}</p>
                        <p>{$photo.genderlink}</p>
                    </div>

                    {if $cfg.link}
                        <p>{$photo.file_orig}</p>
                    {/if}

                    {if $is_can_operation}
                    <p class="operations">
                        {if $is_author || $is_admin}
                            <a href="/photos/editphoto{$photo.id}.html" title="{$LANG.EDIT}"><img src="/templates/_default_/images/icons/edit.png" border="0"/></a>&nbsp;
                        {if $is_admin}
                            <a href="/photos/movephoto{$photo.id}.html" title="{$LANG.MOVE}"><img src="/templates/_default_/images/icons/move.png" border="0"/></a>&nbsp;
                        {/if}
                            <a href="/photos/delphoto{$photo.id}.html" title="{$LANG.DELETE}"><img src="/templates/_default_/images/icons/delete.png" border="0"/></a>&nbsp;
                        {/if}
                    </p>
                    {/if}

                </div>

                {if $photo.album_nav}
                    <div class="photo_sub_details">
                        {$LANG.BACK_TO} <a href="/photos/{$photo.cat_id}">{$LANG.TO_ALBUM}</a><br/>
                        {if $photo.NSDiffer==''}{$LANG.BACK_TO}  <a href="/photos">{$LANG.TO_LIST_ALBUMS}</a>{/if}
                    </div>
                {/if}

                {if $photo.a_bbcode}
                <div class="photo_details" style="margin-top:5px;font-size: 12px">
                    {$LANG.CODE_INPUT_TO_FORUMS}:<br/>
                    <input type="text" class="photo_bbinput" value="{$bbcode}"/>
                </div>
                {/if}

                <div class="photo_sub_details" style="padding:0px 20px">
                    {$tagbar}
                </div>

            </td>
        {/if}
    </tr>
</table>



{/strip}
