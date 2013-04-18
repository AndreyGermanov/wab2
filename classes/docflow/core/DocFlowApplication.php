<?php
/**
 * Класс реализует приложение документооборота. В его функции входит
 * чтение данных из конфигурационного файла, инициализация соединения  базой
 * данных
 *
 * 
 */
class DocFlowApplication extends WABEntity {
    
    function construct($params) {    	 
        if (isset($params[1])) {
            $this->module_id = @$params[0]."_".@$params[1];
            $this->object_id = @$params[1];
        }
        else
            $this->object_id = @$params[0];     
        $this->handler = "sripts/handlers/docflow/core/DocFlowApplication.js";
        $this->loaded = false;
        $this->clientClass = "DocFlowApplication";
        $this->parentClientClasses = "Entity";
        $this->docFlowApp = $this;     
        $this->classTitle = "Бизнес-сервер";
        $this->classListTitle = "Бизнес-сервер";
    }
        
	function getId() {
		return get_class($this)."_".$this->object_id;
	}
	
    function load() {        
        global $Objects,$config_user;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        if (!isset($config_user))
        	if (isset($_SERVER["PHP_AUTH_USER"]))
            	$config = $Objects->get("AdminConfig_".$_SERVER["PHP_AUTH_USER"]);
        	else 
           	$config = $Objects->get("AdminConfig_".$config_user);
        if (isset($config)) {
	        $sysconfig = $config;
	        $module = $app->getModuleByClass("DocFlowApplication_".$this->object_id);
	        if ($module!=0) {
	        	foreach($module as $key=>$value)
	        		$this->fields[$key] = $value;
	        }
        }
        $this->loaded = true;        
    }
    
    function getAdapter($entity) {
    	global $Objects;
        $adapter = $Objects->get("DocFlowDataAdapter_".str_replace(get_class($entity)."_","",$entity->getId()));
        
        if (!$adapter->connected) {
            if (!$this->loaded)
                $this->load();
            if (!$this->loaded)
                return 0;
            $adapter->host = $this->dbHost;
            $adapter->port = $this->dbPort;
            $adapter->path = $this->dbPath;
            $adapter->user = $this->dbUser;
            $adapter->password = $this->dbPassword;
            $adapter->dbname = $this->dbName;
            $adapter->driver = $this->dbDriver;
            $adapter->charset = $this->dbCharset;
            $adapter->entity = $entity;            
        }
        return $adapter;
    }
    
    function setDeletionMark($list,$mark) {
    	if (is_array($list)) {
    		$mark = $list["mark"];
    		$list = $list["entities"];
    	}
		global $Objects;
        if (!is_array($list))
            $list = explode(",",$list);
        $removed_list = array();
        $error_text = "";
        foreach ($list as $entity) {
            $obj = $Objects->get($entity);
  			if (count($obj->role)=="0")
   				$obj->setRole();
           	if ($mark=="1")
           		if ($obj->getRoleValue(@$obj->role["canDelete"])=="false")
           			continue;     
           	if ($mark=="0")
           		if ($obj->getRoleValue(@$obj->role["canUndelete"])=="false")
           			continue;
           	$obj->loaded = false;            	 
            $obj->load();
            $obj->deleted = $mark;
            $obj->old_registered = $obj->registered;
            if ($mark==1) {
           		if ($obj->getRoleValue(@$obj->role["canUnregister"])=="false")
           			continue;     
            	$obj->registered = 0;
            }
            $obj->save(true);
            if ($mark==1) {
	            $childs = $obj->adapter->getChildren();
	            $child_list = array();
	            foreach ($childs as $child) {
	            	if (is_object($child))
	            		$child_list[] = $child->getId();
	            }
	            if (count($child_list)>0) {	            	
	            	$rlist = $this->setDeletionMark(implode(",",$child_list),$mark);
	            	if (@$_POST["ajax"]==true) {
	            		$rlist = json_decode($rlist);
	            		$rlist = (array)$rlist;
	            	}
	            	$rlist = explode("~",$rlist["removed_objects"]);
	            }                        
				if (count($child_list)>0)
					$removed_list = mergeArrays($removed_list,$rlist);
            }			
			$removed_list[] = $obj->getId();				
        }
        if (@$_POST["ajax"]==true)        
            return json_encode(array("removed_objects" => implode("~",$removed_list)));
        else
            return array("removed_objects" => $removed_list);
    }

    function removeLinks($list,$mark) {
    	if (is_array($list)) {
    		$mark = $list["mark"];
    		$list = $list["entities"];
    	}
    	global $Objects;
    	if (!is_array($list))
    		$list = explode(",",$list);
    	$obj = $Objects->get($mark);
    	if (!$obj->loaded)
    		$obj->load();
 		if ($obj->getRoleValue($obj->role["canUnlink"])=="false")
 			return 0;
 		$obj->removeLinks($list);
    	if (@$_POST["ajax"]==true)
    		return json_encode(array("removed_objects" => implode("~",$list)));
    	else
    		return array("removed_objects" => $removed_list);
    }
    

    function setRegisterMark($list,$mark) {
		global $Objects;
        	if (is_array($list)) {
    		$mark = $list["mark"];
    		$list = $list["entities"];
    	}
		if (!is_array($list))
            $list = explode(",",$list);
        $removed_list = array();
        $error_text = "";
        foreach ($list as $entity) {
            $obj = $Objects->get($entity);
            $obj->load();
            if (!$obj->deleted) {
    			if (count($obj->role)=="0")
    				$obj->setRole();
            	if ($mark=="1")
            		if ($obj->getRoleValue(@$obj->role["canRegister"])=="false")
            			continue;     
            	if ($mark=="0")
            		if ($obj->getRoleValue(@$obj->role["canUnregister"])=="false")
            			continue;            	 
            	$obj->registered = $mark;
            	$obj->save(true);
			}
			$removed_list[] = $obj->getId();			
        }
        if (@$_POST["ajax"]==true)        
            return json_encode(array("removed_objects" => implode("~",$removed_list)));
        else
            return array("removed_objects" => $removed_list);
    }

    function getDeletedObjects() {
    	global $Objects;
    	$result = array();
    	$adapter = $Objects->get("DocFlowDataAdapter_".$this->getId()."_".$this->object_id);
    	$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @deleted=1",$adapter,$this->getId());
    	if (is_array($entities) and count($entities)>0) {
    		foreach ($entities as $ent) {
    			$ent->loaded = false;
    			$ent->load();
    			$result[] = $ent;
    		}
    	}
    	return $result;    	
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "setDeletionMarkHook";
    		case '4': return "setRegisterMarkHook";   
    		case '5': return "unlinkHook"; 		
    	}
    	return parent::getHookProc($number);
    }
    
    function setDeletionMarkHook($arguments) {
    	if (isset($arguments["entities"]) and isset($arguments["mark"]))
    		echo $this->setDeletionMark($arguments["entities"],$arguments["mark"]);
    }
    
    function setRegisterMarkHook($arguments) {
    	if (isset($arguments["entities"]) and isset($arguments["mark"]))
    		echo $this->setRegisterMark($arguments["entities"],$arguments["mark"]);
    }
    
    function unlinkHook($arguments) {
    	if (isset($arguments["entities"]) and isset($arguments["mark"]))
    		echo $this->removeLinks($arguments["entities"],$arguments["mark"]);
    }
    
}
?>