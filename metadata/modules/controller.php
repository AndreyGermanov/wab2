<?php
$fields["netCenterAutoRestart"] = 
array
(
	"type" => "",
	"base" => "booleanField",
	"file" => __FILE__,
	"params" => array
	(
		"title" => "Автоматически перезапускать службы Сетевого центра"
	)
);

$fields["smbAutoRestart"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Автоматически перезапускать службы файлового сервера"
	),
	"file" => __FILE__
);

$fields["smbDenyUnknownHosts"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Запретить доступ неизвестных хостов к службам файлового сервера"
	),
	"file" => __FILE__
);

$fields["hostsModifyCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда модификации файла /etc/hosts"
	),
	"file" => __FILE__
);

$fields["shellInABoxCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда запуска демона ShellInABox"
	),
	"file" => __FILE__
);

$fields["remoteVNCConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл настройки удаленного подключения по VNC"
	),
	"file" => __FILE__
);

$fields["dhcpConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл сервера DHCP"
	),
	"file" => __FILE__
);

$fields["dhcpConfigTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла сервера DHCP"
	),
	"file" => __FILE__
);

$fields["dhcpRestartCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска сервера DHCP"
	),
	"file" => __FILE__
);

$fields["dhcpGetHostsTreeCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для получения дерева хостов DHCP из каталога LDAP"
	),
	"file" => __FILE__
);

$fields["dhcpGetDeleteHostsCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для получения дерева хостов DHCP из каталога LDAP для удаления"
	),
	"file" => __FILE__
);

$fields["dhcpAddHostsTreeCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для добавления дерева хостов в LDAP-каталог из файла"
	),
	"file" => __FILE__
);

$fields["dhcpDeleteHostsTreeCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для удаления списка хостов из LDAP-каталога"
	),
	"file" => __FILE__
);

$fields["scanNetbiosNameCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда получения IP-адреса по Netbios-имени"
	),
	"file" => __FILE__
);

$fields["slapdConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл демона LDAP"
	),
	"file" => __FILE__
);

$fields["slapdTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла демона LDAP"
	),
	"file" => __FILE__
);

$fields["ldapSearchCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда поиска по каталогу LDAP"
	),
	"file" => __FILE__
);

$fields["ldapDeleteCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда удаления из каталога LDAP"
	),
	"file" => __FILE__
);

$fields["ldapAddCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда добавления в каталог LDAP"
	),
	"file" => __FILE__
);

$fields["slapdRestartCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска сервера LDAP"
	),
	"file" => __FILE__
);

$fields["ldapClientConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл настройки клиента LDAP для PAM"
	),
	"file" => __FILE__
);

$fields["ldapClientConfigFile2"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл настройки клиента LDAP для NSS"
	),
	"file" => __FILE__
);

$fields["ldapscriptsConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл Ldapscripts"
	),
	"file" => __FILE__
);

$fields["ldapscriptsPasswordFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с паролем администратора LDAP-сервера для Ldapscripts"
	),
	"file" => __FILE__
);

$fields["ldapClientTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла настройки клиента LDAP для PAM и NSS"
	),
	"file" => __FILE__
);

$fields["ldapscriptsTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла настройки для Ldapscripts"
	),
	"file" => __FILE__
);

$fields["openLdapClientConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл системного клиента LDAP"
	),
	"file" => __FILE__
);

$fields["openLdapClientTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла системного клиента LDAP"
	),
	"file" => __FILE__
);

$fields["idealXConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл SMBTools"
	),
	"file" => __FILE__
);

$fields["idealXTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла SMBTools"
	),
	"file" => __FILE__
);

$fields["idealXBindConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл с паролем администратора каталога LDAP для SMBTools"
	),
	"file" => __FILE__
);

$fields["idealXTemplateBindConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла с паролем администратора каталога LDAP для SMBTools"
	),
	"file" => __FILE__
);

$fields["ldapSchemaFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Схема LVA для сервера LDAP"
	),
	"file" => __FILE__
);

$fields["defaultLdapHost"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Хост с каталогом LDAP по умолчанию"
	),
	"file" => __FILE__
);

$fields["defaultLdapPort"] = 
array
(
	"type" => "integerField",
	"params" => array
	(
		"title" => "Протокол LDAP по умолчанию",
		"type" => "list,389~636|LDAP~LDAPS"
	),
	"file" => __FILE__
);

$fields["defaultLdapUser"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Администратор каталога LDAP по умолчанию"
	),
	"file" => __FILE__
);

$fields["defaultLdapPassword"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Пароль пользователя LDAP по умолчанию",
		"password" => "true"
	),
	"file" => __FILE__
);

$fields["defaultLdapBase"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Корень каталога LDAP по умолчанию"
	),
	"file" => __FILE__
);

$fields["bindRestartCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска DNS-сервера"
	),
	"file" => __FILE__
);

$fields["bindReloadCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезагрузки конфигурационного файла DNS-сервера"
	),
	"file" => __FILE__
);

$fields["bindZonesFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл списка зон DNS-сервера"
	),
	"file" => __FILE__
);

$fields["bindZonesTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла списка зон DNS-сервера"
	),
	"file" => __FILE__
);

$fields["bindZoneFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл зоны DNS-сервера"
	),
	"file" => __FILE__
);

$fields["bindZoneTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла зоны DNS-сервера"
	),
	"file" => __FILE__
);

$fields["bindCustomZoneRecordsFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл пользовательских записей зоны DNS-сервера"
	),
	"file" => __FILE__
);

$fields["smbConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл Samba"
	),
	"file" => __FILE__
);

$fields["smbHostsPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к конфигурационный файлам хостов"
	),
	"file" => __FILE__
);

$fields["smbDefaultSharesFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл прав доступа к общим папкам по умолчанию"
	),
	"file" => __FILE__
);

$fields["smbCustomHostsPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к пользовательским конфигурационным файлам хостов"
	),
	"file" => __FILE__
);

$fields["smbTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла Samba"
	),
	"file" => __FILE__
);

$fields["smbShareTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл шаблона описания общей папки Samba"
	),
	"file" => __FILE__
);

$fields["smbRestartCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска Samba"
	),
	"file" => __FILE__
);

$fields["smbReloadCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезагрузки конфигурационного файла Samba"
	),
	"file" => __FILE__
);

$fields["smbResetAdminPasswordCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда смены пароля администратора Samba"
	),
	"file" => __FILE__
);

$fields["smbListUsersCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда вывода списка пользователей Samba"
	),
	"file" => __FILE__
);

$fields["smbAddUserCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда добавление нового пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbRemoveUserCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда удаления пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbAddUserToGroupCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда добавления пользователя в группу Samba"
	),
	"file" => __FILE__
);

$fields["smbRemoveUserFromGroupCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда удаления пользователя из группы Samba"
	),
	"file" => __FILE__
);

$fields["smbListGroupsCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда вывода списка групп Samba"
	),
	"file" => __FILE__
);

$fields["smbAddGroupCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда добавления группы Samba"
	),
	"file" => __FILE__
);

$fields["smbRemoveGroupCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда удаления группы Samba"
	),
	"file" => __FILE__
);

