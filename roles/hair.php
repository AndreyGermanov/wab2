<?php
$roles["sales"] =
array
(
	"title" => "Продажи",
	"file" => __FILE__,
	"name" => "sales",
	"collection" => "roles",
	"visible" => "false",

	"Document" => array
	(
		"fieldDefaults" => array
		(
			"manager" => "[userObject]"
		),
		"canPrint" => "[registered]",
		"canDelete" => "[notRegistered]",			
		"canRegister" => "true",
		"canUnregister" => "false",
		"canViewMovements" => "false",				
		"canAddGroup" => "false",			
		"canCreateBy" => "false",		
		"canPrintList" => "false",
		"canSaveListSettings" => "false",
		"canSetProperties" => "false",	
		"canGlobalSearch" => "false",	
		"canAddCopy" => "false",		
		"numbering" => "periodicYear"
	),
		
	"CRMMenu" => array (
		"referenceItems" => array (
			"contragentsReferenceMenu",
			"usersReferenceMenu"
		),
		"documentItems" => array (
			"quickSaleDocumentsMenu"
		),
		"reportItems" => array(
			"registryDiscountCardsReportsMenu"
		),
		"settingsItems" => array(
			"firmSettingsMenu",
			"deleteMarkedObjectsMenu"
		),
		"fields" => array (
			"helpGuideId" => "rozn"				
		)
	),
		
	"Reference" => array
	(
		"canAddCopy" => "false",
		"canPrintList" => "false",
		"canSetProperties" => "false",
		"canSaveListSettings" => "false",
		"canViewLinks" => "false",
		"canEditLinks" => "false",
		"canSetProperties" => "false",
		"canSaveListSettings" => "false",
		"canAddGroup" => "false",						
		"canCreateBy" => "false",
		"canGlobalSearch" => "false",			
		"fieldDefaults" => array
		(
			"manager" => "[userObject]"
		),
		"showGroupField" => "false" 
	),
			
	"WindowManager" => array
	(
		"fields" => array (
			"showMainMenu" => "0",
			"showInfoPanel" => "0",
			"autorun" => array("EntityGroupsTable_DocFlowApplication_Docs_List" => "Мои дела"),
			"autorunStr" => json_encode(array("EntityGroupsTable_DocFlowApplication_Docs_List" => "Мои дела"))
		)
	),
		
	"ReferenceContragents" => array
	(
		"fieldDefaults" => array("type" => "2"),
		"tabs" => array("simpleInfo","quickSales","discountReport"),
		"active_tab" => "simpleInfo",
		"fields" => array (
			"conditionFields" => "familyName~Фамилия~string,firstName~Имя~string,secondName~Отчество~string,discountCardNumber~Номер дисконтной карты~string,referrerNumber~Номер реферрера~string,mobileNumber~Номер мобильного~string,email~Адрес E-Mail~string,contactInfo~Прочая информация~string",
			"fieldList" => "familyName Фамилия~firstName Имя~secondName Отчество~discountCardNumber № дисконтной карты~referrerNumber Номер реферрера~mobileNumber Номер мобильного~email E-Mail",
			"printFieldList" => "familyName Фамилия~firstName Имя~secondName Отчество~discountCardNumber № дисконтной карты~referrerNumber Номер реферрера~mobileNumber Номер мобильного~email E-Mail",
			"sortOrder" => "familyName ASC",
			"helpGuideId" => "rozn_6"
		)				
	),
	
	"EntityGroupsTable" => array (
		"tabTitles" => array("ReferenceContragents","DocumentQuickSale")
	),
		
	"ReferenceUsers" => array
	(
		"tabs" => array("main"),
		"accountControl" => "advancedAccount",
		"showGroupField" => "false",
		"canSaveListSettings" => "false",
		"canSetProperties" => "false",
		"fields" => array (
			"helpGuideId" => "rozn_5"
		)
	),
		
	"DeleteMarkedObjectsWindow" => array (
		"fields" => array (
			"helpGuideId" => "rozn_7.1"
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
		"rootPath" => "/data/share/"
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
		"showFilesTab" => "false",
		"showNotesTab" => "false",
		"showLinksTab" => "false",
		"showTagsTab" => "false",
		"canEditProfile" => "false",
		"showSaveButton" => "false",
		"canEdit" => "true",
		"fields" => array (
			"createDisplayStyle" => "none"
		)
	),
						
	"DocumentQuickSale" => array (
		"canEdit" => "[notRegistered]",
		"tabs" => array("main"),
		"fields" => array (
			"registerConfirmation" => "true",
			"documentTable" => "Услуга~~1~",
			"createDisplayStyle" => "none"
		),
		"fieldAccess" => array ("*" => "write", "docDate" => "read", "number" => "read", "manager" => "read")
	)		
);

$roles["managersales"] = array (
	"title" => "Менеджер",
	"file" => __FILE__,
	"name" => "managersales",
	"collection" => "roles",
	"visible" => "true"
);

$roles["adminsales"] = array(
		
	"title" => "Администратор",
	"file" => __FILE__,
	"name" => "adminsales",
	"collection" => "roles",
	"visible" => "true",
		
	"WABEntity" => array (
		"showSaveButton" => "true"
	),

	"WindowManager" => array (
		"fields" => array (
			"showMainMenu" => "1",
			"autorun" => array("EntityGroupsTable_DocFlowApplication_Docs_List" => "Мои дела"),
			"autorunStr" => json_encode(array("EntityGroupsTable_DocFlowApplication_Docs_List" => "Мои дела"))
		)
	),
		
	"Reference" => array (
		"canAddCopy" => "true",
		"canPrintList" => "true",
		"canSetProperties" => "true",
		"canSaveListSettings" => "true",
		"canViewLinks" => "true",
		"canEditLinks" => "true",
		"canSetProperties" => "true",
		"canGlobalSearch" => "true"
	),

	"ReferenceUsers" => array
	(
		"tabs" => array("main"),
		"accountControl" => "advancedAccount",
		"showGroupField" => "false",
		"canSaveListSettings" => "false",
		"canSetProperties" => "false"
	),

	"Document" => array (
		"canEdit" => "true",
		"canDelete" => "true",
		"canPrintList" => "true",
		"canViewMovements" => "true",
		"canRegister" => "true",
		"canUnregister" => "true",
		"canSetProperties" => "true",
		"canGlobalSearch" => "true",
		"canSaveListSettings" => "true"
	)
);

$roles["supersales"] = array(
		"title" => "Суперюзер",
		"file" => __FILE__,
		"name" => "supersales",
		"collection" => "roles",
		"visible" => "false",
		"WindowManager" => array (
				"fields" => array (
						"showMainMenu" => "1",
						"autorun" => array("EntityGroupsTable_DocFlowApplication_Docs_List" => "Мои дела"),
						"autorunStr" => json_encode(array("EntityGroupsTable_DocFlowApplication_Docs_List" => "Мои дела")),
						"showInfoPanel" => "1"
				)
		)
);