<?php
// Документ "Изменение состояния заказа"
class DocumentChangeOrderCondition extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentOrder";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Изменения состояния заказов";
		$this->classTitle = "Изменение состояния заказа";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~orderDocument.number AS orderNumber № заказа~orderCondition.title AS orderCondition Состояние заказа~title Комментарий";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Комментарий~string,orderDocument~Заказ~entity,orderCondition~Состояние заказа~entity";        
        $this->sortOrder = "docDate ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentOrder".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/crm/DocumentChangeOrderCondition.html"));
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
		$this->old_orderDocument = $this->orderDocument;
		$this->old_orderCondition = $this->orderCondition;
	}
			
	function checkData() {
		if (!$this->isGroup) {
			if ($this->orderDocument=="") {
				$this->reportError("Укажите заказ","save");
				return 0;
			}				
			if ($this->orderCondition=="") {
				$this->reportError("Укажите состояние заказа","save");
				return 0;
			}				
			if ($this->manager=="") {
				$this->reportError("Укажите ответственного менеджера","save");
				return 0;
			}				
			global $Objects;
			if (!is_object($this->orderDocument))
				$doc = $Objects->get($this->orderDocument);
			else
				$doc = $this->orderDocument;
			if (!$doc->loaded)
				$doc->load();
			if ($doc->docDate>$this->docDate) {
				$this->reportError("Дата изменения состояния заказа не может быть меньше даты создания заказа","save");
				return 0;				
			}
		}
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		if ($this->orderDocument!="") {
			$this->removeLinks($this->old_orderDocument);
			$this->setLinks(array($this->orderDocument));
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
		$record = $Objects->get("RegistryOrderConditions_".$this->module_id."_");
		$record->document = $this;
		$record->regDate = $this->docDate;
		$record->orderDocument = $this->orderDocument;
		$record->orderCondition = $this->orderCondition;	
		$record->save(true);
		$Objects->remove("RegistryOrderConditions_".$this->module_id."_");
	}	
}
?>