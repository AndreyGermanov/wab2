<?php
/**
 * Класс, отображающий всплывающее окно для установки условий отбора в таблицу
 * EventLogDataTable.
 * 
 * В ней присутствуют поля:
 * 
 * EventDataFirst-EventDateLast - период, в который возникло событие
 * EventUser - Имя пользователя, который сгенерировал событие
 * EventType - раскрывающийся список с возможностью выбора типа события
 * EventMessage - текст сообщения
 * 
 * Значения переменных складываются оператором И.
 * 
 * Если указать звездочку вначале или в конце каждого параметра, то она
 * заменяется на символ '%' и используется в условии LIKE запроса к БД.
 *
 * @author andrey
 */
class EventLogConditionsFloatDiv extends WABEntity{
    
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
        $this->template = "templates/core/EventLogConditionsFloatDiv.html";
		$this->skinPath = $app->skinPath;
        $this->icon = $this->skinPath."images/Tree/eventviewer.png";
		
        $this->eventDateStart = "";
        $this->eventDateEnd = "";
        $this->eventUser = "";
        $this->eventMessage = "";
        $this->eventType = "";
        $this->notHide = 'true';
        $this->width = 600;
        $this->height = 350;
        $this->overrided = "width,height";
        
        $this->clientClass = "EventLogConditionsFloatDiv";
        $this->parentClientClasses = "Entity";        
    }
    
    function getArgs() {
        global $Objects,$events;
        $usersList = array();
		$users = $Objects->get("ApacheUsers_".$this->module_id."_users");
		$users->load();
        foreach ($users->apacheUsers as $value) {
        	$usersList[$value->name] = $value->name;
        }
        $userNames = implode("~",$usersList);
        $eventNames = array();$eventTitles = array();
        foreach($events as $key=>$value) {
        	$eventNames[] = $key;
        	$eventTitles[] = $value["title"];
        }
        $this->eventTypes = " ~".implode("~",$eventNames)."| ~".implode("~",$eventTitles);
        $this->userList = " ~".$userNames."| ~".$userNames; 
    	$result = parent::getArgs();
        $result["{className}"] = $this->className;
        
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
        $this->eventUser = $arguments["eventUser"];
        $this->eventMessage = $arguments["eventMessage"];
    }
    
    function getPresentation() {
        return "Условия отбора";
    }    
}
?>