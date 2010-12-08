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
		echo '<div class="update_connect">Сервер обновлений найден.</div>';
		
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
			echo '<div class="update_new">Доступно обновление!</div>';	
			echo '<div class="update_info">';
				echo '<div><strong>Версия вашей системы:</strong> '.CORE_VERSION.' build '.CORE_BUILD.'</div>';
				echo '<div><strong>Версия системы на сервере:</strong> '.$update['curr_version'].' build '.$update['curr_build'].'</div>';
			echo '</div>';
			echo '<div class="update_go"><a href="#" onclick="install(\'index.php?view=update&do=update&from='.CORE_BUILD.'&to='.$update['curr_build'].'\')">Установить обновление</a></div>';
			echo '<div class="update_process" style="display:none">';
				echo '<div class="up1">Идет загрузка обновлений...</div>';
				echo '<div class="up2">Процесс может занять несколько минут. Не закрывайте эту страницу до его завершения!</div>';
			echo '</div>';
		} else {
			//updates not found
			echo '<div class="update_old">Обновление не требуется. Вы имеете последнюю версию.</div>';
			echo '<div class="update_go"><a href="index.php">Назад</a></div>';
		}
		
	} else {
		//server connection error
		echo '<div class="error">Не удалось получить данные с сервера обновлений.</div>';
		if (ini_get('allow_url_fopen')){
			echo '<div class="error">Возможно сервер временно недоступен.</div>';
		} else {
			echo '<div class="error">Параметр "ALLOW_URL_FOPEN" в настройках PHP должен быть включен.</div>';
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
			echo '<div class="update_info" style="font-size:16px"><strong>Обновление #'.$current.'</strong></div>';

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
							echo '<div class="update_log"><strong>Создана папка:</strong> '.$path['url'].'</div>';
						}						 
					}
					//Download file
					if($path['type']=='file'){
						if (!@ftp_get($conn_id, $local, $remote, FTP_BINARY)) {
							echo "<div class=\"update_log_error\">Не удалось загрузить: $remote</div>";
							$errors++;
						} else {
							echo '<div class="update_log"><strong>Загружен файл:</strong> '.$path['url'].'</div>';
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
						echo '<div class="update_log"><strong>База данных обновлена!</strong> Запросов выполнено: '.$q.'</div>';
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
				echo '<div class="update_finish_ok">Обновление прошло успешно! <div>Рекомендуется <a href="/core/auth.php?logout">перезайти</a> в систему для применения всех изменений.</div></div>';
			} else {
				echo '<div class="update_finish_err">Обновление прошло с ошибками! <div>Попробуйте повторить позже или обратиться на официальный сайт.</div></div>';
			}
		} else {
			//ftp connect failed
			echo '<div class="update_finish_err">Не удалось соединиться с сервером по протоколу FTP! <div>Проверьте, имеет ли PHP на вашем хостинге включенный модуль для работы с FTP.</div></div>';
		}
		

}

//=======================================================================================================================================//

function applet_update(){

    $inCore = cmsCore::getInstance();

	$GLOBALS['cp_page_title'] = 'Обновление системы';
 	cpAddPathway('Обновление системы', 'index.php?view=update');	

	if (isset($_REQUEST['do'])) { $do = $_REQUEST['do']; } else { $do = 'check'; }
		
	$toolmenu = array();
	$toolmenu[0]['icon'] = 'save.gif';
	$toolmenu[0]['title'] = 'Закрыть';
	$toolmenu[0]['link'] = '/admin/index.php';

	cpToolMenu($toolmenu);

	if ($do=='check'){
		echo '<h3>Проверка обновлений</h3>';
		cpCheckUpdates();				
	}
	
	if ($do=='update'){
		echo '<h3>Обновление завершено</h3>';	
		if (isset($_REQUEST['from'])) { $from = $_REQUEST['from']; } else { header('location:index.php'); }
		if (isset($_REQUEST['to'])) { $to = $_REQUEST['to']; } else { header('location:index.php'); }
		cpMakeUpdate($from, $to);
	}

}   


?>