$fields["smbGetGroupUsersCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда, отображающая список пользователей группы Samba"
	),
	"file" => __FILE__
);

$fields["smbGetUserGroupsCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда, отображающая список групп Samba, в которые входит пользователь"
	),
	"file" => __FILE__
);

$fields["smbChangeUserPasswordCommand"] = 
array
(
	"base" => "textField",
	"params" => array
	(
		"title" => "Команда изменения пароля пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbChangeUserFullNameCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда изменения полного имени пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbGetLocalSidCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда получения локального идентификатора рабочей станции"
	),
	"file" => __FILE__
);

$fields["smbChangeUserOptionsCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда изменения параметров пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbChangeGroupOptionsCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда изменения параметров группы Samba"
	),
	"file" => __FILE__
);

$fields["smbGetGidOfGroupCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда получения идентификатора группы"
	),
	"file" => __FILE__
);

$fields["smbGetUserInfoCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для получения информации о пользователе Samba"
	),
	"file" => __FILE__
);

$fields["smbGetGroupInfoCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для получения информации о группе Samba"
	),
	"file" => __FILE__
);

$fields["smbUserMapFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл псевдонимов пользователей Samba"
	),
	"file" => __FILE__
);

$fields["smbGetACLCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для получения списка прав доступа пользователя к общей папке"
	),
	"file" => __FILE__
);

$fields["smbSetACLCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для установки прав доступа пользователя к общей папке"
	),
	"file" => __FILE__
);

$fields["smbRemoveACLCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для удаления прав доступа пользователя к общей папке"
	),
	"file" => __FILE__
);

$fields["smbAddPrivilegesCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда установки привилегий пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbRemovePrivilegesCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда удаления привилегий пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbGetPrivilegesCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда получения списка привилегий пользователя Samba"
	),
	"file" => __FILE__
);

$fields["smbGetlocalsidCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда получения локального идентификатора рабочей станции Samba"
	),
	"file" => __FILE__
);

$fields["smbSetLocalsidCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда установки локального идентификатора рабочей станции Samba"
	),
	"file" => __FILE__
);

$fields["smbGetUserACLCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда получения списка прав доступа пользователя к списку общих папок"
	),
	"file" => __FILE__
);

$fields["smbGetShareACLCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда получения списка прав доступа пользователей к общей папке"
	),
	"file" => __FILE__
);

$fields["nfsRestartCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска NFS-сервера"
	),
	"file" => __FILE__
);

$fields["nfsConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл NFS"
	),
	"file" => __FILE__
);

$fields["nfsDefaultOptions"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Параметры по умолчанию для общих папок NFS"
	),
	"file" => __FILE__
);

$fields["smbSharesConfigPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к каталогу с конфигурационными файлами общих папок Samba"
	),
	"file" => __FILE__
);

$fields["smbShareVFSTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла общей папки Samba"
	),
	"file" => __FILE__
);

$fields["smbUsersLdapBase"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Корневая ветвь LDAP по умолчанию для пользователей"
	),
	"file" => __FILE__
);

$fields["smbGroupsLdapBase"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Корневая ветвь LDAP по умолчанию для групп"
	),
	"file" => __FILE__
);

$fields["smbMachinesLdapBase"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Корневая ветвь LDAP по умолчанию для компьютеров"
	),
	"file" => __FILE__
);

$fields["smbAuditLogFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл журнала доступа Samba"
	),
	"file" => __FILE__
);

$fields["smbAuditPeriod"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Периодичность хранения записей в журнале доступа Samba"
	),
	"file" => __FILE__
);

$fields["smbAuditDBHost"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Хост, на котором находится база данных журнала доступа Samba"
	),
	"file" => __FILE__
);

$fields["smbAuditDBPort"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Порт, на котором находится база данных журнала доступа Samba"
	),
	"file" => __FILE__
);

$fields["smbAuditDBName"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя базы данных, в которой находится журнал доступа Samba"
	),
	"file" => __FILE__
);

$fields["smbAuditDBUser"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя пользователя базы данных, в которой находится журнал доступа Samba"
	),
	"file" => __FILE__
);

$fields["smbAuditDBPassword"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Пароль пользователя базы данных, в которой находится журнал доступа Samba",
		"password" => "true"
	),
	"file" => __FILE__
);

$fields["enableShadowCopy"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Включить теневое копирование"
	),
	"file" => __FILE__
);

$fields["snapshotSize"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Размер снимка"
	),
	"file" => __FILE__
);

$fields["snapshotsCount"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Количество снимков"
	),
	"file" => __FILE__
);

$fields["snapshotsFolder"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к каталогу, в котором хранятся снимки"
	),
	"file" => __FILE__
);

$fields["snapshotLibTemplate"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон библиотеки функций Perl для работы со снимками"
	),
	"file" => __FILE__
);

$fields["snapshotRotatorTemplate"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон скрипта ротации снимков на Bash"
	),
	"file" => __FILE__
);

$fields["shadowCopyEnginePath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к скриптам механизма теневого копирования"
	),
	"file" => __FILE__
);

$fields["getAuditDataScript"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Скрипт сбора данных для журнала доступа Samba"
	),
	"file" => __FILE__
);

$fields["getAuditDataScriptTemplate"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон скрипта сбора данных для журнала доступа Samba"
	),
	"file" => __FILE__
);

$fields["eraseTrashScript"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Скрипт очистки корзины Samba"
	),
	"file" => __FILE__
);

$fields["eraseTrashScriptTemplate"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон скрипта очистки корзины Samba"
	),
	"file" => __FILE__
);

$fields["shadowCopyPeriodDays"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Периодичность ротации сником (дни)"
	),
	"file" => __FILE__
);

$fields["shadowCopyPeriodHours"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Периодичность ротации сником (часы)"
	),
	"file" => __FILE__
);

$fields["shadowCopyPeriodMinutes"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Периодичность ротации сником (минуты)"
	),
	"file" => __FILE__
);

$fields["snapshotCopyLinksTemplate"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон скрипта создания символических ссылок на папки со снимками"
	),
	"file" => __FILE__
);

$fields["smbFirewallFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с правилами межсетевого экрана для Samba"
	),
	"file" => __FILE__
);

$fields["lvCreateCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для создания логического тома LVM"
	),
	"file" => __FILE__
);

$fields["lvRemoveCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда удаления логического тома LVM"
	),
	"file" => __FILE__
);

$fields["lvResizeCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда изменения размера логического тома LVM"
	),
	"file" => __FILE__
);

$fields["lvDisplayCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда отображения информации о логических томах LVM"
	),
	"file" => __FILE__
);

$fields["vgDisplayCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда отображения информации о группах томов LVM"
	),
	"file" => __FILE__
);

$fields["shadowCopyVgName"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя группы томов LVM раздела данных"
	),
	"file" => __FILE__
);

$fields["shadowCopyLvName"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя логического тома LVM раздела данных"
	),
	"file" => __FILE__
);

$fields["shadowCopyResizeSize"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Размер приращения размера снимка при достижении критического размера"
	),
	"file" => __FILE__
);

$fields["shadowCopyResizeLimit"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Критический размер снимка, при достижении которого происходит приращение"
	),
	"file" => __FILE__
);

