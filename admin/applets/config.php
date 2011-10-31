<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.9                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2011                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }

function applet_config(){

    $inCore = cmsCore::getInstance();
    $inConf     = cmsConfig::getInstance();

	//check access
	global $adminAccess;
	if (!$inCore->isAdminCan('admin/config', $adminAccess)) { cpAccessDenied(); }

	$GLOBALS['cp_page_title'] = '��������� �����';
 	
	cpAddPathway('��������� �����', 'index.php?view=config');	
	
	$GLOBALS['cp_page_head'][] = '<script language="JavaScript" type="text/javascript" src="js/content.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/admin/js/config.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/jquery.form.js"></script>';
	$GLOBALS['cp_page_head'][] = '<script type="text/javascript" src="/includes/jquery/tabs/jquery.ui.min.js"></script>';
	
	$GLOBALS['cp_page_head'][] = '<link href="/includes/jquery/tabs/tabs.css" rel="stylesheet" type="text/css" />';

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'list'; }	
  
	include('../includes/config.inc.php');

    if (!isset($_CFG['wmark']))    { $_CFG['wmark'] = 'watermark.png'; }
    if (!isset($_CFG['timezone'])) { $_CFG['timezone'] = 'Europe/Moscow'; }
    if (!isset($_CFG['timediff'])) { $_CFG['timediff'] = '0'; }
	
	if ($do == 'save'){

		$newCFG = array();
		$newCFG['sitename'] 	= htmlspecialchars($inCore->request('sitename', 'str'), ENT_QUOTES);

        $newCFG['hometitle'] 	= htmlspecialchars($inCore->request('hometitle', 'str'), ENT_QUOTES);
        $newCFG['homecom']      = htmlspecialchars($inCore->request('homecom', 'str'), ENT_QUOTES);
			
		$newCFG['siteoff'] 		= $inCore->request('siteoff', 'int');
		$newCFG['debug'] 		= $inCore->request('debug', 'int');
		$newCFG['offtext'] 		= htmlspecialchars($inCore->request('offtext', 'str'), ENT_QUOTES);
		$newCFG['keywords'] 	= $inCore->request('keywords', 'str');
		$newCFG['metadesc'] 	= $inCore->request('metadesc', 'str');
		$newCFG['seourl']       = $inCore->request('seourl', 'int');
		$newCFG['lang']         = $inCore->request('lang', 'str', 'ru');
			
		$newCFG['sitemail'] 	= $inCore->request('sitemail', 'str');
		$newCFG['wmark']        = $inCore->request('wmark', 'str');
		$newCFG['stats'] 		= $inCore->request('stats', 'int');
		$newCFG['template'] 	= $inCore->request('template', 'str');
		$newCFG['splash'] 		= $inCore->request('splash', 'int');
		$newCFG['slight'] 		= $inCore->request('slight', 'int');
		$newCFG['back_btn']     = $inCore->request('back_btn', 'int');
		$newCFG['db_host'] 		= $inCore->request('db_host', 'str');
		$newCFG['db_base'] 		= $inCore->request('db_base', 'str');
		$newCFG['db_user'] 		= $_CFG['db_user'];
		$newCFG['db_pass'] 		= $_CFG['db_pass'];
		$newCFG['db_prefix']	= $_CFG['db_prefix'];
		$newCFG['show_pw']		= $inCore->request('show_pw', 'int');
		$newCFG['short_pw']		= $inCore->request('short_pw', 'int');
		$newCFG['index_pw']		= $inCore->request('index_pw', 'int');
		$newCFG['fastcfg']		= $inCore->request('fastcfg', 'int');
		
		$newCFG['mailer'] 		= $inCore->request('mailer', 'str');
		$newCFG['sendmail']		= $inCore->request('sendmail', 'str');
		$newCFG['smtpauth']		= $inCore->request('smtpauth', 'int');
		$newCFG['smtpuser']		= $inCore->request('smtpuser', 'str');
		$newCFG['smtppass']		= $inCore->request('smtppass', 'str');
		$newCFG['smtphost']		= $inCore->request('smtphost', 'str');
        $newCFG['lang']         = $_CFG['lang'];

        $newCFG['timezone']		= $inCore->request('timezone', 'str');
        $newCFG['timediff']		= $inCore->request('timediff', 'str');

		if ($inConf->saveToFile($newCFG)){
           $inCore->redirect('index.php?view=config&msg=ok');
        } else {
           cpCheckWritable('/includes/config.inc.php');
        }
	}
	
	if ($inCore->inRequest('msg')) { $msg='��������� ���������'; }
	
