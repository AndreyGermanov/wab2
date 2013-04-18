<?php
class WebItemTabset extends Tabset {

    function construct($params="") {
        parent::construct($params);
        $this->handler = "scripts/handlers/cms/WebItemTabset.js";
    }
}
?>