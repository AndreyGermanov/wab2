	$contragent = @$input["contragent"];
	$contragent = $Objects->get($contragent);
	if (is_object($contragent))
		$contragent->load();
	if (is_object($contragent->defaultBankAccount))
		return $contragent->defaultBankAccount->getId();
	else
		return "";