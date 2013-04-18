<?php
class ControlContextMenu extends ContextMenu {	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ControlContextMenu";
		$this->parentClientClasses = "ContextMenu~Entity";		
	}
   
    function show() {
        $arr = explode("_",$this->opener_item);
        array_pop($arr);        
        global $Objects;
        $module_id = $arr[1]."_".$arr[2];
        $opener_item = implode("_",$arr);
        $opener_item = str_replace("Tree_".$module_id."_".$arr[2]."_tree_","",$opener_item);
        $obj = $Objects->get($opener_item);
        $obj->load();
        if ($obj->remoteConsolePort!="")
            $this->addItem("console","Консоль");
        if ($obj->remoteDesktopPort!="")
            $this->addItem("desktop","Рабочий стол");
        if ($obj->webInterfacePort!="")
            $this->addItem("web","Web-интерфейс");        
        parent::show();        
    }
}
?>