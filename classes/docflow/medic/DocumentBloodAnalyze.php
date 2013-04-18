<?php
/**
 * Класс реализует документ "Анализ крови"
 *
 * @author andrey
 */
class DocumentBloodAnalyze extends Document {

	public $codesTable = array();
    function construct($params) {
        parent::construct($params);
        global $Objects;        
        $this->fieldList = "number Номер~docDate Дата~patient.title AS patient Пациент~analyzeType.title AS analyzeType Тип анализа";
        $this->allFieldList = "number Номер~docDate Дата~patient.title AS patient Пациент~analyzeType.title AS analyzeType Тип анализа~comment Комментарий";
        $this->conditionFields = "number~Номер~integer,patient~Пациент~entity,patient.title~Имя пациента~string,analyzeType~Тип анализа~entity,analyzeType.title~Название типа анализа~string,comment~Комментарий~string";
        $this->printFieldList = $this->fieldList;
        $this->renderTemplate = "templates/docflow/medic/DocumentBloodAnalyze.html";
        $this->template = "renderForm";
        $this->handler = "scripts/handlers/docflow/medic/DocumentBloodAnalyze.js";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->objectid = str_replace("_","",$this->getId());
        $this->icon = $app->skinPath."images/Tree/document.png";       
        $this->width="750";
        $this->height="420";        
        $this->title = "Анализ крови";
        $this->skipChildrenCheck=true;
        $this->clientClass = "DocumentBloodAnalyze";
        $this->parentClientClasses = "Document~Entity";        
        $this->classTitle = "Анализ крови";
        $this->classListTitle = "Анализ крови";
    }
    
    function getArgs() {
        if (!$this->loaded)
            $this->load();
        if ($this->patient!="") {
            if (!$this->patient->loaded)
                $this->patient->load();
            $this->gender = $this->patient->gender;
        }
        else
            $this->gender = 1;
        $this->displayStyle = "none";
        $this->helpTopic = "";
        if ($this->analyzeType!="") {
			if (!$this->analyzeType->loaded)
				$this->analyzeType->load();
			if (trim($this->analyzeType->helpTopic)!="")
				$this->displayStyle = "";
			$this->helpTopic = trim($this->analyzeType->helpTopic);
		}
        $result = parent::getArgs();
        $result["{documentTableSafe}"] = str_replace(",","~",$this->documentTable);
        return $result;
    }
    
    function checkData() {
        if ($this->patient=="") {
            $this->reportError("Укажите пациента","save");
            return false;
        }
        if ($this->analyzeType=="") {
            $this->reportError("Укажите тип анализа","save");
            return false;
        }
        return parent::checkData();        
    }
    
    function register() {
		parent::register();
		$arr = explode("|",$this->documentTable);
		global $Objects;
		foreach ($arr as $key=>$value) {
			$parts = explode("~",$value);
			if ($parts[1]=="-")
				continue;
			$analyzeDef = $Objects->get("BloodDefinitionsReference_".$this->module_id."_".$parts[0]);
			$analyzeDefValue = $parts[1];
			$record = $Objects->get("RegistryBloodDefinitions_".$this->module_id."_");
			$record->document = $this;
			$record->regDate = $this->docDate;
			$record->patient = $this->patient;
			$record->analyzeType = $this->analyzeType;
			$record->analyzeDef = $analyzeDef;
			$record->analyzeDefValue = $analyzeDefValue;
			$record->save(true);
			$Objects->remove("RegistryBloodDefinitions_".$this->module_id."_");
		}
	}
	
	function getAnalyzeResults() {
		$args = array();
		global $Objects;
		$input = explode("|",$this->codesTable);
		$result = array();
		foreach ($input as $value) {
			$parts = explode("~",$value);
			$key = trim($parts[0]);
			$val = @trim($parts[1]);
			if ($val==0)
				$args["{".$key."}"] = "0";
			else
				$args["{".$key."}"] = $val;
		}
		$formulas = PDODataAdapter::makeQuery("SELECT title,formula FROM fields WHERE @classname='BloodAnalyzeResultsReference'",$this->adapter,$this->module_id);
		foreach ($formulas as $value2) {
			$obj = $this;
			$formula = $value2["formula"];
			foreach ($args as $key=>$v) {
				$formula = str_replace($key,$v,$formula);
			}
			$success = false;
			@eval($formula);
			if ($success)
				$result[] = $value2["title"];
		}
		return implode("<br>",$result);
	}
	
	function getPrintForms() {
		return array("print" => "Анализ крови");
	}
	
