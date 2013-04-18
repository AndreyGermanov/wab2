<?php
$fields["online_monitor"] = array("base" => "booleanField",
								   "params" => array("title" => "Онлайн-мониторинг состояния узлов"
								  ),
								  "file" => __FILE__
);

$fields["online_monitor_update_period"] = array("base" => "integerField",
											    "params" => array("title" => "Периодичность опроса сети"),
												"file" => __FILE__
);

$fields["netCenterAutoRestart"] = array("base" => "booleanField",
										"params" => array("title" => "Автоматически перезапускать службы Сетевого Центра"),
										"file" => __FILE__
);

$fields["manualDNSEntries"] = array("base" => "textField",
									"params" => array("title" => "Дополнительные записи в файле зоны DNS", "control_type" => "editArea", "height" => "100%"),
									"file" => __FILE__
);

$groups["DhcpServerMain"] = array("title" => "Основные",
								   "fields" => array("ldap_base",
								   					  "dns",
								   					  "online_monitor",
								   					  "online_monitor_update_period",
								   					  "netCenterAutoRestart"
								   					),
								  "file" => __FILE__
);

$groups["DhcpServerDNS"] = array("title" => "DNS",
								  "fields" => array("manualDNSEntries"),
								  "file" => __FILE__
);

$groups["DhcpServer"] = array("title" => "DHCP-сервер",
							  "groups" => array("DhcpServerMain","DhcpServerDNS")
);

$models["DhcpServer"] = array("metaTitle" => "DHCP-сервер",
							  "groups" => array("DhcpServerMain","DhcpServer"),
							  "file" => __FILE__,
							  "ldap_base" => "ldap_base",
							  "dns_server" => "dns",
							  "online_monitor" => "online_monitor",
							  "online_monitor_update_period" => "online_monitor_update_period",
							  "netCenterAutoRestart" => "netCenterAutoRestart",
							  "manualDNSEntries" => "manualDNSEntries"
);
?>
