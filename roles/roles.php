<?php
$fields["canRead"] = 
array
(
	"type" => "string",
	"name" => "canRead",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на чтение",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canEdit"] = 
array
(
	"type" => "string",
	"name" => "canRead",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на изменение",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canEditProfile"] = 
array
(
	"type" => "string",
	"name" => "canReadProfile",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на изменение профиля",
		"isSimpleProfileParam" => "true"
	)
);

$fields["notifyUsers"] = 
array
(
	"type" => "string",
	"name" => "notifyUsers",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~justMy~code| ~Да~Нет~Только моих объектов~Алгоритм",
		"title" => "Оповещать пользователей об изменениях",
		"isSimpleProfileParam" => "true"
	)
);

$fields["showTagsTab"] = 
array
(
	"type" => "string",
	"name" => "showTagsTab",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Отображать закладку полей",
		"isSimpleProfileParam" => "true"
	)
);

$fields["showNotesTab"] = 
array
(
	"type" => "string",
	"name" => "showNotesTab",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Отображать закладку заметок",
		"isSimpleProfileParam" => "true"
	)
);

$fields["showFilesTab"] = 
array
(
	"type" => "string",
	"name" => "showFilesTab",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Отображать закладку файлов",
		"isSimpleProfileParam" => "true"
	)
);

$fields["showLinksTab"] = 
array
(
	"type" => "string",
	"name" => "showLinksTab",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Отображать закладку связей",
		"isSimpleProfileParam" => "true"
	)
);

$fields["showProfileTab"] = 
array
(
	"type" => "string",
	"name" => "showProfileTab",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Отображать закладку параметров профиля",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canDelete"] = 
array
(
	"type" => "string",
	"name" => "canDelete",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право помечать на удаление",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canUndelete"] = 
array
(
	"type" => "string",
	"name" => "canUndelete",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право снимать пометку удаления",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canAdd"] = 
array
(
	"type" => "string",
	"name" => "canAdd",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на создание новых объектов",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canAddCopy"] = 
array
(
	"type" => "string",
	"name" => "canAddCopy",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на создание новых объектов копированием",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canPrint"] = 
array
(
	"type" => "string",
	"name" => "canPrint",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на печать объекта",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canPrintList"] = 
array
(
	"type" => "string",
	"name" => "canPrintList",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на печать списка объектов",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canSetProperties"] = 
array
(
	"type" => "string",
	"name" => "canSetProperties",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на установку параметров списка объектов",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canSaveListSettings"] = 
array
(
	"type" => "string",
	"name" => "canSaveListSettings",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на сохранение параметров списка объектов",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canFilter"] = 
array
(
	"type" => "string",
	"name" => "canFilter",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на установку фильтра по полю",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canUnfilter"] = 
array
(
	"type" => "string",
	"name" => "canUnfilter",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на снятие всех фильтров",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canEditLinks"] = 
array
(
	"type" => "string",
	"name" => "canEditLinks",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на редактирования связей объекта",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canRegister"] = 
array
(
	"type" => "string",
	"name" => "canRegister",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на проведение документа",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canUnregister"] = 
array
(
	"type" => "string",
	"name" => "canUnregister",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на отмену проведения документа",
		"isSimpleProfileParam" => "true"
	)
);

$fields["canViewMovements"] =
array
(
		"type" => "string",
		"name" => "canViewMovements",
		"collection" => "fields",
		"file" => __FILE__,
		"params" => array
		(
				"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
				"title" => "Право на просмотр движений документа",
				"isSimpleProfileParam" => "true"
		)
);


$fields["canSetSettings"] = 
array
(
	"type" => "string",
	"name" => "canSetSettings",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~code| ~Да~Нет~Алгоритм",
		"title" => "Право на изменение параметров",
		"isSimpleProfileParam" => "true"
	)
);

