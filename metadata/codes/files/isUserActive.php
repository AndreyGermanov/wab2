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
if ($user->active)
	return l10n("Да");
else
	return l10n("Нет");