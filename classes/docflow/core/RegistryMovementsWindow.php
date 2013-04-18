<?php
class RegistryMovementsWindow extends WABEntity {
	
	public $tabs = array();
	
	function construct($params) {
		parent::construct($params);
		$this->template = "renderForm";
		$this->document = "";
		$this->tabset_id = "WebItemTabset_".$this->module_id."_".get_class($this).$this->name;
		$this->tabsetName = $this->tabset_id;		
		$this->tabs_string = "";
		global $Objects;
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		$this->app = $app;
		$this->skinPath = $app->skinPath;
		$this->icon = $this->skinPath."images/docflow/registry.png";
		$this->tabs = array();
	}
	
	function getPresentation() {
		global $Objects;
		$arr = explode("_",$this->document);
		$obj = $Objects->get(array_shift($arr)."_".$this->module_id."_".array_pop($arr));
		if (is_object($obj)) {
			$obj->noPresent = false;
			$obj->loaded = false;
			$obj->load();
			return "Движения документа ".$obj->getPresentation();
		}
		else
			return "Движения документа";
	}
	
	function getArgs() {		
		global $Objects;
		$arr = explode("_",$this->document);
		$obj = $Objects->get(array_shift($arr)."_".$this->module_id."_".array_pop($arr));
		if (is_object($obj))
			$this->object_text = "Движения документа ".$obj->getPresentation();
		
		$adapter = $Objects->get("DocFlowDataAdapter_".$this->module_id."_adapter");
		$adapter->connect();
		$tabs = array();
		if ($adapter->connected) {
			$stmt = $adapter->dbh->prepare("SELECT DISTINCT classname FROM fields WHERE name='document' AND value='".$this->document."' AND classname LIKE 'Registry%'");
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $value) {
				$obj = $Objects->get($value["classname"]."_".$this->module_id."_1");
				$tabs[] = $value["classname"]."|".$obj->classTitle."|".$this->skinPath."images/spacer.gif";
				$this->tabs[] = $value["classname"];
			}				
		}
		$this->tabs_string = implode(";",$tabs);
		$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
		$object->item="'.$this->getId().'";
		$object->window_id="'.$this->window_id.'";
		$object->tabs_string="'.$this->tabs_string.'";
		$object->active_tab="'.$this->active_tab.'";';
			
		$this->tabsetCode = cleanText($this->tabsetCode);
		return parent::getArgs();		
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/RegistryMovementsWindow.html"));
		$out = $blocks["header"];
		$fname = "/var/WAB2/users/".$this->app->User."/arguments/".str_replace("_","",$this->getId());
		file_put_contents($fname,serialize(array("conditions" => array("document" => array("type" => "=", "value" => $this->document)))));
		$display = "";
		foreach ($this->tabs as $tab) {
			$arr = array("{tab}" => $tab, "{arguments}" => $fname, "{display}" => $display);
			$out .= strtr($blocks["tab"],$arr);
			if ($display=="")
				$display = "none";
		}
		$out .= $blocks["footer"];
		return $out;
	}
}