$fields["listFilter"] = 
array
(
	"type" => "string",
	"name" => "listFilter",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "string",
		"title" => "Условие фильтрации списка",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fieldAccessRules"] = 
array
(
	"type" => "string",
	"name" => "fieldAccessRules",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~read~write~code| ~Чтение~Запись~Алгоритм",
		"title" => "Права доступа к полю",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fieldDefaultsRules"] = 
array
(
	"type" => "string",
	"name" => "fieldDefaultsRules",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~value~code| ~Значение~Алгоритм",
		"title" => "Тип значения поля по умолчанию",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fmCanUpload"] = 
array
(
	"type" => "string",
	"name" => "fmCanUpload",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~rights~code| ~Да~Нет~Права доступа пользователя~Алгоритм",
		"title" => "Право на загрузку файла",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fmCanCreateFolder"] = 
array
(
	"type" => "string",
	"name" => "fmCanCreateFolder",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~rights~code| ~Да~Нет~Права доступа пользователя~Алгоритм",
		"title" => "Право на создание каталога",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fmCanRename"] = 
array
(
	"type" => "string",
	"name" => "fmCanRename",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~rights~code| ~Да~Нет~Права доступа пользователя~Алгоритм",
		"title" => "Право на переименование",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fmCanCopyMove"] = 
array
(
	"type" => "string",
	"name" => "fmCanCopyMove",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~rights~code| ~Да~Нет~Права доступа пользователя~Алгоритм",
		"title" => "Право на копирование/вставку",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fmCanDelete"] = 
array
(
	"type" => "string",
	"name" => "fmCanDelete",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~rights~code| ~Да~Нет~Права доступа пользователя~Алгоритм",
		"title" => "Право на удаление",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fmCanSetProperties"] = 
array
(
	"type" => "string",
	"name" => "fmCanSetProperties",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~false~rights~code| ~Да~Нет~Права доступа пользователя~Алгоритм",
		"title" => "Право на редактирование свойств",
		"isSimpleProfileParam" => "true"
	)
);

$fields["fmRootPath"] = 
array
(
	"type" => "string",
	"name" => "fmRootPath",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"title" => "Корневой каталог",
		"isSimpleProfileParam" => "true",
		"type" => "string"
	)
);

$fields["eventRuleField"] = 
array
(
	"type" => "string",
	"name" => "eventRuleField",
	"collection" => "fields",
	"file" => __FILE__,
	"params" => array
	(
		"type" => "list,empty~true~justMy~false| ~Да~Только мои~Нет",
		"title" => "Условие отправки оповещения о событии"
	)
);

$profileItems = 
array
(
	"name" => "profileItems",
	"file" => __FILE__,
	"canRead" => "canRead",
	"canEdit" => "canEdit",
	"canDelete" => "canDelete",
	"canUndelete" => "canUndelete",
	"canAdd" => "canAdd",
	"canAddCopy" => "canAddCopy",
	"canPrint" => "canPrint",
	"canPrintList" => "canPrintList",
	"canSetProperties" => "canSetProperties",
	"canSaveListSettings" => "canSaveListSettings",
	"canFilter" => "canFilter",
	"canUnfilter" => "canUnfilter",
	"canViewLinks" => "canViewLinks",
	"canEditLinks" => "canEditLinks",
	"canEditProfile" => "canEditProfile",
	"canRegister" => "canRegister",
	"canUnregister" => "canUnregister",
	"canViewMovements" => "canViewMovements",
	"canSetSettings" => "canSetSettings",
	"notifyUsers" => "notifyUsers",
	"showTagsTab" => "showTagsTab",
	"showNotesTab" => "showNotesTab",
	"showFilesTab" => "showFilesTab",
	"showLinksTab" => "showLinksTab",
	"showProfileTab" => "showProfileTab",
	"fieldAccessRules" => "fieldAccessRules",
	"fieldDefaultsRules" => "fieldDefaultsRules",
	"listFilter" => "listFilter",
	"fmCanUpload" => "fmCanUpload",
	"fmCanCreateFolder" => "fmCanCreateFolder",
	"fmCanRename" => "fmCanRename",
	"fmCanCopyMove" => "fmCanCopyMove",
	"fmCanDelete" => "fmCanDelete",
	"fmCanSetProperties" => "fmCanSetProperties",
	"rootPath" => "fmRootPath",
	"eventRuleField" => "eventRuleField"
);

$roles["base"] =
array
(
		"title" => "Базовый",
		"file" => __FILE__,
		"name" => "base",
		"collection" => "roles",
		"visible" => "false",
		"WABEntity" => array
		(
				"canEdit" => "true",
				"fieldAccess" => array
				(
						"*" => "write"
				)
		),
		"InfoPanel" => array
		(
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
						"UPDATE_FILES"
				)
		),	
);
?>