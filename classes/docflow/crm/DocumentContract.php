<?php
// Документ "Договор"
class DocumentContract extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentContract";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Договоры";
		$this->classTitle = "Договор";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~contragent.title AS contragent Контрагент~title Комментарий~summa Сумма~NDS НДС";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Комментарий~string,contragent~Контрагент~entity,summa~Сумма~decimal,NDS~НДС~decimal,contragentAccount~Банковский счет контрагента~entity,firm~Организация~entity,firmAccount~Банковский счет организации~entity";        
        $this->sortOrder = "docDate ASC";
        $this->tabs_string  = "main|Шапка|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";contractText|Текст|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentContract".$this->name;
        $this->handler = "scripts/handlers/docflow/crm/DocumentContract.js";
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
        $this->createObjectList = array("DocumentOrder" => "Заказ", "DocumentInvoice" => "Счет на оплату", "DocumentContragentRequest" => "Обращение контрагента", "DocumentWorkReport" => "Отчет о работе", "ReferenceContacts" => "Контактное лицо", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");        
        
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/crm/DocumentContract.html"));
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
		$this->old_contragent = $this->contragent;
		$this->old_firm = $this->firm;
		$this->old_contractTemplate = $this->contractTemplate;
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
			if ($this->firm=="") {
				$this->reportError("Укажите организацию","save");
				return 0;
			}				
			if ($this->firmAccount=="") {
				$this->reportError("Укажите банковский счет организации","save");
				return 0;
			}				
		}
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		
		if (is_object($this->contragent))
			$this->contragent = $this->contragent->getId();
		
		if (is_object($this->firm))
			$this->firm = $this->firm->getId();
		
		if (is_object($this->old_contragent))
			$this->old_contragent = $this->old_contragent->getId();
	
		if (is_object($this->old_firm))
			$this->old_firm = $this->old_firm->getId();
		
		if ($this->contragent!="") {			
			if ($this->old_contragent!="") {
				$this->removeLinks(array($this->old_contragent));
			}
			$this->setLinks(array($this->contragent));
		}
		
		if ($this->firm!="") {
			if ($this->old_firm!="")
				$this->removeLinks(array($this->old_firm));
			$this->setLinks(array($this->firm));
		}

		if ($this->contractTemplate!="") {
			if ($this->old_contractTemplate!="")
				$this->removeLinks(array($this->old_contractTemplate));
			$this->setLinks(array($this->contractTemplate));
		}		
	}
	
	function getArgs() {

		global $Objects;
		
		if (!is_object($this->contragent) and $this->contragent!="")
			$this->contragent = $Objects->get($this->contragent);
		
		if (!is_object($this->firm) and $this->firm!="")
			$this->firm = $Objects->get($this->firm);		
		
		if ($this->name=="") {
			if ($this->firm=="") {
				if ($this->manager!="") {
					if (!is_object($this->manager))
						$this->manager = $Objects->get($this->manager->getId());
					$this->firm = $this->manager->firm;
				}
			}
			if ($this->contragent!="") {
				if (!$this->contragent->loaded)
					$this->contragent->load();
				$this->contragentAccount = $this->contragent->defaultBankAccount;
			}
						
			if ($this->firm!="") {
				if (!$this->firm->loaded)
					$this->firm->load();
				$this->firmAccount = $this->firm->defaultBankAccount;
			}							
		}	

		$this->firmAccountFieldAccess = json_encode(array());
		$this->firmAccountFieldDefaults = json_encode(array());
		$this->contragentAccountFieldAccess = json_encode(array());
		$this->contragentAccountFieldDefaults = json_encode(array());
		
		if ($this->firm!="" and is_object($this->firm)) {
			$this->firmName = array_pop(explode("_",$this->firm->getId()));			
			$this->firmAccountFieldAccess = json_encode(array("contragent" => "read"));
			$this->firmAccountFieldDefaults = json_encode(array("contragent" => $this->firm->getId()));
		}
		
		if ($this->contragent!="" and is_object($this->contragent)) {
			$this->contragentName = array_pop(explode("_",$this->contragent->getId()));
			$this->contragentAccountFieldAccess = json_encode(array("contragent" => "read"));
			$this->contragentAccountFieldDefaults = json_encode(array("contragent" => $this->contragent->getId()));
		}
		
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
				
		return parent::getArgs();
	}
			
	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);		
		if ($class=="ReferenceContragents") {
			$this->contragent = $doc;
		}
		if ($class=="DocumentContragentRequest") {
			$this->contragent = $doc->contragent;
			$this->title = $doc->title;
		}
		if ($class=="DocumentOrder") {
			$this->contragent = $doc->contragent;
			$this->title = $doc->title;
		}
		if ($class=="DocumentInvoice") {
			$this->contragent = $doc->contragent;
			$this->title = $doc->title;
		}
	}
	
	function getPrintForms() {
		return array("print" => "Договор");
	}	
	
	function printDocument($printForm = "") {
		parent::printDocument($printForm);
		if (!$this->loaded)
			$this->load();
		
		$this->firm->loaded = false;
		$this->contragent->loaded = false;
		$this->firmAccount->loaded = false;
		$this->contragentAccount->loaded = false;

		$this->firm->load();
		$this->contragent->load();
		$this->firmAccount->load();
		$this->contragentAccount->load();
		
		$objArgs = $this->getArgs();
		$args = $this->firm->getArgs();
		$firmArgs = array();
		foreach ($args as $key=>$value) {
			$firmArgs[str_replace("{","{firm_",$key)] = $value;
		};
		$args = $this->contragent->getArgs();
		$contragentArgs = array();
		foreach ($args as $key=>$value) {
			$contragentArgs[str_replace("{","{client_",$key)] = $value;
		};
		$args = $this->firmAccount->getArgs();
		$firmAccountArgs = array();
		foreach ($args as $key=>$value) {
			$firmAccountArgs[str_replace("{","{firmAccount_",$key)] = $value;
		};
		$args = $this->contragentAccount->getArgs();
		$contragentAccountArgs = array();
		foreach ($args as $key=>$value) {
			$contragentAccountArgs[str_replace("{","{clientAccount_",$key)] = $value;
		};		
		
		$this->firmAccount->bank->loaded = false;
		$this->firmAccount->bank->load();
		$this->contragentAccount->bank->loaded = false;
		$this->contragentAccount->bank->load();
		$firmAccountArgs["{firmAccount_bank}"] = $this->firmAccount->bank->title;
		$contragentAccountArgs["{contragentAccount_bank}"] = $this->contragentAccount->bank->title;
		
		
		$args = mergeArrays($objArgs, $firmArgs);
		$args = mergeArrays($args, $contragentArgs);
		$args = mergeArrays($args, $firmAccountArgs);
		$args = mergeArrays($args, $contragentAccountArgs);
				
		$result .= strtr($this->contract,$args);
						
		return $result;
	}	
	
	function getHookProc($number) {
		switch($number) {
			case '4': return "getContractTemplateFields";
		}
		return parent::getHookProc($number);
	}
	
	function getContractTemplateFields($arguments) {
		$this->setArguments($arguments);
		if ($this->contractTemplateId!="") {
			global $Objects;
			$contract = $Objects->get($this->contractTemplateId);
			$contract->loaded = false;
			$contract->load();
			echo $contract->contract;			
		}
	}
}
?>