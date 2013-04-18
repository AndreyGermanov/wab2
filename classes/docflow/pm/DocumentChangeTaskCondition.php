<?php
// Документ "Изменение состояния задачи"
class DocumentChangeTaskCondition extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentChangeTaskCondition";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Изменения состояния задач";
		$this->classTitle = "Изменение состояния задачи";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~taskDocument.title AS title Задача~taskCondition.title AS taskCondition Состояние задачи~title Комментарий";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Комментарий~string,taskDocument~Задача~entity,taskCondition~Состояние задачи~entity";        
        $this->sortOrder = "docDate ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentChangeTaskCondition".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/pm/DocumentChangeTaskCondition.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function load() {
		parent::load();
		$this->old_taskDocument = $this->taskDocument;
		$this->old_taskCondition = $this->taskCondition;
	}
			
	function checkData() {
		if (!$this->isGroup) {
			if ($this->taskDocument=="") {
				$this->reportError("Укажите задачу","save");
				return 0;
			}				
			if ($this->taskCondition=="") {
				$this->reportError("Укажите состояние задачи","save");
				return 0;
			}				
			if ($this->manager=="") {
				$this->reportError("Укажите ответственного менеджера","save");
				return 0;
			}				
			global $Objects;
			if (!is_object($this->taskDocument))
				$doc = $Objects->get($this->taskDocument);
			else
				$doc = $this->taskDocument;
			if (!$doc->loaded)
				$doc->load();
			if ($doc->dateStart!="")
				if ($doc->docDate>$this->docDate) {
					$this->reportError("Дата изменения состояния задачи не может быть меньше даты начала задачи","save");
					return 0;				
				}
		}
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		if ($this->taskDocument!="") {
			$this->removeLinks($this->old_taskDocument);
			$this->setLinks(array($this->taskDocument));
		}
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
						
		return parent::getArgs();
	}
	
	function register() {
		parent::register();
		global $Objects;
		$record = $Objects->get("RegistryTaskConditions_".$this->module_id."_");
		$record->document = $this;
		$record->regDate = $this->docDate;
		$record->taskDocument = $this->taskDocument;
		$record->taskCondition = $this->taskCondition;	
		$record->save(true);
		$Objects->remove("RegistryTaskConditions_".$this->module_id."_");
		$Objects->remove("Registry_".$this->module_id."_");
	}	
}
?>