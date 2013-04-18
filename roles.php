<?php
	$roles["patient"] = array("title" => "Пациент",
						   "file" => __FILE__,
						   "name" => "patient",
						   "collection" => "roles",
			
						   "Reference" => array(
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
						   						"canUnfilter" => "true"
						   					  ),
							"DocumentBloodAnalyze" => array(
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
												"fieldDefaults" => array("patient" => "[userPatient]"),
												"fieldAccess" => array("patient" => "read", "*" => "write")
						   					  ),
			
							 "ReferencePatients" => array(
							 						//"listFilter" => "@name IN ([isAccountPatientCondition])"
							 						),
						   						
							 "ReportBloodAnalyze" => array("fieldAccess" => array("patient" => "write", "*" => "write"),
				  					  					    "fieldDefaults" => array("patient" => "[userPatient]")
														   ),
							 "InfoPanel" => array("eventTypes" => array("USER_ONLINE","USER_OFFLINE"))
			
	);
	
	$roles["patientExpert"] = array("title" => "Пациент-эксперт",
									 "file" => __FILE__,
									 "name" => "patientExpert",
									 "collection" => "roles",			
									 "BloodAnalyzeWorkplace" => array("expert" => "true"),
							 "WABEntity" => array("canEdit" => "true",
						  						"fieldAccess" => array("*" => "write")
				  			 ),
							 "FileManager" => array("rootPath" => "/data/share/files")				
	);
	
	$roles["doctor"] = array("title" => "Врач",
							  "file" => __FILE__,
							  "name" => "patientExpert",
							  "collection" => "roles",
	  						  "DocumentBloodAnalyze" => array(
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
						   		"ReferencePatients" => array(
						   						"canRead" => "true",
						   						"canEdit" => "true",
						   						"canDelete" => "true",
						   						"canUndelete" => "true",
						   						"canAdd" => "true",
						   						"canAddCopy" => "true",
						   						"canRegister" => "true",
						   						"canUnregister" => "false",
						   						"canPrintList" => "false",
						   						"canPrint" => "false",
						   						"canSetProperties" => "false",
						   						"canSaveListSettings" => "false",
						   						"canFilter" => "false",
						   						"canUnfilter" => "false"
						   		),
								"WABEntity" => array("canEdit" => "true",
						  						"fieldAccess" => array("*" => "write")
								)
	);	
	
	$roles["base"] = array("title" => "Врач",
							  "file" => __FILE__,
							  "name" => "base",
							  "collection" => "roles",
							  "WABEntity" => array("canEdit" => "true",
												    "fieldAccess" => array("*" => "write")
							  		               ),			
							  "InfoPanel" => array("eventTypes" => array("USER_ONLINE","USER_OFFLINE")),
							  "FileManager" => array("rootPath" => "/data")
	);
	
	$roles["manager"] = array("title" => "Менеджер",
							   "file" => __FILE__,
							   "name" => "manager",
							   "collection" => "roles",
							   "Document" => array("fieldDefaults" => array("manager" => "[userObject]"),"canRegister" => "true","canUnregister" => "true"),
							   "Reference" => array("fieldDefaults" => array("manager" => "[userObject]"))
	);
?>