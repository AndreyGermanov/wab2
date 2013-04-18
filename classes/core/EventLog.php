<?php
/**
 * Класс отвечает за ведение журнала событий
 * 
 * Данные о доступе к файловым ресурсам хранятся в базе данных MySQL, на которую
 * указывает адаптер. Адаптер берет реквизиты доступа к базе данных
 * из массива настроек данного объекта, который находится внутри метаданных,
 * в соответствующем модуле, в его элементе settings. 
 *
 * host
 * port
 * dbname
 * dbtable
 * user
 * password
 * 
 * На основании этих данных, адаптер типа EventLogDataAdapter подключается к
 * базе данных и позволяет получать из нее информацию о событиях или
 * записывать в нее события.
 * 
 * Типы записываемых событий определяются в массиве logEvents массива настроек
 * журнала. Записать событие можно методом logEvent(event,time,user,params,message).
 * Событие записывается в таблицу dbtable, содержащую следующие поля:
 * 
 * eventDate - время события (time)
 * eventType - тип события (event)
 * eventUser - пользователь, сгенерировавший событие (user)
 * eventObject - объект, с которым произошло событие (берется из массива params, 
 *               если в нем есть элемент с индексом object_id)
 * eventParams - массив параметров события (params)
 * eventMessage - если есть сообщение о событии, оно передается и сохраняется
 * 
 * Периодически журнал событий очищается от устаревших событий. Период ведения журнала
 * указывается в днях в параметре настройки "period". Устаревшие данные удаляются
 * каждый раз при записи нового события функцией logEvent. 
 * 
 * @author andrey
 */
class EventLog extends WABEntity {
        
	public $logEvents = array();
	
    function construct($params) {
        parent::construct($params);
        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->icon = $app->skinPath."images/Tree/eventviewer.png";
        $this->skinPath = $app->skinPath;

        $this->template = "templates/core/EventLog.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/core/EventLog.js";
        
        if (count($this->role)==0)
        	$this->setRole();
        
        $this->tabs_string = "log|Журнал|".$app->skinPath."images/spacer.gif";
        if (@$this->role["canSetSettings"]!="false") {
        	$this->tabs_string.= ";settings|Настройка|".$app->skinPath."images/spacer.gif;";
        	$this->tabs_string.= "events|События|".$app->skinPath."images/spacer.gif";
        }
        
        $this->tabset_id = "WebItemTabset_".$this->module_id."_eventLog";
        $this->active_tab = "log";
        $this->width = "680";
        $this->height = "400";
        $this->overrided = "width,height";
        
        $this->adapter = $Objects->get("EventLogDataAdapter_".$this->module_id."_".str_replace($this->module_id."_","",$this->getId()));
        $this->adapterId = $this->adapter->getId();
        $this->sortFields = "eventDate DESC";
        $this->fieldList = "eventDate Дата~eventUser Пользователь~eventMessage Описание";
        $this->host = $this->adapter->host;
        $this->port = $this->adapter->port;
        $this->dbname = $this->adapter->dbname;
        $this->dbtable = $this->adapter->dbtable;
        $this->user = $this->adapter->user;
        $this->password = $this->adapter->password; 
        
        $this->logEvents = @$this->settings["logEvents"];
        $this->period = @$this->settings["period"];        
        
        $this->eventDateStart = time()."000";
        $this->eventDateEnd = time()."000";
        $this->eventUser = "";
        $this->eventType = "";
        $this->eventObject = "";
        $this->eventMessage = "";
        $this->classTitle = "Журнал событий";
        $this->classListTitle = "Журнал событий";
        $this->profileClass = "EventLogProfile";
        
        $this->clientClass = "EventLog";
        $this->parentClientClasses = "Entity";        
    }
    
    function load() {
        
    }
    
