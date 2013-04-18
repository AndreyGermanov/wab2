<?php
/**
 * Класс справочника пациентов
 *
 * @author andrey
 */
class ReferencePatients extends Reference {
    
    function construct($params) {        
        parent::construct($params);
        global $Objects;	        
        $this->fieldList = "title Фамилия Имя Отчество~birthDate Дата рождения";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList."~gender Пол";
        $this->conditionFields = "title~ФИО~string,birthDate~Дата рождения~date";
        $this->sortOrder = "title ASC";
        $this->template = "renderForm";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules;
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/addrbook.gif";       
        $this->width="650";
        $this->height="450";
        $this->renderTemplate = "templates/docflow/medic/ReferencePatients.html";
        $this->classTitle = "Пациент";
        $this->classListTitle = "Пациенты";        
        $apacheUsers = $Objects->get("ApacheUsers_".$this->module_id."_Users");
        if (!$apacheUsers->loaded)
        	$apacheUsers->load();
        $arr = array();
        $arr[" "] = "";
        foreach($apacheUsers->apacheUsers as $value) {
        	$arr[$value->name] = $value->name;
        }        
        $this->persistedFields["account"] = array("params" => array("type" => "list,".implode("~",$arr)."|".implode("~",$arr)));
        $this->createObjectList = array ("DocumentBloodAnalyze" => "Анализ крови");
        $this->clientClass = "ReferencePatients";
        $this->parentClientClasses = "Reference~Entity";        
    }
    
    function renderForm() {
    	$blocks = getPrintBlocks(file_get_contents($this->renderTemplate));
    	$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
    	$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
    	if ($this->name!="") {
    		$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
    	}
    	$out .= $blocks["footer"];
        $this->classTitle = "Пациент";
        $this->classListTitle = "Пациенты";
    	return $out;
    }    
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "showListHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function showListHook($arguments=null) {
    	$object = $this;
        $object->overrided='width,height';                
        $object->width=650;$object->height=450;                
        $object->className="*ReferencePatients*";
        $object->defaultClassName="ReferencePatients";
        $object->loaded=true;
        $object->template="templates/docflow/core/ReferenceList.html";
        $object->title="Пациенты";
   	}      
}
?>