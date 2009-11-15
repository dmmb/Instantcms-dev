{* ================================================================================ *}
{* ========================= Профиль пользователя ================================= *}
{* ================================================================================ *}

{add_js file='includes/jquery/tabs/jquery.ui.min.js'}
{add_js file="components/users/js/profile.js"}
{add_css file='includes/jquery/tabs/tabs.css'}					

{literal}
	<script type="text/javascript">
		$(document).ready(function(){
			$("#profiletabs > ul#tabs").tabs();
		});
	</script>
{/literal}

<div id="usertitle">
    <div class="con_heading" id="nickname" style="float:left;">
        {$usr.nickname}
    </div>
    {if $cfg.showgroup}<div class="usr_group" style="float:right">{$usr.grp}</div>{/if}
    {if $usr.banned}<div style="color:red;padding:10px;">Пользователь находится в бан-листе</div>{/if}
</div>

<div class="usr_status_bar">
    <div class="usr_status_text" {if !$usr.status_text}style="display:none"{/if}>
        <span>{$usr.status_text}</span>
        <span class="usr_status_date" >// {$usr.status_date} назад</span>
    </div>
    {if $myprofile}
        <div class="usr_status_link">[ <a href="javascript:setStatus()">изменить статус</a> ]</div>
    {/if}
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:14px">
	<tr>
		<td width="200" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center" valign="middle" style="padding:10px; border:solid 1px gray; background-color:#FFFFFF">
						{$usr.avatar}
						{if $is_auth}
							<div id="usermenu" style="text-align:center;">{$usr.menu}</div>
						{/if}
					</td>
				</tr>
				<tr>
					<td>
						<div id="user_ratings">
							<div class="karma">
								<div class="title">Карма</div>
								{if $usr.karma_int >= 0}
									<div class="value-positive">{$usr.karma}</div>
								{else}
									<div class="value-negative">{$usr.karma}</div>
								{/if}
							</div>
							<div class="rating">
								<div class="title">Рейтинг</div>
								<div class="value">{$usr.user_rating}</div>
							</div>
						</div>
					</td>
				</tr>				
			</table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						{if $usr.is_new_friends}
							<div class="usr_friends_query">{$usr.new_friends}</div>
				  		{/if}

                        <div id="user_profile_url">
                            <div>Ссылка на эту страницу:</div>
                            <a href="{$usr.profile_link}" title="{$usr.nickname}">{$usr.profile_link}</a>
                        </div>

					</td>
				</tr>
			</table>
	    </td>
    	<td valign="top" style="padding-left:10px">	
			<div id="profiletabs">
				<ul id="tabs"> 
					<li><a href="#profile"><span>Профиль</span></a></li> 
					{if $myprofile && $cfg.sw_feed}
						<li><a href="#feed"><span>Лента</span></a></li>
					{/if}
                    {if $cfg.sw_content}
                        <li><a href="#content"><span>Контент</span></a></li>
                    {/if}
					{if $cfg.sw_friends}
						<li><a href="#friends"><span>Друзья</span></a></li> 
					{/if}
					{if $cfg.sw_clubs}
						<li><a href="#clubs"><span>Клубы</span></a></li>
					{/if}
                    {if $cfg.sw_awards}
                        <li><a href="#awards"><span>Награды</span></a></li>
                    {/if}
                    {foreach key=id item=plugin from=$plugins}
                        <li><a href="#{$plugin.name}"><span>{$plugin.title}</span></a></li>
                    {/foreach}
				</ul> 
				
				{* ============================== ЗАКЛАДКА №1 ============================================== *}
				<div id="profile">	
					<div class="user_profile_data">
					
						<div class="field">
							<div class="title">Статус:</div>
							<div class="value">{$usr.status}</div>
						</div>
						
						<div class="field">
							<div class="title">Последний визит:</div>
							<div class="value">{$usr.flogdate}</div>
						</div>
						
						<div class="field">
							<div class="title">Дата регистрации:</div>
							<div class="value">{$usr.fregdate}</div>
						</div>
						
						{if $usr.showbirth && $usr.birthdate}
						<div class="field">
							<div class="title">Дата рождения:</div>
							<div class="value">{$usr.birthdate}</div>
						</div>
						{/if}
						
						{if $usr.gender}
						<div class="field">
							<div class="title">Пол:</div>
							<div class="value">{$usr.gender}</div>
						</div>
						{/if}
						
						{if $usr.showicq && $usr.icq}
						<div class="field">
							<div class="title">ICQ:</div>
							<div class="value">{$usr.icq}</div>
						</div>
						{/if}				
						
						{if $usr.showmail}
							{add_js file='includes/jquery/jquery.nospam.js'}
							<div class="field">
								<div class="title">E-mail:</div>
								<div class="value"><a href="#" rel="{$usr.email|NoSpam}" class="email">{$usr.email}</a></div>
							</div>
							{literal}
								<script>						
										$('.email').nospam({ replaceText: true });
								</script>
							{/literal}			
						{/if}				

                        {if $cfg.sw_comm}
						<div class="field">
							<div class="title">Комментариев:</div>
							<div class="value">{$usr.comments_count}
                                {if $usr.comments_count}<a href="/users/{$menuid}/{$usr.id}/comments.html" title="Читать">&rarr;</a>{/if}
                            </div>
						</div>
                        {/if}

                        {if $cfg.sw_forum}
						<div class="field">
							<div class="title">Сообщений на форуме:</div>
							<div class="value">{$usr.forum_count}
                                {if $usr.forum_count}<a href="/users/{$menuid}/{$usr.id}/forumposts.html" title="Читать">&rarr;</a>{/if}
                            </div>
						</div>
                        {/if}
						
						<div class="field">
							<div class="title">Интересы (метки):</div>
							<div class="value">{$usr.description}</div>
						</div>					
					</div>
					
					<div>
						{if $cfg.privforms}
							{$usr.privforms}
						{/if}												
						
						{if $cfg.sw_wall}
							<div class="usr_wall">
								<div class="usr_wall_header">Стена пользователя</div>
								<div class="usr_wall_body">
                                    <div class="wall_body">{$usr.wall_html}</div>
                                </div>
                                <div class="usr_wall_addlink">
                                    <a href="#addwall" id="addlink" onclick="{literal}$('div#addwall').slideToggle();$('.usr_wall_addlink').toggle();$('.wall_message').focus();{/literal}">
                                        Написать на стене
                                    </a>
                                </div>
								<div id="addwall" style="display:none">{$usr.addwall_html}</div>
							</div>
						{/if}
					</div>
				</div>
				
				{* ============================== ЗАКЛАДКА №2 ============================================== *}
				{if $myprofile && $cfg.sw_feed}
					<div id="feed">
                        {if $usr.friends}
                            <div class="usr_friends_feed">
                                {if $usr.friends_comments}
                                    <div class="content_title">Последние комментарии друзей:</div>

                                    <table cellpadding="3" cellspacing="0" border="0" width="100%" class="feed">
                                        {foreach key=tid item=comment from=$usr.friends_comments}
                                            <tr>
                                                <td class="date">
                                                    {$comment.pubdate}
                                                </td>
                                                <td>
                                                    <a href="{profile_url login=$post.login}" class="nickname">{$comment.nickname}</a> &rarr;
                                                    <a href="{$comment.link}">{$comment.content}</a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                {/if}
                                {if $usr.friends_posts}
                                    <div class="content_title">Последние посты в блогах друзей:</div>

                                    <table cellpadding="3" cellspacing="0" border="0" width="100%" class="feed">
                                        {foreach key=tid item=post from=$usr.friends_posts}
                                            <tr>
                                                <td class="date">
                                                    {$post.pubdate}
                                                </td>
                                                <td>
                                                    <a href="{profile_url login=$post.login}" class="nickname">{$post.nickname}</a> &rarr;
                                                    <a href="{$post.url}">{$post.title}</a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                {/if}
                                {if $usr.friends_photos}
                                    <div class="content_title">Последние фотографии друзей:</div>

                                    <table cellpadding="3" cellspacing="0" border="0" width="100%" class="feed">
                                        {foreach key=tid item=photo from=$usr.friends_photos}
                                            <tr>
                                                <td class="date">
                                                    {$photo.pubdate}
                                                </td>
                                                <td>
                                                    <a href="{profile_url login=$post.login}" class="nickname">{$photo.nickname}</a> &rarr;
                                                    <a href="/photos/0/photo{$photo.id}.html">{$photo.title}</a>
                                                </td>                                                
                                            </tr>
                                        {/foreach}
                                    </table>
                                {/if}                                
                            </div>
                       {else}
                            <p>Лента служит для быстрого просмотра новых фотографий и постов в блогах ваших друзей.</p>
                            <p>У вас пока нет друзей на сайте, поэтому лента пуста.</p>
                       {/if}
					</div>	
				{/if}		
	
				{* ============================== ЗАКЛАДКА №3 ============================================== *}
                {if $cfg.sw_content}
                    <div id="content">
                        {if $myprofile}
                            <div class="content_title">Ваш контент:</div>
                        {/if}
                        <div id="usr_links">
                            {if $cfg.sw_blogs}
                                {if $usr.blog_link}
                                    <div id="usr_blog">
                                        {$usr.blog_link}
                                    </div>
                                {/if}
                            {/if}
                            {if $cfg.sw_comm}
                                <div id="usr_comments">
                                    <a href="/users/{$menuid}/{$usr.id}/comments.html">Комментарии</a> ({$usr.comments_count})
                                </div>
                            {/if}
                            {if $cfg.sw_photo}
                                <div id="usr_photoalbum">
                                    <a href="/users/{$menuid}/{$usr.id}/photoalbum.html">Фотоальбом</a> ({$usr.photos_count})
                                </div>
                            {/if}
                            {if $cfg.sw_files}
                                <div id="usr_files">
                                    <a href="/users/{$menuid}/{$usr.id}/files.html">Файлы</a> ({$usr.files_count})
                                </div>
                            {/if}
                            {if $cfg.sw_board}
                                <div id="usr_board">
                                    <a href="/users/{$menuid}/{$usr.id}/board.html">Объявления</a> ({$usr.board_count})
                                </div>
                            {/if}
                        </div>
                    </div>
				{/if}

				{* ============================== ЗАКЛАДКА №4 ============================================== *}
				{if $cfg.sw_friends}
                    <div id="friends">
                        {if $usr.friends}
                            {$usr.friends}
                        {else}
                            {if !$myprofile}
                                <p>У пользователя пока нет друзей на сайте.</p>
                            {else}
                                <p>У вас пока нет друзей на сайте.</p>
                            {/if}
                        {/if}
                    </div>
				{/if}
				
				{* ============================== ЗАКЛАДКА №5 ============================================== *}
				{if $cfg.sw_clubs}
					<div id="clubs">
						{if $usr.clubs}
							{if sizeof($usr.clubs.member)}
								<div class="usr_clubs">
									<span class="label">Состоит в:</span>
									{foreach key=tid item=club from=$usr.clubs.member}
										<a class="usr_club_link" href="/clubs/0/{$club.id}">{$club.title}</a>
									{/foreach}
								</div>
							{/if}
							{if sizeof($usr.clubs.moder)}
								<div class="usr_clubs">
									<span class="label">Модерирует:</span>
									{foreach key=tid item=club from=$usr.clubs.moder}
										<a class="usr_club_link" href="/clubs/0/{$club.id}">{$club.title}</a>
									{/foreach}
								</div>
							{/if}
							{if sizeof($usr.clubs.admin)}
								<div class="usr_clubs">
									<span class="label">Администрирует:</span>
									{foreach key=tid item=club from=$usr.clubs.admin}
										<a class="usr_club_link" href="/clubs/0/{$club.id}">{$club.title}</a>
									{/foreach}
								</div>
							{/if}													
						{else}
                            {if !$myprofile}
                                <p><strong>{$usr.nickname}</strong> не состоит в клубах.</p>
                            {else}
                                <p>Вы не состоите в клубах на сайте.</p>
                            {/if}
						{/if}
					</div>
				{/if}
				
				{* ============================== ЗАКЛАДКА №6 ============================================== *}

                {if $cfg.sw_awards}
					<div id="awards">						
						<div class="awards_list_link">
							<a href="/users/awardslist.html">Как получить награду?</a>
						</div>
						{if sizeof($usr.awards_html)}
							{$usr.awards_html}
						{/if}
					</div>
                {/if}

                {foreach key=id item=plugin from=$plugins}
                    <div id="{$plugin.name}">{$plugin.html}</div>
                {/foreach}
								
			</div>						
	</td>
  </tr>
</table>
