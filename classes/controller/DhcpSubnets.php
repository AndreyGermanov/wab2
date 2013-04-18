<?php
/**
 * Класс представляет из себя коллекцию подсетей DHCP-сервера, находящихся в
 * LDAP каталоге под $this->dhcpServer->getServiceDN();
 *
 * Он работает с динамической коллекцией subnets, которая представляет из себя
 * массив загруженных в память (в массив Objects) объектов DhcpSubnet.
 *
 * Класс позволяет добавлять новые объекты DhcpSubnet, удалять существующие,
 * проверять, существует ли указанная подсеть
 *
 *
 * Для этих целей предназначены следующие методы
 *
 * add - добавляет новую подсеть
 * remove - удаляет указанную по имени подсеть из каталога
 * load - загружает все подсети из каталога в массив
 * save - сохраняет все подсети в каталог
 * contains - проверяет, существует ли указанная подсеть
 *
 * Также существует дополнительный метод getId(), который возвращает идентификатор
 * данного объекта
 * 
 * @author andrey
 */
class DhcpSubnets extends WABEntity {

    public $dhcpServer;

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = @$params[2];

        global $Objects;

        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        $this->service_dn = $this->dhcpServer->getServiceDN();
        
        $this->clientClass = "DhcpSubnets";
        $this->parentClientClasses = "Entity";
        
