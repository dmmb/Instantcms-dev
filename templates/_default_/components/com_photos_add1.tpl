{* ================================================================================ *}
{* ========================= �������� ����, ��� 1 ================================= *}
{* ================================================================================ *}

<h3 style="border-bottom: solid 1px gray">
	<strong>��� 1</strong>: �������� �����
</h3>

<form enctype="multipart/form-data" action="{$form_action}" method="POST">
	<input name="upload" type="hidden" value="1"/>
	<input name="userid" type="hidden" value="{$user_id}"/>

	<p>�������� ���� ��� ��������: </p>
	<input name="picture" type="file" id="picture" size="30" />
	
	<div style="margin-top:5px">
		<strong>���������� ���� ������:</strong> gif, jpg, jpeg, png
	</div>
	
	<p>
		<input type="submit" value="���������"> 
		<input type="button" onclick="window.history.go(-1);" value="������"/>
	</p>
</form>