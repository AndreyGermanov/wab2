<?php

$fields["documentProductIncomeTable"] = array(
	"base" => "arrayField",
	"params" => array("title" => "Таблица прихода товаров на склад"),
	"name" => "documentProductIncomeTable",
	"collection" => "fields",
	"file" => __FILE__
);

$models["DocumentProductIncome"] = array("metaTitle" => "Приход товара на склад",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "DocumentProductIncome",
												   "firm" => "firm",
												   "contragent" => "referenceContragent",														
												   "sklad" => "placeDepartment",
												   "place" => "referencePlace",
												   "table" => "documentProductIncomeTable"
);
?>