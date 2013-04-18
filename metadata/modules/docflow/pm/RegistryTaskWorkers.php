<?php

$fields["period"] = array("name" => "period", "collection" => "fields", "base" => "integerField", "params" => array("title" => "Период"));
$fields["dateStart"] = array("name" => "dateStart", "collection" => "fields", "base" => "dateField", "params" => array("title" => "Дата начала"));
$models["RegistryTaskWorkers"] = array("metaTitle" => "Исполнители задач",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryTaskWorkers",
												   "worker" => "worker",
												   "workObject" => "entityObject",
												   "task" => "documentTask",
												   "dateStart" => "dateStart",
												   "dateEnd" => "dateEnd",
												   "period" => "period",
												   "firm" => "firm"
);
?>