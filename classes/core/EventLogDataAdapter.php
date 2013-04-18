<?php
/*
 * Класс адаптера данных для базы данных журнала событий.
 */
class EventLogDataAdapter extends PDODataAdapter {   
    function construct($params) {
        parent::construct($params);
        global $Objects,$modules;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $module = $app->getModuleByClass($this->module_id);
        $settings = @$module["settings"][$this->name];
        $this->driver   = "pdo_mysql";
        $this->host     = @$settings["host"];
        $this->port     = @$settings["port"];
        $this->dbname   = @$settings["dbname"];
        $this->dbtable  = @$settings["dbtable"];
        $this->user     = @$settings["user"];
        $this->password = @$settings["password"];
                                
        $this->clientClass = "EventLogDataAdapter";
        $this->parentClientClasses = "PDODataAdapter~DataAdapter~Entity";        
    }
}
?>