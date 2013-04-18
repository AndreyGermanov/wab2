global $Objects;
$obj = $Objects->get($input["object_id"]);
$user = @$input["user"];
if ($user=="") {
	$app = $Objects->get("Application");
	if (!$app->initiated)
		$app->initModules();
	$user = $app->User;
};
$apacheUser = $Objects->get("ApacheUser_".$obj->module_id."_".$user);
if (!$apacheUser->loaded)
	$apacheUser->load();

$items = $apacheUser->getLinkedDocflowObjects($obj->module_id,$input["params"]["className"]);
if (count($items)==1) {
	$arr = explode("_",$items[0]->getId());
	return array_shift($arr)."_".array_pop($arr);
}
return "";