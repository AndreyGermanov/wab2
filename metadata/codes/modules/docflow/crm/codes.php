<?php
$codes["getDefaultBankAccount"] = 
array
(
	"file" => __FILE__,
	"comment" => "Алгоритм возвращает банковский счет по умолчанию для указанного контрагента или фирмы (параметр contragent",
	"metaTitle" => "Алгоритм возвращает банковский счет по умолчанию для указанного контрагента или фирмы (параметр contragent",
	"params" => array
	(
		"contragent" => ""
	)
);

$codes["notRegistered"] =
array
(
		"file" => __FILE__,
		"comment" => "Возвращает true, если переданный в качестве параметра документ не проведен, иначе возвращает false",
		"metaTitle" => "Возвращает true, если переданный в качестве параметра документ не проведен, иначе возвращает false",
		"name" => "notRegistered",
		"params" => array
		(
				"object_id" => ""
		)
);

$codes["registered"] =
array
(
		"file" => __FILE__,
		"comment" => "Возвращает true, если переданный в качестве параметра документ проведен, иначе возвращает false",
		"metaTitle" => "Возвращает true, если переданный в качестве параметра документ проведен, иначе возвращает false",
		"name" => "registered",
		"params" => array
		(
				"object_id" => ""
		)
);
?>