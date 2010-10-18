<?php


function exportNews($title, $text){

	// открываем сокет на хост: локалост и на 80 порт (стандартный веб)
    $fp = fsockopen('http://www.mycms.ru/core/import/import.php', 80, $errno, $errstr, 30); 
    // ѕровер€ем успешность установки соединени€
    if ($fp)  { 
      $headers = "POST / HTTP/1.1\r\n"; // отвечает за тип протокола!
      $headers .= "Host: localhost\r\n"; 
      $headers .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1\r\n"; 
      $headers .= "Referer: http://www.neftegaz.ru/\"\r\n"; 
      $headers .= "Connection: Close\r\n\r\n"; 
	  
	  $headers .= "title=$title&text=$text";
      // ќтправл€ем HTTP-запрос серверу
      fwrite($fp, $headers); 
    }
	
	return true;
}

if (exportNews("ѕерва€ новость", "“екст первой новости!")) echo "sended";


?>