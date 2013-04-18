#!/usr/bin/php
<?php
	chdir("/opt/WAB2/utils/sms/");
	$strings = file("tosend");
	if (count($strings)>0) {
		$arr = explode(";",array_shift($strings));
		$result = shell_exec("/opt/WAB2/utils/sms/send.py ".$arr[0]." '".$arr[1]."'");
		if (trim(str_replace("\n","",$result))=="Sms sent") {
			file_put_contents("tosend",implode("",$strings));
			file_put_contents("sent",file_get_contents("sent").date("d.m.Y H:i:s").": ".$arr[0]." - ".$arr[1]);
		}
	}
?>