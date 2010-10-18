<?php
	session_start();
	define("VALID_CMS", 1);	
	require("../includes/config.inc.php");
	require("../includes/database.inc.php");


	$location = $_SERVER["HTTP_REFERER"]; // откуда пришел юзер
	$out=$_POST['title'].' ---- '.$_POST['text']; // строка GET данных
	$headers = apache_request_headers(); // хедеры, переданные апачу
	foreach ($headers as $header => $value) {
	   $out.= "$header: $value <br />"; // складываем все это все в читабельный вид
	}
	$fo=fopen("log.html","a"); // открываем лог
	$entry="<pre>Адрес запроса: $location\r\n\r\n $out\r\n"; // готовим данные
	fputs($fo,$entry); // записываем их
	fclose($fo); // закрываем лог

?>