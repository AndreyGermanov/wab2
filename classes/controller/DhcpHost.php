<?php
/**
 * Класс содержит информацию о хосте в подсети DHCP-сервера.
 * Информация о подсети находится в каталоге LDAP, под учетной записью подсети DHCP-сервера,
 * полный id которой имеет форму "DhcpSubnet_".$this->module_id."_".$this-subnet;
 *
 * Полный id хоста имеет следующую форму: DhcpHost_".$this->module_id."_".$this->subnet."_".$this->name
 *
 *
 * Каждый хост идентифицируется MAC-адресом, который хранится в свойстве hw_address.
 * 
 * Хост может передавать клиенту различные настройки сети. Они указываются в
 * атрибуте dhcpOption и имеют следующие значения:
 *
 * option fixed-address - IP-адрес
 * filename - имя загрузочного файла с ядром (filename)
 * next_server - имя TFTP-сервера, на котором находится filename (next-server)
 * option root_path - путь к корневому диску клиента (root-path)
 * allow_booting/deny booting - разрешить или запретить клиенту подключаться
 * option interface-mtu - максимальная скорость передачи данных по сети
 * option ip-forwarding - включает функцию маршрутизации на хосте
 * 
 * option domain_name_servers - серверы доменных имен (option domain-name-servers)
 * option netbios_name_servers - серверы WINS (option netbios-name-servers)
 * option netbios_node_type - тип узла WINS (option netbios-node-type: 1 - B-Node (только
 *                     широковещание, 2 - P-Node - только WINS, 4 - M-Node (сначала
 *                     широковещание, потом WINS, 8 - H-Node - сначала WINS, потом
 *                     широковещание)
 * option ntp_servers - серверы времени (option ntp-servers)
 * option pop_server - список POP3-серверов, доступных клиентам (option pop-server)
 * option routers - список адресов шлюзов (option routers)
 * option smtp_server - список SMTP-серверов (option smtp_server)
 * option subnet_mask - маска сети (option subnet-mask)
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
class DhcpHost extends WABEntity {

    public $dhcpServer;
    public $fileServer;
    public $accessRules = array();
    public $smbShares = array();
    public $nfsShares = array();
    public $afpShares = array();
    public $userList = array();
    public $host_types;
    public $host_types_icons;
    public $netbios_node_types;
    public $subnet;

    function construct($params) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->host_types = array();
        $this->host_types["desktop"] = "Рабочая станция";
        $this->host_types["desktop_Unix"] = "Рабочая станция Unix/Linux";
        $this->host_types["desktop_MacOS"] = "Рабочая станция MacOS";
        $this->host_types["desktop_Win7"] = "Рабочая станция Windows7/Vista";
        $this->host_types["desktop_WinXP"] = "Рабочая станция Windows XP/2000/NT";
        $this->host_types["desktop_Win95"] = "Рабочая станция Windows 95/98/Me";
        $this->host_types["desktop_LBD"] = "Рабочая станция LVA Business Desktop";
        $this->host_types["server"] = "Сервер";
        $this->host_types["server_Unix"] = "Сервер Unix/Linux";
        $this->host_types["server_MacOS"] = "Сервер MacOS";
        $this->host_types["server_Win2000"] = "Сервер Windows NT/2000/2003 Server";
        $this->host_types["server_Win2008"] = "Сервер Windows 2008 Server";
        $this->host_types["server_Hybrid"] = "Сервер LVA Business Server";
        $this->host_types["server_Bastion"] = "Сервер Mystix Bastion ACS";
        $this->host_types["server_Collector"] = "Сервер Mystix Collector MX";
        $this->host_types["server_Controller"] = "Сервер Mystix Controller";
        $this->host_types["switch"] = "Управляемый коммутатор";
        $this->host_types["switch_CiscoCatalyst"] = "Управляемый коммутатор Cisco Catalyst";
        $this->host_types["router"] = "Маршрутизатор";
        $this->host_types["router_Cisco"] = "Маршрутизатор Cisco";
        $this->host_types["print_server"] = "Принт-сервер";
        $this->host_types["printer"] = "Сетевой принтер";
        $this->host_types["wifi_router"] = "Точка доступа Wi-Fi";

        $this->host_types_icons = array();
        $this->host_types_icons["desktop"] = $app->skinPath."images/Window/system-settings.gif";
        $this->host_types_icons["desktop_Unix"] = $app->skinPath."images/Window/system-settings.gif";
        $this->host_types_icons["desktop_MacOS"] = $app->skinPath."images/Window/system-settings.gif";
        $this->host_types_icons["desktop_Win7"] = $app->skinPath."images/Window/system-settings.gif";
        $this->host_types_icons["desktop_WinXP"] = $app->skinPath."images/Window/system-settings.gif";
        $this->host_types_icons["desktop_Win95"] = $app->skinPath."images/Window/system-settings.gif";
        $this->host_types_icons["desktop_LBD"] = $app->skinPath."images/Window/system-settings.gif";
        $this->host_types_icons["server"] = $app->skinPath."images/Tree/server.png";
        $this->host_types_icons["server_Unix"] = $app->skinPath."images/Tree/server.png";
        $this->host_types_icons["server_MacOS"] = $app->skinPath."images/Tree/server.png";
        $this->host_types_icons["server_Win2000"] = $app->skinPath."images/Tree/server.png";
        $this->host_types_icons["server_Win2008"] = $app->skinPath."images/Tree/server.png";
        $this->host_types_icons["server_Hybrid"] = $app->skinPath."images/Tree/controller-server.gif";
        $this->host_types_icons["server_Bastion"] = $app->skinPath."images/Tree/controller-server.gif";
        $this->host_types_icons["server_Collector"] = $app->skinPath."images/Tree/controller-server.gif";
        $this->host_types_icons["server_Controller"] = $app->skinPath."images/Tree/controller-server.gif";
        $this->host_types_icons["switch"] = $app->skinPath."images/Tree/switch.png";
        $this->host_types_icons["switch_CiscoCatalyst"] = $app->skinPath."images/Tree/switch.png";
        $this->host_types_icons["router"] = $app->skinPath."images/Tree/router.png";
        $this->host_types_icons["router_Cisco"] = $app->skinPath."images/Tree/router.png";
        $this->host_types_icons["print_server"] = $app->skinPath."images/Tree/switch.png";
        $this->host_types_icons["printer"] = $app->skinPath."images/Tree/printer.png";
        $this->host_types_icons["wifi_router"] = $app->skinPath."images/Tree/wifi.gif";

        $this->host_type = "desktop";

        $this->netbios_node_types = array();
        $this->netbios_node_types["1"] = "B-Node";
        $this->netbios_node_types["2"] = "P-Node";
        $this->netbios_node_types["4"] = "M-Node";
        $this->netbios_node_types["8"] = "H-Node";
        $this->netbios_node_type = 8;

        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->hw_address = "";
        $this->subnet_name = array_shift($params);
        if (stripos($this->subnet_name,".")===FALSE)
            $this->subnet_name .= ".".array_shift($params).".".array_shift($params).".".array_shift($params);
        $this->name = implode("_",$params);
        $this->old_subnet_name = $this->subnet_name;
        $this->old_name = $this->name;
        $this->nametitle = $this->name;
        $this->title = "";
        $this->descr = "";
        
        $this->template = "templates/controller/DhcpHost.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/DhcpHost.js";

        $this->icon = $app->skinPath."images/Window/system-settings.gif";
        $this->skinPath = $app->skinPath;
        $this->domain_name_servers="";
        $this->netbios_name_servers="";
        $this->subnet_mask = "";
        $this->routers = "";

        $this->filename = "";
        $this->next_server = "";
        $this->root_path = "";
        $this->fixed_address = "";
        $this->old_fixed_address = "";
        $this->allow_booting = "allow booting";
        $this->interface_mtu = 1500;
        $this->ip_forwarding = false;
        $this->inRate = "";
        $this->outRate = "";
        $this->inRateDim = "kbit";
        $this->outRateDim = "kbit";
        $this->inCeil = "";
        $this->outCeil = "";
        $this->inCeilDim = "kbit";
        $this->outCeilDim = "kbit";
        $this->openPorts = "";
        $this->dropInternet=0;

        $this->remoteDesktopProtocol = "rdp";
        $this->remoteDesktopPort = "";
        $this->remoteDesktopUser = "";
        $this->remoteDesktopPassword = "";
        $this->remoteConsolePort = "";
        $this->webInterfaceProtocol = "http";
        $this->webInterfacePort = "";
        $this->webInterfaceNewWindow = "";         
        $this->defaultUser = 0;
        $this->denyFileAccess = 0;

        $this->accessRules = array();
        
        $this->custom_options = "";

        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->subnet_name.$this->name."DhcpHost";

        $this->width = "750";
        $this->height = "520";
        $this->overrided = "width,height";

        $this->tabs_string = "host|Хост|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "net|Сеть|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "custom|Дополнительно|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "access_rules|Права доступа|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "description|Описание|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "scan|Сканирование|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "control|Управление|".$this->skinPath."images/spacer.gif";
        $capp = $Objects->get($this->module_id);
        if ($capp->gatewayIp!="") {
            $this->tabs_string.= ";internet|Доступ в Интернет|".$this->skinPath."images/spacer.gif";
            $this->tabs_string.= ";ports|Перенаправление портов|".$this->skinPath."images/spacer.gif";
        }

        $this->active_tab = "host";
		$this->clientObjectId = str_replace(".","_",$this->getId());
        $this->clientClass = "DhcpHost";
        $this->parentClientClasses = "Entity";
        $this->referenceCode = "";
        $this->loaded = false;
    }

    function setIcon() {
        if (isset($this->host_types_icons[$this->host_type]))
            $this->icon = $this->host_types_icons[$this->host_type];
    }

    function save($quick=false) {
        global $Objects;
        $saveFirewallFile = false;
        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();

        if (!$this->loaded)
            $this->load();
        
        $shell = $Objects->get("Shell_shell");
        
        if ($this->inRate=="" and ($this->outRate!="" or $this->inCeil!="" or $this->outCeil!="")) {
            $this->reportError("Укажите входящую скорость","save");
            return 0;
        }
        if ($this->outRate=="")
            $this->outRate = $this->inRate;
        
        if ($this->inCeil=="")
            $this->inCeil = $this->inRate;
        
        if ($this->outCeil=="")
            $this->outCeil = $this->inCeil;
        
        
        $old_subnet = $Objects->get("DhcpSubnet_".$this->module_id."_".$this->old_subnet_name);
        $old_subnet_dn = $old_subnet->getDN();
        $subnet = $Objects->get("DhcpSubnet_".$this->module_id."_".$this->subnet_name);
        $subnet_dn = $subnet->getDN();
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

        $found = false;
        
        if (!preg_match("/^[A-Za-z][A-Za-z0-9_\-]*$/",trim($this->name))) {
    	    $this->reportError("Имя хоста должно состоять только из английских букв и цифр, символов '-' и '_' и начинаться с буквы");
    	    return 0;
		}
                
        if (!$quick) {
            $this->name = trim($this->name);
            if ($this->name != $this->old_name and $this->name!="") {
                $saveFirewallFile = true;
                $result = ldap_search($ds,$ldap_service_dn,"cn=".$this->name);
                $entries = ldap_get_entries($ds,$result);
                if ($entries["count"]!=0) {
                    $this->reportError("Хост ".$this->name." уже существует ! -".$this->old_name,"save");
                    return 0;
                }
                if ($this->old_name!="")
                    $Objects->remove("DhcpHost_".$this->module_id."_".$this->old_subnet_name."_".$this->old_name);
            }

            if ($this->fixed_address != $this->old_fixed_address) {
                $saveFirewallFile = true;
                $result = ldap_search($ds,$ldap_service_dn,"(dhcpStatements=fixed-address ".$this->fixed_address.")");
                $entries = ldap_get_entries($ds,$result);
                if ($entries["count"]!=0) {
                    $this->reportError("Хост с указанным IP-адресом уже существует !","save");
                    return 0;
                }
            }

            if ($this->old_subnet_name != $this->subnet_name) {
                $saveFirewallFile = true;
                $result = ldap_list($ds,$ldap_service_dn,"cn=".$this->subnet_name);
                $entries = ldap_get_entries($ds,$result);
                if ($entries["count"]==0) {
                    $this->reportError("Сеть ".$this->subnet_name." не существует !","save");
                    return 0;
                }
                if ($Objects->contains("DhcpHost_".$this->module_id."_".$this->old_subnet_name."_".$this->old_name)) {
                    $Objects->remove("DhcpHost_".$this->module_id."_".$this->old_subnet_name."_".$this->old_name);
                }
            }

            $result = ldap_search($ds,$ldap_service_dn,"cn=".$this->old_name);
            if ($result!=FALSE) {
                $entries = ldap_get_entries($ds,$result);
                if ($entries["count"]==1) {
                    $found = true;
                }
            }
        }
        
        $entry = array();
        $entry["hostType"] = $this->host_type;
        if ($this->descr!="")
            $entry["descr"] = str_replace("|","\n",$this->descr);
        else {
                $del = array();
                $del["descr"] = $this->old_descr;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->title!="")
            $entry["comment"] = $this->title;
        else {
            $del = array();
            $del["comment"] = $this->old_title;
            @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->defaultUser)
        	$entry["defaultUser"] = $this->defaultUser;
        else
        	$entry["defaultUser"] = "0";
        if ($this->denyFileAccess)
        	$entry["denyFileAccess"] = $this->denyFileAccess;
        else {
            $del = array();
            $del["denyFileAccess"] = $this->old_denyFileAccess;
            @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        
        
        if ($this->objectGroup == "")
            $this->objectGroup = 0;
        $entry["objectGroup"] = $this->objectGroup;
        $entry["dhcpHWAddress"] = "ethernet ".$this->hw_address;
        if ($this->hw_address != $this->old_hw_address)
                $saveFirewallFile = true;
        if ($this->denyFileAccess != $this->old_denyFileAccess)
                $saveFirewallFile = true;
        
        $entry["dhcpStatements"] = array();
        $entry["dhcpOption"] = array();
        $entry["dhcpOption"][count($entry["dhcpOption"])] = "host-name ".$this->name;

        if ($this->filename!="")
            $entry["dhcpStatements"][count($entry["dhcpStatements"])] = 'filename "'.$this->filename.'"';
        else {
            if ($this->old_filename!="") {
                $del = array();
                $del["dhcpStatements"] = 'filename "'.$this->old_filename.'"';
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->next_server!="")
            $entry["dhcpStatements"][count($entry["dhcpStatements"])] = "next-server ".$this->next_server;
        else {
            if ($this->old_next_server!="") {
                $del = array();
                $del["dhcpStatements"] = "next-server ".$this->old_next_server;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->allow_booting!="") {
            $entry["dhcpStatements"][count($entry["dhcpStatements"])] = $this->allow_booting;
        }
        
        if ($this->fixed_address!="")
            $entry["dhcpStatements"][count($entry["dhcpStatements"])] = "fixed-address ".$this->fixed_address;
        
        if ($this->root_path!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = 'root-path "'.$this->root_path.'"';
        else {
            if ($this->old_root_path!="") {
                $del = array();
                $del["dhcpOption"] = 'root-path "'.$this->old_root_path.'"';
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->subnet_mask!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "subnet-mask ".$this->subnet_mask;
        else {
            if ($this->old_subnet_mask!="") {
                $del = array();
                $del["dhcpOption"] = "subnet-mask ".$this->old_subnet_mask;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->routers!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "routers ".$this->routers;
        else {
            if ($this->old_routers!="") {
                $del = array();
                $del["dhcpOption"] = "routers ".$this->old_routers;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->domain_name_servers!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "domain-name-servers ".$this->domain_name_servers;
        else {
            if ($this->old_domain_name_servers!="") {
                $del = array();
                $del["dhcpOption"] = "domain-name-servers ".$this->old_domain_name_servers;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->netbios_name_servers!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "netbios-name-servers ".$this->netbios_name_servers;
        else {
            if ($this->old_netbios_name_servers!="") {
                $del = array();
                $del["dhcpOption"] = "netbios-name-servers ".$this->old_netbios_name_servers;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->netbios_node_type!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "netbios-node-type ".$this->netbios_node_type;

        if ($this->interface_mtu!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "interface-mtu ".$this->interface_mtu;
        else {
            if ($this->old_interface_mtu!="") {
                $del = array();
                $del["dhcpOption"] = "interface-mtu ".$this->old_interface_mtu;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }

        if ($this->ip_forwarding!="")
            $entry["dhcpOption"][count($entry["dhcpOption"])] = "ip-forwarding ".$this->ip_forwarding;
        
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
        
        $capp = $Objects->get($this->module_id);
        if ($capp->gatewayIp!="" and file_exists("/var/WAB2/mounts/Bastion/etc/hostname")) {
            $path = $capp->gatewayIntegrationPath;
            $old_file_name = $path."/host_".str_replace(".","_",$this->old_fixed_address);
            $file_name = $path."/host_".str_replace(".","_",$this->fixed_address);

            $shell->exec_command($capp->gatewaySSHCommand." mv '".$old_file_name." ".$file_name."'");

            $this->in_rate = $this->inRate.$this->inRateDim;
            $this->out_rate = $this->outRate.$this->outRateDim;
            $this->in_ceil = $this->inCeil.$this->inCeilDim;
            $this->out_ceil = $this->outCeil.$this->outCeilDim;
            $arr = explode(".",$this->fixed_address);
            $this->net_class_number = $arr[2]."0";
            $this->class_number = $arr[3];
            if (!file_exists("/var/WAB2/mounts/Bastion".$file_name)) {
                    $fp = fopen("/var/WAB2/mounts/Bastion".$file_name,"w");
                    fwrite($fp,"");
                    fclose($fp);
            }
            $string = "";
            if ($this->inRate!="" and $this->outRate!="" and $this->inCeil!="" and $this->outCeil!="")
                $string = strtr(@file_get_contents($capp->gatewayHostTemplateConfigFile),$this->getArgs())."\n";                
            if ($this->dropInternet) {
                $string.="\n"."iptables -I FORWARD -i \$eth0 -o \$eth1 -s ".$this->fixed_address." -j REJECT \n";
                $string.="iptables -I INPUT -i \$eth0 -s ".$this->fixed_address." -p tcp --dport \$http_proxy_port -j ACCEPT\n";
                $string.="iptables -I INPUT -i \$eth0 -s ".$this->fixed_address." -p tcp --dport \$smtp_proxy_port -j ACCEPT\n";
            }
            if ($this->openPorts!="") {
                $string .= "\n# Настраиваем перенаправление портов \n";
                $arr = explode("|",$this->openPorts);
                foreach ($arr as $item) {
                    $item = explode("~",$item);
                    $string .= "iptables -t nat -I PREROUTING -i \$eth1 -p ".$item[1]." --dport ".$item[0]." -j DNAT --to-destination ".$this->fixed_address.":".$item[2]."\n";
                    $string .= "iptables -I FORWARD -i \$eth1 -p ".$item[1]." --dport ".$item[0]." -j ACCEPT\n";
                }
            }
            file_put_contents("/var/WAB2/mounts/Bastion".$file_name,$string);
            $shell->exec_command($capp->gatewaySSHCommand." '".$app->debianNetworkRestartCommand."'");

            $entry["inRate"] = $this->inRate." ".$this->inRateDim;
            $entry["outRate"] = $this->outRate." ".$this->outRateDim;
            $entry["inCeil"] = $this->inCeil." ".$this->inCeilDim;
            $entry["outCeil"] = $this->outCeil." ".$this->outCeilDim;
            if ($this->dropInternet=="")
                $this->dropInternet = "0";
            $entry["dropInternet"] = $this->dropInternet;
            if ($this->openPorts!="")
                $entry["openPorts"] = $this->openPorts;
            else {
                    $del = array();
                    $del["openPorts"] = $this->old_openPorts;
                    @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
            }
        }        
        // <-- Интеграция с Интернет-шлюзом
        if ($this->remoteDesktopProtocol!="")
            $entry["remoteDesktopProtocol"] = $this->remoteDesktopProtocol;
        else {
                $del = array();
                $del["remoteDesktopProtocol"] = $this->old_remoteDesktopProtocol;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->remoteDesktopPort!="")
            $entry["remoteDesktopPort"] = $this->remoteDesktopPort;
        else {
                $del = array();
                $del["remoteDesktopPort"] = $this->old_remoteDesktopPort;
                settype($del["remoteDesktopPort"],"string");
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->remoteDesktopUser!="")
            $entry["remoteDesktopUser"] = $this->remoteDesktopUser;
        else {
                $del = array();
                $del["remoteDesktopUser"] = $this->old_remoteDesktopUser;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->remoteDesktopPassword!="")
            $entry["remoteDesktopPassword"] = $this->remoteDesktopPassword;
        else {
                $del = array();
                $del["remoteDesktopPassword"] = $this->old_remoteDesktopPassword;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->remoteConsolePort!="")
            $entry["remoteConsolePort"] = $this->remoteConsolePort;
        else {
                $del = array();
                $del["remoteConsolePort"] = $this->old_remoteConsolePort;
                settype($del["remoteConsolePort"],"string");
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->webInterfaceProtocol!="")
            $entry["webInterfaceProtocol"] = $this->webInterfaceProtocol;
        else {
                $del = array();
                $del["webInterfaceProtocol"] = $this->old_webInterfaceProtocol;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->webInterfacePort!="")
            $entry["webInterfacePort"] = $this->webInterfacePort;
        else {
                $del = array();
                $del["webInterfacePort"] = $this->old_webInterfacePort;
                settype($del["webInterfacePort"],"string");
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }
        if ($this->webInterfaceNewWindow!="")
            $entry["webInterfaceNewWindow"] = $this->webInterfaceNewWindow;
        else {
                $del = array();
                $del["webInterfaceWindow"] = $this->old_webInterfaceWindow;
                @ldap_mod_del($ds,"cn=".$this->old_name.",cn=".$this->old_subnet_name.",".$ldap_service_dn,$del);
        }

        if ($found or $quick) {
            if ($this->name != $this->old_name or $this->subnet_name != $this->old_subnet_name) {
                ldap_rename($ds,"cn=".$this->old_name.",".$old_subnet_dn,"cn=".$this->name,$subnet_dn,TRUE);
            }
            else
                $entry["cn"] = $this->name;
            @ldap_modify($ds, "cn=".$this->name.",".$subnet_dn, $entry);
        } else {
            $entry["cn"] = $this->name;
            $entry["objectClass"][0] = "dhcpHost";
            $entry["objectClass"][1] = "info";
            @ldap_add($ds,"cn=".$this->name.",".$subnet_dn,$entry);

        }
        if (ldap_error($ds)!="Success") {
        	$this->reportError("Произошла ошибка при сохранении данных! Проверьте правильность ввода: '".ldap_error($ds)."'","save");
        	return 0;
        }        
        
        if ($this->old_name=="" or ($this->name!=$this->old_name and $this->old_name !="") or $this->old_subnet_name != $this->subnet_name)
            $Objects->set("DhcpHost_".$this->module_id."_".$this->subnet_name."_".$this->name,$this);
               
        $app = $Objects->get($this->module_id);
        
        if ($this->old_name!="" and $this->old_name!=$this->name) {
            $shares = $this->getNfsSharesFromFile();
            $result = array();
            foreach ($shares as $name=>$share) {
                $result[$name] = $share;
                if (isset($share["hosts"][$this->old_name])) {
                    $result[$name]["hosts"][$this->name] = $result[$name]["hosts"][$this->old_name];
                    unset($result[$name]["hosts"][$this->old_name]);
                }
            }
            $this->saveNfsSharesToFile($result);
        }
        
        if ($this->fixed_address!="" and $this->old_fixed_address!=$this->fixed_address) {
            $shares = $this->getAfpSharesFromFile();
            $result = array();
            foreach ($shares as $name=>$share) {
                $result[$name] = $share;
                if (isset($share["hosts"][$this->old_fixed_address])) {
                    $result[$name]["hosts"][$this->fixed_address] = $result[$name]["hosts"][$this->old_fixed_address];
                    unset($result[$name]["hosts"][$this->old_fixed_address]);
                }
            }
            $this->saveAfpSharesToFile($result);
        }
        
        $shell = $Objects->get("Shell_shell");
        if ($this->old_name!=$this->name) {
            if ($this->old_name!="") {
                
                if (file_exists($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->old_name).".conf")) {
                    unlink($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->old_name).".conf");
                    unlink($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->old_name)."_guest.conf");
                }
                else {
                    $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name).".conf","w");
                    fwrite($fp," ");
                    fclose($fp);
                    $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name)."_guest.conf","w");
                    fwrite($fp," ");
                    fclose($fp);
                }
                if (file_exists($app->remotePath.$this->fileServer->smbCustomHostsPath."/".strtolower($this->old_name).".conf")) {
                    if ($app->remoteSSHCommand!="")
                        shell_exec($app->remoteSSHCommand.' "mv '.$this->fileServer->smbCustomHostsPath."/".strtolower($this->old_name).".conf"." ".$this->fileServer->smbCustomHostsPath."/".strtolower($this->name).".conf\"");
                    else
                        $shell->exec_command('mv '.$this->fileServer->smbCustomHostsPath."/".strtolower($this->old_name).".conf"." ".$this->fileServer->smbCustomHostsPath."/".strtolower($this->name).".conf");
                }
                else {
                    $fp = fopen($app->remotePath.$this->fileServer->smbCustomHostsPath."/".strtolower($this->name).".conf","w");
                    fwrite($fp," ");
                    fclose($fp);
                }
            }
        }

        if (!file_exists($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name).".conf")) {
            $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name).".conf","w");
            fwrite($fp," ");
            fclose($fp);
        }
        if (!file_exists($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name)."_guest.conf")) {
            $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name)."_guest.conf","w");
            fwrite($fp," ");
            fclose($fp);
        }
        if (!file_exists($app->remotePath.$this->fileServer->smbCustomHostsPath."/".strtolower($this->name).".conf")) {
            $fp = fopen($app->remotePath.$this->fileServer->smbCustomHostsPath."/".strtolower($this->name).".conf","w");
            fwrite($fp," ");
            fclose($fp);
        }

        $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name).".conf","w");
        $strings = file($this->fileServer->smbShareTemplateFile);
        $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name).".conf","w");
        fwrite($fp,"include = ".$this->fileServer->smbCustomHostsPath."/%m.conf\n");
        foreach($this->accessRules as $key=>$rule) {
			$rule = (array)$rule;
            if (isset($this->smbShares[$key])) {
                foreach ($strings as $line) {
                    fwrite($fp,strtr($line,array("{name}" => $key, "{path}" => $rule["path"], "{read_only}" => $rule["read_only"])));
                }
            }
        }
        fclose($fp);
        $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name)."_guest.conf","w");
        if ($this->defaultUser!=0)
        	fwrite($fp,"force user = ".$this->userList[$this->defaultUser]."\n");
        else
        	fwrite($fp,"");
		fclose($fp);
		        
        $shares = $this->getNfsSharesFromFile();
        $result = array();
        foreach ($shares as $name=>$share) {
            if (isset($this->nfsShares[$name]) and isset($this->accessRules[$name])) {
                $rule = (array)$this->accessRules[$name];
                if (isset($share["hosts"][$this->name])) {
                    unset($share["hosts"][$this->name]["ro"]);
                    unset($share["hosts"][$this->name]["rw"]);
                    
                    if ($rule["read_only"]=="yes")
                        $share["hosts"][$this->name]["ro"] = "ro";
                    else
                        $share["hosts"][$this->name]["rw"] = "rw";                                            
                } else {
                    if ($rule["read_only"]=="yes")                    
                        $share["hosts"][$this->name] = array("ro"=>"ro");
                    else
                        $share["hosts"][$this->name] = array("rw"=>"rw");
                }
                if ($this->defaultUser!=0)
                    $share["hosts"][$this->name]["anonuid=".$this->defaultUser] = "anonuid=".$this->defaultUser;
                else
                    unset($share["hosts"][$this->name]["anonuid=".$this->old_defaultUser]);
                unset($this->nfsShares[$name]);
                $result[$name] = $share;
            } else {
                if (isset($share["hosts"][$this->name])) {
                    unset($share["hosts"][$this->name]);
                    if (count($share["hosts"])>0) {
                        $result[$name] = $share;
                    }
                } else {
                    $result[$name] = $share;
                }                
            }
        }
        
        foreach ($this->nfsShares as $key=>$value) {
            $ac = $this->accessRules;
            if (!isset($ac[$key]))
                continue;
            $rule = (array)$this->accessRules[$key];
            $share = array();
            if (!isset($rule["path"]))
                continue;
            $share["path"] = $rule["path"];
            if ($rule["read_only"]=="yes")
                $share["hosts"][$this->name] = array("ro"=>"ro");
            else
                $share["hosts"][$this->name] = array("rw"=>"rw");
            $share["options"] = "-".$this->fileServer->nfsDefaultOptions;
            $result[$key] = $share;
        }    
        $this->saveNfsSharesToFile($result);

        $shares = $this->getAfpSharesFromFile();
        $result = array();
        foreach ($shares as $name=>$share) {
        	if (isset($this->afpShares[$name]) and isset($this->accessRules[$name])) {
        		$rule = (array)$this->accessRules[$name];
        		if (!isset($share["hosts"][$this->fixed_address])) {
        			$share["hosts"][$this->fixed_address] = "rw";        			 
        		}
        		$result[$name] = $share;
        	} else {
        		if (isset($share["hosts"][$this->fixed_address])) {
        			unset($share["hosts"][$this->fixed_address]);
        			if (count($share["hosts"])>0) {
        				$result[$name] = $share;
        			}
        		} else {
        			$result[$name] = $share;
        		}
        	}
        }
        foreach ($this->afpShares as $key=>$value) {
        	$ac = $this->accessRules;
        	if (!isset($ac[$key]))
        		continue;
        	$rule = (array)$this->accessRules[$key];
        	if (!isset($result[$key]))
        		$share = array();
        	else
        		$share = $result[$key];
        	if (!isset($rule["path"]))
        		continue;
        	$share["path"] = $rule["path"];
      		$share["hosts"][$this->fixed_address] = "rw";
        	$result[$key] = $share;
        }
        $this->saveAfpSharesToFile($result);
        
        if ($this->name != $this->old_name or $this->fixed_address != $this->old_fixed_address or $this->old_subnet_name != $this->subnet_name) {
            $capp = $Objects->get($this->module_id);
        	if (is_object($capp->docFlowApp)) {
        		$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
        		if (!$adapter->connected)
        			$adapter->connect();
        		if ($adapter->connected) {
        			$sql = "UPDATE fields SET value='DhcpHost_".$this->module_id."_".$this->subnet_name."_".$this->name."' WHERE name='objectId' AND value='DhcpHost_".$this->module_id."_".$this->old_subnet_name."_".$this->old_name."' AND classname='ReferenceDhcpHostInfoCard'";
        			$stmt = $adapter->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
        			$stmt->execute();
        		}
        	}        	 
            $this->dhcpServer->updateDNSZones();
        }

        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
        	$gapp->initModules();
        if ($this->old_name=="")
        	$gapp->raiseRemoteEvent("DHCPHOST_ADDED","object_id=".$this->getId());
        else
        	$gapp->raiseRemoteEvent("DHCPHOST_CHANGED","object_id=".$this->getId());        	
        $this->old_name = $this->name;
        $this->loaded = true;
        if (!$quick) {
            $app = $Objects->get($this->module_id);
            if ($app->remoteSSHCommand=="") {
                if ($this->fileServer->smbAutoRestart) {
                    $shell->exec_command($this->fileServer->smbReloadCommand);
                    $shell->exec_command($this->fileServer->nfsRestartCommand);
                    $shell->exec_command($this->fileServer->afpRestartCommand);
                }
                if ($this->dhcpServer->netCenterAutoRestart) {
                    $shell->exec_command($this->dhcpServer->dhcpRestartCommand);
                    $shell->exec_command($this->dhcpServer->bindRestartCommand);
                }
            } else
            {
                $cmd = " \"";
                if ($this->fileServer->smbAutoRestart)
                    $cmd .= $this->fileServer->smbReloadCommand.";".$this->fileServer->nfsRestartCommand.";".$this->fileServer->afpRestartCommand;
                if ($this->dhcpServer->netCenterAutoRestart)
                    $cmd .= $this->dhcpServer->dhcpRestartCommand.";".$this->dhcpServer->bindRestartCommand;
                $cmd .= "\"";
                shell_exec($app->remoteSSHCommand." 'echo ".$cmd." | at now'");
            }
        }
        $this->loaded = true;
        if ($saveFirewallFile)
            $this->fileServer->saveFirewallFile();
        ldap_unbind($ds);
    }
    
    function getNfsSharesFromFile() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        if (!isset($this->fileServer))
                $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (file_exists($app->remotePath.$this->fileServer->nfsConfigFile)) {
            $strings = file($app->remotePath.$this->fileServer->nfsConfigFile);
            $shares = array();
            foreach ($strings as $line) {
                $line=trim($line);                
                if ($line[0]!="#" and $line!="") {
                    $line_parts = explode("#",$line);
                    $shareName = trim($line_parts[1]);
                    $line = $line_parts[0];
                    $line_parts = explode(" ",$line);
                    $path = array_shift($line_parts);
                    $hosts = array();
                    foreach($line_parts as $part) {
                        $part = trim($part);
                        if ($part=="")
                            continue;
                        if ($part{0}=="-") {
                            $shares[$shareName]["options"] = $part;
                            continue;
                        }
                        if ($part!="") {                            
                            $matches = array();
                            if (preg_match("~^(\S+)\((\S+)\)~",$part,$matches)) {
                                $hosts[$matches[1]] = array_flip(explode(",",$matches[2]));
                            }
                        }
                    }
                    $shares[$shareName]["path"] = $path;
                    $shares[$shareName]["hosts"] = $hosts;
                }
            }
            return $shares;
        }
        return 0;
    }
    
    function getAfpSharesFromFile() {
    	global $Objects;
    	$app = $Objects->get($this->module_id);
    	$shares = array();
    	if (file_exists($app->remotePath.$app->afpSharesFile)) {
    		$strings = file($app->remotePath.$app->afpSharesFile);
    		foreach ($strings as $line) {
    			$matches = array();
    			if (preg_match("/(.*) (.*) cnidscheme\:dbd allowed_hosts\:(.*)/",$line,$matches)) {
    				$shares[trim($matches[2])]["path"] = trim($matches[1]);
    				$shares[trim($matches[2])]["hosts"] = array_flip(explode(",",trim($matches[3])));
    			}
    		}
    	}
    	return $shares;
    }
    
    function saveNfsSharesToFile($shares) {
        global $Objects;
        $app = $Objects->get($this->module_id);    
        if (!isset($this->fileServer))
                $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (file_exists($app->remotePath.$this->fileServer->nfsConfigFile)) {
            $fp = fopen($app->remotePath.$this->fileServer->nfsConfigFile,"w");
            foreach ($shares as $key=>$share) {
                $hosts_string = "";
                foreach ($share["hosts"] as $host=>$options) {
                    $hosts_string .= $host."(".implode(",",array_flip($options)).") ";
                }
                fwrite($fp,$share["path"]." ".$share["options"]." ".$hosts_string."# ".$key."\n");
            }
        }
        fclose($fp);
    }
    
    function saveAfpSharesToFile($shares) {
    	global $Objects;
    	$app = $Objects->get($this->module_id);
    	$fp = fopen($app->remotePath.$app->afpSharesFile,"w");
    	fwrite($fp,":DEFAULT: options:".$app->afpDefaultOptions."\n");
    	foreach($shares as $key=>$value) {    		
    		if (is_array($value["hosts"]) and count($value["hosts"])>0)
    			fwrite($fp,$value["path"]." ".$key." cnidscheme:dbd allowed_hosts:".@implode(",",@array_keys($value["hosts"]))."\n");
    	}
    	fclose($fp);
    }

    function load() {
        global $Objects;
        $this->dhcpServer = $Objects->get("DhcpServer_".$this->module_id."_Network");
        if (!$this->dhcpServer->loaded)
            $this->dhcpServer->load();

        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        $ldap_service_dn = $this->dhcpServer->getServiceDN();
        $subnet = $Objects->get("DhcpSubnet_".$this->module_id."_".$this->old_subnet_name);
        $subnet_dn = $subnet->getDN();

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
        
        if (!$this->fileServer->users_loaded)
            $this->fileServer->loadUsers(false);
        $this->userList = array();
        foreach($this->fileServer->users as $user)
            $this->userList[$user->uid] = $user->name;
        
        if ($this->old_name!="") {
            $result = @ldap_list($ds,$subnet_dn,"cn=".$this->old_name);
            if ($result==FALSE)
                return 0;
            $entries = ldap_get_entries($ds, $result);
            if ($entries["count"]==0)
                return 0;
        } else return 0;
		
        $value = ldap_first_entry($ds, $result);
        $values = @ldap_get_values($ds,$value,"comment");
        $this->title = $values[0];
        $this->old_title = $values[0];
        $values = @ldap_get_values($ds,$value,"hostType");
        $this->host_type = $values[0];
        $values = @ldap_get_values($ds,$value,"descr");
        $this->descr = str_replace("|","\n",$values[0]);
        $values = @ldap_get_values($ds,$value,"dhcpHWAddress");
        $this->hw_address = str_replace("ethernet ","",$values[0]);
        $values = @ldap_get_values($ds,$value,"objectGroup");
        $this->objectGroup = $values[0];

        $values = @ldap_get_values($ds,$value,"inRate");
        $arr = @explode(" ",$values[0]);$this->inRate = @$arr[0];$this->inRateDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"outRate");
        $arr = @explode(" ",$values[0]);$this->outRate = @$arr[0];$this->outRateDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"inCeil");
        $arr = @explode(" ",$values[0]);$this->inCeil = @$arr[0];$this->inCeilDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"outCeil");
        $arr = @explode(" ",$values[0]);$this->outCeil = @$arr[0];$this->outCeilDim = @$arr[1];
        $values = @ldap_get_values($ds,$value,"dropInternet");
        $this->dropInternet = @$values[0];
        $values = @ldap_get_values($ds,$value,"openPorts");
        $this->openPorts = @$values[0];
        $values = @ldap_get_values($ds,$value,"remoteDesktopProtocol");
        $this->remoteDesktopProtocol = @$values[0];
        $values = @ldap_get_values($ds,$value,"remoteDesktopPort");
        $this->remoteDesktopPort = @$values[0];
        $values = @ldap_get_values($ds,$value,"remoteDesktopUser");
        $this->remoteDesktopUser = @$values[0];
        $values = @ldap_get_values($ds,$value,"remoteDesktopPassword");
        $this->remoteDesktopPassword = @$values[0];
        $values = @ldap_get_values($ds,$value,"remoteConsolePort");
        $this->remoteConsolePort = @$values[0];
        $values = @ldap_get_values($ds,$value,"webInterfaceProtocol");
        $this->webInterfaceProtocol = @$values[0];
        $values = @ldap_get_values($ds,$value,"webInterfacePort");
        $this->webInterfacePort = @$values[0];
        $values = @ldap_get_values($ds,$value,"webInterfaceNewWindow");
        $this->webInterfaceNewWindow = @$values[0];
        $values = @ldap_get_values($ds,$value,"defaultUser");
        $this->defaultUser = @$values[0];
        $values = @ldap_get_values($ds,$value,"denyFileAccess");
        $this->denyFileAccess = @$values[0];

        $this->old_descr = $this->descr;
        $this->old_title = $this->title;
        $this->old_hw_address = $this->hw_address;
        $this->old_remoteDesktopProtocol = $this->remoteDesktopProtocol;
        $this->old_remoteDesktopPort = $this->remoteDesktopPort;
        $this->old_remoteDesktopUser = $this->remoteDesktopUser;
        $this->old_remoteDesktopPassword = $this->remoteDesktopPassword;
        $this->old_remoteConsolePort = $this->remoteConsolePort;
        $this->old_webInterfaceProtocol = $this->webInterfaceProtocol;
        $this->old_webInterfacePort = $this->webInterfacePort;
        $this->old_webInterfaceNewWindow = $this->webInterfaceNewWindow;
        $this->old_defaultUser = $this->defaultUser;
        $this->old_denyFileAccess = $this->denyFileAccess;

        $values = @ldap_get_values($ds,$value,"dhcpOption");
        $custom_options = array();
        if (is_array($values)) {
            foreach($values as $val) {
                if (is_numeric($val))
                    continue;
                $orig_val = $val;
                $value_parts = explode(" ",$val);
                $val = str_replace($value_parts[0]." ","",$val);
                switch ($value_parts[0]) {
                    case "domain-name-servers":
                        $this->domain_name_servers = $val;
                        break;
                    case "netbios-name-servers":
                        $this->netbios_name_servers = $val;
                        break;
                    case "netbios-node-type":
                        $this->netbios_node_type = $val;
                        break;
                    case "interface-mtu":
                        $this->interface_mtu = $val;
                        break;
                    case "ip-forwarding":
                        $this->ip_forwarding = $val;
                        break;
                    case "subnet-mask":
                        $this->subnet_mask = $val;
                        break;
                    case "routers":
                        $this->routers = $val;
                        break;
                    case "root-path":
                        $this->root_path = str_replace('"','',$val);
                        break;
                    case "host-name":
                        $this->hostname = $val;
                        break;
                    default:
                        $custom_options[count($custom_options)] = "option ".$orig_val;
                }
            }
        }
        $values = ldap_get_values($ds,$value,"dhcpStatements");
        if (is_array($values)) {
            foreach($values as $val) {
                if (is_numeric($val))
                    continue;
                $orig_val = $val;
                $value_parts = explode(" ",$val);
                $val = str_replace($value_parts[0]." ","",$val);
                switch ($value_parts[0]) {
                    case "filename":
                        $this->filename = str_replace('"','',$val);
                        break;
                    case "next-server":
                        $this->next_server = $val;
                        break;
                    case "fixed-address":
                        $this->fixed_address = $val;
                        $this->old_fixed_address = $val;
                        break;
                    case "allow":
                        $this->allow_booting = "allow booting";
                        break;
                    case "deny":
                        $this->allow_booting = "deny booting";
                        break;
                    default:
                        $custom_options[count($custom_options)] = $orig_val;
                }
            }
        }
        
        $this->getAccessRules();
        
        $this->custom_options = implode("\n",$custom_options);
        $this->loaded = true;
        $this->old_name = $this->name;        
        ldap_unbind($ds);
        $this->setIcon();
        $app = $Objects->get($this->module_id);
        if ($this->loaded and is_object($app->docFlowApp)) {
        	$this->adapter = $Objects->get("DocFlowDataAdapter_".$app->docFlowApp->getId()."_1");
        	$query = "SELECT entities FROM fields WHERE @objectId='".$this->getId()."' AND @classname='ReferenceDhcpHostInfoCard'";
        	$res = PDODataAdapter::makeQuery($query, $this->adapter,$app->docFlowApp->getId());
        	if (count($res)>0) {
        		$card = current($res);
        		$this->referenceCode = "<input type='button' fileid='".$card->getId()."' id='referenceButton' value='Открыть описание'/>";
        	} else {
        		$this->referenceCode = "<input type='button' fileid='ReferenceDhcpHostInfoCard_".$app->docFlowApp->getId()."_' id='referenceButton' value='Открыть описание'/>";
        	}
        }        
    }

    function getAccessRules() {
        global $Objects;
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        $app = $Objects->get($this->module_id);
        if (file_exists($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name).".conf")) {
            $strings = file($app->remotePath.$this->fileServer->smbHostsPath."/".strtolower($this->name).".conf");
            $current_name = "";
            foreach($strings as $line) {
                $matches = array();
                if (preg_match('/\[(.*)\]/',$line,$matches)==1) {
                    $current_name = $matches[1];
                    $this->smbShares[$current_name] = "yes";
                }
                if (preg_match('/path = (.*)/',$line,$matches)==1)
                    $this->accessRules[$current_name]["path"] = $matches[1];
                if (preg_match('/read only = (.*)/',$line,$matches)==1)
                    $this->accessRules[$current_name]["read_only"] = $matches[1];                
            }
            $shares = $this->getNfsSharesFromFile();           
            if (count($shares)>0)
            foreach ($shares as $name=>$share) {
                if (isset($share["hosts"][$this->name])) {
                    $this->nfsShares[$name] = "yes";
                    if (!isset($this->smbShares[$name])) {                        
                        $this->accessRules[$name]["path"] = $share["path"];
                        $arr = $share["hosts"][$this->name];
                        if (isset($arr["ro"]))
                            $this->accessRules[$name]["read_only"] = "yes";
                        else
                            $this->accessRules[$name]["read_only"] = "no";
                    }
                }
            }
            $shares = $this->getAfpSharesFromFile();           
            if (count($shares)>0)
            foreach ($shares as $name=>$share) {
                if (isset($share["hosts"][$this->fixed_address])) {
                    $this->afpShares[$name] = "yes";
                    if (!isset($this->smbShares[$name]) and !isset($this->nfsShares[$name])) {                        
                        $this->accessRules[$name]["path"] = $share["path"];
                        $this->accessRules[$name]["read_only"] = "no";
                    }
                }
            }
        }
    }	
		
    function getId() {
        return "DhcpHost_".$this->module_id."_".$this->subnet_name."_".$this->name;
    }

    function getDN() {
      if (!$this->loaded)
        $this->load();
      $subnet = $Objects->get("DhcpSubnet_".$this->module_id."_".$this->old_subnet_name);
      $subnet_dn = $subnet->getDN();
      return "cn=".$this->name.",".$subnet_dn;
    }

    function getArgs() {
        $result = parent::getArgs();
        $range_array = explode(" ",$this->range);
        
        if ($this->allow_booting=="allow booting")
            $result["{allow_booting_checked}"] = "checked";
        else
            $result["{allow_booting_checked}"] = "";
        if ($this->ip_forwarding=="true")
            $result["{ip_forwarding_checked}"] = "checked";
        else
            $result["{ip_forwarding_checked}"] = "";

        global $Objects;
        
        $subnets = $Objects->get("DhcpSubnets_".$this->module_id."_nets");
        $subnets->load();
        $subnets_array = array();
        foreach ($subnets->subnets as $subnet) {
            $subnets_array[$subnet->name] = $subnet->title." (".$subnet->name.")";
        }
        $result["{subnets_collection}"] = implode(",",array_keys($subnets_array))."|".implode(",",array_values($subnets_array));
        $result["{host_types_collection}"] = implode(",",array_keys($this->host_types))."|".implode(",",array_values($this->host_types));
        $result["{host_types_ids}"] = implode(",",array_keys($this->host_types));
        $result["{host_types_icons}"] = implode(",",array_values($this->host_types_icons));

        $result["{netbios_node_type_collection}"] = implode(",",array_keys($this->netbios_node_types))."|".implode(",",array_values($this->netbios_node_types));
        $result["{access_rules_table}"] = "HostAccessRulesTable_".$this->module_id."_".$this->subnet_name."_".$this->name;
                        
        $result["{users_list}"] = "0,".implode(",",array_keys($this->userList))."| ,".implode(",",array_values($this->userList));
        $args = array();
        $args["window_id"] = $this->window_id;
        $args["opener_item"] = $this->getId();
        $this->args = urlencode(json_encode($args));
        $result["{args}"] = $this->args;
        $result["{|referenceCode}"] = $this->referenceCode;
        return $result;        
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->name;
    }

    function scanHost($ip) {
		if (is_array($ip))
			$ip = $ip["fixed_address"];
        global $Objects;
        $shell = $Objects->get("Shell_Helix");
        $app = $Objects->get($this->module_id);
        echo $shell->exec_command($app->remoteSSHCommand." ".$app->nmapCommand." -A ".$ip);
    }    
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "getWebInterfaceOptions";
			case '4': return "saveHook";
			case '5': return "scanHost";
		}
		return parent::getHookProc($number);
	}
	
	function getWebInterfaceOptions($arguments) {
		$object->load();
		echo $object->webInterfaceProtocol.' '.$object->webInterfacePort.' '.$object->fixed_address.' '.$object->webInterfaceNewWindow;
	}
		
	function saveHook($arguments) {
		$this->load();
		$this->setArguments($arguments);
		$this->accessRules = (array)$arguments["accessRules"];
		$this->smbShares = (array)$arguments["smbShares"];
		$this->nfsShares = (array)$arguments["nfsShares"];
		$this->afpShares = (array)$arguments["afpShares"];
		$this->save();
	}
}
?>