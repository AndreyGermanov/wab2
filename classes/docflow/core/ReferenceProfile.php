<?php
class ReferenceProfile extends EntityProfile {
	
	public $refRuleNames = array();
	
	function construct($params) {
		parent::construct($params);
		$this->tabs_string.= ";reference|Справочник|".$this->skinPath."images/spacer.gif";
		$this->refRuleNames = array("canDelete","canUndelete","canAdd","canAddCopy","canPrint","canPrintList","canSetProperties","canSaveListSettings","canFilter","canUnfilter","listFilter");
	}
				
	function renderForm_3() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/ReferenceProfile.html"));
		$out = $blocks["referenceHeader"];
		$ruleNames = $this->refRuleNames;
		$args = array();
		foreach ($ruleNames as $ruleName) {
			$args["{referenceRuleName}"] = $ruleName;
			$ruleValue = @$GLOBALS["roles"][$this->role][$this->profile][$ruleName];			
			if (@$ruleValue[0]=="[") {
				$args["{referenceRuleCodeName}"] = strtr($ruleValue,array("[" => "","]" =>""));
				$args["{referenceRuleValue}"] = "code";	
				$args["{codeDisplayStyle"] = "";			
			} else {
				$args["{referenceRuleValue}"] = $ruleValue;
				$args["{referenceRuleCodeName}"] = "";
				$args["{codeDisplayStyle}"] = "none";			
			}
			$args["{referenceRuleHeader}"] = @$GLOBALS["fields"][$GLOBALS["profileItems"][$ruleName]]["params"]["title"];
			$args["{referenceRuleProperties}"] = getMetadataFieldParamsString(@$GLOBALS["profileItems"][$ruleName]);
			$args["{referenceRuleType}"] = @$GLOBALS["fields"][$GLOBALS["profileItems"][$ruleName]]["params"]["type"];
			$out .= strtr($blocks["referenceRulesRow"],$args);
		}
		$out .= $blocks["referenceFooter"];
		return $out;
	}
		
	function load() {
		
	}

	function saveProfile_3($arguments) {
		foreach ($this->refRuleNames as $ruleName) {
			if (isset($arguments[$ruleName])) {
				$value = $arguments[$ruleName];
				if ($value=="empty" or $value=="")
					unset($GLOBALS["roles"][$this->role][$this->profile][$ruleName]);
				else if ($value=="code")
					$GLOBALS["roles"][$this->role][$this->profile][$ruleName] = "[".@$arguments[$ruleName."Code"]."]";
				else
					$GLOBALS["roles"][$this->role][$this->profile][$ruleName] = @$arguments[$ruleName];
			}
		}
	}	
}