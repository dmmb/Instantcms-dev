{* ================================================================================ *}
{* ========================= Список пользователей ================================= *}
{* ================================================================================ *}

<div id="users_search_link" class="float_bar">
    <a href="javascript:void(0)" onclick="{literal}$('#users_sbar').slideToggle();{/literal}">
        <span>{$LANG.USERS_SEARCH}</span>
    </a>
</div>

<h1 class="con_heading">{$LANG.USERS}</h1>

    {if $cfg.sw_search}    
    <div id="users_sbar" style="display:none;">
        <form name="usr_search_form" method="post" action="/users/search.html">
            <table cellpadding="2">
                <tr>
                    <td width="80">{$LANG.FIND}: </td>
                    <td width="170">
                        <select name="gender" id="gender" class="field" style="width:150px">
                            <option value="f">{$LANG.FIND_FEMALE}</option>
                            <option value="m">{$LANG.FIND_MALE}</option>
                            <option value="0" selected>{$LANG.FIND_ALL}</option>
                        </select>
                    </td>
                     <td width="80">{$LANG.AGE_FROM}</td>
                     <td>
                        <input style="width:60px" name="agefrom" type="text" id="agefrom" value="18"/>
                        {$LANG.TO}
                        <input style="width:60px" name="ageto" type="text" id="ageto" value=""/>
                     </td>
                </tr>
                <tr>
                </tr>
                <tr>
                     <td>
                         {$LANG.NAME}
                     </td>
                     <td>
                        <input style="width:150px" id="name" name="name" class="field" type="text" value=""/>
                        <script type="text/javascript">
                            {$autocomplete_js}
                        </script>
                     </td>
                      <td>
                         {$LANG.CITY}
                     </td>
                     <td>
                        <input style="width:150px" id="city" name="city" class="field" type="text" value=""/>
                        <script type="text/javascript">
                            {$autocomplete_js}
                        </script>
                     </td>
                </tr>
                <tr>
                </tr>
                <tr>
                     <td>{$LANG.HOBBY}</td>
                     <td colspan="3">
                        <input style="" id="hobby" class="longfield" name="hobby" type="text" value=""/>
                     </td>
                </tr>
            </table>
            <p>
                <input name="gosearch" type="submit" id="gosearch" value="{$LANG.SEARCH}" />
                <input name="hide" type="button" id="hide" value="{$LANG.HIDE}" onclick="{literal}$('#users_sbar').slideToggle();{/literal}"/>
            </p>
        </form>
    </div>
    {/if}

    {if $querymsg}
        <div class="users_search_results">{$querymsg}</div>
    {/if}

	<table width="100%" cellspacing="0" cellpadding="0" class="users_layout">
		<tr>
			<td width="" valign="top">
			{* ========================= СПИСОК ПОЛЬЗОВАТЕЛЕЙ ============================*}				
				<div class="users_list_buttons">
					<div class="button {if $link.selected=='latest'}selected{/if}"><a rel=”nofollow” href="{$link.latest}">{$LANG.LATEST}</a></div>
                    <div class="button {if $link.selected=='positive'}selected{/if}"><a rel=”nofollow” href="{$link.positive}">{$LANG.POSITIVE}</a></div>
					<div class="button {if $link.selected=='rating'}selected{/if}"><a rel=”nofollow” href="{$link.rating}">{$LANG.RATING}</a></div>					
				</div>
				<div class="users_list">
					<table width="100%" cellspacing="0" cellpadding="0" class="users_list">
						{if $is_users}
							{foreach key=tid item=usr from=$users}								
								<tr>
									<td width="80" valign="top">
										<div class="avatar">{$usr.avatar}</div>
									</td>
									<td valign="top">
                                        {if $link.selected=='rating'}
                                            <div class="rating" title="{$LANG.RATING}">{$usr.rating}</div>
                                        {/if}
                                        {if $link.selected=='positive'}
                                            <div title="{$LANG.KARMA}" class="karma{if $usr.karma > 0} pos{/if}{if $usr.karma < 0} neg{/if}">{if $usr.karma > 0}+{/if}{$usr.karma}</div>
                                        {/if}
                                        <div class="status">{$usr.status}</div>
										<div class="nickname">{$usr.nickname}</div>
                                        {if $usr.microstatus}
                                            <div class="microstatus">{$usr.microstatus}</div>
                                        {/if}
									</td>
                                </tr>
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
		</tr>
	</table>		