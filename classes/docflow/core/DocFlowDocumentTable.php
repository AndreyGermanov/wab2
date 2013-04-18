<?php
/**
 * Класс реализует форму списка документов
 *
 * @author andrey
 */
class DocFlowDocumentTable extends EntityDataTable {
	
	function construct($params) {
		parent::construct($params);
        global $Objects;
        $this->template = "templates/docflow/core/DocFlowDataTable.html";
        $this->handler = "scripts/handlers/docflow/core/DocFlowDocumentTable.js";
        $app = $Objects->get($this->module_id);
        $this->periodStart = "";
        $this->periodEnd = "";
        if (!$app->loaded)
            $app->load();
        $this->app = $app;
        $this->adapter = $app->getAdapter($this);
        $this->clientClass = "DocFlowDocumentTable";
        $this->parentClientClasses = "EntityDataTable~DataTable~Entity";
        $this->tagsCondition = "";
        $this->serverName = $_SERVER["SERVER_NAME"];
        $this->showQRCode = "0";  
	}
	
	function load($em="",$dbEntity="",$force=false) {
		global $Objects;
		$this->objRole = "";
        $doc = $Objects->get($this->className."_".$this->module_id."_List");
        if (!is_object($doc))
        	return 0;
        if (!$doc->loaded)
			$doc->load();
        if (count($doc->role)==0)
        	$doc->setRole();
        $doc->getArgs();
        $this->helpGuideId = $doc->helpGuideId;
        $this->helpButtonDisplay = $doc->helpButtonDisplay;
        $this->classTitle = $doc->classTitle;
        $this->classListTitle = $doc->classListTitle;
        $this->objRole = str_replace("'","``",$doc->roleStr);
        if ($this->parent_object_id!="") {
			$this->allFieldList = $doc->allFieldList;
			$this->printFieldList = $doc->printFieldList;
			$this->conditionFields = $doc->conditionFields;
			$this->sortField = $doc->sortField;
		}
		$this->doc=$doc;
		$this->profileClass = $doc->profileClass;
		if (trim($this->additionalCondition)!=trim($doc->getRoleValue(@$doc->role["listFilter"])))
			$this->additionalCondition .= " ".$doc->getRoleValue(@$doc->role["listFilter"]);
		if ($this->hierarchy)
			$this->parentDisplay = "";
		else
			$this->parentDisplay = "none";
		$this->topLinkRole = "";
		parent::load($em,$dbEntity,$force);
	}
	
    function printDocument($formName="") {
		global $Objects;
		if (file_exists("tmp/".$this->getId()."_printForm"))
			return file_get_contents("tmp/".$this->getId()."_printForm");
	}
	
