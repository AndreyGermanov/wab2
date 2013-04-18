<?php
/**
 * Класс определяет таблицу с данными, таблицу, в которой в качестве значений
 * ячеек выступают элементы InputControl
 *
 * @author andrey
 */
class MailScannerRulesetEditor extends WABEntity {

    public $rules_array = array();

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->title = "Таблица правил ".$this->name;
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $mailapp = $Objects->get("MailApplication_".$this->module_id);
        $this->rulesFile = "";
        $this->valueType = "";
        $this->defaultValue = "";
        $this->width = "450";
        $this->height = "400";
        $this->overrided = "width,height";
        $this->loaded = false;
        $this->template="templates/mail/MailScannerRulesetEditor.html";        
        $this->css=$this->skinPath."styles/MailScannerConfig.css";
        $this->handler="scripts/handlers/mail/MailScannerRulesetEditor.js";
        $this->icon = $this->skinPath."images/Tree/templates.gif";
        $this->loaded = false;
        $this->clientClass = "MailScannerRulesetEditor";
        $this->parentClientClasses = "Entity";        
    }

    function load() {
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $mailapp = $Objects->get("MailApplication_".$this->module_id);
        if (file_exists($mailapp->remotePath.$this->rulesFile)) {
            $this->spamGetRulesCommand = str_replace("{file}",$this->rulesFile,$mailapp->spamGetRulesCommand);
            $strings = explode("\n",$shell->exec_command($mailapp->remoteSSHCommand." ".$this->spamGetRulesCommand));
            $this->rules_array = array();
            foreach($strings as $line) {
                $parts = explode(" ",$line);
                if (trim($parts[0])=="")
                    continue;
                if (strtoupper(trim($parts[0]))=="FROMORTO:" and strtoupper(trim($parts[1]))=="DEFAULT")
                    $this->defaultValue = trim(@$parts[2]);
            }
        }
        $this->loaded = true;        
    }

    function save($arguments=null) {
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        	if (!is_array($this->rules_array)) {
        		if (!is_object($this->rules_array))
        			$this->rules_array = json_decode($this->rules_array);
        		$this->rules_array = (array)$this->rules_array;
        	}
        }
        $mailapp = $Objects->get("MailApplication_".$this->module_id);        
        $fp = fopen($mailapp->remotePath.$this->rulesFile,"w");
        foreach ($this->rules_array as $value) {
            fwrite($fp,$value."\n");
        }
        fwrite($fp,"FromOrTo: default ".$this->defaultValue."\n");
        fclose($fp);
        
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
        $this->loaded = true;
    }

    function getId() {
        return "MailScannerRulesetEditor_".$this->module_id."_".$this->name;
    }


    function getPresentation() {
        return $this->title;
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{strValueType}"] = str_replace(",",".",$this->valueType);
        return $result;
    }

    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }    
}
?>