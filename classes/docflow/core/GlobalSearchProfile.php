<?php
class GlobalSearchProfile extends EntityProfile {
	
	public $eventNames = array();
	public $classesList = array();
	public $fieldsList = array();
	
	function construct($params) {
		parent::construct($params);
		global $Objects;
		$this->obj = $Objects->get($this->entityClass."_".$this->module_id."_".$this->entityId);
		if (is_object($this->obj)) {
			$this->obj->setSettings();
		}
		$this->tabs_string = "classes|Объекты|".$this->skinPath."images/spacer.gif;";
		$this->tabs_string.= "fields|Поля|".$this->skinPath."images/spacer.gif;";
		$this->tabs_string.= "events|События|".$this->skinPath."images/spacer.gif";
		$this->active_tab = "classes";			
		$this->eventNames = array ("*","ENTITY_OPENED","ENTITY_CLOSED");
	}
	
	function getId() {
		return get_class($this)."_".$this->module_id."_".$this->role."_".$this->profile;
	}
			
	function renderForm_1() {
    	global $objGroups,$Objects;
    	$arr = array("references" => "Справочник","documents" => "Документ");
    	$tabblocks = getPrintBlocks(file_get_contents("templates/docflow/core/GlobalSearchProfile.html"));    	 
    	$blocks = getPrintBlocks(file_get_contents("templates/interface/CheckboxTable.html"));
    	$out = $tabblocks["classesHeader"].$blocks["header"];
    	if (count($this->classesList)==0) {
    		if (is_object($this->obj)) {
    			if (isset($this->obj->settings["classesList"]))
    				$this->classesList = $this->obj->settings["classesList"];
    		}
    	}
    	foreach ($arr as $objtype=>$objtitle) {    		
	    	foreach ($objGroups[$objtype]["items"] as $value) {
	    		$args = array();
	    		$args["{id}"] = "obj_".$value;
	    		$class = $Objects->get($value."_".$this->module_id."_List");
	    		$args["{title}"] = $objtitle.":".$class->classTitle;
	    		if (array_search($value,$this->classesList)!==FALSE)
	    			$args["{value}"] = "1";
	    		else
	    			$args["{value}"] = "0";
	    		$out .= strtr($blocks["row"],$args);
			}
    	}
    	$out .= $blocks["footer"].$tabblocks["classesFooter"];
		return $out;
	}
	
	function renderForm_2() {
		global $fields,$Objects;
		$adapter = $Objects->get("DocFlowDataAdapter_".$this->module_id."_Adapter");
		if (!$adapter->connected)
			$adapter->connect();
        if ($adapter->connected) {
            $stmt = $adapter->dbh->prepare("SELECT DISTINCT name FROM fields");
	    	$stmt->execute();
  			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
		
		$blocks = getPrintBlocks(file_get_contents("templates/interface/CheckboxTable.html"));
    	$tabblocks = getPrintBlocks(file_get_contents("templates/docflow/core/GlobalSearchProfile.html"));    	 
		$out = $tabblocks["fieldsHeader"].$blocks["header"];
		if (count($this->fieldsList)==0) {
			if (is_object($this->obj)) {
				if (isset($obj->settings["fieldsList"]))
					$this->fieldsList = $obj->settings["fieldsList"];
			}
		}
		foreach ($res as $value) {
			$args = array();
			$args["{id}"] = "field_".$value["name"];
			if (isset($fields[$value["name"]]) and isset($fields[$value["name"]]["params"]["title"]))
				$args["{title}"] = $fields[$value["name"]]["params"]["title"];
			else
				$args["{title}"] = $value["name"];
			if (array_search($value,$this->fieldsList)!==FALSE)
				$args["{value}"] = "1";
			else
				$args["{value}"] = "0";
			$out .= strtr($blocks["row"],$args);
		}
		$out .= $blocks["footer"].$tabblocks["fieldsFooter"];
		return $out;
	}
			
	function load() {
		
	}
			
	function saveProfile_1($arguments) {
		$GLOBALS["roles"][$this->role][$this->profile]["classesList"] = array();
		foreach ($arguments as $classRule=>$value) {
			if (stripos($classRule,"obj_")!==FALSE and $value=="1") {
				$objName = str_replace("obj_","",$classRule);
				$GLOBALS["roles"][$this->role][$this->profile]["classesList"][] = $objName;
			}					
		}
	}	

	function saveProfile_2($arguments) {
		$GLOBALS["roles"][$this->role][$this->profile]["fieldsList"] = array();
		foreach ($arguments as $fieldRule=>$value) {
			if (stripos($classRule,"field_")!==FALSE and $value=="1") {
				$objName = str_replace("field_","",$classRule);
				$GLOBALS["roles"][$this->role][$this->profile]["fieldsList"][] = $objName;
			}
		}
	}		
}