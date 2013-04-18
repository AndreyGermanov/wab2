<?php
$fields["postfixInConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл 1-го агента Postfix"
	),
	"file" => __FILE__
);

$fields["postfixConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл 2-го агента Postfix"
	),
	"file" => __FILE__
);

$fields["postfixOutConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл 3-го агента Postfix"
	),
	"file" => __FILE__
);

$fields["postfixDomainsTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком доменов Postfix"
	),
	"file" => __FILE__
);

$fields["postfixMailboxesTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком почтовых ящиков Postfix"
	),
	"file" => __FILE__
);

$fields["postfixAliasesTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списками рассылки Postfix"
	),
	"file" => __FILE__
);

$fields["postfixGenericTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком подмены обратных адресов Postfix"
	),
	"file" => __FILE__
);

$fields["postfixTransportTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком транспортных агентов для каждого домена Postfix"
	),
	"file" => __FILE__
);

$fields["fetchMailFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл почтовых ящиков Интернет"
	),
	"file" => __FILE__
);

$fields["dovecotUsersFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Список пользователей Dovecot/Postfix"
	),
	"file" => __FILE__
);

$fields["mailScannerRulesTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком правил MailScanner"
	),
	"file" => __FILE__
);

$fields["mailScannerConfigFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Конфигурационный файл MailScanner"
	),
	"file" => __FILE__
);

$fields["mailScannerConfigTemplateFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Шаблон конфигурационного файла MailScanner"
	),
	"file" => __FILE__
);

$fields["addressBookFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с правилами адресной книги"
	),
	"file" => __FILE__
);

$fields["addressBookDefaultFieldsFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком полей адресной книги по умолчанию"
	),
	"file" => __FILE__
);

$fields["addressBookRuleFilesPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к файлам-записям адресной книги"
	),
	"file" => __FILE__
);

$fields["mailPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к почтовому хранилищу"
	),
	"file" => __FILE__
);

$fields["mailLog"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл журнала событий почты"
	),
	"file" => __FILE__
);

$fields["mailUID"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Идентификатор пользователя-владельца почты"
	),
	"file" => __FILE__
);

$fields["mailGID"] = 
array
(
	"base" => "integerField",
	"params" => array
	(
		"title" => "Идентификатор группы-владельца почты"
	),
	"file" => __FILE__
);

$fields["restartMailScannerCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда перезапуска MailScanner"
	),
	"file" => __FILE__
);

$fields["postmapCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда создания таблицы Postfix"
	),
	"file" => __FILE__
);

$fields["getQueueCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда, получающая содержимое очереди Postfix"
	),
	"file" => __FILE__
);

$fields["listQueueCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда, получающая содержимое очереди Postfix"
	),
	"file" => __FILE__
);

$fields["getDeferReasonCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда, получающая причину задержки письма в очереди"
	),
	"file" => __FILE__
);

$fields["postcatCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда, отображающая содержимое файла с письмом"
	),
	"file" => __FILE__
);

$fields["postSuperCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для осуществления административных действий с почтовой очередью"
	),
	"file" => __FILE__
);

$fields["spamWhitelistFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Белый список спам-фильтра"
	),
	"file" => __FILE__
);

$fields["spamBlacklistFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Черный список спам-фильтра"
	),
	"file" => __FILE__
);

$fields["spamGetRulesCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для получения списка правил из файла правил MailScanner"
	),
	"file" => __FILE__
);

$fields["repFilterTable"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком исключений репутационного фильтра"
	),
	"file" => __FILE__
);

$fields["autoreplyTextsPath"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к списку файлов с текстами автоответчиков для почтовых ящиков"
	),
	"file" => __FILE__
);

$fields["autoreplyDomain"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Домен, из которого приходит письмо с текстом автоответчика"
	),
	"file" => __FILE__
);

$fields["autoreplyAliasesFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл со списком псевдонимов для отправки сообщения на автоответчик"
	),
	"file" => __FILE__
);

