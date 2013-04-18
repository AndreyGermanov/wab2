<?php
/**
 * Класс содержит информацию о подсети для DHCP-сервера.
 * Информация о подсети находится в каталоге LDAP, под учетной записью DHCP-сервера,
 * полный id которой имеет форму "DhcpServer_".$this->module_id."_".$hostname;
 *
 * В качестве имени подсети выступает ее IP-адрес. Подсеть входит в коллекцию
 * подсетей, которая расположена в LDAP-дереве, корнем которого является DN,
 * который можно получить методом getServiceDN() с DHCP-сервера.
 *
 * В качестве удобочитаемого имени сети выступает параметр title, который берется из
 * LDAP-аттрибута comment.
 *
 * Каждая подсеть может передавать хостам, которые в нее входят различные настройки
 * сети. Они указываются в атрибуте dhcpOption и имеют следующие значения:
 *
 * domain_name - имя домена (option domain-name)
 * domain_name_servers - серверы доменных имен (option domain-name-servers)
 * netbios_name_servers - серверы WINS (option netbios-name-servers)
 * netbios_node_type - тип узла WINS (option netbios-node-type: 1 - B-Node (только
 *                     широковещание, 2 - P-Node - только WINS, 4 - M-Node (сначала
 *                     широковещание, потом WINS, 8 - H-Node - сначала WINS, потом
 *                     широковещание)
 * ntp_servers - серверы времени (option ntp-servers)
 * pop_server - список POP3-серверов, доступных клиентам (option pop-server)
 * routers - список адресов шлюзов (option routers)
 * smtp_server - список SMTP-серверов (option smtp_server)
 * subnet_mask - маска сети (option subnet-mask)
 * tftp_server_name - имя сервера TFTP (option server-name)
 * www_server - список www-серверов, передаваемых клиенту
 * range - диапазон адресов, назначаемых клиентам (range)
 * allow_unknown_clients - разрешать подключение клиентов, которых нет в списке
 *                         хостов (allow unknown-clients, deny unknown-clients).
 * filename - имя загрузочного файла с ядром (filename)
 * next_server - имя TFTP-сервера, на котором находится filename (next-server)
 * root_path - путь к корневому диску клиента (root-path)
 *
 * custom_options - произвольные параметры
 *
 * Функция load загружает данные подсети из соответствующей ветки LDAP
 *
 * Функция save сохраняет данные подсети в соответствующую ветвь LDAP
 *
 * Функция getId получает полный идентификатор сети
 *
 * Функция getDN получает DN сети
 *
 * @author andrey
 */
class DhcpSubnet extends WABEntity {

    public $dhcpServer;
    public $fileServer;

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = @$params[2];
        $this->old_name = $this->name;
        $this->title = "";

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        
		$this->app = $app;
        $this->template = "templates/controller/DhcpSubnet.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/DhcpSubnet.js";
        $this->icon = $app->skinPath."images/Tree/network.gif";

        $this->domain_name_servers="";
        $this->netbios_name_servers="";
        $this->subnet_mask = "255.255.255.0";
        $this->routers = "";
        $this->range = "";
        $this->allow_unknown_clients = "";
        $this->filename = "";
        $this->next_server = "";
        $this->root_path = "";
        $this->custom_options = "";
        
        $this->inRate = "";
        $this->outRate = "";
        $this->inRateDim = "kbit";
        $this->outRateDim = "kbit";
        $this->inCeil = "";
        $this->outCeil = "";
        $this->inCeilDim = "kbit";
        $this->outCeilDim = "kbit";
        $this->defaultHostInRate = "";
        $this->defaultHostInRateDim = "kbit";
        $this->defaultHostOutRate = "";
        $this->defaultHostOutRateDim = "kbit";
        $this->defaultHostInCeil = "";
        $this->defaultHostInCeilDim = "kbit";
        $this->defaultHostOutCeil = "";
        $this->defaultHostOutCeilDim = "kbit";
        $this->dropInternet = "0";
        
