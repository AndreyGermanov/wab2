<?php
	
	$monitorCommands = array(
			
			"lighttpd" => array (
				"title" => "Web-сервер Lighttpd",
				"bad_result" => "4",
				"command" => "wget localhost",
				"restoreCommand" => "/etc/init.d/mysql restart;/etc/init.d/lighttpd restart"
			),
			
			"mysql" => array (
				"title" => "Сервер баз данных MySQL",
				"command" => "/etc/init.d/mysql status",
				"restoreCommand" => "/etc/init.d/mysql restart"
			),
			
			"virtualbox" => array (
				"title" => "Виртуальная машина {vmname}",
				"command" => 'VBoxManage list runningvms | grep "{vmname}"',
				"restoreCommand" => "VBoxManage startvm '{vmname}'"
			)
	)
?>