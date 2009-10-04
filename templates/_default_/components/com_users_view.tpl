{* ================================================================================ *}
{* ========================= —писок пользователей ================================= *}
{* ================================================================================ *}

<h1 class="con_heading">ѕользователи</h1>

    {if $cfg.sw_search}
    <div style="margin-bottom:15px;">
        <div id="slink">
            <a href="javascript:void(0)" style="background:url(/components/catalog/images/icons/search.png) no-repeat;padding-left:18px;" onclick="{literal}$('#sbar').slideToggle();$('#slink').slideToggle();{/literal}">ѕоиск пользователей</a>
        </div>
        <div id="sbar" style="padding:6px;background:#ECECEC;display:none;">
            <form name="usr_search_form" method="post" action="/users/{$menuid}/search.html">
                <table cellpadding="2">
                    <tr>
                        <td>Ќайти: </td>
                        <td>
                            <select name="gender" id="gender" style="width:150px">
                                <option value="f">женщин</option>
                                <option value="m">мужчин</option>
                                <option value="0" selected>всех</option>
                            </select>,
                        </td>
                    </tr>
                    <tr>
                         <td>возраст от</td>
                         <td>
                            <input style="width:60px" name="agefrom" type="text" id="agefrom" value="18"/>
                            до
                            <input style="width:60px" name="ageto" type="text" id="ageto" value=""/> лет
                         </td>
                    </tr>
                    <tr>
                         <td>
                             им€
                         </td>
                         <td>
                            <input style="width:150px" id="name" name="name" type="text" value=""/>,
                            <script type="text/javascript">
                                {$autocomplete_js}
                            </script>
                         </td>
                    </tr>
                    <tr>
                         <td>
                             город
                         </td>                    
                         <td>
                            <input style="width:150px" id="city" name="city" type="text" value=""/>,
                            <script type="text/javascript">
                                {$autocomplete_js}
                            </script>
                         </td>
                    </tr>
                    <tr>
                         <td>интересы</td>
                         <td>
                            <input style="" id="hobby" name="hobby" type="text" value=""/>
                         </td>
                         <td>
                             <input name="gosearch" type="submit" id="gosearch" value="ѕоиск" />
                             <input name="hide" type="button" id="hide" value="—крыть" onclick="{literal}$('#sbar').slideToggle();$('#slink').slideToggle();{/literal}"/>
                         </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    {/if}

    {if $querymsg} {$querymsg} {/if}

	<table width="100%" cellspacing="0" cellpadding="5" class="users_layout">
		<tr>
			<td width="50%" valign="top">
			{* ========================= —ѕ»—ќ  ѕќЋ№«ќ¬ј“≈Ћ≈… ============================*}				
				<div class="users_list_buttons">
					<div class="button {if $link.selected=='positive'}selected_positive{/if}"><a rel=ФnofollowФ href="{$link.positive}">ѕозитивные</a></div>
					<div class="button {if $link.selected=='rating'}selected_rating{/if}"><a rel=ФnofollowФ href="{$link.rating}">–ейтинг</a></div>
					<div class="button {if $link.selected=='negative'}selected_negative{/if}"><a rel=ФnofollowФ href="{$link.negative}">Ќегативные</a></div>
				</div>
				<div class="users_list">
					<table width="100%" cellspacing="5" cellpadding="5" class="users_list">
						{if $is_users}
							{foreach key=tid item=usr from=$users}								
								<tr>
									<td width="20" align="left" style="padding:2px;">
										<div class="number">{$usr.num}.</div>
									</td>
									<td width="64" valign="top" align="center" style="padding:2px;">
										<div class="avatar">{$usr.avatar}</div>
									</td>
									<td valign="top">
										<div class="nickname">{$usr.nickname}</div>
										<div class="karma">{$usr.karma} <span class="rating">{$usr.rating}</span></div>
										<div class="status">{$usr.status}</div>
									</td>
							{/foreach}		
						{else}
							<tr>
								<td>
									<p>ѕользователи не найдены.</p>
								</td>
							</tr>
						{/if}
					</table>					
				</div>
				{if (isset($pagebar) && ($orderby!='karma'||$orderto!='asc'))} {$pagebar}	{/if}
			</td>
			<td width="40%" valign="top">
			{* ========================= —“ј“»—“» ј ѕќЋ№«ќ¬ј“≈Ћ≈… ============================*}				
				<div class="stat_block">
					<div class="title">—колько нас?</div>
					<div class="body">				
						<ul>
							<li>{$total_usr|spellcount:'пользователь':'пользовател€':'пользователей'}</li>
						</ul>
					</div>
				</div>
				
				<div class="stat_block">
					<div class="title"> то онлайн?</div>
					<div class="body">				
						<ul>
							<li>{$people.users|spellcount:'пользователь':'пользовател€':'пользователей'}</li>
							<li>{$people.guests|spellcount:'гость':'гост€':'гостей'}</li>
							<li>{$online_link}</li>
						</ul>
					</div>
				</div>				
				
				<div class="stat_block">
					<div class="title"> то мы?</div>
					<div class="body">
						<ul>
							<li><a href="javascript:void(0)" rel=ФnofollowФ onclick="searchGender('m', {$menuid})">{$gender_stats.male|spellcount:'мужчина':'мужчины':'мужчин'}</a></li>
							<li><a href="javascript:void(0)" rel=ФnofollowФ onclick="searchGender('f', {$menuid})">{$gender_stats.female|spellcount:'женщина':'женщины':'женщин'}</a></li>
							<li>Ќе определились &mdash; {$gender_stats.unknown}</li>
						</ul>
					</div>
				</div>
				
				<div class="stat_block">
					<div class="title">ќткуда мы?</div>
					<div class="body">
						<ul>
							{foreach key=tid item=city from=$city_stats}
								{if $city.href}
									<li><a href="{$city.href}" rel=ФnofollowФ>{$city.city}</a> &mdash; {$city.count}</li>
								{else}
									<li>{$city.city} &mdash; {$city.count}</li>
								{/if}
							{/foreach}
						</ul>
					</div>
				</div>
				
				{if $bday}
				<div class="stat_block_bday" style="margin-top:10px;">
					<div class="title">—егодн€ день рождени€:</div>
					<div class="body">
						{$bday}
					</div>
				</div>
				{/if}
			</td>
		</tr>
	</table>		