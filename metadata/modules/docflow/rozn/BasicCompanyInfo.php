<?php
$fields["directorName"] = array ("base" => "stringField", "params" => array("title" => "Директор"),"collection" => "fields", "name" => "directorName", "file" => __FILE__);
$fields["buhgalterName"] = array ("base" => "stringField", "params" => array("title" => "Главный бухгалтер"),"collection" => "fields", "name" => "buhgalterName", "file" => __FILE__);
$fields["kassirName"] = array ("base" => "stringField", "params" => array("title" => "Кассир"), "collection" => "fields", "name" => "kassirName", "file" => __FILE__);
$fields["firmName"] = array ("base" => "stringField", "params" => array("title" => "Организация"),"collection" => "fields", "name" => "firmName", "file" => __FILE__);
$fields["firmOKPO"] = array ("base" => "stringField", "params" => array("title" => "Код по ОКПО"),"collection" => "fields", "name" => "firmOKPO", "file" => __FILE__);
$fields["kassaPrihodOsnovanie"] = array ("base" => "textField", "params" => array("title" => "Основание для приходного ордера"),"collection" => "fields", "name" => "kassaPrihodOsnovanie", "file" => __FILE__);
$fields["referralDiscount"] = array("base" => "integerField", "params" => array("title" => "Скидка реферала (руб.)"),"collection" => "fields", "name" => "referralDiscount", "file" => __FILE__);
$fields["referrerDiscounts"] = array("base" => "stringField", "params" => array("title" => "Скидки реферреров (%, через запятую, начиная от первого уровня и до последнего"),"collection" => "fields", "name" => "referrerDiscounts", "file" => __FILE__);
$fields["smsTemplate"] = array("base" => "stringField", "params" => array("title" => "Шаблон SMS-сообщения"),"collection" => "fields", "name" => "smsTemplate", "file" => __FILE__);
$fields["emailTemplate"] = array("base" => "textField", "params" => array("title" => "Шаблон Email-сообщения", "control_type" => "tinyMCE"),"collection" => "fields", "name" => "emailTemplate", "file" => __FILE__);
$fields["smsFrom"] = array("base" => "stringField", "params" => array("title" => "Мобильный отправителя"),"collection" => "fields", "name" => "smsFrom", "file" => __FILE__);
$fields["emailFrom"] = array("base" => "stringField", "params" => array("title" => "Email-адрес отправителя"),"collection" => "fields", "name" => "emailFrom", "file" => __FILE__);
$fields["emailSubject"] = array("base" => "stringField", "params" => array("title" => "Тема письма"),"collection" => "fields", "name" => "emailSubject", "file" => __FILE__);

$groups["basicCompanyMain"] = array("name" => "main", "collection" => "groups", "file" => __FILE__, "metaTitle" => "Основное", "fields" => array("directorName","buhgalterName","kassirName","firmName","firmOKPO","kassaPrihodOsnovanie"));
$groups["basicCompanyDiscounts"] = array("name" => "main", "collection" => "fields", "file" => __FILE__, "metaTitle" => "Скидки", "fields" => array("referralDiscount","referreDiscounts"));
$groups["basicCompanyTemplates"] = array("name" => "main", "collection" => "fields", "file" => __FILE__, "metaTitle" => "Скидки", "fields" => array("smsTemplate","emailTemplate","smsFrom","emailFrom"));
$groups["basicCompany"] = array("metaTitle" => "Сведения об организации", "name" => "basicCompany", "file" => __FILE__, "collection" => "groups", "groups" => array("basicCompanyMain","basicCompanyDiscounts","basicCompanyTemplates"));

$models["BasicCompanyInfo"] = array(
	"name" => "BasicCompanyInfo",
	"collecion" => "models",
	"file" => __FILE__,
	"metaTitle" => "Сведения об организации",
	"groups" => array("basicCompanyMain","basicCompanyDiscounts","basicCompanyTemplates"),
	"directorName" => "directorName",
	"buhgalterName" => "buhgalterName",
	"kassirName" => "kassirName",
	"firmName" => "firmName",
	"firmOKPO" => "firmOKPO",
	"kassaPrihodOsnovanie" => "kassaPrihodOsnovanie",
	"referralDiscount" => "referralDiscount",
	"referrerDiscounts" => "referrerDiscounts",
	"smsFrom" => "smsFrom",
	"smsTemplate" => "smsTemplate",
	"emailFrom" => "emailFrom",
	"emailSubject" => "emailSubject",
	"emailTemplate" => "emailTemplate"					
);