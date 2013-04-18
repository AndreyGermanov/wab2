<?php
$roles["manager"] =
array
(
		"title" => "Менеджер",
		"file" => __FILE__,
		"name" => "manager",
		"collection" => "roles",
		"visible" => "false",
		"Document" => array
		(
				"fieldDefaults" => array
				(
						"manager" => "[userObject]"
				),
				"canWrite" => "true",
				"canRegister" => "true",
				"canUnregister" => "true",
				"numbering" => "periodicYear"
		),
		"Reference" => array
		(
				"fieldDefaults" => array
				(
						"manager" => "[userObject]"
				)
		),
		"FileManager" => array
		(
				"fmCanUpload" => "true",
				"fmCanCreateFolder" => "true",
				"fmCanRename" => "true",
				"fmCanCopyMove" => "true",
				"fmCanDelete" => "true",
				"fmCanSetProperties" => "false",
				"rootPath" => "/data/share/files/art"
		),
		"GlobalSearchTable" => array
		(
				"classesList" => array
				(
				),
				"fieldsList" => array
				(

				)
		),
		"DocumentInvoice" => array
		(
				"fieldDefaults" => array
				(
						"includeNDS" => "1",
						"isStamp" => "0"
				)
		),
		
		"CRMMenu" => array
		(
			"menu" => array
			(
				"referencesMenu",
				"documentsMenu",
				"reportsMenu",
				"settingsMenu",
				"helpMenu"
			),

			"reportItems" => array
			(
				"registryReportsMenu"
			),
				
			"settingsItems" => array
			(
				"crmTablesMenu",
				"deleteMarkedObjectsMenu"
			),
			"helpItems" => array
			(
				"referenceMenu",
				"sendRequestMenu"
			)		
		),
		
		"WABEntity" => array (
			"showProfileTab" => "false",
			"canEditProfile" => "false"		
		),

		"EntityGroupsTable" => array (
			"tabTitles" => array 
			(
				"ReferenceContragents",
				"ReferenceContacts",
				"ReferenceEmailAddresses",
				"DocumentContragentRequest",
				"DocumentOrder",
				"ReferenceProducts",
				"DocumentInvoice",
				"ReferenceProjects",
				"DocumentTask",
				"DocumentWorkReport",
				"ReferenceNotes",
				"ReferenceFiles"
			)
		),
		
		"ReferenceContragents" => array (
			"tabs" => array("main","contacts","banks","emails","tags","files","notes","links2","profile")
		)
);

$roles["alex"] = array ( 
	"title" => "Менеджер",
	"file" => __FILE__,
	"name" => "manager",
	"collection" => "roles",
	"visible" => "false",
	"DocumentContragentRequest" => array (		
		"printForms" => array("letter" => "Письмо"),
		"fieldAccess" => array
		(
			"*" => "write",
			"sign" => "write"
		)
	)
);

$roles["sitemanager"] = array (
		"title" => "Менеджер сайта",
		"file" => __FILE__,
		"name" => "sitemanager",
		"collection" => "roles",
		"visible" => "false",
		"ReferenceUsers" => array
		(
			"tabs" => array("main"),
			"accountControl" => "advancedAccount",
			"showGroupField" => "false",
			"canSaveListSettings" => "false",
			"canSetProperties" => "false",
			"fields" => array (
				"fieldList" => "account Логин~title Фамилия~firstName Имя~secondName Отчество",
				"displayHasAdminAccess" => "",
				"displayUserEmailAddress" => "",
				"webServerApplication" => "WebServerApplication_Web",
				"webSite" => "1"
			)
		)		
);

$roles["admin"] =
array
(
	"title" => "Администратор",
	"file" => __FILE__,
	"name" => "admin",
	"collection" => "roles",
	"visible" => "false",
	"Document" => array
	(
		"fieldDefaults" => array
		(
			"manager" => "[userObject]"
		),
		"canRegister" => "true",
		"canUnregister" => "true",
		"numbering" => "periodicYear"
	),
		
	"Reference" => array
	(
		"fieldDefaults" => array
		(
			"manager" => "[userObject]"
		)
	),
	"FileManager" => array
	(
		"fmCanUpload" => "true",
		"fmCanCreateFolder" => "true",
		"fmCanRename" => "true",
		"fmCanCopyMove" => "true",
		"fmCanDelete" => "true",
		"fmCanSetProperties" => "true",
	),
		
	"GlobalSearchTable" => array
	(
		"classesList" => array
		(
		),
		"fieldsList" => array
		(
		)
	),
	
	"WABEntity" => array (
		"showProfileTab" => "false",
		"canEditProfile" => "false"
	),
					
	"DocumentInvoice" => array
	(
		"fieldDefaults" => array
		(
			"includeNDS" => "1",
			"isStamp" => "1"
		)
	),
		
	"DocumentContragentRequest" => array (
		"fieldAccess" => array
		(
			"*" => "write",
			"sign" => "none"
		)
	),
		
	"ReferenceContragents" => array (
		"tabs" => array("main","contacts","banks","emails","tags","files","notes","links2","profile")
	)		
);