?>
<div style="width:800px">

      <?php if (@$msg) { echo '<p><font color="green">'.$msg.'</font></p>'; } ?>

      <?php cpCheckWritable('/includes/config.inc.php'); ?>
	  
<div id="config_tabs">

  <ul id="tabs">
	  	<li><a href="#basic"><span>����</span></a></li>
	  	<li><a href="#home"><span>������� ��������</span></a></li>
		<li><a href="#design"><span>������</span></a></li>
		<li><a href="#time"><span>�����</span></a></li>
		<li><a href="#database"><span>���� ������</span></a></li>
		<li><a href="#mail"><span>�����</span></a></li>
		<li><a href="#other"><span>������</span></a></li>
  </ul>
	
	<form action="/admin/index.php?view=config" method="post" name="CFGform" target="_self" id="CFGform" style="margin-bottom:30px">
        <div id="basic">
			<table width="720" border="0" cellpadding="5">
				<tr>
					<td>
                        <strong>�������� �����:</strong><br/>
						<span class="hinttext">������������ � ���������� �������</span>
                    </td>
					<td width="350" valign="top">
                        <input name="sitename" type="text" id="sitename" value="<?php echo @$_CFG['sitename'];?>" style="width:358px" />
                    </td>
				</tr>
				<tr>
					<td>
                        <strong>���� �����:</strong>
                    </td>
					<td width="350" valign="top">
                        <select name="lang" id="lang" style="width:364px">
                            <?php $inCore->langList($_CFG['lang']); ?>
                        </select>
                    </td>
				</tr>
				<tr>
					<td>
                        <strong>���� ��������:</strong><br/>
                        <span class="hinttext">����������� ���� ����� ������ ���������������</span>
                    </td>
					<td valign="top">
                        <input name="siteoff" type="radio" value="0" <?php if (@!$_CFG['siteoff']) { echo 'checked="checked"'; } ?>/> ��
                        <input name="siteoff" type="radio" value="1" <?php if (@$_CFG['siteoff']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
                </tr>
				<tr>
					<td>
                        <strong>�������� ����� �������:</strong><br/>
						<span class="hinttext">���������� ������ ���� ������ � ������ ��������</span>
                    </td>
					<td valign="top">
						<input name="debug" type="radio" value="1" <?php if (@$_CFG['debug']) { echo 'checked="checked"'; } ?>/> ��
						<input name="debug" type="radio" value="0" <?php if (@!$_CFG['debug']) { echo 'checked="checked"'; } ?>/> ���
                    </td>
				</tr>
				<tr>
					<td valign="middle">
                        <strong>������� ��������� ������:</strong><br />
						<span class="hinttext">������������ �� ������� ��������<br/>��� ���������� �����</span>

                    </td>
					<td valign="top"><input name="offtext" type="text" id="offtext" value="<?php echo @$_CFG['offtext'];?>" style="width:358px" /></td>
				</tr>
				<tr>
					<td>
                        <strong>E-mail �����: </strong><br/>
						<span class="hinttext">����� �� ����� �������� ����� �����������<br/>����������� �������������</span>
                    </td>
					<td>
						<input name="sitemail" type="text" id="sitemail" value="<?php echo @$_CFG['sitemail'];?>" style="width:358px" />					</td>
				</tr>
				<tr>
					<td>
                        <strong>������� ���� ��� ����������: </strong><br/>
						<span class="hinttext">�������� �������� � ����� /images/</span>
                    </td>
					<td>
						<input name="wmark" type="text" id="wmark" value="<?php echo @$_CFG['wmark'];?>" style="width:358px" />
                    </td>
				</tr>
				<tr>
					<td>
                        <strong>���� ����������: </strong></strong><br/>
						<span class="hinttext">������������� ���������� ����� ����� ������� ���������� <a href="index.php?view=components&do=config&link=statistics">����������</a></span>
                    </td>
					<td>
						<input name="stats" type="radio" value="1" <?php if (@$_CFG['stats']) { echo 'checked="checked"'; } ?>/> ���
						<input name="stats" type="radio" value="0" <?php if (@!$_CFG['stats']) { echo 'checked="checked"'; } ?>/> ����					</td>
				</tr>
				<tr>
					<td>
						<strong>������� ���������:</strong> <br />
						<span class="hinttext">���� ��������, �� ����� ��������� ������� ���������� �������� &quot;���������&quot;. </span>					</td>
				<td valign="top">
					<input name="fastcfg" type="radio" value="1" <?php if (@$_CFG['fastcfg']) { echo 'checked="checked"'; } ?>/> ���
					<input name="fastcfg" type="radio" value="0" <?php if (@!$_CFG['fastcfg']) { echo 'checked="checked"'; } ?>/> ����				</td>
				</tr>
			</table>
        </div>
        <div id="home">
			<table width="720" border="0" cellpadding="5">
                <tr>
    				<td>
                        <strong>��������� ������� ��������:</strong><br />
						<span class="hinttext">���� �� ������, ����� ��������� � ��������� �����</span><br/>
                        <span class="hinttext">������������ � ��������� ���� ��������</span>
                    </td>
                    <td width="350" valign="top">
                        <input name="hometitle" type="text" id="hometitle" value="<?php echo @$_CFG['hometitle'];?>" style="width:358px" />
                    </td>
			    </tr>
				<tr>
					<td valign="top">
						<strong>�������� �����:</strong><br />
						<span class="hinttext">����� �������, 10-15 ����.</span>
						<div class="hinttext" style="margin-top:4px"><a style="color:#09C" href="http://tutorial.semonitor.ru/#5" target="_blank">��� ��������� �������� �����?</a></div>
                    </td>
					<td>
						<textarea name="keywords" style="width:350px" rows="3" id="keywords"><?php echo @$_CFG['keywords'];?></textarea>					</td>
				</tr>
				<tr>
					<td valign="top">
						<strong>��������:</strong><br />
						<span class="hinttext">�� ����� 250 ��������.</span>
						<div class="hinttext" style="margin-top:4px"><a style="color:#09C" href="http://tutorial.semonitor.ru/#219" target="_blank">��� ��������� ��������� ��������?</a></div>
                    </td>
					<td>
						<textarea name="metadesc" style="width:350px" rows="3" id="metadesc"><?php echo @$_CFG['metadesc'];?></textarea>
                    </td>
				</tr>
                <tr>
    				<td>
                        <strong>��������� �� ������� ��������:</strong>
                    </td>
                    <td width="350" valign="top">
                        <select name="homecom" style="width:358px">
                            <option value="" <?php if(!$_CFG['homecom']){ ?>selected="selected"<?php } ?>>-- ��� ����������, ������ ������ --</option>
                            <?php echo $inCore->getListItems('cms_components', $_CFG['homecom'], 'title', 'ASC', 'internal=0', 'link'); ?>
                        </select>
                    </td>
			    </tr>
				<tr>
					<td>
						<strong>������� ��������:</strong> <br/>
						<span class="hinttext">������������ ��� ������ ��������� �����</span> <br/>
                        <span class="hinttext">����: <strong>/templates/&lt;��� ������&gt;/splash/splash.php</strong></span>
					</td>
					<td valign="top">
						<input name="splash" type="radio" value="0" <?php if (@!$_CFG['splash']) { echo 'checked="checked"'; } ?>/>	������
						<input name="splash" type="radio" value="1" <?php if (@$_CFG['splash']) { echo 'checked="checked"'; } ?>/> ����������
					</td>
				</tr>
			</table>
        </div>
		<div id="design">
			<table width="720" border="0" cellpadding="5">
				<tr>
					<td valign="top">
                        <div style="margin-top:2px">
                            <strong>������:</strong><br />
                            <span class="hinttext">���������� ����� &quot;templates/&quot; </span>
                        </div>                        
					</td>
					<td>
                        <select name="template" id="template" style="width:350px" onchange="document.CFGform.submit();">
                            <?php $inCore->templatesList($_CFG['template']); ?>
                        </select>
                        <div style="margin-top:5px" class="hinttext">
                            ��� ����� ������� ���������� �������� ����� &laquo;<strong>cache</strong>&raquo; �� �������
                        </div>
					</td>
				</tr>
				<tr>
					<td><strong>��������� ����������� ������:</strong></td>
					<td valign="top">
						<input name="slight" type="radio" value="1" <?php if (@$_CFG['slight']) { echo 'checked="checked"'; } ?>/> ��
						<input name="slight" type="radio" value="0" <?php if (@!$_CFG['slight']) { echo 'checked="checked"'; } ?>/> ���
					</td>
				</tr>
                <tr>
					<td><strong>���������� ������ &laquo;�����&raquo;:</strong></td>
					<td valign="top">
						<input name="back_btn" type="radio" value="1" <?php if (@$_CFG['back_btn']) { echo 'checked="checked"'; } ?>/> ��
						<input name="back_btn" type="radio" value="0" <?php if (@!$_CFG['back_btn']) { echo 'checked="checked"'; } ?>/> ���
					</td>
				</tr>
			</table>
		</div>
		<div id="time">
			<table width="720" border="0" cellpadding="5">
				<tr>
					<td valign="top" width="100">
                        <div style="margin-top:2px">
                            <strong>��������� ����:</strong>
                        </div>
					</td>
					<td>
                        <select name="timezone" id="timezone" style="width:350px">
                            <?php include(PATH.'/admin/includes/timezones.php'); ?>
                            <?php foreach($timezones as $tz) { ?>
                            <option value="<?php echo $tz; ?>" <?php if ($tz == $_CFG['timezone']) { ?>selected="selected"<?php } ?>><?php echo $tz; ?></option>
                            <?php } ?>
                        </select>
					</td>
				</tr>
				<tr>
					<td>
						<strong>�������� � �����:</strong>
					</td>
					<td width="350">
                        <select name="timediff" id="timediff" style="width:60px">
                            <?php for($h=-12; $h<=12; $h++) { ?>
                                <option value="<?php echo $h; ?>" <?php if ($h == $_CFG['timediff']) { ?>selected="selected"<?php } ?>><?php echo ($h > 0 ? '+'.$h : $h); ?></option>
                            <?php } ?>
                        </select>
					</td>
				</tr>
			</table>
		</div>
		<div id="database">
			<table width="720" border="0" cellpadding="5" style="margin-top:15px;">
				<tr>
					<td>
						<strong>������:</strong><br />
						<span class="hinttext">������ ��� &quot;localhost&quot; </span>
					</td>
					<td width="350">
						<input name="db_host" type="text" id="db_host" value="<?php echo @$_CFG['db_host'];?>" style="width:350px" />
					</td>
				</tr>
				<tr>
					<td>
						<strong>���� ������:</strong><br />
						<span class="hinttext">�������� �� MySQL </span>
					</td>
					<td>
						<input name="db_base" type="text" id="db_base" value="<?php echo @$_CFG['db_base'];?>" style="width:350px" />
					</td>
				</tr>
				<tr>
					<td>
						<strong>������� ���� ������:</strong><br />
						<span class="hinttext">������� �� MySQL </span>
					</td>
					<td>
						<input name="db_prefix" type="text" id="db_prefix" value="<?php echo @$_CFG['db_prefix'];?>" style="width:350px" />
					</td>
				</tr>
				<tr>
					<td colspan="2"><span class="hinttext">������������ � ������ MySQL ������������� � ����� /includes/config.inc.php</span></td>
				</tr>
			</table>
        </div>
		<div id="mail">
			<table width="720" border="0" cellpadding="5" style="margin-top:15px;">
				<tr>
					<td>
						<strong>������ ��������:</strong>
					</td>
					<td width="250">
						<?php if(!isset($_CFG['mailer'])) { $_CFG['mailer'] = 'mail'; } ?>
						<select name="mailer" style="width:354px">
							<option value="mail" <?php if (@$_CFG['mailer']=='mail') { echo 'selected="selected"'; } ?>>������� mail � PHP</option>
							<option value="sendmail" <?php if (@$_CFG['mailer']=='sendmail') { echo 'selected="selected"'; } ?>>Sendmail</option>
							<option value="smtp" <?php if (@$_CFG['mailer']=='smtp') { echo 'selected="selected"'; } ?>>SMTP-������</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<strong>���� � Sendmail:</strong><br/>
						<span class="hinttext">������ ��� /usr/sbin/sendmail</span>
					</td>
					<td width="350">
						<?php if(!isset($_CFG['sendmail'])) { $_CFG['sendmail'] = '/usr/sbin/sendmail'; } ?>
						<input name="sendmail" type="text" id="sendmail" value="<?php echo @$_CFG['sendmail'];?>" style="width:350px" />
					</td>
				</tr>
				<tr>
					<td>
						<strong>SMTP �����������:</strong>
					</td>
					<td width="350">
						<?php if(!isset($_CFG['smtpauth'])) { $_CFG['smtpauth'] = 0; } ?>
						<input name="smtpauth" type="radio" value="1" <?php if (@$_CFG['smtpauth']) { echo 'checked="checked"'; } ?>/> ��
						<input name="smtpauth" type="radio" value="0" <?php if (@!$_CFG['smtpauth']) { echo 'checked="checked"'; } ?>/> ���
					</td>
				</tr>	
				<tr>
					<td>
						<strong>SMTP ������������:</strong>
					</td>
					<td width="350">
						<?php if(!isset($_CFG['smtpuser'])) { $_CFG['smtpuser'] = ''; } ?>
						<input name="smtpuser" type="text" id="smtpuser" value="<?php echo @$_CFG['smtpuser'];?>" style="width:350px" />
					</td>
				</tr>
				<tr>
					<td>
						<strong>SMTP ������:</strong>
					</td>
					<td width="350">
						<?php if(!isset($_CFG['smtppass'])) { $_CFG['smtppass'] = ''; } ?>
						<input name="smtppass" type="password" id="smtppass" value="<?php echo @$_CFG['smtppass'];?>" style="width:350px" />
					</td>
				</tr>	
				<tr>
					<td>
						<strong>SMTP ����:</strong>
					</td>
					<td width="350">
						<?php if(!isset($_CFG['smtphost'])) { $_CFG['smtphost'] = 'localhost'; } ?>
						<input name="smtphost" type="text" id="smtphost" value="<?php echo @$_CFG['smtphost'];?>" style="width:350px" />
					</td>
				</tr>																		
			</table>
		</div>
		<div id="other">
			<table width="720" border="0" cellpadding="5">
				<tr>
					<td>
						<strong>���������� ����������?</strong><br />
						<span class="hinttext">
                            ���������� ���� � �������,<br/>
                            � ������� ��������� ����������
                        </span>
					</td>
					<td>
						<input name="show_pw" type="radio" value="1" <?php if (@$_CFG['show_pw']) { echo 'checked="checked"'; } ?>/> ��
						<input name="show_pw" type="radio" value="0" <?php if (@!$_CFG['show_pw']) { echo 'checked="checked"'; } ?>/> ��� 
					</td>
				</tr>
				<tr>
					<td><strong>���������� �� ������� ��������:</strong></td>
					<td>
						<input name="index_pw" type="radio" value="1" <?php if (@$_CFG['index_pw']) { echo 'checked="checked"'; } ?>/> ��
						<input name="index_pw" type="radio" value="0" <?php if (@!$_CFG['index_pw']) { echo 'checked="checked"'; } ?>/>	��� 
					</td>
				</tr>
				<tr>
					<td><strong>�������� ������� �������� � �����������:</strong></td>
					<td>
						<input name="short_pw" type="radio" value="0" <?php if (!$_CFG['short_pw']) { echo 'checked="checked"'; } ?>/> ��
						<input name="short_pw" type="radio" value="1" <?php if ($_CFG['short_pw']) { echo 'checked="checked"'; } ?>/> ���
					</td>
				</tr>
			</table>
        </div>
	</div>		

	<script type="text/javascript">$('#config_tabs > ul#tabs').tabs();</script>
	<div align="left" style="margin-top:15px">
		<input name="do" type="hidden" id="do" value="save" />
		<input name="save" type="submit" id="save" value="���������" />
		<input name="back" type="button" id="back" value="������" onclick="window.history.back();"/>
	</div>
</form>
</div>
<?php } ?>