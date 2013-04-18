<?php
$fields["hostname"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя хоста"
	),
	"file" => __FILE__
);

$fields["password"] = 
array
(
	"base" => "passwordField",
	"params" => array
	(
		"title" => "Пароль пользователя root"
	),
	"file" => __FILE__
);

$fields["ipaddr"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "IP-адрес"
	),
	"file" => __FILE__
);

$fields["netmask"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Маска сети"
	),
	"file" => __FILE__
);

$fields["gateway"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Шлюз по умолчанию"
	),
	"file" => __FILE__
);

$fields["dns"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Адрес DNS-сервера"
	),
	"file" => __FILE__
);

$fields["dns1"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Адрес DNS-сервера 1"
	),
	"file" => __FILE__
);

$fields["dns2"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Адрес DNS-сервера 2"
	),
	"file" => __FILE__
);

$fields["ldap_host"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя сервера базы данных"
	),
	"file" => __FILE__
);

$fields["ldap_port"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Порт базы данных",
		"type" => "list,389~636|LDAP~LDAPS"
	),
	"file" => __FILE__
);

$fields["ldap_user"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя пользователя базы данных"
	),
	"file" => __FILE__
);

$fields["ldap_password"] = 
array
(
	"base" => "passwordField",
	"params" => array
	(
		"title" => "Пароль пользователя базы данных"
	),
	"file" => __FILE__
);

$fields["ldap_base"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Корневой узел дерева базы данных"
	),
	"file" => __FILE__
);

$fields["is_ldap_localhost"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "База расположена локально"
	),
	"file" => __FILE__
);

$fields["dns3"] = 
array
(
	"type" => "",
	"base" => "stringField",
	"file" => __FILE__,
	"params" => array
	(
		"title" => "Адрес DNS-сервера 3"
	)
);

$groups["SystemSettings"] = 
array
(
	"title" => "Основные параметры сервера",
	"groups" => array
	(
		"SystemSettingsMain",
		"SystemSettingsNetwork",
		"SystemSettingsLDAP"
	),
	"file" => __FILE__,
	"fields" => array
	(
		"" => "dns3"
	)
);

$groups["SystemSettingsMain"] = 
array
(
	"title" => "Основные параметры",
	"fields" => array
	(
		"hostname",
		"password"
	),
	"file" => __FILE__
);

$groups["SystemSettingsNetwork"] = 
array
(
	"title" => "Сеть",
	"fields" => array
	(
		"ipaddr",
		"netmask",
		"gateway",
		"dns1",
		"dns2",
		"dns3"
	),
	"file" => __FILE__
);

$groups["SystemSettingsLDAP"] = 
array
(
	"title" => "База данных",
	"fields" => array
	(
		"is_ldap_localhost",
		"ldap_host",
		"ldap_port",
		"ldap_user",
		"ldap_password",
		"ldap_base"
	),
	"file" => __FILE__
);

$models["SystemSettings"] = 
array
(
	"metaTitle" => "Основные параметры сервера",
	"groups" => array
	(
		"SystemSettingsMain",
		"SystemSettingsNetwork",
		"SystemSettingsLDAP"
	),
	"file" => __FILE__,
	"hostname" => "hostname",
	"password" => "password",
	"ipaddr" => "ipaddr",
	"netmask" => "netmask",
	"gateway" => "gateway",
	"dns" => "dns",
	"dns1" => "dns1",
	"dns2" => "dns2",
	"dns3" => "dns3",
	"is_ldap_localhost" => "is_ldap_localhost",
	"ldap_host" => "ldap_host",
	"ldap_port" => "ldap_port",
	"ldap_user" => "ldap_user",
	"ldap_password" => "ldap_password",
	"ldap_base" => "ldap_base"
);
?>