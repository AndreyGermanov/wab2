<?php
$fields["submitSuccessTemplate"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitSuccessTemplate",
		"collection" => "fields",
		"params" => array(
				"type" => "entity",
				"title" => "Шаблон по которому страница будет отображаться при успешном завершении",
				"className" => "WebTemplate",
				"treeClassName" => "WebTemplateTree",
				"show_float_div" => "true",
				"classTitle" => "Шаблоны"
		)
);

$fields["submitErrorTemplate"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitErrorTemplate",
		"collection" => "fields",
		"params" => array(
				"type" => "entity",
				"title" => "Шаблон по которому страница будет отображаться при возникновении ошибок",
				"className" => "WebTemplate",
				"treeClassName" => "WebTemplateTree",
				"show_float_div" => "true",
				"classTitle" => "Шаблоны"
		)
);

$fields["submitEmailTemplate"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitEmailTemplate",
		"collection" => "fields",
		"params" => array(
				"type" => "entity",
				"title" => "Шаблон письма",
				"className" => "WebTemplate",
				"treeClassName" => "WebTemplateTree",
				"show_float_div" => "true",
				"classTitle" => "Шаблоны"
		)
);

$fields["submitAfterSaveMethod"] = array("type" => "string", 
		"file" => __FILE__,
		"name" => "submitAfterSaveMethod",
		"collection" => "fields",
		"params" => array(
			"title" => "Функция вызываемая на клиенте после завершения сохранения"
		)
);

$fields["submitEmailAddress"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitEmailAddress",
		"collection" => "fields",
		"params" => array(
			"title" => "Электронный адрес получателя"
		)
);

$fields["submitEmailAddressFrom"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitEmailAddressFrom",
		"collection" => "fields",
		"params" => array(
			"title" => "Электронный адрес отправителя"
		)
);

$fields["submitEmailSubject"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitEmailSubject",
		"collection" => "fields",
		"params" => array(
			"title" => "Тема электронного письма"
		)
);

$fields["submitTitleField"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitTitleField",
		"collection" => "fields",
		"params" => array(
			"title" => "Заголовок письма"
		)
);

$fields["submitFormFields"] = array("type" => "string",
		"file" => __FILE__,
		"name" => "submitFormFields",
		"collection" => "fields",
		"params" => array(
			"title" => "Поля дочерних элементов"
		)
);

$fields["submitSendEmail"] = array("type" => "boolean",
		"file" => __FILE__,
		"name" => "submitSendEmail",
		"collection" => "fields",
		"params" => array(
			"title" => "Отправлять письмо при успешном завершении"
		)
);

$groups["InputWebEntity"] = array(
	"name" => "InputWebEntity",
	"collection" => "groups",
	"file" => __FILE__,
	"title" => "Форма ввода на сайте",
	"fields" => array (
		"submitSuccessTemplate",
		"submitErrorTemplate",
		"submitEmailTemplate",
		"submitAfterSaveMethod",
		"submitEmailAddress",
		"submitEmailAddressFrom",
		"submitEmailSubject",
		"submitTitleField",
		"submitFormFields",
		"submitSendEmail"
	)		
);

$models["InputWebEntity"] = array(
	"name" => "InputWebEntity",
	"collection" => "models",
	"metaTitle" => "Форма ввода на сайте",
	"file" => __FILE__,
	"submitSuccessTemplate" => "submitSuccessTemplate",
	"submitErrorTemplate" => "submitErrorTemplate",
	"submitEmailTemplate" => "submitEmailTemplate",
	"submitAfterSaveMethod" => "submitAfterSaveMethod",
	"submitEmailAddress" => "submitEmailAddress",
	"submitEmailAddressFrom" => "submitEmailAddressFrom",
	"submitEmailSubject" => "submitEmailSubject",
	"submitTitleField" => "submitTitleField",
	"submitFormFields" => "submitFormFields",
	"submitSendEmail" => "submitSendEmail"
);