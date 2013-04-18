<?php

$fields["workgroup"] = array("base" => "stringField",
							  "params" => array("title" => "Рабочая группа Windows"),
							 "file" => __FILE__
);

$fields["domain_controller"] = array("base" => "booleanField",
									  "params" => array("title" => "Является контроллером домена"),
									  "file" => __FILE__
);

$fields["smbAutoRestart"] = array("base" => "booleanField",
								  "params" => array("title" => "Автоматически перезапускать службы файлового сервера"),
								  "file" => __FILE__
);

$fields["smbDenyUnknownHosts"] = array("base" => "booleanField",
										"params" => array("title" => "Запретить доступ неизвестных хостов к службам файлового сервера"),
										"file" => __FILE__
);

$groups["FileServer"] = array("title" => "Параметры файлового сервера",
							   "fields" => array("workgroup","domain_controller","smbAutoRestart","smbDenyUnknownHosts"),
							   "file" => __FILE__
);

$models["FileServer"] = array("metaTitle" => "Параметры файлового сервера",
							   "groups" => array("FileServer"),
							   "file" => __FILE__,
							   "workgroup" => "workgroup",
							   "domain_controller" => "domain_controller",
							   "smbAutoRestart" => "smbAutoRestart",
							   "smbDenyUnknownHosts" => "smbDenyUnknownHosts"
);
?>