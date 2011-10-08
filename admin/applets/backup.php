<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8.1                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function applet_backup(){

    $inCore = cmsCore::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = 'Резервное копирование';
 	
	cpAddPathway('Настройки сайта', 'index.php?view=config');	
	cpAddPathway('Резервное копирование', 'index.php?view=backup');	
	
	$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/content.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/admin/js/config.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
		
?>
<div style="width:800px">

	<h3>Резервное копирование</h3>
	<?php cpCheckWritable('/backups', 'folder'); ?>
		<table width="650" border="0" cellpadding="10" style="border:solid 1px gray; background:#FCFCFC">
			<tr>
				<td valign="top">
					<strong>Сохранить копию базы:</strong><br />
					<span class="hinttext">Введите название файла и нажмите &quot;Экспорт&quot;</span>
				</td>
				<td valign="top" width="350">
					<div>
						<form id="dump" name="dump" action="/core/ajax/dumper.php" method="post">
							/backups/<input name="file" type="text" id="file" value="<?php echo date('d-m-Y').'.sql'?>" size="25" />
							<input type="button" name="godump" id="godump" value="Экспорт" onclick="makeDump()"/>
						</form>
					</div>
					<div id="dumpinfo" style="margin-top:10px">
                        <span class="hinttext" style="color:gray">
                            Если база большого объема, то перед началом экспорта убедитесь что максимальное время выполнения скрипта
                            (max_execution_time в php.ini) достаточно велико, чтобы экспорт успел произойти полностью.<br/><br/>
                            После создания дампа, скачайте его по FTP из папки <strong>backups</strong>и удалите с сервера.
						</span>
					</div>
				</td>
			</tr>
		</table>			
		
	<h3>Восстановление базы данных</h3>
		<?php cpCheckWritable('/backups', 'folder'); ?>
		<table width="650" border="0" cellpadding="10" style="border:solid 1px gray; background:#FCFCFC">
			<tr>
				<td width="250" valign="top">
					<strong>Файл с дампом базы:</strong><br />
					<span class="hinttext">Выберите файл и нажмите &quot;Импорт&quot;</span>
				</td>
				<td valign="top">
					<div>
						<form id="importdump" name="importdump" action="/core/ajax/dumper.php" method="post" enctype="multipart/form-data">
							<input type="hidden" name="opt" value="import" />
							<input name="dumpfile" type="file" id="dumpfile" size="25" />
							<input type="button" name="goimport" id="goimport" value="Импорт" onclick="importDump()"/>
						</form>
					</div>
					<div id="importinfo" style="margin-top:10px"></div>
				</td>
			</tr>
		</table>
</div>
<?php } ?>