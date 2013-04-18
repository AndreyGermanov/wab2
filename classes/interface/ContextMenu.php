<?php
class ContextMenu extends WABEntity{

    public $items;

    function construct($params) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        $this->name = implode("_",$params);
        $this->template = "templates/interface/ContextMenu.html";
        $this->handler = "scripts/handlers/interface/ContextMenu.js";
        $this->skinPath = $app->skinPath;
        $this->css = $app->skinPath."styles/ContextMenu.css";
        $this->notHide = "false";
        $this->opener_object = "";
        $this->items = array();
        $this->clientClass = "ContextMenu";
        $this->parentClientClasses = "Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{items}"] = implode(",",array_keys($this->items))."|".implode(",",array_values($this->items));
        return $result;
    }

    function addItem($id,$title) {
        $this->items[$id] = $title;
    }

    function getId() {
        if ($this->module_id=="")
            return get_class($this)."_".$this->name;
        else
            return get_class($this)."_".$this->module_id."_".$this->name;
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '2': return "showMenu";
		}
		return parent::getHookProc($number);
	}
	
	function showMenu($arguments) {
		$object = $this;
		$object->left = $arguments["left"];
		$object->top = $arguments["top"];
		$object->opener_item = $arguments["opener_item"];
		$object->opener_object = $arguments["opener_object"];
		if (isset($arguments["arguments"])) {			
			if (is_object($arguments["arguments"])) {
				$arguments["arguments"] = (array)$arguments["arguments"];
				foreach ($arguments["arguments"] as $key=>$value) {
					$object->fields[$key] = $value;
				}
				if (isset($arguments["arguments"]["hook"])) {
					$hook = $arguments["arguments"]["hook"];
					$hook = $this->getHookProc($hook);
					$object->$hook($arguments["arguments"]);
				}
			} else
				eval($arguments["arguments"]);
		}
		$object->show();
	}
}
?>