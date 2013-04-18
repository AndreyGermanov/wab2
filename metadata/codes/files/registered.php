global $Objects;
$obj = $Objects->get($input["object_id"]);
$name = array_pop(explode("_",$input["object_id"]));
if (!ctype_digit($name))
	return "false";
$obj->load();
if ($obj->registered)
	return "true";
else
	return "false";