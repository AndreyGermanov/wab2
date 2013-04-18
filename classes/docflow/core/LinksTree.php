<?
// Дерево ссылок
class LinksTree extends Tree {

    function  construct($params) {
        parent::construct($params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->handler = "scripts/handlers/docflow/core/LinksTree.js";
        $this->css = $app->skinPath."styles/Tree.css";
        $this->icon = $app->skinPath."images/Tree/folder.png";
        $this->skinPath = $app->skinPath;
        $this->clientClass = "LinksTree";
        $this->parentClientClasses = "Tree~Entity";      
        $this->items_string = "";  
    }

    function setTreeItems($parent="") {
        global $Objects;
        $app = $Objects->get($this->module_id);
        if ($parent=="")
        	$parent = $this->topObject;
        $obj = $Objects->get($parent);
        if (!$obj->loaded)
        	$obj->load();
        $links = $obj->getLinks();
        foreach ($links as $link) {
        		$link->loaded = false;
        		$link->noPresent = false;
				$link->load();
				$link->setRole();
				if ($link->getRoleValue(@$link->role["canRead"])=="false")
					continue;
        		if ($link->getPresentation()=="")
        			continue;      
        		if ($link->getId()==$this->topObject)
        			continue;
        		if ($link->getId()==$parent)
        			continue;
        		$key = $link->getId();
            	$result[$key]["id"] = $key;
                $result[$key]["title"] = $link->getPresentation();
                $result[$key]["icon"] = $link->icon;
                if ($parent==$this->topObject)
                	$result[$key]["parent"] = "";
                else
                	$result[$key]["parent"] = $parent;
                $result[$key]["loaded"] = "false";
        }
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
    }

    function getId() {
        return "LinksTree_".$this->module_id."_".$this->name;
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "getItemsHook";
		}
		return parent::getHookProc($number);
	}
	
	function getItemsHook($arguments) {
		$this->setArguments($arguments);
		$this->SetTreeItems(@$arguments["parent"]);
		echo $this->items_string;
	}
	
	function showHook($arguments) {		
		$this->setArguments($arguments);
		global $Objects;
		$obj = $Objects->get($this->topObject);
        $this->title = "Связи объекта '".$obj->presentation."'";
		$this->SetTreeItems(@$arguments["parent"]);
		$this->show();
	}	
}
?>