<?php
class MetadataContextMenu extends ContextMenu {	
	function construct($params) {
		parent::construct($params);
		$this->handler = "scripts/handlers/metadata/MetadataContextMenu.js";
		$this->clientClass = "MetadataContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";		
	}
   
    function show() {
        $arr = explode("_",$this->target);
        if ($arr[0]!="MetadataObjectField" and $arr[0]!="MetadataObjectModel" and $arr[0]!="MetadataObjectCode" and $arr[0]!="MetadataPanel") {
        	$this->addItem("addGroup","Новая группа");
        	$this->addItem("add","Новый элемент");
        }
        if ($arr[0]=="MetadataObjectField" or $arr[0]=="MetadataObjectModel" or $arr[0]="MetadataObjectCode" or $arr[0]=="MetadataGroup" or $arr[0]=="MetadataModelGroup" or $arr[0]=="MetadataCodeGroup" or $arr[0]=="MetadataPanel") {
        	$this->addItem("change","Изменить");
        	$this->addItem("remove","Удалить");
        }
        parent::show();        
    }
}
?>