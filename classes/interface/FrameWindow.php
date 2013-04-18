<?php
/**
 * Класс, реализующий окно с фрэймом внутри
 *
 * @author andrey
 */
class FrameWindow extends WABEntity {
    
    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->url = "";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->css = $this->skinPath."styles/Mailbox.css";
        $this->icon = $this->skinPath."images/Window/window.png";
        $this->template = "templates/interface/FrameWindow.html";
        $this->handler = "scripts/handlers/core/WABEntity.js";
        $this->width = 650;
        $this->height = 500;
        $this->overrided = "width,height";
        $this->clientClass = "FrameWindow";
        $this->parentClientClasses = "Entity";        
    }
    
    function getId() {
        return "FrameWindow_"+$this->module_id+"_"+$this->name;
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '2': return "openURL";
			case '3': return "openWindow";
		}
		return parent::getHookProc($number);
	}
	
	function openURL($arguments) {
		$this->url = $arguments["url"];
	}
    	
	function openWindow($arguments) {
		$this->object_text = 'Web-интерфейс '+$arguments['hostname'];
		$this->url = protocol+"://"+address+":"+port;
	}	
}
?>