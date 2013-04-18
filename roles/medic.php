<?php
$roles["patient"] =
array
(
		"title" => "Пациент",
		"file" => __FILE__,
		"name" => "patient",
		"collection" => "roles",
		"visible" => "false",
		"Reference" => array
		(
				"canRead" => "true",
				"canEdit" => "true",
				"canDelete" => "true",
				"canUndelete" => "true",
				"canAdd" => "true",
				"canAddCopy" => "true",
				"canRegister" => "true",
				"canUnregister" => "true",
				"canPrintList" => "true",
				"canPrint" => "true",
				"canSetProperties" => "true",
				"canSaveListSettings" => "true",
				"canFilter" => "true",
				"canUnfilter" => "true",
				"canViewLinks" => "true",
				"canEditLinks" => "true"
		),
		
		"WABEntity" => array(
			"fields" => array(
				"scanCodeGenEvent" => "0",
				"scanCodeShowForm" => "1"
			)
		),
		
		"DocumentBloodAnalyze" => array
		(
				"canRead" => "[isAccountPatient]",
				"canEdit" => "[isAccountPatient]",
				"canDelete" => "[isAccountPatient]",
				"canUndelete" => "[isAccountPatient]",
				"canAdd" => "true",
				"canAddCopy" => "true",
				"canRegister" => "true",
				"canUnregister" => "true",
				"canPrintList" => "true",
				"canPrint" => "true",
				"canSetProperties" => "true",
				"canSaveListSettings" => "true",
				"canFilter" => "true",
				"canUnfilter" => "true",
				"listFilter" => "@patient.@name IN ([isAccountPatientCondition])",
				"fieldDefaults" => array
				(
						"patient" => "[userPatient]",
						"number" => "15",
						"comment" => "Новое значение"
				),
				"fieldAccess" => array
				(
						"*" => "[empty]",
						"comment" => "write"
				),
				"canEditProfile" => "true",
				"notifyUsers" => "justMy"
		),
		"ReferencePatients" => array
		(

		),
		"ReportBloodAnalyze" => array
		(
				"fieldAccess" => array
				(
						"patient" => "write",
						"*" => "write"
				),
				"fieldDefaults" => array
				(
						"patient" => "[userPatient]"
				)
		),
		"InfoPanel" => array
		(
				"eventTypes" => array
				(
						"USER_ONLINE",
						"USER_OFFLINE"
				)
		),
		"DocumentContragentRequest_88" => array
		(
				"canRead" => "true",
				"canEdit" => "true",
				"canEditProfile" => "true",
				"notifyUsers" => "true",
				"fieldAccess" => array
				(
						"dateCreated" => "write",
						"modifications" => "read",
						"number" => "write",
						"docDate" => "write",
						"registered" => "write",
						"title" => "write",
						"*" => "write"
				)
		),
		"ReferenceContragents" => array
		(
				"canRead" => "true",
				"canEdit" => "false",
				"canEditProfile" => "true",
				"notifyUsers" => "justMy",
				"fieldAccess" => array
				(

				),
				"canDelete" => "true",
				"canUndelete" => "true"
		),
		"DocumentOrder" => array
		(
				"canEditProfile" => "true",
				"notifyUsers" => "justMy",
				"canViewLinks" => "true"
		),
		"ReferenceBankAccounts" => array
		(
				"fieldDefaults" => array
				(

				)
		)
);

$roles["doctor"] =
array
(
		"title" => "Врач",
		"file" => __FILE__,
		"name" => "patientExpert",
		"collection" => "roles",
		"visible" => "false",
		"DocumentBloodAnalyze" => array
		(
				"canRead" => "true",
				"canEdit" => "false",
				"canDelete" => "false",
				"canUndelete" => "true",
				"canAdd" => "false",
				"canAddCopy" => "true",
				"canRegister" => "true",
				"canUnregister" => "true",
				"canPrintList" => "true",
				"canPrint" => "true",
				"canSetProperties" => "true",
				"canSaveListSettings" => "true",
				"canFilter" => "true",
				"canUnfilter" => "true"
		),
		"WABEntity" => array
		(
				"canEdit" => "true",
				"fieldAccess" => array
				(
						"*" => "write"
				)
		)
);

$roles["patientExpert"] =
array
(
		"title" => "Пациент-эксперт",
		"file" => __FILE__,
		"name" => "patientExpert",
		"collection" => "roles",
		"visible" => "false",
		"BloodAnalyzeWorkplace" => array
		(
				"expert" => "true"
		),
		"WABEntity" => array
		(
				"canEdit" => "true",
				"fieldAccess" => array
				(
						"*" => "write"
				)
		),
		"FileManager" => array
		(
				"rootPath" => "/data/share/files"
		)
);