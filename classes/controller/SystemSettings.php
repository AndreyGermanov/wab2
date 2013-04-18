<?php
/**
 * Класс управляет окно настроек системы. В этом окне вводятся такие системные
 * параметры как имя сервера, рабочая группа, пароль пользователя root и
 * параметры для подключения к сети.
 *
 * @author andrey
 */
class SystemSettings extends WABEntity{
    public $app,$shell;
    function construct($params) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
		$this->models[] = "SystemSettings";
        $this->template="templates/controller/SystemSettings.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        $this->icon = $app->skinPath."images/Tree/settings.png";
        $this->skinPath = $app->skinPath;
        $this->network_style = $this->app->user->config["appconfig"]["networkSettingsStyle"];
        if ($this->network_style=="RedHat") {
            $this->network_config_file = $this->app->user->config["appconfig"]["redHatNetworkSettingsFile"];
            $this->restart_command = $this->app->user->config["appconfig"]["redHatNetworkRestartCommand"];
        }
        else {
            $this->network_config_file = $this->app->user->config["appconfig"]["debianNetworkSettingsFile"];
            $this->network_config_template_file = $this->app->user->config["appconfig"]["debianNetworkSettingsTemplateFile"];
            $this->restart_command = $this->app->user->config["appconfig"]["debianNetworkRestartCommand"];
        }

        $this->shell = $Objects->get("Shell_shell");

        $this->hostname = "";
        $this->password = "";
        
        $this->bootproto = 'static';
        $this->ipaddr = "";
        $this->netmask = "";
        $this->gateway = "";
        $this->dns1 = "";
        $this->dns2 = "";
        $this->dns3 = "";
        $this->dns = "";

        $gapp = $Objects->get($this->module_id);
        $this->gapp = $gapp;
        $this->ldap_port = $gapp->defaultLdapPort;
        if ($this->ldap_port=="636")
        	$this->ldap_proto = "ldaps";
        else
        	$this->ldap_proto = "ldap";
        $this->ldap_host = $gapp->defaultLdapHost;
        $this->ldap_user = $gapp->defaultLdapUser;
        $this->ldap_password = $gapp->defaultLdapPassword;
        $this->ldap_base = $gapp->defaultLdapBase;
        
        $this->width = "500";
        $this->height = "300";
        $this->overrided = "width,height";
        
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."SystemSettings";
        $this->tabs_string = "main|Основные|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "network|Сеть|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "database|База данных|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "main";
        
        $this->clientClass = "SystemSettings";
        $this->parentClientClasses = "Entity";
        