	function createPrintForm($arguments=null) {
		global $Objects;
		$str = "";
		$this->getArgs();
		if (isset($arguments))
			$this->setArguments($arguments);
		$str .= '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body bgcolor="#FFFFFF"/>';
		$fieldsArray = explode("~",$this->printFieldList);
        $names = array();$titles=array();
        foreach ($fieldsArray as $field) {
			$parts = explode(" ",$field);
			if (count($parts)==2) {
				$titles[] = array_pop($parts);
				$names[] = implode(" ",$parts);
			} else {
				if (@$parts[1]=="AS") {
					$names[] = array_shift($parts)." ".array_shift($parts)." ".array_shift($parts);
					$titles[] = implode(" ",$parts);
				} else {
					$names[] = array_shift($parts);
					$titles[] = implode(" ",$parts);
				}
			}
		}
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/printForms/list.html"));
		$header = $blocks["header"];
		$titleCell = $blocks["titleCell"];
		$cell = $blocks["cell"];
		$endRow = $blocks["endRow"];
		$string = $blocks["str"];
		$str .=$header;
		foreach ($titles as $title) {
			$str.=str_replace("{fieldName}",$title,$titleCell);
		}
		if ($this->showQRCode)
			$str.=str_replace("{fieldName}","Штрихкод",$titleCell);		
		$str .= $endRow;
		if ($this->additionalCondition!="") {
			$this->condition = str_replace("@parent IS NOT EXISTS","",$this->condition);
			if ($this->condition!="")
				$this->condition .= " AND ".$this->additionalCondition;
			else
				$this->condition .= $this->additionalCondition;
		}
		$this->condition = str_replace("xoxoxo","'",str_replace($this->module_id."_","",$this->condition));
		$this->condition = str_replace("#xo","'",$this->condition);
		$this->doc = $Objects->get($this->className."_".$this->module_id."_List");
		if ($this->condition=="@parent IS NOT EXISTS")
			$this->condition = "";
		if ($this->condition!="")
			$this->condition = "WHERE ".$this->condition." AND @classname='".get_class($this->doc)."'";
		else
			$this->condition = "WHERE @classname='".get_class($this->doc)."'";		
		$query = "SELECT ".implode(",",$names).",entityId,parent,isGroup FROM fields ".$this->condition." ORDER BY parent ASC,".$this->sortField;
		$persistedArray = $this->doc->getPersistedArray();
		$rows = PDODataAdapter::makeQuery($query,$this->adapter,$this->module_id);
		foreach($rows as $fields) {
			if ($fields["isGroup"]=="1")
				continue;
			$str .= $string;
			$i=0;
			foreach ($fields as $key=>$field) {
				if (isset($persistedArray[$key])) {
					$parts = explode("|",$persistedArray[$key]);
					$args = explode("~",$parts[1]);
					foreach ($args as $arg) {
						$arg_parts = explode("=",$arg);
						if ($arg_parts[0]=="type" and $arg_parts[1]=="date" and $field!="")
							$field = date("d.m.Y",substr($field,0,strlen($field)-3));
					}
				}
				if ($key=="entityId" and $this->showQRCode)
					$str .= str_replace("{fieldValue}",'<img src="utils/qr/qr.php?text=https://'.$_SERVER["SERVER_NAME"].'/bc.php?i='.$field.'"/>',$cell);
				else
					$str .= str_replace("{fieldValue}",$field,$cell);
				$i++;
				if (!$this->showQRCode) {
					if ($i>=count($names))
						break;
				} else {
					if ($i>=count($names)+1)
						break;						
				}
			}
			$str .= $endRow;
		}
		file_put_contents("tmp/".$this->getId()."_printForm",$str);
	}
	
	function findRecord($searchString,$exclude_entities="") {
		if (is_array($searchString)) {
			$arguments = $searchString;
			$exclude_entities = $searchString["exclude_entities"];
			$searchString = $searchString["searchString"];
			$this->setArguments($arguments);			
		}
		$fieldsArray = explode(",",$this->fieldList);
        $names = array();$titles=array();
        foreach ($fieldsArray as $field) {
			$parts = explode(" ",$field);
			if (count($parts)==2) {
				$titles[] = array_pop($parts);
				$names[] = implode(" ",$parts);
			} else {
				if (@$parts[1]=="AS") {
					$names[] = array_shift($parts);
					$titles[] = implode(" ",$parts);
				} else {
					$names[] = array_shift($parts);
					$titles[] = implode(" ",$parts);
				}
			}
		}
		$search_query = array();
		foreach ($names as $name) {
			$search_query[] = "@".str_replace(".",".@",$name)." LIKE '%".$searchString."%'";
		}
		if ($exclude_entities!="")
			$not_in_query = " AND @entityId NOT IN ('".str_replace(",","','",$exclude_entities)."')";
		else
			$not_in_query = "";
		$search_query = "".implode(" OR ",$search_query)."";
		$this->condition = str_replace("xoxoxo","'",str_replace($this->module_id."_","",$this->condition));
		global $Objects;
		$this->condition = str_replace("#xo","'",$this->condition);
		$this->doc = $Objects->get($this->className."_".$this->module_id."_List");
		foreach ($names as $name) {
			$search_query = "@".str_replace(".",".@",$name)." LIKE '%".$searchString."%'";
			if ($this->condition!="")
				$condition = "WHERE ".$this->condition." AND ".$search_query.$not_in_query." AND @classname='".$this->className."'";
			else
				$condition = "WHERE ".$search_query.$not_in_query." AND @classname='".$this->className."'";
			$query = "SELECT entities FROM fields ".$condition." ORDER BY ".$this->sortField." LIMIT 1";
			$results = PDODataAdapter::makeQuery($query,$this->adapter,$this->module_id);
			if (count($results)>0) {
				break;
			}
		}
		if (isset($results) and $results!=0 and $results!="" and count($results)>0) {
			$number = @current($results)->name;
			$Objects->simpleQuery(get_class($this->doc)."_".$this->module_id,$names[0],$this->condition,$this->sortField,$this->adapter,"",&$number);
			echo @current($results)->getId()."|".$number;
		} 
	}
	
