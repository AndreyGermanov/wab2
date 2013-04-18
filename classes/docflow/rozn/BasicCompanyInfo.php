<?php
// Справочник базовых сведений об организации
class BasicCompanyInfo extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "BasicCompanyInfo";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Основные сведения о компании";
		$this->classTitle = "Основные сведения о компании";		
		$this->template = "renderForm";
        $this->fieldList = "firmName Организация~directorName Руководитель~buhgalterName Главный бухгалтер";
        $this->additionalFields = "name~deleted";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->overrided = "width,height";
        $this->conditionFields = "firmName~Организация~string|directorName~Директор~string|buhgalterName~Главный бухгалтер~string|kassirName~Кассир~string|kassaPrihodOsnovanie~Основание ПКО~string|referalDiscount~Скидка реферрала~integer|referrerDiscounts~Скидки реферреров~string|smsTemplate~Шаблон SMS~string|emailTemplate~Шаблон Email~string";        
        $this->sortOrder = "firmName ASC";
        $this->tabs_string  = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";discounts|Скидки|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";templates|Шаблоны|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".get_class($this).$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->skinPath = $this->app->skinPath;
        $this->icon = $this->app->skinPath."images/docflow/firm.png";
        $this->helpGuideId = "rozn_4";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/rozn/BasicCompanyInfo.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function getPresentation($full=true) {
		if ($this->noPresent)
			return "";
		$this->loaded = false;
		$this->load();
		if (!$full)
			return $this->firmName;
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->firmName;
		else
			return $this->firmName;
	}	
		
	function checkData() {		
		global $Objects,$roles;
		
		$this->referrerDiscounts = trim($this->referrerDiscounts);
		$this->referralDiscount = trim($this->referralDiscount);
		if ($this->referrerDiscounts!="") {
			$arr = explode(",",$this->referrerDiscounts);
			$all_values = 0;
			foreach($arr as $value) {
				if (!ctype_digit($value)) {
					$this->reportError("Скидки реферреров указаны неверно!");
					return 0;
				}						
				$all_values += $value;
			}
			if ($all_values>100) {
				$this->reportError("Общая сумма скидки реферрерам больше 100% !");
				return 0;
			}
		}
		if ($this->referralDiscount!="") {
			if (!ctype_digit($this->referralDiscount)) {
				$this->reportError("Скидка реферрала указана неверно!");
				return 0;
			}				
		}
		return parent::checkData();			
	}
}
?>