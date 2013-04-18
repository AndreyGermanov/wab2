<?php
class DocumentProfile extends ReferenceProfile {
	
	public $refRuleNames = array();
	
	function construct($params) {
		parent::construct($params);
		$this->tabs_string.= ";document|Документ|".$this->skinPath."images/spacer.gif";
		$this->docRuleNames = array("canRegister","canUnregister","canViewMovements");
	}
				
	function renderForm_4() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/DocumentProfile.html"));
		$out = $blocks["documentHeader"];
		$ruleNames = $this->docRuleNames;
		$args = array();
		foreach ($ruleNames as $ruleName) {
			$args["{documentRuleName}"] = $ruleName;
			$ruleValue = @$GLOBALS["roles"][$this->role][$this->profile][$ruleName];			
			if (@$ruleValue[0]=="[") {
				$args["{documentRuleCodeName}"] = strtr($ruleValue,array("[" => "","]" =>""));
				$args["{documentRuleValue}"] = "code";	
				$args["{codeDisplayStyle"] = "";			
			} else {
				$args["{documentRuleValue}"] = $ruleValue;
				$args["{documentRuleCodeName}"] = "";
				$args["{codeDisplayStyle}"] = "none";			
			}
			$args["{documentRuleHeader}"] = @$GLOBALS["fields"][$GLOBALS["profileItems"][$ruleName]]["params"]["title"];
			$args["{documentRuleProperties}"] = getMetadataFieldParamsString(@$GLOBALS["profileItems"][$ruleName]);
			$args["{documentRuleType}"] = @$GLOBALS["fields"][$GLOBALS["profileItems"][$ruleName]]["params"]["type"];
			$out .= strtr($blocks["documentRulesRow"],$args);
		}
		$out .= $blocks["documentFooter"];
		return $out;
	}
		
	function load() {
		
	}

	function saveProfile_4($arguments) {
		foreach ($this->docRuleNames as $ruleName) {
			if (isset($arguments[$ruleName])) {
				$value = $arguments[$ruleName];
				if ($value=="empty")
					unset($GLOBALS["roles"][$this->role][$this->profile][$ruleName]);
				else if ($value=="code")
					$GLOBALS["roles"][$this->role][$this->profile][$ruleName] = "[".@$arguments[$ruleName."Code"]."]";
				else
					$GLOBALS["roles"][$this->role][$this->profile][$ruleName] = @$arguments[$ruleName];
			}
		}
	}	
}