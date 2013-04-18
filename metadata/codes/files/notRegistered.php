global $Objects;
$obj = $Objects->get($input["object_id"]);
$name = array_pop(explode("_",$input["object_id"]));
if (!ctype_digit($name))
	return "true";
$obj->load();
if ($obj->registered)
	return "false";
else
	return "true";