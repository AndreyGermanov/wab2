<?php
$fields["resource"] = array("name" => "resource", "collection" => "fields", "base" => "referenceUser", "params" => array("title" => "Ресурс"));

$models["RegistryWorkingTime"] = array("metaTitle" => "Отчеты о работе",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryWorkingTime",
												   "resource" => "referenceUser",
												   "workObject" => "entityObject",
												   "dateStart" => "dateStart",
												   "dateEnd" => "dateEnd",
												   "period" => "period",
												   "firm" => "firm",
												   "reportText" => "description",
);
?>