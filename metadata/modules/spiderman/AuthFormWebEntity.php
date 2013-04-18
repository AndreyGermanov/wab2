<?php
$fields["login"] = array(
	"file" => __FILE__,
	"name" => "login",
	"collection" => "fields",
	"base" => "stringField",
	"params" => array("title" => "Логин")
);

$fields["password"] = array(
		"file" => __FILE__,
		"name" => "password",
		"collection" => "fields",
		"base" => "stringField",
		"params" => array("title" => "Логин", "password" => "true")
);

$models["AuthFormWebEntity"] = array (
	"metaTitle" => "Форма авторизации",
	"collection" => "models",
	"name" => "AuthFormWebEntity",
	"file" => __FILE__,
	"login" => "login",
	"password" => "password"
);