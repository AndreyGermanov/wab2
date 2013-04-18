<?php
// Документ "Обращение контрагента"
class DocumentContragentRequest extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentContragentRequest";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Обращения";
		$this->classTitle = "Обращение";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~contragent.title AS contragent Контрагент~requestType.title AS requestType Тип обращения~requestForm.title AS requestForm Форма обращения~title Предмет обращения";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Предмет обращения~string,contragent~Контрагент~entity,contact~Контактное лицо~entity,requestType~Тип обращения~entity,requestForm~Форма обращения~entity";        
        $this->sortOrder = "docDate ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentContragentRequest".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
        $this->createObjectList = array("DocumentOrder" => "Заказ", "DocumentContract" => "Договор", "DocumentInvoice" => "Счет на оплату", "DocumentWorkReport" => "Отчет о работе", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/crm/DocumentContragentRequest.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function getPrintForms() {
		$this->setRole();
		if (@$this->role["printForms"]!=null && is_array(@$this->role["printForms"]) && count(@$this->role["printForms"]>0))
			return @$this->role["printForms"];
		else
			return array();
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
			if ($this->requestType=="") {
				$this->reportError("Укажите тип обращения","save");
				return 0;
			}
			if ($this->requestForm=="") {
				$this->reportError("Укажите форму обращения","save");
				return 0;
			}
			if ($this->contragent=="") {
				$this->reportError("Укажите контрагента","save");
				return 0;
			}				
		}
		return parent::checkData();			
	}

	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);
		if ($class=="ReferenceContragents") {
			$this->contragent = $doc;
		}
		if ($class=="DocumentOrder") {
			$this->contragent = $doc->contragent;
		}
		if ($class=="DocumentContract") {
			$this->contragent = $doc->contragent;
		}
		if ($class=="DocumentInvoice") {
			$this->contragent = $doc->contragent;
		}		
	}	
	
	function afterSave($out=true) {
		parent::afterSave($out);
		if ($this->contragent!="") {
			$this->removeLinks($this->old_contragent);
			$this->setLinks(array($this->contragent));
		}
		if ($this->contragent!="") {
			$this->removeLinks($this->old_contact);
			$this->setLinks(array($this->contact));
		}		
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
						
		return parent::getArgs();
	}
	
	function printDocument($printForm = "") {
		if (!$this->loaded)
			$this->load();
		$result = parent::printDocument($printForm);
		$tpl = "templates/docflow/crm/printForms/Letter.html";
		$blocks = getPrintBlocks(file_get_contents($tpl));
		$result .= $blocks["header"];
		if ($this->sign=="1")
			$result .= $blocks["sign"];
		$result .= $blocks["footer"];
		$this->manager->load();
		$result = strtr($result,array("{docDate}" => date("d.m.Y",substr($this->docDate,0,strlen($this->docDate)-3)),"{requestText}" => $this->requestText,"{skinPath}" => $this->skinPath,"{userName}" => $this->manager->title." ".mb_substr($this->manager->firstName,0,1,"UTF8").". ".mb_substr($this->manager->secondName,0,1,"UTF8")."."));
		return $result;
	}
}
?>