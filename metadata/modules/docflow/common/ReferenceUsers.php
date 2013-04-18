<?php
$fields["userParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceUsers",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Пользователи",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Фамилия~fistName Имя~secondName Отчество",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceUsers_DocFlowApplication_Docs_"),
		"name" => "userParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["firstName"] = array("base" => "stringField",
						  "params" => array("title" => "Имя"),
						  "name" => "firstName",
						  "file" => __FILE__,
					      "collection" => "fields"
		
);

$fields["secondName"] = array("base" => "stringField",
							  "params" => array("title" => "Отчество"),
							  "name" => "secondName",
							  "file" => __FILE__,
						      "collection" => "fields"
);

$fields["userEmailAddress"] = array("base" => "stringField",
		"params" => array("title" => "Адрес E-mail"),
		"name" => "userEmailAddress",
		"file" => __FILE__,
		"collection" => "fields"
);

$fields["idDocumentType"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceIdDocumentTypes",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Документы удостоверения личности",
				"editorType" => "WABWindow",
				"title" => "Документ удостоверения личности",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceIdDocumentTypes_DocFlowApplication_Docs_"),
		"name" => "idDocumentType",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["birthCity"] = array("base" => "stringField",
							 "params" => array("title" => "Город рождения"),
							  "name" => "birthCity",
							  "file" => __FILE__,
						      "collection" => "fields"
);

$fields["idDocumentSeries"] = array("base" => "stringField",
							 		"params" => array("title" => "Серия документа, удостоверяющего личность"),
									"name" => "idDocumentSeries",
									"file" => __FILE__,
								    "collection" => "fields"
);

$fields["idDocumentNumber"] = array("base" => "stringField",
						  			"params" => array("title" => "Номер документа, удостоверяющего личность"),
									"name" => "idDocumentNumber",
									"file" => __FILE__,
								    "collection" => "fields"
);

$fields["idDocumentDate"] = array("base" => "stringField",
								   "params" => array("title" => "Номер документа, удостоверяющего личность"),
									"name" => "idDocumentDate",
									"file" => __FILE__,
								    "collection" => "fields"
);

$fields["hasAdminAccess"] = array("base" => "booleanField",
								  "params" => array("title" => "Пользователь панели управления",
								  					 "show_description" => "true",
								  					 "description" => "Пользователь панели управления")
							);

$fields["accountPassword"] = array("base" => "stringField",
		"params" => array("title" => "Пароль", "password" => "true"),
		"name" => "accountPassword",
		"file" => __FILE__,
		"collection" => "fields"
);

$fields["idDocumentPlace"] = array("base" => "textField",
									"params" => array("title" => "Место выдачи документа, удостоверяющего личность", "width" => "100%", "height" => "100%"),
									"name" => "idDocumentPlace",
									"file" => __FILE__,
								    "collection" => "fields"
);

$fields["firm"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceFirms",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Организация",
				"editorType" => "WABWindow",
				"title" => "Организация",
				"fieldList" => "title Наименование~phones Телефоны~defaultEmail.email AS email Email",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceFirms_DocFlowApplication_Docs_"),
		"name" => "firm",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["department"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceDepartments",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Отдел",
				"editorType" => "WABWindow",
				"title" => "Подразделение",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceDepartments_DocFlowApplication_Docs_"),
		"name" => "department",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["appointment"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceAppointments",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Должность",
				"editorType" => "WABWindow",
				"title" => "Должность",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@parent IS NOT EXISTS",
				"parentEntity" => "ReferenceAppointments_DocFlowApplication_Docs_"),
		"name" => "appointment",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["emailAccount"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceEmailAccounts",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Учетная запись Email",
				"editorType" => "WABWindow",
				"title" => "Учетная запись электронной почты",
				"fieldList" => "email Email~title Описание",
				"sortOrder" => "email ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceEmailAccounts_DocFlowApplication_Docs_"),
		"name" => "emailAccount",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["userPhoto"] = array("base" => "fileField",
		"params" => array("title" => "Фото", "control_type" => "image", "show_preview" => "true"),
		"name" => "userPhoto",
		"file" => __FILE__,
		"collection" => "fields"
);

$groups["ReferenceUsers"] = array("title" =>"Пользователи",
		"name" => "ReferenceUsers",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array(),
		"groups" => array("ReferenceUsersMain","ReferenceUsersPersonal","ReferenceUsersWork")
);

$groups["ReferenceUsersMain"] = array("title" => "Основное",
									   "name" => "ReferenceUsersMain",
									   "file" => __FILE__,
									   "collection" => "groups",
									   "fields" => array("firstName","secondName","title","account","emailAccount","photo")
);

$groups["ReferenceUsersPersonal"] = array("title" => "Личное",
									       "name" => "ReferenceUsersPersonal",
										   "file" => __FILE__,
									  	   "collection" => "groups",
										   "fields" => array("birthDate","birthCity","idDocumentType","idDocumentSeries","idDocumentNumber","idDocumentDate","idDocumentPlace")	
											
);

$groups["ReferenceUsersWork"] = array("title" => "Работа",
									  "name" => "ReferenceUsersWork",
									  "file" => __FILE__,
									  "collection" => "groups",
									  "fields" => array("firm","department","appointment")
);

$groups["ReferenceUsersContacts"] = array("title" => "Контакты",
		"name" => "ReferenceUsersContacts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("postalAddress","phones")
);

$models["ReferenceUsers"] = array("metaTitle" => "Пользователи",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceUsers",
								   "isGroup" => "isGroup",
								   "parent" => "userParent",
								   "account" => "account",
								   "accountPassword" => "accountPassword",
								   "accountPassword2" => "accountPassword",
								   "accountRole" => "account",
								   "photo" => "userPhoto",
								   "title" => "title",
								   "firstName" => "firstName",
								   "secondName" => "secondName",			
								   "fullName" => "fullName",			
								   "birthDate" => "birthDate",
								   "birthCity" => "birthCity",
								   "idDocumentType" => "idDocumentType",
								   "idDocumentSeries" => "idDocumentSeries",
								   "idDocumentNumber" => "idDocumentNumber",
							       "idDocumentDate" => "idDocumentDate",
								   "idDocumentPlace" => "idDocumentPlace",
								   "firm" => "firm",
								   "department" => "department",
								   "appointment" => "appointment",
								   "postalAddress" => "postalAddress",
								   "hasAdminAccess" => "hasAdminAccess",
								   "phones" => "phones",
								   "defaultEmail" => "emailAccount",
								   "userEmailAddress" => "userEmailAddress",
								   "groups" => array("ReferenceUsersMain","ReferenceUsersPersonal","ReferenceUsersWork")
);
?>