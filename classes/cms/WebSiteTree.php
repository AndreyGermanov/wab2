<?php
/**
 * Description of WebSiteTree
 *
 * @author andrey
 */
class WebSiteTree extends EntityTree {
    function construct($params) {
        $this->siteId = $params[count($params)-2];
        $this->defaultClassName = "";
        array_splice($params,-2,1);
        parent::construct($params);
        global $Objects;
        if ($this->module_id!="")
            $this->adapter = $Objects->get("SiteDataAdapter_".$this->module_id."_".$this->siteId."_".$this->siteId);
        else
            $this->adapter = $Objects->get("SiteDataAdapter_".$this->siteId."_".$this->siteId);        
        $this->handler = "scripts/handlers/cms/WebSiteTree.js";
        $this->contextMenuClass = "WebEntityContextMenu";
        $this->rootContextMenuClass = "WebSiteRootContextMenu";
        $this->clientClass = "WebSiteTree";
        $this->parentClientClasses = "EntityTree~Tree~Entity";
    }
    
    function getId() {
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->siteId."_".$this->name;
        else
            return get_class($this)."_".$this->siteId."_".$this->name;
    }
}
?>