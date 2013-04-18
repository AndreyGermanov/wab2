<?php
$fields["mainpage"] = array("type" => "string",
		"params" => array("type" => "entity",
				"className" => "*WebEntity*",
				"tableClassName" => "EntityDataTable",
				"show_float_div" => "true",
				"classTitle" => "Разделы",
				"editorType" => "WABWindow",
				"title" => "Главная страница",
				"condition" => "@parent IS NOT EXISTS",				
				"parentEntity" => "WebEntity_WebServerApplication_Web_"),
		"name" => "mainpage",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["port"] = array (
		"file" => __FILE__,
		"name" => "port",
		"collection" => "fields",
		"base" => "integerField",
		"params" => array (
				"title" => "Порт"
		)
);

$fields["db_type"] = array (
		"file" => __FILE__,
		"name" => "db_type",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Тип базы данных"
		)
);

$fields["db_host"] = array (
		"file" => __FILE__,
		"name" => "db_host",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Сервер базы данных"
		)
);

$fields["db_name"] = array (
		"file" => __FILE__,
		"name" => "db_name",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Имя базы данных"
		)
);

$fields["db_user"] = array (
		"file" => __FILE__,
		"name" => "db_user",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Пользователь базы данных"
		)
);

$fields["db_password"] = array (
		"file" => __FILE__,
		"name" => "db_password",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Пароль пользователя базы данных",
				"password" => "true"
		)
);

$fields["alias"] = array (
		"file" => __FILE__,
		"name" => "alias",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array (
				"title" => "Псевдоним сайта"
		)
);

$fields["path"] = array (
		"file" => __FILE__,
		"name" => "path",
		"collection" => "fields",
		"base" => "fileField",
		"params" => array (
				"title" => "Путь к файлу данных",
				"absolutePath" => "true"
		)
);

$fields["is_ssl"] = array (
		"file" => __FILE__,
		"name" => "is_ssl",
		"collection" => "fields",
		"base" => "booleanField",
		"params" => array (
				"title" => "Используется шифрование"
		)
);

$fields["is_auth"] = array (
		"file" => __FILE__,
		"name" => "is_auth",
		"collection" => "fields",
		"base" => "booleanField",
		"params" => array (
				"title" => "Используется авторизация"
		)
);

$fields["is_cached"] = array (
		"file" => __FILE__,
		"name" => "is_cached",
		"collection" => "fields",
		"base" => "booleanField",
		"params" => array (
				"title" => "Используется кэширование"
		)
);

$groups["WebSite"] = array (
	"file" => __FILE__,
	"name" => "WebSite",
	"collection" => "groups",
	"title" => "Web-сайт",
	"fields" => array(
		"title",
		"mainpage",
		"port",
		"db_type",
		"db_host",
		"db_name",
		"db_user",
		"db_password",
		"alias",
		"path",
		"is_ssl",
		"is_auth",
		"is_cached"
	)
);

$models["WebSite"] = array(
	"file" => __FILE__,
	"metaTitle" => "Раздел Web-сайта",
	"collection" => "models",
	"title" => "title",
	"mainpage" => "mainpage",
	"port" => "port",
	"db_type" => "db_type",
	"db_host" => "db_host",
	"db_name" => "db_name",
	"db_user" => "db_user",
	"db_password" => "db_password",
	"alias" => "alias",
	"path" => "path",
	"is_ssl" => "is_ssl",
	"is_auth" => "is_auth",
	"is_cached" => "is_cached"
);