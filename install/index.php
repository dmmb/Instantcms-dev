<?php
/******************************************************************************/
//                                                                            //
//                             InstantCMS v1.8                                //
//                        http://www.instantcms.ru/                           //
//                                                                            //
//                   written by InstantCMS Team, 2007-2010                    //
//                produced by InstantSoft, (www.instantsoft.ru)               //
//                                                                            //
//                        LICENSED BY GNU/GPL v2                              //
//                                                                            //
/******************************************************************************/

	define('VALID_CMS', 1);

    define('PATH', $_SERVER['DOCUMENT_ROOT']);
    define('HOST', 'http://' . $_SERVER['HTTP_HOST']);

    include('../core/cms.php');

    $inCore     = cmsCore::getInstance(true);

    $inCore->loadClass('config');

    $inConf     = cmsConfig::getInstance();

    //����������� ������ PHP
    $php_req = array();
    $php_req['major']       = '5';
    $php_req['minor']       = '2';
    $php_req['release']     = '0';

    $php_req_ver = $php_req['major'] * 10000 + $php_req['minor'] * 100 + $php_req['release'];

    //������ ����������� ���������� PHP
    $ext_req = array();
    $ext_req['mbstring']    = 'mbstring';
    $ext_req['iconv']       = 'iconv';
    $ext_req['GD']          = 'gd';
    $ext_req['SimpleXML']   = 'simplexml';

	if (isset($_POST['install'])){

		$msg = '';

		if(!empty($_REQUEST['sitename'])) { $sitename = $_REQUEST['sitename']; } else { $sitename = '��� ����'; }
		if(!empty($_REQUEST['db_server'])) { $db_server = $_REQUEST['db_server']; } else { $msg .= '���������� ������� ������ ��!<br/>'; }
		if(!empty($_REQUEST['db_base'])) { $db_base = $_REQUEST['db_base']; } else { $msg .= '���������� ������� �������� ��!<br/>'; }
		if(!empty($_REQUEST['db_user'])) { $db_user = $_REQUEST['db_user']; } else { $msg .= '���������� ������� ������������ ��!<br/>'; }
		if(!empty($_REQUEST['db_password'])) { $db_password = $_REQUEST['db_password']; } else { $db_password = ''; }
		if(!empty($_REQUEST['db_prefix'])) { $db_prefix = $_REQUEST['db_prefix']; } else { $msg .= '���������� ������� ������� ��!<br/>'; }
		if(!empty($_REQUEST['admin_login'])) { $admin_login = $_REQUEST['admin_login']; } else { $msg .= '���������� ������� ����� ��������������!<br/>'; }
		if(!empty($_REQUEST['admin_password'])) { $admin_password = $_REQUEST['admin_password']; } else { $msg .= '���������� ������� ������ ��������������!<br/>'; }

		if(!$msg){

			//INSTALL SYSTEM
			$_CFG = array();
			$_CFG['sitename']     = $sitename;
			$_CFG['db_host']      = $db_server;
			$_CFG['db_base']      = $db_base;
			$_CFG['db_user']      = $db_user;
			$_CFG['db_pass']      = $db_password;
			$_CFG['db_prefix']    = $db_prefix;
			$_CFG['template']     = '_default_';
			$_CFG['tooltips']     = '1';
			$_CFG['index_pw']     = '0';
			$_CFG['show_pw']      = '1';
			$_CFG['short_pw']     = '1';
			$_CFG['splash']       = '0';
			$_CFG['stats']        = '0';
			$_CFG['slight']       = '0';
			$_CFG['siteoff']      = '0';
			$_CFG['offtext']      = '������������ ���������� �����';
			$_CFG['keywords']     = 'InstantCMS, ������� ���������� ������, ���������� CMS, ������ �����, CMS, ������ ���������� ����';
			$_CFG['metadesc']     = 'InstantCMS - ���������� ������� ���������� ������ � ����������� ���������';
			$_CFG['fastcfg']      = '1';
            $_CFG['debug']        = '0';
            $_CFG['lang']         = 'ru';
            $_CFG['wmark']        = 'watermark.png';
            $_CFG['back_btn']     = '0';
            $_CFG['timezone']     = 'Europe/Moscow';
            $_CFG['timediff']     = '0';
            $_CFG['admin_folder'] ='admin';

            $inConf->saveToFile($_CFG);

			$GLOBALS['db'] = @mysql_connect($_CFG['db_host'], $_CFG['db_user'], $_CFG['db_pass']);

			if (mysql_error()) {
				$msg .= '�� ������� ���������� ���������� c MySQL.<br/>
						 ��������� ����� ������� MySQL � ������������ ������������ � ������ ��.<br/>
						 �� ���������� ���� ���������� �� ������ ���������� � ������ �������.'; }
			else {
				@mysql_select_db($_CFG['db_base'], $GLOBALS['db']);
				if (mysql_error()) {
					$msg .= '�� ������� ������� �� MySQL.<br/>
							 ���� ������ "'.$_CFG['db_base'].'" �� ������� �� ��������� �������.<br/>
						 	 �� ���������� ���� ���������� �� ������ ���������� � ������ �������.';
				}
			}

			if(!$msg){

                $sql_file = ((int)$_REQUEST['demodata']==1 ?'sqldumpdemo.sql' : 'sqldumpempty.sql');

                include($_SERVER['DOCUMENT_ROOT'].'/includes/database.inc.php');
                include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');

                mysql_query("SET NAMES cp1251");

                dbRunSQL($_SERVER['DOCUMENT_ROOT'].'/install/'.$sql_file, $db_prefix);

                $sql = "UPDATE {$db_prefix}_users
                        SET password = md5('{$admin_password}'),
                            login = '{$admin_login}'
                        WHERE id = 1";

                mysql_query($sql);

                $installed = (mysql_error() ? 0 : 1);

                $sql = "UPDATE {$db_prefix}_users
                        SET password = md5('{$admin_password}')
                        WHERE id > 1";

                mysql_query($sql);

			}

		}

	}

// =================================================================================================== //

function getPHPVersion(){
    $version['text'] = phpversion();
    $version['int']  = $version['text'][0] * 10000 + $version['text'][2] * 100 + $version['text'][4];
    return $version;
}

function installCheckFolders(){
	$folders = array();
	$folders[] = '/images';
	$folders[] = '/upload';
	$folders[] = '/includes';
	$folders[] = '/backups';
    $folders[] = '/cache';

	echo '<table align="center">';
		echo '<tr>';
			echo '<th width="260">�����</th>';
			echo '<th style="text-align:center" width="170">�������� ��� ������</th>';
		echo '</tr>';

	foreach($folders as $key=>$folder){
		$right = true;
		if(!@is_writable($_SERVER['DOCUMENT_ROOT'].$folder)){
			if (!@chmod($_SERVER['DOCUMENT_ROOT'].$folder, 0777)){
				$right = false;;
			}
		}
		echo '<tr>';
			echo '<td class="folder">'.$folder.'</td>';
			echo '<td style="text-align:center">'.($right ? '<span style="color:green">��</span>' : '<span style="color:red">���</span>').'</td>';
		echo '</tr>';
	}

	echo '</table>';
}

// =================================================================================================== //

function installCheckExtensions(){

    global $ext_req;
    global $php_req;
    global $php_req_ver;

	echo '<table align="center">';
		echo '<tr>';
			echo '<th width="300">���������� PHP</th>';
			echo '<th style="text-align:center" width="70">�����������</th>';
		echo '</tr>';

    $all_right = true;

	foreach($ext_req as $name=>$ext){
		$right = true;
		if(!extension_loaded($ext)){
            $right = false;
            $all_right = false;
		}
		echo '<tr>';
			echo '<td class="extension"><a href="http://ru.php.net/manual/ru/book.'.$ext.'.php" title="���������� �������� �� ����� PHP">'.$name.'</td>';
			echo '<td style="text-align:center">'.($right ? '<span style="color:green">��</span>' : '<span style="color:red">���</span>').'</td>';
		echo '</tr>';
	}

	echo '</table>';

    if (!$all_right){
        echo '<p>��� ��������� ������������� ���������� ���������� � ������ �������.</p>';
        echo '<p><a href="http://www.instantcms.ru/forum/0/thread1345-1.html">��� ���������� mbstring �� ������</a> ������� �� ����� ������.</p>';
    }

    $php_ver = getPHPVersion();

    $right = true;
    $php53 = false;

    if ($php_ver['int'] < $php_req_ver) { $right=false; }

    echo '<p><strong>������ PHP:</strong> '.$php_ver['text'].' &mdash '.($right ? '<span style="color:green">�k</span>' : '<span style="color:red">��������� '.$php_req['major'].'.'.$php_req['minor'].'.'.$php_req['release'].' ��� ����</span>').'</p>';

    if (!$right){
        echo '<p>��� ���������� PHP ���������� � ������ �������.</p>';
    }

}

// =================================================================================================== //
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>InstantCMS - ���������</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
	<script src='/includes/jquery/jquery.js' type='text/javascript'></script>
	<script src='/install/js/jquery.wizard.js' type='text/javascript'></script>
	<script src='/install/js/install.js' type='text/javascript'></script>
	<link type='text/css' href='/install/css/styles.css' rel='stylesheet' media='screen' />
</head>

<body>

	<table id="wrapper" align="center">
	<tr><td>

		<h1 id="header">
			��������� InstantCMS <?php echo CORE_VERSION; ?>
		</h1>

		<?php if(!isset($msg)){ ?>

		<form class="wizard" action="#" method="post" >
			<div class="wizard-nav"  align="center">
				<a href="#start">������</a>
				<a href="#php">�������� PHP</a>
				<a href="#folders">�������� ����</a>
				<a href="#install">���������</a>
			</div>
			<!-- ================================================================ -->

			<div id="start" class="wizardpage">
				<h2>����� ����������</h2>
				<img src="/install/images/start.gif" border="0" />
				<p>
					C����� ��������� �������� ������ �� ������������ ����������� ����������� � �������� ���
					����������� �������� ��� ������ ������ � InstantCMS.
				</p>
                <p>������������� InstantCMS ����� ������ � �������� ����� �����.</p>
				<p>
					����� ������� ��������� �������� ����� ���� ������ MySQL �� ����� ��������.
				</p>
				<p>��� ���������� ������� �� ��������� ��������� � �� Windows&trade; ��� ������������, ������� � <a href="http://www.instantcms.ru/wiki/doku.php/%D0%BB%D0%BE%D0%BA%D0%B0%D0%BB%D1%8C%D0%BD%D0%B0%D1%8F_%D1%83%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0_%D0%B4%D0%B5%D0%BD%D0%B2%D0%B5%D1%80" target="_blank">����������</a> �� ����������� �����.</p>

                <p>InstantCMS ���������������� �� �������� GNU/GPL ������ 2. �� ������ ����������� � ��������� ���� �������� ��� ��������� �������.</p>

                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="20"><input type="checkbox" id="license_agree" onClick="checkAgree()"/></td>
                        <td>
                            <label for="license_agree">� �������� � ���������</label>
                            <a target="_blank" href="/license.rus.win.txt">�������� GNU/GPL</a>
                            (<a target="_blank" href="http://www.gnu.org/licenses/gpl-2.0.html">�������� �� ����������</a>)</p></td>
                    </tr>
                </table>

			</div>

			<!-- ================================================================ -->

			<div id="php" class="wizardpage">

                <h2>�������� ���������� PHP</h2>
                <img src="/install/images/extensions.gif" border="0" />

                <p>
					��� ���������� ������ InstantCMS ���������� ����� PHP �� ����� ������� ���� ������������� ����������, ������������� ����.
				</p>

                <?php installCheckExtensions(); ?>

			</div>

			<!-- ================================================================ -->

			<div id="folders" class="wizardpage">
				<h2>�������� ���� �� �����</h2>
				<img src="/install/images/folders.gif" border="0" />

                <p>
					��� ���������� ������ InstantCMS ��������� ���� ����� ������ ���� �������� ��� ������.
					������� ����� �� ����� ����� � ������� FTP-�������, �������� Total Commander ��� FAR.
				</p>

				<?php installCheckFolders(); ?>

				<p>
					���� �� �� ������ ��� ������������ ����� ����� ����� ����������, ����� ������� ����� ��������� ��� ������, ����������
                    � ����������� ��������� ������ ��������.
				</p>

				<p>
					��������� ����� ���������� � �� ��������� �����, �� ����������� ���������������� ������� ��� ���� �� �������������.
				</p>

			</div>

			<!-- ================================================================ -->

		  <div id="install" class="wizardpage">
                <h2>���������</h2>
                <p>��������� ����� � ������� "����������" ��� ���������� ��������.</p>

                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr>
                        <td width="140" valign="top">
                            <img src="/install/images/install.gif" border="0" />
                        </td>
                        <td valign="top">
                            <table width="" border="0" cellpadding="4" cellspacing="0" style="margin-bottom:10px">
                              <tr>
                                <td width="220">�������� �����:</td>
                                <td width="" align="center"><input name="sitename" type="text" class="txt" value="��� ���������� ����"></td>
                              </tr>
                              <tr>
                                <td>����� �������������� �����:</td>
                                <td align="center"><input name="admin_login" type="text" class="txt" value="admin"></td>
                              </tr>
                              <tr>
                                <td>������ �������������� �����:</td>
                                <td align="center"><input name="admin_password" type="password" class="txt"></td>
                              </tr>
                            </table>
                            <table width="" border="0" cellpadding="4" cellspacing="0" style="margin-bottom:0px">
                              <tr>
                                <td width="220">������ MySQL: </td>
                                <td align="center"><input name="db_server" type="text" class="txt" value="localhost"></td>
                              </tr>
                              <tr>
                                <td>���� ������: </td>
                                <td align="center"><input name="db_base" type="text" class="txt"></td>
                              </tr>
                              <tr>
                                <td>������������ ��: </td>
                                <td align="center"><input name="db_user" type="text" class="txt" value="root"></td>
                              </tr>
                              <tr>
                                <td>������ ������������ ��: </td>
                                <td align="center"><input name="db_password" type="password" class="txt"></td>
                              </tr>
                              <tr>
                                <td>������� ������ � ���� ������: </td>
                                <td align="center"><input name="db_prefix" type="text" class="txt" value="cms"></td>
                              </tr>
                              <tr>
                                <td>����-������:</td>
                                <td align="center" valign="top">
                                    <label><input name="demodata" type="radio" value="1" checked /> ��</label>
                                    <label><input name="demodata" type="radio" value="0" /> ���</label>
                                </td>
                              </tr>
                            </table>
                        </td>
                    </tr>
                </table>

				<p style="color:gray">
                    ��� ��������� � ����-������� ���� ������������� ����� ���������� ���������� ������, ����������� � ������� ��������������.
                    ����� ������� ������������ ����� ������ �� ������ ��� ������� ��� �� ������ ����������.
                </p>

                <p>��������� ����� ������ �� ������ �� ���������� �����, � ����������� �� �������� ������ �������.</p>

				</div>
		</form>

		<?php }

			if (isset($msg) && @$msg != ''){
				echo '<div style="margin-left:52px;_margin-left:0px">';
				echo '<h2>���������� ������!</h2>';
				echo '<p style="color:red">'.$msg.'</p>';
				echo '<p><a href="index.php">��������� ���� ������</a></p>';
				echo '</div>';
			}

			if (isset($installed)){
				if($installed){
					echo '<div style="margin-left:52px;_margin-left:0px">';
					echo '<h2>��������� ���������!</h2>';
					echo '<div>';
					echo '<p>������� ����������� � ������ � ������.</p>';
					echo '<div style="background:url(/install/images/cron.png) no-repeat;padding-left:24px;margin-top:30px;margin-bottom:30px;">
                            <div style="margin-bottom:6px;"><strong>�������� ������� ��� CRON</strong></div>
                            <div>
                                �������� ���� <strong>/cron.php</strong> � ���������� ������� CRON � ������ ������ ��������.<br/>
                                �������� ���������� &mdash; 24 ����. ��� �������� ������� ��������� ������������� ��������� ������.
                                ������ �������, ������� ����� �������� � CRON, �������� ���:
                                <pre class="cron">  php -f /������/����/��/�����/cron.php > /dev/null</pre>
                            </div>
                            <div>
                                � ������ ����������� ���������� � ����������� ��������� ��������.
                            </div>
                          </div>';
					echo '<div style="background:url(/install/images/warning.png) no-repeat;padding-left:24px;margin-top:30px;margin-bottom:30px;">
                            <div style="margin-bottom:6px;"><strong>��������!</strong></div>
                            �� �������� �� ���� ���������� ������� �������� "install" � "migrate"<br/>
                            �� ������� ������ �� ����� ������������ � ��� �������!
                          </div>';
					echo '<p style="font-size:18px"><a href="/">������� �� ����</a> | <a href="/admin">������� � ������ ����������</a></p>';
					echo '<p>
                            <a id="tutorial" href="http://www.instantcms.ru/articles/quickstart.html">������� ��� ����������</a>
                            <a id="video" href="http://www.instantcms.ru/video-lessons.html">�����-�����</a>
                          </p>';
					echo '</div>';
					echo '</div>';
				}
			}
		?>

		<div id="footer">
			<a href="http://www.instantcms.ru/" target="_blank"><strong>InstantCMS</strong></a> &copy; 2007-2011
		</div>

	</div>
	</td></tr></table>
</body>
</html>