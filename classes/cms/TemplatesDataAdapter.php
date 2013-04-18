<?php
/**
 * Адаптер для базы данных шаблонов дизайна сайта
 *
 * @author andrey
 */
class TemplatesDataAdapter extends PDODataAdapter {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        if ($this->module_id!="") {
            $this->webapp = $Objects->get($this->module_id);
        }
        else {
            $this->webapp = $Objects->get(@$_SERVER["MODULE_ID"]);  
        }
        $this->driver = "pdo_sqlite";
        $this->path = $this->webapp->templatesDB;        
        $this->clientClass = "TemplatesDataAdapter";
        $this->parentClientClasses = "PDODataAdapter~DataAdapter~Entity";
    }
}
?>