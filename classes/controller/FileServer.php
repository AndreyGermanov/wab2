<?php
/**
 * Класс реализует файловый сервер. Он хранит настройки файлового сервера и
 * позволяет с ними работать.
 *
 * Файловый сервер хранит информацию о подключении к серверу LDAP, на котором
 * хранится информация об общих файловых ресурсах (экземпляры класса FileShare).
 * Информация об общих ресурсах хранится как дочерние элементы DN-а, записанного в
 * shares_base.
 *
 * Общие ресурсы должны храниться внутри папки shares_root, пути к общим ресурсам
 * всегда указываются относительно этой папки. Сама эта папка также является общим
 * ресурсом с именем "root". Это имя ресурса считается системным, невозможно
 * создавать общие ресурсы с таким именем.
 *
 * Эта информация включает в себя (как и в классе DhcpServer) ldap_host, ldap_port,
 * ldap_base, ldap_user, ldap_password. Физически вся эта информация хранится в
 * файле /etc/samba/smb.conf. Этот файл каждый раз пересоздается из шаблона
 * templates/controller/smb.conf.
 *
 * Также этот класс управляет коллекцией общих папок сервера, который находится в
 * динамическом массиве shares. Это коллекция объектов FileShare, которая динамически
 * берется из общего стэка $Objects.
 *
 * Для своей работы класс использует следующие методы:
 *
 * load() - загружает информацию о файловом сервере из /etc/samba/smb.conf
 * save() - сохраняет информацию о файловом сервере в файл /etc/samba/smb.conf,
 *          используя шаблон templates/controller/smb.conf
 * loadShares() - загружает коллекцию общих папок
 * saveShares() - сохраняет коллекцию общих папок
 * addShare() - создает новый общий ресурс
 * deleteShare() - удаляет указанный общий ресурс
 * contains() - проверяет, существует ли общий ресурс с указанным именем
 * getId() - возвращает идентификатор объекта
 * getDN() - возвращает DN файлового сервера. Полный DN формируется как shares_base + ldap_base.
 * getArgs() - возвращает массив параметров для подстановки в файл шаблона
 *
 * Класс содержит следующие поля:
 *
 * ldap_host - сервер LDAP
 * ldap_port - порт LDAP
 * ldap_user - пользователь LDAP
 * ldap_password - пароль LDAP
 * ldap_base - базовый домен, под которым будут храниться все учетные записи
 * shares_base - RDN в LDAP, под которым будет храниться информация об учетных записях
 * shares_root - путь к базовому каталогу на диске, в котором хранятся общие ресурсы
 *
 * smbConfigFile - путь к конфигурационному файлу Samba (берется из конфигурационного файла текущего пользователя)
 * smbTemplateConfigFile - путь к шаблону конфигурационного файла Samba (берется из конфигурационного файла текущего пользователя)
 *
 * module_id - идентификатор модуля
 * name - имя сервера
 * 
 * shares - динамическая коллекция общих ресурсов, включая ресурс "root"
 *
 * @author andrey
 */
class FileServer extends WABEntity {

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->template = "templates/controller/FileServer.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/FileServer.js";

        $this->icon = $app->skinPath."images/Tree/fileserver.png";
        $this->skinPath = $app->skinPath;
        

        $this->domain_controller = "#include /etc/samba/domain.conf";
        
        global $Objects;
        $app = $Objects->get($this->module_id);

        $this->ldap_host = $app->defaultLdapHost;
        $this->ldap_port = $app->defaultLdapPort;
        $this->ldap_user = $app->defaultLdapUser;
        if ($this->ldap_port=="636") {
        	$this->ldap_proto = "ldaps";
        	$this->ldap_use_ssl = "1";
        }
        else {
       		$this->ldap_proto = "ldap";
        	$this->ldap_use_ssl = "0";
        }
        $this->ldap_password = $app->defaultLdapPassword;
        $this->ldap_base = $app->defaultLdapBase;
        $this->shares_base = "shares";
        $this->shares_root = "/data/share";
		$this->initial_ldap_host = $this->ldap_host;
        if ($this->ldap_host=="localhost") {
        	if ($app->remoteAddress!="")
            	$this->ldap_host = $app->remoteAddress;
		}

       	foreach ($app->module as $key=>$value)
       		$this->fields[$key] = $value;
                        
