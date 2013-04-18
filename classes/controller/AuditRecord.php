<?php
/**
 * Класс, содержащий структуру данных записи в журнале событий доступа Samba
 *
 * @author andrey
 */
class AuditRecord extends WABEntity {    
    function construct($params) {        
        parent::construct($params);
        global $Objects;
        $this->persistedFields .= "name|string|type=string~title=Системное имя\n";
        $this->persistedFields .= "eventDate|integer|type=date~title=Дата события\n";
        $this->persistedFields .= "eventType|integer|type=integer~title=Тип события\n";
        $this->persistedFields .= "eventIP|integer|type=integer~title=IP-адрес\n";
        $this->persistedFields .= "eventFilePath|string|type=string~title=Путь к файлу\n";
        $this->persistedFields .= "eventFileNewPath|string|type=string~title=Путь к файлу назначения";
        
        $this->adapter = $Objects->get("FullAuditDataAdapter_".$this->module_id."_report");
        
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules;
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/addrbook.gif";       
        $this->width="650";
        $this->height="300";
        $this->loaded = false;

        $this->clientClass = "AuditRecord";
        $this->parentClientClasses = "Entity";       
    }            
    
    function load() {
        $this->loaded = true;
    } 
}
?>