<?php
/**
 * Класс, отображающий всплывающее окно для установки условий отбора в таблицу
 * LogDataTable.
 * 
 * В ней присутствуют поля:
 * 
 * EventDataFirst-EventDateLast - период, в который возникло событие
 * EventIP - IP-адрес компьютера, инициирующего событие
 * EventType - раскрывающийся список с возможностью выбора типа события
 * EventFilePath - файл, с которым произошло событие
 * EventFileNewPath - новое имя файла, с которым произошло событие
 * 
 * Значения переменных складываются оператором И.
 * 
 * Если указать звездочку вначале или в конце каждого параметра, то она
 * заменяется на символ '%' и используется в условии LIKE запроса к БД.
 *
 * @author andrey
 */
class AuditConditionsFloatDiv extends WABEntity{
    
    function construct($params) {
        parent::construct($params);
        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
			$app->initModules();
        
        $this->entityId = "";
        $this->className = "";
        $this->condition = "";
        $this->parent_object_id = "";
        $this->editorType = "";
        $this->divName = "";
        $this->windowWidth = "";
        $this->windowHeight = "";
        $this->windowTitle = "";
        $this->destroyDiv = "";
        $this->additionalFields="";
        $this->handler = "scripts/handlers/controller/AuditConditionsFloatDiv.js";    
        $this->template = "templates/controller/AuditConditionsFloatDiv.html";
		$this->skinPath = $app->skinPath;
        $this->icon = $this->skinPath."images/Tree/eventviewer.png";
		
        $this->eventDateStart = "";
        $this->eventDateEnd = "";
        $this->eventIP = "";
        $this->eventFilePath = "";
        $this->eventFileNewPath = "";
        $this->eventType = 0;
        $this->notHide = 'true';
        $this->width = 600;
        $this->height = 350;
        $this->overrided = "width,height";
        
        $this->clientClass = "AuditConditionsFloatDiv";
        $this->parentClientClasses = "Entity";        
    }
    
    function getArgs() {
        $result = parent::getArgs();
        $result["{className}"] = $this->className;
        global $Objects;
        $obj = $Objects->get("FullAuditReport_".$this->module_id."_report");
        $result["{eventTypes}"] = "0~".implode("~",array_keys($obj->eventTitles))."| ~".implode("~",array_values($obj->eventTitles));
        return $result;
    }   
    
    function getHookProc($number) {
        switch ($number) {
            case '2': return "initWindow";
        }
        return parent::getHookProc($number);
    }
    
    function initWindow($arguments) {
        $this->eventType = $arguments["eventType"];
        $this->eventDateStart = $arguments["eventDateStart"];
        $this->eventDateEnd = $arguments["eventDateEnd"];
        $this->eventIP = $arguments["eventIP"];
        $this->eventFilePath = $arguments["eventFilePath"];
        $this->eventFileNewPath = $arguments["eventFileNewPath"];
    }
    
    function getPresentation() {
        return "Условия отбора";
    }    
}
?>