	function printDocument($printForm = "") {
		parent::printDocument($printForm);
		$tpl = "templates/docflow/medic/printForms/DocumentBloodAnalyze.html";
		$blocks = getPrintBlocks(file_get_contents($tpl));
		$res = $this->getArgs();
		$res["{docDate}"] = date("d.m.Y",substr($this->docDate,0,strlen($this->docDate)-3));
		$this->patient->load();
		$this->analyzeType->load();
		$res["{patientTitle}"] = $this->patient->title;
		$res["{analyzeTypeTitle}"] = $this->analyzeType->title;
		$result = parent::printDocument($printForm);
		$result .= strtr($blocks["header"],$res);
		$arr = explode("|",$this->documentTable);
		global $Objects;
		$str_block = $blocks["str"];
		$i=1;
		$this->codesTable = array();
		foreach ($arr as $line) {
			$parts = explode("~",$line);
			$def = $Objects->get("BloodDefinitionsReference_".$this->module_id."_".$parts[0]);
			if (!$def->loaded)
				$def->load();
			$this->codesTable[] = $def->code."~".$parts[1];
			if (is_object($def->dimension)) {
				if (!$def->dimension->loaded)
					$def->dimension->load();				
			} else 
				continue;
			$def->num = $i;
			$res = $def->getArgs();
			$res["{dimensionTitle}"] = $def->dimension->title;
			$res["{value}"] = $parts[1];
			if ($this->gender==1) {
				$res["{minValue}"] = $def->mMinV;
				$res["{maxValue}"] = $def->mMaxV;
			} else {
				$res["{minValue}"] = $def->wmMinV;
				$res["{maxValue}"] = $def->wmMaxV;
			}
			if ($res["{value}"]<$res["{minValue}"] or $res["{value}"]>$res["{maxValue}"])
				$result .=  strtr($blocks["selected-str"],$res);
			else
				$result .=  strtr($blocks["str"],$res);
			$i++;
		}
		$result .= $blocks["table-footer"];
		$this->codesTable = implode("|",$this->codesTable);
		$results = $this->getAnalyzeResults();
		if ($results=="")
			$res = array("{analyzeResults}" => "нет");
		else
			$res = array("{analyzeResults}" => $results);
		$result .= strtr($blocks["results"],$res);
		return $result;
	}
	
	function getHookProc($number) {
		switch ($number) {
			case '2': return "checkTable";
			case '3': return "showListHook";
			case '4': return "showListHookShow";
		}
		return parent::getHookProc($number);
	}
	
	function load() {
		parent::load();
		$this->old_patient = $this->patient;
		$this->old_analyzeType = $this->analyzeType;
	}	
	
	function afterSave($out=true) {
		parent::afterSave($out);
	
		if (is_object($this->patient))
			$this->patient = $this->patient->getId();
		
		if (is_object($this->analyzeType))
			$this->analyzeType = $this->analyzeType->getId();
		
		if (is_object($this->old_patient))
			$this->old_patient = $this->old_patient->getId();
		
		if (is_object($this->old_analyzeType))
			$this->old_analyzeType = $this->old_analyzeType->getId();
		
		if ($this->patient!="") {
			if ($this->old_patient!="") {
				$this->removeLinks(array($this->old_patient));
			}
			$this->setLinks(array($this->patient));
		}
		
		if ($this->analyzeType!="") {
			if ($this->old_analyzeType!="")
				$this->removeLinks(array($this->old_analyzeType));
			$this->setLinks(array($this->analyzeType));
		}
	}	
	
	function checkTable($arguments) {
		$object = $this;
		$object->load();
		$object->codesTable = $arguments["code_values"];
		$object->docDate = $arguments["docDate"];
		echo $object->getAnalyzeResults();
	}
	
	function showListHook($arguments=null) {
		$object = $this;
		$object->overrided='width,height';
		$object->width=750;$object->height=450;
		$object->className="*DocumentBloodAnalyze*";
		$object->defaultClassName="DocumentBloodAnalyze";
		$object->loaded=true;
		$object->template="templates/docflow/core/DocumentList.html";
		$object->title="Список документов Анализ крови";		
	}

	function showListHookShow($arguments=null) {
		$object = $this;
		$object->overrided='width,height';
		$object->width=750;$object->height=450;
		$object->className="*DocumentBloodAnalyze*";
		$object->defaultClassName="DocumentBloodAnalyze";
		$object->loaded=true;
		$object->template="templates/docflow/core/DocumentList.html";
		$object->title="Список документов Анализ крови";
		$object->show();
	}
	
	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);
		if ($class=="ReferencePatients") {
			$this->patient = $doc;
		}
	}		
}
?>