	function saveSettings($arguments=null) {
		global $Objects;
		$app = $Objects->get("Application");
		
		$object = $this;
		if (file_exists("/var/WAB2/users/".$app->User."/settings/".$this->getId())) {
			eval(file_get_contents("/var/WAB2/users/".$app->User."/settings/".$this->getId()));
		}		
		if (isset($arguments))
			$this->setArguments($arguments);
		if ($this->profileToRemove!="") {
			unset($this->listProfiles[$this->profileToRemove]);
		}
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		mkdir("/var/WAB2/users/".$app->User."/settings",0777,true);
		$str = "";
		if ($this->profileToRemove=="") {
			if ($this->fieldList!="")
				$this->listProfiles[$this->defaultListProfile]["fieldList"] = $this->fieldList;
			else
				unset($this->listProfiles[$this->defaultListProfile]["fieldList"]);
			if ($this->printFieldList!="")
				$this->listProfiles[$this->defaultListProfile]["printFieldList"] = $this->printFieldList;
			else
				unset($this->listProfiles[$this->defaultListProfile]["printFieldList"]);
			
			if ($this->condition!="") {
				$this->condition = str_replace('#xo',"'",$this->condition);
				$this->condition = str_replace('xoxoxo',"'",$this->condition);						
				$this->listProfiles[$this->defaultListProfile]["condition"] = $this->condition;
			} else 
				unset($this->listProfiles[$this->defaultListProfile]["condition"]);
					
			if ($this->tagsCondition!="") {
				$this->tagsCondition = str_replace('#xo',"'",$this->tagsCondition);
				$this->tagsCondition = str_replace('xoxoxo',"'",$this->tagsCondition);			
				$this->listProfiles[$this->defaultListProfile]["tagsCondition"] = $this->tagsCondition;
			} else
				unset($this->listProfiles[$this->defaultListProfile]["tagsCondition"]);
							
			if ($this->sortField!="") {
				$this->listProfiles[$this->defaultListProfile]["sortField"] = $this->sortField;
				$this->listProfiles[$this->defaultListProfile]["sortOrder"] = $this->sortField;
			} else {
				unset($this->listProfiles[$this->defaultListProfile]["sortField"]);				
				unset($this->listProfiles[$this->defaultListProfile]["sortOrder"]);				
			}
			if ($this->periodStart!="")
				$this->listProfiles[$this->defaultListProfile]["periodStart"] = $this->periodStart;
			else
				unset($this->listProfiles[$this->defaultListProfile]["periodStart"]);
					
			if ($this->periodEnd!="")
				$this->listProfiles[$this->defaultListProfile]["periodEnd"] = $this->periodEnd;
			else
				unset($this->listProfiles[$this->defaultListProfile]["periodEnd"]);
					
			if ($this->itemsPerPage!="")
				$this->listProfiles[$this->defaultListProfile]["itemsPerPage"] = $this->itemsPerPage;
			
			$this->listProfiles[$this->defaultListProfile]["showQRCode"] = $this->showQRCode;
		}
		$str = '$object->defaultListProfile = "'.$this->defaultListProfile.'";'."\n";
		$str .= '$object->listProfiles = '.getArrayCode($this->listProfiles).";\n";
		file_put_contents("/var/WAB2/users/".$app->User."/settings/".$this->getId(),$str);
	}
		
	function getPresentation() {
		return "Печать списка";
	}
	
	function getHookProc($number) {
		switch ($number) {
			case '6': return "createPrintForm";
			case '7': return "findRecord";
			case '8': return "saveSettings";
			case '9': return "removeProfile";
			case 'prn': return "printDocumentHook";
		}
		return parent::getHookProc($number);
	}
	
	function printDocumentHook($arguments) {
		$formName = "";
		if (isset($arguments["formName"]))
			$formName = $arguments["formName"];
		echo $this->printDocument($formName);
	}	
}
?>