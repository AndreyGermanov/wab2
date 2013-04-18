<?php
$groups["ReferenceTaskConditions"] = array("title" =>"Состояния задач",
		"name" => "ReferenceTaskConditions",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("orderNumber")
);

$models["ReferenceTaskConditions"] = array("metaTitle" => "Состояния задач",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceTaskConditions",
								   "isGroup" => "isGroup",
								   "title" => "title",
								   "orderNumber" => "orderNumber"	
);
?>