<?php
/**
 * Класс, реализующий отчет
 *
 * @author andrey
 */
class Report extends WABEntity {
    
    function construct($params) {
        $old_params = $params;
        global $Objects;
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        if (count($params)>0)
            $this->name_set = true;
        $this->name = implode("_",$params);
        $this->old_name = $this->name;
        $app = $Objects->get($this->module_id);
        if (!$app->loaded)
            $app->load();
        $this->app = $app;
        
        $this->adapter = $app->getAdapter($this);
        $this->className = get_class($this);
        $this->defaultClassName = get_class($this);
        $this->childClass = $this->className;
        $this->objectid = str_replace("_","",$this->getId());
        
        $params = $old_params;
        parent::construct($params);
        $this->icon = $this->skinPath."images/Tree/report.png";
        $this->clientClass = "Report";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "Отчет";
        $this->classListTitle = "Отчеты";
		$this->classType = "Отчет";			
    }
    
    
    function getPresentation() {
		if ($this->noPresent)
			return "";
    	return "Анализ изменения показателей крови";
    }
        
    function afterInit() {
        return true;
    }
        
    function printReport($formName="") {
		return '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body bgcolor="#FFFFFF"/>';
	}
	
	function getPrintForms() {
		return array();
	}    
	
	function getHookProc($number) {
		switch($number) {
			case '3': return "printReportHook";
			case 'prn': return "printDocumentHook";				
		}
		return parent::getHookProc($number);
	}
	
	function printReportHook($arguments) {
		$this->setArguments($arguments);
		echo $this->printReport(@$arguments["formName"]);
	}
	
	function printDocumentHook($arguments) {
		$formName = "";
		if (isset($arguments["formName"]))
			$formName = $arguments["formName"];
		echo $this->printReport($formName);
	}	
}
?>