<?php
/**
 * Модуль интеграции Контроллера с модулем бизнес-приложений
 * 
 * Для интеграции требуется указать название модуля документооборота, как он
 * прописан в конфигурационном файле и включить флажок "Включить интеграцию".
 * 
 * Название модуля документооборота указано в параметре docflowModuleName,
 * а значение флажка указано в параметре docflowModuleCheck.
 *
 * @author andrey
 */
class DocFlowIntegrator extends WABEntity {
    
    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->mailModuleCheck = false;
        $this->mailModuleName = "";
        $this->template = "templates/controller/DocFlowIntegrator.html";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->intitiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/docflow/contragent.png";
        $this->css = $app->skinPath."styles/Mailbox.css";
        
        $this->clientClass = "DocFlowIntegrator";
        $this->parentClientClasses = "Entity";        
    }
    
    function getArgs() {
        if ($this->docflowModuleCheck)
            $this->docflowModuleCheckStr = "1";
        else
            $this->docflowModuleCheckStr = "0";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $names = array();
        $titles = array();
        foreach ($app->modules as $key=>$module) {
            $names[] = $key;
            $titles[] = $module["title"];
        }
        $this->modulesString = implode("~",$names)."|".implode("~",$titles);
        return parent::getArgs();
    }
    
    function load() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $docflowModule = $app->docFlowIntegration;
        if ($docflowModule == "") {
            $this->docflowModuleName = "";
            $this->docflowModuleCheck = false;
        } else {
            $this->docflowModuleName = $app->docFlowIntegration;
            if ($this->docflowModuleName!="")
                $this->docflowModuleCheck = true;
            else
                $this->docflowModuleCheck = false;
        }
        $this->loaded = true;
    }
    
    function save($arguments=null) {
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }        
        if (!$this->loaded)
            $this->load();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $module = $app->getModuleByClass($this->module_id);
        if ($this->docflowModuleCheck) {            
        	$module["docFlowIntegration"] = $this->docflowModuleName;
        } else {
        	$module["docFlowIntegration"] = "";
        }
		$GLOBALS["modules"][$module["name"]] = $module;
		$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
		file_put_contents($module["file"],$str);  
		$app->raiseRemoteEvent("DOCFLOWINTEGRATOR_CHANGED");		                      
        $this->loaded = true;
    }
    
    function getId() {
        return "DocFlowIntegrator_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return "Интеграция с модулем бизнес-приложений";
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>