        $this->width = "600";
        $this->height = "550";
        $this->overrided = "width,height";

        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."DhcpSubnet";
        $this->tabs_string = "net|Сеть|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "host|Хост|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "custom|Дополнительно|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "scan|Поиск хостов|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "net";
        $this->skinPath = $app->skinPath;
        $this->denyFileAccess = 0;
        $this->loaded = false;
        $this->hosts_loaded = false;
        
        $this->clientClass = "DhcpSubnet";
        $this->parentClientClasses = "Entity";        
    }

    function save($arguments=null) {
        global $Objects;
        $saveFirewallFile = false;
        if (isset($arguments)) {
			$this->load();
			$this->setArguments($arguments);
		}
        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();

        $capp = $Objects->get($this->module_id);
        if ($capp->gatewayIp!="" and file_exists("/var/WAB2/mounts/Bastion/etc/hostname")) {
			if ($this->inRate=="") {
			    $this->reportError("Укажите минимальную входящую скорость сети.", "load");
			    return 0;            
			}        
			if ($this->outRate=="")
			    $this->outRate = $this->inRate;
			if ($this->inCeil=="")
			    $this->inCeil = $this->inRate;
			if ($this->outCeil=="")
			    $this->outCeil = $this->inCeil;
			
			if ($this->defaultHostInRate=="")
			    $this->defaultHostInRate = $this->inRate;
			if ($this->defaultHostOutRate=="")
			    $this->defaultHostOutRate = $this->defaultHostInRate;
			if ($this->defaultHostInCeil=="")
			    $this->defaultHostInCeil = $this->defaultHostInRate;
			if ($this->defaultHostOutCeil=="")
			    $this->defaultHostOutCeil = $this->defaultHostInCeil;
		}
        
            
        $ldap_service_dn = $this->dhcpServer->getServiceDN();
        $ds = ldap_connect($this->dhcpServer->ldap_proto."://".$this->dhcpServer->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }
        
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->dhcpServer->ldap_user,$this->dhcpServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }
        if ($this->name != $this->old_name and $this->old_name!="") {
            $saveFirewallFile = true;
            $result = ldap_list($ds,$ldap_service_dn,"cn=".$this->name);
            $entries = ldap_get_entries($ds,$result);
            if ($entries["count"]!=0) {
                $this->reportError("Подсеть ".$this->name." уже существует !","save");
                return 0;
            }
            $Objects->remove("DhcpSubnet_".$this->module_id."_".$this->old_name);
        }
        $result = ldap_list($ds,$ldap_service_dn,"cn=".$this->old_name);
        $found = false;
        if ($result!=FALSE) {
            $entries = ldap_get_entries($ds,$result);
            if ($entries["count"]==1) {
                $found = true;
            }
        }
        $entry = array();
        $entry["cn"] = $this->name;
        if ($this->range != "")
            $entry["dhcpRange"] = $this->range;
        else {
            if ($this->old_range!="") {
                $del = array();
                $del["dhcpRange"] = $this->old_range;
                ldap_mod_del($ds,"cn=".$this->old_name.",".$ldap_service_dn,$del);
            }
        }
        $entry["dhcpStatements"] = array();
        $entry["dhcpOption"] = array();
        if ($this->allow_unknown_clients!="")
            $entry["dhcpStatements"][count($entry["dhcpStatements"])] = $this->allow_unknown_clients;

        if ($this->filename!="")
            $entry["dhcpStatements"][count($entry["dhcpStatements"])] = "filename ".$this->filename;
        else {
            if ($this->old_filename!="") {
                $del = array();
                $del["dhcpStatements"] = "filename ".$this->old_filename;
                ldap_mod_del($ds,"cn=".$this->old_name.",".$ldap_service_dn,$del);
            }
        }
        if ($this->next_server!="")
            $entry["dhcpStatements"][count($entry["dhcpStatements"])] = "next-server ".$this->next_server;
        else {
            if ($this->old_next_server!="") {
                $del = array();
                $del["dhcpStatements"] = "next-server ".$this->old_next_server;
                ldap_mod_del($ds,"cn=".$this->old_name.",".$ldap_service_dn,$del);
            }
        }
        if ($this->root_path!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "root-path ".$this->root_path;
        else {
            if ($this->old_root_path!="") {
                $del = array();
                $del["dhcpOption"] = "root-path ".$this->old_root_path;
                ldap_mod_del($ds,"cn=".$this->old_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->subnet_mask!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "subnet-mask ".$this->subnet_mask;

        if ($this->subnet_mask != $this->old_subnet_mask)
            $saveFirewallFile = true;
        if ($this->routers!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "routers ".$this->routers;
        else {
            if ($this->old_routers!="") {
                $del = array();
                $del["dhcpOption"] = "routers ".$this->old_routers;
                ldap_mod_del($ds,"cn=".$this->old_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->domain_name_servers!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "domain-name-servers ".$this->domain_name_servers;
        else {
            if ($this->old_domain_name_servers!="") {
                $del = array();
                $del["dhcpOption"] = "domain-name-servers ".$this->old_domain_name_servers;
                ldap_mod_del($ds,"cn=".$this->old_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->netbios_name_servers!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "netbios-name-servers ".$this->netbios_name_servers;
        else {
            if ($this->old_netbios_name_servers!="") {
                $del = array();
                $del["dhcpOption"] = "netbios-name-servers ".$this->old_netbios_name_servers;
                ldap_mod_del($ds,"cn=".$this->old_name.",".$ldap_service_dn,$del);
            }
        }
        
        // Вычисляем количество бит сетевой части маски сети
        $subnet_mask_arr = explode(".",$this->subnet_mask);
        $res = "";
        foreach ($subnet_mask_arr as $sub) {
            $res .= base_convert($sub,10,2);
        }
        $res = str_replace("0","",$res);
        $entry["dhcpNetMask"] = strlen($res);
        $entry["comment"] = $this->title;
        $entry["denyFileAccess"] = $this->denyFileAccess;
        if ($this->denyFileAccess!=$this->old_denyFileAccess)
            $saveFirewallFile = true;
        $custom_options = explode("\n",str_replace("|","\n",$this->custom_options));
        foreach ($custom_options as $option) {
            if ($option=="")
                continue;
            $opt_parts = explode(" ",$option);
            if ($opt_parts[0]=="option") {
                $opt_part = array_shift($opt_parts);
                $option = implode(" ",$opt_parts);
                $entry["dhcpOption"][count($entry["dhcpOption"])] = $option;
            } else
                $entry["dhcpStatements"][count($entry["dhcpStatements"])] = $option;
        }
        if (count($entry["dhcpOption"])==0)
            unset($entry["dhcpOption"]);
        if (count($entry["dhcpStatements"])==0)
            unset($entry["dhcpStatements"]);

        // Интеграция с Интернет-шлюзом -->
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        
        $shell = $Objects->get("Shell_shell");
        $capp = $Objects->get($this->module_id);
        if ($capp->gatewayIp!="" and file_exists("/var/WAB2/mounts/Bastion/etc/hostname")) {
            $path = $capp->gatewayIntegrationPath;
            $old_shaping_file_name = $path."/a_net_".str_replace(".","_",$this->old_name);
            $shaping_file_name = $path."/a_net_".str_replace(".","_",$this->name);
            $old_drop_file_name = $path."/a_1net_".str_replace(".","_",$this->old_name);
            $drop_file_name = $path."/a_1net_".str_replace(".","_",$this->name);

            $shell->exec_command($capp->gatewaySSHCommand." mv '".$old_shaping_file_name." ".$shaping_file_name."'");
            $shell->exec_command($capp->gatewaySSHCommand." mv '".$old_drop_file_name." ".$drop_file_name."'");

            $this->in_rate = $this->inRate.$this->inRateDim;
            $this->out_rate = $this->outRate.$this->outRateDim;
            $this->default_host_in_rate = $this->defaultHostInRate.$this->defaultHostInRateDim;
            $this->default_host_out_rate = $this->defaultHostInRate.$this->defaultHostOutRateDim;
            $this->in_ceil = $this->inCeil.$this->inCeilDim;
            $this->out_ceil = $this->outCeil.$this->outCeilDim;
            $this->default_host_in_ceil = $this->defaultHostInCeil.$this->defaultHostInCeilDim;
            $this->default_host_out_ceil = $this->defaultHostInCeil.$this->defaultHostOutCeilDim;
            $arr = explode(".",$this->name);
            $this->class_number = $arr[2]."0";
            if (!file_exists("/var/WAB2/mounts/Bastion".$shaping_file_name)) {
                    $fp = fopen("/var/WAB2/mounts/Bastion".$shaping_file_name,"w");
                    fwrite($fp,"");
                    fclose($fp);
            }
            if (!file_exists("/var/WAB2/mounts/Bastion".$drop_file_name)) {
                    $fp = fopen("/var/WAB2/mounts/Bastion".$drop_file_name,"w");
                    fwrite($fp,"");
                    fclose($fp);
            }
            file_put_contents("/var/WAB2/mounts/Bastion".$shaping_file_name,strtr(@file_get_contents($capp->gatewayNetworkTemplateConfigFile),$this->getArgs()));
            if ($this->dropInternet) {
                file_put_contents("/var/WAB2/mounts/Bastion".$drop_file_name,"#!/bin/sh\niptables -I FORWARD -i \$eth0 -o \$eth1 -s ".$this->name."/".$this->subnet_mask." -j REJECT
iptables -I INPUT -i \$eth0 -s ".$this->name."/".$this->subnet_mask." -p tcp --dport \$http_proxy_port -j REJECT
iptables -I INPUT -i \$eth0 -s ".$this->name."/".$this->subnet_mask." -p tcp --dport \$smtp_proxy_port -j REJECT");
            } else {
                file_put_contents("/var/WAB2/mounts/Bastion".$drop_file_name,"");
            }
            $shell->exec_command($capp->gatewaySSHCommand." '".$app->debianNetworkRestartCommand."'");
            $entry["inRate"] = $this->inRate." ".$this->inRateDim;
            $entry["outRate"] = $this->outRate." ".$this->outRateDim;
            $entry["inCeil"] = $this->inCeil." ".$this->inCeilDim;
            $entry["outCeil"] = $this->outCeil." ".$this->outCeilDim;
            $entry["defaultHostInRate"] = $this->defaultHostInRate." ".$this->defaultHostInRateDim;
            $entry["defaultHostOutRate"] = $this->defaultHostOutRate." ".$this->defaultHostOutRateDim;
            $entry["defaultHostInCeil"] = $this->defaultHostInCeil." ".$this->defaultHostInCeilDim;
            $entry["defaultHostOutCeil"] = $this->defaultHostOutCeil." ".$this->defaultHostOutCeilDim;        
            $entry["dropInternet"] = $this->dropInternet;
        }
        // <-- Интеграция с Интернет-шлюзом
        
        $entry["objectClass"][0] = "dhcpSubnet";
        $entry["objectClass"][1] = "info";
        if (!$found or $this->name != $this->old_name)
            @ldap_add($ds,"cn=".$this->name.",".$ldap_service_dn,$entry);
        else
            @ldap_modify($ds,"cn=".$this->name.",".$ldap_service_dn,$entry);
        if (ldap_error($ds)!="Success") {
        	$this->reportError("Произошла ошибка при сохранении данных! Проверьте правильность ввода: '".ldap_error($ds)."'","save");
        	return 0;
        }
        if ($found) {
            if ($this->name != $this->old_name) {
            	$capp = $Objects->get($this->module_id);
            	if (is_object($capp->docFlowApp)) {
            		$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
            		if (!$adapter->connected)
            			$adapter->connect();
            		if ($adapter->connected) {
            			$sql = "UPDATE fields SET value='DhcpSubnet_".$this->module_id."_".$this->name."' WHERE name='objectId' AND value='DhcpSubnet_".$this->module_id."_".$this->old_name."' AND classname='ReferenceDhcpSubnetInfoCard'";
            			$stmt = $adapter->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
            			$stmt->execute();
            		}
            	}
            	 
                $result = ldap_list($ds,"cn=".$this->old_name.",".$ldap_service_dn,"objectClass=dhcpHost");
                $entries = ldap_get_entries($ds,$result);
                foreach($entries as $entry) {
	          	    if ($entry["cn"][0]=="")
    	     	        continue;
               		$host = $Objects->get("DhcpHost_".$this->module_id."_".$this->old_name."_".$entry["cn"][0]);
            	   	if (!$host->loaded)
	    	      		$host->load();
    	           	$host->subnet_name = $this->name;
		           	$host->save();
    	        }
                @ldap_delete($ds,"cn=".$this->old_name.",".$ldap_service_dn);
            }
        }
        if ($this->old_name=="" or ($this->name!=$this->old_name and $this->old_name !=""))
            $Objects->set("DhcpSubnet_".$this->module_id."_".$this->name,$this);

        $this->dhcpServer->load();
        $shell = $Objects->get("Shell_shell");
        $sub_arr = explode(".",$this->old_name);
        array_pop($sub_arr);
        $app = $Objects->get($this->module_id);
        $bind_path = explode('/',$this->dhcpServer->bindZonesFile);
        array_pop($bind_path);
        $bind_path = implode("/",$bind_path);

        $sub = implode(".",array_reverse($sub_arr));
        $sub_file = $bind_path."/".$sub.".in-addr.arpa";

        $shell->exec_command($app->remoteSSHCommand." ".$this->dhcpServer->deleteCommand." ".$sub_file);
        $this->loaded = true;
        $app = $this->dhcpServer;
        if (!$app->loaded)
            $app->load();
        if ($this->dhcpServer->netCenterAutoRestart)
            $shell->exec_command($app->remoteSSHCommand." ".$app->dhcpRestartCommand);
        if ($this->name != $this->old_name) {
            $Objects->remove("DhcpSubnet_".$this->module_id."_".$this->old_name);
            $this->dhcpServer->updateDNSZones();        
        }
        if ($saveFirewallFile) {            
            $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
            $this->fileServer->saveFirewallFile();
        }
        
        $this->dhcpServer->save();
        $app = $this->app;
        if ($this->old_name=="")
        	$app->raiseRemoteEvent("DHCPSUBNET_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("DHCPSUBNET_CHANGED","object_id=".$this->getId());
        $this->old_name = $this->name;        
        $this->loaded = true; 
        ldap_unbind($ds);
    }

    function load() {

        global $Objects;
        
        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();
        
        $capp = $Objects->get($this->module_id);
        if ($capp->gatewayIp!="") {
            if (!$this->setInetAccess) {
                $this->tabs_string.= ";internet|Доступ в Интернет|".$this->skinPath."images/spacer.gif";
                $this->setInetAccess = true;
            }            
        }
        $ldap_service_dn = $this->dhcpServer->getServiceDN();
        $ds = ldap_connect($this->dhcpServer->ldap_proto."://".$this->dhcpServer->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->dhcpServer->ldap_user,$this->dhcpServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }
        if ($this->old_name!="") {
            $result = ldap_list($ds,$ldap_service_dn,"cn=".$this->old_name);
            if ($result==FALSE)
                return 0;
            $entries = ldap_get_entries($ds, $result);
            if ($entries["count"]==0)
                return 0;
        } else return 0;
        $value = ldap_first_entry($ds, $result);
        $values = ldap_get_values($ds,$value,"comment");
        $this->title = $values[0];
        $values = @ldap_get_values($ds,$value,"denyFileAccess");
        $this->denyFileAccess = $values[0];
        $values = @ldap_get_values($ds,$value,"dhcpRange");
        $this->range = @$values[0];
        $this->old_range = @$values[0];
        $values = ldap_get_values($ds,$value,"dhcpOption");
        $custom_options = array();
        foreach($values as $val) {
            if (is_numeric($val))
                continue;
            $orig_val = $val;
            $value_parts = explode(" ",$val);
            $val = str_replace($value_parts[0]." ","",$val);
            switch ($value_parts[0]) {
                case "netbios-name-servers":
                    $this->netbios_name_servers = $val;
                    break;
                case "domain-name-servers":
                    $this->domain_name_servers = $val;
                    break;
                case "subnet-mask":
                    $this->subnet_mask = $val;
                    break;
                case "routers":
                    $this->routers = $val;
                    break;
                case "root-path":
                    $this->root_path = $val;
                    break;
                default:
                    $custom_options[count($custom_options)] = "option ".$orig_val;
            }
        }
        $values = ldap_get_values($ds,$value,"dhcpStatements");
        foreach($values as $val) {
            if (is_numeric($val))
                continue;
            $orig_val = $val;
            $value_parts = explode(" ",$val);
            $val = str_replace($value_parts[0]." ","",$val);
            switch ($value_parts[0]) {
                case "filename":
                    $this->filename = $val;
                    break;
                case "next-server":
                    $this->next_server = $val;
                    break;
                case "allow-unknown-clients":
                    $this->allow_unknown_clients = "allow-unknown-clients";
                    break;
                case "deny-unknown-clients":
                    $this->allow_unknown_clients = "";
                    break;
                default:
                    $custom_options[count($custom_options)] = $orig_val;
            }
        }
        
        $this->custom_options = implode("\n",$custom_options);

        $values = @ldap_get_values($ds,$value,"inRate");        
        $arr = @explode(" ",@$values[0]);$this->inRate = @$arr[0];$this->inRateDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"outRate");        
        $arr = @explode(" ",@$values[0]);$this->outRate = @$arr[0];$this->outRateDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"inCeil");        
        $arr = @explode(" ",@$values[0]);$this->inCeil = @$arr[0];$this->inCeilDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"outCeil");        
        $arr = @explode(" ",@$values[0]);$this->outCeil = @$arr[0];$this->outCeilDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"defaultHostInRate");        
        $arr = @explode(" ",@$values[0]);$this->defaultHostInRate = @$arr[0];$this->defaultHostInRateDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"defaultHostOutRate");        
        $arr = @explode(" ",@$values[0]);$this->defaultHostOutRate = @$arr[0];$this->defaultHostOutRateDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"defaultHostInCeil");        
        $arr = @explode(" ",@$values[0]);$this->defaultHostInCeil = @$arr[0];$this->defaultHostInCeilDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"defaultHostOutCeil");        
        $arr = @explode(" ",@$values[0]);$this->defaultHostOutCeil = @$arr[0];$this->defaultHostOutCeilDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"dropInternet");        
        $this->dropInternet = @$values[0];
        
        $this->old_domain_name_servers=$this->domain_name_servers;
        $this->old_netbios_name_servers=$this->netbios_name_servers;
        $this->old_subnet_mask = $this->subnet_mask;
        $this->old_routers = $this->routers;
        $this->old_range = $this->range;
        $this->old_allow_unknown_clients = $this->allow_unknown_clients;
        $this->old_filename = $this->filename;
        $this->old_next_server = $this->next_server;
        $this->old_root_path = $this->root_path;
        $this->old_name = $this->name;
        $this->old_title = $this->title;
        $this->old_denyFileAccess = $this->denyFileAccess;
        $this->loaded = true;
        ldap_unbind($ds);
        $app = $Objects->get($this->module_id);
        if ($this->loaded and is_object($app->docFlowApp)) {
        	$this->adapter = $Objects->get("DocFlowDataAdapter_".$app->docFlowApp->getId()."_1");
        	$query = "SELECT entities FROM fields WHERE @objectId='".$this->getId()."' AND @classname='ReferenceDhcpSubnetInfoCard'";
        	$res = PDODataAdapter::makeQuery($query, $this->adapter,$app->docFlowApp->getId());
        	if (count($res)>0) {
        		$card = current($res);
        		$this->referenceCode = "<input type='button' fileid='".$card->getId()."' id='referenceButton' value='Открыть описание'/>";
        	} else {
        		$this->referenceCode = "<input type='button' fileid='ReferenceDhcpSubnetInfoCard_".$app->docFlowApp->getId()."_' id='referenceButton' value='Открыть описание'/>";
        	}
        }        
    }

    function loadHosts($fullLoad=false) {
        global $Objects;
        
        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();
        
        if (!$this->loaded)
            $this->load();
        $ldap_service_dn = $this->dhcpServer->getServiceDN();
        $ds = ldap_connect($this->dhcpServer->ldap_proto."://".$this->dhcpServer->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->dhcpServer->ldap_user,$this->dhcpServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }
        $result = @ldap_list($ds,$this->getDN(),"objectClass=dhcpHost");
        $entries = @ldap_get_entries($ds,$result);
        $custom_options = array();
        if (ldap_error($ds)=="Success") {
            foreach($entries as $entry) {
                if ($entry["cn"][0]=="")
                    continue;
                $host = $Objects->get("DhcpHost_".$this->module_id."_".$this->name."_".$entry["cn"][0]);
                $host->host_type = $entry["hosttype"][0];
                $host->denyFileAccess = @$entry["denyfileaccess"][0];
                $host->title = @$entry["comment"][0];                
                $host->icon = $host->host_types_icons[$host->host_type];
                $host->objectGroup = $entry["objectgroup"][0];                
                $host->hw_address = str_replace("ethernet ","",$entry["dhcphwaddress"][0]);                
                $values = $entry["dhcpstatements"];
                if (is_array($values)) {
                    foreach($values as $val) {
                        if (is_numeric($val))
                            continue;
                        $orig_val = $val;
                        $value_parts = explode(" ",$val);
                        $val = str_replace($value_parts[0]." ","",$val);
                        switch ($value_parts[0]) {
                            case "filename":
                                $host->filename = str_replace('"','',$val);
                                break;
                            case "next-server":
                                $host->next_server = $val;
                                break;
                            case "fixed-address":
                                $host->fixed_address = $val;
                                $host->old_fixed_address = $val;
                                break;
                            case "allow":
                                $host->allow_booting = "allow booting";
                                break;
                            case "deny":
                                $host->allow_booting = "deny booting";
                                break;
                            default:
                                $custom_options[count($custom_options)] = $orig_val;
                        }
                    }
                }                
                if ($fullLoad and !$host->loaded)
                    $host->load();
            }
        }
        $this->hosts_loaded = true;
    }

    function __get($name) {
        global $Objects;
        switch ($name) {
            case "hosts":
                return $Objects->query("DhcpHost",array("subnet_name"=>$this->name));
            default:
                if (isset($this->fields[$name]))
                    return $this->fields[$name];
                else
                    return "";
        }
    }

    function getId() {
        if (!$this->loaded)
            $this->load();
        return "DhcpSubnet_".$this->module_id."_".$this->name;
    }

    function getDN() {
        global $Objects;
                
        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();        
        return "cn=".$this->name.",".$this->dhcpServer->getServiceDN();
    }

    function getArgs() {
        $result = parent::getArgs();
        $range_array = explode(" ",$this->range);
        if (count($range_array)==2) {
            $result["{range_start}"] = $range_array[0];
            $result["{range_end}"] = $range_array[1];
        } else {
            $result["{range_start}"] = "";
            $result["{range_end}"] = "";
        }
        if ($this->allow_unknown_clients=="allow-unknown-clients")
            $result["{allow_unknown_clients_checked}"] = "checked";
        else
            $result["{allow_unknown_clients_checked}"] = "";
        $result["{|referenceCode}"] = $this->referenceCode;
        return $result;
        
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->title;
    }

    function remove($host) {
		if (is_array($host))
			$host = $host["host"];
        if (!$this->hosts_loaded)
            $this->loadHosts();
        global $Objects;
        
        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();

        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        
        $hostObj = $Objects->get("DhcpHost_".$this->module_id."_".$this->name."_".$host);
        if (!$Objects->contains("DhcpHost_".$this->module_id."_".$this->name."_".$host)) {
            $this->reportError("Хост ".$host." не существует");
            return 0;
        }
        $ds = ldap_connect($this->dhcpServer->ldap_proto."://".$this->dhcpServer->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->dhcpServer->ldap_user,$this->dhcpServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }
        
        $this->app->raiseRemoteEvent("DHCPHOST_DELETED","object_id=".$hostObj->getId());
        
        ldap_delete($ds, "cn=".$host.",".$this->getDN());
        $app = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        if ($app->gatewayIp!="" and file_exists("/var/WAB2/mounts/Bastion/etc/hostname")) {
            $shell->exec_command($app->gatewaySSHCommand." rm ".$app->gatewayIntegrationPath."/host_".str_replace(".","_",$hostObj->fixed_address));
            $shell->exec_command($app->gatewaySSHCommand." /etc/init.d/networking restart");
        }
        if (file_exists($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($host).".conf"))
                unlink($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($host).".conf");
        if (file_exists($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($host)."_guest.conf"))
                unlink($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($host)."_guest.conf");
        if (file_exists($app->remotePath.$this->fileServer->smbCustomHostsPath."/".strtolower($host).".conf"))
                unlink($app->remotePath.$this->fileServer->smbCustomHostsPath."/".strtolower($host).".conf");        
        
        $shares = $hostObj->getNfsSharesFromFile();
        $result = array();
        foreach ($shares as $name=>$share) {
            if (isset($share["hosts"][$hostObj->name])) {
                unset($share["hosts"][$hostObj->name]);
            }
            $result[$name] = $share;
        }            
        $hostObj->saveNfsSharesToFile($result);

        $shares = $hostObj->getAfpSharesFromFile();
        $result = array();
        foreach ($shares as $name=>$share) {
        	if (isset($share["hosts"][$hostObj->fixed_address])) {
        		unset($share["hosts"][$hostObj->fixed_address]);
        	}
        	$result[$name] = $share;
        }
        $hostObj->saveAfpSharesToFile($result);
        
        $Objects->remove("DhcpHost_".$this->module_id."_".$this->name."_".$host);
        $this->dhcpServer->updateDNSZones();
        if ($app->remoteSSHCommand=="") {
        if ($this->fileServer->smbAutoRestart) {
            $shell->exec_command($app->remoteSSHCommand." ".$this->fileServer->smbReloadCommand);
            $shell->exec_command($app->remoteSSHCommand." ".$this->fileServer->nfsRestartCommand);
        }
        if ($this->dhcpServer->netCenterAutoRestart)
            $shell->exec_command($app->remoteSSHCommand." ".$this->dhcpServer->bindRestartCommand);
        } else {
            $cmd = " \"";
            if ($this->fileServer->smbAutoRestart) {
                $cmd .= $this->fileServer->smbReloadCommand.";";
                $cmd .= $this->fileServer->nfsRestartCommand.";";
            }
            if ($this->dhcpServer->netCenterAutoRestart)
                $cmd .= $this->dhcpServer->bindRestartCommand;
            $cmd .= "\"";
            shell_exec($app->remoteSSHCommand." 'echo ".$cmd." | at now'");            
        }
        
        $capp = $Objects->get($this->module_id);
        if (is_object($capp->docFlowApp)) {
        	$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
        	if (!$adapter->connected)
        		$adapter->connect();
        	if ($adapter->connected) {
        		$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @objectId='".$hostObj->getId()."' AND @classname='ReferenceDhcpHostInfoCard'",$adapter,$capp->docFlowApp->getId());
        		foreach ($entities as $entity) {
        			$entity->loaded = false;
        			$entity->load();
        			$entity->deleted = 1;
        			$entity->save(true);
        		}
        	}
        }        
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "remove";
			case '4': return "save";
		}
		return parent::getHookProc($number);
	}
}
?>