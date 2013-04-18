<?php
class RegistrySettingsDialog extends WABEntity {
	
	public $allFields = array();
	public $printProfile = array();
	
	function construct($params) {
		
		parent::construct($params);
		global $Objects,$fields;
		$this->template = "renderForm";
		$this->handler = "scripts/handlers/docflow/core/RegistrySettingsDialog.js";
		$this->app = $Objects->get("Application");
		if (!$this->app->initiated)
			$this->app->initModules();
		$this->skinPath = $this->app->skinPath;
		$this->icon = $this->skinPath."images/Tree/report.png";
		$this->regObj = $Objects->get($this->name."_".$this->module_id."_Settings");
		$this->width="650";
		$this->height = "450";		
		$arr = array();
		$all = "";
		if ($this->regObj->dimensions!="") {
			$dims = array_flip(explode(",",$this->regObj->dimensions));
			$dms = explode(",",$this->regObj->dimensions);
			foreach ($dms as $value) {
				$dims[$value."Value"] = count($dims);
			} 
			$dims = array_flip($dims);
			$all .= implode(",",$dims);
		}
		if ($this->regObj->recvs!="") {
			if ($all!="")
				$all = ",".$all;
			$all .= $this->regObj->recvs.",regDate,document";
		}
		$allFields = explode(",",$all);
		$perFields = $this->regObj->explodePersistedFields($this->regObj->persistedFields);
		foreach ($allFields as $value) {
			$this->allFields[$value] = @$perFields[$value]["params"]["title"];
		}
		$this->allFieldsStr = implode("~",array_keys($this->allFields))."|".implode("~",$this->allFields);
		$this->allFieldsJSON = json_encode($this->allFields);
				
        $this->tabs_string  = "fields|Поля|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "sort|Сортировка|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "groups|Группировка|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "totals|Итоги|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string  .= "conditions|Отбор|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".get_class($this).$this->name;
        $this->active_tab = "fields";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/RegistrySettingsDialog.html"));
		$out = $blocks["header"];
		$arr = array();
		$this->printProfile = objectToArray($this->printProfile);
		$perFields = $this->regObj->explodePersistedFields($this->regObj->persistedFields);
		foreach ($this->allFields as $key=>$value) {
			$arr["{fieldName}"] = $key;
			$type = $perFields[$key]["params"]["type"];
			$arr["{fieldTitle}"] = $perFields[$key]["params"]["title"];
			$arr["{fieldArgs}"] = $this->getClientInputControlStr($perFields[$key]["params"]);
			$arr["{fieldInvisible}"] = "none";
			$arr["{fieldValue2}"] = "";
			if ($this->printProfile["conditions"][$key]["type"] == " IN " || $this->printProfile["conditions"][$key]["type"] == " NOT IN ") {
				$arr["{fieldType}"] = "array";
				$arr["{itemPrototype}"] = $arr["{fieldArgs}"];
			} else {
				$arr["{fieldType}"] = $type;
				$arr["{itemPrototype}"] = "";					
			}
			if (isset($this->printProfile["conditions"][$key])) {				
				$arr["{fieldChecked}"] = "1";
				if (is_array($this->printProfile["conditions"][$key]["value"])) {
					$value = implode("~",$this->printProfile["conditions"][$key]["value"]);
					$arr["{fieldValue}"] = $value;
				} else {
					if ($this->printProfile["conditions"][$key]["type"]==" BETWEEN ") {
						$value1 = $this->printProfile["conditions"][$key]["value1"];
						$value2 = $this->printProfile["conditions"][$key]["value2"];
						$arr["{fieldValue}"] = $value1;
						$arr["{fieldValue2}"] = $value2;						
						$arr["{fieldInvisible}"] = "";
					} else {
						$value = $this->printProfile["conditions"][$key]["value"];						
						$arr["{fieldValue}"] = $value;
					}
				}
				$arr["{listValue}"] = $this->printProfile["conditions"][$key]["type"];
			}
			else {
				$arr["{fieldChecked}"] = "0";
				$arr["{fieldValue}"] = "";
				$arr["{itemPrototype}"] = "";
				$arr["{listValue}"] = "";				
			}
			if ($type=="decimal" or $type=="integer")
				$arr["{listType}"] = "list,=~!=~<~>~<=~>=~ IN ~ NOT IN ~ BETWEEN |равно~не равно~меньше~больше~меньше или равно~больше или равно~в списке~не в списке~между";
			else if ($type == "string" or $type=="text")
				$arr["{listType}"] = "list,=~!=~ LIKE ~ NOT LIKE ~ IN ~ NOT IN |равно~не равно~содержит~не содержит~в списке~не в списке";
			else if ($type == "date") {
				$arr["{listType}"] = "list,=~!=~ IN ~ NOT IN ~ BETWEEN |равно~не равно~в списке~не в списке~между";
			}
			else
				$arr["{listType}"] = "list,=~!=~ IN ~ NOT IN |равно~не равно~в списке~не в списке";
			$out .= strtr($blocks["row"],$arr);
		}
		$out .= $blocks["footer"];
		return $out;
	}
	
	function getArgs() {
		$this->printProfileJSON = json_encode($this->printProfile);
		
		$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
		$object->item="'.$this->getId().'";
		$object->window_id="'.$this->window_id.'";
		$object->tabs_string="'.$this->tabs_string.'";
		$object->active_tab="'.$this->active_tab.'";';

		$this->tabsetCode = cleanText($this->tabsetCode);
		
		return parent::getArgs();
	}
	
	function getPresentation() {
		return "Параметры отчета по регистру `".$this->regObj->classTitle."`";
	}
}