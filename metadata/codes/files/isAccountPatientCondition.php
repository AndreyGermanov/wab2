$user = @$input["user"];
$doc = $Objects->get(@$input["object_id"]);
if ($user=="") {
	$app = $Objects->get("Application");
	if (!$app->initiated)
		$app->initModules();
	$user = $app->User;
}
$user = $Objects->get("ApacheUser_".$doc->module_id."_".$user);
if (is_object($user)) {
	$patients = $user->getLinkedDocflowObjects($doc->module_id,"ReferencePatients");
	$arr = array();
	foreach($patients as $value) {
		$arr[] = "'".$value->name."'";
	}
	return implode(",",$arr);				
} 
return "";