$doc = $Objects->get($input["object_id"]);
$end = array_pop(explode("_",$input["object_id"]));
if ($end=="List" or $end=="")
	return "true";
if (!$doc->loaded)
	$doc->load();
$user = @$input["user"];
if ($user=="") {
	$app = $Objects->get("Application");
	if (!$app->initiated)
		$app->initModules();
	$user = $app->User;
}
if ($user!="" and is_object($doc->patient)) {
	$user = $Objects->get("ApacheUser_".$doc->module_id."_".$user);
	$patients = $user->getLinkedDocflowObjects($doc->module_id,"ReferencePatients");
	if (is_array($patients) and count($patients)>0) {
		foreach ($patients as $value) {
			if ($value->getId()==@$doc->patient->getId())
				return "true";
		}
	}
}
return "false";