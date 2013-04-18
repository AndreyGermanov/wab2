<?php
/**
 * Класс меню подсистемы документооборота
 *
 * @author andrey
 */
class DocFlowMenu extends Menu {
    
    function construct($params) {
        global $Objects;
        parent::construct($params);
        $this->horizontal = "false";
        $this->table_properties = "cellpadding=5^style=border-width:1px;border-color:#000000";
        $this->properties = "class=".$this->getId()."_menu^style=display:none;border-width:1px;border-style:solid;border-color:#000000";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;        
        $this->css = $app->skinPath."styles/DocFlowMenu.css";
        $this->clientClass = "DocFlowMenu";
        $this->parentClientClasses = "Menu~Entity";        
    }    
}
?>