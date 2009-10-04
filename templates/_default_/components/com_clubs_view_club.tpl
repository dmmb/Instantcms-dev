{* ================================================================================ *}
{* ========================= Просмотр клуба ======================================= *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

{if $club}
	
		<table class="club_full_entry" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" class="left">
					<div class="image"><img src="/images/clubs/{$club.imageurl}" border="0"/></div>
					<div class="members_list">
						<div class="title">Администратор клуба:</div>
						<div class="list">{$club.admin}</div>
					</div>
					{if $club.members_list}
						<div class="members_list">
							<div class="title">Участники клуба ({math equation="x - 1" x=$club.members}):</div>
							<div class="list">{$club.members_list}</div>
						</div>
					{/if}		
				</td>
				<td valign="top">
					<div class="data">
						<div class="details">
							<span class="rating"><strong>Рейтинг:</strong> {$club.rating}</span>
							<span class="members"><strong>{$club.members|spellcount:'участник':'участника':'участников'}</strong></span>
							<span class="date">{$club.pubdate}</span>
						</div>					
						<div class="description">
							{$club.description}
						</div>
						{if $is_member || $is_admin || $is_moder || $club.member_link}
							<div class="clubmenu">
								<div>{$club.member_link}</div>
								{if $is_admin}
									<div><a class="config" href="/clubs/{$menuid}/{$club.id}/config.html">Настройки клуба</a></div>
								{/if}
							</div>			
						{/if}																								
					</div>
					<div class="clubcontent">
						{if $club.enabled_blogs}
						<div class="blog">
							<div class="title"><a href="/blogs/{$menuid}/{$club.blog_id}/blog.html">Блог клуба</a></div>
							<div class="content">{$club.blog_content}</div>
						</div>
						{/if}
						{if $club.enabled_photos}
						<div class="album">
							<div class="title"><a href="/photos/{$menuid}/{$club.root_album_id}">Фотоальбомы</a></div>
							<div class="content">
								{$club.photo_albums}
								{if $is_admin || $is_moder || $is_karma_enabled}
									<p>
										<span id="add_album_link"><a class="service" href="javascript:void(0)" onclick="{literal}$('#add_album_link').toggle();$('#add_album_form').toggle();$('#add_album_form input.text').focus();{/literal}">Добавить фотоальбом</a></span>
										<span id="add_album_form" style="display:none">
											<input type="text" class="text" name="album_title" id="album_title"/> 
											<input type="button" value="Создать" onclick="javascript:createAlbum({$club.id}, {$menuid});"/>
										</span>
										<span id="add_album_wait" style="display:none">Загрузка...</span>
									</p>
								{/if}
							</div>
						</div>
						{/if}
					</div>				
					<div class="wall">
                        {add_js file="components/users/js/wall.js"}
						<div class="header">Стена клуба</div>
						<div class="body">
                            <div class="wall_body">{$club.wall_html}</div>
                        </div>
                        <div class="usr_wall_addlink">
                            <a href="#addwall" id="addlink" onclick="{literal}$('div#addwall').slideToggle();$('.usr_wall_addlink').toggle();$('.wall_message').focus();{/literal}">
                                Написать на стене
                            </a>
                        </div>
						<div id="addwall" style="display:none">{$club.addwall_html}</div>
					</div>					
				</td>
			</tr>
		</table>

{else}
	<p>Клуб не найден. Возможно он не активен или удален.</p>
{/if}