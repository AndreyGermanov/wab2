<?php

class BlockedObjectsTable extends EntityDataTable {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "BlockedObjectsTable";
		$this->parentClientClasses = "EntityDataTable~DataTable~Entity";
		$this->collection = "BlockedObjectsTable_".$this->module_id."_".$this->name;
		$this->collectionGetMethod = "getBlockedObjects";
		$this->template = "templates/auth/BlockedObjectsTable.html";
		$this->handler = "scripts/handlers/auth/BlockedObjectsTable.js";
		$this->className = "";
		$this->defaultClassName = "";
		$this->parentEntity = "";
		$this->itemsPerPage = 10;
		$this->userName = $this->name;
		$this->fieldList = "description Наименование";
		$this->sortOrder = "description ASC";		
	}
	
	function getBlockedObjects() {
		global $Objects;
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		$result = array();
		$dir = $app->variablesPath."users/".$this->name."/windows";
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if ($file!="." and $file!="..") {
						if (!is_dir($dir."/".$file)) {
							$window_file = $file;
							$obj = $Objects->get($file);
							if (!$obj->loaded)
								$obj->load();
							$obj->description = $obj->getPresentation();
							$result[] = $obj;							
						}
					}
				}
				closedir($dh);
			}
		}
		return $result;		
	}
}
?>