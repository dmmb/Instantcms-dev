{* ================================================================================ *}
{* ================= ��������������/�������� ������ =============================== *}
{* ================================================================================ *}

<div class="con_heading">{$pagetitle}</div>

<form id="addform" name="addform" method="post" action="">
	<table width="605" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table width="605" border="0" cellspacing="5" class="proptable">
					<tr>
						<td width="236" valign="top">
							<strong>��������� ������:</strong><br />
							<span class="hinttext">������������ �� �����</span>
						</td>
						<td width="348" valign="top">
						 	<input name="title" type="text" id="title" style="width:300px" value="{$mod.title}" />
						</td>
					</tr>
					<tr>
						<td valign="top">
							<strong>���� ������:</strong><br />
							<span class="hinttext">�������� �����, ����� �������</span>
						</td>
						<td valign="top">
							<input name="tags" type="text" id="tags" style="width:300px" value="{$mod.tags}" />
							<script type="text/javascript">
								{$autocomplete_js}
							</script>
						</td>
					</tr>
					{if $do=='addarticle'}
					<tr>
						<td valign="top">
							<strong>������:</strong><br />
							<div><span class="hinttext">���� ��������� ������</span></div>
							{if $is_admin}
								<div style="margin-top:10px"><span class="hinttext">����� �������� ������ � ���� ������, �������� � <a href="/admin/index.php?view=cats">����������</a> ����� ������� ����� "��������� ������ �� �������������"</span></div>
							{/if}
						</td>
						<td valign="top">
							<select name="category_id" size="8" id="category_id" style="width:304px">								
								{$pubcats}
							</select>
						</td>
					</tr>
					{/if}
                    {if $do=='editarticle'}
                        <input type="hidden" name="category_id" value="{$mod.category_id}" />
                    {/if}
				</table>
			</td>
		</tr>
	</table>
	<table width="100%" border="0">
		<tr>
			<td>
				<p><strong>����� ������</strong> (�� �����������):</p>
				<div>{wysiwyg name='description' value=$mod.description height=200 width='90%' toolbar='Basic'}</div>

				<p><strong>����� ������:</strong></p>
				<div>{wysiwyg name='content' value=$mod.content height=450 width='90%' toolbar='Admin'}</div>
			</td>
		</tr>
	</table>
				  
	{if $do=='editarticle'}
		{$add_notice}
	{/if}

	<p>
		<input name="add_mod" type="submit" id="add_mod" {if $do=='addarticle'} value="�������� ������" {else} value="��������� ���������" {/if} />
		<input name="back" type="button" id="back" value="������" onclick="window.history.back();"/>
		{if $do=='editarticle'}
			<input name="id" type="hidden" value="{$mod.id}" />
		{/if}
	</p>
</form>