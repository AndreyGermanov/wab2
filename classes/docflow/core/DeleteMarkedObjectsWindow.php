<?php
/**
 * Окно удаления помеченных объектов
 */
class DeleteMarkedObjectsWindow extends WABEntity {
	
	function construct($params) {
		parent::construct($params);
		global $Objects;
		$this->app = $Objects->get("Application");
		if (!$this->app->initiated)
			$this->app->initModules();
		$this->skinPath = $this->app->skinPath;
		$this->handler = "scripts/handlers/docflow/core/DeleteMarkedObjectsWindow.js";
		$this->template = "templates/docflow/core/DeleteMarkedObjectsWindow.html";
		$this->icon = $this->skinPath."images/Tree/delmail.png";
		$this->deletedObjectsTableId = "DeletedObjectsTable_".$this->module_id."_objs";
		$this->blockingObjectsTableId = "BlockingObjectsTable_".$this->module_id."_objs";
		$this->width = 650;
		$this->height = 400;
		$this->overrided = "width,height";
	}
	
	function getPresentation() {
		return "Удаление помеченных объектов";
	}	
}
?>