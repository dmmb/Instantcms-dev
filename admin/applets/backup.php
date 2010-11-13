<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.6   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

function applet_backup(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = '��������� �����������';
 	
	cpAddPathway('��������� �����', 'index.php?view=config');	
	cpAddPathway('��������� �����������', 'index.php?view=backup');	
	
	$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/content.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/admin/js/config.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
		
?>
<div style="width:800px">

	<h3>��������� �����������</h3>
	<?php cpCheckWritable('/backups', 'folder'); ?>
		<table width="650" border="0" cellpadding="10" style="border:solid 1px gray; background:#FCFCFC">
			<tr>
				<td valign="top">
					<strong>��������� ����� ����:</strong><br />
					<span class="hinttext">������� �������� ����� � ������� &quot;�������&quot;</span>
				</td>
				<td valign="top" width="350">
					<div>
						<form id="dump" name="dump" action="/core/ajax/dumper.php" method="post">
							/backups/<input name="file" type="text" id="file" value="<?php echo date('d-m-Y').'.sql'?>" size="25" />
							<input type="button" name="godump" id="godump" value="�������" onclick="makeDump()"/>
						</form>
					</div>
					<div id="dumpinfo" style="margin-top:10px">
						<span class="hinttext">����� �������� �����, �������� ��� � ������� � �������.<br />
						��������� ����� �� ������� ����� ���� �� ���������!</span>
					</div>
				</td>
			</tr>
		</table>			
		
	<h3>�������������� ���� ������</h3>
		<?php cpCheckWritable('/backups', 'folder'); ?>
		<table width="650" border="0" cellpadding="10" style="border:solid 1px gray; background:#FCFCFC">
			<tr>
				<td width="250" valign="top">
					<strong>���� � ������ ����:</strong><br />
					<span class="hinttext">�������� ���� � ������� &quot;������&quot;</span>
				</td>
				<td valign="top">
					<div>
						<form id="importdump" name="importdump" action="/core/ajax/dumper.php" method="post" enctype="multipart/form-data">
							<input type="hidden" name="opt" value="import" />
							<input name="dumpfile" type="file" id="dumpfile" size="25" />
							<input type="button" name="goimport" id="goimport" value="������" onclick="importDump()"/>
						</form>
					</div>
					<div id="importinfo" style="margin-top:10px"></div>
				</td>
			</tr>
		</table>
</div>
<?php } ?>