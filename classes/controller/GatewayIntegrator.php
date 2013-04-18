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
class GatewayIntegrator extends WABEntity {
    
    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->gatewayCheck = false;
        $this->gatewayIp = "";
        $this->template = "templates/controller/GatewayIntegrator.html";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->intitiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/firewall.png";
        $this->css = $app->skinPath."styles/Mailbox.css";
        
        $this->clientClass = "GatewayIntegrator";
        $this->parentClientClasses = "Entity";        
    }
    
    function getArgs() {
        if ($this->gatewayCheck)
            $this->gatewayCheckStr = "1";
        else
            $this->gatewayCheckStr = "0";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $names = array();
        $titles = array();
        foreach ($app->modules as $module) {
            $names[] = $module["name"];
            $titles[] = $module["title"];
        }
        $this->modulesString = implode("~",$names)."|".implode("~",$titles);
        return parent::getArgs();
    }
    
    function load() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $gatewayIp = $app->gatewayIp;
        if ($gatewayIp == null) {
            $this->gatewayIp = "";
            $this->gatewayCheck = false;
        } else {
            $this->gatewayIp = $app->gatewayIp;
            if ($this->gatewayIp!="")
                $this->gatewayCheck = true;
            else
                $this->gatewayCheck = false;
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
        $shell = $Objects->get("Shell_shell");
        if (!$this->loaded)
            $this->load();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $module = $app->getModuleByClass($this->module_id);
        if ($this->gatewayCheck and $this->gatewayIp!="") {
       		$module["gatewayIntegration"] = $this->gatewayIp;   
            $capp = $Objects->get($this->module_id);
            $shell->exec_command("ssh root@".$this->gatewayIp." mv ".$capp->gatewayIntegrationPath."_inactive ".$capp->gatewayIntegrationPath);
            $shell->exec_command("ssh root@".$this->gatewayIp." ".$app->debianNetworkRestartCommand);
        } else {
        	$module["gatewayIntegration"] = "";
            $capp = $Objects->get($this->module_id);
            $shell->exec_command($capp->gatewaySSHCommand." mv ".$capp->gatewayIntegrationPath." ".$capp->gatewayIntegrationPath."_inactive");
            $shell->exec_command($capp->gatewaySSHCommand." '".$app->debianNetworkRestartCommand."'");
        }
		$GLOBALS["modules"][$module["name"]] = $module;
		$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
		file_put_contents($module["file"],$str);   
		$app->raiseRemoteEvent("GATEWAYINTEGRATOR_CHANGED");                     
        $this->loaded = true;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return 'save';
    	}
    	return parent::getHookProc($number);
    }
    
    function getId() {
        return "GatewayIntegrator_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return "Интеграция с Интернет-шлюзом";
    }    
}
?>