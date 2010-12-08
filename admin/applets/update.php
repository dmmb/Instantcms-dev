<?php
if(!defined('VALID_CMS_ADMIN')) { die('ACCESS DENIED'); }
/*********************************************************************************************/
//																							 //
//                              InstantCMS v1.7   (c) 2010 FREEWARE                          //
//	 					  http://www.instantcms.ru/, info@instantcms.ru                      //
//                                                                                           //
// 						    written by Vladimir E. Obukhov, 2007-2010                        //
//                                                                                           //
/*********************************************************************************************/

//=======================================================================================================================================//

function cpCheckUpdates(){
	
	clearstatcache();
	
	$update_info_file = @fopen("http://www.instantcms.ru/update/update.info", "r");
	
	if ($update_info_file){
		//server connection ok
		echo '<div class="update_connect">������ ���������� ������.</div>';
		
		//read update information		
		while(!feof($update_info_file)){
			$param = fgets($update_info_file);
			$update[substr($param, 0, strpos($param, '='))] = substr($param, strpos($param, '=')+1);
		}
		
		//force integer build number	
		$update['curr_build'] = (int)$update['curr_build'];

		//check version	
		if ($update['curr_build'] > CORE_BUILD){		
			//updates found
			echo '<div class="update_new">�������� ����������!</div>';	
			echo '<div class="update_info">';
				echo '<div><strong>������ ����� �������:</strong> '.CORE_VERSION.' build '.CORE_BUILD.'</div>';
				echo '<div><strong>������ ������� �� �������:</strong> '.$update['curr_version'].' build '.$update['curr_build'].'</div>';
			echo '</div>';
			echo '<div class="update_go"><a href="#" onclick="install(\'index.php?view=update&do=update&from='.CORE_BUILD.'&to='.$update['curr_build'].'\')">���������� ����������</a></div>';
			echo '<div class="update_process" style="display:none">';
				echo '<div class="up1">���� �������� ����������...</div>';
				echo '<div class="up2">������� ����� ������ ��������� �����. �� ���������� ��� �������� �� ��� ����������!</div>';
			echo '</div>';
		} else {
			//updates not found
			echo '<div class="update_old">���������� �� ���������. �� ������ ��������� ������.</div>';
			echo '<div class="update_go"><a href="index.php">�����</a></div>';
		}
		
	} else {
		//server connection error
		echo '<div class="error">�� ������� �������� ������ � ������� ����������.</div>';
		if (ini_get('allow_url_fopen')){
			echo '<div class="error">�������� ������ �������� ����������.</div>';
		} else {
			echo '<div class="error">�������� "ALLOW_URL_FOPEN" � ���������� PHP ������ ���� �������.</div>';
		}
	}
	
}

//=======================================================================================================================================//

function ftp_is_dir( $conn_id,  $dir )
    {
        if( @ftp_chdir( $conn_id, $dir ) ) {
            ftp_chdir( $conn_id, '/../' );
            return true;
        } else {
            return false;
        }
    }
 function ftp_searchdir($conn_id, $dir)
    {
        if( !@ftp_is_dir( $conn_id, $dir ) ) {
            die( 'No such directory on the ftp-server' );
        }
        $dirlist[0]['url'] = $dir;
		$dirlist[0]['type'] = 'dir';
        $list = ftp_nlist( $conn_id, $dir );
        foreach( $list as $path ) {
            if( $path != $dir.'.' && $path != $dir.'..') {
                if( ftp_is_dir( $conn_id, $path ) ) {
                        $temp = ftp_searchdir( $conn_id, ($path), 1 );
                        $dirlist = array_merge( $dirlist, $temp );
                } else {
                    $dirlist[]['url'] = $path;
					$dirlist[sizeof($dirlist)-1]['type'] = 'file';
                }
            }

        }
        ftp_chdir( $conn_id, '/../' );
       
        return $dirlist;

    }
//=======================================================================================================================================//

