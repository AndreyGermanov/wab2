<?php
class MailSettings extends WABEntity {

    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->template = "templates/controller/NetworkSettings.html";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->css = $this->skinPath."styles/Mailbox.css";
        $this->width = "550";
        $this->height= "500";
        $this->overrided = "width,height";
        $this->icon = $this->skinPath."images/Tree/base_options.png";
        
        $this->mailHost = "";
        $this->relayHost = "";
        $this->saslUserName = "";
        $this->saslUserPassword = "";
        $this->rejectUnknownClient = 1;
        $this->clientRestrictions = "";
        $this->recipientRestrictions = "";
        $this->greylistCheck = 1;
        $this->mailboxSize = 0;
        $this->messageSize = 0;        
        $this->relayHostCheck = 0;

        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."MailSettings";
        $this->tabs_string = "system|Система|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "post|Почтовый сервер|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "system";
        $this->clientClass = "MailSettings";
        $this->parentClientClasses = "Entity";        
	    $this->classTitle = "Параметры почтового сервера";
	    $this->classListTitle = "Параметры почтового сервера";
    }

    function getId() {
        return "MailSettings_".$this->module_id."_".$this->name;
    }

    function getPresentation() {
        return "Параметры почтового сервера";
    }

    function load()
    {
        $this->ipaddr = "";
        $this->netmask = "";
        $this->gateway = "";
        $this->dns1 = "";
        $this->dns2 = "";
        $this->dns3 = "";
        $this->defaultDomain = "";
        $this->bootproto = "static";
        global $Objects;
        $app = $Objects->get($this->module_id);
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
            $gapp->initModules();
        $shell = $Objects->get("Shell_shell");
        
        $this->postfixConfigDir = str_replace("main.cf","",$app->postfixConfigFile);
        $this->postfixInConfigDir = str_replace("main.cf","",$app->postfixInConfigFile);
        $this->postfixOutConfigDir = str_replace("main.cf","",$app->postfixOutConfigFile);
        
        $this->hostname = $shell->exec_command($app->remoteSSHCommand." ".$gapp->hostnameCommand);
        $this->old_hostname = $this->hostname;
        if ($app->network_style == "RedHat") {
            if (file_exists($app->remotePath.$app->network_config_file)) {
                $network_settings = file($app->remotePath.$app->network_config_file);
                for ($counter=0;$counter<count($network_settings);$counter++) {
                    $parts = explode("=",$network_settings[$counter]);
                    $this->fields[trim(strtolower($parts[0]))] = $parts[1];
                }
            }
        } else {
            if (file_exists($app->remotePath.$app->network_config_file)) {
                $strings = file($app->remotePath.$app->network_config_file);
                foreach ($strings as $line) {
                    if (preg_match('/address (.*)/',$line,$matches)==1)
                        $this->ipaddr = trim($matches[1]);
                    if (preg_match('/iface eth0 inet (.*)/',$line,$matches)==1)
                        $this->bootproto = trim($matches[1]);
                    if (preg_match('/netmask (.*)/',$line,$matches)==1)
                        $this->netmask = trim($matches[1]);
                    if (preg_match('/gateway (.*)/',$line,$matches)==1)
                        $this->gateway = trim($matches[1]);
                    if (preg_match('/dns-nameservers (.*)/',$line,$matches)==1) {
                        $dnsses = explode(" ",trim($matches[1]));
                        $this->dns1 = @$dnsses[0];
                        $this->dns2 = @$dnsses[1];
                        $this->dns3 = @$dnsses[2];
                    }
                    if (preg_match('/dns-search (.*)/',$line,$matches)==1)
                        $this->defaultDomain = trim($matches[1]);
                }
                $this->old_ipaddr = $this->ipaddr;
            }
        }
        
        // Загружаем параметры почтового сервера
        $this->mailHost = $this->hostname.".".$this->defaultDomain;
        $this->relayHost = trim(str_replace("[","",str_replace("]","",$shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfShowCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "relayhost"))."'"))));
        if ($this->relayHost!="")
            $this->relayHostCheck = 1;
        else
            $this->relayHostCheck = 0;

        $this->clientRestrictions = trim(array_pop(explode("=",$shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfShowCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "smtpd_client_restrictions"))."'"))));
        $this->recipientRestrictions = trim(array_pop(explode("=",$shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfShowCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "smtpd_recipient_restrictions"))."'"))));
        $this->mailboxSize = trim($shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfShowCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "mailbox_size_limit"))."'"));
        $this->mailboxSize = ceil($this->mailboxSize/1048576);
        $this->messageSize = trim($shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfShowCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "message_size_limit"))."'"));                
        $this->messageSize = ceil($this->messageSize/1048576);
        $arr = getHashFromString($this->clientRestrictions);
        if (isset($arr["reject_unknown_client"]))
            $this->rejectUnknownClient = 1;
        else
            $this->rejectUnknownClient = 0;
        $arr = getHashFromString($this->recipientRestrictions);
        if (isset($arr["inet:127.0.0.1:10023"]))
            $this->greylistCheck = 1;
        else
            $this->greylistCheck = 0;
        if ($this->relayHostCheck) {
            $strings = file($app->remotePath.$app->saslPasswordFile);
            $string = trim($strings[0]);
            if ($string!="") {
                $arr = explode(" ",$string);
                array_shift($arr);
                $arr = explode(":",implode($arr));
                $this->saslUserName = trim($arr[0]);
                $this->saslUserPassword = trim($arr[1]);
            }
        }
        $this->oldRelayHost = $this->relayHost;
        $this->oldRelayHostCheck = $this->relayHostCheck;
        $this->oldRejectUnknownClient = $this->rejectUnknownClient;
        $this->oldGreylistCheck = $this->greylistCheck;
    }

    function save($arguments) {
        // сохранение настроек сети
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        $app = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        if ($this->relayHost!="") {
            if ($this->saslUserName=="") {
                $this->reportError("Укажите имя пользователя !");
                return 0;
            }
            if ($this->saslUserPassword=="") {
                $this->reportError("Укажите пароль пользователя !");
                return 0;
            }                     
            $this->relayHost = "[".$this->relayHost."]";
            $fp = fopen($app->remotePath.$app->saslPasswordFile,"w");            
            fwrite($fp,$this->relayHost." ".$this->saslUserName.":".$this->saslUserPassword);
            fclose($fp);
            if ($app->remoteSSHCommand=="")
				$shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "relayhost=".$this->relayHost))."");
			else
				$shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "relayhost=".$this->relayHost))."'");
        } else {
            $this->relayHost = "";
            $this->saslUserName = "";
            $this->saslUserPassword = "";
            $fp = fopen($app->remotePath.$app->saslPasswordFile,"w");
            fwrite($fp," ");
            fclose($fp);
            if ($app->remoteSSHCommand=="")
				$shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "relayhost="))."");
			else
				$shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "relayhost="))."'");
        }
        if ($this->rejectUnknownClient != $this->oldRejectUnknownClient) {
            if (!$this->rejectUnknownClient) {
                $arr = getHashFromString($this->clientRestrictions);
                unset($arr["reject_unknown_client"]);                
                $this->clientRestrictions = implode(" ",$arr);
            } else {
                $arr = getHashFromString($this->clientRestrictions);
                $arr["reject_unknown_client"] = "reject_unknown_client";
                $this->clientRestrictions = implode(" ",$arr);                
            }
            if ($app->remoteSSHCommand=="")
				$shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "smtpd_client_restrictions=".$this->clientRestrictions))."");
			else
				$shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "smtpd_client_restrictions=".$this->clientRestrictions))."'");
        }
        if ($this->greylistCheck != $this->oldGreylistCheck) {
            if (!$this->greylistCheck) {
                $arr = getHashFromString($this->recipientRestrictions);                
                unset($arr["check_policy_service"]);
                unset($arr["inet:127.0.0.1:10023"]);                
                $this->recipientRestrictions = implode(" ",$arr);
            } else {
                $arr = getHashFromString($this->recipientRestrictions);                                
                $arr["check_policy_service"] = "check_policy_service";
                $arr["inet:127.0.0.1:10023"] = "inet:127.0.0.1:10023";
                $this->recipientRestrictions = implode(" ",$arr);
            }
            if ($app->remoteSSHCommand=="")
				$shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "smtpd_recipient_restrictions=".$this->recipientRestrictions))."");
			else
				$shell->exec_command($app->remoteSSHCommand." '".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "smtpd_recipient_restrictions=".$this->recipientRestrictions))."'");
        }
        if ($this->mailboxSize=="") {
            $this->reportError("Укажите размер почтового ящика","save");
            return 0;
        } else {
            $this->mailboxSize = ceil($this->mailboxSize*1048576);
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "mailbox_size_limit=".$this->mailboxSize))."");
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixConfigDir,"{param}" => "mailbox_size_limit=".$this->mailboxSize))."");
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "mailbox_size_limit=".$this->mailboxSize))."");
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "virtual_mailbox_limit=".$this->mailboxSize))."");
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixConfigDir,"{param}" => "virtual_mailbox_limit=".$this->mailboxSize))."");
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "virtual_mailbox_limit=".$this->mailboxSize))."");
        }
        if ($this->messageSize=="") {
            $this->reportError("Укажите размер сообщения","save");
            return 0;
        } else {
            $this->messageSize = ceil($this->messageSize*1048576);
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "message_size_limit=".$this->messageSize))."");
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixConfigDir,"{param}" => "message_size_limit=".$this->messageSize))."");
            $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "message_size_limit=".$this->messageSize))."");
        }        
         
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
            $gapp->initModules();
        $fp = fopen($app->remotePath.$gapp->hostnameFile,"w");
        fwrite($fp,$this->hostname);
        fclose($fp);
        $shell->exec_command($app->remoteSSHCommand." hostname ".$this->hostname);
        $this->password = trim($this->password);
        if ($this->password!="") {
            if (file_exists($app->remotePath.$gapp->shadowFile)) {
                $strings = file($app->remotePath.$gapp->shadowFile);
                $fp = fopen($app->remotePath.$gapp->shadowFile,"w");
                for($c=0;$c<count($strings);$c++) {
                    $line = explode(":",$strings[$c]);
                    if ($line[0]=="root")
                        $line[1]=crypt($this->password);
                    $line = implode(":",$line);
                    fwrite($fp,$line);
                }
                fclose($fp);
            }
        }
        $fp = fopen($app->remotePath.$app->network_config_file,"w");
        if ($app->network_style == "RedHat") {
            fwrite($fp,"IPADDR=".$this->ipaddr."\n");
            fwrite($fp,"NETMASK=".$this->netmask."\n");
            fwrite($fp,"GATEWAY=".$this->gateway."\n");
            if ($this->dns1!="")
                fwrite($fp,"DNS1=".$this->dns1."\n");
            if ($this->dns2!="")
                fwrite($fp,"DNS2=".$this->dns2."\n");
            if ($this->dns3!="")
                fwrite($fp,"DNS3=".$this->dns3."\n");
            fclose($fp);
        } else {
            if (file_exists($app->network_config_template_file)) {
                $strings = file($app->network_config_template_file);
                $fp = fopen($app->remotePath.$app->network_config_file,"w");
                if ($this->defaultDomain!="")
                    $this->dns = $this->dns."\n"."dns-search ".$this->defaultDomain;
                $args = $this->getArgs();
                foreach ($strings as $string) {
                    fwrite($fp,strtr($string,$args));
                }
            }
            global $Objects;
            $shell = $Objects->get("Shell_shell");
            exec($app->remoteSSHCommand." 'echo ".$app->restart_command." | at now'");
            if ($this->ipaddr!=$this->old_ipaddr) {
                $gapp = $Objects->get("Application");
                if (!$gapp->initiated)
                    $gapp->initModules();
                $gapp->changeModuleIp($app->moduleName,$this->ipaddr);
            }
            fclose($fp);
        }
        $this->mailHost = $this->hostname.".".$this->defaultDomain;
        $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixInConfigDir,"{param}" => "myhostname=".$this->mailHost))."");
        $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixConfigDir,"{param}" => "myhostname=".$this->mailHost))."");
        $shell->exec_command($app->remoteSSHCommand." ".strtr($app->postconfEditCommand,array("{config}" => $this->postfixOutConfigDir,"{param}" => "myhostname=".$this->mailHost))."");
        $shell->exec_command($app->remoteSSHCommand." ".$app->postmapCommand." ".$app->saslPasswordFile."");
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand."");        
        $gapp->raiseRemoteEvent("MAILSETTINGS_CHANGED");
    }  
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>