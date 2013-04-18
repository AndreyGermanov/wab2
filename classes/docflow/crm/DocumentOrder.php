<?php
// Документ "Заказ"
class DocumentOrder extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentOrder";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Заказы";
		$this->classTitle = "Заказ";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~contragent.title AS contragent Контрагент~orderCondition Состояние заказа~title Описание заказа~orderSumma Сумма";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = "docDate Дата~number Номер~contragent.title AS contragent Контрагент~title Описание заказа~orderSumma Сумма";
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Описание заказа~string,contragent~Контрагент~entity,contact~Контактное лицо~entity,orderSumma~Сумма~decimal";        
        $this->sortOrder = "docDate ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
		if ($this->name!="") {
			$this->tabs_string .= ";orderChanges|Состояние|".$this->skinPath."images/spacer.gif";
		}
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentOrder".$this->name;
        $this->handler = "scripts/handlers/docflow/crm/DocumentOrder.js";
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
        $this->createObjectList = array("DocumentContragentRequest" => "Обращение контрагента", "DocumentContract" => "Договор", "DocumentInvoice" => "Счет на оплату", "DocumentWorkReport" => "Отчет о работе", "ReferenceContacts" => "Контактное лицо", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/crm/DocumentOrder.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $blocks["notNew"];
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function load() {
		parent::load();
		$this->old_contragent = $this->contragent;
		$this->old_contact = $this->contact;
	}
			
	function checkData() {
		if ($this->title=="") {
			$this->reportError("Укажите наименование","save");
			return 0;
		}

		if (!$this->isGroup) {
			if ($this->contragent=="") {
				$this->reportError("Укажите контрагента","save");
				return 0;
			}				
		}
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		
		if (is_object($this->contragent))
			$this->contragent = $this->contragent->getId();
		
		if (is_object($this->contact))
			$this->contact = $this->contact->getId();
		
		if (is_object($this->old_contragent))
			$this->old_contragent = $this->old_contragent->getId();
	
		if (is_object($this->old_contact))
			$this->old_contact = $this->old_contact->getId();
		
		if ($this->contragent!="") {			
			if ($this->old_contragent!="") {
				$this->removeLinks(array($this->old_contragent));
			}
			$this->setLinks(array($this->contragent));
		}
		
		if ($this->contact!="") {
			if ($this->old_contact!="")
				$this->removeLinks(array($this->old_contact));
			$this->setLinks(array($this->contact));
		}			
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
		
		$this->changesfieldList = "docDate Дата~orderCondition.title AS orderCondition Состояние заказа~title Комментарий";
		$this->changesItemsPerPage = 10;
		$this->changesSortOrder = "docDate DESC";
		$this->changesSortField = $this->changesSortOrder;
		$this->changesConditionFields = "number~Номер~integer|docDate~Дата~date|orderDocument~Заказ~entity|orderCondition~Состояние~entity";
		$this->changesCondition = "@parent IS NOT EXISTS";
		
		$this->orderChangesTableId = "DocFlowDocumentTable_".$this->module_id."_".$this->name."orderChanges";
		
		$this->orderChangesTableFieldAccess = json_encode(array("orderDocument" => "read"));
		$this->orderChangesTableFieldDefaults = json_encode(array("orderDocument" => $this->getId()));
		
		$this->orderChangesCode = '$object->additionalCondition="@orderDocument.@name='.$this->name.'";
									$object->window_id="'.$this->window_id.'";
									$object->parent_object_id="'.$this->getId().'";
									$object->className="DocumentChangeOrderCondition";
									$object->defaultClassName="DocumentChangeOrderCondition";
									$object->fieldList="'.$this->changesfieldList.'";
									$object->itemsPerPage='.$this->changesItemsPerPage.';
									$object->currentPage=1;
									$object->hierarchy=0;
									$object->additionalFields="name~deleted~registered";
									$object->condition="'.$this->changesCondition.'";
									$object->conditionFields="'.$this->changesConditionFields.'";
									$object->adapterId="'.$this->adapter->getId().'";
									$object->autoload="false";
									$object->sortOrder="'.$this->changesSortOrder.'";';
		
		$this->orderChangesCode = cleanText($this->orderChangesCode);
		
		return parent::getArgs();
	}
	
	function __get($key) {
		if ($key=="orderCondition") {
			global $Objects;
			$reg = $Objects->get("RegistryOrderConditions_".$this->module_id."_1");
			$record = $reg->getValue(time()."000","orderCondition","@orderDocument='".get_class($this)."_".$this->name."'");
			if (is_object($record)) {
				if (!$record->loaded)
					$record->load();
				return $record->title;
			}
		}
		return parent::__get($key);
	}
	
	function register() {
		parent::register();
		global $Objects;
		$record = $Objects->get("RegistryOrderConditions_".$this->module_id."_");
		$record->document = $this;
		$record->regDate = $this->docDate;
		$record->orderDocument = $this;
		$record->orderCondition = $Objects->get("ReferenceOrderConditions_".$this->module_id."_42");	
		$record->save(true);
		$Objects->remove("RegistryOrderConditions_".$this->module_id."_");
		$Objects->remove("Registry_".$this->module_id."_");
	}	
	
	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);		
		if ($class=="ReferenceContragents") {
			$this->contragent = $doc;
		}
		if ($class=="DocumentContract") {
			$this->contragent = $doc->contragent;
			$this->title = $doc->title;
		}
		if ($class=="DocumentContragentRequest") {
			$this->contragent = $doc->contragent;
			$this->contact = $doc->contact;
			$this->requestText = $doc->requestText; 
			$this->title = $doc->title;
		}		
		if ($class=="ReferenceProjects") {
			$this->title = $doc->title;
		}
	}
}
?>