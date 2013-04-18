$user = @$input["user"];
$object_id = @$input["object_id"];
if ($object_id == "")
	return "false";
if ($user=="") {
	$app = $Objects->get("Application");
	if (!$app->initiated)
		$app->initModules();
	$user = $app->User;
}
$obj = $Objects->get($object_id);
if ($obj->user=="")
	$obj->getAuthor();
if ($obj->user != $user)
	return "false";
else
	return "true";