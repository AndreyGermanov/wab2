<?php
$tags["Важность"] = 
array
(
	"field" => "importantList",
	"value" => "Важный",
	"file" => __FILE__
);

$tags["Тип"] = 
array
(
	"field" => "textField",
	"value" => "Документ",
	"file" => __FILE__
);

$tags["Размер"] = 
array
(
	"field" => "integerField",
	"value" => "0",
	"file" => __FILE__
);

$tags["Роль"] = 
array
(
	"field" => "dateStart",
	"file" => __FILE__,
	"value" => ""
);

$tagGroups["Основные"] = 
array
(
	"Важность",
	"Тип",
	"file" => __FILE__
);

$tagGroups["Файл"] = 
array
(
	"Тип",
	"Важность",
	"Размер",
	"file" => __FILE__
);
?>