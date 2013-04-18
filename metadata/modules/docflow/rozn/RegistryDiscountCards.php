<?php
$fields["discountCardNumber"] = array("base" => "stringField", "name" => "discountCardNumber", "collection" => "fields", "params" => array("title" => "Номер дисконтной карты"));
$fields["summa"] = array("base" => "decimalField", "name" => "summa", "collection" => "fields", "params" => array("title" => "Сумма"));

$models["RegistryDiscountCards"] = array("metaTitle" => "Движения по дисконтным картам",
												   "file" => __FILE__,
												   "collection" => "models",
												   "name" => "RegistryDiscountCards",
												   "contragent" => "referenceContragent",
												   "discountCardNumber" => "discountCardNumber",
												   "summa" => "summa"
);
?>