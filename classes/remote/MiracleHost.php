<?php
/**
 * Класс описывает модуль управления включением сервера. С помощью этого модуля
 * можно отправить сигнал wakeonlan на определенный сервер и подключиться в режиме
 * удаленного рабочего стола или панели управления к виртуальным серверам, которые
 * на этом сервере находятся.
 *
 * Свои настройки модуль читает из конфигурационного файла пользователя, от имени
 * которого пользователь подключается. Настройки эти следующие:
 *
 * MACAddress - MAC-адрес сервера, на который отправляется команда включения.
 * HostIPAddress - IP-адрес сервера, который включается
 * DefaultServer - Виртуальный сервер, используемый по умолчанию
 * BootAfterPower - Подключаться ли к виртуальному серверу по умолчанию в режиме
 *                  удаленного рабочего стола сразу после включения питания.
 * RDesktopCommand - Команда, запускающая клиент удаленного рабочего стола.
 *
 * Также эта команда управляет массивом virtualServers, каждый из элементов
 * которого является виртуальным сервером, к которому может подключаться данный
 * хост.
 *
 * @author andrey
 */
class MiracleHost extends WABEntity {

    public $virtual_servers = array();

    function construct($params) {
        $this->module_id = "MiracleHost_".$params[0];

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
                $app->initModules();
        $this->template = "templates/remote/MiracleHost.html";
        $this->skinPath = $app->skinPath;
        $this->css = $app->skinPath."styles/MiracleHost.css";
        $this->handler = "scripts/handlers/remote/MiracleHost.js";

        $this->network_style = $app->config->networkSettingsStyle;
        if ($this->network_style=="RedHat") {
            $this->network_config_file = $app->config->redHatNetworkSettingsFile;
            $this->restart_command = $app->config->redHatNetworkRestartCommand;
        }
        else {
            $this->network_config_file = $app->config->debianNetworkSettingsFile;
            $this->network_config_template_file = $app->config->debianNetworkSettingsTemplateFile;
            $this->restart_command = $app->config->debianNetworkRestartCommand;
        }
        $this->clientClass = "MiracleHost";
        $this->parentClientClasses = "Entity";        
	    $this->classTitle = "Модуль удаленного администрирования сервера";
	    $this->classListTitle = "Модуль удаленного администрирования сервера";
        $this->load();
    }

    function load() {

        global $Objects;
        
        if ($this->network_style == "RedHat") {
            if (file_exists($this->network_config_file)) {
                $network_settings = file($this->network_config_file);
                for ($counter=0;$counter<count($network_settings);$counter++) {
                    $parts = explode("=",$network_settings[$counter]);
                    $this->fields[trim(strtolower($parts[0]))] = $parts[1];
                }
            }
        } else {
            if (file_exists($this->network_config_file)) {
                $strings = file($this->network_config_file);
                foreach ($strings as $line) {
                    if (preg_match('/address (.*)/',$line,$matches)==1)
                        $this->managementModuleIP = trim($matches[1]);
                    if (preg_match('/netmask (.*)/',$line,$matches)==1)
                        $this->managementModuleNetmask = trim($matches[1]);
                    if (preg_match('/gateway (.*)/',$line,$matches)==1)
                        $this->managementModuleGateway = trim($matches[1]);
                    if (preg_match('/dns-nameservers (.*)/',$line,$matches)==1) {
                        $this->managementModuleDNSServers = trim($matches[1]);
                    }
                }
            }
        }

        $config = $Objects->get("AdminConfig_".$_SERVER["PHP_AUTH_USER"]);
        $sysconfig = $config;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if (isset($config->modules[$this->module_id])) {
            $defaultConfig = $config->defaultModules[$this->module_id];
            $config = $config->modules[$this->module_id];

            $arr = array();
            $arr[0] = $defaultConfig;
            $arr[1] = $config;
            foreach($arr as $config) {
                    if ($config != null) {
                        if ($config->getElementsByTagName("HostIpAddress")->item(0) != null)
                            $this->serverIP = $config->getElementsByTagName("HostIpAddress")->item(0)->getAttribute("value");
                        if ($config->getElementsByTagName("MACAddress")->item(0) != null)
                            $this->serverMAC = $config->getElementsByTagName("MACAddress")->item(0)->getAttribute("value");
                        if ($config->getElementsByTagName("DefaultVirtualServer")->item(0) != null)
                            $this->defaultServer = $config->getElementsByTagName("DefaultVirtualServer")->item(0)->getAttribute("value");
                        if ($config->getElementsByTagName("BootAfterPower")->item(0) != null)
                            $this->bootAfterPower = $config->getElementsByTagName("BootAfterPower")->item(0)->getAttribute("value");
                        if ($config->getElementsByTagName("RDesktopCommand")->item(0) != null)
                            $this->rdesktopCommand = $config->getElementsByTagName("RDesktopCommand")->item(0)->getAttribute("value");
                        if ($config->getElementsByTagName("RootPassword")->item(0) != null)
                            $this->rootPassword = $config->getElementsByTagName("RootPassword")->item(0)->getAttribute("value");

                        if ($config->getElementsByTagName("VirtualServers")->item(0) != null)
                            $vservers = $config->getElementsByTagName("VirtualServers")->item(0)->getElementsByTagName("VirtualServer");
                        if (isset($vservers)) {
                            foreach($vservers as $vserver) {
                                $name = $vserver->getAttribute("name");
                                $this->virtual_servers[$name] = array();
                                $this->virtual_servers[$name]["title"] = $vserver->getAttribute("title");
                                if ($vserver->getElementsByTagName("IpAddress")->item(0) != null)
                                    $this->virtual_servers[$name]["ip_address"] = $vserver->getElementsByTagName("IpAddress")->item(0)->getAttribute("value");
                                if ($vserver->getElementsByTagName("RDesktopPort")->item(0) != null)
                                    $this->virtual_servers[$name]["rdesktop_port"] = $vserver->getElementsByTagName("RDesktopPort")->item(0)->getAttribute("value");
                                if ($vserver->getElementsByTagName("ControlPanelURL")->item(0) != null)
                                    $this->virtual_servers[$name]["controlpanel_url"] = $vserver->getElementsByTagName("ControlPanelURL")->item(0)->getAttribute("value");
                                if ($vserver->getElementsByTagName("PowerOnCommand")->item(0) != null)
                                    $this->virtual_servers[$name]["poweron_command"] = str_replace("`","'",str_replace("``",'"',$vserver->getElementsByTagName("PowerOnCommand")->item(0)->getAttribute("value")));
                                if ($vserver->getElementsByTagName("PowerOffCommand")->item(0) != null)
                                    $this->virtual_servers[$name]["poweroff_command"] = str_replace("`","'",str_replace("``",'"',$vserver->getElementsByTagName("PowerOffCommand")->item(0)->getAttribute("value")));
                            }
                        }
                    }
                }
            }
        $this->loaded = true;        
    }

