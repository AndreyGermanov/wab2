#!/usr/bin/php
<?php
	chdir("/opt/WAB2");
	include ("metadata/monitor/commands.php");
	include ("metadata/monitor/runcommands.php");
	
	$errors = array();
	$retVal = 0;
	foreach ($runMonitorCommands as $key=>$value) {
		$sensorName = $value["commandName"];
		$sensor = $monitorCommands[$sensorName];
		if (isset($value["params"])) {
			$cmd = strtr($sensor["command"],@$value["params"]);			
		}
		else {
			$cmd = $sensor["command"];
		}
		system($cmd,$retVal);
		if (isset($sensor["bad_result"])) {
			if ($retVal==$sensor["bad_result"])
				$errors[$key] = $sensor;
		} else if (isset($sensor["good_result"])) {
			if ($retVal!=$sensor["good_result"])
				$errors[$key] = $sensor;				
		} else {
			if ($retVal)
				$errors[$key] = $sensor;				
		}
		if (isset($errors[$key]) and isset($sensor["restoreCommand"])) {
			if (isset($value["params"])) {
				$restoreCmd = strtr($sensor["restoreCommand"],@$value["params"]);
			}
			else {
				$restoreCmd = $sensor["restoreCommand"];
			}
			system($restoreCmd);	
		}			
	}
	
	if (count($errors)>0) {
		$text = "На сервере '".$monitorServer."' сработали следующие датчики: <br/>";
		$text .= "<ul>";
		foreach ($errors as $key=>$value) {
			if (isset($runMonitorCommands[$key]["params"]))
				$title = strtr($value["title"],$runMonitorCommands[$key]["params"]);
			else
				$title= $value["title"];
			$text .= "<li>".$title."</li>"; 				
		}
		$text .= "</ul>";
		$headers = "From: ".$monitorEmail."\n";	
		$headers.= "MIME-Version: 1.0\n";
		$headers.= "Content-type: text/html; charset=utf-8\n";
		$to = $monitorEmail;
		$from = $monitorEmail;
		$subject = "СБОЙ СЕРВЕРА!";
		mail($to,$subject,$text,$headers);	
	}
?>