<?php
	session_start();
	define("VALID_CMS", 1);	
	require("../includes/config.inc.php");
	require("../includes/database.inc.php");


	$location = $_SERVER["HTTP_REFERER"]; // ������ ������ ����
	$out=$_POST['title'].' ---- '.$_POST['text']; // ������ GET ������
	$headers = apache_request_headers(); // ������, ���������� �����
	foreach ($headers as $header => $value) {
	   $out.= "$header: $value <br />"; // ���������� ��� ��� ��� � ����������� ���
	}
	$fo=fopen("log.html","a"); // ��������� ���
	$entry="<pre>����� �������: $location\r\n\r\n $out\r\n"; // ������� ������
	fputs($fo,$entry); // ���������� ��
	fclose($fo); // ��������� ���

?>