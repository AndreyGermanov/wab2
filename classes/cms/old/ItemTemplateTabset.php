<?php
class ItemTemplateTabset extends Tabset {

    function construct($params="") {
        parent::construct($params);
        $this->module_id = $params[0]."_".$params[1];
        $this->base_id = $params[2];
        $this->type = $params[3];
        $this->handler = "scripts/handlers/ItemTemplateTabset.js";
    }

    function getId() {
        return get_class($this)."_".$this->module_id."_".$this->base_id."_".$this->type;
    }
}
?>
