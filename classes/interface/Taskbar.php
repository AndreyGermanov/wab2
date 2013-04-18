<?php
/**
 * Класс панели задач интерфейса управления
 *
 * @author andrey
 */
class Taskbar extends WABEntity {

    function construct($params) {        
        $this->name = $params[0];
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->template = "templates/interface/taskbar.html";
        $this->css = $app->skinPath."styles/taskbar.css";
        $this->handler = "scripts/handlers/interface/taskbar.js";
        $this->left = "1";
        $this->width="100%";
        $this->height="30";
        $this->clientClass = "Taskbar";
        $this->parentClientClasses = "Entity";        
    }

    function load()
    {
    	$this->loaded = true;
    }

    function getId()
    {
        return get_class($this)."_".$this->name;
    }
}
?>