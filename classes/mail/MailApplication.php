<?php
class MailApplication extends WABEntity {
    public $app;
    function  construct($params="") {
        global $Objects,$config_user;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        if (isset($params[1])) {
            $this->module_id = @$params[0]."_".@$params[1];
            $this->object_id = @$params[1];
        }
        else
            $this->object_id = @$params[0];
        if (!isset($config_user))
            $config = @$Objects->get("AdminConfig_".$_SERVER["PHP_AUTH_USER"]);
        else
            $config = $Objects->get("AdminConfig_".$config_user);
        $sysconfig = $config;
        $module = $app->getModuleByClass("MailApplication_".$this->object_id);
        if ($module!=0) {
            $this->moduleName = $module["name"];
            foreach($module as $key=>$value)
            	$this->fields[$key] = $value;
            $this->MailScannerRulesTable = $this->mailScannerRulesTable;
            $this->dovecotUsersTable = $this->dovecotUsersFile;
            $this->mailBasePath = $this->mailPath;
            $this->addrbookFile = $this->addressBookFile;
            $this->addrbookDefaultFieldsFile = $this->addressBookDefaultFieldsFile;
            $this->addrbookRuleFilesPath = $this->addressBookRuleFilesPath;
            $this->postfixMailboxTable = $this->postfixMailboxesTable;
            $this->adminsFile = $app->apacheUsersTable;
            $this->rootPasswordFile = $app->rootPasswordFile;
            $this->sudoPasswordFile = $app->sudoPasswordFile;
            $this->networkSettingsStyle = $app->networkSettingsStyle;
            if ($this->networkSettingsStyle=="Debian")
            	$this->networkSettingsFile = $app->debianNetworkSettingsFile;
            else
            	$this->networkSettingsFile = $app->redHatNetworkSettingsFile;
            $shell = $Objects->get("Shell_shell");
            if ($this->remoteAddress!="")
            	$this->remotePath = $app->variablesPath."mounts/".$module["name"]."/";
            	if ($shell->exec_command(strtr($this->app->pingPortTestCommand,array("{address}" => $this->remoteAddress,"{port}" => 22)))==0) {
                $this->remoteSSHCommand = "ssh root@".$this->remoteAddress;
                $this->remotePath = $app->variablesPath."mounts/".$module["name"]."/";
                $shell->exec_command("fusermount -uz ".$this->remotePath);
                if (!file_exists($this->remotePath)) {
                    $shell->exec_command($app->makeDirCommand." -p ".$this->remotePath);
                    $shell->exec_command($app->chownCommand." ".$app->apacheServerUser." ".$this->remotePath);     
                }
                $shell->exec_command($app->sshFsCommand." root@".$this->remoteAddress.":/ ".$this->remotePath." -o uid=33,gid=33,allow_other,follow_symlinks");
            } else
                $this->down = true;
                        
            $this->networkSettingsStyle = $sysconfig->networkSettingsStyle;
            $this->redHatNetworkSettingsFile = $sysconfig->redHatNetworkSettingsFile;
            $this->debianNetworkSettingsFile = $sysconfig->debianNetworkSettingsFile;
            $this->debianNetworkSettingsTemplateFile = $sysconfig->debianNetworkSettingsTemplateFile;
            $this->debianNetworkRestartCommand = $sysconfig->debianNetworkRestartCommand;
            $this->redHatNetworkRestartCommand = $sysconfig->redHatNetworkRestartCommand;

            $this->network_style = $this->networkSettingsStyle;
            if ($this->network_style=="RedHat") {
                $this->network_config_file = $this->redHatNetworkSettingsFile;
                $this->restart_command = $this->redHatNetworkRestartCommand;
            }
            else {
                $this->network_config_file = $this->debianNetworkSettingsFile;
                $this->network_config_template_file = $this->debianNetworkSettingsTemplateFile;
                $this->restart_command = $this->debianNetworkRestartCommand;
            }

            $this->rootPasswordFile = $app->rootPasswordFile;

            $this->adminsFile = $app->adminsFile;
            $this->apacheUsersTable = $this->adminsFile;

            $this->tree_init_string = '$object->icon = "'.$app->skinPath.'images/Window/collectormx.jpg";$object->title="Mystix Collector MX";$object->setTreeItems();';
            $this->postAliasesTableCommand = $this->postmapCommand." ".$this->postfixAliasesTable;
            $this->postDomainsTableCommand = $this->postmapCommand." ".$this->postfixDomainsTable;
            $this->postMailboxTableCommand = $this->postmapCommand." ".$this->postfixMailboxTable;
            $this->postGenericTableCommand = $this->postmapCommand." ".$this->postfixGenericTable;
            $this->postTransportTableCommand = $this->postmapCommand." ".$this->postfixTransportTable;
        }

        $this->template = "templates/mail/MailApplication.html";
        $this->handler = "scripts/handlers/mail/MailApplication.js";
        $this->css=$app->skinPath."styles/MailApplication.css";
        $this->icon=$app->skinPath."images/Window/header-lva.gif";
        $this->clientClass = "MailApplication";
		$this->parentClientClasses = "Entity";
	    $this->classTitle = "Почтовый сервер";
	    $this->classListTitle = "Почтовый сервер";
    }

