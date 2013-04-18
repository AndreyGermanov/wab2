<?php
/**
 * Таблица, отображающая набор сущностей, помеченных на удаление
 * 
 * @author andrey
 */
class BlockingObjectsTable extends EntityDataTable {	
    function construct($params) {
        parent::construct($params);
        $this->clientClass = "BlockingObjectsTable";
        $this->parentClientClasses = "EntityDataTable~DataTable~Entity";
        $this->collection = "";
        $this->collectionGetMethod = "getBlockingObjects";
        $this->fieldList = "presentation Объект";
        global $Objects;
        $this->app = $Objects->get("Application");
        if (!$this->app->initiated)
        	$this->app->initModules();
        $this->skinPath = $this->app->skinPath;
        $this->template = "templates/docflow/core/BlockingObjectsTable.html";        
    }
}
?>