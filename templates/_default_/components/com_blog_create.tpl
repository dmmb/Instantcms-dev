{* ================================================================================ *}
{* ============================== �������� ����� ================================== *}
{* ================================================================================ *}

<div class="con_heading">������� ����</div>

<p><strong>����</strong> - ��� �������� ������ ���������� ������� � �����. � ������� �� �������� ��������� ��������, �������
�� ������� ���������� ����������, ��������-������� ������ ��� �������. ����������� � ����e ������ ����� ���������������� ������� ��������������.</p>
<p>���������� ���� � ����� ���������� �����, ���������� ����� ����������� ��� ������ ���������� ������ ��� ���������.</p>
<p>�������� ������������ ��������-���� - �������� ���� ����!</p>
<form style="margin-top:15px" action="" method="post" name="addform">
  <table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="6">
	<tr>
	  <td width="180"><strong>�������� �����: </strong></td>
	  <td><input name="title" type="text" id="title" size="40" /></td>
	</tr>
	<tr>
	  <td><strong>��� �����: </strong></td>
	  <td>
	  	  <select name="ownertype" id="ownertype">
			  <option value="single" selected>������������ {$min_karma_private}</option>
			  <option value="multi" >������������ {$min_karma_public}</option>
		  </select>
	  </td>
	</tr>
	<tr>
	  <td><strong>����������:</strong></td>
	  <td>
	  	<select name="allow_who" id="allow_who">
			<option value="all" selected="selected">����</option>
			<option value="friends" {if $friends eq 1}selected="selected"{/if}>���� �������</option>
			<option value="nobody">������ ���</option>
		</select>
	   </td>
	</tr>
  </table>			
  <p>
  	<input name="goadd" type="submit" id="goadd" value="������� ����" /> 
  	<input name="cancel" type="button" onclick="window.history.go(-1)" value="������" />
  </p>
</form>