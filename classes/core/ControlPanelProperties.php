<?php
/**
 * Модуль параметров панели управления сервером
 * 
 * @author andrey
 */
class ControlPanelProperties extends WABEntity {
    
    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->gatewayCheck = false;
        $this->gatewayIp = "";
        $this->template = "templates/interface/ControlPanelProperties.html";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->intitiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/control_panel.png";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->apacheServerUserPassword = "";
        $this->apacheServerUserPassword2 = "";
        $this->width = 550;
        $this->height = 200;
        $this->overrided = "width,height";
        $this->disableSsh = false;
        $this->clientClass = "ControlPanelProperties";
        $this->parentClientClasses = "Entity";
    }
    
    function getArgs() {
        if ($this->disableSsh)
            $this->disableSshStr = "1";
        else
            $this->disableSshStr = "0";
        
        return parent::getArgs();
    }
    
    function findDenyUser($DenyUser) {        
        global $Objects;
        $app = $Objects->get("Application");
        $app->initModules();
        $shell = $Objects->get("Shell_shell");
        $sshdConfig = file_get_contents($app->sshdConfigFile);
        $matches = array();
        if (preg_match("/DenyUsers (.*)$/",$sshdConfig,$matches)) {
            $denyUsers = $matches[1];
            $denyUsersList = explode(" ",$matches[1]);
            foreach ($denyUsersList as $denyUser) {
                if ($denyUser==$DenyUser) {
                    return $matches[0];
                }
            }
        }
        return false;
    }
    
    function load() {
        global $Objects;
        $app = $Objects->get("Application");
        $shell = $Objects->get("Shell_shell");
        if (!$app->initiated)
            $app->initModules();
        $users = str_replace("\n","~",trim($shell->exec_command($app->getActiveUnixUsersCommand." | grep -v 'root'")));
        
        $this->usersList = $users."|".$users;
        $this->apacheServerUser = $app->apacheServerUser;
        if ($this->findDenyUser($this->apacheServerUser)!=false) {
            $this->disableSsh = true;
        }
        $this->oldDisableSsh = $this->disableSsh;
        $this->oldApacheServerUser = $this->apacheServerUser;
        $this->loaded = true;
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "save";
		}
		return parent::getHookProc($number);
	}
	
    function save($arguments=null) {
        global $Objects;
        if (isset($arguments)) {
			$this->load();
			$this->setArguments($arguments);
		}
        $app = $Objects->get("Application");
        $shell = $Objects->get("Shell_shell");
        if ($this->apacheServerUserPassword != $this->apacheServerUserPassword2) {
            $this->reportError("Пароли не совпадают","save");
            return 0;
        } 
        if (!$this->loaded)
            $this->load();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $fp = fopen("/etc/WAB2/config/apache_restart.sh","w");
        fwrite($fp,$app->apacheRestartCommand);
        fclose($fp);        
        if ($this->oldDisableSsh != $this->disableSsh) {            
            $shell->exec_command($app->chownCommand." ".$this->oldApacheServerUser." ".$app->sshdConfigFile);
            if ($this->disableSsh==0) {                
                $denyUser = $this->findDenyUser($this->apacheServerUser);
                if (!$denyUser)
                    $denyUser = $this->findDenyUser($this->oldApacheServerUser);
                if ($denyUser) {
                    $arr = explode(" ",$denyUser);
                    $arr2 = array();
                    for ($i=0;$i<count($arr);$i++) {
                        if ($arr[$i]!=$this->apacheServerUser or $arr[$i]!=$this->oldApacheServerUser)
                            $arr2[] = $arr[$i];                            
                    }
                    if (count($arr2)>1)
                        $newDenyUser = implode(" ",$arr2);                    
                    else
                        $newDenyUser = "";
                    file_put_contents($app->sshdConfigFile,str_replace($denyUser,$newDenyUser,file_get_contents($app->sshdConfigFile)));                        
                }
            } else {
                $sshdConfig = file_get_contents($app->sshdConfigFile);
                $matches = array();
                if (preg_match("/DenyUsers (.*)$/",$sshdConfig,$matches)) {
                    $denyUser = $matches[0];
                    $newDenyUser = $matches[0]." ".$this->apacheServerUser;                    
                    file_put_contents($app->sshdConfigFile,str_replace($denyUser,$newDenyUser,file_get_contents($app->sshdConfigFile)));             
                } else {
                    file_put_contents($app->sshdConfigFile,file_get_contents($app->sshdConfigFile)."\nDenyUsers ".$this->apacheServerUser);
                }                    
            }
            $shell->exec_command($app->chownCommand." root ".$app->sshdConfigFile);
        }
        $shell->exec_command($app->sshdRestartCommand);
        if ($this->apacheServerUserPassword!="" and $this->apacheServerUserPassword==$this->apacheServerUserPassword2) {
            $this->password = $this->apacheServerUserPassword;
            
            if (file_exists($app->shadowFile)) {
                $shell->exec_command($app->chownCommand." ".$this->oldApacheServerUser." ".$app->shadowFile);
                $strings = file($app->shadowFile);
                $fp = fopen($app->shadowFile,"w");
                for($c=0;$c<count($strings);$c++) {
                    $line = explode(":",$strings[$c]);
                    if ($line[0]==$app->apacheServerUser)
                        $line[1]=crypt($this->password);
                    $line = implode(":",$line);
                    fwrite($fp,$line);
                }
                fclose($fp);
                $shell->exec_command($app->chownCommand." root ".$app->shadowFile);
            }

            if (file_exists($app->sudoPasswordFile)) {
	        $shell->exec_command($app->chownCommand." ".$this->oldApacheServerUser." ".$app->sudoPasswordFile);
    	        $fp = fopen($app->sudoPasswordFile,"w");
                fwrite($fp,"#!/bin/sh\n");
	        fwrite($fp,"echo ".$this->apacheServerUserPassword);
                fclose($fp);
	    }
        }
        
        $dir = "/etc/WAB2/config";        
        $shell->exec_command("at -f /etc/WAB2/config/apache_restart.sh now");
        $this->loaded = true;
    }
    
    function getId() {
        return "ControlPanelProperties_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return "Параметры панели управления";
    }    
}
?>