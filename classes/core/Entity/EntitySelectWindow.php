<?php
/**
 * Класс отвечает за отображение окна выбора сущности
 *
 * Он отображает список сущностей класса className, которые соответствуют запросу
 * condition. Если задано свойство entityId, то должна быть выделена сущность с
 * таким именем.
 *
 * @author andrey
 */
class EntitySelectWindow extends WABEntity {
    
    function construct($params) {
        parent::construct($params);
        $this->fieldList='title Наименование~sortOrder Порядковый номер~isPublic Опубликовано booleans';
        $this->itemsPerPage = 10;
        $this->hierarchy = true;
        $this->condition = "@parent IS NOT EXISTS";
        $this->persistedFieldsSafe = "";
        $this->sortOrder = "";
        $this->windowWidth = 400;
        $this->windowHeight = 400;        
        $this->template = "templates/core/EntitySelectWindow.html";
        $this->handler = "scripts/handlers/core/EntitySelectWindow.js";
        $this->valueTitle = "";
        $this->entityParentId = "";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $this->skinPath."images/Tree/folder.gif";
        $this->selectGroup = "1";
        $this->tableObject = "";
        $this->clientClass = "EntitySelectWindow";
        $this->parentClientClasses = "Entity";   
        $this->fieldAccess = "";
        $this->fieldDefaults = "";     
    }
    
    function load() {
        $this->loaded = true;
    }
    
    function getPresentation() {
        return "Выбор элемента";
    }
    
    function getArgs() {
    	global $Objects;
    	if ($this->tableClassName=="") {
    		$obj = $Objects->get($this->tableObject);
    		if (method_exists($obj,"getId")) {
    			$this->tableClassName = $obj->entityDataTableClass;
    			$this->fieldList = $obj->fieldList;
    			$this->hierarchy = $obj->hierarchy;
    		}
    	}
    	if (is_object($this->fieldAccess) or is_array($this->fieldAccess))
    		$this->fieldAccess = json_encode($this->fieldAccess);
    	if (is_object($this->fieldDefaults) or is_array($this->fieldDefaults))
    		$this->fieldDefaults = json_encode($this->fieldDefaults);
    	if (is_object($this->links) or is_array($this->links))
    		$this->linksStr = json_encode($this->links);
    	else
    		$this->linksStr = "";
    	$this->roleStr = "";
    	$this->fieldAccessStr = $this->fieldAccess;
    	$this->fieldDefaultsStr = $this->fieldDefaults;
    	$this->condition = str_replace("xoxo","#",$this->condition);
    	$this->condition = str_replace("yoyo","@",$this->condition);
    	$this->condition = str_replace("zozo","=",$this->condition);
    	if ($this->className=="")
    		$this->fieldList = "presentation Объект";
    	$result = parent::getArgs();
        $result["{className}"] = $this->className;
        return $result;
    }
}
?>