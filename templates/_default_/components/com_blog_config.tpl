{* ================================================================================ *}
{* ============================= ��������� ����� ================================== *}
{* ================================================================================ *}

<form action="" method="post" name="cfgform" id="cfgform" style="margin-top:5px">
  <table width="550" border="0" cellpadding="6" cellspacing="0" style="background-color:#EBEBEB">
	<tr>
	  <td width="160"><strong>�������� �����: </strong></td>
	  <td><input name="title" type="text" id="title" value="{$blog.title}" style="width:360px"/></td>
	</tr>
	<tr>
	  <td><strong>���������� ����:</strong></td>
	  	<td>
			<select name="allow_who" id="allow_who" style="width:360px">
				<option value="all" selected="selected" {if ($blog.allow_who == 'all')} selected {/if}>����</option>
				<option value="friends" {if ($blog.allow_who == 'friends')} selected {/if}>���� �������</option>
				<option value="nobody" {if ($blog.allow_who == 'nobody')} selected {/if}>������ ���</option>
			</select>
		</td>
	</tr>
	<tr>
	  <td><strong>���������� �������</strong>: </td>
	  <td>
		  <select name="showcats" id="showcats">
			<option value="1" selected="selected" {if ($blog.showcats == 1)} selected {/if}>��</option>
			<option value="0" {if ($blog.showcats == 0)} selected {/if}>���</option>
		  </select>
	  </td>
	</tr>
  </table>			
  <table width="550" border="0" cellpadding="6" cellspacing="0" style="background-color:#EBEBEB;margin-top:6px">
	<tr>
	  <td width="160"><strong>��� �����: </strong></td>
	  <td>
		  <select name="ownertype" id="ownertype" onchange="selectOwnerType()" style="width:360px">
			<option value="single" {if ($blog.ownertype == 'single')} selected {/if}>������������ {$min_karma_private}</option>
			<option value="multi" {if ($blog.ownertype == 'multi')} selected {/if}>������������ {$min_karma_public}</option>
		  </select>
	  </td>
	</tr>
  </table>
  <table width="550" border="0" cellpadding="6" cellspacing="0" id="multiblogcfg" style="background-color:#EBEBEB;display:{if $blog.ownertype=='single'}none;{else}block;{/if}">
	<tr>
	  <td width="160"><strong>������������ �������: </strong></td>
	  <td>
		  <select name="premod" id="premod" style="width:360px">
			  <option value="1" {if ($blog.premod == 1)} selected {/if}>��������</option>
			  <option value="0" {if ($blog.premod == 0)} selected {/if}>���������</option>
		  </select>
	  </td>
	</tr>
	<tr>
	  <td><strong>��� ����� ������ �  ����: </strong></td>
	  <td>
		  <select name="forall" id="forall" onchange="selectAuthorsType()" style="width:360px">
			  <option value="1" {if ($blog.forall == 1)} selected {/if}>��� ������������</option>
			  <option value="0" {if ($blog.forall == 0)} selected {/if}>������ �������������</option>
		  </select>
	  </td>
	</tr>
  </table>
  <input type="hidden" name="uid" id="uid" value="<?php echo $blog['user_id']?>"/>
  <table width="550" border="0" cellspacing="0" cellpadding="10" id="multiuserscfg" style="margin-top:5px;border:solid 1px silver;display: {if $blog.ownertype=='single' || $blog.forall}none;{else}table;{/if}">
	  <td align="center" valign="top"><strong>����� ������ � ����: </strong><br/>				   
		<select name="authorslist[]" size="15" multiple id="authorslist" style="width:200px">
			{$authors_list}
		</select>          
	  </td>
	  <td align="center">
	  	  <div><input name="author_add" type="button" id="author_add" value="&lt;&lt;"></div>
		  <div><input name="author_remove" type="button" id="author_remove" value="&gt;&gt;" style="margin-top:4px"></div>
	  </td>
	  <td align="center" valign="top"><strong>��� ������������:</strong><br/>
		<select name="userslist" size="15" multiple id="userslist" style="width:200px">
			{$users_list}
		</select>
	  </td>
	</tr>  
  </table>
  <p>
	<input name="goadd" type="submit" id="goadd" value="��������� ���������" /> 
	<input name="delete" type="button" onclick="window.location.href='/blogs/{$menuid}/{$id}/delblog.html'" value="������� ����" />
	<input name="cancel" type="button" onclick="window.history.go(-1)" value="������" />
  </p>
</form>