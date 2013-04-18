<?php
/**
 * Класс, реализующий дерево сайтов
 *
 * @author andrey
 */
class WebSitesTree extends EntityTree {    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        if ($this->module_id=="")
            $this->adapter = $Objects->get("PDODataAdapter_".get_class($this)."_".$this->name);
        else
            $this->adapter = $Objects->get("PDODataAdapter_".$this->module_id."_".get_class($this)."_".$this->name);
        $this->adapter->dbEntity = "";        
        if ($this->module_id!="")
            $webapp = $Objects->get($this->module_id);
        else
            $webapp = $Objects->get($_SERVER["MODULE_ID"]);        
        $this->adapter->path = $webapp->sitesDB;        
        $this->treeClassName = "WebSiteTree";
        $this->groupEntityImage = $this->skinPath."images/Tree/sites.gif";
        $this->entityImage = $this->skinPath."images/Tree/sites.gif";        
        $this->sortOrder = "title ASC strings";        
        $this->contextMenuClass = "WebTemplateTreeContextMenu";        
        $this->rootContextMenuClass = "WebTemplateTreeRootContextMenu"; 
        $this->clientClass = "WebSitesTree";
        $this->parentClientClasses = "EntityTree~Tree~Entity";        
    }   
    
    function setTreeItems() {
        global $Objects;
        if ($this->module_id!="")
            $items = $Objects->query("WebSite_".$this->module_id,"simple|title strings~entityImage strings~groupEntityImage strings~name integers|",$this->adapter,"title ASC strings");
        else
            $items = $Objects->query("WebSite","",$this->adapter,"title ASC strings");
        $valid_sites = array();
        $all = false;
		$lapp = $Objects->get($this->module_id);

        $valid_sites = array();
        $all = false;
        foreach($lapp->webSites as $site) {
	        $valid_sites[$site] = $site;
            if ($site=="All") {
		        $all = true;
    		    break;
	       	}
        }
        
        if ($this->entityId!="" and $this->entityId!=-1) {
            $currentEntity = $Objects->get($this->entityId);
        }
        $result = array();
        foreach($items as $item) {
            if (!$all and !isset($valid_sites[$item->fields["title"]]))
                continue;
            $arr = explode("_",$item->getId());
            $adapterId = array_pop($arr);
            $result[$item->getId()]["id"] = "WebSiteTree_".$this->module_id."_".$adapterId."_".$adapterId;
            $result[$item->getId()]["title"] = $item->fields["title"];
            $result[$item->getId()]["icon"] = $this->groupEntityImage;
            $result[$item->getId()]["parent"] = '';
            $result[$item->getId()]["loaded"] = "target_object=".$item->getId()."#className=*WebEntity*#defaultClassName=WebEntity#loaded=false#additionalFields=siteId#editorType=WABWindow#adapterId=SiteDataAdapter_".$this->module_id."_".$adapterId."_".$adapterId;
            $result[$item->getId()]["subtree"] = "true";                        
        }
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        if (isset($res_str))
            return implode("|",$res)."|".$res_str;
        else
            return implode("|",$res);        
        
    }
    
    function getArgs() {
        $result = parent::getArgs();
        $result["{className}"] = $this->className;
        return $result;
    }
}
?>