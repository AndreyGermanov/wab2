<?php
$fields["sitesConfigDir"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к каталогу с конфигурационными файлами сайтов Apache"
	),
	"file" => __FILE__
);

$fields["serverConfigDir"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к конфигурационному каталогу Apache"
	),
	"file" => __FILE__
);

$fields["sitesDir"] = 
array
(
	"base" => "pathField",
	"params" => array
	(
		"title" => "Путь к каталогу с Web-сайтами"
	),
	"file" => __FILE__
);

$fields["sitesDB"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл базы данных с информацией о сайтах"
	),
	"file" => __FILE__
);

$fields["templatesDB"] = 
array
(
	"base" => "fileField",
	"params" => array
	(
		"title" => "Файл базы данных с информацией о шаблонах дизайна"
	),
	"file" => __FILE__
);

$fields["showWebSites"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Отображать дерево Web-сайтов"
	),
	"file" => __FILE__
);

$fields["showTemplates"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Отображать дерево шаблонов дизайна"
	),
	"file" => __FILE__
);

$fields["showSystemSettings"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Отображать дерево системных настроек"
	),
	"file" => __FILE__
);

$fields["addWebSites"] = 
array
(
	"base" => "booleanField",
	"params" => array
	(
		"title" => "Разрешать создавать Web-сайты"
	),
	"file" => __FILE__
);

$fields["webSites"] = 
array
(
	"type" => "array",
	"params" => array
	(
		"title" => "Список отображаемых Web-сайтов",
		"itemPrototype" => "type=string",
		"width" => "100%"
	),
	"file" => __FILE__
);

$groups["MystixSpiderman"] = 
array
(
	"title" => "Параметры",
	"fields" => array
	(
		"title",
		"class",
		"image",
		"sitesConfigDir",
		"serverConfigDir",
		"sitesDir",
		"sitesDB",
		"templatesDB",
		"showWebSites",
		"showTemplates",
		"showSystemSettings",
		"addWebSites",
		"webSites"
	),
	"file" => __FILE__,
	"groups" => array
	(

	)
);

$codeGroups["MystixSpiderman"] = 
array
(
	"metaTitle" => "Web-сервер",
	"file" => __FILE__
);

$models["MystixSpiderman"] = 
array
(
	"file" => __FILE__,
	"groups" => array
	(
		"MystixSpiderman"
	),
	"title" => "title",
	"class" => "class",
	"image" => "image",
	"sitesConfigDir" => "sitesConfigDir",
	"serverConfigDir" => "serverConfigDir",
	"sitesDir" => "sitesDir",
	"sitesDB" => "sitesDB",
	"templatesDB" => "templatesDB",
	"showWebSites" => "showWebSites",
	"showTemplates" => "showTemplates",
	"showSystemSettings" => "showSystemSettings",
	"addWebSites" => "addWebSites",
	"webSites" => "webSites",
	"remoteAddress" => "remoteAddress",
	"metaTitle" => "Web-сервер"
);
/*
$modules["MystixSpiderman"] = 
array
(
	"title" => "Сайты",
	"class" => "WebServerApplication_Web",
	"image" => "images/Tabs/sites.gif",
	"sitesConfigDir" => "/etc/apache2/sites-enabled/",
	"serverConfigDir" => "/etc/apache2/",
	"sitesDir" => "/var/www/",
	"sitesDB" => "/etc/WAB2/db/sites.db",
	"templatesDB" => "/etc/WAB2/db/templates.db",
	"showWebSites" => "1",
	"showTemplates" => "1",
	"showSystemSettings" => "1",
	"addWebSites" => "1",
	"name" => "MystixSpiderman",
	"collection" => "modules",
	"webSites" => array
	(
		"All"
	),
	"file" => __FILE__
);
*/
$modules["MystixSpiderman"] =
array
(
		"title" => "Сайты",
		"class" => "WebServerApplication_Web",
		"image" => "images/Tabs/sites.gif",
		"sitesConfigDir" => "/etc/lighttpd/conf-enabled/",
		"serverConfigDir" => "/etc/lighttpd/",
		"serverType" => "lighttpd",
		"sitesDir" => "/var/www/",
		"sitesDB" => "/etc/WAB2/db/sites.db",
		"templatesDB" => "/etc/WAB2/db/templates.db",
		"showWebSites" => "1",
		"showTemplates" => "1",
		"showSystemSettings" => "1",
		"addWebSites" => "1",
		"showUsers" => "1",
		"name" => "MystixSpiderman",
		"collection" => "modules",
		"docflowClass" => "DocFlowApplication_Docs",
		"webSites" => array
		(
				"All"
		),
		"file" => __FILE__
);

?>