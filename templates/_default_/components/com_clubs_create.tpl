{* ================================================================================ *}
{* =============================== �������� ����� ================================= *}
{* ================================================================================ *}

<div class="con_heading">������� ����</div>

<p>
	<strong>�����</strong> ��������� ����� ������������. �������� ����� ���� �� ������������� ����������� ��� ���������������.
<p>
�� ������� ��������� ����������� � ������������� ����������� �� ���������� ������ � ����������.
</p>

<p>
	������ ��� ����� ������� �������� ����� � ��� ���. ����������� ��������� ����� �������� ����� �������� �����.
</p>

<form style="margin-top:15px" action="" method="post" name="addform">
  <table style="background-color:#EBEBEB" border="0" cellspacing="0" cellpadding="10">
	<tr>
	  <td width="140">
	  	<strong>�������� �����: </strong>
	  </td>
	  <td>
	  	<input name="title" type="text" id="title" style="width:300px" />
	</td>
	</tr>
	<tr>
	  <td><strong>��� �����: </strong></td>
	  <td>
		  <select name="clubtype" id="clubtype" style="width:300px">
			<option value="public">������ ��� ���� (public)</option>
			<option value="private">������ ��� ��������� (private)</option>
		  </select>
	  </td>
	</tr>
  </table>			
  <p>
  	<input name="create" type="submit" id="create" value="������� ����" /> 
  	<input name="cancel" type="button" onclick="window.history.go(-1)" value="������" />
  </p>
</form>