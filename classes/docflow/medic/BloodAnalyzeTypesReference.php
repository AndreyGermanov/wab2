<?php
/**
 * Класс справочника типов анализа крови
 *
 * @author andrey
 */
class BloodAnalyzeTypesReference extends Reference {
	
    function construct($params) {        
        parent::construct($params);
        global $Objects;
        
        $this->persistedFields["title"] = array("type" => "string",
        		                                "params" => array("type" => "string",
        		                                		           "title" => "Наименование"
        		                                )
        );
        $this->persistedFields["defs"] = array("type" => "text",
        		                                "params" => array("type" => "text",
        		                                		           "title" => "Показатели"
        		                                )
        );
        $this->persistedFields["helpTopic"] = array("type" => "string",
        		                                     "params" => array("type" => "string",
        		                                		                "title" => "Номер раздела в справочной системе"
        		                                     )
        );
        $this->fieldList = "title Показатель";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->conditionFields = "title~Показатель~string";        
        $this->sortOrder = "title ASC";
        $this->renderTemplate = "templates/docflow/medic/BloodAnalyzeTypesReference.html";
        $this->template = "renderForm";
        $this->handler = "scripts/handlers/docflow/medic/BloodAnalyzeTypesReference.js";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules;
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/addrbook.gif";       
        $this->width="650";
        $this->height="400";
 		$this->clientClass = "BloodAnalyzeTypesReference";
        $this->parentClientClasses = "Reference~Entity";        
        $this->classTitle = "Тип анализа крови";
        $this->classListTitle = "Типы анализа крови";
    }            
    
    function getArgs() {
        $result = parent::getArgs();
        $result["{definitionsSafe}"] = str_replace(",","~",$this->defs);
        return $result;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "showListHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function showListHook($arguments=null) {
    	$object = $this;
        $object->overrided='width,height';                
        $object->width=650;$object->height=450;                
        $object->className="*BloodAnalyzeTypesReference*";
        $object->defaultClassName="BloodAnalyzeTypesReference";
        $object->loaded=true;
        $object->template="templates/docflow/core/ReferenceList.html";
        $object->handler="scripts/handlers/docflow/core/Reference.js";
        $object->title="Типы анализа крови";
	}    
}
?>