        $this->workgroup = "";
        $app = $Objects->get("Application");
        $capp = $Objects->get($this->module_id);
        if (!$app->initiated)
            $app->initModules();
        $this->netbios_name = file_get_contents($capp->remotePath.$app->hostsFile);
        $this->hostname_command = $capp->remoteSSHCommand." ".$app->hostnameCommand;
        $this->netbios_name = array_shift(explode(".",$this->netbios_name));
        $this->ldap_machine_suffix = $capp->smbMachinesLdapBase;
        $this->ldap_user_suffix = $capp->smbUsersLdapBase;
        $this->ldap_group_suffix = $capp->smbGroupsLdapBase;
        $this->ldap_idmap_suffix = "";
        $this->crontabFile = $app->crontabFile;
        $this->rcLocalFile = $app->rcLocalFile;

        $this->width = "500";
        $this->height = "300";
        $this->overrided = "width,height";
        $this->system_total_space = round(disk_total_space("/")/1073741824,2);
        $this->system_free_space = round(disk_free_space("/")/1073741824,2);
        
        $this->models[] = "FileServer";
        
        if (file_exists("/data")) {
            $this->data_total_space = round(disk_total_space($capp->remotePath."/data")/1073741824,2);
            $this->data_free_space = round(disk_free_space($capp->remotePath."/data")/1073741824,2);
        }
        
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

        $this->loaded = false;
        $this->users_loaded = false;
        $this->groups_loaded = false;
        