        $this->loaded = false;
    }

    function load() {
        global $Objects;

        $service_dn = $this->service_dn;
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();
        $ds = ldap_connect($this->dhcpServer->ldap_proto."://".$this->dhcpServer->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к серверу каталога !","load");
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($ds,$this->dhcpServer->ldap_user,$this->dhcpServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога !","load");
        }
        $result = @ldap_list($ds,$service_dn,"(objectClass=DhcpSubnet)");
        $entries = @ldap_get_entries($ds, $result);
        $value = @ldap_first_entry($ds,$result);
        do {
            if ($value==false)
                break;
            $vals = @ldap_get_values($ds,$value,"cn");
            $subnet = $Objects->get("DhcpSubnet_".$this->module_id."_".$vals[0]);
            if (!$subnet->loaded) {
                $vals = ldap_get_values($ds,$value,"comment");
                $subnet->title = $vals[0];
                $vals = @ldap_get_values($ds,$value,"dhcpRange");
                $subnet->range = @$vals[0];
                $vals = @ldap_get_values($ds,$value,"denyFileAccess");
                $subnet->denyFileAccess = $vals[0];
                $custom_options = array();
                $values = ldap_get_values($ds,$value,"dhcpOption");
                foreach($values as $val) {
                    $value_parts = explode(" ",$val);
                    $val = str_replace($value_parts[0]." ","",$val);
                    switch ($value_parts[0]) {
                        case "domain-name":
                            $subnet->domain_name = str_replace('"','',$val);
                            break;
                        case "domain-name-servers":
                            $subnet->domain_name_servers = $val;
                            break;
                        case "subnet-mask":
                            $subnet->subnet_mask = $val;
                            break;
                        case "routers":
                            $subnet->routers = $val;
                            break;
                        case "root-path":
                            $subnet->root_path = $val;
                            break;
                        default:
                            $custom_options[count($custom_options)] = "option ".$value;
                    }
                }
                $values = ldap_get_values($ds,$value,"dhcpStatements");
                foreach($values as $val) {
                    $value_parts = explode(" ",$val);
                    $val = str_replace($value_parts[0]." ","",$val);
                    switch ($value_parts[0]) {
                        case "range":
                            $subnet->range = $val;
                            break;
                        case "filename":
                            $subnet->filename = $val;
                            break;
                        case "next-server":
                            $subnet->next_server = $val;
                            break;
                        case "allow_unknown_clients":
                            $subnet->allow_unknown_clients = $val;
                            break;
                        case "deny-unknown-clients":
                            $subnet->allow_unknown_clients = $val;
                            break;
                        default:
                            $custom_options[count($custom_options)] = $val;
                    }
                }
                $subnet->custom_options = implode("\n",$custom_options);
                $subnet->old_name = $subnet->name;
                $subnet->loaded = true;
            }
        } while ($value = ldap_next_entry($ds,$value));
        ldap_unbind($ds);
        $this->loaded = true;
    }

    function __get($name) {
        global $Objects;
        switch($name) {
            case "subnets":
                return $Objects->query("DhcpSubnet");
            default:
                if (isset($this->fields[$name]))
                        return $this->fields[$name];
                else
                    return "";
        }
        
    }

    function save() {
        foreach($this->subnets as $subnet)
            $subnet->save();
        $this->loaded = true;
    }
    
    function contains($name) {
        if (!$this->loaded)
            $this->load();
        global $Objects;
        return $Objects->contains("DhcpSubnet_".$this->module_id."_".$name);
    }

    function add($name,$title) {
        if ($this->contains($name)) {
            $this->reportError("Сеть ".$name." уже существует!","add");
            return 0;
        }
        global $Objects;
        $obj = $Objects->get("DhcpSubnet_".$this->module_id."_".$name);
        $obj->title = $title;
        return $obj;
    }

    function remove($name) {
		if (is_array($name)) {
			$name = $name["subnet"];
		}
        if (!$this->contains($name)) {
            $this->reportError("Сеть ".$name." не существует!","remove");
            return 0;
        }
        $ds = ldap_connect($this->dhcpServer->ldap_proto."://".$this->dhcpServer->ldap_host);
         if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к серверу каталога","remove");
            return 0;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_bind($ds,$this->dhcpServer->ldap_user,$this->dhcpServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога","remove");
            return 0;
        }

        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get($this->module_id);
        $fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$fileServer->loaded)
            $fileServer->load();
        $service_dn = $this->dhcpServer->getServiceDN();
        $result = ldap_list($ds, "cn=".$name.",".$service_dn, "(ObjectClass=dhcpHost)");
        $entries = ldap_get_entries($ds,$result);
        $capp = $Objects->get($this->module_id);
        if (is_object($capp->docFlowApp)) {
        	$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
        	if (!$adapter->connected)
        		$adapter->connect();
        	if ($adapter->connected) {
        		$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @objectId='DhcpSubnet_".$this->module_id."_".$name."' AND @classname='ReferenceDhcpSubnetInfoCard'",$adapter,$capp->docFlowApp->getId());
        		foreach ($entities as $entity) {
        			$entity->loaded = false;
        			$entity->load();
        			$entity->deleted = 1;
        			$entity->save(true);
        		}
        		$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @objectId LIKE 'DhcpHost_".$this->module_id."_".$name."_%' AND @classname='ReferenceDhcpHostInfoCard'",$adapter,$capp->docFlowApp->getId());
        		foreach ($entities as $entity) {
        			$entity->loaded = false;
        			$entity->load();
        			$entity->deleted = 1;
        			$entity->save(true);
        		}
        	}
        }        
        foreach($entries as $entry) {
            if ($entry["dn"]!="") {
                $host = array_shift(explode(",",$entry["dn"]));
                @unlink($app->remotePath.$this->dhcpServer->deleteCommand." ".$fileServer->smbHostsPath."/".$host.".conf");
                @unlink($app->remotePath.$this->dhcpServer->deleteCommand." ".$fileServer->smbCustomHostsPath."/".$host.".conf");
                $Objects->remove("DhcpHost_".$this->module_id."_".$name."_".$host);
                ldap_delete($ds,$entry["dn"]);                
            }
        }
        $sub_arr = explode(".",$name);
        array_pop($sub_arr);
        $bind_path = explode('/',$this->dhcpServer->bindZonesFile);
        array_pop($bind_path);
        $bind_path = implode("/",$bind_path);

        $sub = implode(".",array_reverse($sub_arr));
        $sub_file = $bind_path."/".$sub.".in-addr.arpa";

        @unlink($app->remotePath.$this->dhcpServer->deleteCommand." ".$sub_file);
        ldap_delete($ds,"cn=".$name.",".$service_dn);        
        if ($app->gatewayIp!="" and file_exists("/var/WAB2/mounts/Bastion/etc/hostname")) {
            $shell->exec_command($app->gatewaySSHCommand." rm ".$app->gatewayIntegrationPath."/a_net_".str_replace(".","_",$name));
            $shell->exec_command($app->gatewaySSHCommand." rm ".$app->gatewayIntegrationPath."/z_net_".str_replace(".","_",$name));
            $shell->exec_command($app->gatewaySSHCommand." /etc/init.d/networking restart");
        }
        
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("DHCPSUBNET_DELETED","object_id="."DhcpSubnet_".$this->module_id."_".$name);
        
        $Objects->remove("DhcpSubnet_".$this->module_id."_".$name);        
        $this->load();
        $this->dhcpServer->updateDNSZones();
        $this->dhcpServer->save();
        if ($app->remoteSSHCommand=="") {
            if ($fileServer->smbAutoRestart) {
                $shell->exec_command($app->remoteSSHCommand." ".$fileServer->smbReloadCommand);
                $shell->exec_command($app->remoteSSHCommand." ".$fileServer->nfsRestartCommand);
            }
            if ($this->dhcpServer->netCenterAutoRestart)
                $shell->exec_command($app->remoteSSHCommand." ".$this->dhcpServer->bindRestartCommand);
        } else {
                $cmd = " \"";                
                if ($fileServer->smbAutoRestart) {
                    $cmd .= $fileServer->smbReloadCommand.";";
                    $cmd .= $fileServer->nfsRestartCommand.";";
                }
                if ($this->dhcpServer->netCenterAutoRestart)
                    $cmd .= $this->dhcpServer->bindRestartCommand;
                $cmd .= "\"";
                shell_exec($app->remoteSSHCommand." 'echo ".$cmd." | at now'");            
        }
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "remove";
		}
		return parent::getHookProc($number);
	}
}
?>