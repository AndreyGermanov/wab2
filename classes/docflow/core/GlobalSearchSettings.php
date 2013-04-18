<?php
class GlobalSearchSettings extends WABEntity {
	
	public $classesList = array();
	public $fieldsList = array();
		
	function construct($params) {
		global $Objects;
		parent::construct($params);
		$this->template = "renderForm";
		$this->handler = "scripts/handlers/core/EntityProfile.js";
		if ($this->module_id!="") {
			$this->tabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."Tabset";
			$this->tabsetName = $this->tabset_id;
			$this->fieldsTabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."FieldsTabset";
			$this->fieldsTabsetName = $this->fieldsTabset_id;
		}
		else {
			$this->tabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."Tabset";
			$this->tabsetName = str_replace("_","",$this->getId())."Tabset";
			$this->fieldsTabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."FieldsTabset";
			$this->fieldsTabsetName = str_replace("_","",$this->getId())."FieldsTabset";
		}
		
		$this->tabs_string = "classes|Объекты|".$this->skinPath."images/spacer.gif;";
		$this->tabs_string.= "fields|Поля|".$this->skinPath."images/spacer.gif";
		$this->active_tab = "classes";
		$this->width = "600";
		$this->height = "450";
		$this->overrided = "width,height";		
		$this->icon = $this->skinPath."images/Buttons/gfind.png";		
	}
	
	function getArgs() {
		$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
		$object->item="'.$this->getId().'";
		$object->window_id="'.$this->window_id.'";
		$object->tabs_string="'.$this->tabs_string.'";
		$object->active_tab="'.$this->active_tab.'";';
			
		$this->tabsetCode = cleanText($this->tabsetCode);	
		return parent::getArgs();	
	}
	
	function getId() {
		return get_class($this)."_".$this->module_id."_".$this->name;
	}
	
	function getPresentation() {
		return "Настройка глобального поиска";
	}
			
	function renderForm_1() {
    	global $objGroups,$Objects;
    	$arr = array("references" => "Справочник","documents" => "Документ");
    	$tabblocks = getPrintBlocks(file_get_contents("templates/docflow/core/GlobalSearchSettings.html"));    	 
    	$blocks = getPrintBlocks(file_get_contents("templates/interface/CheckboxTable.html"));
    	$out = $tabblocks["header"].$tabblocks["classesHeader"].$blocks["header"];
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
    	$tabblocks = getPrintBlocks(file_get_contents("templates/docflow/core/GlobalSearchSettings.html"));    	 
		$out = $tabblocks["fieldsHeader"].$blocks["header"];
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
		$out .= $blocks["footer"].$tabblocks["fieldsFooter"].$tabblocks["footer"];
		return $out;
	}
			
	function renderForm() {
		$out = $this->renderForm_1();
		$out .= $this->renderForm_2();
		return $out;
	}
	
	function setParamsHook($arguments) {
		$this->classesList = explode(",",$arguments["classesList"]);
		$this->fieldsList = explode(",",$arguments["fieldsList"]);
	}	
	
}