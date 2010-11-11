{* ================================================================================ *}
{* ========================= Просмотр клуба ======================================= *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>
{if $messages}
    <div class="sess_messages">
        {foreach key=id item=message from=$messages}
            {$message}
        {/foreach}
    </div>
{/if}
{if $club}

    {if $is_access}

		<table class="club_full_entry" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" class="left">
					<div class="image"><img src="/images/clubs/{$club.imageurl}" border="0"/></div>
					<div class="members_list">
						<div class="title">{$LANG.CLUB_ADMIN}:</div>
						<div class="list">{$club.admin}</div>
					</div>
					{if $club.members_list}
						<div class="members_list">
							<div class="title">{$LANG.CLUB_MEMBERS} ({math equation="x - 1" x=$club.members}):</div>
							<div class="list">{$club.members_list}</div>
                            {if $is_admin}
                            <div class="title"><a href="/clubs/{$club.id}/message-members.html" title="{$LANG.SEND_MESSAGE_TO_MEMBERS}">{$LANG.SEND_MESSAGE}</a></div>
                            {/if}
						</div>
					{/if}		
				</td>
				<td valign="top">
					<div class="data">
						<div class="details">
							<span class="rating"><strong>{$LANG.RATING}:</strong> {$club.rating}</span>
							<span class="members"><strong>{$club.members|spellcount:$LANG.USER:$LANG.USER2:$LANG.USER10}</strong></span>
							<span class="date">{$club.pubdate}</span>
						</div>					
						<div class="description">
							{$club.description}
						</div>
						{if $is_member || $is_admin || $is_moder || $club.member_link}
							<div class="clubmenu">
                                {if $uid}
								<div>{$club.member_link}</div>
                                {/if}
								{if $is_admin}
									<div><a class="config" href="/clubs/{$club.id}/config.html">{$LANG.CONFIG_CLUB}</a></div>
								{/if}
							</div>			
						{/if}																								
					</div>
					<div class="clubcontent">
						{if $club.enabled_blogs}
						<div class="blog">
							<div class="title"><a href="{$club.blog_url}">{$LANG.CLUB_BLOG}</a></div>
							<div class="content">{$club.blog_content}</div>
						</div>
						{/if}
						{if $club.enabled_photos}
						<div class="album">
							<div class="title"><a href="/photos/{$club.root_album_id}">{$LANG.PHOTOALBUMS}</a></div>
							<div class="content">
								{$club.photo_albums}
								{if $is_admin || $is_moder || $is_karma_enabled}
									<p>
                                    	{if $club.all_albums >= 6}
                                    	<span><a href="/photos/{$club.root_album_id}">{$LANG.ALL_ALBUMS} (<strong id="count_photo">{$club.all_albums}</strong>)</a></span>
                                        {/if}
										<span id="add_album_link"><a class="service" href="javascript:void(0)" onclick="{literal}$('#add_album_link').toggle();$('#add_album_form').toggle();$('#add_album_form input.text').focus();{/literal}">{$LANG.ADD_PHOTOALBUM}</a></span>
										<span id="add_album_form" style="display:none">
											<input type="text" class="text" name="album_title" id="album_title"/> 
											<input type="button" value="{$LANG.CREATE}" onclick="javascript:createAlbum({$club.id}, {$club.root_album_id});"/>
                                            <input type="button" value="{$LANG.CANCEL}" onclick="{literal}$('#add_album_link').toggle();$('#add_album_form').toggle();{/literal}"/>
										</span>
										<span id="add_album_wait" style="display:none">{$LANG.LOADING}...</span>
									</p>
								{/if}
							</div>
						</div>
						{/if}
					</div>
					<div class="wall">
						<div class="header">{$LANG.CLUB_WALL}</div>
						<div class="body">
                            <div class="wall_body">{$club.wall_html}</div>
                        </div>
                        <div class="usr_wall_addlink">
                            <a href="#addwall" id="addlink" onclick="{literal}$('div#addwall').slideToggle();$('.usr_wall_addlink').toggle();$('.wall_message').focus();{/literal}">
                                {$LANG.WRITE_ON_WALL}
                            </a>
                        </div>
						<div id="addwall" style="display:none">{$club.addwall_html}</div>
					</div>
				</td>
			</tr>
		</table>

        {else}
            <p>{$LANG.CLUB_PRIVATE}</p>
            <p>{$LANG.CLUB_ADMIN}: {$club.admin}</p>
        {/if}

{else}
	<p>{$LANG.CLUB_NOT_FOUND_TEXT}</p>
{/if}