function cpMakeUpdate($from_build, $to_build){

		$server = 'ftp.instantcms.ru';
		$localroot = $_SERVER['DOCUMENT_ROOT'];
	
		$error_no_ftp = false;
	
		for($current = $from_build+1; $current<=$to_build; $current++){			
			//update to next build
			echo '<div class="update_info" style="font-size:16px"><strong>���������� #'.$current.'</strong></div>';

			$filesdir = '/domains/instantcms.ru/public_ftp/update/files/build-'.$current;
			
			//connect to ftp
			if($conn_id = @ftp_connect($server)){
				//login
				$login_result = ftp_login($conn_id, 'r2hosting', ',hjytnhfycgjhnth');			
				//get updated files list
				$contents = ftp_searchdir($conn_id, $filesdir);
				//init error counter
				$errors = 0;
				//download all updated files
				foreach($contents as $id=>$path) { 
					$path = str_replace('//', '/', str_replace($filesdir, '', $path)); 
					
					$remote = $filesdir.$path['url'];
					$local = $localroot.$path['url'];
					//Create directory
					if($path['type']=='dir' && trim($path['url'])!=''){ 
						if(!file_exists($local)){ 
							@mkdir($local); 
							echo '<div class="update_log"><strong>������� �����:</strong> '.$path['url'].'</div>';
						}						 
					}
					//Download file
					if($path['type']=='file'){
						if (!@ftp_get($conn_id, $local, $remote, FTP_BINARY)) {
							echo "<div class=\"update_log_error\">�� ������� ���������: $remote</div>";
							$errors++;
						} else {
							echo '<div class="update_log"><strong>�������� ����:</strong> '.$path['url'].'</div>';
						}
					}
				}
				//run sql file
				include($_SERVER['DOCUMENT_ROOT'].'/includes/dbimport.inc.php');
				$sqlfile = $localroot.'/_update.sql';
				if (file_exists($sqlfile)){
					$q = dbRunSQL($sqlfile);
					if ($q){
						@unlink($sqlfile);
						echo '<div class="update_log"><strong>���� ������ ���������!</strong> �������� ���������: '.$q.'</div>';
					} else {
						$errors++;
					}
				}//if sql				
			} else {
				$error_no_ftp = true;
			}//if connect	
				
		}
		
		if(!$error_no_ftp){
			//check errors
			if(!$errors){
				echo '<div class="update_finish_ok">���������� ������ �������! <div>������������� <a href="/core/auth.php?logout">���������</a> � ������� ��� ���������� ���� ���������.</div></div>';
			} else {
				echo '<div class="update_finish_err">���������� ������ � ��������! <div>���������� ��������� ����� ��� ���������� �� ����������� ����.</div></div>';
			}
		} else {
			//ftp connect failed
			echo '<div class="update_finish_err">�� ������� ����������� � �������� �� ��������� FTP! <div>���������, ����� �� PHP �� ����� �������� ���������� ������ ��� ������ � FTP.</div></div>';
		}
		

}

//=======================================================================================================================================//

function applet_update(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = '���������� �������';
 	cpAddPathway('���������� �������', 'index.php?view=update');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'check'; }
		
	$toolmenu = array();
	$toolmenu[0]['icon'] = 'save.gif';
	$toolmenu[0]['title'] = '�������';
	$toolmenu[0]['link'] = '/admin/index.php';

	cpToolMenu($toolmenu);

	if ($do=='check'){
		echo '<h3>�������� ����������</h3>';
		cpCheckUpdates();				
	}
	
	if ($do=='update'){
		echo '<h3>���������� ���������</h3>';	
		if (isset($_REQUEST['from'])) { $from = $_REQUEST['from']; } else { header('location:index.php'); }
		if (isset($_REQUEST['to'])) { $to = $_REQUEST['to']; } else { header('location:index.php'); }
		cpMakeUpdate($from, $to);
	}

}   


?>
