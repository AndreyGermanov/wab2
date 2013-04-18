<?php

class ObjectsSelectTable extends WABEntity {

    public $selected_items,$collection;
    
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->collection = "";
        $this->selected_items = "";
        $this->width = "99%";
        $this->title = "Группы";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->template = "templates/interface/Table.html";
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/controller/ObjectsSelectTable.js";
        
        $this->clientClass = "ObjectsSelectTable";
        $this->parentClientClasses = "Entity";        
    }

    function getId() {
        return "ObjectsSelectTable_".$this->module_id."_".$this->name;
    }

    function getRows() {
        $result = array();
        $c = count($result);
        $result[$c][0] = '<input type="checkbox" id="'.$this->getId().'_checkAll" onclick="$O(\''.$this->getId().'\',\'\').checkAll(event)">~class=header';
        $result[$c][0] = str_replace('"','\"',$result[$c][0]);
        $result[$c][1] = " ~class=header";
        $result[$c][2] = $this->title."~class=header&style=width:100%";
        $hosts_in_group = array();
        $hosts_not_in_group = array();
        global $Objects;
        $selected = array_flip(explode(",",$this->selected_items));
        foreach($this->collection as $item) {
            if ($item->name == "" or $item->name == "root")
                continue;
            $c1 = count($result);
            $checked = "";
            if (isset($selected[$item->name]))
                $checked = "checked";
            else
                $checked = "";
            $result[$c1][0] = '<input '.$checked.' type="checkbox" onclick="$O(\''.$this->getId().'\',\'\').check(event)" id="'.$item->name.'">~class=cell';
            $result[$c1][0] = str_replace('"','\"',$result[$c1][0]);
            $result[$c1][1] = "<img src='".$item->icon."'>~class=cell";
            $result[$c1][2] = $item->name."~class=cell";
        }

        ksort($result);

        $res = array();
        foreach ($result as $item)
            $res[count($res)] = implode("#",$item);

        return implode("|",$res);
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{table_rows}"] = $this->getRows();
        return $result;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "showUsersHook";
    		case '4': return "showGroupsHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function showUsersHook($arguments) {
    	global $Objects;
    	$object = $this;
    	$this->setArguments($arguments);
    	$this->selected_items = $arguments["selected_items"];
    	$fs=$Objects->get('FileServer_'.$this->module_id.'_Shares');
    	$fs->loadUsers(false);
    	$object->collection = $fs->users;
    	$object->show();
    }

    function showGroupsHook($arguments) {
    	global $Objects;
    	$object = $this;
    	$this->setArguments($arguments);
    	$this->selected_items = $arguments["selected_items"];
    	$fs=$Objects->get('FileServer_'.$this->module_id.'_Shares');
    	$fs->loadGroups(false);
    	$object->collection = $fs->groups;
    	$object->show();
    }    
}
?>