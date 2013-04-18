<?php
/**
 * Description of RepFilterConfig
 *
 * @author andrey
 */
class RepFilterConfig extends WABEntity{

    public $rules_array = array();

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        global $Objects;
        $app = $Objects->get("Application");
        $this->skinPath = $app->skinPath;
        $this->icon = $this->skinPath."images/Tree/sites.gif";
        $this->css = $this->skinPath."styles/Mailbox.css";
        $app = $Objects->get("MailApplication_".$this->module_id);
        $this->template = "templates/mail/RepFilterConfig.html";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        $this->postfixInConfigFile = $app->postfixInConfigFile;
        $this->mailScannerConfigFile = $app->mailScannerConfigFile;
        $this->repFilterTable = $app->repFilterTable;
        $this->restartMailScannerCommand = $app->restartMailScannerCommand;
        $this->width = 450;
        $this->height = 450;
        $this->overrided = "width,height";
        $this->display_style = "none";
        $this->repofilter_checked = "";
        $this->clientClass = "RepFilterConfig";
        $this->parentClientClasses = "Entity";        
	    $this->classTitle = "Параметры репутационного фильтра";
	    $this->classListTitle = "Параметры репутационного фильтра";
    }

    function load() {
        if (file_exists($this->postfixInConfigFile)) {
            $strings = file($this->postfixInConfigFile);
            foreach ($strings as $line) {
                if (trim($line) == "smtpd_client_restrictions = permit_mynetworks permit_sasl_authenticated reject_unknown_client check_client_access proxy:tcp:localhost:6666") {
                    $this->display_style = "";
                    $this->repofilter_checked = "checked";
                }
            }
        }
        $this->loaded = true;
    }

    function save($arguments=null) {
    	if (isset($arguments)) {
    		$this->load();
    		$this->setArguments($arguments);
    		$this->rules_array = (array)$arguments["rules_array"];
    	}
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        if (file_exists($app->remotePath.$this->postfixInConfigFile)) {
            if ($this->repofilter_check=="checked") {
                $search_string = "#smtpd_client_restrictions = permit_mynetworks permit_sasl_authenticated reject_unknown_client check_client_access proxy:tcp:localhost:6666";
                $replace_string = "smtpd_client_restrictions = permit_mynetworks permit_sasl_authenticated reject_unknown_client check_client_access proxy:tcp:localhost:6666";
            } else {
                $search_string = "smtpd_client_restrictions = permit_mynetworks permit_sasl_authenticated reject_unknown_client check_client_access proxy:tcp:localhost:6666";
                $replace_string = "#smtpd_client_restrictions = permit_mynetworks permit_sasl_authenticated reject_unknown_client check_client_access proxy:tcp:localhost:6666";                
            }
            $found = false;
            $strings = file($app->remotePath.$this->postfixInConfigFile);
            $fp =fopen($app->remotePath.$this->postfixInConfigFile,"w");
            foreach($strings as $line) {
                if (str_replace("\n","",$line)==$search_string) {
                    fwrite($fp,str_replace("\n","",$replace_string)."\n");
                    $found = true;
                } else
                    fwrite($fp,str_replace("\n","",$line)."\n");
            }
            if ($found == false)
                fwrite($fp,str_replace("\n","",$replace_string)."\n");
            fclose($fp);
        }
        if ($this->repofilter_check=="checked") {
            $search_string = "Incoming Queue Dir = /var/spool/postfix/hold";
            $replace_string = "Incoming Queue Dir = /var/spool/postfix.in/hold";
            //$search_string1 = "Outgoing Queue Dir = /var/spool/postfix/incoming";
            //$replace_string1 = "Outgoing Queue Dir = /var/spool/postfix.in/incoming";
        } else {
            $search_string = "Incoming Queue Dir = /var/spool/postfix.in/hold";
            $replace_string = "Incoming Queue Dir = /var/spool/postfix/hold";
            //$search_string1 = "Outgoing Queue Dir = /var/spool/postfix.in/incoming";
            //$replace_string1 = "Outgoing Queue Dir = /var/spool/postfix/incoming";
        }
        if (file_exists($app->remotePath.$this->mailScannerConfigFile)) {
            $strings = file($app->remotePath.$this->mailScannerConfigFile);
            $fp = fopen($app->remotePath.$this->mailScannerConfigFile,"w");
            foreach($strings as $line) {
                if (str_replace("\n","",$line) == $search_string) {
                    fwrite($fp,str_replace("\n","",$replace_string)."\n");
                }
                else {
                    if (str_replace("\n","",$line) == $search_string1) {
                        fwrite($fp,str_replace("\n","",$replace_string1)."\n");
                    } else
                        fwrite($fp,str_replace("\n","",$line)."\n");
                }
            }
            fclose($fp);
        }
        $fp = fopen($app->remotePath.$this->repFilterTable,"w");
        foreach($this->rules_array as $line) {
            fwrite($fp,$line."\n");
        }
        fclose($fp);
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($app->remoteSSHCommand." ".$this->restartMailScannerCommand);
        $app = $Objects->get("Application");
        $app->raiseRemoteEvent("REPFILTER_CHANGED");
    }

    function getArgs() {
        $result = parent::getArgs();
        return $result;
    }

    function getPresentation() {
        return "Репутационный фильтр";
    }

}
?>