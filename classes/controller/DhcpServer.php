<?php
/**
 * Description of DhcpServer
 *
 * @author andrey
 */
class DhcpServer extends WABEntity {

    function construct($params) {
        
        $this->module_id = $params[0]."_".$params[1];
        $this->name = @$params[2];
        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->template = "templates/controller/DhcpServer.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/DhcpServer.js";
        $this->icon = $app->skinPath."images/Tree/networks.gif";
        $this->width = "500";
        $this->height = "320";
        $this->overrided = "width,height";
        $this->dns_server = "localhost";
        $this->skinPath = $app->skinPath;
        
        $app = $Objects->get($this->module_id);
        $this->fields["hostnameCommand"] = $app->remoteSSHCommand." ".$app->hostnameCommand;
        $this->hostName = trim(file_get_contents($app->remotePath.$app->hostnameFile));
        $this->ldap_host = $app->defaultLdapHost;
        $this->ldap_port = $app->defaultLdapPort;
        if ($this->ldap_port=="636") {
        	$this->ldap_proto = "ldaps";
			$this->ldap_use_ssl = "1";
        }
        else {
        	$this->ldap_proto = "ldap";
			$this->ldap_use_ssl = "0";
        }
        $this->ldap_user = $app->defaultLdapUser;
        $this->ldap_password = $app->defaultLdapPassword;
        $this->ldap_base = $app->defaultLdapBase;
        $this->online_monitor = "no";
		$this->online_monitor_update_period = 5000;
        $this->manualDNSEntries = "";
        $this->models[] = "DhcpServer";
        $this->loaded = false;
        
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."DhcpServer";
        $this->tabs_string = "main|Основные|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "dns|DNS|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "main";
        
        $this->clientClass = "DhcpServer";
        $this->parentClientClasses = "Entity";        
    }

    function load() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        
        if (!$app->loaded)
            $app->load();
        $ap = $Objects->get("Application");
        if (!$ap->initiated)
            $ap->initModules();
        
        foreach ($app->module as $key=>$value)
        	$this->fields[$key] = $value;
		$sys = $Objects->get("SystemSettings_".$this->module_id."_Settings");
		$sys->loadLdapSettings();
		$this->ldap_port = $sys->ldap_user;
		$this->ldap_port = $sys->ldap_port;		
		$this->ldap_proto = $sys->ldap_proto;
		$this->ldap_password = $sys->ldap_password;
		$this->ldap_base = $sys->base_dn;
		$this->ldap_user_name = $sys->ldap_user;
		$this->ldap_user = "cn=".$sys->ldap_user.",".$sys->base_dn;
		$this->ldap_use_ssl = $sys->ldap_use_ssl;
        $this->getIpCommand = $ap->getIpCommand;
        $this->deleteCommand = $ap->deleteCommand;
        if (!file_exists($app->remotePath.$this->dhcpConfigFile))
            return 0;
        $strings = file($app->remotePath.$this->dhcpConfigFile);
        foreach ($strings as $line) {
            if (preg_match('/#dns-server "(.*)";/',$line,$matches)==1)
                $this->dns_server = trim($matches[1]);
            if (preg_match('/#online-monitor "(.*)";/',$line,$matches)==1)
                $this->online_monitor = trim($matches[1]);
            if (preg_match('/#online-monitor-update-period "(.*)";/',$line,$matches)==1)
                $this->online_monitor_update_period = trim($matches[1]);
        }
        $this->old_ldap_host = $this->ldap_host;
        $this->old_ldap_base = $this->ldap_base;
        $this->old_ldap_user = $this->ldap_user;
        $this->old_ldap_password = $this->ldap_password;
        $this->initial_ldap_host = $this->ldap_host;
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
            $gapp->initModules();
        $shell = $Objects->get("Shell_shell");
        if ($this->ldap_host=="localhost") {
	        if ($app->remoteAddress!="")
    	        $this->ldap_host = $app->remoteAddress;
	        $this->displayDomain = "";
        } else {
        	$this->displayDomain = "none";
        }
        
        $this->domain = str_replace("dc=","",str_replace(",",".",$this->ldap_base));
        
