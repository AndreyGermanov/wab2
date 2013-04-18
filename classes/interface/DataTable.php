<?php
/**
 * Класс определяет таблицу с данными, таблицу, в которой в качестве значений
 * ячеек выступают элементы InputControl
 *
 * @author andrey
 */
class DataTable extends WABEntity {

    function construct($params) {
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        $this->name = implode("_",$params);
        $this->template = "templates/interface/DataTable.html";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->css = $this->skinPath."styles/DataTable.css";
        $this->handler = "scripts/handlers/interface/DataTable.js";

        $this->width = "";
        $this->height = "";
        $this->sortOrder = "";
        if ($this->module_id!="")
            $this->pagePanelId = "DataTablePagePanel_".$this->module_id."_panel_".$this->getId();
        else
            $this->pagePanelId = "DataTablePagePanel_panel_".$this->getId();
        $this->itemsPerPage = "0";
        $this->numPages = "0";
        $this->currentPage = "0";
        $this->readonly = "false";
        $this->clientClass = "DataTable";        
        $this->parentClientClasses = "Entity"; 
        $this->showHierarchy = "false";
        $this->entityImagesStr = "";
        $this->additionalLinksStr = "";
        $this->ownerObject = "";
        $this->selectGroup = "";
        $this->tableClassName = "";
        $this->defaultListProfile = "";
        $this->table = "";
        $this->collection = "";
        $this->collectionGetMethod = "";
        $this->collectionLoadMethod = "";
        $this->allFieldList = "";
        $this->printFieldList = "";
        $this->conditionFields = "";
        $this->childCondition = "";
        $this->additionalCondition = "";
        $this->tagsCondition = "";
        $this->topLinkObject = "";
        $this->classTitle = "";
                
        $this->clientObjectId = str_replace(".","_",$this->getId());   
    }

    function getId() {
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->name;
        else
            return get_class($this)."_".$this->name;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "rebuildHook";
    		case '4': return "show";
    	}
    	return parent::getHookProc($number);
    }
    
    function getArgs() {
    	global $Objects;
    	$parent = $Objects->get($this->parent_object_id);
    	if (is_object($parent)) {
    		@$parent->getArgs();
    		$this->readonly = @$parent->readonly;
    	}    		
    	return parent::getArgs();
    }
    
    function rebuildHook($arguments) {
    	global $Objects;
    	if (isset($arguments["adapterId"]) and $arguments["adapterId"]!="")
    		$this->adapter = $Objects->get($arguments["adapterId"]);
    	$this->setArguments($arguments);
    	$result = $this->getArgs($arguments);
    	echo $result["{data}"];
    }    
}
?>