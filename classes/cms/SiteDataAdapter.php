<?php
/**
 * Адаптер данных Web-сайта
 *
 * @author andrey
 */
class SiteDataAdapter extends PDODataAdapter {
    
    function construct($params) {
        $this->siteId = $params[count($params)-2];
        array_splice($params,-2,1);
        parent::construct($params);
        $this->init();
    }
    
    function init() {
    	global $Objects;
    	if ($this->module_id!="") {
    		$this->site = $Objects->get("WebSite_".$this->module_id."_".$this->siteId);
    		$this->site->siteAdapter = $this;
    	}
    	else {
    		$this->site = $Objects->get("WebSite_".$this->siteId);
    		$this->site->siteAdapter = $this;
    	}
    	if (!$this->site->loaded)
    		$this->site->load();
    	
    	$this->dbname = $this->site->db_name;
    	$this->driver = $this->site->db_type;
    	$this->user = $this->site->db_user;
    	$this->password = $this->site->db_password;
    	$this->host = $this->site->db_host;
    	$this->path = $this->site->path;
    	$this->clientClass = "SiteDataAdapter";
    	$this->parentClientClasses = "PDODataAdapter~DataAdapter~Entity";
    	 
    }
    
    function getId() {
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->siteId."_".$this->name;
        else
            return get_class($this)."_".$this->siteId."_".$this->name;
    }
}
?>