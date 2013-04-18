$user = @$input["user"];
if ($user=="") {
	$app = $Objects->get("Application");
	if (!$app->initiated)
		$app->initModules();
	$user = $app->User;
}
return "'".$user."'";