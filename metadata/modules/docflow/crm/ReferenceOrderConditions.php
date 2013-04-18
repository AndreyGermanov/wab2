<?php
$fields["orderNumber"] = array("base" => "integerField",
							  "params" => array("title" => "№ п.п."),
							  "name" => "orderNumber",
							  "file" => __FILE__,
							  "collection" => "fields"
);

$groups["ReferenceOrderConditions"] = array("title" =>"Состояния заказов",
		"name" => "ReferenceOrderConditions",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("orderNumber")
);

$models["ReferenceOrderConditions"] = array("metaTitle" => "Состояния заказа",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceOrderConditions",
								   "isGroup" => "isGroup",
								   "title" => "title",
								   "orderNumber" => "orderNumber"	
);
?>