{* ================================================================================ *}
{* ==================== ����� ���������� �� ����� ���������� ====================== *}
{* ================================================================================ *}

<form action="{$action_url}" method="POST">
	<div class="photo_sortform">
		<table cellspacing="2" cellpadding="2" >
			<tr>
				<td >���: </td>
				<td >
					<select name="obtype" id="obtype">
						<option value="" {if (empty($btype))} selected {/if}>��� ����</option>
						{$btypes}
					</select>
				</td>
				<td >�����: </td>
				<td >
					{$bcities}
				</td>
				<td >�����������: </td>
				<td >
					<select name="orderby" id="orderby">
						<option value="title" {if $orderby=='title'} selected {/if}>�� ��������</option>
						<option value="pubdate" {if $orderby=='pubdate'} selected {/if}>�� ����</option>
						<option value="hits" {if $orderby=='hits'} selected {/if}>�� ����������</option>
						<option value="obtype" {if $orderby=='obtype'} selected {/if}>�� ����</option>
						<option value="user_id" {if $orderby=='user_id'} selected {/if}>�� ������</option>
					</select>
					<select name="orderto" id="orderto">';
						<option value="desc" {if $orderto=='desc'} selected {/if}>�� ��������</option>
						<option value="asc" {if $orderto=='asc'} selected {/if}>�� �����������</option>
					</select>
					<input type="submit" value="������" />
				</td>		
			</tr>
		</table>
	</div>
</form>