    function save($arguments=null) {
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);        	
            $this->virtual_servers = (array)$this->virtual_servers;
            foreach ($this->virtual_servers as $key=>$value)
            	$this->virtual_servers[$key] = (array)$this->virtual_servers[$key];
        }
        $shell = $Objects->get("Shell_shell");
        if (file_exists($this->network_config_template_file)) {
            $strings = file($this->network_config_template_file);
            $fp = fopen($this->network_config_file,"w");
            $args = $this->getArgs();
            foreach ($strings as $string) {
                fwrite($fp,strtr($string,$args));
            }
        }
        $shell->exec_command($this->restart_command);
        fclose($fp);
        $config = new DOMDocument();
        $config->load("/etc/WAB2/config/".$_SERVER["PHP_AUTH_USER"].".conf");
        $config->getElementsByTagName("HostIpAddress")->item(0)->setAttribute("value",$this->serverIP);
        $config->getElementsByTagName("MACAddress")->item(0)->setAttribute("value",$this->serverMAC);
        $config->getElementsByTagName("RDesktopCommand")->item(0)->setAttribute("value",$this->rdesktopCommand);
        $config->getElementsByTagName("RootPassword")->item(0)->setAttribute("value",$this->rootPassword);
        $vservers = $config->getElementsByTagName("VirtualServers")->item(0)->getElementsByTagName("VirtualServer");
        foreach($vservers as $vserver) {
            $vserver->getElementsByTagName("IpAddress")->item(0)->setAttribute("value",$this->virtual_servers[$vserver->getAttribute("name")]["ip_address"]);
            $vserver->getElementsByTagName("RDesktopPort")->item(0)->setAttribute("value",$this->virtual_servers[$vserver->getAttribute("name")]["rdesktop_port"]);
            $vserver->getElementsByTagName("ControlPanelURL")->item(0)->setAttribute("value",$this->virtual_servers[$vserver->getAttribute("name")]["controlpanel_url"]);
        }
        $config->save("/etc/WAB2/config/".$_SERVER["PHP_AUTH_USER"].".conf");
    }

    function getId() {
        return $this->module_id;
    }

    function getServerStatus($arguments=null) {
        $result = array();
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $shell = $Objects->get("Shell_shell");
        $res = trim($shell->exec_command(strtr($app->pingPortTestCommand,array("{address}"=>$this->serverIP,"{port}"=>"22"))));
        if ($res=="0")
            $result[count($result)] = "on";
        else
            $result[count($result)] = "off";
        foreach($this->virtual_servers as $key=>$value) {
            $status = "off";
            $res = trim($shell->exec_command(strtr($app->pingPortTestCommand,array("{address}"=>$this->serverIP,"{port}"=>$value["rdesktop_port"]))));
            if ($res=="0") {
                $status = "loading";
            }
            if ($status!="off") {
                $res = trim($shell->exec_command(strtr($app->pingPortTestCommand,array("{address}"=>$value["ip_address"],"{port}"=>"443"))));
                if ($res=="0") {
                    $status = "on";
                }
            }
            $result[count($result)] = $key."~".$status;
        }
        return implode("|",$result);
    }

    function pressPowerButton($serverName,$operation,$shutdown_all='false') {
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if ($shutdown_all=="true") {
            foreach ($this->virtual_servers as $server) {
                $shell->exec_command(strtr($server["poweroff_command"],array("{server_ip}"=>$this->serverIP,"{password}"=>$this->rootPassword)));
            }
            return 0;
        }
        if ($operation=="on")
            if ($serverName=="rootHost")
                $shell->exec_command("wakeonlan ".$this->serverMAC);
            else
                $shell->exec_command(strtr($this->virtual_servers[$serverName]["poweron_command"],array("{server_ip}"=>$this->serverIP,"{password}"=>$this->rootPassword)));
        else {
            if ($serverName=="rootHost")
                $shell->exec_command(strtr($app->sshRemoteCommand,array("{server_string}"=>"root@".$this->serverIP,"{command}"=>"poweroff")));
            else
                $shell->exec_command(strtr($this->virtual_servers[$serverName]["poweroff_command"],array("{server_ip}"=>$this->serverIP,"{password}"=>$this->rootPassword)));
        }
    }

    function pressDisplayButton($serverName) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $shell = $Objects->get("Shell_shell");
        $fp = fopen("rdesktop.sh","w");
        fwrite($fp,$this->rdesktopCommand." ".$this->serverIP.":".$this->virtual_servers[$serverName]["rdesktop_port"]);
        fclose($fp);
        return "https://".$_SERVER["SERVER_ADDR"]."/rdesktop.sh";
    }

    function getArgs() {
        $result = parent::getArgs();
        if ($this->bootAfterPower=="true")
            $result["{bootAfterPowerChecked}"] = "checked";
        else
            $result["{bootAfterPowerChecked}"] = "";
        $res = "mhost.virtual_servers = new Array;\n";
        foreach($this->virtual_servers as $key=>$value) {
            $res .= "mhost.virtual_servers['".$key."']= new Array;\n";
            $res .= "mhost.virtual_servers['".$key."']['title']= '".$value["title"]."';\n";
            $res .= "mhost.virtual_servers['".$key."']['ip_address']= '".$value["ip_address"]."';\n";
            $res .= "mhost.virtual_servers['".$key."']['rdesktop_port']= '".$value["rdesktop_port"]."';\n";
            $res .= "mhost.virtual_servers['".$key."']['controlpanel_url']= '".$value["controlpanel_url"]."';\n";
            $res .= "mhost.virtual_servers['".$key."']['poweron_command']= '".str_replace("'","\'",$value["poweron_command"])."';\n";
            $res .= "mhost.virtual_servers['".$key."']['poweroff_command']= '".str_replace("'","\'",$value["poweroff_command"])."';\n";
            $res .= "mhost.virtual_servers['".$key."']['poweron_command']= mhost.virtual_servers['".$key."']['poweron_command'].replace(/\'/g,\"'\");";
            $res .= "mhost.virtual_servers['".$key."']['poweroff_command']= mhost.virtual_servers['".$key."']['poweroff_command'].replace(/\'/g,\"'\");";
        }
        $result["{virtual_servers}"] = $res;
        $result["{ipaddr}"] = $this->managementModuleIP;
        $result["{netmask}"] = $this->managementModuleNetmask;
        $result["{gateway}"] = $this->managementModuleGateway;
        $result["{dns}"] = "dns-nameservers ".$this->managementModuleDNSServers;
        return $result;
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    		case '4': return "pressDisplayButtonHook";
    		case '5': return "pressPowerButtonHook";
    		case '6': return "getServerStatusHook";
    	}
    }
    
    function pressDisplayButtonHook($arguments) {
    	$this->load();
    	echo $this->pressDisplayButton($arguments["serverName"]);
    }
    
    function pressPowerButtonHook($arguments) {
    	$this->load();
    	echo $this->pressPowerButton($arguments["serverName"],$arguments["operation"],$arguments["shutdown"]);
    }
    
    function getServerStatusHook($arguments) {
    	$this->load();
    	echo $this->getServerStatus();
    }
}
?>