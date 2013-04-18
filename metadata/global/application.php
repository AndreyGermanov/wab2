<?php
$fields["defaultModule"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "list,|",
		"title" => "Модуль по умолчанию"
	),
	"file" => __FILE__
);

$fields["networkSettingsStyle"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "list,RedHat~Debian|RedHat~Debian",
		"title" => "Тип настроек сети"
	),
	"file" => __FILE__
);

$fields["redHatNetworkSettingsFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл настройки сети RedHat"
	),
	"file" => __FILE__
);

$fields["debianNetworkSettingsFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл настройки сети Debian"
	),
	"file" => __FILE__
);

$fields["debianNetworkSettingsTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон файла настроек сети Debian"
	),
	"file" => __FILE__
);

$fields["debianNetworkRestartCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда перезапуска сети Debian"
	),
	"file" => __FILE__
);

$fields["redHatNetworkRestartCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда перезапуска сети RedHat"
	),
	"file" => __FILE__
);

$fields["rootPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Корневая папка панели управления"
	),
	"file" => __FILE__
);

$fields["rootPasswordFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с паролем пользователя root"
	),
	"file" => __FILE__
);

$fields["sudoPasswordFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с паролем пользователя www-data"
	),
	"file" => __FILE__
);

$fields["shadowFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл паролей пользователей"
	),
	"file" => __FILE__
);

$fields["hostnameFile"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "file",
		"title" => "Файл с именем хоста",
		"width" => "100%",
		"control_type" => "fileManager"
	),
	"file" => __FILE__
);

$fields["hostnameCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда установки имени хоста"
	),
	"file" => __FILE__
);

$fields["makeDirCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда создания каталога"
	),
	"file" => __FILE__
);

$fields["moveDirCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда перемещения файла/каталога"
	),
	"file" => __FILE__
);

$fields["deleteCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда удаления файла"
	),
	"file" => __FILE__
);

$fields["chownCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда изменения владельца файла"
	),
	"file" => __FILE__
);

$fields["copyCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда копирования"
	),
	"file" => __FILE__
);

$fields["touchCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда изменения времени доступа к файлу"
	),
	"file" => __FILE__
);

$fields["tarCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда запуска архиватора TAR"
	),
	"file" => __FILE__
);

$fields["sshRemoteCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда запуска команд на удаленном узле по SSH"
	),
	"file" => __FILE__
);

$fields["skinPath"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "list,|",
		"title" => "Тема оформления"
	),
	"file" => __FILE__
);

$fields["mD5SumCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда получения контрольной суммы файла"
	),
	"file" => __FILE__
);

$fields["getIpCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда получения текущего IP-адреса"
	),
	"file" => __FILE__
);

$fields["hostsFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл /etc/hosts"
	),
	"file" => __FILE__
);

$fields["nmapCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда сканирования хостов"
	),
	"file" => __FILE__
);

$fields["pingIpTestCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда проверки доступности IP-адреса"
	),
	"file" => __FILE__
);

$fields["pingPortTestCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда проверки доступности порта"
	),
	"file" => __FILE__
);

$fields["sshFsCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда монтирования файловой системы по SSH"
	),
	"file" => __FILE__
);

$fields["getInodeCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда получения идентификатора файла"
	),
	"file" => __FILE__
);

$fields["apacheRestartCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда перезапуска Web-сервера Apache"
	),
	"file" => __FILE__
);

$fields["apacheServerUser"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Пользователь, от имени которого работает панель управления"
	),
	"file" => __FILE__
);

$fields["sshdConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл сервера SSH"
	),
	"file" => __FILE__
);

$fields["sshdRestartCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда перезапуска сервера SSH"
	),
	"file" => __FILE__
);

$fields["getActiveUnixUsersCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда получения списка активных пользователей UNIX"
	),
	"file" => __FILE__
);

$fields["findCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда поиска по файловой системе"
	),
	"file" => __FILE__
);

$fields["getUserHomeDirCommand"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Команда получения домашней папки пользователя UNIX"
	),
	"file" => __FILE__
);

$fields["sudoersFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл sudoers"
	),
	"file" => __FILE__
);

$fields["language"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "list,|",
		"title" => "Язык интерфейса"
	),
	"file" => __FILE__
);

$fields["apacheUsersTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл пользователей Apache"
	),
	"file" => __FILE__
);

$fields["webServerAuthConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл авторизации на Web-сервере"
	),
	"file" => __FILE__
);

$fields["webServerAuthTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла авторизации на Web-сервере"
	),
	"file" => __FILE__
);

$fields["apacheAdminsBase"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "list,file~ldap|Файл пользователей~Каталог LDAP",
		"title" => "Источник пользователей панели управления"
	),
	"file" => __FILE__
);

$fields["apacheAdminsLdapHost"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Хост каталога LDAP с пользователями системы управления"
	),
	"file" => __FILE__
);

