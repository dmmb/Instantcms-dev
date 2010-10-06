{* ================================================================================ *}
{* ============================= Настройка блога ================================== *}
{* ================================================================================ *}

<form action="" method="post" name="cfgform" id="cfgform" style="margin-top:5px">
  <table width="550" border="0" cellpadding="6" cellspacing="0" style="background-color:#EBEBEB">
	<tr>
	  <td width="160"><strong>{$LANG.BLOG_TITLE}: </strong></td>
	  <td><input name="title" type="text" id="title" value="{$blog.title|escape:'html'}" style="width:360px"/></td>
	</tr>
	<tr>
	  <td><strong>{$LANG.SHOW_BLOG}:</strong></td>
	  	<td>
			<select name="allow_who" id="allow_who" style="width:360px">
				<option value="all" selected="selected" {if ($blog.allow_who == 'all')} selected {/if}>{$LANG.TO_ALL}</option>
				<option value="friends" {if ($blog.allow_who == 'friends')} selected {/if}>{$LANG.TO_MY_FRIENDS}</option>
				<option value="nobody" {if ($blog.allow_who == 'nobody')} selected {/if}>{$LANG.TO_ONLY_ME}</option>
			</select>
		</td>
	</tr>
	<tr>
	  <td><strong>{$LANG.SHOW_CAT}</strong>: </td>
	  <td>
		  <select name="showcats" id="showcats">
			<option value="1" selected="selected" {if ($blog.showcats == 1)} selected {/if}>{$LANG.YES}</option>
			<option value="0" {if ($blog.showcats == 0)} selected {/if}>{$LANG.NO}</option>
		  </select>
	  </td>
	</tr>
  </table>			
  <table width="550" border="0" cellpadding="6" cellspacing="0" style="background-color:#EBEBEB;margin-top:6px">
	<tr>
	  <td width="160"><strong>{$LANG.BLOG_TYPE}: </strong></td>
	  <td>
		  <select name="ownertype" id="ownertype" onchange="selectOwnerType()" style="width:360px">
			<option value="single" {if ($blog.ownertype == 'single')} selected {/if}>{$LANG.PERSONAL} {$min_karma_private}</option>
			<option value="multi" {if ($blog.ownertype == 'multi')} selected {/if}>{$LANG.COLLECTIVE} {$min_karma_public}</option>
		  </select>
	  </td>
	</tr>
  </table>
  <table width="550" border="0" cellpadding="6" cellspacing="0" id="multiblogcfg" style="background-color:#EBEBEB;display:{if $blog.ownertype=='single'}none;{else}block;{/if}">
	<tr>
	  <td width="160"><strong>{$LANG.PREMODER_POST}: </strong></td>
	  <td>
		  <select name="premod" id="premod" style="width:360px">
			  <option value="1" {if ($blog.premod == 1)} selected {/if}>{$LANG.ON}</option>
			  <option value="0" {if ($blog.premod == 0)} selected {/if}>{$LANG.OFF}</option>
		  </select>
	  </td>
	</tr>
	<tr>
	  <td><strong>{$LANG.WHO_CAN_WRITE_TO_BLOG}: </strong></td>
	  <td>
		  <select name="forall" id="forall" onchange="selectAuthorsType()" style="width:360px">
			  <option value="1" {if ($blog.forall == 1)} selected {/if}>{$LANG.ALL_USERS}</option>
			  <option value="0" {if ($blog.forall == 0)} selected {/if}>{$LANG.LIST_USERS}</option>
		  </select>
	  </td>
	</tr>
  </table>
  <input type="hidden" name="uid" id="uid" value="{$blog.user_id}"/>
  <table width="550" border="0" cellspacing="0" cellpadding="10" id="multiuserscfg" style="margin-top:5px;border:solid 1px silver;display: {if $blog.ownertype=='single' || $blog.forall}none;{else}table;{/if}">
	  <td align="center" valign="top"><strong>{$LANG.CAN_WRITE_TO_BLOG}: </strong><br/>
		<select name="authorslist[]" size="15" multiple id="authorslist" style="width:200px">
			{$authors_list}
		</select>          
	  </td>
	  <td align="center">
	  	  <div><input name="author_add" type="button" id="author_add" value="&lt;&lt;"></div>
		  <div><input name="author_remove" type="button" id="author_remove" value="&gt;&gt;" style="margin-top:4px"></div>
	  </td>
	  <td align="center" valign="top"><strong>{$LANG.ALL_USERS}:</strong><br/>
		<select name="userslist" size="15" multiple id="userslist" style="width:200px">
			{$users_list}
		</select>
	  </td>
	</tr>  
  </table>
  <p>
	<input name="goadd" type="submit" id="goadd" value="{$LANG.SAVE_CONFIG}" />
	<input name="delete" type="button" onclick="window.location.href='/blogs/{$id}/delblog.html'" value="{$LANG.DEL_BLOG}" />
	<input name="cancel" type="button" onclick="window.history.go(-1)" value="{$LANG.CANCEL}" />
  </p>
</form>