        $this->clientClass = "FileServer";
        $this->parentClientClasses = "Entity";        
    }

    function getArgs() {

        $result = parent::getArgs();
        $result["{ldap_base_dn}"] = $this->ldap_base;
        $result["{ldap_user_dn}"] = $this->ldap_user;
        $result["{ldap_base}"] = str_replace("dc=","",$this->ldap_base);
        $result["{ldap_base}"] = str_replace(",",".",$result["{ldap_base}"]);
        $this->base_dn = $result["{ldap_base}"];
        $result["{ldap_user}"] = array_shift(explode(",",$this->ldap_user));
        $result["{ldap_user}"] = str_replace("cn=","",$result["{ldap_user}"]);
        $this->ldap_user_dn = $result["{ldap_user}"];
        $result["{ldap_host}"] = $this->initial_ldap_host;
        return $result;        
    }

    function save($arguments=null) {
    	
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        
		if ($this->domain_controller)
			$this->domain_controller = "include = /etc/samba/domain.conf";
		else
			$this->domain_controller = "#include = /etc/samba/domain.conf";
		
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get($this->module_id);
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
            $gapp->initModules();
        $shell = $Objects->get("Shell_shell");
                
        if (!$this->loaded)
            $this->load();
                        
        if ($this->shares_root=="") {
            $this->report_error("Не указана корневая папка !","save");
            return 0;
        }

        if (trim($this->smbAuditPeriod)==="") {
            $this->report_error("Не указан период хранения журнала доступа !","save");
            return 0;
        }
        
        $ds = @ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);

        $r = @ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }        

        $this->base_dn = $this->ldap_base;
        $this->ldap_user_dn = $this->ldap_user;
                
        // Если не существует корень для общих папок, создаем его
        $result = @ldap_list($ds,$this->base_dn,"(ou=".$this->shares_base.")");
        $entries = ldap_get_entries($ds,$result);
        $entry = array();
        if ($result == false or $entries["count"]==0) {
 	       $entry["ou"] = $this->shares_base;
           $entry["objectClass"][0] = "organizationalUnit";
           ldap_add($ds,"ou=".$this->shares_base.",".$this->base_dn,$entry);
        }

        // Если не существует корень для пользователей, создаем его
        $result = ldap_list($ds,$this->base_dn,"(".$this->ldap_user_suffix.")");
        $entries = ldap_get_entries($ds,$result);
        if ($entries["count"]==0 or $entries==FALSE) {
    	    $entry = array();
            $suffix_parts = explode("=",$this->ldap_user_suffix);
            $entry["ou"] = $suffix_parts[1];
            $entry["objectClass"][0] = "organizationalUnit";
            ldap_add($ds,$this->ldap_user_suffix.",".$this->base_dn,$entry);
        }

        // Если не существует корень для групп, создаем его
        $result = ldap_list($ds,$this->base_dn,"(".$this->ldap_group_suffix.")");
        $entries = ldap_get_entries($ds,$result);
        if ($entries["count"]==0 or $entries==FALSE) {
    	    $entry = array();
            $suffix_parts = explode("=",$this->ldap_group_suffix);
            $entry["ou"] = $suffix_parts[1];
            $entry["objectClass"][0] = "organizationalUnit";
            ldap_add($ds,$this->ldap_group_suffix.",".$this->base_dn,$entry);
        }

        // Если не существует корень для компьютеров, создаем его
        $result = ldap_list($ds,$this->base_dn,"(".$this->ldap_machine_suffix.")");
        $entries = ldap_get_entries($ds,$result);
        if ($entries["count"]==0 or $entries==FALSE) {
    	    $entry = array();
            $suffix_parts = explode("=",$this->ldap_machine_suffix);
            $entry["ou"] = $suffix_parts[1];
            $entry["objectClass"][0] = "organizationalUnit";
            ldap_add($ds,$this->ldap_machine_suffix.",".$this->base_dn,$entry);
        }

        // Если не существует корень для счетчика идентификаторов пользователей и групп, создаем его
        $entry = array();
        $result = ldap_list($ds,$this->base_dn,"(ou=NextFreeUnixId)");
        $entries = ldap_get_entries($ds,$result);
        if ($entries["count"]==0 or $entries==FALSE) {
            $entry["ou"] = "NextFreeUnixId";
            $entry["objectClass"][0] = "organizationalUnit";
            $entry["objectClass"][1] = "sambaUnixIdPool";
            $entry["uidNumber"] = 10015;
            $entry["gidNumber"] = 10015;
            ldap_add($ds,"ou=NextFreeUnixId,".$this->base_dn,$entry);
        }
	
        if (!file_exists($app->remotePath.$this->shares_root)) {
            if ($this->old_shares_root!="" and file_exists($app->remotePath.$this->old_shares_root))
                $shell->exec_command($app->remoteSSHCommand." mv ".$this->old_shares_root." ".$this->shares_root);
            else {
                $shell->exec_command($app->remoteSSHCommand." mkdir -p ".$this->shares_root);
                $shell->exec_command($app->remoteSSHCommand." chmod -R 7777 ".$this->shares_root);
            }
        }

		$module = $gapp->getModuleByClass($this->module_id);
        if ($this->smbAutoRestart)
            $value = "1";
        else
            $value = "0";
		$module["smbAutoRestart"] = $value;
        if ($this->smbDenyUnknownHosts)
            $value = "1";
        else
            $value = "0";
        
        $module["smbDenyUnknownHosts"] = $this->smbDenyUnknownHosts;
		$GLOBALS["modules"][$module["name"]] = $module;
		$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
		file_put_contents($module["file"],$str);
        
		$this->saveFiles();
        
        if ($this->smbDenyUnknownHosts != $this->old_smbDenyUnknownHosts)
            $this->saveFirewallFile();
        $this->old_shares_base = $this->shares_base;
        $this->old_shares_root = $this->shares_root;
        $gapp->raiseRemoteEvent("FILESERVER_CHANGED");        
        $this->loaded = true;
    }
    
    function saveFiles() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        
        $args = $this->getArgs();
        $args["{ldap_host}"] = $this->initial_ldap_host;
        
        $strings = file($this->smbTemplateConfigFile);
        $fp = fopen($app->remotePath.$this->smbConfigFile,"w");
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);

        $fp = fopen($app->remotePath.$this->ldapClientConfigFile,"w");
        $strings = file($this->ldapClientTemplateConfigFile);
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);

        $fp = fopen($app->remotePath.$this->ldapClientConfigFile2,"w");
        $strings = file($this->ldapClientTemplateConfigFile);
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);

        $fp = fopen(str_replace(".conf",".secret",$app->remotePath.$this->ldapClientConfigFile),"w");
        fwrite($fp,$this->ldap_password);
        fclose($fp);

        $fp = fopen(str_replace(".conf",".secret",$app->remotePath.$this->ldapClientConfigFile2),"w");
        fwrite($fp,$this->ldap_password);
        fclose($fp);
        
        $fp = fopen($app->remotePath.$this->ldapscriptsConfigFile,"w");
        $strings = file($this->ldapscriptsTemplateConfigFile);
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);

        $fp = fopen(str_replace(".conf",".passwd",$app->remotePath.$this->ldapscriptsConfigFile),"w");
        fwrite($fp,$this->ldap_password);
        fclose($fp);
        
        $fp = fopen($app->remotePath.$this->ldapscriptsPasswordFile,"w");
        fwrite($fp,$this->ldap_password);
        fclose($fp);

        $fp = fopen($app->remotePath.$this->idealXConfigFile,"w");
        if ($this->domain_controller=="include = /etc/samba/domain.conf")
            $args["{domain}"] = $this->workgroup;
        else
            $args["{domain}"] = $this->netbios_name;
        $strings = file($this->idealXTemplateConfigFile);
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);

        $fp = fopen($app->remotePath.$this->idealXBindConfigFile,"w");
        $strings = file($this->idealXTemplateBindConfigFile);
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);

        $fp = fopen($app->remotePath.$this->openLdapClientConfigFile,"w");
        $strings = file($this->openLdapClientTemplateConfigFile);
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);
        $dhcp = $Objects->get("DhcpServer_".$this->module_id."_Networks");
        if (!$dhcp->loaded)
        	$dhcp->load();
        $args = $dhcp->getArgs();
        $fp = fopen($app->remotePath.$this->dhcpConfigFile,"w");
        $strings = file($this->dhcpConfigTemplateFile);
        foreach($strings as $line) {
            fwrite($fp,strtr($line,$args));
        }
        fclose($fp);
        if ($app->remoteSSHCommand=="") {
            $cmd = $this->smbRestartCommand.";".$this->smbResetAdminPasswordCommand." -w ".$this->ldap_password.";".$this->smbRestartCommand.";".$this->dhcpRestartCommand;
            $shell->exec_command("echo ".$cmd." | at now");            
        } else {
                $cmd = " \"".$this->smbRestartCommand.";".$this->smbResetAdminPasswordCommand." -w ".$this->ldap_password.";".$this->smbRestartCommand.";".$this->dhcpRestartCommand."\"";
                shell_exec($app->remoteSSHCommand." 'echo ".$cmd." | at now'");            
        }
    }

    function load() {        
        global $Objects;
        $app = $Objects->get($this->module_id);
        
        $strings = file($app->remotePath.$this->smbConfigFile);
        foreach ($strings as $line) {
            if (preg_match('/WORKGROUP = (.*)/',$line,$matches)==1)
                $this->workgroup = trim($matches[1]);
            if (preg_match("/NETBIOS NAME = (.*)/",$line,$matches)==1)
                $this->netbios_name = trim($matches[1]);
            if (preg_match('/#ldap user suffix = (.*)/',$line,$matches)==1)
                $this->ldap_user_suffix = trim($matches[1]);
            if (preg_match('/#ldap machine suffix = (.*)/',$line,$matches)==1)
                $this->ldap_machine_suffix = trim($matches[1]);
            if (preg_match('/#ldap group suffix = (.*)/',$line,$matches)==1)
                $this->ldap_group_suffix = trim($matches[1]);
            if (preg_match('/#ldap idmap suffix = (.*)/',$line,$matches)==1)
                $this->ldap_idmap_suffix = trim($matches[1]);
            if (preg_match('/# ldap shares suffix = (.*)/',$line,$matches)==1)
                $this->shares_base = trim($matches[1]);
            if (preg_match('/# shares root path = (.*)/',$line,$matches)==1)
                $this->shares_root = trim($matches[1]);
            if (preg_match('/include = \/etc\/samba\/domain.conf/',$line,$matches)==1)
                $this->domain_controller = "1";
            if (preg_match('/#include = \/etc\/samba\/domain.conf/',$line,$matches)==1)
                $this->domain_controller = "0";
            
        }
        $this->domain = str_replace("dc=","",str_replace(",",".",$this->ldap_base));
        $ldap_base = $this->ldap_base;
        $ldap_base = explode(".",$ldap_base);
        for ($counter=0;$counter<count($ldap_base);$counter++) {
            $ldap_base[$counter] = "dc=".$ldap_base[$counter];
        }
        
        $this->loaded = true;
        $sys = $Objects->get("SystemSettings_".$this->module_id."_Settings");
        $sys->loadLdapSettings();
        
        $this->ldap_host = $sys->ldap_host;
        $this->ldap_port = $sys->ldap_port;
        $this->ldap_base = $sys->base_dn;
        $this->ldap_use_ssl = $sys->ldap_use_ssl;
        $this->ldap_user = "cn=".$sys->ldap_user.",".$sys->base_dn;
        $this->ldap_password = $sys->ldap_password;
        $this->base_dn = $sys->base_dn;
        $this->old_base_dn = $sys->base_dn;
        $this->ldap_user_dn = $this->ldap_user;
        $this->old_shares_base = $this->shares_base;
        $this->old_shares_root = $this->shares_root;
        $this->old_ldap_base = $this->ldap_base;
        $this->old_ldap_password = $this->ldap_password;
        $this->old_ldap_user = $this->ldap_user;     
        $this->old_smbDenyUnknownHosts = $this->smbDenyUnknownHosts;
        $this->old_snapshotsFolder = $this->snapshotsFolder;
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
            $gapp->initModules();
        $shell = $Objects->get("Shell_shell");
        $this->initial_ldap_host = $this->ldap_host;
        if ($this->ldap_host=="localhost")
        	if ($app->remoteAddress!="")
            	$this->ldap_host = $app->remoteAddress;
        $this->old_ldap_host = $this->ldap_host;      
        $this->ldap_proto = $sys->ldap_proto;
        $changed=false;
    }

    function getId() {
        return "FileServer_".$this->module_id."_".$this->name;
    }

    function getDN() {
        if (!$this->loaded)
            $this->load();
        return "ou=".$this->shares_base.",".$this->ldap_base;
    }

    function getobjectGroupDN() {
        if (!$this->loaded)
            $this->load();
        return "ou=objectgroups".",".$this->ldap_base;
    }
    
    function getGroupDN() {
        if (!$this->loaded)
            $this->load();
        return "ou=groups".",".$this->ldap_base;
    }    

    function getUserDN() {
        if (!$this->loaded)
            $this->load();
        return "ou=users".",".$this->ldap_base;
    }    
    
    function getPresentation() {
        return l10n("Файловый сервер");
    }

    function __get($name) {
        global $Objects;
        switch($name) {
            case "shares":
                return $Objects->query("FileShare");
            case "objectGroups":
                return $Objects->query("ObjectGroup");
            case "users":
                return $Objects->query("User");
            case "groups":
                return $Objects->query("Group");
            default:
                if (isset($this->fields[$name]))
                    return $this->fields[$name];
                else
                    return "";
        }
    }

    function contains($name) {
        if (!$this->loaded)
            $this->load();

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getDN(), "(comment=".$name.")");
        if ($res == FALSE)
            return false;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return false;
        else
            return true;                
    }

    function containsObjectGroup($name) {
        if (!$this->loaded)
            $this->load();

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
            if (ldap_error($ds)!="Success") {
	            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getObjectGroupDN(), "(comment=".$name.")");
        if ($res == FALSE)
            return false;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return false;
        else
            return true;
    }

    function containsId($id) {
        if (!$this->loaded)
            $this->load();

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getDN(), "(idnumber=".$id.")");
        if ($res == FALSE)
            return false;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return false;
        else
            return true;
    }

    function containsObjectGroupId($id) {
        if (!$this->loaded)
            $this->load();

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getObjectGroupDN(), "(idnumber=".$id.")");
        if ($res == FALSE)
            return false;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return false;
        else
            return true;
    }

    function containsPath($id) {
        if (!$this->loaded)
            $this->load();

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }
		global $Objects;
        $res = @ldap_list($ds, $this->getDN(), "(sharepath=".$id.")");
        if ($res == FALSE)
            return false;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return false;
        else {
            return $Objects->get("FileShare_".$this->module_id."_".$entries[0]["idnumber"][0]);
		}
    }

    function loadShares($full=false) {
        if (!$this->loaded)
            $this->load();

        global $Objects;

        $root_folder = $Objects->get("FileShare_".$this->module_id."_0");
        $root_folder->name = "root";
        $root_folder->path = $this->shares_root;

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getDN(), "(objectClass=fileShare)");        
        if ($res == FALSE)
            return 0;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return 0;

        foreach($entries as $key=>$value) {
            if (is_numeric($key)) {
                $val = $Objects->get("FileShare_".$this->module_id."_".$value["idnumber"][0]);
                $val->name = $value["comment"][0];
                $val->old_name = $val->name;
                $val->path = $value["sharepath"][0];
                $val->old_path = $val->path;
                $val->ftpFolder = @$value["ftpfolder"][0];
                if ($full)
                    $val->load();
                $val->loaded = true;
            }
        }
        $this->sharesLoaded = true;
    }

    function loadObjectGroups() {
        if (!$this->loaded)
            $this->load();

        global $Objects;

        $root_folder = $Objects->get("ObjectGroup_".$this->module_id."_0");
        $root_folder->name = "Вне групп";
        $root_folder->path = $this->shares_root;
        
        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getObjectGroupDN(), "(objectClass=objectGroup)");
        if ($res == FALSE)
            return 0;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return 0;

        global $Objects;
        foreach($entries as $key=>$value) {
            if (is_numeric($key)) {
                $val = $Objects->get("ObjectGroup_".$this->module_id."_".$value["idnumber"][0]);
                $val->name = $value["comment"][0];
                $val->loaded = true;
            }
        }
        $this->objectGroupsLoaded = true;
    }

    function saveShares() {
        if (!$this->loaded)
            $this->load();

        foreach($this->shares as $share) {
            $share->save();
        }
        $this->objectGroupsLoaded = true;
    }

    function saveObjectGroups() {
        if (!$this->loaded)
            $this->load();
        foreach($this->objectGroups as $objectGroup) {
            $objectGroup->save();
        }
        $this->objectGroupsLoaded = true;
    }

    function loadUsers($full_load=true) {
        if (!$this->loaded)
            $this->load();
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get($this->module_id);
        $this->app = $Objects->get("Application");
        if (!$this->app->initiated)
            $this->app->initModules();
        
        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getUserDN(), "(objectClass=posixAccount)");

        if ($res == FALSE)
            return 0;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return 0;

        foreach($entries as $key=>$value) {
            if (is_numeric($key)) {
                if ($value["uid"][0]!="") {
                    $user = $Objects->get("User_".$this->module_id."_".$value["uid"][0]);
                    if ($user->loaded)
                        continue;
                    $user->uid = $value["uidnumber"][0];
                    $user->home_dir = $value["homedirectory"][0];
                    if ($full_load)
                        if (!$user->loaded)
                                $user->load();
                }
            }
        }        
        
        $this->users_loaded = true;
    }

    function saveUsers() {
        if (!$this->loaded)
            $this->load();
        foreach($this->users as $user) {
            $user->save();
        }
        $this->users_loaded = true;
    }

    function loadGroups($full_load=false) {
        if (!$this->loaded)
            $this->load();
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get($this->module_id);
        $this->app = $Objects->get("Application");
        if (!$this->app->initiated)
            $this->app->initModules();
        
        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds, $this->getGroupDN(), "(objectClass=posixGroup)");

        if ($res == FALSE)
            return 0;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return 0;

        foreach($entries as $key=>$value) {
            if (is_numeric($key)) {
                $group = $Objects->get("Group_".$this->module_id."_".$value["cn"][0]);
                $group->gidNumber = $value["gidnumber"][0];
                if ($full_load)
                    if (!$group->loaded)
                            $group->load();
            }
        }               
        $this->groups_loaded = true;
    }

    function saveGroups() {
        if (!$this->loaded)
            $this->load();
        foreach($this->groups as $group) {
            $user->save();
        }
        $this->groups_loaded = true;
    }

    function addShare($name,$path) {
        if ($this->contains($name)) {
            $this->reportError("Указанная общая папка уже существует","save");
            return 0;
        }
        global $Objects;
        $result = $Objects->get("FileShare_".$this->module_id."_");
        $result->name = $name;
        $result->path = $path;
        return $result;
    }

    function addObjectGroup($name,$path) {
        if ($this->containsObjectGroup($name)) {
            $this->reportError("Указанный объект уже существует","save");
            return 0;
        }
        global $Objects;
        $result = $Objects->get("ObjectGroup_".$this->module_id."_");
        $result->name = $name;
        return $result;
    }

    function removeShare($id) {
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        if ($id==0) {
            $this->reportError("Нельзя удалить корневую папку !","removeShare");
            return 0;
        }
        
        if (!$this->containsId($id)) {
            $this->reportError("Указанной общей папки не существует !","removeShare");
            return 0;
        }

        if (!$this->loaded)
            $this->load();

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        global $Objects;

        $share = $Objects->get("FileShare_".$this->module_id."_".$id);
        $share->load();
        unset($share->accessRules[$share->name]);
        $share->smbShare=false;
        $share->nfsShare=false;
        $share->afpShare=false;
        if ($share->ftpFolder) {
            $ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
            $share->oldFolders =$ftpServer->getFolders();
        }
        $share->ftpFolder=0;
        $share->save();
        
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		$app->raiseRemoteEvent("FILESHARE_DELETED","object_id=".$share->getId());
		
        $subs = $Objects->get("DhcpSubnets_".$this->module_id."_Networks");
        if (!$subs->loaded)
            $subs->load();
        foreach ($subs->subnets as $subnet) {
            if (!$subnet->hosts_loaded)
                $subnet->loadHosts();
            foreach ($subnet->hosts as $host) {
                if (!$host->loaded)
                    $host->load();
                unset($host->accessRules[$share->name]);
                $host->save();
            }
        }

        ldap_delete($ds, "idnumber=".$id.",".$this->getDN());
        $app = $Objects->get($this->module_id);
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if ($app->remoteSSHCommand=="") {
            if ($this->smbAutoRestart) 
                $shell->exec_command($app->remoteSSHCommand." ".$this->smbReloadCommand);
                $shell->exec_command($app->remoteSSHCommand." ".$this->nfsRestartCommand);
        } else {
            if ($this->fileServer->smbAutoRestart) {
                $cmd = " \"";
                $cmd .= $this->fileServer->smbReloadCommand.";";
                $cmd .= $this->fileServer->nfsRestartCommand.";";
                $cmd .= "\"";
                shell_exec($app->remoteSSHCommand." 'echo ".$cmd." | at now'");            
            }
        }
        @unlink($app->remotePath.$this->smbSharesConfigPath."/".$share->name."_vfs.conf");
        $Objects->remove("FileShare_".$this->module_id."_".$id);
    }

    function removeObjectGroup($id) {
    	if (is_array($id))
    		$id = $id["share"];
        if (!$this->containsObjectGroupId($id)) {
            $this->reportError("Указанного объекта не существует !");
            return 0;
        }

        if (!$this->loaded)
            $this->load();

        $ds = ldap_connect($this->ldap_proto."://".$this->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->ldap_user,$this->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        global $Objects;
        
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("OBJECTGROUP_DELETED","object_id="."ObjectGroup_".$this->module_id."_".$id);
        
        ldap_delete($ds, "idnumber=".$id.",".$this->getObjectGroupDN());
                
        $res = ldap_search($ds, $this->ldap_base, "(objectClass=dhcpHost)");

        if ($res == FALSE)
            return 0;
        $entries = ldap_get_entries($ds,$res);
        if ($entries["count"]<1)
            return 0;

        foreach($entries as $key=>$value) {
            if (is_numeric($key)) {
                $arr = str_replace("cn=","",explode(",",$value["dn"]));
                $host = $Objects->get("DhcpHost_".$this->module_id."_".$arr[1]."_".$arr[0]);                
                if (!$host->loaded)
                    $host->load();
                if ($host->loaded) {
                    $groups = explode(",",$host->objectGroup);
                    if (array_search($id,$groups)!==FALSE) {
                        unset($groups[array_search($id,$groups)]);
                        $host->objectGroup = implode(",",array_flip($groups));
                        if ($host->objectGroup=="")
                            $host->objectGroup="0";
                        $host->save(true);
                    }
                }
            }
        }               
        
        $Objects->remove("ObjectGroup_".$this->module_id."_".$id);
        
        $capp = $Objects->get($this->module_id);
        if (is_object($capp->docFlowApp)) {
        	$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
        	if (!$adapter->connected)
        		$adapter->connect();
        	if ($adapter->connected) {
        		$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @objectId='ObjectGroup_".$this->module_id."_".$id."' AND @classname='ReferenceObjectGroupInfoCard'",$adapter,$capp->docFlowApp->getId());
        		foreach ($entities as $entity) {
        			$entity->loaded = false;
        			$entity->load();
        			$entity->deleted = 1;
        			$entity->save(true);
        		}
        	}
        }
        
    }
    
    function restart($arguments) {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($app->remoteSSHCommand." ".$this->nfsRestartCommand);
        $shell->exec_command($app->remoteSSHCommand." ".$this->afpRestartCommand);
        $this->saveFirewallFile();
        echo $shell->exec_command($app->remoteSSHCommand." ".$this->smbRestartCommand);
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("FILESERVER_RESTARTED");
    }
    
    function saveFirewallFile() {        
        global $Objects;
        $subs = $Objects->get("DhcpSubnets_".$this->module_id."_Subs");
        $shell = $Objects->get("Shell_shell");
        $new_chain  = "#!/bin/bash\n";
        $new_chain .= "export PATH=\$PATH:/sbin\n";
        $new_chain .= "/sbin/iptables -A INPUT -s 127.0.0.1 -j ACCEPT\n";
        $new_chain .= "/sbin/iptables -D INPUT -p tcp -m multiport --dports 111,139,445,".$this->afpPort." -j SAMBA 2>/dev/null\n";
        $new_chain .= "/sbin/iptables -D INPUT -p udp -m multiport --dports 111,137,138,".$this->afpPort." -j SAMBA 2>/dev/null\n";
        $new_chain .= "/sbin/iptables -F SAMBA 2>/dev/null;iptables -X SAMBA 2>/dev/null;iptables -N SAMBA\n";
        $new_chain .= "/sbin/iptables -A INPUT -p tcp -m multiport --dports 111,139,445,".$this->afpPort." -j SAMBA\n";
        $new_chain .= "/sbin/iptables -A INPUT -p udp -m multiport --dports 111,137,138,".$this->afpPort." -j SAMBA\n";
        if (!$subs->loaded)
            $subs->load();
        $deny_hosts = array();
        $accepted_hosts = array();
        foreach($subs->subnets as $subnet) {
            $subnet->loadHosts();
            foreach($subnet->hosts as $host) {
                if ($host->denyFileAccess) {
                    $deny_hosts[] = "/sbin/iptables -A SAMBA -m mac --mac-source ".$host->hw_address." -j REJECT";
                } else {
                    if ($this->smbDenyUnknownHosts) {
                        $accepted_hosts[] = "/sbin/iptables -A SAMBA -m mac --mac-source ".$host->hw_address." -s ".$host->fixed_address." -j ACCEPT";
                    }
                }                
            }
            if ($subnet->denyFileAccess) {
                $deny_hosts[] = "/sbin/iptables -A SAMBA -s ".$subnet->name."/".$subnet->subnet_mask." -j REJECT";                
            } 
        }        
        if ($this->smbDenyUnknownHosts) {
            $deny_string = "/sbin/iptables -A SAMBA -j REJECT";
        }
        $app = $Objects->get($this->module_id);            
        $fp = fopen($app->remotePath.$this->smbFirewallFile,"w");
        if (count($deny_hosts)>0 or count($accepted_hosts)>0) {
            $deny_hosts = array_unique($deny_hosts);
            $accepted_hosts = array_unique($accepted_hosts);
            $app = $Objects->get($this->module_id);            
            $fp = fopen($app->remotePath.$this->smbFirewallFile,"w");
            fwrite($fp,$new_chain."\n");
            foreach ($deny_hosts as $string)
                fwrite($fp,$string."\n");
            foreach ($accepted_hosts as $string)
                fwrite($fp,$string."\n");
            if (isset($deny_string))
                fwrite($fp,$deny_string);            
            fclose($fp);
            $shell->exec_command($app->remoteSSHCommand." ".$this->smbFirewallFile);            
        } else {
            fwrite($fp,$new_chain);
            fclose($fp);            
            $shell->exec_command($app->remoteSSHCommand." ".$this->smbFirewallFile);            
        } 
    }
    
    function getInvalidUsers() {
        global $Objects;
        $capp = $Objects->get($this->module_id);
        if (!file_exists($capp->remotePath.$this->smbInvalidUsersFile))
            return array();
        $line = file_get_contents($capp->remotePath.$this->smbInvalidUsersFile);
        $matches = array();
        $result = array();        
        if (preg_match("/invalid users = (.*)/",$line,$matches)) {
            $matches = explode(" ",trim($matches[1]));
            $result = "";
            foreach($matches as $match) {
                $match = trim($match);
                if ($match=="")
                    continue;
                $result[$match] = $match;
            }
        }
        return $result;
    }
    
    function setInvalidUsers($invalidUsers) {
        global $Objects;
        $capp = $Objects->get($this->module_id);
        file_put_contents($capp->remotePath.$this->smbInvalidUsersFile,"invalid users = ".implode(" ",$invalidUsers));
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "save";
    		case '4': return "restart";
    		case '5': return "removeObjectGroup";
    	}
    	return parent::getHookProc($number);
    }
}
?>