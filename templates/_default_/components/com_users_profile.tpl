{* ================================================================================ *}
{* ========================= œÓÙËÎ¸ ÔÓÎ¸ÁÓ‚‡ÚÂÎˇ ================================= *}
{* ================================================================================ *}

{add_js file='includes/jquery/tabs/jquery.ui.min.js'}
{add_js file="components/users/js/profile.js"}
{add_css file='includes/jquery/tabs/tabs.css'}					

{if $messages}
    <div class="sess_messages">
        {foreach key=id item=message from=$messages}
            {$message}
        {/foreach}
    </div>
{/if}

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
    {if $usr.banned}<div style="color:red;padding:10px;">{$LANG.USER_IN_BANLIST}</div>{/if}
</div>

<div class="usr_status_bar">
    <div class="usr_status_text" {if !$usr.status_text}style="display:none"{/if}>
        <span>{$usr.status_text}</span>
        <span class="usr_status_date" >// {$usr.status_date} {$LANG.BACK}</span>
    </div>
    {if $myprofile}
        <div class="usr_status_link">[ <a href="javascript:setStatus()">{$LANG.CHANGE_STATUS}</a> ]</div>
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
								<div class="title">{$LANG.KARMA}</div>
								{if $usr.karma_int >= 0}
									<div class="value-positive">{$usr.karma}</div>
								{else}
									<div class="value-negative">{$usr.karma}</div>
								{/if}
							</div>
							<div class="rating">
								<div class="title">{$LANG.RATING}</div>
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
                            <div>{$LANG.LINK_TO_THIS_PAGE}:</div>
                            <a href="{$usr.profile_link}" title="{$usr.nickname}">{$usr.profile_link}</a>
                        </div>

					</td>
				</tr>
			</table>
	    </td>
    	<td valign="top" style="padding-left:10px">	
			<div id="profiletabs">
				<ul id="tabs"> 
					<li><a href="#upr_profile"><span>{$LANG.PROFILE}</span></a></li>
					{if $myprofile && $cfg.sw_feed}
						<li><a href="#upr_feed"><span>{$LANG.FEED}</span></a></li>
					{/if}
                    {if $cfg.sw_content}
                        <li><a href="#upr_content"><span>{$LANG.CONTENT}</span></a></li>
                    {/if}
					{if $cfg.sw_friends}
						<li><a href="#upr_friends"><span>{$LANG.FRIENDS}</span></a></li>
					{/if}
					{if $cfg.sw_clubs}
						<li><a href="#upr_clubs"><span>{$LANG.CLUBS}</span></a></li>
					{/if}
                    {if $cfg.sw_awards}
                        <li><a href="#upr_awards"><span>{$LANG.AWARDS}</span></a></li>
                    {/if}
                    {foreach key=id item=plugin from=$plugins}
                        <li><a href="#upr_{$plugin.name}"><span>{$plugin.title}</span></a></li>
                    {/foreach}
				</ul> 
				
				{* ============================== «¿ À¿ƒ ¿ π1 ============================================== *}
				<div id="upr_profile">
					<div class="user_profile_data">
					
						<div class="field">
							<div class="title">{$LANG.STATUS}:</div>
							<div class="value">{$usr.status}</div>
						</div>
						
						<div class="field">
							<div class="title">{$LANG.LAST_VISIT}:</div>
							<div class="value">{$usr.flogdate}</div>
						</div>
						
						<div class="field">
							<div class="title">{$LANG.DATE_REGISTRATION}:</div>
							<div class="value">{$usr.fregdate}</div>
						</div>
						<div class="field">
							<div class="title">{$LANG.CITY}:</div>
                            <div class="value"><a href="/users/city/{$usr.cityurl}">{$usr.city}</a></div>
						</div>
						
						{if $usr.showbirth && $usr.birthdate}
						<div class="field">
							<div class="title">{$LANG.BIRTH}:</div>
							<div class="value">{$usr.birthdate}</div>
						</div>
						{/if}
						
						{if $usr.gender}
						<div class="field">
							<div class="title">{$LANG.SEX}:</div>
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
							<div class="title">{$LANG.COMMENTS}:</div>
							<div class="value">{$usr.comments_count}
                                {if $usr.comments_count}<a href="/users/{$usr.id}/comments.html" title="{$LANG.READ}">&rarr;</a>{/if}
                            </div>
						</div>
                        {/if}

                        {if $cfg.sw_forum}
						<div class="field">
							<div class="title">{$LANG.MESS_IN_FORUM}:</div>
							<div class="value">{$usr.forum_count}
                                {if $usr.forum_count}<a href="/users/{$usr.id}/forumposts.html" title="{$LANG.READ}">&rarr;</a>{/if}
                            </div>
						</div>
                        {/if}
						
						<div class="field">
							<div class="title">{$LANG.HOBBY} ({$LANG.TAGSS}):</div>
							<div class="value">{$usr.description}</div>
						</div>					
					</div>
					
					<div>
						{if $cfg.privforms}
							{$usr.privforms}
						{/if}												
						
						{if $cfg.sw_wall}
							<div class="usr_wall">
								<div class="usr_wall_header">{$LANG.USER_WALL}</div>
								<div class="usr_wall_body">
                                    <div class="wall_body">{$usr.wall_html}</div>
                                </div>
                                <div class="usr_wall_addlink">
                                    <a href="#addwall" id="addlink" onclick="{literal}$('div#addwall').slideToggle();$('.usr_wall_addlink').toggle();$('.wall_message').focus();{/literal}">
                                        {$LANG.WRITE_ON_WALL}
                                    </a>
                                </div>
								<div id="addwall" style="display:none">{$usr.addwall_html}</div>
							</div>
						{/if}
					</div>
				</div>
				
				{* ============================== «¿ À¿ƒ ¿ π2 ============================================== *}
				{if $myprofile && $cfg.sw_feed}
					<div id="upr_feed">
                        {if $usr.friends}
                            <div class="usr_friends_feed">
                                {if $usr.friends_comments}
                                    <div class="content_title">{$LANG.LAST_FRIEND_COMM}:</div>

                                    <table cellpadding="3" cellspacing="0" border="0" width="100%" class="feed">
                                        {foreach key=tid item=comment from=$usr.friends_comments}
                                            <tr>
                                                <td class="date">
                                                    {$comment.pubdate}
                                                </td>
                                                <td>
                                                    <a href="{profile_url login=$comment.login}" class="nickname">{$comment.nickname}</a> &rarr;
                                                    <a href="{$comment.target_link}">{$comment.content}</a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                {/if}
                                {if $usr.friends_posts}
                                    <div class="content_title">{$LANG.LAST_POSTS_IN_FRIEND_BLOG}:</div>

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
                                    <div class="content_title">{$LANG.LAST_FRIEND_PHOTOS}:</div>

                                    <table cellpadding="3" cellspacing="0" border="0" width="100%" class="feed">
                                        {foreach key=tid item=photo from=$usr.friends_photos}
                                            <tr>
                                                <td class="date">
                                                    {$photo.pubdate}
                                                </td>
                                                <td>
                                                    <a href="{profile_url login=$photo.login}" class="nickname">{$photo.nickname}</a> &rarr;
                                                    <a href="/photos/photo{$photo.id}.html">{$photo.title}</a>
                                                </td>                                                
                                            </tr>
                                        {/foreach}
                                    </table>
                                {/if}                                
                            </div>
                       {else}
                            <p>{$LANG.FEED_DESC}</p>
                            <p>{$LANG.FEED_EMPTY_TEXT}</p>
                       {/if}
					</div>	
				{/if}		
	
				{* ============================== «¿ À¿ƒ ¿ π3 ============================================== *}
                {if $cfg.sw_content}
                    <div id="upr_content">
                        {if $myprofile}
                            <div class="content_title">{$LANG.YOUR_CONTENT}:</div>
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
                                    <a href="/users/{$usr.id}/comments.html">{$LANG.USR_COMMENTS}</a> ({$usr.comments_count})
                                </div>
                            {/if}
                            {if $cfg.sw_photo}
                                <div id="usr_photoalbum">
                                    <a href="/users/{$usr.id}/photoalbum.html">{$LANG.PHOTOALBUM}</a> ({$usr.photos_count})
                                </div>
                            {/if}
                            {if $cfg.sw_files}
                                <div id="usr_files">
                                    <a href="/users/{$usr.id}/files.html">{$LANG.FILES}</a> ({$usr.files_count})
                                </div>
                            {/if}
                            {if $cfg.sw_board}
                                <div id="usr_board">
                                    <a href="/users/{$usr.id}/board.html">{$LANG.ADVS}</a> ({$usr.board_count})
                                </div>
                            {/if}
                        </div>
                    </div>
				{/if}

				{* ============================== «¿ À¿ƒ ¿ π4 ============================================== *}
				{if $cfg.sw_friends}
                    <div id="upr_friends">
                        {if $usr.friends}
                            {$usr.friends}
                        {else}
                            {if !$myprofile}
                                <p>{$LANG.NOT_FRIENDS_IN_USER}</p>
                            {else}
                                <p>{$LANG.NOT_FRIENDS_IN_YOUR}</p>
                            {/if}
                        {/if}
                    </div>
				{/if}
				
				{* ============================== «¿ À¿ƒ ¿ π5 ============================================== *}
				{if $cfg.sw_clubs}
					<div id="upr_clubs">
						{if $usr.clubs}
							{if sizeof($usr.clubs.member)}
								<div class="usr_clubs">
									<span class="label">{$LANG.CONSIST}:</span>
									{foreach key=tid item=club from=$usr.clubs.member}
										<a class="usr_club_link" href="/clubs/{$club.id}">{$club.title}</a>
									{/foreach}
								</div>
							{/if}
							{if sizeof($usr.clubs.moder)}
								<div class="usr_clubs">
									<span class="label">{$LANG.MODERATE}:</span>
									{foreach key=tid item=club from=$usr.clubs.moder}
										<a class="usr_club_link" href="/clubs/{$club.id}">{$club.title}</a>
									{/foreach}
								</div>
							{/if}
							{if sizeof($usr.clubs.admin)}
								<div class="usr_clubs">
									<span class="label">{$LANG.ADMINING}:</span>
									{foreach key=tid item=club from=$usr.clubs.admin}
										<a class="usr_club_link" href="/clubs/{$club.id}">{$club.title}</a>
									{/foreach}
								</div>
							{/if}													
						{else}
                            {if !$myprofile}
                                <p><strong>{$usr.nickname}</strong> {$LANG.USET_NOT_IN_CLUBS}</p>
                            {else}
                                <p>{$LANG.YOU_NOT_IN_CLUBS}</p>
                            {/if}
						{/if}
					</div>
				{/if}
				
				{* ============================== «¿ À¿ƒ ¿ π6 ============================================== *}

                {if $cfg.sw_awards}
					<div id="upr_awards">
						<div class="awards_list_link">
							<a href="/users/awardslist.html">{$LANG.HOW_GET_AWARD}</a>
						</div>
						{if sizeof($usr.awards_html)}
							{$usr.awards_html}
						{/if}
					</div>
                {/if}

                {foreach key=id item=plugin from=$plugins}
                    <div id="upr_{$plugin.name}">{$plugin.html}</div>
                {/foreach}

			</div>						
	</td>
  </tr>
</table>
