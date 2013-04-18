<?php	
$groups["docflowCommon"] = array("title" =>"Стандартные объекты документооборота",
		"name" => "docflowCommon",
		"file" => __FILE__,
		"collection" => "groups",
		"groups" => array("ReferenceBanks","ReferenceContragents","ReferenceBankAccounts")
);

$modelGroups["docflowCommon"] = array("title" => "Стандартные объекты документооборота",
		"name" => "docflowCommon",
		"file" => __FILE__,
		"collection" => "modelGroups",
		"groups" => array("ReferenceBanks","ReferenceContragents","ReferenceBankAccounts")
);
?>