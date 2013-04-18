<?php
// Справочник банковских счетов
class ReferenceBankAccounts extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceBankAccounts";
		$this->hierarchy = false;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Банковские счета";
		$this->classTitle = "Банковский счет";		
		$this->template = "renderForm";
        $this->fieldList = "contragent.title AS contragent Контрагент~RS № счета~BIK БИК~KS Корр.счет~bank.title AS bank Банк";
        $this->additionalFields = "name~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "600";
        $this->height = "400";        
        $this->conditionFields = "contragent~Контрагент~entity,contragent.title~Имя контрагента~string,RS~№ счета~string,BIK~БИК~integer,KS~Корр.счет~string,bank.title~Банк~string";        
        $this->sortOrder = "contragent.title ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceFiles".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->icon = $this->skinPath."images/docflow/bankaccount.png";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceBankAccounts.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}		
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function getPresentation() {
		global $Objects;
		$bank = "";
		if ($this->noPresent)
			return "";
		if (!$this->loaded)
			$this->load();
		if ($this->bank=="") {
			$bank = "";
		} else {
			if (!$this->bank->loaded)
				$this->bank->load();
			$bank = $this->bank->title;
		}
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->RS." в ".$bank;
		else
			return $this->RS." в ".$bank;
		
	}	
	
	function checkData() {
		if ($this->contragent=="") {
			$this->reportError("Укажите контрагента","save");
			return 0;
		}

		if ($this->bank=="") {
			$this->reportError("Укажите банк","save");
			return 0;
		}		
					
		if (!preg_match('/^[1-9][0-9]{19}$/',$this->RS)) {
			$this->reportError("Расчетный счет указан не верно","save");
			return 0;
		}

		if (!preg_match('/^[1-9][0-9]{19}$/',$this->KS)) {
			$this->reportError("Корр. счет указан не верно","save");
			return 0;
		}
			
		if (!preg_match('/^0[1-9][0-9]{7}$/',$this->BIK)) {
			$this->reportError("БИК указан не верно","save");
			return 0;				
		}
		
		if ($this->name!="")
			$query = "SELECT entities FROM fields WHERE @RS='".$this->RS."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
		else
			$query = "SELECT entities FROM fields WHERE @RS='".$this->RS."' AND @classname='".get_class($this)."'";
		$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
		if (count($result)>0) {
			$this->reportError("Такой расчетный счет уже есть в базе!","save");
			return 0;
		}			
		return parent::checkData();			
	}
}
?>