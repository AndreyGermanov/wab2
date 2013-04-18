global $Objects;

if (isset($input["user"]) and trim($input["user"]) != "") 
	$user = $Objects->get("ApacheUser_".$this->module_id."_".trim($input["user"]));
else {
	$app = $Objects->get("Application");
	if (!$app->initiated)
		$app->initModules();
	$user = $Objects->get("ApacheUser_".$this->module_id."_".$app->User);	
}
$user->load();
if ($user->isActive)
	$active_time = $user->active_seconds;
else
	$active_time = 0;
if ($active_time==0)
	return "";
$returnType = @$input["returnType"];
switch ($returnType) {
	case 'string':
		return execAlgo("secondsToDuration",array("seconds" => $active_time));
		break;
	case 'array':
		return execAlgo("secondsToDuration",array("seconds" => $active_time, "returnType" => "array"));
		break;
	default:
		return $active_time;
}