$fields["postconfEditCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для редактирования конфигурационного файла Postfix"
	),
	"file" => __FILE__
);

$fields["postconfShowCommand"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Команда для отображения конфигурационного файла Postfix"
	),
	"file" => __FILE__
);

$fields["saslPasswordFile"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл с паролем SASL-аутентификации для перенаправления почты через внешний сервер"
	),
	"file" => __FILE__
);

$groups["MystixCollectorMX"] = 
array
(
	"title" => "Mystix Collector MX",
	"groups" => array
	(
		"collectorMain",
		"collectorFiles",
		"collectorCommands"
	),
	"fields" => array
	(

	),
	"file" => __FILE__
);

$groups["collectorMain"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(

	),
	"fields" => array
	(
		"title",
		"class",
		"image",
		"mailUID",
		"mailGID",
		"autoreplyDomain",
		"remoteAddress"
	),
	"title" => "Настройки"
);

$groups["collectorFiles"] = 
array
(
	"title" => "Конфигурационные файлы",
	"fields" => array
	(
		"postfixConfigFile",
		"postfixInConfigFile",
		"postfixOutConfigFile",
		"postfixDomainsTable",
		"postfixMailboxesTable",
		"postfixAliasesTable",
		"postfixGenericTable",
		"postfixTransportTable",
		"fetchMailFile",
		"dovecotUsersFile",
		"mailScannerRulesTable",
		"mailScannerConfigFile",
		"mailScannerConfigTemplateFile",
		"addressBookFile",
		"addressBookDefaultFieldsFile",
		"addressBookRuleFilesPath",
		"mailPath",
		"mailLog",
		"spamWhitelistFile",
		"spamBlacklistFile",
		"repFilterTable",
		"autoreplyTextsPath",
		"autoreplyAliasesFile",
		"saslPasswordFile"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$groups["collectorCommands"] = 
array
(
	"title" => "Команды",
	"fields" => array
	(
		"restartMailScannerCommand",
		"postmapCommand",
		"getQueueCommand",
		"listQueueCommand",
		"getDeferReasonCommand",
		"postcatCommand",
		"postSuperCommand",
		"spamGetRulesCommand",
		"postconfEditCommand",
		"postconfShowCommand"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$codeGroups["MystixCollectorMX"] = 
array
(
	"metaTitle" => "Почтовый сервер",
	"file" => __FILE__,
	"fields" => array
	(

	)
);

$models["MystixCollectorMX"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"collectorMain",
		"collectorFiles",
		"collectorCommands"
	),
	"title" => "title",
	"class" => "class",
	"image" => "image",
	"postfixConfigFile" => "postfixConfigFile",
	"postfixInConfigFile" => "postfixInConfigFile",
	"postfixOutConfigFile" => "postfixOutConfigFile",
	"postfixDomainsTable" => "postfixDomainsTable",
	"postfixMailboxesTable" => "postfixMailboxesTable",
	"postfixAliasesTable" => "postfixAliasesTable",
	"postfixGenericTable" => "postfixGenericTable",
	"postfixTransportTable" => "postfixTransportTable",
	"fetchmailFile" => "fetchmailFile",
	"dovecotUsersFile" => "dovecotUsersFile",
	"mailScannerRulesTable" => "mailScannerRulesTable",
	"mailScannerConfigFile" => "mailScannerConfigFile",
	"mailScannerConfigTemplateFile" => "mailScannerConfigTemplateFile",
	"addressBookFile" => "addressBookFile",
	"addressBookDefaultFieldsFile" => "addressBookDefaultFieldsFile",
	"addressBookRuleFilesPath" => "addressBookRuleFilesPath",
	"mailPath" => "mailPath",
	"mailLog" => "mailLog",
	"mailUID" => "mailUID",
	"mailGID" => "mailGID",
	"restartMailScannerCommand" => "restartMailScannerCommand",
	"postmapCommand" => "postmapCommand",
	"postSuperCommand" => "postSuperCommand",
	"getQueueCommand" => "getQueueCommand",
	"listQueueCommand" => "listQueueCommand",
	"getDeferReasonCommand" => "getDeferReasonCommand",
	"postcatCommand" => "postcatCommand",
	"spamWhitelistFile" => "spamWhitelistFile",
	"spamBlacklistFile" => "spamBlacklistFile",
	"spamGetRulesCommand" => "spamGetRulesCommand",
	"repFilterTable" => "repFilterTable",
	"autoreplyTextsPath" => "autoreplyTextsPath",
	"autoreplyDomain" => "autoreplyDomain",
	"autoreplyAliasesFile" => "autoreplyAliasesFile",
	"postconfEditCommand" => "postconfEditCommand",
	"postconfShowCommand" => "postconfShowCommand",
	"saslPasswordFile" => "saslPasswordFile",
	"remoteAddress" => "remoteAddress",
	"metaTitle" => "Почтовый сервер"
);

$modules["MystixCollectorMX"] = 
array
(
	#"remoteAddress" => "192.168.0.52",
	"title" => "Почта",
	"class" => "MailApplication_Mail",
	"image" => "images/Tabs/mail.gif",
	"postfixConfigFile" => "/etc/postfix/main.cf",
	"postfixInConfigFile" => "/etc/postfix.in/main.cf",
	"postfixOutConfigFile" => "/etc/postfix.out/main.cf",
	"postfixDomainsTable" => "/etc/postfix/virtual_domains",
	"postfixMailboxesTable" => "/etc/postfix/virtual_mailboxes",
	"postfixAliasesTable" => "/etc/postfix/virtual_aliases",
	"postfixGenericTable" => "/etc/postfix/generic",
	"postfixTransportTable" => "/etc/postfix.out/transport",
	"fetchmailFile" => "/etc/fetchmailrc",
	"dovecotUsersFile" => "/etc/postfix/users",
	"mailScannerRulesTable" => "/etc/MailScanner/rules/spam.rules",
	"mailScannerConfigFile" => "/etc/MailScanner/MailScanner.conf",
	"mailScannerConfigTemplateFile" => "templates/collector/MailScanner.conf",
	"addressBookFile" => "/etc/postfix/addrbook.rules",
	"addressBookDefaultFieldsFile" => "/etc/postfix/addrbook.fields",
	"addressBookRuleFilesPath" => "/etc/postfix/addrbook/",
	"mailPath" => "/var/spool/mail/",
	"mailLog" => "/var/log/mail.info",
	"mailUID" => "107",
	"mailGID" => "111",
	"restartMailScannerCommand" => "/etc/init.d/mailscanner reload >/dev/null 2>/dev/null",
	"postmapCommand" => "postmap",
	"getQueueCommand" => "ls -lR /var/spool/{postfix,postfix.in,postfix.out}/{queue} | grep -v '^\$' | grep -v '^[d/а-яt]' | fmt -us | cut -d ' ' -f9",
	"listQueueCommand" => "ls -lR /var/spool/{postfix,postfix.in,postfix.out}/{queue} | grep -v '^\$' | grep -v '^[d/а-яt' | fmt -us | cut -d ' ' -f9",
	"getDeferReasonCommand" => "cat /var/spool/{postfix,postfix.in,postfix.out}/defer/{first_symb}/{id} 2>/dev/null | grep 'reason=' | sed -e 's/reason=//g'",
	"postcatCommand" => "postcat -c {configPath} -q {id} 2>/dev/null",
	"postSuperCommand" => "postsuper -c {configPath} {cmd} 2>/dev/null",
	"spamWhitelistFile" => "/etc/MailScanner/rules/spam.whitelist.rules",
	"spamBlacklistFile" => "/etc/MailScanner/rules/spam.blacklist.rules",
	"spamGetRulesCommand" => "cat /etc/MailScanner/rules/spam.rules | grep -v '#' | grep -v '^\$' | fmt -us",
	"repFilterTable" => "/etc/postfix/reptable",
	"autoreplyTextsPath" => "/etc/postfix.out/autoreply",
	"autoreplyDomain" => "autoreply-X-W.ru",
	"autoreplyAliasesFile" => "/etc/postfix.out/autoreply_maps",
	"postconfEditCommand" => "postconf -c {config} -e '{param}'",
	"postconfShowCommand" => "postconf -c {config} -h '{param}'",
	"saslPasswordFile" => "/etc/postfix/sasl_passwd",
	"name" => "MystixCollectorMX",
	"collection" => "modules",
	"settings" => array
	(
		"collection" => "modules",
		"name" => "settings",
		"EventLog_Events" => array
		(
			"host" => "192.168.0.9",
			"port" => "3306",
			"dbname" => "crm",
			"dbtable" => "events",
			"user" => "root",
			"password" => "vecrec",
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
				"ADDRBOOK_ADDRESS_ADDED",
				"ADDRBOOK_ADDRESS_CHANGED",
				"ADDRBOOK_ADDRESS_DELETED",
				"ADDRBOOK_CHANGED",
				"ADDRBOOK_DEFAULT_FIELDS_CHANGED",
				"LDAPADDRBOOK_ADDED",
				"LDAPADDRBOOK_CHANGED",
				"LDAPADDRBOOK_DELETED",
				"MAILALIAS_ADDED",
				"MAILALIAS_CHANGED",
				"MAILALIAS_DELETED",
				"MAILALIAS_ADDRESS_ADDED",
				"MAILALIAS_ADDRESS_CHANGED",
				"MAILALIAS_ADDRESS_DELETED",
				"MAILBOX_ADDED",
				"MAILBOX_CHANGED",
				"MAILBOX_DELETED",
				"MAILDOMAIN_ADDED",
				"MAILDOMAIN_CHANGED",
				"MAILDOMAIN_DELETED",
				"MAILQUEUE_RESEND",
				"MAILQUEUE_DELETE",
				"MAILQUEUE_REASON",
				"MAILSCANNERCONFIG_CHANGED",
				"MAILSETTINGS_CHANGED",
				"REMOTEMAILBOX_ADDED",
				"REMOTEMAILBOX_CHANGED",
				"REMOTEMAILBOX_DELETED",
				"REPFILTER_CHANGED"
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
			"OBJECTGROUP_DELETED",
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
			"ADDRBOOK_ADDRESS_ADDED",
			"ADDRBOOK_ADDRESS_CHANGED",
			"ADDRBOOK_ADDRESS_DELETED",
			"ADDRBOOK_CHANGED",
			"ADDRBOOK_DEFAULT_FIELDS_CHANGED",
			"LDAPADDRBOOK_ADDED",
			"LDAPADDRBOOK_CHANGED",
			"LDAPADDRBOOK_DELETED",
			"MAILALIAS_ADDED",
			"MAILALIAS_CHANGED",
			"MAILALIAS_DELETED",
			"MAILALIAS_ADDRESS_ADDED",
			"MAILALIAS_ADDRESS_CHANGED",
			"MAILALIAS_ADDRESS_DELETED",
			"MAILBOX_ADDED",
			"MAILBOX_CHANGED",
			"MAILBOX_DELETED",
			"MAILDOMAIN_ADDED",
			"MAILDOMAIN_CHANGED",
			"MAILDOMAIN_DELETED",
			"MAILQUEUE_RESEND",
			"MAILQUEUE_DELETE",
			"MAILQUEUE_REASON",
			"MAILSCANNERCONFIG_CHANGED",
			"MAILSETTINGS_CHANGED",
			"REMOTEMAILBOX_ADDED",
			"REMOTEMAILBOX_CHANGED",
			"REMOTEMAILBOX_DELETED",
			"REPFILTER_CHANGED"
		)
		)
	),		
	"file" => __FILE__
);
?>