$fields["enableAutoSnapshotsRotation"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Включить ротацию снимков"
	),
	"file" => __FILE__
);

$fields["expiredSnapshotsBackupFolder"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Папка, в которую сбрасываются устаревающие снимки"
	),
	"file" => __FILE__
);

$fields["snapshotPHPRotatorTemplate"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон скрипта ротации снимков на PHP"
	),
	"file" => __FILE__
);

$fields["backupViewerAddress"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Адрес Web-интерфейса для просмотра теневых резервных копий"
	),
	"file" => __FILE__
);

$fields["smbInvalidUsersFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком запрещенных пользователей Samba"
	),
	"file" => __FILE__
);

$fields["smbRenameGroupCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда переименования группы пользователей Samba"
	),
	"file" => __FILE__
);

$fields["mailIntegration"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"title" => "Интеграция с почтовым сервером",
		"type" => "list,|"
	),
	"file" => __FILE__
);

$fields["gatewayIntegration"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "IP-адрес Интернет-шлюза для интеграции"
	),
	"file" => __FILE__
);

$fields["docFlowIntegration"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"title" => "Интеграция с Бизнес-сервером",
		"type" => "list,|"
	),
	"file" => __FILE__
);

$fields["gatewayNetworkTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон файла правил сети для интеграции с Интернет-шлюзом"
	),
	"file" => __FILE__
);

$fields["gatewayHostTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон файла правил хоста для интеграции с Интернет-шлюзом"
	),
	"file" => __FILE__
);

$fields["gatewayIntegrationPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Каталог интеграции с Интернет-шлюзом"
	),
	"file" => __FILE__
);

$fields["fTPUserHomesMountsFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком домашних папок пользователей Интернет"
	),
	"file" => __FILE__
);

$fields["fTPFoldersMountsFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком общих папок Интернет"
	),
	"file" => __FILE__
);

$fields["proFTPdConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл ProFTPd"
	),
	"file" => __FILE__
);

$fields["proFTPdConfigPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к конфигурационным файлам ProFTPd"
	),
	"file" => __FILE__
);

$fields["proFTPdWAB2ConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл платформы ЛВА для ProFTPd"
	),
	"file" => __FILE__
);

$fields["proFTPdTemplateConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла ProFTPd"
	),
	"file" => __FILE__
);

$fields["proFTPdRestartCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска сервера ProFTPd"
	),
	"file" => __FILE__
);

$fields["fTPUsersFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком пользователей, которым запрещен доступ по FTP"
	),
	"file" => __FILE__
);

$fields["fTPWhoCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда, отображающая список активных пользователей FTP"
	),
	"file" => __FILE__
);

$fields["fTPVirtualHostsConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл виртуальных хостов ProFTPd"
	),
	"file" => __FILE__
);

$fields["fTPVirtualHostsTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла виртуальных хостов ProFTPd"
	),
	"file" => __FILE__
);

$fields["fTPShapingConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл ограничения скорости доступа к FTP-серверу"
	),
	"file" => __FILE__
);

$fields["fTPShapingTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла ограничения скорости доступа к FTP-серверу"
	),
	"file" => __FILE__
);

$fields["ftpdctlCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда управления FTP-сервером"
	),
	"file" => __FILE__
);

$fields["davFoldersConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл общих папок WebDAV"
	),
	"file" => __FILE__
);

$fields["davFoldersTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с шаблоном конфигурации общей папки WebDAV"
	),
	"file" => __FILE__
);

$fields["afpRestartCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска сервера AFP"
	),
	"file" => __FILE__
);

$fields["afpPort"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Порт AFP"
	),
	"file" => __FILE__
);

$fields["afpDefaultOptions"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Параметры общего ресурса AFP по умолчанию"
	),
	"file" => __FILE__
);

$fields["afpSharesFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с описанием общих ресурсов AFP"
	),
	"file" => __FILE__
);

$fields["afpConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл AFP"
	),
	"file" => __FILE__
);

$fields["netatalkConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл демона Netatalk"
	),
	"file" => __FILE__
);

$fields["avahiAfpServiceConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл службы обнаружения сервера AFP"
	),
	"file" => __FILE__
);

$fields["afpDefaultNetwork"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Сеть по умолчанию"
	),
	"file" => __FILE__
);

