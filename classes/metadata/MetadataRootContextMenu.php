<?php
class MetadataRootContextMenu extends ContextMenu {	
	function construct($params) {
		parent::construct($params);
		$this->handler = "scripts/handlers/metadata/MetadataRootContextMenu.js";
       	$this->addItem("addGroup","Новая группа");
       	$this->addItem("add","Новый элемент");
		$this->clientClass = "MetadataRootContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";		
	}   
}
?>