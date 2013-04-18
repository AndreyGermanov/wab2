<?php
// Документ "Счет на оплату"
class DocumentInvoice extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentInvoice";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Счета";
		$this->classTitle = "Счет";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~contragent.title AS contragent Контрагент~title Комментарий~invoiceSumma Сумма~invoiceNDS НДС";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "800";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Комментарий~string,contragent~Контрагент~entity,invoiceSumma~Сумма~decimal,invoiceNDS~НДС~decimal,contragentAccount~Банковский счет контрагента~entity,firm~Организация~entity,firmAccount~Банковский счет организации~entity";        
        $this->sortOrder = "docDate ASC";
        $this->tabs_string  = "main|Шапка|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";products|Таблица|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentInvoice".$this->name;
        $this->handler = "scripts/handlers/docflow/crm/DocumentInvoice.js";
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
        $this->isStampDisplay = "none";
        $this->createObjectList = array("DocumentContragentRequest" => "Обращение контрагента", "DocumentWorkReport" => "Отчет о работе", "ReferenceContacts" => "Контактное лицо", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/crm/DocumentInvoice.html"));
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
	}
			
	function checkData() {
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
	}
	
	function getArgs() {

		global $Objects;
		if (!$this->loaded)
			$this->load();
		$this->documentTableId = "DocumentInvoiceTable_".$this->module_id."_".$this->name;
		$this->documentTableCode = '$object->documentInvoice="'.$this->getId().'";$object->parent_object_id="'.$this->getId().'";';
		$this->invoiceSummaStr = $this->invoiceSumma;
		$this->invoiceNDSStr = $this->invoiceNDS;
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
		if ($class=="DocumentContract") {
			$this->contragent = $doc->contragent;
			$this->title = $doc->title;
		}
		if ($class=="DocumentOrder") {
			$this->contragent = $doc->contragent;
			$this->title = $doc->title;
		}
		if ($class=="ReferenceProjects") {
			$this->firm = $doc->firm;
			$this->title = $doc->title;
		}		
	}
	
	function getPrintForms() {
		return array("print" => "Счет на оплату");
	}	
	
	function printDocument($printForm = "") {
		parent::printDocument($printForm);
		$tpl = "templates/docflow/crm/printForms/DocumentInvoice.html";
		$blocks = getPrintBlocks(file_get_contents($tpl));
		$res = $this->getArgs();
		$this->firm->loaded = false;
		$this->firm->load();		
		$this->contragent->loaded = false;
		$this->contragent->load();		
		$res["{docDate}"] = date("d.m.Y",substr($this->docDate,0,strlen($this->docDate)-3));
		$res["{firmLogo}"] = $this->firm->logo;
		$res["{firmINN}"] = $this->firm->INN;
		$res["{firmKPP}"] = $this->firm->KPP;
		$res["{firmName}"] = $this->firm->title;
		$this->firmAccount->loaded = false;
		$this->firmAccount->load();		
		$res["{firmBIK}"] = $this->firmAccount->BIK; 
		$res["{firmKS}"] = $this->firmAccount->KS;
		$res["{firmRS}"] = $this->firmAccount->RS;
		$this->firmAccount->bank->loaded = false;
		$this->firmAccount->bank->load();
		$res["{firmBank}"] = $this->firmAccount->bank->title;
		$res["{number}"] = $this->number;
		
		$res["{firmText}"] = $this->firm->title;
		if ($this->firm->INN !="")
			$res["{firmText}"] .= ", <b>ИНН</b> ".$this->firm->INN;
		if ($this->firm->KPP !="")
			$res["{firmText}"] .= ", <b>КПП</b> ".$this->firm->KPP;
		if ($this->firm->officialAddress !="")
			$res["{firmText}"] .= ", <b>адрес</b> ".$this->firm->officialAddress;
		if ($this->firm->phones !="")
			$res["{firmText}"] .= ", <b>тел:</b> ".$this->firm->phones;
		$res["{contragentText}"] = $this->contragent->title;
		if ($this->contragent->INN!="")
			$res["{contragentText}"] .= ", <b>ИНН</b> ".$this->contragent->INN;
		if ($this->contragent->KPP!="")
			$res["{contragentText}"] .= ", <b>КПП</b> ".$this->contragent->KPP;
		if ($this->contragent->officialAddress!="")
			$res["{contragentText}"] .= ", <b>адрес</b> ".$this->contragent->officialAddress;
		if ($this->contragent->phones!="")
			$res["{contragentText}"] .= ", <b>тел:</b> ".$this->contragent->phones;
		
		$result = parent::printDocument($printForm);		
		$result .= strtr($blocks["header"],$res);
		
		$rows = explode("|",$this->documentTable);
		$num = 1;
		foreach ($rows as $row) {
			$cells = explode("~",$row);
			$res["{num}"] = $num;
			$res["{product}"] = $cells[0];
			$res["{ed}"] = $cells[1];
			$res["{cost}"] = $cells[2];
			$res["{count}"] = $cells[3];
			$res["{total}"] = $cells[7];
			$result .= strtr($blocks["row"],$res);		
			$num = $num+1;		
		}
		$num = $num-1;
		$res["{invoiceSumma}"] = $this->invoiceSumma;
		$res["{invoiceNDS"] = $this->invoiceNDS;
		$res["{summaStr}"] = num2str(floatval($this->invoiceSumma));		
		$res["{summaStr}"] = mb_strtoupper(mb_substr($res["{summaStr}"],0,1,"UTF8")).mb_substr($res["{summaStr}"],1,strlen($res["{summaStr}"])-1,"UTF8");
		$result .= strtr($blocks["footer"],$res);
		if (!$this->isStamp) {
			$this->firm->director->loaded = false;
			$this->firm->director->load();
			$this->firm->buhgalter->loaded = false;
			$this->firm->buhgalter->load();
			$res["{director}"] = $this->firm->director->title." ".mb_substr($this->firm->director->firstName,0,1,"UTF8").". ".mb_substr($this->firm->director->secondName,0,1,"UTF8").".";
			$res["{buhgalter}"] = $this->firm->buhgalter->title." ".mb_substr($this->firm->buhgalter->firstName,0,1,"UTF8").". ".mb_substr($this->firm->buhgalter->secondName,0,1,"UTF8").".";
			$result .= strtr($blocks["footerText"],$res);
		} else {
			$result .= strtr($blocks["footerImg"],$res);				
		}				
		return $result;
	}	
	
	function getHookProc($number) {
		switch($number) {
			case '10': return "getProductInfo";
		}
		return parent::getHookProc($number);
	}
	
	function getProductInfo($arguments) {
		global $Objects;
		$obj = $Objects->get(@$arguments["id"]);
		if (is_object($obj)) {
			$obj->load();
			if (is_object($obj->dimension)) {
				$obj->dimension->load();
				$dim = $obj->dimension->title;
			}
			else {
				$dim = "";
			}			
			echo json_encode(array("title" => $obj->title, "code" => $obj->code, "dimension" => $dim, "cost" => $obj->cost, "NDS" => $obj->NDS));
		} else
			echo json_encode(array());
	}
}
?>