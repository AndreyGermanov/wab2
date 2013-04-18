<?php
/**
 * Класс, реализующий дерево шаблонов дизайна сайтов
 *
 * @author andrey
 */
class WebTemplateTree extends EntityTree {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        if ($this->module_id=="")
            $this->adapter = $Objects->get("TemplatesDataAdapter_".get_class($this)."_".$this->name);
        else
            $this->adapter = $Objects->get("TemplatesDataAdapter_".$this->module_id."_".get_class($this)."_".$this->name);
        $this->adapter->dbEntity = "";  
        if ($this->module_id!="")
            $webapp = $Objects->get($this->module_id);
        else
            $webapp = $Objects->get($_SERVER["MODULE_ID"]);        
        $this->adapter->path = $webapp->templatesDB;
        $this->groupEntityImage = $this->skinPath."images/Tree/templates.gif";
        $this->entityImage = $this->skinPath."images/Tree/templates.gif";        
        $this->sortOrder = "title ASC strings";        
        $this->contextMenuClass = "WebTemplateTreeContextMenu";        
        $this->rootContextMenuClass = "WebTemplateTreeRootContextMenu";    
        $this->adapterId = "";
        
        $this->clientClass = "WebTemplateTree";
        $this->parentClientClasses = "EntityTree~Tree~Entity";        
    }
    
    function getArgs() {
        $result = parent::getArgs();
        $result["{className}"] = $this->className;
        return $result;
    }
}
?>