<?php


function exportNews($title, $text){

	// ��������� ����� �� ����: �������� � �� 80 ���� (����������� ���)
    $fp = fsockopen('http://www.mycms.ru/core/import/import.php', 80, $errno, $errstr, 30); 
    // ��������� ���������� ��������� ����������
    if ($fp)  { 
      $headers = "POST / HTTP/1.1\r\n"; // �������� �� ��� ���������!
      $headers .= "Host: localhost\r\n"; 
      $headers .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1\r\n"; 
      $headers .= "Referer: http://www.neftegaz.ru/\"\r\n"; 
      $headers .= "Connection: Close\r\n\r\n"; 
	  
	  $headers .= "title=$title&text=$text";
      // ���������� HTTP-������ �������
      fwrite($fp, $headers); 
    }
	
	return true;
}

if (exportNews("������ �������", "����� ������ �������!")) echo "sended";


?>