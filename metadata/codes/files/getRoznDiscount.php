global $Objects;
$obj = $Objects->get($input["obj"]);
$obj->contragent = $Objects->get($input["contragent"]);
return $obj->getContragentDiscount($input["docDate"]-1);