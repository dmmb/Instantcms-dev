{* ================================================================================ *}
{* ========================= Настройки клуба ====================================== *}
{* ================================================================================ *}

<div class="con_heading">
	<a href="/clubs/{$menuid}/{$club.id}">{$club.title}</a> &rarr; Настройки
</div>

{add_js file='includes/jquery/tabs/jquery.ui.min.js'}
{add_js file='admin/js/clubs.js'}
{add_css file='includes/jquery/tabs/tabs.css'}					

<form name="configform" id="club_config_form" action="" method="post" enctype="multipart/form-data">

<div id="configtabs">
	<ul id="tabs"> 
		<li><a href="#tab1"><span>Описание клуба</span></a></li> 
		<li><a href="#tab2"><span>Модераторы</span></a></li> 
		<li><a href="#tab3"><span>Участники</span></a></li>
		{if $club.enabled_photos || $club.enabled_blogs}
		<li><a href="#tab4"><span>Ограничения</span></a></li> 
		{/if}
	</ul> 
	
	{* ============================== ЗАКЛАДКА №1 ============================================== *}
	<div id="tab1">	
		<table width="100%" border="0" cellspacing="0" cellpadding="10" style="border-bottom:solid 1px silver;margin-bottom:20px">
			<tr>
				<td width="48">
					<div style="padding:2px; border: solid 1px silver">
						<img src="/images/clubs/small/{$club.imageurl}" border="0" alt="{$club.title}"/>
					</div>
				</td>
				<td width="120">
					<label>Загрузить логотип:</label>
				</td>
				<td>
					<input name="picture" type="file" id="picture" />
				</td>			
			</tr>	
		</table>	
		{wysiwyg name='description' value=$club.description height=350 width='100%' toolbar='Admin'}
	</div>

	<div id="tab2">	
		<table width="500" border="0" cellspacing="0" cellpadding="10" id="multiuserscfg">
			<tr>
				<td colspan="3">
					<div class="hint">Модераторы имеют доступ ко всему содержимому клуба. Они могут редактировать, удалять и модерировать посты в блоге, фотографии и фотоальбомы.</div>
				</td>
			</tr>	
			<tr>			
				<td align="center" valign="top">
					<p><strong>Модераторы клуба: </strong></p>
					<select name="moderslist[]" size="10" multiple id="moderslist" style="width:200px">
						{$moders_list}
					</select>
				</td>
				<td align="center">
					<div><input name="moderator_add" type="button" id="moderator_add" value="&lt;&lt;"></div>
					<div><input name="moderator_remove" type="button" id="moderator_remove" value="&gt;&gt;" style="margin-top:4px"></div>
				</td>
				<td align="center" valign="top">
					<p><strong>Все пользователи:</strong></p>
					<select name="userslist1" size="10" multiple id="userslist1" style="width:200px">
						{$users_list}
					</select>
				</td>
			</tr> 
		</table>
	</div>

	<div id="tab3">		
		<table width="500" border="0" cellspacing="0" cellpadding="10">
			<tr>
			  <td>Максимальное число участников:<br/><span style="color:#CCCCCC">Установите "0" для бесконечного количества</span> </td>
			  <td><input name="maxsize" type="text" style="width:200px"  value="{$club.maxsize}"/></td>
		  </tr>
			<tr>
				<td>
					<label>Выберите тип клуба:</label>
				</td>
				<td width="200">
					<select name="clubtype" id="clubtype" style="width:200px" onchange="toggleMembers()">
                        <option value="public" {if $club.clubtype=='public'}selected="selected"{/if}>Открыт для всех (public)</option>
                        <option value="private" {if $club.clubtype=='private'}selected="selected"{/if}>Открыт для избранных (private)</option>
                     </select>
				</td>			
			</tr>	
		</table>
		<table width="500" border="0" cellspacing="0" id="minkarma" cellpadding="10" style="display: {if $club.clubtype!='public'}none;{else}table;{/if}">
			<tr>
			  <td>Использовать ограничение по карме: <br/><span style="color:#CCCCCC">Если отключено, любой пользователь <br/> сможет вступить в клуб</span></td>
			  <td valign="top">
					<input name="join_karma_limit" type="radio" value="1" {if $club.join_karma_limit}checked{/if}/> Да 
					<input name="join_karma_limit" type="radio" value="0" {if !$club.join_karma_limit}checked{/if}/> Нет
			  </td>
		  </tr>
			<tr>
				<td>
					Ограничение по карме: <br/><span style="color:#CCCCCC">Размер кармы, необходимый пользователю <br/> для вступления клуб</span>
				</td>
				<td width="200" valign="top">
					&ge; <input name="join_min_karma" type="text" style="width:60px" value="{$club.join_min_karma}"/> баллов
				</td>			
			</tr>	
		</table>		
		<table width="500" border="0" cellspacing="0" cellpadding="10" id="members" style="display: {if $club.clubtype=='public'}none;{else}table;{/if}">
			<tr>
				<td align="center" valign="top">
					<p><strong>Участники клуба: </strong></p>
					<select name="memberslist[]" size="10" multiple id="memberslist" style="width:200px">
						{$members_list}
					</select>
				</td>
				<td align="center">
					<div><input name="member_add" type="button" id="member_add" value="&lt;&lt;"></div>
					<div><input name="member_remove" type="button" id="member_remove" value="&gt;&gt;" style="margin-top:4px"></div>
				</td>
				<td align="center" valign="top">
					<p><strong>Все пользователи:</strong></p>
					<select name="userslist2" size="10" multiple id="userslist2" style="width:200px">
						{$users_list}
					</select>
				</td>
			</tr>  
		</table>
	</div>
	
	{if $club.enabled_photos || $club.enabled_blogs}
	<div id="tab4">		
		<table width="400" border="0" cellspacing="0" cellpadding="10">
			{if $club.enabled_blogs}
			<tr>
				<td>
					<label><strong>Премодерация записей в блогах:</strong></label>
				</td>
				<td width="150">
					<input name="blog_premod" type="radio" value="1" {if $club.blog_premod}checked{/if}/> Да 
					<input name="blog_premod" type="radio" value="0" {if !$club.blog_premod}checked{/if}/> Нет
				</td>			
			</tr>	
			{/if}
			{if $club.enabled_photos}
			<tr>
				<td>
					<label><strong>Премодерация фотографий:</strong></label>
				</td>
				<td width="150">
					<input name="photo_premod" type="radio" value="1" {if $club.photo_premod}checked{/if}/> Да 
					<input name="photo_premod" type="radio" value="0" {if !$club.photo_premod}checked{/if}/> Нет
				</td>			
			</tr>	
			{/if}
			{if $club.enabled_blogs}
			<tr>
				<td>
					<label>Ограничение по карме для <br/>создания новых постов в блоге:</label>
				</td>
				<td width="150">&ge; <input name="blog_min_karma" type="text" style="width:60px" value="{$club.blog_min_karma}"/> баллов
			  </td></tr>	
			{/if}
			{if $club.enabled_photos}
			<tr>
				<td>
					<label>Ограничение по карме для <br/>добавления фотографий:</label>
				</td>
				<td width="150">
					&ge;  
					<input name="photo_min_karma" type="text" style="width:60px" value="{$club.photo_min_karma}"/> баллов
				</td>			
			</tr>	
			{/if}
			{if $club.enabled_photos}	
			<tr>
				<td>
					<label>Ограничение по карме для <br/>cоздания фотоальбомов:</label>
				</td>
				<td width="150">
					&ge; <input name="album_min_karma" type="text" style="width:60px" value="{$club.album_min_karma}"/> баллов
			  </td>			
			</tr>	
			{/if}							
		</table>
	</div>
	{/if}
	
</div>

<p>
	<input type="submit" class="button" name="save" value="Сохранить"/> 
	<input type="button" class="button" name="back" value="Отмена" onclick="window.history.go(-1)"/> 
</p>

</form>

{literal}
	<script type="text/javascript">
		$("#configtabs > ul#tabs").tabs();
		$("#club_config_form").submit(function() { 
		$('#moderslist').each(function(){
				$('#moderslist option').attr("selected","selected");
			});  
			$('#memberslist').each(function(){
				$('#memberslist option').attr("selected","selected");
			});  
		});
	</script>
{/literal}