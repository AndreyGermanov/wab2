<?php

class UserAccessRulesTable extends HostAccessRulesTable {
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->type = $params[2];
        $this->name = $params[3];
        $this->width = "99%";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->template = "templates/interface/Table.html";
        $this->css = $app->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/controller/HostAccessRulesTable.js";
        
        $this->clientClass = "UserAccessRulesTable";
        $this->parentClientClasses = "HostAccessRulesTable~Entity";        
    }

    function getId() {
        return "UserAccessRulesTable_".$this->module_id."_".$this->type."_".$this->name;
    }

    function getAccessRules($name) {
        global $Objects;
        if ($this->type=="user")
            $obj = $Objects->get("User_".$this->module_id."_".$this->name);
        if ($this->type=="group")
            $obj = $Objects->get("Group_".$this->module_id."_".$this->name);
//        if (!$obj->loaded)
            $obj->getAccessRules();
        $access_rules = $obj->shares_access_rules;
        $read_checked = ""; $write_checked = "";
        if (isset($access_rules[$name])) {
            $item = array_pop(explode("~",$access_rules[$name]));
            if (@$item=="r") {
                $read_checked = "checked";
                $write_checked = "";
            } elseif (@$item=="rw") {
                $read_checked = "checked";
                $write_checked = "checked";
            }
        }
        return $read_checked." ".$write_checked;
    }
}
?>