$fields["apacheAdminsLdapPort"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Порт каталога LDAP с пользователями системы управления"
	),
	"file" => __FILE__
);

$fields["apacheAdminsLdapUser"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Идентификатор пользователя каталога LDAP с пользователями системы управления"
	),
	"file" => __FILE__
);

$fields["apacheAdminsLdapPassword"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"password" => "true",
		"title" => "Пароль пользователя каталога LDAP с пользователями системы управления"
	),
	"file" => __FILE__
);

$fields["apacheAdminsLdapBase"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "База поиска каталога LDAP с пользователями системы управления"
	),
	"file" => __FILE__
);

$fields["apacheAdminsLdapFilter"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Фильтр поиска каталога LDAP с пользователями системы управления"
	),
	"file" => __FILE__
);

$groups["appconfig"] = 
array
(
	"title" => "Глобальные настройки панели управления",
	"fields" => array
	(

	),
	"groups" => array
	(
		"appconfigMain",
		"appconfigFiles",
		"appconfigCommands"
	),
	"file" => __FILE__
);

$groups["appconfigFiles"] = 
array
(
	"title" => "Конфигурационные файлы",
	"fields" => array
	(
		"redHatNetworkSettingsFile",
		"debianNetworkSettingsFile",
		"debianNetworkSettingsTemplateFile",
		"rootPasswordFile",
		"sudoPasswordFile",
		"shadowFile",
		"hostnameFile",
		"hostsFile",
		"sshdConfigFile",
		"sudoersFile",
		"apacheUsersTable",
		"rcLocalFile",
		"crontabFile",
		"webServerAuthConfigFile",
		"webServerAuthTemplateConfigFile"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["appconfigCommands"] = 
array
(
	"title" => "Команды",
	"fields" => array
	(
		"debianNetworkRestartCommand",
		"redHatNetworkRestartCommand",
		"hostnameCommand",
		"makeDirCommand",
		"moveDirCommand",
		"deleteCommand",
		"chownCommand",
		"copyCommand",
		"touchCommand",
		"tarCommand",
		"sshRemoteCommand",
		"mD5SumCommand",
		"getIpCommand",
		"nmapCommand",
		"pingIpTestCommand",
		"pingPortTestCommand",
		"sshFsCommand",
		"getInodeCommand",
		"apacheRestartCommand",
		"sshdRestartCommand",
		"getActiveUnixUsersCommand",
		"findCommand",
		"getUserHomeDirCommand"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["appconfigMain"] = 
array
(
	"title" => "Настройки",
	"fields" => array
	(
		"rootPath",
		"defaultModule",
		"networkSettingsStyle",
		"apacheServerUser",
		"skinPath",
		"language",
		"apacheAdminsBase",
		"apacheAdminsLdapHost",
		"apacheAdminsLdapPort",
		"apacheAdminsLdapUser",
		"apacheAdminsLdapPassword",
		"apacheAdminsLdapBase",
		"apacheAdminsLdapFilter"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$appconfig = 
array
(
	"defaultModule" => "MystixController",
	"networkSettingsStyle" => "Debian",
	"redHatNetworkSettingsFile" => "/etc/sysconfig/network-scripts/ifcfg-eth0",
	"debianNetworkSettingsFile" => "/etc/network/interfaces",
	"debianNetworkSettingsTemplateFile" => "templates/network/interfaces",
	"debianNetworkRestartCommand" => "/etc/init.d/networking restart",
	"redHatNetworkRestartCommand" => "ifdown eth0;ifup eth0",
	"rootPath" => "/opt/WAB2",
	"rootPasswordFile" => "/root/pw",
	"sudoPasswordFile" => "/etc/WAB2/config/sudopw",
	"shadowFile" => "/etc/shadow",
	"hostnameFile" => "/etc/hostname",
	"hostnameCommand" => "hostname",
	"makeDirCommand" => "mkdir",
	"moveDirCommand" => "mv",
	"deleteCommand" => "rm",
	"chownCommand" => "chown",
	"copyCommand" => "cp",
	"touchCommand" => "touch",
	"tarCommand" => "tar",
	"lnCommand" => "ln",
	"sshRemoteCommand" => "ssh {server_string} {command}",
	"skinPath" => "skins/default/",
	"mD5SumCommand" => "md5sum {path} | cut -d ' ' -f1",
	"getIpCommand" => "ifconfig eth0 | grep 'inet addr:' | cut -d ':' -f2 | cut -d ' ' -f1",
	"hostsFile" => "/etc/hosts",
	"nmapCommand" => "nmap -n",
	"pingIpTestCommand" => "ping -c 1 -w 1 {address} >/dev/null 2>/dev/null; echo \$?",
	"pingPortTestCommand" => "echo '' | nc -w 1 {address} {port} >/dev/null 2>/dev/null;echo \$?",
	"sshFsCommand" => "sshfs",
	"getInodeCommand" => "ls -id '{path}' | cut -d ' ' -f1",
	"apacheRestartCommand" => "/etc/init.d/lighttpd restart",
	"apacheServerUser" => "root",
	"sshdConfigFile" => "/etc/ssh/sshd_config",
	"sshdRestartCommand" => "/etc/init.d/ssh restart",
	"getActiveUnixUsersCommand" => "cat /etc/shadow | grep -v '\:\*\:' | grep -v '\:\!\:' | cut -d ':' -f1",
	"findCommand" => "find",
	"getUserHomeDirCommand" => "cat /etc/passwd | grep {user} | cut -d ':' -f6",
	"sudoersFile" => "/etc/sudoers",
	"crontabFile" => "/etc/crontab",
	"rcLocalFile" => "/etc/rc.local",
	"language" => "rus",
	"apacheUsersTable" => "/etc/WAB2/config/admins",
	"apacheAdminsBase" => "file",
	"apacheAdminsLdapHost" => "localhost",
	"apacheAdminsLdapPort" => "389",
	"apacheAdminsLdapUser" => "cn=admin,dc=mydomain,dc=ru",
	"apacheAdminsLdapPassword" => "111111",
	"apacheAdminsLdapBase" => "ou=users,dc=mydomain,dc=ru",
	"apacheAdminsLdapFilter" => "(wabUser=1)",
	"collection" => "appconfig",
	"name" => "appconfig",
	"wabBackgroundColor" => "DDDDDD",
	"webServerAuthConfigFile" => "/etc/lighttpd/auth.conf",
	"webServerAuthTemplateConfigFile" => "templates/controller/auth.conf",
	"variablesPath" => "/var/WAB2/",
	"file" => __FILE__
);

$models["appconfig"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"appconfigMain",
		"appconfigFiles",
		"appconfigCommands"
	),
	"defaultModule" => "defaultModule",
	"networkSettingsStyle" => "networkSettingsStyle",
	"redHatNetworkSettingsFile" => "redHatNetworkSettingsFile",
	"debianNetworkSettingsFile" => "debianNetworkSettingsFile",
	"debianNetworkSettingsTemplateFile" => "debianNetworkSettingsTemplateFile",
	"debianNetworkRestartCommand" => "debianNetworkRestartCommand",
	"redHatNetworkRestartCommand" => "redHatNetworkRestartCommand",
	"rootPath" => "rootPath",
	"rootPasswordFile" => "rootPasswordFile",
	"sudoPasswordFile" => "sudoPasswordFile",
	"shadowFile" => "shadowFile",
	"hostnameFile" => "hostnameFile",
	"hostnameCommand" => "hostnameCommand",
	"makeDirCommand" => "makeDirCommand",
	"moveDirCommand" => "moveDirCommand",
	"deleteCommand" => "deleteCommand",
	"chownCommand" => "chownCommand",
	"copyCommand" => "copyCommand",
	"touchCommand" => "touchCommand",
	"tarCommand" => "tarCommand",
	"sshRemoteCommand" => "sshRemoteCommand",
	"skinPath" => "skinPath",
	"mD5SumCommand" => "mD5SumCommand",
	"getIpCommand" => "getIpCommand",
	"hostsFile" => "hostsFile",
	"nmapCommand" => "nmapCommand",
	"pingIpTestCommand" => "pingIpTestCommand",
	"pingPortTestCommand" => "pingPortTestCommand",
	"sshFsCommand" => "sshFsCommand",
	"getInodeCommand" => "getInodeCommand",
	"apacheRestartCommand" => "apacheRestartCommand",
	"apacheServerUser" => "apacheServerUser",
	"sshdConfigFile" => "sshdConfigFile",
	"sshdRestartCommand" => "sshdRestartCommand",
	"getActiveUnixUsersCommand" => "getActiveUnixUsersCommand",
	"findCommand" => "findCommand",
	"getUserHomeDirCommand" => "getUserHomeDirCommand",
	"sudoersFile" => "sudoersFile",
	"language" => "language",
	"apacheUsersTable" => "apacheUsersTable",
	"apacheAdminsBase" => "apacheAdminsBase",
	"apacheAdminsLdapHost" => "apacheAdminsLdapHost",
	"apacheAdminsLdapPort" => "apacheAdminsLdapPort",
	"apacheAdminsLdapUser" => "apacheAdminsLdapUser",
	"apacheAdminsLdapPassword" => "apacheAdminsLdapPassword",
	"apacheAdminsLdapBase" => "apacheAdminsLdapBase",
	"apacheAdminsLdapFilter" => "apacheAdminsLdapFilter",
	"rcLocalFile" => "rcLocalFile",
	"crontabFile" => "crontabFile",
	"wabBackgroundColor" => "wabBackgroundColor",
	"webServerAuthConfigFile" => "webServerAuthConfigFile",
	"webServerAuthTemplateConfigFile" => "webServerAuthTemplateConfigFile",
	"metaTitle" => "Основные параметры панели управления"
);
?>