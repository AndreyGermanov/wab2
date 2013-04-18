<?php
/**
 * Класс справочника единиц измерения
 *
 * @author andrey
 */
class ReferenceDimensions extends Reference {
    function construct($params) {        
        parent::construct($params);
        global $Objects;
        $this->persistedFields["name"] = array("type" => "string",
											    "params" => array("type" => "string",
											    		           "title" => "Наименование"
											    )											    		    											    		
		);
        $this->persistedFields["title"] = array("type" => "string",
        										 "params" => array("type" => "string",
        														    "title" => "Заголовок"
        										)
        );        
        $this->fieldList = "title Наименование";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->conditionFields = "title~Наименование~string";        
        $this->sortOrder = "title ASC";
        $this->renderTemplate = "templates/docflow/core/ReferenceDimensions.html";
        $this->template = "renderForm";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules;
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/addrbook.gif";       
        $this->width="650";
        $this->height="400";
        $this->clientClass = "ReferenceDimensions";
        $this->parentClientClasses = "Reference~Entity";        
    }  
}
?>