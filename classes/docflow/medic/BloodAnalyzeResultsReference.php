<?php

/**
 * Класс справочника результатов анализа крови
 *
 * @author andrey
 */
class BloodAnalyzeResultsReference extends Reference {
    
    function construct($params) {        
        parent::construct($params);
        global $Objects;
        $this->persistedFields["name"] = array("type" => "string",
        								        "params" => array("type" => "string",
        								       		               "title" => "Системное имя"
        								       )
        );
        $this->persistedFields["title"] = array("type" => "string",
        								        "params" => array("type" => "string",
        								       		               "title" => "Наименование"
        								       )
        );
        $this->persistedFields["formula"] = array("type" => "text",
        								           "params" => array("type" => "text",
        								       		                  "title" => "Формула",
        								       		                  "control_type" => "editArea",
        								       		                  "width" => "100%",
        								       		                  "height" => "100%"
        								         )
        );
        $this->fieldList = "title Наименование";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->conditionFields = "title~Наименование~string";
        $this->sortOrder = "title ASC";
        $this->classTitle = "Результат анализа крови";
        $this->classListTitle = "Результаты анализа крови";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules;
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/addrbook.gif";       
        $this->renderTemplate = "templates/docflow/medic/BloodAnalyzeResultsReference.html";
        $this->template = "renderForm";
        $this->width="650";
        $this->height="400";
        $this->clientClass = "BloodAnalyzeResultsReference";
        $this->parentClientClasses = "Reference~Entity";        
    }            
    
    function checkData() {
        if (trim($this->formula)=="") {
            $this->reportError("Укажите формулу!");
            return false;
        }
        return parent::checkData();
    }
    
    function getArgs() {
		$this->formula = str_replace("'","xoxoxo",str_replace('"','yoyoyo',$this->formula));
		return parent::getArgs();
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
        $object->width=550;$object->height=450;                
        $object->className="*BloodAnalyzeResultsReference*";
        $object->defaultClassName="BloodAnalyzeResultsReference";
        $object->loaded=true;
        $object->template="templates/docflow/core/ReferenceList.html";
        $object->title="Результаты анализа крови";
	}	
}
?>