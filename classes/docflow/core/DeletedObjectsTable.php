<?php
/**
 * Таблица, отображающая набор сущностей, помеченных на удаление
 * 
 * @author andrey
 */
class DeletedObjectsTable extends EntityDataTable {	
    function construct($params) {
        parent::construct($params);
        $this->clientClass = "DeletedObjectsTable";
        $this->parentClientClasses = "EntityDataTable~DataTable~Entity";
        $this->collection = "DocFlowApplication_".$this->module_id;
        $this->collectionGetMethod = "getDeletedObjects";
        $this->fieldList = "presentation Объект~canDeletePresentation Можно удалить";
        global $Objects;
        $this->app = $Objects->get("Application");
        if (!$this->app->initiated)
        	$this->app->initModules();
        $this->skinPath = $this->app->skinPath;
        $this->template = "templates/docflow/core/DeletedObjectsTable.html";        
    }
}
?>