<?php
/**
 * Модуль интеграции Контроллера с почтовым сервером
 * 
 * Для интеграции требуется указать название модуля почтового сервера, как он
 * прописан в конфигурационном файле и включить флажок "Включить интеграцию".
 * 
 * Название модуля почтового сервера указано в параметре mailModuleName,
 * а значение флажка указано в параметре mailModuleCheck.
 *
 * @author andrey
 */
class MailIntegrator extends WABEntity {
    
    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->mailModuleCheck = false;
        $this->mailModuleName = "";
        $this->template = "templates/controller/MailIntegrator.html";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->intitiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/mail.gif";
        $this->css = $app->skinPath."styles/Mailbox.css";
        
        $this->clientClass = "MailIntegrator";
        $this->parentClientClasses = "Entity";        
    }
    
    function getArgs() {
        if ($this->mailModuleCheck)
            $this->mailModuleCheckStr = "1";
        else
            $this->mailModuleCheckStr = "0";
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
        $mailModule = $app->mailIntegration;
        if ($mailModule == "") {
            $this->mailModuleName = "";
            $this->mailModuleCheck = false;
        } else {
            $this->mailModuleName = $app->mailIntegration;
            if ($this->mailModuleName!="")
                $this->mailModuleCheck = true;
            else
                $this->mailModuleCheck = false;
        }
        $this->loaded = true;
    }
    
    function save($arguments=null) {
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        $app = $Objects->get("Application");
        if (!$this->loaded)
            $this->load();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $module = $app->getModuleByClass($this->module_id);
        if ($this->mailModuleCheck) {            
        	$module["mailIntegration"] = $this->mailModuleName;
        } else {
        	$module["mailIntegration"] = "";
        }
		$GLOBALS["modules"][$module["name"]] = $module;
		$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
		file_put_contents($module["file"],$str);      
		$app->raiseRemoteEvent("MAILINTEGRATOR_CHANGED");                  
        $this->loaded = true;
    }
    
    function getId() {
        return "MailIntegrator_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return "Интеграция с почтовым сервером";
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
    
}
?>