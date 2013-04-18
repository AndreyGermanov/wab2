<?php
$fields["dbHost"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Адрес хоста с базой данных"
	),
	"file" => __FILE__
);

$fields["dbName"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Имя базы данных"
	),
	"file" => __FILE__
);

$fields["dbDriver"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Драйвер базы данных"
	),
	"file" => __FILE__
);

$fields["dbUser"] = 
array
(
	"base" => "stringField",
	"params" => array
	(
		"title" => "Пользователь базы данных"
	),
	"file" => __FILE__
);

$fields["dbPassword"] = 
array
(
	"type" => "",
	"base" => "stringField",
	"file" => __FILE__,
	"params" => array
	(
		"password" => "true",
		"title" => "Пароль пользователя базы данных"
	)
);

$groups["DocFlowApplication"] = 
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
		"dbHost",
		"dbName",
		"dbDriver",
		"dbUser",
		"dbPassword",
		"remoteAddress"
	),
	"title" => "Документы"
);

$codeGroups["DocFlowApplication"] = 
array
(
	"metaTitle" => "Документооборот",
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$models["DocFlowApplication"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"DocFlowApplication"
	),
	"title" => "title",
	"class" => "class",
	"image" => "image",
	"dbHost" => "dbHost",
	"dbName" => "dbName",
	"dbDriver" => "dbDriver",
	"dbUser" => "dbUser",
	"dbPassword" => "dbPassword",
	"remoteAddress" => "remoteAddress",
	"metaTitle" => "Документооборот"
);

$models["DocFlowCRM"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"DocFlowApplication"
	),
	"title" => "title",
	"class" => "class",
	"image" => "image",
	"dbHost" => "dbHost",
	"dbName" => "dbName",
	"dbDriver" => "dbDriver",
	"dbUser" => "dbUser",
	"dbPassword" => "dbPassword",
	"remoteAddress" => "remoteAddress",
	"metaTitle" => "Документооборот"
);

$modules["DocFlowApplication"] = 
array
(
	"title" => "Подсистема CRM",
	"class" => "DocFlowApplication_Docs",
	"image" => "",
	"dbHost" => "localhost",
	"dbName" => "medic",
	"dbDriver" => "pdo_mysql",
	"dbUser" => "root",
	"dbPassword" => "vecrec",
	"name" => "DocFlowApplication",
	"collection" => "modules",
	"settings" => array
	(
		"EventLog_Events" => array
		(
			"host" => "localhost",
			"port" => "3306",
			"dbname" => "medic",
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
				"FM_MAKEDIR",
				"FM_RENAME",
				"FM_COPY",
				"FM_MOVE",
				"FM_DELETE",
				"FM_UPLOAD",
				"FM_CHANGEPROPERTIES",
				"USER_BAN",
				"UPDATE_FILES"
			),
			"logEvents" => array
			(
				"USER_ONLINE",
				"USER_OFFLINE",
				"ENTITY_CHANGED",
				"ENTITY_ADDED",
				"ENTITY_DELETED",
				"ENTITY_MARK_DELETED",
				"ENTITY_MARK_UNDELETED",
				"ENTITY_OPENED",
				"FM_MAKEDIR",
				"FM_RENAME",
				"FM_COPY",
				"FM_MOVE",
				"FM_DELETE",
				"FM_UPLOAD",
				"FM_CHANGEPROPERTIES",
				"USER_BAN",
				"UPDATE_FILES"
			)
		),
		"GlobalSearchTable" => array
		(
			"classesList" => array
			(

			)
		),
		"DocumentQuickSale" => array (
			"sendSMSCommand" => "/opt/WAB2/utils/sendtime.py 8 23 {phone} {message}"
		)
	),
	"file" => __FILE__
);

$modules["DocFlowCRM"] = 
array
(
	"title" => "Подсистема CRM",
	"class" => "DocFlowApplication_Docs",
	"image" => "",
	"dbHost" => "localhost",
	"dbName" => "crm",
	"dbDriver" => "pdo_mysql",
	"dbUser" => "root",
	"dbPassword" => "vecrec",
	"name" => "DocFlowCRM",
	"collection" => "modules",
	"settings" => array
	(
		"EventLog_Events" => array
		(
			"host" => "localhost",
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
				"FM_MAKEDIR",
				"FM_RENAME",
				"FM_COPY",
				"FM_MOVE",
				"FM_DELETE",
				"FM_UPLOAD",
				"FM_CHANGEPROPERTIES",
				"USER_BAN",
				"UPDATE_FILES"
			),
			"logEvents" => array
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
				"FM_MAKEDIR",
				"FM_RENAME",
				"FM_COPY",
				"FM_MOVE",
				"FM_DELETE",
				"FM_UPLOAD",
				"FM_CHANGEPROPERTIES",
				"USER_BAN",
				"UPDATE_FILES"
			)
		),
		"GlobalSearchTable" => array
		(
			"classesList" => array
			(

			)
		),
		"DocumentQuickSale" => array (
			"sendSMSScriptPath" => "/opt/WAB2/utils/sms/",
			"sendSMSCommand" => "/opt/WAB2/utils/sms/send.py {phone} '{message}'"
		)				
	),
	"file" => __FILE__
);
?>