        $this->loaded = false;
    }

    function load() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $this->hostname = $this->shell->exec_command($app->remoteSSHCommand." ".$this->app->hostnameCommand);
        $this->old_hostname = str_replace("\n","",trim($this->hostname));
        if (file_exists($app->remotePath.$this->app->rootPasswordFile)) {
            $strings = file($app->remotePath.$this->app->rootPasswordFile);
            foreach ($strings as $line) {
                if (preg_match('/echo (.*)/',$line,$matches)==1)
                    $this->password = trim($matches[1]);
            }
        }
        if ($this->network_style == "RedHat") {
            if (file_exists($app->remotePath.$this->network_config_file)) {
                $network_settings = file($app->remotePath.$this->network_config_file);
                for ($counter=0;$counter<count($network_settings);$counter++) {
                    $parts = explode("=",$network_settings[$counter]);
                    $this->fields[trim(strtolower($parts[0]))] = $parts[1];
                }
            }        
        } else {
            if (file_exists($app->remotePath.$this->network_config_file)) {
                $strings = file($app->remotePath.$this->network_config_file);
                foreach ($strings as $line) {
                    if (preg_match('/address (.*)/',$line,$matches)==1)
                        $this->ipaddr = trim($matches[1]);
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
                }
            }
        }
        $this->old_ipaddr = $this->ipaddr;
        $this->old_dns1 = $this->dns1;
        $this->old_dns2 = $this->dns2;
        $this->old_dns3 = $this->dns3;
        $this->old_netmask = $this->netmask;
        $this->old_gateway = $this->gateway;
        $this->loadLdapSettings();
        $this->loaded = true;
    }
    
    function loadLdapSettings() {
		global $Objects;
		$app = $Objects->get($this->module_id);
		if (file_exists($app->remotePath.$app->openLdapClientConfigFile)) {
			$strings = file($app->remotePath.$app->openLdapClientConfigFile);
			foreach ($strings as $line) {
				$matches = array();
				if (preg_match("/URI (.*)\:\/\/(.*)/",$line,$matches)) {
					$this->ldap_proto = trim($matches[1]);
					$this->ldap_host = trim($matches[2]);
					if ($this->ldap_proto == "ldaps")
						$this->ldap_port = "636";
					else
						$this->ldap_port = "389";						
				}
				if (preg_match("/BASE (.*)/",$line,$matches)) {
					$this->base_dn = trim($matches[1]);
				}
				if (preg_match("/BINDDN (.*)/",$line,$matches)) {
					$this->ldap_user = str_replace("cn=","",array_shift(explode(",",trim($matches[1]))));
				}
				if (preg_match("/\#BINDPW (.*)/",$line,$matches)) {
					$this->ldap_password = trim($matches[1]);
				}
			}
			if ($this->ldap_proto=="ldaps")
				$this->ldap_use_ssl = "1";
			else
				$this->ldap_use_ssl = "0";
			$this->domain = str_replace("dc=","",str_replace(",",".",$this->base_dn));
			if ($this->ldap_host=="localhost") {
				$this->is_ldap_localhost = "1";
				$this->ldapHostDisplay = "none";
			}
			else {
				$this->is_ldap_localhost = "0";
				$this->ldapHostDisplay = "";
			}
			$this->old_ldap_user = $this->ldap_user;
			$this->old_ldap_password = $this->ldap_password;
			$this->old_ldap_host = $this->ldap_host;			
		} 	
    }

    function save($arguments=null) {        

		global $Objects,$appconfig;
		$shell = $Objects->get("Shell_shell");
		$app = $Objects->get($this->module_id);
		
    	if (isset($arguments)) {
    		$this->load();
    		$this->setArguments($arguments);
    		if ($this->ldap_port=="636")
    			$this->ldap_proto = "ldaps";
    		else
    			$this->ldap_proto = "ldap";
    		if ($this->is_ldap_localhost)
    			$this->ldap_host = "localhost";
    	}
    	
  		if ($this->old_ldap_host!=$this->ldap_host or $this->old_ldap_user!=$this->ldap_user or $this->old_ldap_password != $this->ldap_password) {
   			$args = array("{ldap_user_name}" => "cn=".$this->ldap_user.",".$this->base_dn, "{ldap_password}" => $this->ldap_password, "{ldap_base_dn}" => $this->base_dn, "{ldap_host}" => $this->ldap_host);
  			if ($this->ldap_host=="localhost") {
				file_put_contents($app->remotePath.$app->slapdConfigFile,strtr(file_get_contents($app->slapdTemplateConfigFile),$args));  
    			file_put_contents($app->remotePath.$this->app->webServerAuthConfigFile,strtr(file_get_contents($this->app->webServerAuthTemplateConfigFile),$args));
				$shell->exec_command($app->remoteSSHCommand." ".$app->slapdRestartCommand.";".$app->remoteSSHCommand." ".$this->app->apacheRestartCommand);
				$appconfig["apacheAdminsLdapHost"] = $this->ldap_host;
				$appconfig["apacheAdminsLdapUser"] = "cn=".$this->ldap_user.",".$this->base_dn;
				$appconfig["apacheAdminsLdapPassword"] = $this->ldap_password;
				$GLOBALS["appconfig"] = $appconfig;
				$str = "<?php\n".getMetadataString(getMetadataInFile($appconfig["file"]))."\n?>";
				file_put_contents($appconfig["file"],$str);				
				$ds = ldap_connect($this->ldap_proto."://".$this->ldap_host."/");
		        if (ldap_error($ds)!="Success") {
		            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
		            return 0;
		        }			        
		        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
		        $r = @ldap_bind($ds,"cn=".$this->ldap_user.",".$this->base_dn,$this->ldap_password);
		        while (ldap_error($ds)!="Success") {
		        	$r = @ldap_bind($ds,"cn=".$this->ldap_user.",".$this->base_dn,$this->ldap_password);
		        }
    		} else {
    			file_put_contents($app->remotePath.$this->app->webServerAuthConfigFile,strtr(file_get_contents($this->app->webServerAuthTemplateConfigFile),$args));
    			$shell->exec_command($app->remoteSSHCommand." ".$this->app->apacheRestartCommand);    			 
				$appconfig["apacheAdminsLdapHost"] = $this->ldap_host;
				$appconfig["apacheAdminsLdapUser"] = "cn=".$this->ldap_user.",".$this->base_dn;
				$appconfig["apacheAdminsLdapPassword"] = $this->ldap_password;
				$GLOBALS["appconfig"] = $appconfig;
				$str = "<?php\n".getMetadataString(getMetadataInFile($appconfig["file"]))."\n?>";
				file_put_contents($appconfig["file"],$str);				
    		}
    	}
    	
    	$args = array("{ldap_user_dn}" => "cn=".$this->ldap_user.",".$this->base_dn, "{ldap_proto}" => $this->ldap_proto, "{ldap_host}" => $this->ldap_host, "{ldap_base_dn}" => $this->base_dn, "{ldap_password}" => $this->ldap_password);
    	$fp = fopen($app->remotePath.$app->openLdapClientConfigFile,"w");
    	$strings = file($app->openLdapClientTemplateConfigFile);
    	foreach($strings as $line) {
    		fwrite($fp,strtr($line,$args));
    	}
    	fclose($fp);
    	 
        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host."/");
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        $r = @ldap_bind($ds,"cn=".$this->ldap_user.",".$this->base_dn,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        // Если не существует корень дерева, создаем его 
        $base_dn_parts = explode(",",$this->base_dn);
        $result = @ldap_list($ds,$base_dn_parts[1],"(".$base_dn_parts[0].")");
        $entries = @ldap_get_entries($ds,$result);
        if ($entries["count"]==0 or $entries==FALSE) {
        	$entry["dc"] = str_replace("dc=","",$base_dn_parts[0]);
        	$entry["objectClass"][0] = "dcObject";
        	$entry["objectClass"][1] = "top";
        	$entry["objectClass"][2] = "locality";
        	@ldap_add($ds,$base_dn_parts[0].",".$base_dn_parts[1],$entry);
        }

        // Если не существует корень для счетчиков идентификаторов пользователей и групп, создаем его
        $result = @ldap_list($ds,$this->base_dn,"(objectClass=sambaUnixIdPool)");        
        $entries = @ldap_get_entries($ds,$result);
        $entry = array();
        $fileServer = $Objects->get("FileServer_".$this->module_id."_Files");
        if (!$fileServer->loaded)
        	$fileServer->load();
        $dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$dhcpServer->loaded)
        	$dhcpServer->load();
        if (@$entries["count"]=="0") {
            $entry["objectClass"][0] = "organizationalUnit";
            $entry["objectClass"][1] = "sambaUnixIdPool";
            $entry["ou"] = "NextFreeUnixId";
            $entry["uidNumber"] = "10015";
            $entry["gidNumber"] = "10015";
            ldap_add($ds,"ou=NextFreeUnixId,".$this->base_dn,$entry);            
            $fileServer->save();            
            $dhcpServer->save();            
            if ($app->remoteSSHCommand!="")            
            	shell_exec($app->remoteSSHCommand." echo -e \"".$this->password."\n".$this->password."\" | smbpasswd -a root'");
        	else
    	    	$shell->exec_command("echo -e '".$this->password."\n".$this->password."' | smbpasswd -a root");
        }
        	
        
        //Если не существует корень для групп объектов, создаем его
        $result = ldap_list($ds,$this->base_dn,"(ou=objectgroups)");
        $entries = ldap_get_entries($ds,$result);
        if ($entries["count"]==0 or $entries==FALSE) {
        	$entry = array();
        	$entry["ou"] = "objectgroups";
        	$entry["objectClass"][0] = "organizationalUnit";
        	ldap_add($ds,"ou=objectgroups,".$this->base_dn,$entry);
        }
        		
        $fileServer->saveFiles();      
         
        if ($this->hostname!=$this->old_hostname) {
        	$gapp = $this->gapp;
        	
        	if ($app->remoteSSHCommand!="")
        		$local_sid = trim(shell_exec($app->remoteSSHCommand." \"".$app->smbGetlocalsidCommand."\""));
        	else
        		$local_sid = trim($shell->exec_command($gapp->smbGetlocalsidCommand));
        	$fp = fopen($gapp->remotePath.$this->app->hostnameFile,"w");
        	fwrite($fp,$this->hostname);
        	fclose($fp);

        	$fp = fopen($gapp->remotePath.$this->app->hostsFile,"w");
        	fwrite($fp,"127.0.0.1 localhost ".$this->hostname."\n");
        	fclose($fp);
        	 
        	$shell->exec_command($app->remoteSSHCommand." ".$this->app->hostnameCommand." ".$this->hostname);
        	
        	// сохраняем базу данных в текстовый файл
        	if ($gapp->remoteSSHCommand!="")
        		$shell->exec_command($gapp->remoteSSHCommand." \"/root/ldapsearch.sh 'cn=".$this->ldap_user.",".$this->base_dn."' '".$this->ldap_password."'\"");
        	else {
        		$shell->exec_command("/root/ldapsearch.sh 'cn=".$this->ldap_user.",".$this->base_dn."' '".$this->ldap_password."'");
        	}        	 
        	
        	// удаляем базу данных
        	$shell->exec_command($gapp->remoteSSHCommand." ".$app->ldapDeleteCommand." -D 'cn=".$this->ldap_user.",".$this->base_dn."' -w '".$this->ldap_password."' -r -x ".$this->base_dn);

        	// заменяем в сохраненном текстовом файле имя хоста на новое
        	$args = array("=".$this->old_hostname.",".$this->base_dn => "=".$this->hostname.",".$this->base_dn, strtoupper($this->old_hostname) => strtoupper($this->hostname));
        	file_put_contents($gapp->remotePath."/root/base.ldif",strtr(file_get_contents($gapp->remotePath."/root/base.ldif"),$args));
        	$shell->exec_command($gapp->remoteSSHCommand." ".$app->ldapAddCommand." -c -D 'cn=".$this->ldap_user.",".$this->base_dn."' -w '".$this->ldap_password."' -x -f /root/base.ldif");     
            $shell->exec_command($app->remoteSSHCommand." ".$app->smbRestartCommand);
        	$entries = array();
        	while (!isset($entries[0])) {
        		$result = ldap_search($ds,$this->base_dn,"(sambaDomainName=".strtoupper($this->hostname).")");
        		$entries = ldap_get_entries($ds, $result);
        	}
        	$entry = array();
        	$entry["sambasid"] = $local_sid;
        	@ldap_modify($ds,"sambaDomainName=".strtoupper($this->hostname).",".$this->ldap_base,$entry);
        	@ldap_delete($ds,"sambaDomainName=".strtoupper($this->old_hostname).",".$this->ldap_base);        	 
        }
                
        if (file_exists($app->remotePath.$this->app->shadowFile)) {
            $strings = file($app->remotePath.$this->app->shadowFile);
            $fp = fopen($app->remotePath.$this->app->shadowFile,"w");
            for($c=0;$c<count($strings);$c++) {
                $line = explode(":",$strings[$c]);
                if ($line[0]=="root")
                    $line[1]=crypt($this->password);
                $line = implode(":",$line);
                fwrite($fp,$line);
            }
            fclose($fp);
        }
        $fp = fopen($app->remotePath.$this->app->rootPasswordFile,"w");
        fwrite($fp,"#!/bin/sh\necho ".$this->password);
        fclose($fp);        
        $this->changeNetworkSettings();
		$this->app->raiseRemoteEvent("CONTROLLER_SETTINGS_CHANGED");
        $this->loaded = true;
    }
    
    function changeNetworkSettings() {
    	global $Objects;
    	if (!$this->loaded)
    		$this->load();
    	$app = $Objects->get($this->module_id);
    	$shell = $Objects->get("Shell_shell");
    	$fp = fopen($app->remotePath.$this->network_config_file,"w");
    	if ($this->network_style == "RedHat") {
    		fwrite($fp,"IPADDR=".$this->ipaddr."\n");
    		fwrite($fp,"NETMASK=".$this->netmask."\n");
    		fwrite($fp,"GATEWAY=".$this->gateway."\n");
    		if ($this->dns1!="")
    			fwrite($fp,"DNS1=".$this->dns1."\n");
    		if ($this->dns2!="")
    			fwrite($fp,"DNS2=".$this->dns2."\n");
    		if ($this->dns1!="")
    			fwrite($fp,"DNS3=".$this->dns3."\n");
    		fclose($fp);
    	} else {
    		if (file_exists($this->network_config_template_file)) {
    			$this->dns = "dns-nameservers ".str_replace("   "," ",str_replace("  "," ",$this->dns1." ".$this->dns2." ".$this->dns3));
    			$this->dns = $this->dns."\n"."dns-search ".$this->domain;
    			$strings = file($this->network_config_template_file);
    			$fp = fopen($app->remotePath.$this->network_config_file,"w");
    			$args = $this->getArgs();
    			foreach ($strings as $string) {
    				fwrite($fp,strtr($string,$args));
    			}
    		}
    		fclose($fp);
    		if ($app->remoteSSHCommand!="")
    			shell_exec($app->remoteSSHCommand." 'echo ".$this->restart_command." | at now'");
    		else
    			shell_exec('echo "'.$this->restart_command.'" | at now');
    	}
    	if ($this->ipaddr!=$this->old_ipaddr) {
    		$gapp = $Objects->get("Application");
    		if (!$gapp->initiated)
    			$gapp->initModules();
    		$gapp->changeModuleIp($app->moduleName,$this->ipaddr);
    	}
    	
    	if ($this->ipaddr==$this->old_ipaddr) {
    		$this->shell->exec_command(str_replace("{user}","root",str_replace("password",$this->password,$app->remoteSSHCommand." ".$app->smbChangeUserPasswordCommand)));
    		$cmd =  $app->smbReloadCommand.";";
    		$cmd .= $app->nfsRestartCommand.";";
    		$cmd .= $app->afpRestartCommand;
    		$shell->exec_command("echo ".$cmd." | at now");
    	}    	 
    }

    function getId() {
        return "SystemSettings_".$this->module_id."_".$this->name;
    }

    function getPresentation() {
        return "Основные параметры";
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{dns}"] = str_replace("|"," ",$result["{dns}"]);
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