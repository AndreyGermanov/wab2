<?php
/**
 * Класс главного меню медицинской подсистемы
 *
 * @author andrey
 */
class MedicMenu extends Menu {    
    function construct($params) {
        global $Objects;
        parent::construct($params);
        $this->horizontal = "true";
        $this->height = "20";        
        $this->table_properties = "cellpadding=5";
        $this->properties = "class=".$this->getId()."_menu^style=display:";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;        
        $this->css = $app->skinPath."styles/DocFlowMenu.css";
        $this->template = "templates/docflow/medic/MedicMenu.html";
        $this->handler = "scripts/handlers/docflow/medic/MedicMenu.js";
        $this->clientClass = "MedicMenu";
        $this->parentClientClasses = "DocFlowApplication~DocFlowMenu~Menu~Entity";        
        $this->classTitle = "Меню медицинской подсистемы";
        $this->classListTitle = "Меню медицинской подсистемы";
    }
    
    function getArgs() {
        $result = parent::getArgs();
        $data = "referencesMenu~Справочники&nbsp;~~~|documentsMenu~Документы&nbsp;~~~|reportsMenu~Отчеты&nbsp;~~~|settingsMenu~Система&nbsp;~~~|helpMenu~Помощь&nbsp;~~~";
        $result["{data}"] = $data;
        return $result;
    }      
}
?>