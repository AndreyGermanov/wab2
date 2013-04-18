<?php
/**
 * Адаптер данных для документооборота
 *
 * @author andrey
 */
class DocFlowDataAdapter extends PDODataAdapter{
    
    function construct($params) {
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        $this->name = implode("_",$params);
        $this->old_name = $this->name;     
        $this->isPDO = true;        
        global $Objects;
        $app = $Objects->get($this->module_id);
        if (!$app->loaded)
            $app->load();
        if (!$app->loaded)
            return 0;
        $this->host = $app->dbHost;
        $this->port = $app->dbPort;
        $this->path = $app->dbPath;
        $this->user = $app->dbUser;
        $this->password = $app->dbPassword;
        $this->dbname = $app->dbName;
        $this->driver = $app->dbDriver;
        $this->charset = $app->dbCharset;
        $this->clientClass = "DocFlowDataAdapter";
        $this->parentClientClasses = "PDODataAdapter~DataAdapter~Entity";        
    }    
}
?>