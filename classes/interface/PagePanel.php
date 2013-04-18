<?php
/**
 * Класс управляет панелью страниц
 *
 * @author andrey
 */
class PagePanel extends WABEntity {

    function construct($params) {
        if (count($params)>3) {
            $this->module_id = array_shift($params)."_".array_shift($params);
        }
        $this->name = array_shift($params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->item_id = implode("_",$params);
        $this->current_page = 1;
        $this->num_pages = 5;
        $this->items_per_page = 10;
        $this->parent_item = "";
        $this->parent_object_id = "";
        $this->template = "templates/interface/PagePanel.html";
        $this->skinPath = $app->skinPath;
        $this->css = $this->skinPath."styles/PagePanel.css";
        $this->handler = "scripts/handlers/interface/PagePanel.js";
        $this->class = "scripts/classes/interface/PagePanel.js";
		$this->clientClass = "PagePanel";
		$this->parentClientClasses = "Entity";		
    }

    function getId() {
        if ($this->module_id != "")
            return get_class($this)."_".$this->module_id."_".$this->name."_".$this->item_id;
        else
            return get_class($this)."_".$this->name."_".$this->item_id;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "rebuild";
    	}
    	return parent::getHookProc($number);
    }
    
    function rebuild($arguments) {
    	$this->setArguments($arguments);
    	$this->init();
    	$args = $this->getArgs();
    	echo $args['{row_properties}'].'XOXOXO'.$args['{cell_properties}'];    	
    }
}
?>