    function load() {   
        if (file_exists($this->remotePath."/etc/hostname")) {
            if (!file_exists($this->remotePath.$this->postfixConfigFile))
                    $postfix_config=fopen($this->remotePath.$this->postfixConfigFile,"w");
            if (!file_exists($this->remotePath.$this->postfixDomainsTable))
                    $postfix_domains=fopen($this->remotePath.$this->postfixDomainsTable,"w");
            if (!file_exists($this->remotePath.$this->postfixMailboxTable))
                    $postfix_mailboxes=fopen($this->remotePath.$this->postfixMailboxTable,"w");
            if (!file_exists($this->remotePath.$this->postfixAliasesTable))
                    $postfix_aliases=fopen($this->remotePath.$this->postfixAliasesTable,"w");
            if (!file_exists($this->remotePath.$this->fetchmailFile))
                    $fetchmail_file=fopen($this->remotePath.$this->fetchmailFile,"w");
            if (!file_exists($this->remotePath.$this->dovecotUsersTable))
                    $dovecot_users_table=fopen($this->remotePath.$this->dovecotUsersTable,"w");
            if (!file_exists($this->remotePath.$this->postfixGenericTable))
                    $postfix_generic=fopen($this->remotePath.$this->postfixGenericTable,"w");
            if (!file_exists($this->remotePath.$this->postfixTransportTable))
                    $postfix_transport=fopen($this->remotePath.$this->postfixTransportTable,"w");
            $postfix_config=fopen($this->remotePath.$this->postfixConfigFile,"r");
            $postfix_domains=fopen($this->remotePath.$this->postfixDomainsTable,"r");
            $postfix_mailboxes=fopen($this->remotePath.$this->postfixMailboxTable,"r");
            $postfix_aliases=fopen($this->remotePath.$this->postfixAliasesTable,"r");
            $fetchmail_file=fopen($this->remotePath.$this->fetchmailFile,"r");
            $dovecot_users_table=fopen($this->remotePath.$this->dovecotUsersTable,"r");
            $postfix_generic=fopen($this->remotePath.$this->postfixGenericTable,"r");
            $postfix_transport=fopen($this->remotePath.$this->postfixTransportTable,"r");
            $doms = file($this->remotePath.$this->postfixDomainsTable);
            for ($counter=0;$counter<count($doms);$counter++)
            {
                $dom = explode(" ",$doms[$counter]);
                $this->addMailDomain(trim($dom[0]));
            }

            $strings = file($this->remotePath.$this->MailScannerRulesTable);
            for ($counter=0;$counter<count($strings);$counter++) {
                $string = explode(" ",$strings[$counter]);
                if (trim($string[1])=="default") {
                    if (trim($string[2])!="deliver") {
                        $this->spambox = trim($string[3]);
                        break;
                    }
                }
            }

            fclose($postfix_domains);
            fclose($postfix_mailboxes);
            fclose($postfix_aliases);
            fclose($fetchmail_file);
            fclose($dovecot_users_table);
            fclose($postfix_generic);
            fclose($postfix_transport);
        }
        //$this->load_network_settings();
        
        $this->loaded = true;
    }


    function getMailDomains() {
        return explode(",",$this->mail_domain);
    }

    function addMailDomain($name) {
        if (strstr($this->mail_domain,$name.",") == false and strstr($this->mail_domain,",".$name) == false)
        {
            if ($this->mail_domain!="")
                $this->mail_domain .= ",".$name;
            else
                $this->mail_domain .= $name;
        }       
    }

    function removeMailDomain($name) {
        global $Objects;
        $domains = $Objects->get("MailDomains_".$this->module_id);
        $domains->remove($name);
    }

    function hasMailDomain($name) {
        global $Objects;
        
        $domains = $Objects->get("MailDomains_".$this->module_id);
        if (!$domains->loaded)
                $domains->load();
        return $domains->contains($name);
    }

    function getLocalMailboxes() {
        global $Objects;
        return $Objects->getObject("Mailboxes_".$this->module_id);
    }

    function getArgs() {
        if ($this->loaded=="") $this->load();
        $result = parent::getArgs();
        $result["{domains}"] = $this->mail_domain."|".$this->mail_domain;       
        if (trim($this->bootproto) == "dhcp")
                $result["{dhcp_checked}"] = "checked";
        else
            $result["{dhcp_checked}"] = "";
        return $result;
    }

    function getPresentation() {
        return "Панель управления";
    }

    function getId() {
        return "MailApplication_".$this->object_id;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "conTestHook";
    	}
    }
    
    function conTestHook($arguments) {
    	global $Objects;
    	$shell = $Objects->get("Shell_shell");
    	$app = $Objects->get($this->module_id);
    	if ($app->remoteSSHCommand=="")
    		$shell->exec_command("ping -c1 ".$arguments["ipaddr"]." >/dev/null 2>/dev/null;echo $?");
    	else
    		shell_exec($app->remoteSSHCommand." 'ping -c1 ".$arguments["ipaddr"]." >/dev/null 2>/dev/null;echo $?'");	 
    }
}
?>