<?php
$fields["booleanField"] = 
array
(
	"type" => "boolean",
	"params" => array
	(
		"type" => "boolean",
		"title" => "Логическое"
	),
	"file" => __FILE__
);

$fields["integerField"] = 
array
(
	"type" => "integer",
	"params" => array
	(
		"type" => "integer",
		"title" => "Целое число"
	),
	"file" => __FILE__
);

$fields["decimalField"] = 
array
(
	"type" => "decimal",
	"params" => array
	(
		"type" => "decimal",
		"title" => "Число с плавающей запятой"
	),
	"file" => __FILE__
);

$fields["stringField"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Строка"
	),
	"file" => __FILE__
);

$fields["staticField"] =
array
(
		"type" => "string",
		"params" => array
		(
			"type" => "plaintext"
		),
		"file" => __FILE__
);

$fields["passwordField"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"password" => "true",
		"title" => "Пароль"
	),
	"file" => __FILE__
);

$fields["textField"] = 
array
(
	"type" => "text",
	"params" => array
	(
		"type" => "text",
		"title" => "Многострочный текст",
		"width" => "100%"
	),
	"file" => __FILE__
);

$fields["dateField"] = 
array
(
	"type" => "integer",
	"params" => array
	(
		"type" => "date",
		"title" => "Дата"
	),
	"file" => __FILE__
);

$fields["fileField"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "file",
		"control_type" => "fileManager",
		"width" => "100%",
		"title" => "Файл"
	),
	"file" => __FILE__
);

$fields["pathField"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "path",
		"control_type" => "fileManager",
		"width" => "100%",
		"title" => "Каталог"
	),
	"file" => __FILE__
);

$fields["title"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Заголовок"
	),
	"file" => __FILE__
);

$fields["authorName"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Создатель",
		"hide" => "true"
	),
	"file" => __FILE__
);

$fields["modifications"] = 
array
(
	"type" => "text",
	"params" => array
	(
		"type" => "text",
		"title" => "Модификации",
		"hide" => "true"
	),
	"file" => __FILE__
);

$fields["dateCreated"] = 
array
(
	"type" => "integer",
	"params" => array
	(
		"type" => "date",
		"title" => "Дата создания",
		"hide" => "true"
	),
	"file" => __FILE__
);

$fields["systemName"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Системное имя"
	),
	"file" => __FILE__
);

$fields["class"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "string",
		"title" => "Класс"
	),
	"file" => __FILE__
);

$fields["image"] = 
array
(
	"type" => "string",
	"params" => array
	(
		"type" => "file",
		"title" => "Изображение",
		"control_type" => "fileManagerImage",
		"width" => "100%"
	),
	"file" => __FILE__
);

$fields["remoteAddress"] = 
array
(
	"type" => "",
	"base" => "stringField",
	"file" => __FILE__,
	"params" => array
	(
		"title" => "IP-адрес модуля"
	)
);

$groups["modules"] = 
array
(
	"title" => "Модули",
	"fields" => array
	(

	),
	"groups" => array
	(
		"MystixController",
		"MystixCollectorMX",
		"MystixSpiderman",
		"DocFlowApplication",
		"MystixBastion"
	),
	"file" => __FILE__
);

$groups["base"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(

	),
	"fields" => array
	(
		"booleanField",
		"integerField",
		"decimalField",
		"stringField",
		"textField",
		"dateField",
		"fileField",
		"pathField",
		"passwordField"
	),
	"title" => "Базовые типы"
);

$groups["MystixBastion"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"appconfigCommands",
		"appconfigMain"
	),
	"fields" => array
	(
		"fullName"
	),
	"title" => "Интернет-шлюз"
);

$groups["WABEntity"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(

	),
	"fields" => array
	(
		"systemName",
		"authorName",
		"modifications",
		"dateCreated",
		"deletionMark"
	),
	"title" => "Сущность"
);
?>