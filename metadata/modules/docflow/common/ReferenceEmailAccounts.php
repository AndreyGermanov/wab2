<?php
$fields["emailAccountParent"] = array("type" => "entity",
		"params" => array("type" => "entity",
				"className" => "ReferenceEmailAccounts",
				"tableClassName" => "DocFlowReferenceTable",
				"additionalFields" => "name,isGroup",
				"show_float_div" => "true",
				"classTitle" => "Учетные записи электронной почты",
				"editorType" => "WABWindow",
				"title" => "Родитель",
				"fieldList" => "title Наименование",
				"sortOrder" => "title ASC",
				"width" => "100%",
				"adapterId" => "DocFlowDataAdapter_DocFlowApplication_Docs_1",
				"condition" => "@isGroup=1 AND @parent IS NOT EXISTS",
				"parentEntity" => "ReferenceEmailAccounts_DocFlowApplication_Docs_"),
		"name" => "emailAccountParent",
		"collection" => "fields",
		"file" => __FILE__
);

$fields["email"] = array("base" => "stringField",
						  "params" => array("title" => "Адрес Email"),
						  "name" => "email",
						  "collection" => "fields",
						  "file" => __FILE__
);

$fields["emailProtocol"] = array("base" => "stringField",
							 "params" => array("type" => "list,imap~pop3|IMAP~POP3", "title" => "Протокол"),
							 "name" => "emailProtocol",
							 "collection" => "fields",
							 "file" => __FILE__
);

$fields["emailHost"] = array("base" => "stringField",
							  "params" => array("title" => "Имя хоста"),
							  "name" => "emailHost",
							  "collection" => "fields",
							  "file" => __FILE__
);

$fields["emailPort"] = array("base" => "integerField",
							  "params" => array("title" => "Порт"),
							  "name" => "emailPort",
							  "collection" => "fields",
							  "file" => __FILE__
);

$fields["emailUsername"] = array("base" => "stringField",
								  "params" => array("title" => "Имя пользователя"),
								  "name" => "emailUsername",
								  "collection" => "fields",
								  "file" => __FILE__
);

$fields["emailPassword"] = array("base" => "stringField",
								  "params" => array("title" => "Пароль", "password" => "true"),
								  "name" => "emailPassword",
								  "collection" => "fields",
							      "file" => __FILE__
);

$fields["isEmailAuth"] = array("base" => "booleanField",
							    "params" => array("title" => "Аутентификация на сервере"),
								"name" => "isEmailAuth",
								"collection" => "fields",
								"file" => __FILE__
);

$fields["emailCryptType"] = array("base" => "stringField",
								 "params" => array("title" => "Шифрование соединения", "type" => "list, ~tls~ssl|Нет~STARTTLS~SSL"),
								 "name" => "emailCryptType",
								 "collection" => "fields",
								 "file" => __FILE__
);

$groups["ReferenceEmailAccounts"] = array("title" =>"Учетные записи электронной почты",
		"name" => "ReferenceEmailAccounts",
		"file" => __FILE__,
		"collection" => "groups",
		"fields" => array("emailAccountParent","email","emailProtocol","emailHost","emailPort","emailUsername","emailPassword","isEmailAuth","emailCryptType")
);

$models["ReferenceEmailAccounts"] = array("metaTitle" => "Учетные записи электронной почты",
								   "file" => __FILE__,
								   "collection" => "models",
								   "name" => "ReferenceEmailAccounts",
								   "isGroup" => "isGroup",
								   "parent" => "emailAccountParent",
								   "email" => "email",
								   "title" => "title",
								   "protocol" => "emailProtocol",
								   "host" => "emailHost",
		 						   "port" => "emailPort",
								   "username" => "emailUsername",
								   "password" => "emailPassword",
		                           "isAuth" => "isEmailAuth",
								   "cryptType" => "emailCryptType"	
);
?>