$groups["controllerMain"] = 
array
(
	"title" => "Параметры",
	"fields" => array
	(
		"title",
		"class",
		"image",
		"hostsModifyCommand",
		"shellInABoxCommand",
		"remoteVNCConfigFile"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerSMB"] = 
array
(
	"title" => "SMB",
	"fields" => array
	(
		"smbConfigFile",
		"smbHostsPath",
		"smbDefaultSharesFile",
		"smbCustomHostsPath",
		"smbTemplateConfigFile",
		"smbShareTemplateFile",
		"smbRestartCommand",
		"smbReloadCommand",
		"smbResetAdminPasswordCommand",
		"smbListUsersCommand",
		"smbAddUserCommand",
		"smbRemoveUserCommand",
		"smbAddUserToGroupCommand",
		"smbRemoveUserFromGroupCommand",
		"smbListGroupsCommand",
		"smbAddGroupCommand",
		"smbRemoveGroupCommand",
		"smbGetGroupUsersCommand",
		"smbGetUserGroupsCommand",
		"smbChangeUserPasswordCommand",
		"smbChangeUserFullNameCommand",
		"smbGetLocalSidCommand",
		"smbChangeUserOptionsCommand",
		"smbChangeGroupOptionsCommand",
		"smbGetUserInfoCommand",
		"smbGetGroupInfoCommand",
		"smbGetGidOfGroupCommand",
		"smbUserMapFile",
		"smbGetACLCommand",
		"smbSetACLCommand",
		"smbRemoveACLCommand",
		"smbAddPrivilegesCommand",
		"smbRemovePrivilegesCommand",
		"smbGetPrivilegesCommand",
		"smbGetlocalsidCommand",
		"smbSetLocalsidCommand",
		"smbGetUserACLCommand",
		"smbGetShareACLCommand",
		"smbAutoRestart",
		"smbSharesConfigPath",
		"smbShareVFSTemplateFile",
		"smbDenyUnknownHosts",
		"smbInvalidUsersFile",
		"smbRenameGroupCommand",
		"smbFirewallFile",
		"scanNetbiosNameCommand",
		"smbUsersLdapBase",
		"smbGroupsLdapBase",
		"smbMachinesLdapBase"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerLog"] = 
array
(
	"title" => "Журналы",
	"fields" => array
	(
		"smbAuditLogFile",
		"smbAuditPeriod",
		"smbAuditDBHost",
		"smbAuditDBPort",
		"smbAuditDBName",
		"smbAuditDBUser",
		"smbAuditDBPassword",
		"getAuditDataScript",
		"getAuditDataScriptTemplate"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerShadowCopy"] = 
array
(
	"title" => "Теневое копирование",
	"fields" => array
	(
		"enableShadowCopy",
		"snapshotSize",
		"snapshotsCount",
		"snapshotsFolder",
		"snapshotLibTemplate",
		"shadowCopyEnginePath",
		"shadowCopyPeriodDays",
		"shadowCopyPeriodHours",
		"shadowCopyPeriodMinutes",
		"snapshotCopyLinksTemplate",
		"snapshotRotatorTemplate",
		"lvCreateCommand",
		"lvRemoveCommand",
		"lvResizeCommand",
		"lvDisplayCommand",
		"vgDisplayCommand",
		"shadowCopyVgName",
		"shadowCopyLvName",
		"shadowCopyResizeSize",
		"shadowCopyResizeLimit",
		"enableAutoSnapshotsRotation",
		"expiredSnapshotsBackupFolder",
		"snapshotPHPRotatorTemplate",
		"backupViewerAddress"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerTrash"] = 
array
(
	"title" => "Корзина",
	"fields" => array
	(
		"eraseTrashScript",
		"eraseTrashScriptTemplate"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerNFS"] = 
array
(
	"title" => "NFS",
	"fields" => array
	(
		"nfsRestartCommand",
		"nfsConfigFile",
		"nfsDefaultOptions"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerDHCP"] = 
array
(
	"title" => "DHCP",
	"fields" => array
	(
		"dhcpConfigFile",
		"dhcpConfigTemplateFile",
		"dhcpRestartCommand",
		"dhcpGetHostsTreeCommand",
		"dhcpGetDeleteHostsCommand",
		"dhcpAddHostsTreeCommand",
		"dhcpDeleteHostsTreeCommand",
		"netCenterAutoRestart"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerLDAP"] = 
array
(
	"title" => "LDAP",
	"fields" => array
	(
		"slapdConfigFile",
		"slapdTemplateConfigFile",
		"ldapSearchCommand",
		"ldapDeleteCommand",
		"ldapAddCommand",
		"slapdRestartCommand",
		"ldapClientConfigFile",
		"ldapClientConfigFile2",
		"ldapscriptsConfigFile",
		"ldapscriptsPasswordFile",
		"ldapClientTemplateConfigFile",
		"ldapscriptsTemplateConfigFile",
		"openLdapClientConfigFile",
		"openLdapClientTemplateConfigFile",
		"idealXConfigFile",
		"idealXTemplateConfigFile",
		"idealXBindConfigFile",
		"idealXTemplateBindConfigFile",
		"ldapSchemaFile",
		"defaultLdapHost",
		"defaultLdapPort",
		"defaultLdapUser",
		"defaultLdapPassword",
		"defaultLdapBase"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerDNS"] = 
array
(
	"title" => "DNS",
	"fields" => array
	(
		"bindRestartCommand",
		"bindReloadCommand",
		"bindZonesFile",
		"bindZonesTemplateFile",
		"bindZoneFile",
		"bindCustomZoneRecordsFile",
		"bindZoneTemplateFile"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerIntegration"] = 
array
(
	"title" => "Интеграция",
	"fields" => array
	(
		"docFlowIntegration",
		"mailIntegration",
		"gatewayIntegration",
		"gatewayNetworkTemplateConfigFile",
		"gatewayHostTemplateConfigFile",
		"gatewayIntegrationPath",
		"remoteAddress"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerInternet"] = 
array
(
	"title" => "Интернет",
	"fields" => array
	(
		"fTPUserHomesMountsFile",
		"fTPFoldersMountsFile",
		"proFTPdConfigFile",
		"proFTPdConfigPath",
		"proFTPdWAB2ConfigFile",
		"proFTPdTemplateConfigFile",
		"proFTPdRestartCommand",
		"fTPUsersFile",
		"fTPWhoCommand",
		"fTPVirtualHostsConfigFile",
		"fTPShapingConfigFile",
		"fTPVirtualHostsTemplateFile",
		"fTPShapingTemplateFile",
		"ftpdctlCommand",
		"davFoldersConfigFile",
		"davFoldersTemplateFile"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["controllerAFP"] = 
array
(
	"title" => "AFP",
	"fields" => array
	(
		"afpPort",
		"afpRestartCommand",
		"netatalkConfigFile",
		"afpConfigFile",
		"avahiAfpServiceConfigFile",
		"afpSharesFile",
		"afpDefaultOptions",
		"afpDefaultNetwork"
	),
	"file" => __FILE__
);

$groups["MystixController"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"controllerMain",
		"controllerSMB",
		"controllerLog",
		"controllerShadowCopy",
		"controllerTrash",
		"controllerNFS",
		"controllerDHCP",
		"controllerLDAP",
		"controllerDNS",
		"controllerIntegration",
		"controllerInternet",
		"controllerAFP"
	),
	"fields" => array
	(

	),
	"title" => "Mystix Controller"
);

$codes["usedDiskSpace"] = 
array
(
	"file" => __FILE__,
	"comment" => "Вычисляет объем, занятый на диске папкой.
В качестве параметров принимает:

path - путь к папке, 
format - формат результата: bytes, kbytes, mbytes, gbytes, adaptive

Соответственно в формате задается либо единица измерения, либо 'adaptive'. При этом автоматически вычисляется единица измерения и подставляется в результат.",
	"metaTitle" => "Возвращает объем занятого пространства на диске",
	"params" => array
	(
		"path" => "/data/share/files",
		"format" => "bytes"
	)
);

$codeGroups["MystixController"] = 
array
(
	"metaTitle" => "Файловый сервер (контроллер)",
	"file" => __FILE__,
	"fields" => array
	(
		"usedDiskSpace"
	)
);

$models["MystixController"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"controllerMain",
		"controllerLDAP",
		"controllerSMB",
		"controllerNFS",
		"controllerAFP",
		"controllerDHCP",
		"controllerDNS",
		"controllerLog",
		"controllerTrash",
		"controllerShadowCopy",
		"controllerInternet",
		"controllerIntegration"
	),
	"title" => "title",
	"class" => "class",
	"image" => "image",
	"hostsModifyCommand" => "hostsModifyCommand",
	"shellInABoxCommand" => "shellInABoxCommand",
	"remoteVNCConfigFile" => "remoteVNCConfigFile",
	"dhcpConfigFile" => "dhcpConfigFile",
	"dhcpConfigTemplateFile" => "dhcpConfigTemplateFile",
	"dhcpRestartCommand" => "dhcpRestartCommand",
	"dhcpGetHostsTreeCommand" => "dhcpGetHostsTreeCommand",
	"dhcpGetDeleteHostsCommand" => "dhcpGetDeleteHostsCommand",
	"dhcpAddHostsTreeCommand" => "dhcpAddHostsTreeCommand",
	"dhcpDeleteHostsTreeCommand" => "dhcpDeleteHostsTreeCommand",
	"scanNetbiosNameCommand" => "scanNetbiosNameCommand",
	"netCenterAutoRestart" => "netCenterAutoRestart",
	"slapdConfigFile" => "slapdConfigFile",
	"slapdTemplateConfigFile" => "slapdTemplateConfigFile",
	"ldapSearchCommand" => "ldapSearchCommand",
	"ldapDeleteCommand" => "ldapDeleteCommand",
	"ldapAddCommand" => "ldapAddCommand",
	"slapdRestartCommand" => "slapdRestartCommand",
	"ldapClientConfigFile" => "ldapClientConfigFile",
	"ldapClientConfigFile2" => "ldapClientConfigFile2",
	"ldapscriptsConfigFile" => "ldapscriptsConfigFile",
	"ldapscriptsPasswordFile" => "ldapscriptsPasswordFile",
	"ldapClientTemplateConfigFile" => "ldapClientTemplateConfigFile",
	"ldapscriptsTemplateConfigFile" => "ldapscriptsTemplateConfigFile",
	"openLdapClientConfigFile" => "openLdapClientConfigFile",
	"openLdapClientTemplateConfigFile" => "openLdapClientTemplateConfigFile",
	"idealXConfigFile" => "idealXConfigFile",
	"idealXTemplateConfigFile" => "idealXTemplateConfigFile",
	"idealXBindConfigFile" => "idealXBindConfigFile",
	"idealXTemplateBindConfigFile" => "idealXTemplateBindConfigFile",
	"ldapSchemaFile" => "ldapSchemaFile",
	"defaultLdapHost" => "defaultLdapHost",
	"defaultLdapPort" => "defaultLdapPort",
	"defaultLdapUser" => "defaultLdapUser",
	"defaultLdapPassword" => "defaultLdapPassword",
	"defaultLdapBase" => "defaultLdapBase",
	"bindRestartCommand" => "bindRestartCommand",
	"bindReloadCommand" => "bindReloadCommand",
	"bindZonesFile" => "bindZonesFile",
	"bindZonesTemplateFile" => "bindZonesTemplateFile",
	"bindZoneFile" => "bindZoneFile",
	"bindCustomZoneRecordsFile" => "bindCustomZoneRecordsFile",
	"bindZoneTemplateFile" => "bindZoneTemplateFile",
	"smbConfigFile" => "smbConfigFile",
	"smbHostsPath" => "smbHostsPath",
	"smbDefaultSharesFile" => "smbDefaultSharesFile",
	"smbCustomHostsPath" => "smbCustomHostsPath",
	"smbTemplateConfigFile" => "smbTemplateConfigFile",
	"smbShareTemplateFile" => "smbShareTemplateFile",
	"smbRestartCommand" => "smbRestartCommand",
	"smbReloadCommand" => "smbReloadCommand",
	"smbResetAdminPasswordCommand" => "smbResetAdminPasswordCommand",
	"smbListUsersCommand" => "smbListUsersCommand",
	"smbAddUserCommand" => "smbAddUserCommand",
	"smbRemoveUserCommand" => "smbRemoveUserCommand",
	"smbAddUserToGroupCommand" => "smbAddUserToGroupCommand",
	"smbRemoveUserFromGroupCommand" => "smbRemoveUserFromGroupCommand",
	"smbListGroupsCommand" => "smbListGroupsCommand",
	"smbAddGroupCommand" => "smbAddGroupCommand",
	"smbRemoveGroupCommand" => "smbRemoveGroupCommand",
	"smbGetGroupUsersCommand" => "smbGetGroupUsersCommand",
	"smbGetUserGroupsCommand" => "smbGetUserGroupsCommand",
	"smbChangeUserPasswordCommand" => "smbChangeUserPasswordCommand",
	"smbChangeUserFullNameCommand" => "smbChangeUserFullNameCommand",
	"smbGetLocalSidCommand" => "smbGetLocalSidCommand",
	"smbChangeUserOptionsCommand" => "smbChangeUserOptionsCommand",
	"smbChangeGroupOptionsCommand" => "smbChangeGroupOptionsCommand",
	"smbGetUserInfoCommand" => "smbGetUserInfoCommand",
	"smbGetGroupInfoCommand" => "smbGetGroupInfoCommand",
	"smbGetGidOfGroupCommand" => "smbGetGidOfGroupCommand",
	"smbUserMapFile" => "smbUserMapFile",
	"smbGetACLCommand" => "smbGetACLCommand",
	"smbSetACLCommand" => "smbSetACLCommand",
	"smbRemoveACLCommand" => "smbRemoveACLCommand",
	"smbAddPrivilegesCommand" => "smbAddPrivilegesCommand",
	"smbRemovePrivilegesCommand" => "smbRemovePrivilegesCommand",
	"smbGetPrivilegesCommand" => "smbGetPrivilegesCommand",
	"smbGetlocalsidCommand" => "smbGetlocalsidCommand",
	"smbSetLocalsidCommand" => "smbSetLocalsidCommand",
	"smbGetUserACLCommand" => "smbGetUserACLCommand",
	"smbGetShareACLCommand" => "smbGetShareACLCommand",
	"smbAutoRestart" => "smbAutoRestart",
	"nfsRestartCommand" => "nfsRestartCommand",
	"nfsConfigFile" => "nfsConfigFile",
	"nfsDefaultOptions" => "nfsDefaultOptions",
	"smbSharesConfigPath" => "smbSharesConfigPath",
	"smbShareVFSTemplateFile" => "smbShareVFSTemplateFile",
	"smbUsersLdapBase" => "smbUsersLdapBase",
	"smbGroupsLdapBase" => "smbGroupsLdapBase",
	"smbMachinesLdapBase" => "smbMachinesLdapBase",
	"smbAuditLogFile" => "smbAuditLogFile",
	"smbAuditPeriod" => "smbAuditPeriod",
	"smbAuditDBHost" => "smbAuditDBHost",
	"smbAuditDBPort" => "smbAuditDBPort",
	"smbAuditDBName" => "smbAuditDBName",
	"smbAuditDBUser" => "smbAuditDBUser",
	"smbAuditDBPassword" => "smbAuditDBPassword",
	"smbDenyUnknownHosts" => "smbDenyUnknownHosts",
	"enableShadowCopy" => "enableShadowCopy",
	"snapshotSize" => "snapshotSize",
	"snapshotsCount" => "snapshotsCount",
	"snapshotsFolder" => "snapshotsFolder",
	"snapshotLibTemplate" => "snapshotLibTemplate",
	"snapshotRotatorTemplate" => "snapshotRotatorTemplate",
	"shadowCopyEnginePath" => "shadowCopyEnginePath",
	"getAuditDataScript" => "getAuditDataScript",
	"getAuditDataScriptTemplate" => "getAuditDataScriptTemplate",
	"eraseTrashScript" => "eraseTrashScript",
	"eraseTrashScriptTemplate" => "eraseTrashScriptTemplate",
	"shadowCopyPeriodDays" => "shadowCopyPeriodDays",
	"shadowCopyPeriodHours" => "shadowCopyPeriodHours",
	"shadowCopyPeriodMinutes" => "shadowCopyPeriodMinutes",
	"snapshotCopyLinksTemplate" => "snapshotCopyLinksTemplate",
	"smbFirewallFile" => "smbFirewallFile",
	"lvCreateCommand" => "lvCreateCommand",
	"lvRemoveCommand" => "lvRemoveCommand",
	"lvResizeCommand" => "lvResizeCommand",
	"lvDisplayCommand" => "lvDisplayCommand",
	"vgDisplayCommand" => "vgDisplayCommand",
	"shadowCopyVgName" => "shadowCopyVgName",
	"shadowCopyLvName" => "shadowCopyLvName",
	"shadowCopyResizeSize" => "shadowCopyResizeSize",
	"shadowCopyResizeLimit" => "shadowCopyResizeLimit",
	"enableAutoSnapshotsRotation" => "enableAutoSnapshotsRotation",
	"expiredSnapshotsBackupFolder" => "expiredSnapshotsBackupFolder",
	"snapshotPHPRotatorTemplate" => "snapshotPHPRotatorTemplate",
	"backupViewerAddress" => "backupViewerAddress",
	"smbInvalidUsersFile" => "smbInvalidUsersFile",
	"smbRenameGroupCommand" => "smbRenameGroupCommand",
	"mailIntegration" => "mailIntegration",
	"gatewayIntegration" => "gatewayIntegration",
	"docFlowIntegration" => "docFlowIntegration",
	"gatewayNetworkTemplateConfigFile" => "gatewayNetworkTemplateConfigFile",
	"gatewayHostTemplateConfigFile" => "gatewayHostTemplateConfigFile",
	"gatewayIntegrationPath" => "gatewayIntegrationPath",
	"fTPUserHomesMountsFile" => "fTPUserHomesMountsFile",
	"fTPFoldersMountsFile" => "fTPFoldersMountsFile",
	"proFTPdConfigFile" => "proFTPdConfigFile",
	"proFTPdConfigPath" => "proFTPdConfigPath",
	"proFTPdWAB2ConfigFile" => "proFTPdWAB2ConfigFile",
	"proFTPdTemplateConfigFile" => "proFTPdTemplateConfigFile",
	"proFTPdRestartCommand" => "proFTPdRestartCommand",
	"fTPUsersFile" => "fTPUsersFile",
	"fTPWhoCommand" => "fTPWhoCommand",
	"fTPVirtualHostsConfigFile" => "fTPVirtualHostsConfigFile",
	"fTPShapingConfigFile" => "fTPShapingConfigFile",
	"fTPVirtualHostsTemplateFile" => "fTPVirtualHostsTemplateFile",
	"fTPShapingTemplateFile" => "fTPShapingTemplateFile",
	"ftpdctlCommand" => "ftpdctlCommand",
	"davFoldersConfigFile" => "davFoldersConfigFile",
	"davFoldersTemplateFile" => "davFoldersTemplateFile",
	"afpSharesFile" => "afpSharesFile",
	"afpRestartCommand" => "afpRestartCommand",
	"afpDefaultOptions" => "afpDefaultOptions",
	"afpPort" => "afpPort",
	"afpConfigFile" => "afpConfigFile",
	"netatalkConfigFile" => "netatalkConfigFile",
	"avahiAfpServiceConfigFile" => "avahiAfpServiceConfigFile",
	"afpDefaultNetwork" => "afpDefaultNetwork",
	"name" => "name",
	"remoteAddress" => "remoteAddress",
	"metaTitle" => "Файловый сервер (контроллер)"
);

$models["FileProperties"] = 
array
(
	"metaTitle" => "Свойства файла или папки",
	"file" => __FILE__
);

$modules["MystixController"] = 
array
(
	"title" => "Сеть",
	"class" => "ControllerApplication_Network",
	"image" => "images/Tree/network.gif",
	"hostsModifyCommand" => "echo 127.0.0.1 {hostname} localhost > /etc/hosts",
	"shellInABoxCommand" => "echo '/usr/bin/shellinaboxd -t -p {port} -s /:SSH:{ip}:{consolePort} -c /var/lib/shellinabox --static-file=/etc/shellinabox/options-available/wb.css:style.css' | at now",
	"remoteVNCConfigFile" => "/etc/guacamole/user-mapping.xml",
	"dhcpConfigFile" => "/etc/dhcp/dhcpd.conf",
	"dhcpConfigTemplateFile" => "templates/controller/dhcpd.conf",
	"dhcpRestartCommand" => "/etc/init.d/isc-dhcp-server restart >/dev/null 2>/dev/null",
	"dhcpGetHostsTreeCommand" => "ldapsearch -x -b '{base}' -LLL | sed 's/cn={old_hostname}/cn={hostname}/g' > /root/ldif.ldif",
	"dhcpGetDeleteHostsCommand" => "ldapsearch -x -b '{base}' -LLL | grep 'dn:' | sed -e 's/dn: //' | tac > /root/delete.ldif",
	"dhcpAddHostsTreeCommand" => "ldapadd -D '{ldap_user_name}' -w '{ldap_password}' -x -f /root/ldif.ldif",
	"dhcpDeleteHostsTreeCommand" => "ldapdelete -D '{ldap_user_name}' -w '{ldap_password}' -x -f /root/delete.ldif",
	"scanNetbiosNameCommand" => "nmblookup -A {host} | grep 'ACTIVE' | head -1 | fmt -us | sed -e 's/	//g' | cut -d ' ' -f1",
	"netCenterAutoRestart" => "1",
	"slapdConfigFile" => "/etc/ldap/slapd.conf",
	"slapdTemplateConfigFile" => "templates/controller/slapd.conf",
	"ldapSearchCommand" => "ldapsearch",
	"ldapDeleteCommand" => "ldapdelete",
	"ldapAddCommand" => "ldapadd",
	"slapdRestartCommand" => "/etc/init.d/slapd restart >/dev/null 2>/dev/null",
	"ldapClientConfigFile" => "/etc/pam_ldap.conf",
	"ldapClientConfigFile2" => "/etc/libnss-ldap.conf",
	"ldapscriptsConfigFile" => "/etc/ldapscripts/ldapscripts.conf",
	"ldapscriptsPasswordFile" => "/etc/ldapscripts/ldapscripts.passwd",
	"ldapClientTemplateConfigFile" => "templates/controller/ldap.conf",
	"ldapscriptsTemplateConfigFile" => "templates/controller/ldapscripts.conf",
	"openLdapClientConfigFile" => "/etc/ldap/ldap.conf",
	"openLdapClientTemplateConfigFile" => "templates/controller/openldap.conf",
	"idealXConfigFile" => "/etc/smbldap-tools/smbldap.conf",
	"idealXTemplateConfigFile" => "templates/controller/smbldap.conf",
	"idealXBindConfigFile" => "/etc/smbldap-tools/smbldap_bind.conf",
	"idealXTemplateBindConfigFile" => "templates/controller/smbldap_bind.conf",
	"ldapSchemaFile" => "/etc/ldap/schema/lva.schema",
	"defaultLdapHost" => "localhost",
	"defaultLdapPort" => "389",
	"defaultLdapUser" => "admin",
	"defaultLdapPassword" => "333333",
	"defaultLdapBase" => "dc=lvacompany,dc=ru",
	"bindRestartCommand" => "/etc/init.d/bind9 restart >/dev/null 2>/dev/null",
	"bindReloadCommand" => "/etc/init.d/bind9 reload >/dev/null 2>/dev/null",
	"bindZonesFile" => "/etc/bind/mystix.zones",
	"bindZonesTemplateFile" => "templates/controller/mystix.zones",
	"bindZoneFile" => "/etc/bind/db.domain",
	"bindCustomZoneRecordsFile" => "/etc/bind/db.custom",
	"bindZoneTemplateFile" => "templates/controller/db.domain",
	"smbConfigFile" => "/etc/samba/smb.conf",
	"smbHostsPath" => "/etc/samba/hosts",
	"smbDefaultSharesFile" => "/etc/samba/hosts/default.conf",
	"smbCustomHostsPath" => "/etc/samba/hosts_custom",
	"smbTemplateConfigFile" => "templates/controller/smb.conf",
	"smbShareTemplateFile" => "templates/controller/smb_share.conf",
	"smbRestartCommand" => "/etc/init.d/samba restart",
	"smbReloadCommand" => "/etc/init.d/samba reload",
	"smbResetAdminPasswordCommand" => "smbpasswd",
	"smbListUsersCommand" => "/usr/bin/net rpc user -U {credentials}",
	"smbAddUserCommand" => "/usr/bin/net rpc user add {user} {password} -U {credentials}",
	"smbRemoveUserCommand" => "/usr/bin/net rpc user delete {user} -U {credentials};rm -rf {home_dir}",
	"smbAddUserToGroupCommand" => "/usr/bin/net rpc group addmem {group} {user} -U {credentials}",
	"smbRemoveUserFromGroupCommand" => "/usr/bin/net rpc group delmem {group} {user} -U {credentials}",
	"smbListGroupsCommand" => "/usr/bin/net rpc group -U {credentials}",
	"smbAddGroupCommand" => "/usr/bin/net rpc group add {group} -U {credentials}",
	"smbRemoveGroupCommand" => "/usr/bin/net rpc group delete {group} -U {credentials}",
	"smbGetGroupUsersCommand" => "/usr/bin/net rpc group members {group} -U {credentials} | cut -d '\' -f2",
	"smbGetUserGroupsCommand" => "/usr/bin/net rpc user info {user} -U {credentials}",
	"smbChangeUserPasswordCommand" => "/usr/sbin/smbldap-passwd {user} <<EOF
{password}
{password}
EOF",
	"smbChangeUserFullNameCommand" => "/usr/sbin/smbldap-usermod -c '{title1}' -S '{title1}'",
	"smbGetLocalSidCommand" => "/usr/bin/net getlocalsid | cut -d ' ' -f6",
	"smbChangeUserOptionsCommand" => "/usr/sbin/smbldap-usermod {params} ",
	"smbChangeGroupOptionsCommand" => "/usr/sbin/smbldap-groupmod {params} ",
	"smbGetUserInfoCommand" => "/usr/sbin/smbldap-usershow {user}",
	"smbGetGroupInfoCommand" => "/usr/sbin/smbldap-groupshow {group}",
	"smbGetGidOfGroupCommand" => "/usr/sbin/smbldap-groupshow {group} | grep gidNumber | cut -d ';' -f2 | cut -d ' ' -f2",
	"smbUserMapFile" => "/etc/samba/smbusers",
	"smbGetACLCommand" => "getfacl -c '{share}' 2>/dev/null | grep {user_or_group} | grep -v default | cut -d ':' -f2,3 | grep '{user}' | cut -d ':' -f2",
	"smbSetACLCommand" => "setfacl -R -m {acl} '{share}';setfacl -R -d -m {acl} '{share}'",
	"smbRemoveACLCommand" => "setfacl -R -x {acl} '{share}';setfacl -R -d -x {acl} '{share}'",
	"smbAddPrivilegesCommand" => "/usr/bin/net rpc rights grant {user} {privileges} -U {credentials}",
	"smbRemovePrivilegesCommand" => "/usr/bin/net rpc rights revoke {user} {privileges} -U {credentials}",
	"smbGetPrivilegesCommand" => "/usr/bin/net rpc rights list {user} -U {credentials}",
	"smbGetlocalsidCommand" => "/usr/bin/net getlocalsid | cut -d ':' -f2 | cut -d ' ' -f2",
	"smbSetLocalsidCommand" => "/usr/bin/net setlocalsid",
	"smbGetUserACLCommand" => "/bin/bash /etc/WAB2/config/getuseracls.sh {user_or_group} {user} {shares_list}",
	"smbGetShareACLCommand" => "getfacl -cp '{share}' | grep '{type}:' | grep -v 'default:' | grep -v '::' | tr ':' '~' | cut -d '~' -f2,3",
	"smbAutoRestart" => "0",
	"nfsRestartCommand" => "exportfs -ra",
	"nfsConfigFile" => "/etc/exports",
	"nfsDefaultOptions" => "sync,subtree_check",
	"smbSharesConfigPath" => "/etc/samba/shares",
	"smbShareVFSTemplateFile" => "templates/controller/smb_share_vfs.conf",
	"smbUsersLdapBase" => "ou=users",
	"smbGroupsLdapBase" => "ou=groups",
	"smbMachinesLdapBase" => "ou=machines",
	"smbAuditLogFile" => "/var/log/samba/log.audit",
	"smbAuditPeriod" => "1",
	"smbAuditDBHost" => "localhost",
	"smbAuditDBPort" => "3306",
	"smbAuditDBName" => "fullAudit",
	"smbAuditDBUser" => "root",
	"smbAuditDBPassword" => "111111",
	"smbDenyUnknownHosts" => "0",
	"enableShadowCopy" => "1",
	"snapshotSize" => "1",
	"snapshotsCount" => "1",
	"snapshotsFolder" => "/data/snapshots",
	"snapshotLibTemplate" => "templates/controller/libsnapshot.pm",
	"snapshotRotatorTemplate" => "templates/controller/snapshot_rotator.sh",
	"shadowCopyEnginePath" => "/root/shadow_copy/",
	"getAuditDataScript" => "/etc/WAB2/config/getauditdata.php",
	"getAuditDataScriptTemplate" => "templates/controller/getauditdata.php",
	"eraseTrashScript" => "/etc/WAB2/config/erasetrash.php",
	"eraseTrashScriptTemplate" => "templates/controller/erasetrash.php",
	"shadowCopyPeriodDays" => "*",
	"shadowCopyPeriodHours" => "*/1",
	"shadowCopyPeriodMinutes" => "1",
	"snapshotCopyLinksTemplate" => "templates/controller/shadowcopy_make_links.php",
	"smbFirewallFile" => "/root/firewall.sh",
	"lvCreateCommand" => "/sbin/lvcreate",
	"lvRemoveCommand" => "/sbin/lvremove",
	"lvResizeCommand" => "/sbin/lvresize",
	"lvDisplayCommand" => "/sbin/lvdisplay",
	"vgDisplayCommand" => "/sbin/vgdisplay",
	"shadowCopyVgName" => "vg0",
	"shadowCopyLvName" => "DATA",
	"shadowCopyResizeSize" => "6",
	"shadowCopyResizeLimit" => "81",
	"enableAutoSnapshotsRotation" => "1",
	"expiredSnapshotsBackupFolder" => "/data/share/trash/snapshots",
	"snapshotPHPRotatorTemplate" => "templates/controller/rotate_snapshots.php",
	"backupViewerAddress" => "http://192.168.0.45:81",
	"smbInvalidUsersFile" => "/etc/samba/invalid.conf",
	"smbRenameGroupCommand" => "/usr/bin/net rpc group rename {group} {new_group} -U {credentials}",
	"mailIntegration" => "MystixCollectorMX",
	"gatewayIntegration" => "",
	"gatewayNetworkTemplateConfigFile" => "templates/bastion/net",
	"gatewayHostTemplateConfigFile" => "templates/bastion/host",
	"gatewayIntegrationPath" => "/etc/lbs",
	"fTPUserHomesMountsFile" => "/root/ftphomes.sh",
	"fTPFoldersMountsFile" => "/root/ftpfolders.sh",
	"proFTPdConfigFile" => "/etc/proftpd/proftpd.conf",
	"proFTPdConfigPath" => "/etc/proftpd",
	"proFTPdWAB2ConfigFile" => "/etc/proftpd/wab2.conf",
	"proFTPdTemplateConfigFile" => "templates/controller/proftpd.conf",
	"proFTPdRestartCommand" => "/etc/init.d/proftpd restart",
	"fTPUsersFile" => "/etc/ftpusers",
	"fTPWhoCommand" => "ftpwho -v -o oneline -S {hostname} | fmt -usw 1000 | grep '\[' | cut -d ' ' -f2-",
	"fTPVirtualHostsConfigFile" => "/etc/proftpd/virtuals.conf",
	"fTPShapingConfigFile" => "/root/ftpshaping.sh",
	"fTPVirtualHostsTemplateFile" => "templates/ftp/ftphost.conf",
	"fTPShapingTemplateFile" => "templates/ftp/ftpshaping.sh",
	"ftpdctlCommand" => "ftpdctl -s /var/run/proftpd/proftpd.sock {command}",
	"davFoldersConfigFile" => "/etc/lighttpd/davfolders.conf",
	"davFoldersTemplateFile" => "templates/controller/davfolders.conf",
	"afpSharesFile" => "/etc/netatalk/AppleVolumes.default",
	"afpRestartCommand" => "/etc/init.d/netatalk restart",
	"afpDefaultOptions" => "upriv,usedots",
	"afpPort" => "548",
	"afpConfigFile" => "/etc/netatalk/afpd.conf",
	"netatalkConfigFile" => "/etc/default/netatalk",
	"avahiAfpServiceConfigFile" => "/etc/avahi/services/afpd.service",
	"afpDefaultNetwork" => "192.168.0.0/8",
	"name" => "MystixController",
	"collection" => "modules",
	"settings" => array
	(
		"collection" => "modules",
		"name" => "settings",
		"EventLog_Events" => array
		(
			"host" => "localhost",
			"port" => "3306",
			"dbname" => "fullAudit",
			"dbtable" => "events",
			"user" => "root",
			"password" => "111111",
			"period" => "1",
			"eventTypes" => array
			(
				"USER_ONLINE",
				"USER_OFFLINE",
				"ENTITY_CHANGED",
				"ENTITY_ADDED",
				"ENTITY_DELETED",
				"ENTITY_MARK_DELETED",
				"ENTITY_MARK_UNDELETED",
				"ENTITY_OPENED",
				"ENTITY_CLOSED",
				"ENTITY_REGISTERED",
				"ENTITY_MARK_REGISTERED",
				"ENTITY_MARK_UNREGISTERED",
				"DHCPHOST_ADDED",
				"DHCPHOST_CHANGED",
				"DHCPHOST_DELETED",
				"DHCPSUBNET_ADDED",
				"DHCPSUBNET_CHANGED",
				"DHCPSUBNET_DELETED",
				"DHCPSERVER_CHANGED",
				"DHCPSERVER_RESTARTED",
				"FILESERVER_CHANGED",
				"FILESERVER_RESTARTED",
				"DOCFLOWINTEGRATOR_CHANGED",
				"FILESHARE_DELETED",
				"FILESHARE_ADDED",
				"FILESHARE_CHANGED",
				"GATEWAYINTEGRATOR_CHANGED",
				"GROUP_ADDED",
				"GROUP_CHANGED",
				"GROUP_DELETED",
				"LVMSNAPSHOT_CREATE",
				"LVMSNAPSHOT_REMOVE",
				"LVMSNAPSHOT_REMOVEALL",
				"LVMSNAPSHOT_RESIZE",
				"MAILINTEGRATOR_CHANGED",
				"OBJECTGROUP_ADDED",
				"OBJECTGROUP_CHANGED",
				"SHADOWCOPY_CHANGED",
				"CONTROLLER_SETTINGS_CHANGED",
				"USER_ADDED",
				"USER_CHANGED",
				"USER_DELETED",
				"APACHEUSER_ADDED",
				"APACHEUSER_CHANGED",
				"APACHEUSER_DELETED",
				"USER_BAN",
				"FTPSERVER_CHANGED",
				"ROLE_ADDED",
				"ROLE_CHANGED",
				"ROLE_DELETED",
				"PROFILE_CHANGED",
				"PROFILE_DELETED",
				"FM_MAKEDIR",
				"FM_RENAME",
				"FM_COPY",
				"FM_MOVE",
				"FM_DELETE",
				"FM_UPLOAD",
				"FM_CHANGEPROPERTIES",
				"UPDATE_FILES"
			),
			"logEvents" => array
			(
				"USER_ONLINE",
				"USER_OFFLINE",
				"ENTITY_CHANGED",
				"ENTITY_OPENED",
				"ENTITY_CLOSED",
				"DHCPHOST_ADDED",
				"DHCPHOST_CHANGED",
				"DHCPHOST_DELETED",
				"DHCPSUBNET_ADDED",
				"DHCPSUBNET_CHANGED",
				"DHCPSUBNET_DELETED",
				"DHCPSERVER_CHANGED",
				"DHCPSERVER_RESTARTED",
				"FILESERVER_CHANGED",
				"FILESERVER_RESTARTED",
				"DOCFLOWINTEGRATOR_CHANGED",
				"FILESHARE_DELETED",
				"FILESHARE_ADDED",
				"FILESHARE_CHANGED",
				"GATEWAYINTEGRATOR_CHANGED",
				"GROUP_ADDED",
				"GROUP_CHANGED",
				"GROUP_DELETED",
				"LVMSNAPSHOT_CREATE",
				"LVMSNAPSHOT_REMOVE",
				"LVMSNAPSHOT_REMOVEALL",
				"LVMSNAPSHOT_RESIZE",
				"MAILINTEGRATOR_CHANGED",
				"OBJECTGROUP_ADDED",
				"OBJECTGROUP_CHANGED",
				"SHADOWCOPY_CHANGED",
				"CONTROLLER_SETTINGS_CHANGED",
				"USER_ADDED",
				"USER_CHANGED",
				"USER_DELETED",
				"APACHEUSER_ADDED",
				"APACHEUSER_CHANGED",
				"APACHEUSER_DELETED",
				"USER_BAN",
				"FTPSERVER_CHANGED",
				"ROLE_ADDED",
				"ROLE_CHANGED",
				"ROLE_DELETED",
				"PROFILE_CHANGED",
				"PROFILE_DELETED",
				"FM_MAKEDIR",
				"FM_RENAME",
				"FM_COPY",
				"FM_MOVE",
				"FM_DELETE",
				"FM_UPLOAD",
				"FM_CHANGEPROPERTIES",
				"UPDATE_FILES"
			)
		)
	),
	"file" => __FILE__,
	"docFlowIntegration" => "DocFlowCRM"
);
?>