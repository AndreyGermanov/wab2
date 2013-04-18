<?
// Дерево группы типов объектов
class ObjGroupsTree extends Tree {

    function construct($params) {
        parent::construct($params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->handler = "scripts/handlers/core/ObjGroupsTree.js";
        $this->css = $app->skinPath."styles/Tree.css";
        $this->icon = $app->skinPath."images/Tree/folder.png";
        $this->skinPath = $app->skinPath;
        $this->clientClass = "ObjGroupsTree";
        $this->parentClientClasses = "Tree~Entity";     
        $this->treeClassName = "ObjGroupsTree";
        $this->result_object_id = "";
        $this->selectGroup = "";
        $this->objGroup = ""; 
        $this->items_string = "";  
    }

    function setTreeItems($objGroup) {
        global $Objects;
        $result = array();
        foreach ($objGroup["items"] as $link) {
        	$obj = @$Objects->get($link."_".$this->module_id."_");  
       		if (!is_object($obj) or $obj->classTitle=="")
       			continue;      
       		$key = $link;       		
           	$result[$key]["id"] = "EntityTree_".$this->module_id."_".$key;
            $result[$key]["title"] = $obj->classListTitle;
            $result[$key]["icon"] = $obj->icon;
            $result[$key]["parent"] = "";
            $result[$key]["loaded"] = "editorType=".$this->editorType."#className=".$key."#defaultClassName=".$key."#adapterId=DocFlowDataAdapter_".$this->module_id."_1#selectGroup=0#parent_object_id=".$this->getId()."#result_object_id=".$this->result_object_id."#hook=setParams#loaded=false#hierarchy=true#entityImage=".$obj->icon."#selectGroup=".$this->selectGroup."#condition=".str_replace("=","zozo",$this->condition)."#tableId=".$this->tableId;
            $result[$key]["subtree"] = "true";
        }

        ksort($result);
        $res = array();
        foreach($result as $value) {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
    }

    function getId() {
        return "ObjGroupsTree_".$this->module_id."_".$this->name;
    }
    
    function load() {
    	
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "getItemsHook";
		}
		return parent::getHookProc($number);
	}
	
	function getItemsHook($arguments) {
		$this->setArguments($arguments);
		global $objGroups;
		$this->objGroup = $objGroups[$arguments["objGroup"]];
		$this->SetTreeItems($this->objGroup);
		echo $this->items_string;
	}
	
	function show() {
		global $objGroups;
		$this->objGroupStr = $this->objGroup;
		$this->objGroup = @$objGroups[$this->objGroup];
		$this->title = $this->objGroup["title"];
		$this->icon = $this->skinPath.@$this->objGroup["icon"];
		//$this->SetTreeItems($this->objGroup);
		parent::show();

	}	
}
?>