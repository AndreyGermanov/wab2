<?php
/*
 * Класс управляет интерфейсным элементом "Панель закладок".
 */
class Tabset extends WABEntity {

    function construct($params="") {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if (count($params)==4) {
            $this->module_id=$params[0]."_".$params[1];
            $this->object_id = $params[2];
            $this->type = $params[3];
        } else {
            $this->object_id = @$params[0];
            $this->type = @$params[1];
        }
        if ($this->type=="")
            $this->type="up";
        $this->template = "templates/interface/Tabset.html";
        $this->css = $app->skinPath."styles/Tabset.css";
        $this->skinPath = $app->skinPath;
        $this->handler = "scripts/handlers/interface/Tabset.js";
        $this->parent_object_id="";
        $this->clientClass = "Tabset";
        $this->parentClientClasses = "Mailbox~Entity";        
    }

    function getId() {
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->object_id;
        else
            return $this->object_id;
    }
}
?>