        $app = $ap;        
        $capp = $Objects->get($this->module_id);
        $this->mailIntegration = $capp->mailIntegration;
        $this->new_maildomain = "true";
        // Запись информации о почтовом ящике
        if ($capp->mailIntegration) {
            foreach ($app->modules as $key=>$module) {
                if ($key == $capp->mailIntegration) {
                    $mailapp = $Objects->get($module["class"]);
                    $this->mailModuleId = $module["class"];
                }
            }
            if (isset($mailapp) and !$mailapp->down) {
                $maildoms = $Objects->get("MailDomains_".$mailapp->id);
                
                if ($maildoms->contains($this->domain));                
                    $this->new_maildomain = "false";
            }
        }        
        if (file_exists($capp->remotePath.$this->bindCustomZoneRecordsFile)) {
            $this->manualDNSEntries = file_get_contents($capp->remotePath.$this->bindCustomZoneRecordsFile);
            $this->oldManualDNSEntries = $this->manualDNSEntries;
        }
        $this->loaded = true;
    }

    function save($arguments=null) {
        global $Objects;
        if (isset($arguments)) {
			$this->load();
			$this->setArguments($arguments);
		}
		
        $shell = $Objects->get("Shell_Helix");
        
        $app = $Objects->get($this->module_id);
		if ($this->online_monitor_update_period<5000)
			$this->online_monitor_update_period=5000;
		
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
            $gapp->initModules();
        
        $this->old_domain = str_replace("dc=","",str_replace(",",".",$this->old_ldap_base));
        $this->domain = str_replace("dc=","",str_replace(",",".",$this->ldap_base));

        if (!file_exists($app->remotePath.$this->bindCustomZoneRecordsFile)) {
            $fp = fopen($app->remotePath.$this->bindCustomZoneRecordsFile,"w");
            fwrite($fp,"");
            fclose($fp);
        }
        file_put_contents($app->remotePath.$this->bindCustomZoneRecordsFile,$this->manualDNSEntries);
        
        // Если изменилось имя корневого домена или имя пользователя и пароль для подключения к серверу LDAP
        if ($this->old_ldap_base != $this->ldap_base && $this->initial_ldap_host=="localhost")  {
        	$this->ldap_user = "cn=".$this->ldap_user_name.",".$this->ldap_base;             
            // Запись информации о почтовом домене
            $ap = $Objects->get("Application");
            if ($ap->initiated)
                $ap->initModules();
            $capp = $app;
            if ($capp->mailIntegration and $this->ldap_base != $this->old_ldap_base) {
                foreach ($ap->modules as $key=>$module) {
                    if ($key == $capp->mailIntegration) {
                        $mailapp = $Objects->get($module["class"]);
                    }
                }
                if (isset($mailapp) and file_exists($mailapp->remotePath."etc/hostname")) {
                    $mbox = $Objects->get("MailDomain_".$mailapp->id."_".$this->old_domain);
                    $mbox->load();
                    $res = $mbox->usedBy();
                    if (is_object($res)) {
                        $this->reportError("Данные почтового домена ".$this->old_domain." в данный момент редактируются в другом окне пользователем ".$res->name,"save");
                        return 0;
                    }
                    $mbox->name = $this->domain;
                    $mbox->save();
                }
            }
            
            // сохраняем базу данных в текстовый файл
            if ($app->remoteSSHCommand!="")
            	$shell->exec_command($app->remoteSSHCommand." \"/root/ldapsearch.sh '".$this->old_ldap_user."' '".$this->old_ldap_password."'\"");
            else {
            	$shell->exec_command("/root/ldapsearch.sh '".$this->old_ldap_user."' '".$this->old_ldap_password."'");
            }
            	
            // удаляем базу данных
            $shell->exec_command($app->remoteSSHCommand." ".$this->ldapDeleteCommand." -D '".$this->old_ldap_user."' -w ".$this->old_ldap_password." -r -x ".$this->old_ldap_base);
            // Заменяем конфигурационный файл сервера LDAP (slapd.conf);
            $strings = file_get_contents($this->slapdTemplateConfigFile);
            $strings = strtr($strings,$this->getArgs());
            file_put_contents($app->remotePath.$this->slapdConfigFile,$strings);
            // Перезагружаем сервер LDAP
            $shell->exec_command($app->remoteSSHCommand." ".$this->slapdRestartCommand);
            // Заменяем конфигурационные файлы файлового сервера
            // Заменяем в выгруженном файле базы данных старое имя домена на новое
            $arr = explode(",",$this->ldap_base);$arr = $arr[0];$arr = str_replace("=",": ",$arr);$this->dc = $arr;
            $arr = explode(",",$this->old_ldap_base);$arr = $arr[0];$arr = str_replace("=",": ",$arr);$this->old_dc = $arr;
            $this->old_domain_name = str_replace(",",".",str_replace("dc=","",$this->old_ldap_base));
            $this->domain_name = str_replace(",",".",str_replace("dc=","",$this->ldap_base));
            $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Files");
            $this->fileServer->load();
            $this->fileServer->ldap_user = $this->ldap_user;
            $this->fileServer->ldap_base = $this->ldap_base;
            $this->fileServer->base_dn = $this->ldap_base;
            if ($this->fileServer->domain_controller=="include = /etc/samba/domain.conf") {
                $this->fileServer->workgroup = strtoupper($this->domain_name);
            }
            $this->fileServer->saveFiles();            
            $strings = file_get_contents($app->remotePath."/root/base.ldif");
            $strings = str_replace($this->old_ldap_base,$this->ldap_base,$strings);
            $strings = str_replace(strtoupper($this->old_ldap_base),strtoupper($this->ldap_base),$strings);
            $strings = str_replace($this->old_domain_name,$this->domain_name,$strings);
            $strings = str_replace(strtoupper($this->old_domain_name),strtoupper($this->domain_name),$strings);
            $strings = str_replace($this->old_dc,$this->dc,$strings);
            $strings = str_replace(strtoupper($this->old_dc),strtoupper($this->dc),$strings);
            file_put_contents($app->remotePath."/root/base.ldif",$strings);
            // загружаем файл в базу данных
            $shell->exec_command($app->remoteSSHCommand." ".$this->ldapAddCommand." -c -D '".$this->ldap_user."' -w ".$this->ldap_password." -x -f /root/base.ldif");
            // Сохраняем файл настроек DHCP
            $args = $this->getArgs();
            $args["{serial}"] = date('Ymds');
            $app = $Objects->get($this->module_id);
            if (!$app->loaded)
                $app->load();
            $fp = fopen(str_replace("//","/",$app->remotePath.$this->dhcpConfigFile),"w");
            $strings = file($this->dhcpConfigTemplateFile);
            foreach($strings as $line) {
                $line = strtr($line,$args);
                fwrite($fp,$line);
            }
            fclose($fp);     
            if ($this->old_ldap_base != $this->ldap_base)
                @$this->UpdateDNSZones();
            $sys = $Objects->get("SystemSettings_".$this->module_id."_Settings");
            $sys->changeNetworkSettings();
            $ap->raiseRemoteEvent("DHCPSERVER_CHANGED");
        }
                
        $ds = @ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Не могу подключиться к серверу LDAP. Проверьте имя сервера и порт! - ".ldap_error($ds), "save");
            return 0;
        }
        
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = @ldap_bind($ds,$this->ldap_user,$this->ldap_password);        
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка доступа к серверу LDAP. Проверьте имя пользователя и пароль! - ".ldap_error($ds)."-".$this->ldap_user,"save");
            return 0;
        }
        
        $hostname = trim(exec($this->hostnameCommand));
        $old_hostname = $hostname;
        $this->hostname = $hostname;
        
        $result = @ldap_search($ds,$this->ldap_base,"(objectClass=dhcpServer)");
        if ($result!=FALSE)
			$entries = ldap_get_entries($ds, $result);
		else
			$entries = array();
        $entry = array();
        $dn = "cn=".$hostname.",".$this->ldap_base;

        if ($result==FALSE or @$entries["count"]==0) {
            $entry["objectClass"] = "dhcpServer";
            $entry["cn"] = $hostname;
            $entry["dhcpServiceDN"] = "cn=Network,cn=".$hostname.",".$this->ldap_base;
            ldap_add($ds,$dn,$entry);
            if (ldap_error($ds)!="Success") {
                $this->reportError("Ошибка записи в каталог LDAP. Проверьте корень дерева LDAP!","save");
                return 0;
            }
        } else
            @ldap_modify($ds,$dn,$entry);

        $result = ldap_search($ds,$this->ldap_base,"(objectClass=dhcpService)");
        $entries = ldap_get_entries($ds, $result);
        $this->domain_name = str_replace(",",".",str_replace("dc=","",$this->ldap_base));
        $entry = array();
        $entry["cn"] = "Network";
        $entry["dhcpPrimaryDN"] = "cn=".$hostname.",".$this->ldap_base;
        $entry["objectClass"][0] = "dhcpService";
        $entry["objectClass"][1] = "dhcpOptions";
        $entry["dhcpStatements"][0] = "default-lease-time 21600";
        $entry["dhcpStatements"][1] = "max-lease-time 43200";
        $entry["dhcpStatements"][2] = "use-host-decl-names on";
        $entry["dhcpStatements"][3] = "ddns-update-style interim";
        $entry["dhcpStatements"][4] = "ddns-updates on";
        $entry["dhcpStatements"][5] = 'ddns-domainname "'.$this->domain_name.'"';
        $entry["dhcpStatements"][6] = 'ddns-rev-domainname "in-addr.arpa"';
        $entry["dhcpStatements"][7] = "update-static-leases on";
        $entry["dhcpStatements"][8] = "key DHCP_UPDATE { algorithm HMAC-MD5.SIG-ALG.REG.INT; secret STUDJX6cV23g4Va5ZZfxnQ==";
        $entry["dhcpStatements"][9]= "} zone ".$this->domain_name.". { primary ".$this->dns_server."; key DHCP_UPDATE";
        $counter = 10;
        $this->loaded = true;
        $subns = array();
        $resu = @ldap_list($ds,"cn=Network,cn=".$hostname.",".$this->ldap_base,"(objectClass=dhcpSubnet)");
        $entris = @ldap_get_entries($ds,$resu);
        if (is_array($entris))
        foreach($entris as $entri) {
            $name = $entri["cn"][0];
            if ($name=="")
                continue;
            $sub_arr = explode(".",$name);
            array_pop($sub_arr);
            $sub = implode(".",array_reverse($sub_arr));
            $entry["dhcpStatements"][$counter] = "} zone ".$sub.".in-addr.arpa. { primary ".$this->dns_server."; key DHCP_UPDATE";
            $counter++;
            $subns[count($subns)] = $sub;
        }
        $entry["dhcpStatements"][$counter]= "}";
        $entry["dhcpOption"] = 'domain-name "'.$this->domain_name.'"';

        if ($entries["count"]==0)
            @ldap_add($ds,"cn=Network,cn=".$hostname.",".$this->ldap_base,$entry);
        else
           @ldap_modify($ds,"cn=Network,cn=".$hostname.",".$this->ldap_base,$entry);

        if (ldap_error($ds)!="Success") {
        	$this->reportError("Ошибка записи в каталог LDAP. Проверьте корень дерева LDAP!","save");
        	return 0;
        }
        
        if ($hostname != $old_hostname) {
            $subs = $Objects->get("DhcpSubnet_".$this->module_id."_subnets");
        }

        $args = $this->getArgs();
        $args["{serial}"] = date('Ymds');
        $app = $Objects->get($this->module_id);
        if (!$app->loaded)
            $app->load();
        $fp = fopen(str_replace("//","/",$app->remotePath.$this->dhcpConfigFile),"w");
        $strings = file($this->dhcpConfigTemplateFile);
        foreach($strings as $line) {
            $line = strtr($line,$args);
            fwrite($fp,$line);
        }
        fclose($fp);

        $args["{zone_name}"] = $args["{ldap_base}"];
        $args["{zone_name1}"] = $args["{ldap_base}"];
        if (file_exists($app->remotePath.$this->bindZonesFile) and file_exists($this->bindZonesTemplateFile)) {
            $fp = fopen($app->remotePath.$this->bindZonesFile,"w");
            $strings = file($this->bindZonesTemplateFile);
            $args["{file}"] = "db.domain";
            foreach($strings as $line) {
                $line = strtr($line,$args);
                fwrite($fp,$line);
            }

            foreach ($subns as $sub) {
                fwrite($fp,"\n");
                $args["{file}"] = $sub.".in-addr.arpa";
                $args["{zone_name}"] = $args["{file}"];
                foreach($strings as $line) {
                    $line = strtr($line,$args);
                    fwrite($fp,$line);
                }
            }
            fclose($fp);

            $args["{record_type}"] = "A";
            $bind_path = explode('/',$app->remotePath.$this->bindZonesFile);
            array_pop($bind_path);
        }
        
        $this->updateDNSZones();
		$module = $gapp->getModuleByClass($this->module_id);
        if ($this->netCenterAutoRestart)
            $value = "1";
        else
            $value = "0";
        
		$module["netCenterAutoRestart"] = $value;
		$GLOBALS["modules"][$module["name"]] = $module;
		$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
		file_put_contents($module["file"],$str);                        
		            
        if ($app->remoteSSHCommand=="") {
            if ($this->netCenterAutoRestart) {
            	$cmd = $this->dhcpRestartCommand.";".$this->bindRestartCommand;
            	$shell->exec_command("echo '".$cmd."' | at now");
            }
        } else {
            if ($this->netCenterAutoRestart) {
                $cmd = " \"".$app->dhcpRestartCommand.";".$app->bindRestartCommand."\"";
                shell_exec($app->remoteSSHCommand." 'echo ".$cmd." | at now'");            
            }
        }
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("DHCPSERVER_CHANGED");                
        $this->loaded = true;
    }

    function getId() {
        return "DhcpServer_".$this->module_id."_".$this->name;
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{hostname}"] = $this->hostName;
        $result["{ldap_base_dn}"] = $this->ldap_base;
        $result["{ldap_user_name}"] = $this->ldap_user;
        $result["{ldap_base}"] = str_replace(",",".",str_replace("dc=","",$this->ldap_base));
        $result["{ldap_user}"] = str_replace($this->ldap_base,"",$this->ldap_user);
        $result["{ldap_user}"] = str_replace("cn=","",str_replace(",","",$result["{ldap_user}"]));
        $result["{ldap_host}"] = $this->initial_ldap_host;         
        return $result;
    }

    function getServerDn() {
        if (!$this->loaded)
            $this->load();
        return "cn=".$this->hostName.",".$this->ldap_base;
    }

    function getServiceDn() {
        return "cn=Network,".$this->getServerDn();
    }

    function getPresentation() {
        return "Настройка модуля управления сетью";
    }
    
    function updateDNSZone($zoneFile,$zone_name,$zone_name1,$record_type,$ip_address,$addrTable) {
        global $Objects;
        $app = $Objects->get($this->module_id);
        if (!$this->loaded)
            $this->load();
        $tpl = file_get_contents($this->bindZoneTemplateFile);
        $tpl = strtr($tpl,array("{zone_name}"=>$zone_name,"{zone_name1}"=>$zone_name1,"{record_type}"=>$record_type,"{ip_address}"=>$ip_address,"{serial}"=>date('Ymds')));
        $tpl .="\n".implode("\n",$addrTable);
        file_put_contents($zoneFile,$tpl."\n");                        
    }
    
    function UpdateDNSZones() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        $this->domain_name = str_replace(",",".",str_replace("dc=","",$this->ldap_base));
        $ip = $shell->exec_command($app->remoteSSHCommand." ".$this->getIpCommand);
        $collection = $Objects->get("DhcpSubnets_".$this->module_id."_subnets");
        if (!$collection->loaded)
            $collection->load();
        $forwardZoneFile = $app->remotePath.$this->bindZoneFile;
        $forwardArr = array();
        foreach ($collection->subnets as $subnet) {
            $subnet->loadHosts(true);
            $reverseArr = array();
            foreach ($subnet->hosts as $host) {
                if (stripos($host->name," ")!==FALSE)
                    continue;
                if (stripos($host->name,"_")!==FALSE)
                    continue;
                $forwardArr[$host->name] = $host->name.".".$this->domain_name.". IN A ".$host->fixed_address;
                $addr = array_pop(explode(".",$host->fixed_address));
                $reverseArr[$addr] = $addr." IN PTR ".$host->name.".".$this->domain_name.".";
            }
            $arr = explode("/",$forwardZoneFile);
            array_pop($arr);
            $arr1 = explode(".",$subnet->name);
            array_pop($arr1);
            $arr1 = array_reverse($arr1);
            $fileName = implode(".",$arr1).".in-addr.arpa";
            $arr[] = $fileName;
            $reverseZoneFile = implode("/",$arr);
            $this->updateDNSZone($reverseZoneFile,$fileName,$this->domain_name,"PTR",$ip,$reverseArr);  
            @unlink($reverseZoneFile.".jnl");
        }
        $this->updateDNSZone($forwardZoneFile,$this->domain_name,$this->domain_name,"A",$ip,$forwardArr);
        file_put_contents($app->remotePath.$this->bindZoneFile,file_get_contents($app->remotePath.$this->bindZoneFile).file_get_contents($app->remotePath.$this->bindCustomZoneRecordsFile)."\n");
        @unlink($forwardZoneFile.".jnl");
    }
    
    function restart() {
        global $Objects;
        if (!$this->loaded)
            $this->load();
        $app = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");        
        $result = $shell->exec_command($app->remoteSSHCommand." ".$this->dhcpRestartCommand);
        $result .= $shell->exec_command($app->remoteSSHCommand." ".$this->bindRestartCommand);
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("DHCPSERVER_RESTARTED");
        echo $result;
    }
    
    function getHookProc($number) {
		switch($number) {
			case '3': return "save";
			case '4': return "restart";
		}
		return parent::getHookProc($number);
	}
}
?>