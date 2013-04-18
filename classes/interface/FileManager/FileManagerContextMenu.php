<?php

class FileManagerContextMenu extends ContextMenu {
   
    function show() {
		global $Objects;
		$obj = $Objects->get($this->opener_object);
		$obj->useCase = $this->useCase;
		$obj->setOperations();
		if (@$obj->fileOperations["makeDir"])
            $this->addItem("makeDir","Создать каталог");
		$this->addItem("selectAll","Выделить все");
		$this->clientClass = "FileManagerContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";		
        parent::show();        
    }
}
?>