    function save($arguments) {    	
        global $Objects;
        
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        
        if (@$this->role["canSetSettings"]!="false") {
	        if ($this->host=="") {
	            $this->reportError("Не указан сервер БД!","save");
	            return 0;
	        }
	
	        if ($this->dbname=="") {
	            $this->reportError("Не указано имя БД!","save");
	            return 0;
	        }
	        if ($this->dbtable=="") {
	            $this->reportError("Не указано имя таблицы БД!","save");
	            return 0;
	        }
	        if ($this->user=="") {
	            $this->reportError("Не указано имя пользователя БД!","save");
	            return 0;
	        }
	        if ($this->password=="") {
	        	$this->reportError("Не указан пароль пользователя БД!","save");
	        	return 0;
	        }
	        $logEvents = array();
			foreach ($arguments as $key=>$value) {
	        	if (strpos($key,"event_")!==FALSE and $value=="1")
					$logEvents[] = str_replace("event_","",$key);
	        }
	        $gapp = $Objects->get("Application");
	        if (!$gapp->initiated)
	            $gapp->initModules();
	        $module = $gapp->getModuleByClass($this->module_id);
	        $this->settings["host"] = $this->host;
	        $this->settings["port"] = $this->port;
	        $this->settings["dbname"] = $this->dbname;
	        $this->settings["dbtable"] = $this->dbtable;
	        $this->settings["user"] = $this->user;
	        $this->settings["password"] = $this->password;
	        $this->settings["period"] = $this->period;
	        $this->settings["logEvents"] = $logEvents;
	        $module["settings"][str_replace($this->module_id."_","",$this->getId())] = $this->settings;
			$GLOBALS["modules"][$module["name"]] = $module;
			$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
			file_put_contents($module["file"],$str);
        }
	}
    
    function getPresentation() {
        return "Журнал событий";
    }
    
    function getArgs() {
    	global $events;
    	$blocks = getPrintBlocks(file_get_contents("templates/interface/CheckboxTable.html"));
    	$this->eventsTable = $blocks["header"];
    	$this->setSettings();
    	foreach ($this->settings["eventTypes"] as $value) {
    		$args = array();
    		$args["{id}"] = "event_".$value;
    		$args["{title}"] = $events[$value]["title"];
    		if (array_search($value,$this->settings["logEvents"])!==FALSE)
    			$args["{value}"] = "1";
    		else
    			$args["{value}"] = "0";
    		$this->eventsTable .= strtr($blocks["row"],$args);
		}
    	$this->eventsTable .= $blocks["footer"];
    	$result = parent::getArgs();
    	$result["{|eventsTable}"] = $this->eventsTable;
    	return $result;
    }
    
    function logEvent($event,$time,$user,$params,$message="") {
        global $Objects;
        if (@array_search($event, @$this->settings["logEvents"])===FALSE)
        	return 0;
        $t = time();
        $t2 = $t-$this->period*24*60*60;        
        if (!$this->adapter->connected)
            $this->adapter->connect();
        if (!$this->adapter->connected)
    	    return 0;
        @$this->adapter->dbh->exec("CREATE TABLE ".$this->dbtable." (eventDate INT, eventType VARCHAR(100), eventUser VARCHAR(100), eventObject VARCHAR(100), eventMessage LONGTEXT, eventParams LONGTEXT)");	        
        $stmt = $this->adapter->dbh->prepare("DELETE FROM ".$this->dbtable." WHERE eventDate<=:eventDate");
        $stmt->bindParam(":eventDate",$t2);
        $stmt->execute();
        $p = getParams($params);
        if (isset($p["object_id"]))
        	$eventObject = $p["object_id"];
        else
        	$eventObject = "";
        $stmt=$this->adapter->dbh->prepare("INSERT INTO ".$this->dbtable." (eventDate,eventType,eventUser,eventObject,eventMessage,eventParams) VALUES(:eventDate,:eventType,:eventUser,:eventObject,:eventMessage,:eventParams)");
        $stmt->bindParam(":eventDate",$time);
        $stmt->bindParam(":eventType",$event);
        $stmt->bindParam(":eventUser",$user);
        $stmt->bindParam(":eventObject",$eventObject);
        $stmt->bindParam(":eventMessage",$message);
        $stmt->bindParam(":eventParams",$params);
        $stmt->execute();
        $this->adapter->dbh->exec("OPTIMIZE TABLE ".$this->dbtable);
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>