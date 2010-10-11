{* ================================================================================ *}
{* ========================= —ÔËÒÓÍ ÔÓÎ¸ÁÓ‚‡ÚÂÎÂÈ ================================= *}
{* ================================================================================ *}

<h1 class="con_heading">{$LANG.USERS}</h1>

    {if $cfg.sw_search}
    <div style="margin-bottom:15px;">
        <div id="slink">
            <a href="javascript:void(0)" style="background:url(/components/catalog/images/icons/search.png) no-repeat;padding-left:18px;" onclick="{literal}$('#sbar').slideToggle();$('#slink').slideToggle();{/literal}">{$LANG.USERS_SEARCH}</a>
        </div>
        <div id="sbar" style="padding:6px;background:#ECECEC;display:none;">
            <form name="usr_search_form" method="post" action="/users/search.html">
                <table cellpadding="2">
                    <tr>
                        <td>{$LANG.FIND}: </td>
                        <td>
                            <select name="gender" id="gender" style="width:150px">
                                <option value="f">{$LANG.FIND_FEMALE}</option>
                                <option value="m">{$LANG.FIND_MALE}</option>
                                <option value="0" selected>{$LANG.FIND_ALL}</option>
                            </select>,
                        </td>
                    </tr>
                    <tr>
                         <td>{$LANG.AGE_FROM}</td>
                         <td>
                            <input style="width:60px" name="agefrom" type="text" id="agefrom" value="18"/>
                            {$LANG.TO}
                            <input style="width:60px" name="ageto" type="text" id="ageto" value=""/> {$LANG.YEARS}
                         </td>
                    </tr>
                    <tr>
                         <td>
                             {$LANG.NAME}
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
                             {$LANG.CITY}
                         </td>                    
                         <td>
                            <input style="width:150px" id="city" name="city" type="text" value=""/>,
                            <script type="text/javascript">
                                {$autocomplete_js}
                            </script>
                         </td>
                    </tr>
                    <tr>
                         <td>{$LANG.HOBBY}</td>
                         <td>
                            <input style="" id="hobby" name="hobby" type="text" value=""/>
                         </td>
                         <td>
                             <input name="gosearch" type="submit" id="gosearch" value="{$LANG.SEARCH}" />
                             <input name="hide" type="button" id="hide" value="{$LANG.HIDE}" onclick="{literal}$('#sbar').slideToggle();$('#slink').slideToggle();{/literal}"/>
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
			{* ========================= —œ»—Œ  œŒÀ‹«Œ¬¿“≈À≈… ============================*}				
				<div class="users_list_buttons">
					<div class="button {if $link.selected=='positive'}selected_positive{/if}"><a rel=înofollowî href="{$link.positive}">{$LANG.POSITIVE}</a></div>
					<div class="button {if $link.selected=='rating'}selected_rating{/if}"><a rel=înofollowî href="{$link.rating}">{$LANG.RATING}</a></div>
					<div class="button {if $link.selected=='negative'}selected_negative{/if}"><a rel=înofollowî href="{$link.negative}">{$LANG.NEGATIVE}</a></div>
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
                                        {if $usr.microstatus}
                                            <div style="clear:both">&mdash; {$usr.microstatus}</div>
                                        {/if}
										<div class="status">{$usr.status}</div>
									</td>
							{/foreach}		
						{else}
							<tr>
								<td>
									<p>{$LANG.USERS_NOT_FOUND}.</p>
								</td>
							</tr>
						{/if}
					</table>					
				</div>
				{if (isset($pagebar) && ($orderby!='karma'||$orderto!='asc'))} {$pagebar}	{/if}
			</td>
			<td width="40%" valign="top">
			{* ========================= —“¿“»—“» ¿ œŒÀ‹«Œ¬¿“≈À≈… ============================*}				
				<div class="stat_block">
					<div class="title">{$LANG.HOW_MUCH_US}</div>
					<div class="body">				
						<ul>
							<li>{$total_usr|spellcount:$LANG.USER:$LANG.USER2:$LANG.USER10}</li>
						</ul>
					</div>
				</div>
				
				<div class="stat_block">
					<div class="title"> ÚÓ ÓÌÎ‡ÈÌ?</div>
					<div class="body">				
						<ul>
							<li>{$people.users|spellcount:$LANG.USER:$LANG.USER2:$LANG.USER10}</li>
							<li>{$people.guests|spellcount:$LANG.GUEST:$LANG.GUEST2:$LANG.GUEST10}</li>
							<li>{$online_link}</li>
						</ul>
					</div>
				</div>				
				
				<div class="stat_block">
					<div class="title"> ÚÓ Ï˚?</div>
					<div class="body">
						<ul>
							<li><a href="javascript:void(0)" rel=înofollowî onclick="searchGender('m')">{$gender_stats.male|spellcount:$LANG.MALE1:$LANG.MALE2:$LANG.MALE10}</a></li>
							<li><a href="javascript:void(0)" rel=înofollowî onclick="searchGender('f')">{$gender_stats.female|spellcount:$LANG.FEMALE1:$LANG.FEMALE2:$LANG.FEMALE10}</a></li>
							<li>{$LANG.UNKNOWN} &mdash; {$gender_stats.unknown}</li>
						</ul>
					</div>
				</div>
				
				<div class="stat_block">
					<div class="title">{$LANG.WHERE_WE_FROM}</div>
					<div class="body">
						<ul>
							{foreach key=tid item=city from=$city_stats}
								{if $city.href}
									<li><a href="{$city.href}" rel=înofollowî>{$city.city}</a> &mdash; {$city.count}</li>
								{else}
									<li>{$city.city} &mdash; {$city.count}</li>
								{/if}
							{/foreach}
						</ul>
					</div>
				</div>
				
				{if $bday}
				<div class="stat_block_bday" style="margin-top:10px;">
					<div class="title">{$LANG.TODAY_BIRTH}:</div>
					<div class="body">
						{$bday}
					</div>
				</div>
				{/if}
			</td>
		</tr>
	</table>		