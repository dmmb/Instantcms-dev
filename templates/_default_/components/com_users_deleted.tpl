{* ================================================================================ *}
{* ========================= ������� ���������� ������������ ====================== *}
{* ================================================================================ *}

<div id="usertitle">
	<div class="con_heading" id="nickname" style="float:left;">
		{$nickname}		
	</div>						
</div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="200" valign="top">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center" valign="middle" style="padding:10px; border:solid 1px gray; background-color:#FFFFFF">
						{$avatar}
					</td>
				</tr>		
			</table>

	    </td>
    	<td valign="top" style="padding-left:10px">	
				<div class="usr_deleted">������� ������������ ��� ������.</div>
				{if $is_admin}
                    {if !$others_active}
                        <div class="usr_restore">�� ������ <a href="/users/restoreprofile{$id}.html">������������ �������</a></div>
                    {else}
                        <div class="usr_restore">�� �� ������ ������������ ���� �������, �.�. �� ����� ���� �������� ������������ � ����� �� ������� ({$login}).</div>
                    {/if}
				{/if}
		</td>
  </tr>
</table>
