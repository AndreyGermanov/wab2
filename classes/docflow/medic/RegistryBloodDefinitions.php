<?php
/**
 * Класс, реализующий регистр значений показателей анализов крови.
 * 
 * Реквизиты регистра:
 * 
 * patient - Пациент (справочник ReferencePatients)
 * analyzeType - Тип анализа (справочник BloodAnalyzeTypesReference)
 * 
 * Измерения регистра:
 * 
 * analyzeDef - Показатель анализа (справочник BloodAnalyzeDefinitionsReference) 
 * analyzeDefValue - Значение показателя анализа (дробное число)
 * 
 * @author andrey
 */
class RegistryBloodDefinitions extends Registry {
		
    function construct($params) {
		parent::construct($params);		
		$this->persistedFields["patient"] = array("type" => "entity",
												   "params" => array("type" => "entity",
												   					  "className" => "ReferencePatients",
												   		              "additionalFields" => "name",
												   		              "show_float_div" => "true",
												   		              "classTitle" => "Пациент",
												   		              "editorType" => "WABWindow",
												   		              "title" => "ФИО",
												   					  "fieldList" => "title ФИО",
												   					  "sortOrder" => "title ASC",
												   					  "width" => "100%",
												   					  "adapterId" => "DocFlowDataAdapter_".$this->module_id."_1",
												   		 			  "parentEntity" => "ReferencePatients_".$this->module_id."_"
												   )	
		);
		$this->persistedFields["analyzeType"] = array("type" => "entity",
												       "params" => array("type" => "entity",
												   					      "className" => "BloodAnalyzeTypesReference",
												   		                  "additionalFields" => "name",
												   		                  "show_float_div" => "true",
												   		                  "classTitle" => "Тип анализа",
												   		                  "editorType" => "WABWindow",
												   		                  "title" => "Тип анализа",
												   					      "fieldList" => "title Тип",
												   					      "sortOrder" => "title ASC",
												   					      "width" => "100%",
												   					      "adapterId" => "DocFlowDataAdapter_".$this->module_id."_1",
												   		 			      "parentEntity" => "BloodAnalyzeTypesReference_".$this->module_id."_"
												       )	
		);
		$this->persistedFields["analyzeDef"] = array("type" => "entity",
												      "params" => array("type" => "entity",
												   					     "className" => "BloodDefinitionsReference",
												   		                 "additionalFields" => "name",
												   		                 "show_float_div" => "true",
												   		                 "classTitle" => "Показатель анализа крови",
												   		                 "editorType" => "WABWindow",
												   		                 "title" => "Тип показателя крови",
												   					     "fieldList" => "title Показатель",
												   					     "sortOrder" => "title ASC",
												   					     "width" => "100%",
												   					     "adapterId" => "DocFlowDataAdapter_".$this->module_id."_1",
												   		 			     "parentEntity" => "BloodDefinitionsReference_".$this->module_id."_"
												       )	
		);
		$this->persistedFields["analyzeDefValue"] = array("type" => "decimal",
				                                           "params" => array("type" => "decimal",
				                                           		              "title" => "Значение показателя крови"
				                                           )
		);
        $this->title = "Значения показателей крови";
        $this->classTitle = "Значения показателей крови";
        $this->dimensions = "analyzeDef";
        $this->recvs = "patient,analyzeType";
        $this->clientClass = "RegistryBloodDefinitions";
        $this->parentClientClasses = "Registry~Entity";
                
        $this->printProfiles["Основной"] = array (
        	"settings" => array (
        		"settingsClass" => "RegistrySettingsDialog"
        	),
      		"Основной" => array (
       			"printFields" => array (
   					"regDate" => array (
						"size" => "10%"
   					),
   					"document" => array (
						"size" => "23%"
   					),
   					"patient" => array (
						"size" => "24%"
   					),        							
   					"analyzeType" => array (
						"size" => "23%"
   					),    					
   					"analyzeDef" => array (
						"size" => "10%",        							
   					), 
   					"analyzeDefValue" => array (
						"size" => "10%"
   					)
       			),
      			"totals" => array (
      				"bgColor" => "#DDDDDD",
      				"fontFace" => "Arial",
      				"fontSize" => "15",
      				"fontWeight" => "bold",
      				"fontColor" => "#000000"      				
      			)      					 
      		)
        );        
    }
}
?>