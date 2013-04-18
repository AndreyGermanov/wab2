<?php
class FileManagerProfile extends EntityProfile {
	
	public $ruleNames = array();
	
	function construct($params) {
		parent::construct($params);
		$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
		$this->tabs_string .= ";events|События|".$this->skinPath."images/spacer.gif";
		$this->ruleNames = array ("fmCanUpload","fmCanCreateFolder","fmCanRename","fmCanCopyMove","fmCanDelete","fmCanSetProperties","rootPath");
		$this->eventNames = array("*","ENTITY_OPENED","ENTITY_CLOSED","FM_MAKEDIR","FM_RENAME","FM_COPY","FM_MOVE","FM_DELETE","FM_UPLOAD");
	}
		
	function renderForm_2() {
		return "";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/core/EntityProfile.html"));
		$out = $blocks["header"];
		$methods = get_class_methods(get_class($this));
		natsort($methods);
		foreach ($methods as $method) {
			if (stripos($method,"renderForm_")!==FALSE)
				$out .= $this->$method();
		}		
		$out .= $blocks["footer"];
		return $out;
	}
	
	function load() {
		
	}
	
	function getHookProc($number) {
		switch ($number) {
			case "3": return "saveProfile";
		}
		return parent::getHookProc($number);
	}
	
	function saveProfile_1($arguments) {
		foreach ($this->ruleNames as $ruleName) {
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
	
	function saveProfile_2($arguments) {
		return "";
	}	
	
	function saveProfile($arguments) {
		$arguments = (array)$arguments;
		$methods = get_class_methods(get_class($this));
		natsort($methods);
		foreach ($methods as $method) {
			if (stripos($method,"checkData_")!==FALSE) {
				$result = $this->$method($arguments);
				if (!$result)
					return 0;
			}				
		}
		foreach ($methods as $method) {
			if (stripos($method,"saveProfile_")!==FALSE) {
				$this->$method($arguments);
			}
		}
		$this->file = $GLOBALS["roles"][$this->role]["file"];
		$str = "<?php\n".getMetadataString(getMetadataInFile($this->file))."\n?>";
		file_put_contents($this->file,$str);		
	}
}